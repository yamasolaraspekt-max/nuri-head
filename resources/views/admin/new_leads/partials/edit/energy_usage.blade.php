<div class="container">
    <div class="row">

        {{-- Haushaltsstrom --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Haushaltsstrom</label>
            <input type="text" class="form-control" name="power_household" value="{{ old('power_household', optional($alternative)->power_household) }}">
        </div>

        {{-- WP-Strom --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">WP-Strom</label>
            <input type="text" class="form-control" name="power_heatpump" value="{{ old('power_heatpump', optional($alternative)->power_heatpump) }}">
        </div>

        {{-- E-Auto-Strom --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">E-Auto-Strom</label>
            <input type="text" class="form-control" name="power_electric_car" value="{{ old('power_electric_car', optional($alternative)->power_electric_car) }}">
        </div>

        {{-- Sonstiges --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Sonstiges</label>
            <input type="text" class="form-control" name="power_other" value="{{ old('power_other', optional($alternative)->power_other) }}">
        </div>

        {{-- Gesamtverbrauch (readonly) --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;"><strong style="color:#3c98d6;">Gesamtverbrauch</strong></label>
            <input type="text" class="form-control" id="power_total" name="power_total" value="{{ old('power_total', optional($alternative)->power_total) }}" readonly>
        </div>

        {{-- Zählerschrank --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Zählerschrank</label>
            <input type="text" class="form-control" name="meter_cabinet" value="{{ old('meter_cabinet', optional($alternative)->meter_cabinet) }}">
        </div>

        {{-- Anzahl Zähler --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Anzahl Zähler</label>
            <input type="text" class="form-control" name="meter_count" value="{{ old('meter_count', optional($alternative)->meter_count) }}">
        </div>

        {{-- Mieterstrommodell --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Mieterstrommodell</label>
            <select name="tenant_model" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="individuell" @if(optional($alternative)->tenant_model == "individuell") selected @endif>Individuell</option>
                <option value="zentral" @if(optional($alternative)->tenant_model == "zentral") selected @endif>Zentral</option>
                <option value="nicht-vorhanden" @if(optional($alternative)->tenant_model == "nicht-vorhanden") selected @endif>Nicht vorhanden</option>
            </select>
        </div>

        {{-- Aufstellungsort --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Aufstellungsort</label>
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

        {{-- Netzwerk / WLAN --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Netzwerk / WLAN</label>
            <select name="network_wlan" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="vorhanden" @if(optional($alternative)->network_wlan == "vorhanden") selected @endif>Vorhanden</option>
                <option value="nicht-vorhanden" @if(optional($alternative)->network_wlan == "nicht-vorhanden") selected @endif>Nicht vorhanden</option>
                <option value="geplant" @if(optional($alternative)->network_wlan == "geplant") selected @endif>Geplant</option>
            </select>
        </div>

        {{-- Bemerkung --}}
        <div class="col-12 mt-3">
            <label>Bemerkung</label>
            <textarea name="energy_remark" class="form-control" rows="2">{{ old('energy_remark', optional($alternative)->energy_remark) }}</textarea>
        </div>
    </div>
</div>
