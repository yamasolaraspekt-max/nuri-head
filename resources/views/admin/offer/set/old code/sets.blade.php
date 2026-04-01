@extends('admin.layouts.app')
@section('title') Set Artikle @stop
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
                        <h2 class="content-header-title float-left mb-0">Set Artikle</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('article_group_set') }}">  </a>
                                </li>

                                <li class="breadcrumb-item"><a
                                        href="{{ url('master_set/'.$title->article_group_id.'/'.$title->sub_id) }}"> </a>
                                </li>
                                <li class="breadcrumb-item"><a href=" "> </a>
                                </li>

                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Table Hover Animation start -->
            <div class="row" id="table-hover-animation">
                <div class="col-md-6 col-12 mb-1">
                    <form action="">
                        <fieldset>
                            <div class="input-group">

                                <input type="text" class="form-control"
                                    placeholder="Geben Sie die Details Ihrer Suche ein" aria-describedby="button-addon2"
                                    name="search">
                                <div class="input-group-append" id="button-addon2">
                                    <button class="btn btn-primary waves-effect waves-light" type="button"><i
                                            class="feather icon-search"></i></button>
                                </div>

                            </div>

                        </fieldset>
                    </form>
                </div>

                <div class="col-md-6 col-12 mb-1">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle mr-1 waves-effect waves-light" type="button"
                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Set Details
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start"
                            style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 38px, 0px);">
                            <a class="dropdown-item" href="{{ url('add_product/'.request()->id.'/'.request()->phase) }}">Produkt Details</a>
                            <a class="dropdown-item" href="{{ url('add_sub_product/'.request()->id.'/'.request()->phase) }}">Sub Produkt Details</a>
                            <a class="dropdown-item" href="{{ url('add_employee_set/'.request()->id.'/'.request()->phase) }}">Mitarbeiter
                                Details</a>
                            <a class="dropdown-item" href="{{ url('set_paragraph/'.request()->id) }}">Paragraph
                                Details</a>
                            <a class="dropdown-item" href="{{ url('add_image/'.request()->id) }}">Foto Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php

        $percentage = DB::table('product_master_sets')->where('id', request()->id)->select('employee_percent',
        'material_percent')->first();
        @endphp

        @php
        $skill_sum_total = $data->sum('total');
        @endphp

        @php
        $total = $skills->sum('total');
        $hours = $skills->sum('work_hour');
        $work_price_hour = ($hours != 0) ? ($total / $hours) : 0;
        @endphp
        <div class="content-body">
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height">
                    <div class="col-md-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Mitarbeiterposition
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1 mb-1 waves-effect waves-light"
                                         data-toggle="popover" 
                                        data-content="Mitarbeiter, die für dieses Set benötigt werden" 
                                        data-original-title="Mitarbeiterposition"  
                                        data-trigger="hover"
                                        data-placement="top"><i class="feather icon-info"></i>
                                    </button>
                                </h4>
                                <a href="{{ url('refresh_master_set/'.request()->id) }}">
                                    <div class="badge badge-info">Anteil Materialkosten: {{ $percentage->material_percent }}
                                        %</div>
                                </a>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover-animation mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Mitarbeiterposition</th>
                                                    <th scope="col">Arbeitsstunde</th>
                                                    <th scope="col">Arbeitspreis/Uhr</th>
                                                    <th scope="col">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($skills as $item)
                                              
                                                <tr>
                                                    <th scope="row">{{ $item->id }}</th>
                                                    <td>{{ $item->position }}</td>
                                                    <td>{{ $item->work_hour }}</td>
                                                        
                                                    <td>
                                                        @if($item->buying_price==0)
                                                        {{ number_format( $item->sale_price, 2, ',', '.') }}€
                                                        @else
                                                        {{ number_format( $item->buying_price, 2, ',', '.') }}€
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format( $item->total, 2, ',', '.') }}€</td>
                                                </tr>
                                               
                                                @endforeach
                                                <tr>
                                                    <th colspan="1">
                                                    <td style="font-weight: bold;">{{ $skills->count('id') }} Person(en)
                                                    </td>
                                                    <td style="font-weight: bold;">{{ $skills->sum('work_hour') }}</td>
                                                    <td style="font-weight: bold;"> {{ number_format($work_price_hour,
                                                        2, ',', '.') }}€</td>
                                                    <td style="font-weight: bold;">{{
                                                        number_format($skills->sum('total'), 2, ',', '.') }}€</td>

                                                    </th>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- {{-- Product Set --}} -->
                    <div class="col-md-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">ProduktSet        
                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1 waves-effect waves-light"
                                         data-toggle="popover" 
                                        data-content="Liste der Produkte, die in diesem Set vorhanden sind" 
                                        data-original-title="ProduktSet"  
                                        data-trigger="hover"
                                        data-placement="top"><i class="feather icon-info"></i></button>
                                </h4>  
                                <a href="{{ url('refresh_master_set/'.request()->id) }}">
                                    <div class="badge badge-info">Anteil Lohnkosten: {{ $percentage->employee_percent }}
                                    </div>
                                </a>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover-animation mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">ProduktSet</th>
                                                    <th scope="col">Anzahl</th>
                                                    <th scope="col">Einkaufspreis + Rabbat</th>
                                                    <th scope="col">Gesamtpreis</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $item)
                                                <tr>
                                                    <th scope="row">{{ $item->product_id }}</th>
                                                    <td>{{ $item->product }}</td>
                                                    <td>{{ $item->product_count }}</td>
                                                    <td>{{ number_format( $item->purchase_price, 2, ',', '.') }}€</td>
                                                    <td>{{ number_format($item->total, 2, ',', '.') }}€</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <th colspan="2">
                                                    <td style="font-weight: bold;">{{ $subProducts->sum('product_count') }}</td>

                                                    <td style="font-weight: bold;">{{
                                                        number_format($data->sum('purchase_price'), 2, ',', '.') }}€
                                                    </td>
                                                    <td style="font-weight: bold;">{{ number_format($data->sum('total'),
                                                        2, ',', '.') }}€</td>

                                                    </th>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- {{-- End: Product Set --}} -->

                      <!-- {{-- Product Set --}} -->
                    <div class="col-md-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sub-produckt (Unterprodukte)
                                      <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1   waves-effect waves-light"
                                         data-toggle="popover" 
                                        data-content="Liste der Produkte, die vom Masterset abhängig sind" 
                                        data-original-title="Sub-produckt"  
                                        data-trigger="hover"
                                        data-placement="top"><i class="feather icon-info"></i>
                                    </button>
                                </h4>
                                <a href="{{ url('refresh_master_set/'.request()->id) }}">
                                    <div class="badge badge-info">Anteil Lohnkosten: {{ $percentage->employee_percent }}
                                    </div>
                                </a>
                            </div>
                           
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover-animation mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Houptprodukt</th>
                                                    <th scope="col">ProduktSet</th>
                                                    <th scope="col">Anzahl</th>
                                                    <th scope="col">Einkaufspreis + Rabbat</th>
                                                    <th scope="col">Gesamtpreis</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @php
                                                    $all_products = $product;  
                                                @endphp

                                                @foreach ($subProducts as $item)
                                                    <tr>
                                                        <th scope="row">{{ $item->product_id }}</th>
                                                          <td>{{ $item->product }}</td>
                                                        <td>
                                                            @php 
                                                                $main_product = collect($all_products)->firstWhere('id', $item->main_product);
                                                            @endphp
                                                            
                                                            @if($main_product)
                                                                {{ $main_product['product'] }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                      
                                                        <td>{{ $item->product_count }}</td>
                                                        <td>{{ number_format($item->purchase_price, 2, ',', '.') }}€</td>
                                                        <td>{{ number_format($item->total, 2, ',', '.') }}€</td>
                                                    </tr>
                                                @endforeach


                                                <tr>
                                                    <th colspan="2">
                                                    <td style="font-weight: bold;">{{ $data->sum('product_count') }}
                                                    </td>
                                                    <td style="font-weight: bold;">{{
                                                        number_format($subProducts->sum('purchase_price'), 2, ',', '.') }}€
                                                    </td>
                                                    <td style="font-weight: bold;">{{ number_format($subProducts->sum('total'),2, ',', '.') }}€</td>

                                                    </th>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- {{-- End: Product Set --}} -->

                    {{-- Product Set --}}
                    <div class="col-md-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Set Paragraph</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover-animation mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Paragraph</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($paragraph as $item)
                                                <tr>
                                                    <th scope="row">{{ $item->id }}</th>
                                                    <td>{!! $item->content !!}</td>
                                                </tr>
                                                @endforeach


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- End: Product Set --}}

                    {{-- Product Set --}}
                    <div class="col-md-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Set Image</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover-animation mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Image</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($images as $item)
                                                <tr>
                                                    <th scope="row">{{ $item->id }}</th>
                                                    <td>
                                                        <div class="avatar mr-1 avatar-xl">
                                                            <img src="{{ asset('images/products/'.$item->image)}}"
                                                                alt="{{ $item->image }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="badge badge-{{ $item->status == 'active' ? 'success' : 'secondary' }} mr-1 mb-1">
                                                            <i class="feather icon-image"></i> {{ $item->status }}
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <a type="button"
                                                            class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light"><i
                                                                class="feather icon-sun"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- End: Product Set --}}
                </div>
        </div>
        </section>
        <!-- // Basic Horizontal form layout section end -->



    </div>

</div>
</div>
</div>
<!-- END: Content-->
@stop

@section('script')
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

    <script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>


@endsection