@extends('admin.layouts.app')
@section('title') Lieferpreis @stop

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Produkt</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/product_details/'.$product_id)}}">{{ $product->product }}</a></li>
                                <li class="breadcrumb-item active">Lieferpreis</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-md-6 col-12 mb-1">
                    <form action="" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Geben Sie die Details Ihrer Suche ein" name="search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="feather icon-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <a href="{{ url('/product_details/'.$product_id) }}" class="btn btn-outline-warning mt-1">
                        <i class="feather icon-chevrons-left"></i> Zurück
                    </a>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ action('App\\Http\\Controllers\\DistributorPriceController@store') }}" method="post">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table" id="add_d">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Produkt</th>
                                                <th>Lieferant</th>
                                                <th>Preis</th>
                                                <th>Rabbat-Gruppe</th>
                                                <th>Datum</th>
                                                <th>Verfügbarkeit</th>
                                                <th>Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <input type="hidden" name="d[0][distributor_id]" value="{{ $distributor->id }}">
                                                <input type="hidden" name="d[0][product_id]" value="{{ $product_id }}">
                                                <td><input type="text" class="form-control" disabled value="{{ $product->product }}"></td>
                                                <td>
                                                    <input type="text" class="form-control" disabled value="{{ $distributor->name }}">
                                                    <input type="hidden" name="d[0][status]" value="Published">
                                                </td>
                                                <td><input type="number" class="form-control" name="d[0][price]" placeholder="Preis"></td>
                                                <td>
                                                    <select class="form-control" name="d[0][discount_price]">
                                                        @foreach ($discount as $dis)
                                                            <option value="{{ $dis->id }}">{{ $dis->discount_group }} - {{ $dis->discount }}%</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="date" class="form-control" name="d[0][price_date]"></td>
                                                <td><input type="text" class="form-control" name="d[0][availability]" value="sofortige Lieferung"></td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add_price">
                                                        <i class="feather icon-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-right mt-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="feather icon-save"></i> Datensatz speichern
                                    </button>
                                </div>
                            </form>

                            @include('admin.product.distributor.partial', ['distributor_price' => $distributor_price, 'distributor' => $distributor])

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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

<script>
    let i = 0;

    document.getElementById('add_price').addEventListener('click', function() {
        ++i;
        const add_d = document.getElementById('add_d');

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <input type="hidden" name="d[${i}][distributor_id]" value="{{ $distributor->id }}">
            <input type="hidden" name="d[${i}][product_id]" value="{{ $product_id }}">
            <td><input type="text" class="form-control" disabled value="{{ $product->product }}"></td>
            <td>
                <input type="text" class="form-control" disabled value="{{ $distributor->name }}">
                <input type="hidden" name="d[${i}][status]" value="Published">
            </td>
            <td><input type="number" class="form-control" name="d[${i}][price]" placeholder="Preis"></td>
            <td>
                <select class="form-control" name="d[${i}][discount_price]">
                    @foreach ($discount as $dis)
                        <option value="{{ $dis->id }}">{{ $dis->discount_group }} - {{ $dis->discount }}%</option>
                    @endforeach
                </select>
            </td>
            <td><input type="date" class="form-control" name="d[${i}][price_date]"></td>
            <td><input type="text" class="form-control" name="d[${i}][availability]" value="sofortige Lieferung"></td>
            <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="feather icon-minus-square"></i></button></td>
        `;

        add_d.appendChild(newRow);
    });

    function removeRow(button) {
        button.closest('tr').remove();
    }
</script>
@endsection
