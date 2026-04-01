
@extends('admin.layouts.app')
@section('title', 'Berechnung bearbeiten')
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title">Berechnung bearbeiten</h2>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('economic_calculations.update', $calculation->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <label>Solar Gesamtpreis (€)</label>
                                <input type="number" step="0.01" name="solar_total_cost" class="form-control" value="{{ $calculation->solar_total_cost }}">
                            </div>
                            <div class="col-md-6">
                                <label>Solar Ersparnis (€)</label>
                                <input type="number" step="0.01" name="solar_savings" class="form-control" value="{{ $calculation->solar_savings }}">
                            </div>
                            <div class="col-md-6">
                                <label>Solar ROI (Jahre)</label>
                                <input type="number" step="0.01" name="solar_roi_years" class="form-control" value="{{ $calculation->solar_roi_years }}">
                            </div>
                            <div class="col-md-6">
                                <label>WP Gesamtpreis (€)</label>
                                <input type="number" step="0.01" name="wp_total_cost" class="form-control" value="{{ $calculation->wp_total_cost }}">
                            </div>
                            <div class="col-md-6">
                                <label>WP Ersparnis (€)</label>
                                <input type="number" step="0.01" name="wp_savings" class="form-control" value="{{ $calculation->wp_savings }}">
                            </div>
                            <div class="col-md-6">
                                <label>WP ROI (Jahre)</label>
                                <input type="number" step="0.01" name="wp_roi_years" class="form-control" value="{{ $calculation->wp_roi_years }}">
                            </div>
                            <div class="col-md-6">
                                <label>Gesamtersparnis (€)</label>
                                <input type="number" step="0.01" name="combined_savings" class="form-control" value="{{ $calculation->combined_savings }}">
                            </div>
                            <div class="col-md-6">
                                <label>Gesamt ROI (Jahre)</label>
                                <input type="number" step="0.01" name="combined_roi" class="form-control" value="{{ $calculation->combined_roi }}">
                            </div>
                        </div>

                        @if($calculation->lead->solarSystems->first())
                        <hr>
                        <h5 class="text-primary mt-2">Photovoltaik-System</h5>
                        <div class="row">
                            @php $solar = $calculation->lead->solarSystems->first(); @endphp
                            <div class="col-md-6"><input name="kwp_size" class="form-control mb-1" value="{{ $solar->kwp_size }}" placeholder="kWp"></div>
                            <div class="col-md-6"><input name="self_consumption_rate" class="form-control mb-1" value="{{ $solar->self_consumption_rate }}" placeholder="Eigenverbrauch %"></div>
                            <div class="col-md-6"><input name="feed_in_tariff" class="form-control mb-1" value="{{ $solar->feed_in_tariff }}" placeholder="Einspeisevergütung"></div>
                            <div class="col-md-6"><input name="system_price" class="form-control mb-1" value="{{ $solar->system_price }}" placeholder="Gesamtpreis"></div>
                            <div class="col-md-6"><input name="battery_capacity" class="form-control mb-1" value="{{ $solar->battery_capacity }}" placeholder="Speichergröße"></div>
                            <div class="col-md-6"><input name="battery_price" class="form-control mb-1" value="{{ $solar->battery_price }}" placeholder="Speicherpreis"></div>
                        </div>
                        @endif

                        @if($calculation->lead->heatPumps->first())
                        <hr>
                        <h5 class="text-success mt-2">Wärmepumpe-System</h5>
                        <div class="row">
                            @php $wp = $calculation->lead->heatPumps->first(); @endphp
                            <div class="col-md-6"><input name="type" class="form-control mb-1" value="{{ $wp->type }}" placeholder="Typ"></div>
                            <div class="col-md-6"><input name="cop" class="form-control mb-1" value="{{ $wp->cop }}" placeholder="COP"></div>
                            <div class="col-md-6"><input name="installation_cost" class="form-control mb-1" value="{{ $wp->installation_cost }}" placeholder="Installationskosten"></div>
                            <div class="col-md-6"><input name="annual_costs" class="form-control mb-1" value="{{ $wp->annual_costs }}" placeholder="Jährliche Kosten"></div>
                        </div>
                        @endif

                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-success">Aktualisieren</button>
                            <a href="{{ route('economic_calculations.index') }}" class="btn btn-secondary">Zurück</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection