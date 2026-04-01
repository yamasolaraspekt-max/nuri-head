<form class="partial-form" data-section="energy_usage" data-id="{{ $alternative->id }}">
    @csrf
<div class="row">

    {{-- Energy types --}}
    <div class="col-md-3 mt-1">
        <label>Haushaltsstrom</label>
        <input type="text" class="form-control" id="power_household_input" name="power_household" value="{{ old('power_household', optional($alternative)->power_household) }}">
    </div>

    <div class="col-md-3 mt-1">
        <label>WP-Strom</label>
        <input type="text" class="form-control" id="power_heatpump_input" name="power_heatpump" value="{{ old('power_heatpump', optional($alternative)->power_heatpump) }}">
    </div>

    <div class="col-md-3 mt-1">
        <label>E-Auto-Strom</label>
        <input type="text" class="form-control"  id="power_electric_car_input" name="power_electric_car" value="{{ old('power_electric_car', optional($alternative)->power_electric_car) }}">
    </div>

    <div class="col-md-3 mt-1">
        <label>sonstiges</label>
        <input type="text" class="form-control" id="power_other_input" name="power_other" value="{{ old('power_other', optional($alternative)->power_other) }}">
    </div>

    {{-- Total --}}
    <div class="col-md-3 mt-1">
        <label><strong style="color:#3c98d6">Gesamtverbrauch</strong></label>
        <input type="hidden" name="power_total" id="power_total_hidden">
        <input type="text" class="form-control" id="power_total" value="{{ old('power_total', optional($alternative)->power_total) }}" readonly>
        <small id="power_total_year" class="form-text text-muted"></small>
    </div>

    {{-- Zählerdaten --}}
    <div class="col-md-3 mt-1">
        <label>Zählerschrank</label>
        <input type="text" class="form-control" name="meter_cabinet" value="{{ old('meter_cabinet', optional($alternative)->meter_cabinet) }}">
    </div>

    <div class="col-md-3 mt-1">
        <label>Anzahl Zähler</label>
        <input type="text" class="form-control" name="meter_count" value="{{ old('meter_count', optional($alternative)->meter_count) }}">
    </div>

    <div class="col-md-3 mt-1">
        <label>Wohnheinheiten</label>
        <input type="text" class="form-control" name="number_we" id="number_we" value="{{ old('number_we', optional($alternative)->number_we) }}">
    </div>

    <div class="col-md-3 mt-1">
        <label>Mieterstrommodell</label>
        <select name="tenant_model" class="form-control">
            <option value="">Bitte wählen</option>
            <option value="individuell" @if(optional($alternative)->tenant_model == "individuell") selected @endif>Individuell</option>
            <option value="zentral" @if(optional($alternative)->tenant_model == "zentral") selected @endif>Zentral</option>
            <option value="nicht-vorhanden" @if(optional($alternative)->tenant_model == "nicht-vorhanden") selected @endif>Nicht vorhanden</option>
        </select>
    </div>

    <div class="col-md-3 mt-1">
        <label>Aufstellungsort</label>
        <select name="installation_location_power" class="form-control">
            <option value="">Bitte wählen</option>
            <option value="KG" @if(optional($alternative)->installation_location_power == "KG") selected @endif>Keller (KG)</option>
            <option value="EG" @if(optional($alternative)->installation_location_power == "EG") selected @endif>Erdgeschoss (EG)</option>
            <option value="OG" @if(optional($alternative)->installation_location_power == "OG") selected @endif>Obergeschoss (OG)</option>
            <option value="DG" @if(optional($alternative)->installation_location_power == "DG") selected @endif>Dachgeschoss (DG)</option>
            <option value="garage" @if(optional($alternative)->installation_location_power == "garage") selected @endif>Garage</option>
            <option value="sonstiges" @if(optional($alternative)->installation_location_power == "sonstiges") selected @endif>Sonstiges</option>
        </select>
    </div>

    <div class="col-md-3 mt-1">
        <label>Netzwerk / WLAN</label>
        <select name="network_wlan" class="form-control">
            <option value="">Bitte wählen</option>
            <option value="vorhanden" @if(optional($alternative)->network_wlan == "vorhanden") selected @endif>Vorhanden</option>
            <option value="nicht-vorhanden" @if(optional($alternative)->network_wlan == "nicht-vorhanden") selected @endif>Nicht vorhanden</option>
            <option value="geplant" @if(optional($alternative)->network_wlan == "geplant") selected @endif>Geplant</option>
        </select>
    </div>

    {{-- Remark --}}
    <div class="col-md-12 mt-2">
        <label>Bemerkung</label>
        <textarea name="energy_remark" class="form-control" rows="2">{{ old('energy_remark', optional($alternative)->energy_remark) }}</textarea>
    </div>

</div>
<div class="mt-3 text-right">
        <button type="submit" class="btn btn-success">Speichern</button>
    </div>

</form>
