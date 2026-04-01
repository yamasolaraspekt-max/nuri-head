@extends('admin.layouts.app')

@section('title') Projectmanagement @endsection
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
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Warenkorb</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Kunden</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Neu</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
            <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-6 col-12">
                
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\CustomerCartController@store')}}"  class="custom-file-upload" enctype="multipart/form-data">
                                        @csrf    
                                        <div class="form-body">
                                                <div class="row">

                                     

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Project Name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" id="project" class="form-control" value="{{old('project')}}" name="project" >
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Kunden</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                    <select class="form-control" id="basicSelect" name="customer_id">
                                                                        @foreach ($customer as $cus)
                                                                        <option value="{{ $cus->id }}">{{ $cus->name }} {{ $cus->lastname }}</option>
                                                                        @endforeach
                                                                        
                                                                    </select>
                                                                </fieldset>
                                                             </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Produkt</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <fieldset class="form-group">
                                                            <select class="select2-customize-result form-control" multiple="multiple" name="product_id[]" id="product"  >
                                                                @foreach ($product as $pro)
                                                                <option value="{{ $pro->id }}">{{ $pro->product }}</option>
                                                                @endforeach
                                                                
                                                            </select>
                                                        </fieldset>
                                                        </div>
                                                    </div>

                                                   <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Kontaktperson</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                    <select class="form-control" id="basicSelect" name="employee_id">
                                                                        @foreach ($employee as $emp)
                                                                        <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                        @endforeach
                                                                        
                                                                    </select>
                                                                </fieldset>
                                                             </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Einreichen</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>
                            <div class="col-md-6 col-12">
                            
                               
                            </div>
                        </div>
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


<script>
        $(document).ready(function() {
            $('#product').select2();
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