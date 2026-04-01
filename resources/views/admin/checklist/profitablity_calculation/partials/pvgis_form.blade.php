@php
    $lat = old('latitude') ?? $lat ?? '';
    $lon = old('longitude') ?? $lon ?? '';
@endphp
<form id="pvgis-form" method="POST" action="{{ route('profitability.pvgis.fetch') }}">
    @csrf
    <input type="hidden" name="customer_id" value="{{ $customer_id }}">
    <input type="hidden" name="alternative_id" value="{{ $alternative_id }}">
    <input type="hidden" name="active_step" value="step-2">

    <div class="row">
        <div class="col-md-4">
            <label>Postleitzahl (PLZ)</label>
            <input type="text" class="form-control" name="postcode" id="postcode" value="{{ $postcode }}" required>
        </div>
        <div class="col-md-4">
            <label>Latitude</label>
            <input type="text" class="form-control" name="latitude" id="latitude" value="{{ $lat }}">
        </div>
        <div class="col-md-4">
            <label>Longitude</label>
            <input type="text" class="form-control" name="longitude" id="longitude" value="{{ $lon }}">
        </div>
        <div class="col-md-3">
            <label>Peak Power (kWp)</label>
            <input type="number" step="0.1" class="form-control" name="peakpower" value="3.0">
        </div>
        <div class="col-md-3">
            <label>Systemverlust (%)</label>
            <input type="number" step="0.1" class="form-control" name="loss" value="14">
        </div>
        <div class="col-md-3">
            <label>Neigung (°)</label>
            <input type="number" step="1" class="form-control" name="angle" value="45">
        </div>
        <div class="col-md-3">
            <label>Ausrichtung (0=S)</label>
            <input type="number" step="1" class="form-control" name="aspect" value="0">
        </div>
        <div class="col-md-3">
            <label>Batterygröße (Wh)</label>
            <input type="number" step="1" class="form-control" name="battery_size" value="50">
        </div>
        <div class="col-md-3">
            <label>Verbrauch pro Tag (Wh)</label>
            <input type="number" step="1" class="form-control" name="consumption" value="200">
        </div>
        <div class="col-md-3">
            <label>Entladegrenze (%)</label>
            <input type="number" step="1" class="form-control" name="cutoff" value="40">
        </div>
    </div>

    <button type="submit" class="btn btn-success btn-block mt-3">
        <i class="fa fa-sun-o"></i> Wetterdaten laden
    </button>
</form>