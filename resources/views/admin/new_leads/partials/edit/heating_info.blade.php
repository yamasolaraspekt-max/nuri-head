<div class="container">
    <div class="row">
        {{-- Heiztechnik --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heiztechnik</label>
            <select class="form-control" name="heating_system_type">
                <option value=""></option>
                <option value="Gas" @selected(optional($alternative)->heating_system_type == 'Gas')>Gas</option>
                <option value="Öl" @selected(optional($alternative)->heating_system_type == 'Öl')>Öl</option>
                <option value="Wärmepumpe" @selected(optional($alternative)->heating_system_type == 'Wärmepumpe')>Wärmepumpe</option>
                <option value="Nachtspeicher" @selected(optional($alternative)->heating_system_type == 'Nachtspeicher')>Nachtspeicher</option>
            </select>
        </div>

        {{-- Kamin --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Kamin</label>
            <select class="form-control" name="fireplace">
                <option value="">Bitte wählen</option>
                <option value="Ja" @selected(optional($alternative)->fireplace == 'Ja')>Ja</option>
                <option value="Nein" @selected(optional($alternative)->fireplace == 'Nein') >Nein</option>
            </select>
         </div>

        {{-- Heizkreise --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heizkreise</label>
            <input type="number" step="any" class="form-control" name="heating_circuits_count" value="{{ old('heating_circuits_count', optional($alternative)->heating_circuits_count) }}">
        </div>

        {{-- Heizsystem --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heizsystem</label>
            <select name="heating_type" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="underfloor_heating" @selected(optional($alternative)->heating_type == 'underfloor_heating')>Fußbodenheizung</option>
                <option value="radiators" @selected(optional($alternative)->heating_type == 'radiators')>Heizkörper</option>
                <option value="both" @selected(optional($alternative)->heating_type == 'both')>Beides</option>
                <option value="none" @selected(optional($alternative)->heating_type == 'none')>Keine</option>
            </select>
        </div>

        {{-- Holzverbrauch --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Holzverbrauch</label>
            <input type="number" step="any" class="form-control" name="wood_consumption" value="{{ old('wood_consumption', optional($alternative)->wood_consumption) }}">
        </div>

        {{-- Rohrsystem Anzahl --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Rohranzahl</label>
            <input type="number" step="any" class="form-control" name="pipe_system_count" value="{{ old('pipe_system_count', optional($alternative)->pipe_system_count) }}">
        </div>

        {{-- Heizungsalter --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heizung Alter</label>
            <input type="number" step="any" class="form-control" name="heating_system_age" value="{{ old('heating_system_age', optional($alternative)->heating_system_age) }}">
        </div>

        {{-- Menge --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Menge</label>
            <input type="number" step="any" class="form-control" name="quantity" value="{{ old('quantity', optional($alternative)->quantity) }}">
        </div>

        {{-- Rohrmaterial --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Rohrmaterial</label>
            <input type="text" class="form-control" name="pipe_system_material" value="{{ old('pipe_system_material', optional($alternative)->pipe_system_material) }}">
        </div>

        {{-- Verbrauch --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Verbrauch</label>
            <input type="number" step="any" class="form-control" name="consumption" value="{{ old('consumption', optional($alternative)->consumption) }}">
        </div>

        {{-- Solarthermie --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Solarthermie</label>
            <select name="solar_thermal" class="form-control">
                <option value=""></option>
                <option value="Ja" @selected(optional($alternative)->solar_thermal == 'Ja')>Ja</option>
                <option value="Nein" @selected(optional($alternative)->solar_thermal == 'Nein')>Nein</option>
            </select>
        </div>

        {{-- Badezimmer --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Bäderanzahl</label>
            <input type="number" step="any" class="form-control" name="bathroom_count" value="{{ old('bathroom_count', optional($alternative)->bathroom_count) }}">
        </div>

        {{-- Aufstellungsort --}}
        <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
            <label class="me-2">Aufstellungsort</label>
            <select name="installation_location" class="form-control mb-1">
                <option value="">Bitte wählen</option>
                <option value="KG" @selected(optional($alternative)->installation_location == 'KG')>KG</option>
                <option value="EG" @selected(optional($alternative)->installation_location == 'EG')>EG</option>
                <option value="OG" @selected(optional($alternative)->installation_location == 'OG')>OG</option>
                <option value="DG" @selected(optional($alternative)->installation_location == 'DG')>DG</option>
                <option value="SONSTIGES" @selected(optional($alternative)->installation_location == 'SONSTIGES')>Sonstiges</option>
            </select>
            <input type="text" class="form-control" placeholder="Sonstiges" name="installation_location_extra" value="{{ old('installation_location_extra', optional($alternative)->installation_location_extra) }}">
        </div>

        {{-- Warmwasser --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Warmwasser</label>
            <select name="hot_water_generation" class="form-control">
                <option value=""></option>
                <option value="zentral" @selected(optional($alternative)->hot_water_generation == 'zentral')>Zentral</option>
                <option value="dezentral" @selected(optional($alternative)->hot_water_generation == 'dezentral')>Dezentral</option>
            </select>
        </div>

        {{-- Badewannen --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Badewannen</label>
            <input type="number" step="any" class="form-control" name="bathtub_count" value="{{ old('bathtub_count', optional($alternative)->bathtub_count) }}">
        </div>

        {{-- Einkommensstufe --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Einkommen</label>
            <select name="income_level" class="form-control">
                <option value="0" @selected(optional($alternative)->income_level == '0')>Über 40000</option>
                <option value="1" @selected(optional($alternative)->income_level == '1')>Unter 40000</option>
                <option value="2" @selected(optional($alternative)->income_level == '2')>Unbekannt</option>
            </select>
        </div>

        {{-- Gesamtverbrauch Wärme --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Wärmeverbrauch</label>
            <input type="number" step="any" class="form-control" name="total_heat_consumption" value="{{ old('total_heat_consumption', optional($alternative)->total_heat_consumption) }}">
        </div>

        {{-- Gesamtverbrauch Strom --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Stromverbrauch</label>
            <input type="number" step="any" class="form-control" name="total_electricity_consumption" value="{{ old('total_electricity_consumption', optional($alternative)->total_electricity_consumption) }}">
        </div>

        {{-- Heizlastberechnung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heizlast</label>
            <input type="number" step="any" class="form-control" name="heating_load_calculation" value="{{ old('heating_load_calculation', optional($alternative)->heating_load_calculation) }}">
        </div>

        {{-- Bemerkung --}}
        <div class="col-12 mt-3">
            <label>Bemerkung</label>
            <textarea name="energy_remark" class="form-control" rows="3">{{ old('energy_remark', optional($alternative)->energy_remark) }}</textarea>
        </div>
    </div>
</div>
