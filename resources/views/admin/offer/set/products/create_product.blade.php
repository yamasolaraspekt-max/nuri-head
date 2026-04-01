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
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Set Product</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('article_group_set') }}">{{ $title->article_group }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('master_set/'.$title->article_group_id.'/'.$title->sub_id) }}">{{ $title->sub_article }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('sets/'.$title->master_id) }}">{{ $title->setname }}</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body"> 
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
                                                    
                                                            <div class="card"> 
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                            <form novalidate action="{{ route('add.product.create',['master'=>request()->master, 'phase'=>request()->phase])}}" method="get" >
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
                                                            <hr>
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-8">
                                                                        <span><h3>Ergebnisprodukt</h3></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if(count($distributor_price))
                                                                <table class="table" id="brand_table">
                                                                    <thead>
                                                                        <tr style="background: #8fc73e; color: white;">
                                                                            <th style="border: 1px solid;">#</th>
                                                                            <th style="border: 1px solid;">Artikel#</th>
                                                                            <th style="border: 1px solid;">Product</th>
                                                                            <th style="border: 1px solid;">Liefrant</th>
                                                                            <th style="border: 1px solid;">Rabbat-Gruppe</th>
                                                                            <th style="border: 1px solid;">Einkaufspreis</th>
                                                                            <th style="border: 1px solid;">Gesamtpreis</th>
                                                                            <th style="border: 1px solid;">Verfügbarkeit</th>
                                                                            <th style="border: 1px solid;">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($distributor_price as $item)
                                                                            <tr>
                                                                                <td>{{ $item->product_id }}</td>
                                                                                <td>{{ $item->article_no }}</td>
                                                                                <td>{{ $item->product }}</td>
                                                                                <td>{{ $item->distributor_name ?? 'Kein Lieferant' }}</td>
                                                                                <td>
                                                                                    @if($item->discount_group)
                                                                                        {{ $item->discount_group }} - {{ $item->discount }}%
                                                                                    @else
                                                                                        Kein Rabatt
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $item->price ?? '-' }}</td>
                                                                                <td>{{ $item->purchase_price ?? '-' }}</td>
                                                                                <td>{{ $item->availability ?? '-' }}</td>
                                                                                <td>
                                                                                    @if($item->price_id)
                                                                                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#add-product{{ $item->product_id }}">
                                                                                            Zum Set hinzufügen
                                                                                        </button>

                                                                                        <!-- Modal for product -->
                                                                                        <div class="modal fade" id="add-product{{ $item->product_id }}" tabindex="-1" role="dialog" aria-labelledby="productModalLabel{{ $item->product_id }}" aria-hidden="true">
                                                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header">
                                                                                                        <h5 class="modal-title" id="productModalLabel{{ $item->product_id }}">Produkt zum Set hinzufügen</h5>
                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                            <span aria-hidden="true">&times;</span>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <form class="form-horizontal" method="POST" action="{{ route('add.product.set.save') }}" enctype="multipart/form-data">
                                                                                                        @csrf
                                                                                                        <div class="modal-body">
                                                                                                            <input type="hidden" name="master_set_id" value="{{ request()->master }}">
                                                                                                            <input type="hidden" name="phase" value="{{ request()->phase }}">
                                                                                                            <input type="hidden" name="price_id" value="{{ $item->price_id }}">
                                                                                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                                                                            <input type="hidden" name="distributor_id" value="{{ $item->distributor_id }}">

                                                                                                            <div class="form-group">
                                                                                                                <label>Product</label>
                                                                                                                <input type="text" class="form-control" name="product" value="{{ $item->product }}" required>
                                                                                                            </div>
                                                                                                            <div class="form-group">
                                                                                                                <label>Liefrant</label>
                                                                                                                <input type="text" class="form-control" name="distributor" value="{{ $item->distributor_name }}" required>
                                                                                                            </div>
                                                                                                            <div class="form-group">
                                                                                                                <label>Gesamtpreis</label>
                                                                                                                <input type="text" class="form-control" name="purchase_price" value="{{ $item->purchase_price }}" required>
                                                                                                            </div>
                                                                                                            <div class="form-group">
                                                                                                                <label>Produktanzahl</label>
                                                                                                                <input type="text" class="form-control" name="product_count" required>
                                                                                                            </div>
                                                                                                            <div class="form-group">
                                                                                                                <label>Maßeinheit</label>
                                                                                                                <select class="form-control" name="measure_unit" required>
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
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="badge badge-warning">Kein Preis</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            @else
                                                                <div class="text-center p-5 border rounded bg-light">
                                                                    <h4>Kein Produkt mit Lieferpreis gefunden.</h4>
                                                                    <p>Sie können jetzt einen Lieferanten mit Preis hinzufügen.</p>
                                                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#distributors" data>
                                                                        <i class="feather icon-plus"></i> Lieferant & Preis erstellen
                                                                    </button>
                                                                </div>
                                                            @endif

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
    $(document).ready(function () {
        $('#item').on('change', function () {
            var search = $(this).val();

            $.ajax({
                url: "{{ route('product.set.search') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    search: search
                },
                success: function (data) {
                    if (data.error) {
                        $('#brand_table tbody').html('<tr><td colspan="9" class="text-center text-danger">' + data.error + '</td></tr>');
                        return;
                    }

                    let tableBody = '';
                    $('#brand_table tbody').html('');

                    if (!data.distributor_price.length) {
                        $('#brand_table tbody').html('<tr><td colspan="9" class="text-center">Kein passendes Produkt gefunden.</td></tr>');
                        return;
                    }

                    data.distributor_price.forEach(function (item) {
                        let modalId = `add-product${item.product_id}`;
                        let hasPrice = item.price_id !== null;

                        tableBody += `
                            <tr>
                                <td>${item.product_id}</td>
                                <td>${item.article_no || '-'}</td>
                                <td>${item.product}</td>
                                <td>${item.distributor_name || 'Kein Lieferant'}</td>
                                <td>${item.discount_group ? item.discount_group + ' - ' + item.discount + '%' : 'Kein Rabatt'}</td>
                                <td>${item.price ?? '-'}</td>
                                <td>${item.purchase_price ?? '-'}</td>
                                <td>${item.availability ?? '-'}</td>
                                <td>
                                    ${hasPrice ? `
                                        <button type="button" class="btn btn-outline-primary open-modal" data-id="${item.product_id}">Zum Set hinzufügen</button>
                                        <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Produkt zum Set hinzufügen</h5>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form method="POST" action="{{ route('add.product.set.save') }}" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <input type="hidden" name="master_set_id" value="{{ request()->master }}">
                                                              <input type="hidden" name="phase" value="{{ request()->phase }}">

                                                            <input type="hidden" name="price_id" value="${item.price_id}">
                                                            <input type="hidden" name="product_id" value="${item.product_id}">
                                                            <input type="hidden" name="distributor_id" value="${item.distributor_id ?? ''}">
                                                            
                                                            <div class="form-group">
                                                                <label>Produkt</label>
                                                                <input type="text" name="product" class="form-control" value="${item.product}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Lieferant</label>
                                                                <input type="text" name="distributor" class="form-control" value="${item.distributor_name ?? ''}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Gesamtpreis</label>
                                                                <input type="text" name="purchase_price" class="form-control" value="${item.purchase_price ?? ''}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Produktanzahl</label>
                                                                <input type="text" name="product_count" class="form-control" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Maßeinheit</label>
                                                                <select name="measure_unit" class="form-control">
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
                                        </div>
                                    ` : `<span class="badge badge-warning">Kein Preis</span>`}
                                </td>
                            </tr>
                        `;

                        if (hasPrice) {
                            $('body').append(`#${modalId}`);
                        }
                    });

                    $('#brand_table tbody').html(tableBody);
                },
                error: function () {
                    alert('Fehler beim Abrufen der Daten.');
                }
            });
        });

        // Modal open handler
        $(document).on('click', '.open-modal', function () {
            const productId = $(this).data('id');
            $(`#add-product${productId}`).modal('show');
        });
    });
</script>


 
@endsection






