@extends('admin.layouts.app')

@section('title') REUQEST OUT @endsection
@section('style') 
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
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
                           
                            <h2 class="content-header-title float-left mb-0">Product </h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="">request out</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Create</a>
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
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal">
                                            <div class="form-body">
                                                <div class="row">
                                                    <form method="get" action="">
                                                        <div class="col-8">
                                                            <div class="form-group row">
                                                                <div class="col-md-4">
                                                                    <span>Produkt</span>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <fieldset class="form-group">
                                                                        <select class="select2-customize-result form-control" name="product_id" id="product"  >
                                                                            <option value="{{ old('product') }} " disabled selected>{{ old('product') }} </option>
                                                                            @foreach ($product as $pro)
                                                                            <option value="{{ $pro->id }}">{{ $pro->product }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-4">
                                                            <button type="submit" id="search" onclick="" class="btn btn-primary"><i class="fa fa-search"></i> Suchen</button>
                                                        </div>
                                                    </form>

                                                    
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h4 class="card-title"> Verfügbare Produkte im Lager</h4>
                                                            </div>
                                                            <div class="card-content">
                                                                <div class="card-body">
                                                                    <p class="card-text"></p>
                                                                </div>
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered mb-0">
                                                                        <thead>
                                                                            <tr>
                                                                              
                                                                                <th>#</th>
                                                                                <th>Product</th>
                                                                                <th>Model</th>
                                                                                <th>Unternahmen</th>
                                                                                <th>Liefrant</th>
                                                                                <th>Purchase Price</th>
                                                                                <th>Quantity</th>
                
                                                                            </tr>
                                                                        </thead>
                                                                        <form action="{{ action('App\Http\Controllers\InventoryRequestOutController@request') }}" method="get" class="custom-file-upload" enctype="multipart/form-data">
                                                                        <tbody>

                                                                            @if(count($all_products))
                                                                           @foreach ($all_products as $prod)
                                                                          
                                                                            @csrf
                                                                           <tr>
                                                                                <input type="hidden" name="product_id" value="{{ $prod->id }}">
                                                                                <input type="hidden" name="quantity" value="{{ $prod->quantity }}">
                                                                               <td><input type="radio" name="check" > - {{ $prod->id }}</td>
                                                                               <td>{{ $prod->product }}</td>
                                                                               <td>{{ $prod->model }}</td>
                                                                               <td>{{ $prod->brandname }}</td>
                                                                               <td>{{ $prod->distributor }}</td>
                                                                               <td>{{ $prod->purchase_price }}</td>
                                                                               @if($prod->quantity != 0)
                                                                               <td id="quantity">{{ $prod->quantity }}</td>
                                                                               @else
                                                                               <td colspan='1'>
                                                                                <a type="button" href="{{ route('purchase.request.create') }}" class="btn btn-outline-success mr-1 mb-1 waves-effect waves-light"><i class="fa fa-shopping-cart  "></i> Kaufanfrage</a>
                                                                                </td>
                                                                                @endif
                                                                            </tr>
                                                                           @endforeach
                                                                           @else
                                                                           <tr>
                                                                            <td colspan='6'>Keine Daten vorhanden!</td>
                                                                            <td colspan='1'>
                                                                                <a type="button" href="{{ route('purchase.request.create') }}" class="btn btn-outline-success mr-1 mb-1 waves-effect waves-light"><i class="fa fa-shopping-cart  "></i> Kaufanfrage</a>
                                                                            </td>
                                                                            </tr>
                                                                            @endif
                                                                           <tr>
                                                                            <td colspan='6'>
                                                                                <button type="submit" class="btn btn-outline-success mr-1 mb-1 waves-effect waves-light"><i class="fa fa-shopping-cart  "></i> Anfrage raus </button>
                                                                             </td>
                                                                        </tr>
                                                                    </form>
                                                                       
                                                                       
                                                                       
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                   

                                                                      
                                                 </div>
                                            </div>
                                        </form>
                                    </div>                               
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

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#product').select2();
        $('#requester').select2();
        $('#responsible').select2();
    });

</script>



@endsection