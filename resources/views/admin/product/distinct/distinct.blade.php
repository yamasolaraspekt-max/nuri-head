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
                                                                        <select class="select2-customize-result form-control" name="search" id="product"  >
                                                                            <option value="{{ old('product') }} " disabled selected>{{ old('product') }} </option>
                                                                            @foreach ($product as $pro)
                                                                            <option value="{{ $pro->product}}">{{ $pro->product }}</option>
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
                                                                <h4 class="card-title"> Produktunterschied Nach Preis</h4>
                                                            </div>
                                                            <div class="card-content">
                                                                <div class="card-body">
                                                                    <p class="card-text"></p>
                                                                </div>
                                                                <div class="row" style="justify-content: center;">
                                                                    @foreach ($content as $con)
                                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                                        <div class="card" style="background:#dbdbdb">
                                                                            <div class="card-header d-flex align-items-start pb-0">
                                                                                <div>
                                                                                    <h2 class="text-bold-700 mb-0">{{ $con->purchase_price }}€</h2>
                                                                                    <p>{{ $con->product }}</p>
                                                                                    <p>{{ $con->distributor }} - {{ $con->brand }} </p>
                                                                                   
                                                                                </div>
                                                                                <div class="avatar bg-rgba-primary p-50 m-0">
                                                                                    @foreach ($image->take(1) as $img)
                                                                                    @if($img->product_id==$con->id)
                                                                                    <div class="avatar mr-1 avatar-lg">
                                                                                        <img src="{{ asset('images/products/'.$img->image) }}" alt="{{ $con->product }}">
                                                                                    </div> 
                                                                                    @else
                                                                                    <div class="avatar-content position-relative">
                                                                                        <i class="avatar-icon feather icon-cpu"></i>
                                                                                    </div>
                                                                                    @endif
                                                                                    @endforeach
                                                                                   
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="divider divider-success">
                                                                    <div class="divider-text">Bestepreis</div>
                                                                </div>
                                                                    <div class="row" style="justify-content: center;">    
                                                                        @foreach ($best as $be)
                                                                        <div class="col-lg-3 col-sm-6 col-12">
                                                                            <div class="card" style="background: #75cf2c">
                                                                                <div class="card-header d-flex align-items-start pb-0">
                                                                                    <div>
                                                                                        <h2 class="text-bold-700 mb-0">{{ $be->purchase_price }}€</h2>
                                                                                        <p>{{ $be->product }}</p>
                                                                                        <p>{{ $be->distributor }} - {{ $be->brand }} </p>
                                                                                    
                                                                                    </div>
                                                                                    <div class="avatar bg-rgba-primary p-50 m-0">
                                                                                        @foreach ($image->take(1) as $img)
                                                                                        @if($img->product_id==$be->id)
                                                                                        <div class="avatar mr-1 avatar-lg">
                                                                                            <img src="{{ asset('images/products/'.$img->image) }}" alt="{{ $be->product }}">
                                                                                        </div> 
                                                                                        @else
                                                                                        <div class="avatar-content position-relative">
                                                                                            <i class="avatar-icon feather icon-cpu"></i>
                                                                                        </div>
                                                                                        @endif
                                                                                        @endforeach
                                                                                    
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        @endforeach
                                                                    </div>

                                                                    <div class="divider divider-success">
                                                                        <div class="divider-text">Hoher Prise</div>
                                                                    </div>
                                                                        <div class="row" style="justify-content: center;">    
                                                                            @foreach ($high as $be)
                                                                            <div class="col-lg-3 col-sm-6 col-12">
                                                                                <div class="card" style="background: #cf412c">
                                                                                    <div class="card-header d-flex align-items-start pb-0">
                                                                                        <div>
                                                                                            <h2 class="text-bold-700 mb-0">{{ $be->purchase_price }}€</h2>
                                                                                            <p>{{ $be->product }}</p>
                                                                                            <p>{{ $be->distributor }} - {{ $be->brand }} </p>
                                                                                        
                                                                                        </div>
                                                                                        <div class="avatar bg-rgba-primary p-50 m-0">
                                                                                            @foreach ($image->take(1) as $img)
                                                                                            @if($img->product_id==$be->id)
                                                                                            <div class="avatar mr-1 avatar-lg">
                                                                                                <img src="{{ asset('images/products/'.$img->image) }}" alt="{{ $be->product }}">
                                                                                            </div> 
                                                                                            @else
                                                                                            <div class="avatar-content position-relative">
                                                                                                <i class="avatar-icon feather icon-cpu"></i>
                                                                                            </div>
                                                                                            @endif
                                                                                            @endforeach
                                                                                        
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
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