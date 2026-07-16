<div class="row">
    <!-- Stromverbrauch -->
    <div class="col-12 mt-1">
        <label>Stromverbrauch</label>
        <div class="position-relative">
            <input type="text" class="form-control" name="annual_consumption" value="{{ old('annual_consumption', $alternative->annual_consumption) }}">
            <span class="position-absolute" style="right: 20px; top: 8px;">kWh</span>
        </div>
    </div>

    <!-- Kamin vorhanden -->
    <div class="col-12 col-md-4 mt-1">
        <label>Kamin vorhanden</label>
        @php $fireplace = old('fireplace', $alternative->fireplace); @endphp
        <select name="fireplace" class="form-control">
            <option value="Ja" {{ $fireplace == 'Ja' ? 'selected' : '' }}>Ja</option>
            <option value="Nein" {{ $fireplace == 'Nein' ? 'selected' : '' }}>Nein</option>
        </select>
    </div>

    <!-- Holzverbrauch -->
    <div class="col-12 col-md-4 mt-1">
        <label>Holzverbrauch</label>
        @php $wood = old('wood_consumption', $alternative->wood_consumption); @endphp
        <select name="wood_consumption" id="wood_consumption" class="form-control">
            <option value="" {{ $wood == '' ? 'selected' : '' }}></option>
            <option value="cubic" {{ $wood == 'cubic' ? 'selected' : '' }}>Raummeter</option>
            <option value="m3" {{ $wood == 'm3' ? 'selected' : '' }}>m³</option>
        </select>
    </div>

    <!-- Anzahl -->
    <div class="col-12 col-md-4 mt-1">
        <label>Anzahl</label>
        <input type="text" class="form-control" name="fireplace_value" id="fireplace_value" value="{{ old('fireplace_value', $alternative->fireplace_value) }}">
    </div>

    <!-- Heizenergie m³ -->
    <div class="col-12 col-md-4 mt-1">
        <label>Heizenergie (Gas/Öl)</label>
        <div class="position-relative">
            <input type="text" class="form-control" name="annual_heating_energy_consumption" id="annual_heating_energy_consumption" value="{{ old('annual_heating_energy_consumption', $alternative->annual_heating_energy_consumption) }}">
            <span class="position-absolute" style="right: 20px; top: 8px;">m³</span>
        </div>
    </div>

    <!-- Heizenergie kWh -->
    <div class="col-12 col-md-4 mt-1">
        <label>Heizenergie (Strom)</label>
        <div class="position-relative">
            <input type="text" name="annual_heating_energy_consumption_kwh" id="annual_heating_energy_consumption_kwh" class="form-control" value="{{ old('annual_heating_energy_consumption_kwh', $alternative->annual_heating_energy_consumption_kwh) }}">
            <span class="position-absolute" style="right: 20px; top: 8px;">kWh</span>
        </div>
    </div>

    <!-- Bemerkung -->
    <div class="col-12 mt-1">
        <label>Bemerkung</label>
        <textarea name="energy_remark" class="form-control" rows="2">{{ old('energy_remark', $alternative->energy_remark) }}</textarea>
    </div>
</div>
