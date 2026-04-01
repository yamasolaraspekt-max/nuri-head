@extends('admin.layouts.app')
@section('title') Set Product @stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
@endsection

@section('content')
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
            <div class="row" id="table-hover-animation">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-9 mb-1">
                                        <form action="{{ route('add.product.set', ['master'=>request()->master, 'phase'=>request()->phase]) }}">
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control" placeholder="Search Product">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">Go</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <a class="btn btn-outline-primary btn-block" href="{{ url('add_product_create/'.request()->master.'/'.request()->phase) }}">Erstellen</a>
                                    </div>
                                </div>

                                <!-- Product Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Art.name</th>
                                                <th>Art. Menge</th>
                                                <th>UVP</th>
                                                <th>Rabbat-Gruppe</th>
                                                <th>Einkaufspreis + Rabatt</th>
                                                <th>Gesamtpreis</th>
                                                <th>Beschreibung</th>
                                                <th>Aktionen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->product }}</td>
                                                <td>{{ $item->product_count }} {{ $item->measure }}</td>
                                                <td>{{ number_format($item->retail_price, 2, ',', '.') }}€</td>
                                                <td>{{ $item->discount_group }}</td>
                                                <td>{{ number_format($item->purchase_price, 2, ',', '.') }}€</td>
                                                <td>{{ number_format($item->total, 2, ',', '.') }}€</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#description{{ $item->id }}">
                                                        <i class="feather icon-maximize-2"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-pro{{ $item->id }}">
                                                            <i class="feather icon-trash"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editmodel{{ $item->id }}">
                                                            <i class="feather icon-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#add_product{{ $item->id }}">
                                                            <i class="feather icon-log-in"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $data->links() }}

                                <!-- Include all modals -->
                                @include('admin.offer.set.products.modals', compact('data', 'product_description', 'product', 'measure'))

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.form-control').each(function () {
            if ($(this).is('select')) {
                $(this).select2({ width: 'resolve' });
            }
        });

        @if(Session::has('update_msg'))
            toastr.success("{{ session('update_msg') }}");
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
