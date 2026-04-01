@extends('admin.layouts.app')

@section('title')Set Produckt @endsection
@section('style')
<!-- Include stylesheet -->

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

<style>
    .img-flag{
        width : 20px !important;
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
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">ZUBEHÖRARTIKEL</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-content"> 
                                    <div class="card-body">  
                                        <div class="form-body">
                                            <div class="row"> 
                                                    <div class="col-12">
                                                        @if (count($errors) > 0)
                                                        <div class="alert alert-danger">
                                                            <ul>
                                                                @foreach ($errors->all() as $error)
                                                                    <li>{{ $error }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        @endif 
                                                            @if(DB::table('user_rolls')
                                                                    ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                    ->where('user_rolls.item_id', '=', 'Product')
                                                                    ->where('user_rolls.is_add', '=', 'on')
                                                                    ->first())
                                                    
                                                            <div class="card"> 
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                            <form novalidate action="{{ route('add.sub.product', ['master'=>request()->master, 'phase'=>request()->phase]) }}" method="get" >
                                                                              @csrf
                                                                                <div class="row"> 
                                                                                        <div class="col-md-6 col-12">
                                                                                            <div class="text-bold-600 font-medium-2">
                                                                                                Suchen Sie im Produkt nach dem Set
                                                                                            </div>
                                                                                            <p><code>Achtung: </code> In der Liste sind nur die Produkte mit Lieferpreis verfügbar</p>
                                                                                            <fieldset class="form-group">
                                                                                                <select class="select2"  name="search" id="item" style=""  >
                                                                                                    @foreach ($products as $pro)
                                                                                                        <option {{ $pro->id }}>{{ $pro->product }}</option>
                                                                                                    @endforeach 
                                                                                                </select>
                                                                                            </fieldset>
                                                                                           
                                                                                        </div> 
                                                                                </div>
                                                                           </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                               @endif
                                                            <hr>
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-8">
                                                                        <span><h3>Ergebnisprodukt</h3></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <table class="table" id="brand_table">
                                                                <thead>
                                                                    <tr style=" background: #8fc73e;   color: white;  ">
                                                                        <th style="border: 1px; border-style: solid; ">#</th>
                                                                        <th style="border: 1px; border-style: solid; ">Artikel#</th> 
                                                                        <th style="border: 1px; border-style: solid; ">Zobehörartikel</th> 
                                                                        <th style="border: 1px; border-style: solid; ">Liefrant</th>
                                                                        <th style="border: 1px; border-style: solid; ">Rabbat-Gruppe</th>
                                                                        <th style="border: 1px; border-style: solid; ">Einkaufspreis</th>
                                                                        <th style="border: 1px; border-style: solid; ">Gesamtpreis</th>
                                                                        <th style="border: 1px; border-style: solid; ;">Verfügbarkeit</th>
                                                                        <th style="border: 1px; border-style: solid;">Action</th>
                                                                    </tr>
                                                                
                                                                </thead>
                                                                <tbody> 
                                                                  @foreach ($distributor_price as $item)
                                                                    <tr>
                                                                        <td>{{ $item->product_id }}</td>
                                                                        <td>{{ $item->article_no }}</td> 
                                                                        <td>{{ $item->product }}</td>
                                                                        <td>{{ $item->distributor_name }}</td>
                                                                        <td>{{ $item->discount_group }} - {{ $item->discount }}%</td>
                                                                        <td>{{ $item->price }}</td>
                                                                        <td>{{ $item->purchase_price }}</td>
                                                                        <td>{{ $item->availability }}</td>
                                                                        <td>
                                                                            <!-- Add to Set Button -->
                                                                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#add-product{{ $item->product_id }}">
                                                                                Zum Set hinzufügen
                                                                            </button>

                                                                            <!-- Modal for adding product to set -->
                                                                            <div class="modal fade" id="add-product{{ $item->product_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">&times;</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\ProductSubSetController@store')}}" enctype="multipart/form-data">
                                                                                            @csrf
                                                                                            <div class="modal-body">
                                                                                                <h5>Produkt zum Set hinzufügen</h5>
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">Product</label>
                                                                                                    <input type="hidden" name="master_set_id" value="{{ request()->master }}">
                                                                                                    <input type="hidden" name="price_id" value="{{ $item->price_id }}">
                                                                                                    <input type="hidden" name="phase" value="{{ request()->phase }}">
                                                                                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                                                                    <input type="text" class="form-control" name="product" value="{{ $item->product }}" required>
                                                                                                    @if ($errors->has('product'))<p style="color:red;">{!!$errors->first('product')!!}</p>@endif
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">Liefrant</label>
                                                                                                    <input type="hidden" name="distributor_id" value="{{ $item->id }}">
                                                                                                    <input type="text" class="form-control" name="distributor" value="{{ $item->distributor_name }}" required>
                                                                                                    @if ($errors->has('product'))<p style="color:red;">{!!$errors->first('product')!!}</p>@endif
                                                                                                </div>
                                                                                               
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">Gesamtpreis</label>
                                                                                                    <input type="text" class="form-control" value="{{ $item->purchase_price }}" required>
                                                                                                </div>
                                                                                                    <div class="form-group">
                                                                                                    <label for="measure">Houptartikel</label>
                                                                                                    <select class="form-control" name="main_product">
                                                                                                        @foreach ($main_products as $product)
                                                                                                        <option value="{{ $product->id }}">{{ $product->product }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">Produktanzahl</label>
                                                                                                    <input type="text" class="form-control" name="product_count" required>
                                                                                                    @if ($errors->has('product_count'))<p style="color:red;">{!!$errors->first('product_count')!!}</p>@endif
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label for="measure">Maßeinheit</label>
                                                                                                    <select class="form-control" name="measure_unit">
                                                                                                        @foreach ($measure as $me)
                                                                                                        <option value="{{ $me->id }}">{{ $me->measure }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @if ($errors->has('measure'))<p style="color:red;">{!!$errors->first('measure')!!}</p>@endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="submit" class="btn btn-primary">Einreichen</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach

                                                                </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                                @if(Session()->has('handoverID'))
                                                <a type="button" class="btn btn-primary" href="{{ url('/handover_next/'.Session('handoverID')) }}">Next</a>
                                                @endif
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
  <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>

<script src="{{ asset('js/select2.min.js') }}"></script>
 

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
 
<script type="text/javascript">
    $(document).ready(function() {
        $('#item').on('change', function() {
            var search = $(this).val();
            
            $.ajax({
                url: "{{ route('product.set.search') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    search: search
                },
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        var tableBody = '';
                        $.each(data.distributor_price, function(key, item) {
                            tableBody += '<tr>';
                            tableBody += '<td>' + item.product_id + '</td>';
                            tableBody += '<td>' + item.article_no + '</td>';
                            tableBody += '<td>' + item.product + '</td>';
                            tableBody += '<td>' + item.distributor_name + '</td>';
                            tableBody += '<td>' + item.discount_group + '-' + item.discount + '%</td>';
                            tableBody += '<td>' + item.price + '</td>';
                            tableBody += '<td>' + item.purchase_price + '</td>';
                            tableBody += '<td>' + item.availability + '</td>';
                            tableBody += '<td><button type="button" class="btn btn-outline-primary open-modal" data-id="' + item.product_id + '">Zum Set hinzufügen</button></td>';
                            tableBody += '</tr>';
                            
                            // Create a corresponding modal for the product dynamically
                            var modalHtml = `
                                <div class="modal fade" id="add-product${item.product_id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\ProductSubSetController@store')}}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <h5>Produkt zum Set hinzufügen</h5>
                                                    <div class="form-group">
                                                        <label for="Title">Product</label>
                                                        <input type="hidden" name="master_set_id" value="{{ request()->master }}">
                                                        <input type="hidden" name="price_id" value="${item.price_id}">
                                                        <input type="hidden" name="product_id" value="${item.product_id}">
                                                        <input type="text" class="form-control" name="product" value="${item.product}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Title">Liefrant</label>
                                                        <input type="hidden" name="distributor_id" value="${item.id}">
                                                        <input type="text" class="form-control" name="distributor" value="${item.distributor_name}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Title">Gesamtpreis</label>
                                                        <input type="text" class="form-control" value="${item.purchase_price}" required>
                                                    </div>
                                                      <div class="form-group">
                                                        <label for="measure">Houptartikel</label>
                                                        <select class="form-control" name="main_product">
                                                            @foreach ($main_products as $product)
                                                            <option value="{{ $product->id }}">{{ $product->product }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Title">Produktanzahl</label>
                                                        <input type="text" class="form-control" name="product_count" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="measure">Maßeinheit</label>
                                                        <select class="form-control" name="measure_unit">
                                                            @foreach ($measure as $me)
                                                            <option value="{{ $me->id }}">{{ $me->measure }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Einreichen</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>`;
                            
                            $('body').append(modalHtml);  // Append the modal to the body
                        });
                        $('#brand_table tbody').html(tableBody);
                    }
                },
                error: function() {
                    alert('Error while searching');
                }
            });
        });
        
        // Delegate event handling to dynamically created elements
        $(document).on('click', '.open-modal', function() {
            var productId = $(this).data('id');
            $('#add-product' + productId).modal('show');
        });
    });
</script>

@endsection






