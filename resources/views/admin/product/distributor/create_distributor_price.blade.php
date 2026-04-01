@extends('admin.layouts.app')
@section('title') Lieferpreis  @stop

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Produkt</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/product_details/'.$product_id) }}">{{ $product->product }}</a></li>
                                <li class="breadcrumb-item active">Lieferpreis</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <form action="" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Geben Sie die Details Ihrer Suche ein">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="feather icon-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-md-right mt-1 mt-md-0">
                    <a href="{{ url('/product_details/'.$product_id) }}" class="btn btn-outline-warning">
                        <i class="feather icon-chevrons-left"></i> Zurück
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lieferpreis erstellen</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ action('App\\Http\\Controllers\\DistributorPriceController@store') }}" method="POST" novalidate>
                        @csrf
                        <div class="table-responsive">
                            <table class="table" id="add_d">
                                <thead>
                                    <tr>
                                        <th>Produkt</th>
                                        <th>Lieferant</th>
                                        <th>Artikel#</th>
                                        <th>UVP</th>
                                        <th>Rabatt-Gruppe</th>
                                        <th>Rabatt-Preis</th>
                                        <th>Rabatt%</th>
                                        <th>Einkaufspreis</th>
                                        <th>Datum</th>
                                        <th>Verfügbarkeit</th>
                                        <th>Aktion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <input type="hidden" name="d[0][product_id]" value="{{ $product_id }}">
                                        <td><input type="text" class="form-control" value="{{ $product->product }}" disabled></td>
                                        
                                        <td>
                                                <select name="d[0][distributor_id]" class="form-control">
                                                    <option value="">-- Bitte wählen --</option> 
                                                    @foreach ($distributors as $distributor)
                                                        <option value="{{ $distributor->distributor_id }}">
                                                            {{ $distributor->distributor_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td> 
                                        <td><input type="text" class="form-control" name="d[0][article_no]" placeholder="Artikel#"></td>
                                        <td><input type="number" class="form-control" name="d[0][price]" placeholder="UVP"></td>
                                        <td>
                                            <select name="d[0][discount_group]" class="form-control discount_group">
                                                <option value="">-- Bitte wählen --</option> 

                                                @foreach ($discount_groups as $discount)
                                                    <option value="{{ $discount->discount }}" data-discount="{{ $discount->discount }}">
                                                        {{ $discount->discount_group }} - {{ $discount->discount }}%
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="d[0][discount_price]" placeholder="Rabatt-Preis"></td>
                                        <td><input type="number" class="form-control" name="d[0][discount_percent]" placeholder="Rabatt %"></td>
                                        <td><input type="number" class="form-control" name="d[0][purchase_price]" placeholder="Einkaufspreis"></td>
                                        <td><input type="date" class="form-control" name="d[0][price_date]" value="{{ date('Y-m-d') }}"></td>
                                        <td><input type="text" class="form-control" name="d[0][availability]" value="sofortige Lieferung"></td>
                                        <td>
                                            <button type="button" class="btn btn-icon btn-outline-primary" id="add_price"><i class="feather icon-plus"></i></button>
                                            <button type="button" class="btn btn-icon btn-outline-secondary" id="refresh"><i class="feather icon-refresh-ccw"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-success"><i class="feather icon-save"></i> Datensatz speichern</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title">Alle Lieferpreise</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Lieferant</label>
                                <select name="distributor_id" class="form-control">
                                    <option value="">-- Alle --</option>
                                    @foreach ($distributors as $d)
                                        <option value="{{ $d->distributor_id }}" {{ request('distributor_id') == $d->distributor_id ? 'selected' : '' }}>
                                            {{ $d->distributor_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Datum</label>
                                <input type="date" name="price_date" class="form-control" value="{{ request('price_date') }}">
                            </div>

                            <div class="col-md-3">
                                <label>Suche</label>
                                <input type="text" name="search" class="form-control" placeholder="Suche..." value="{{ request('search') }}">
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2"><i class="feather icon-filter"></i> Filtern</button>
                                <a href="{{ url()->current() }}" class="btn btn-secondary"><i class="feather icon-x"></i> Zurücksetzen</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Produkt</th>
                                    <th>Lieferant</th>
                                    <th>UVP</th>
                                    <th>Rabatt-Gruppe</th>
                                    <th>Einkaufspreis</th>
                                    <th>Verfügbarkeit</th>
                                    <th>Datum</th>
                                    <th>Status</th>
                                    <th>Aktion</th>
                                    <th>Veröffentlichen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($distributor_price as $dist)
                                    <tr>
                                        <td>{{ $dist->product }}</td>
                                        <td>{{ $dist->name }}</td>
                                        <td>{{ number_format($dist->price, 2, ',', '.') }} €</td>
                                        <td>{{ $dist->discount_percent }}%</td>
                                        <td>{{ number_format($dist->purchase_price, 2, ',', '.') }} €</td>
                                        <td>{{ $dist->availability }}</td>
                                        <td>{{ $dist->price_date }}</td>
                                        <td>
                                            <span class="badge badge-{{ $dist->status == 'Published' ? 'success' : 'danger' }}">
                                                {{ $dist->status == 'Published' ? 'Veröffentlicht' : 'Nicht Veröffentlicht' }}
                                            </span>
                                        </td>
                                        <td class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#delete-pro{{ $dist->id }}">
                                                <i class="feather icon-trash"></i>
                                            </button>
                                            <div class="modal fade text-left" id="delete-pro{{ $dist->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Löschen bestätigen</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Möchten Sie diesen Lieferpreis wirklich löschen?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form action="{{ url('distributor_price/delete/'.$dist->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Löschen</button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#edit{{ $dist->id }}">
                                                <i class="feather icon-edit"></i>
                                            </button>
                                            <!-- Add your edit modal content here -->
                                        </td>
                                        <td>
                                            @if ($dist->status == 'Unpublished' || $dist->status == '')
                                                <a href="{{ url('/distributor_price_publish/' . $dist->id) }}" class="btn btn-sm btn-success">
                                                    <i class="feather icon-check"></i>
                                                </a>
                                            @else
                                                <a href="{{ url('/distributor_price_unpublish/' . $dist->id) }}" class="btn btn-sm btn-danger">
                                                    <i class="feather icon-x"></i>
                                                </a>
                                            @endif
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
</div>
@endsection

@section('script')
<script>
    $(document).ready(function (){
        console.log('it is working');
    })
</script>
<script>
    @if(Session::has('update_msg'))
    <script>toastr.success("{{ session('updated_msg') }}");</script>
    @endif
    @if(Session::has('save_msg'))
    <script>toastr.success("{{ session('save_msg') }}");</script>
    @endif
    @if(Session::has('delete_msg'))
    <script>toastr.error("{{ session('delete_msg') }}");</script>
    @endif

</script>
<script>
$(document).ready(function () {
    let rowCount = 1;

    function calculateRow($row, manualField = null) {
        let price = parseFloat($row.find('input[name*="[price]"]').val()) || 0;
        let discountPrice = parseFloat($row.find('input[name*="[discount_price]"]').val()) || 0;
        let discountPercent = parseFloat($row.find('input[name*="[discount_percent]"]').val()) || 0;
        let purchasePrice = parseFloat($row.find('input[name*="[purchase_price]"]').val()) || 0;

        if (price > 0) {
            if (manualField === 'discount_price') {
                let percent = (discountPrice / price) * 100;
                let purchase = price - discountPrice;
                $row.find('input[name*="[discount_percent]"]').val(percent.toFixed(2));
                $row.find('input[name*="[purchase_price]"]').val(purchase.toFixed(2));
            } else if (manualField === 'discount_percent') {
                let discount = (price * discountPercent) / 100;
                let purchase = price - discount;
                $row.find('input[name*="[discount_price]"]').val(discount.toFixed(2));
                $row.find('input[name*="[purchase_price]"]').val(purchase.toFixed(2));
            } else if (manualField === 'purchase_price') {
                let discount = price - purchasePrice;
                let percent = (discount / price) * 100;
                $row.find('input[name*="[discount_price]"]').val(discount.toFixed(2));
                $row.find('input[name*="[discount_percent]"]').val(percent.toFixed(2));
            }
        } else if (purchasePrice > 0 && discountPrice > 0) {
            let calcPrice = purchasePrice + discountPrice;
            let percent = (discountPrice / calcPrice) * 100;
            $row.find('input[name*="[price]"]').val(calcPrice.toFixed(2));
            $row.find('input[name*="[discount_percent]"]').val(percent.toFixed(2));
        } else if (purchasePrice > 0 && discountPercent > 0) {
            let calcPrice = purchasePrice / (1 - (discountPercent / 100));
            let calcDiscount = calcPrice - purchasePrice;
            $row.find('input[name*="[price]"]').val(calcPrice.toFixed(2));
            $row.find('input[name*="[discount_price]"]').val(calcDiscount.toFixed(2));
        }
    }

    $('#add_d').on('input', 'input', function () {
        const $row = $(this).closest('tr');
        const nameAttr = $(this).attr('name') || '';
        const match = nameAttr.match(/\[([^\]]+)\]$/);
        if (match && match[1]) {
            calculateRow($row, match[1]);
        }
    });

    $('#add_d').on('change', 'select.discount_group', function () {
        const $row = $(this).closest('tr');
        const discount = $(this).find('option:selected').data('discount') || 0;
        $row.find('input[name*="[discount_percent]"]').val(discount);
        calculateRow($row, 'discount_percent');
    });

    $('#refresh').click(function () {
        $('#add_d tbody tr').each(function () {
            calculateRow($(this));
        });
    });

    $('#add_price').click(function () {
        const $lastRow = $('#add_d tbody tr:last');
        const $newRow = $lastRow.clone();

        $newRow.find('input, select').each(function () {
            const name = $(this).attr('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, `[${rowCount}]`);
                $(this).attr('name', newName);
            }

            if ($(this).attr('name').includes('[product_id]')) {
                $(this).val('{{ $product_id }}');
            } else {
                $(this).val('');
            }
        });

        // Set today's date
        const today = new Date().toISOString().split('T')[0];
        $newRow.find('input[type="date"]').val(today);


        $('#add_d tbody').append($newRow);
        rowCount++;
    });

});
</script>

@endsection
