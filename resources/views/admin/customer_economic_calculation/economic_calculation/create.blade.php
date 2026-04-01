@extends('admin.layouts.app')
@section('title')Wirtschaftlichkeitsberechnung @stop
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
                        <h2 class="content-header-title float-left mb-0">Wirtschaftlichkeitsberechnung</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">NEUE</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <form id="calc-form">
                        @csrf
                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">

                        <div class="form-group">
                            <label for="product_id">Produkt wählen</label>
                            <select name="product_id" id="product_id" class="form-control select2">
                                <option value="">-- Produkt auswählen --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ strtolower($product->name) }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="solar-fields" class="d-none">
                            <h5 class="mt-2 text-primary">Photovoltaik Angaben</h5>
                            <div class="row">
                                <div class="col-md-6"><input name="kwp_size" class="form-control mb-1" placeholder="Anlagengröße kWp"></div>
                                <div class="col-md-6"><input name="self_consumption_rate" class="form-control mb-1" placeholder="Eigenverbrauchsquote %"></div>
                                <div class="col-md-6"><input name="feed_in_tariff" class="form-control mb-1" placeholder="Einspeisevergütung ct/kWh"></div>
                                <div class="col-md-6"><input name="system_price" class="form-control mb-1" placeholder="Gesamtpreis €"></div>
                                <div class="col-md-6"><input name="battery_capacity" class="form-control mb-1" placeholder="Speichergröße (optional)"></div>
                                <div class="col-md-6"><input name="battery_price" class="form-control mb-1" placeholder="Speicherpreis (optional)"></div>
                            </div>
                        </div>

                        <div id="heatpump-fields" class="d-none">
                            <h5 class="mt-2 text-success">Wärmepumpe Angaben</h5>
                            <div class="row">
                                <div class="col-md-6"><input name="type" class="form-control mb-1" placeholder="Typ (z.B. Luft-Wasser)"></div>
                                <div class="col-md-6"><input name="cop" class="form-control mb-1" placeholder="COP (Effizienz)"></div>
                                <div class="col-md-6"><input name="installation_cost" class="form-control mb-1" placeholder="Installationskosten €"></div>
                                <div class="col-md-6"><input name="annual_costs" class="form-control mb-1" placeholder="Jährliche Kosten (optional)"></div>
                            </div>
                        </div>

                        <div class="text-right mt-2">
                            <button type="submit" class="btn btn-primary">Berechnung starten</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->
@stop

@section('script')
<script>
    $(document).ready(function() {
        $('.select2').select2();

        const keywords = {
            solar: ['pv', 'photovoltaik', 'photovoltaic', 'solar'],
            wp: ['wp', 'wärmepumpe', 'heatpump']
        };

        $('#product_id').on('change', function () {
            const selectedName = $('#product_id option:selected').data('name') || '';
            $('#solar-fields').addClass('d-none');
            $('#heatpump-fields').addClass('d-none');

            if (keywords.solar.some(k => selectedName.includes(k))) {
                $('#solar-fields').removeClass('d-none');
            } else if (keywords.wp.some(k => selectedName.includes(k))) {
                $('#heatpump-fields').removeClass('d-none');
            }
        });

        $('#calc-form').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('economic_calculations.store') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Berechnung gespeichert.');
                    $('#calc-form')[0].reset();
                    $('#solar-fields, #heatpump-fields').addClass('d-none');
                },
                error: function(err) {
                    toastr.error('Fehler beim Speichern.');
                    console.log(err);
                }
            });
        });

        @if(Session::has('update_msg')) toastr.success("{{ session('updated_msg') }}"); @endif
        @if(Session::has('save_msg')) toastr.success("{{ session('save_msg') }}"); @endif
        @if(Session::has('delete_msg')) toastr.error("{{ session('delete_msg') }}"); @endif
    });
</script>
@endsection