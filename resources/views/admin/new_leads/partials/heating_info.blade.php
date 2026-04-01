<div class="container">
    <div class="row">
        {{-- Heiztechnik --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heiztechnik</label>
            <select class="form-control" name="heating_system_type">
                <option value="">Bitte wählen</option>
                <option value="Gas">Gas</option>
                <option value="Öl">Öl</option>
                <option value="Wärmepumpe">Wärmepumpe</option>
                <option value="Nachtspeicher">Nachtspeicher</option>
            </select>
        </div>

        {{-- Kamin --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Kamin</label>
            <select class="form-control" name="fireplace">
                <option value="">Bitte wählen</option>
                <option value="Ja">Ja</option>
                <option value="Nein">Nein</option>
            </select>
        </div>

        {{-- Heizkreise --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heizkreise</label>
            <input type="number" step="any"  class="form-control" name="heating_circuits_count">
        </div>

        {{-- Heizsystem --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heizsystem</label>
            <select name="heating_type" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="underfloor_heating">Fußbodenheizung</option>
                <option value="radiators">Heizkörper</option>
                <option value="both">Beides</option>
                <option value="none">Keine</option>
            </select>
        </div>

        {{-- Holzverbrauch --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Holzverbrauch</label>
            <input type="number" step="any"  class="form-control" name="wood_consumption">
        </div>

        {{-- Rohrsystem Anzahl --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Rohranzahl</label>
            <input type="number" step="any"  class="form-control" name="pipe_system_count">
        </div>

        {{-- Heizungsalter --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heizung Alter</label>
            <input type="number" step="any"  class="form-control" name="heating_system_age">
        </div>

        {{-- Menge --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Menge</label>
            <input type="number" step="any"  class="form-control" name="quantity">
        </div>

        {{-- Rohrmaterial --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Rohrmaterial</label>
            <input type="text" class="form-control" name="pipe_system_material">
        </div>

        {{-- Verbrauch --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Verbrauch</label>
            <input type="number" step="any"  class="form-control" name="consumption">
        </div>

        {{-- Solarthermie --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Solarthermie</label>
            <select name="solar_thermal" class="form-control">
                <option value=""></option>
                <option value="Ja">Ja</option>
                <option value="Nein">Nein</option>
            </select>
        </div>

        {{-- Badezimmer --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Bäderanzahl</label>
            <input type="number" step="any"  class="form-control" name="bathroom_count">
        </div>

        {{-- Aufstellungsort --}}
        <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
            <label class="me-2">Aufstellungsort</label>
            <select name="installation_location" class="form-control mb-1">
                <option value="">Bitte wählen</option>
                <option value="KG">KG</option>
                <option value="EG">EG</option>
                <option value="OG">OG</option>
                <option value="DG">DG</option>
                <option value="SONSTIGES">Sonstiges</option>
            </select>
            <input type="text" class="form-control" placeholder="Sonstiges" name="installation_location_extra">
        </div>

        {{-- Warmwasser --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Warmwasser</label>
            <select name="hot_water_generation" class="form-control">
                <option value=""></option>
                <option value="zentral">Zentral</option>
                <option value="dezentral">Dezentral</option>
            </select>
        </div>

        {{-- Badewannen --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Badewannen</label>
            <input type="number" step="any"  class="form-control" name="bathtub_count">
        </div>

        {{-- Einkommensstufe --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Einkommen</label>
            <select name="income_level" class="form-control">
                <option value="0">Über 40000</option>
                <option value="1">Unter 40000</option>
                <option value="2">Unbekannt</option>
            </select>
        </div>

        {{-- Gesamtverbrauch Wärme --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Wärmeverbrauch</label>
            <input type="number" step="any"  class="form-control" name="total_heat_consumption">
        </div>

        {{-- Gesamtverbrauch Strom --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Stromverbrauch</label>
            <input type="number" step="any"  class="form-control" name="total_electricity_consumption">
        </div>

        {{-- Heizlastberechnung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Heizlast</label>
            <input type="number" step="any"  class="form-control" name="heating_load_calculation">
        </div>

        {{-- Bemerkung --}}
        <div class="col-12 mt-3">
            <label>Bemerkung</label>
            <textarea name="heating_remark" class="form-control" rows="3"></textarea>
        </div>
    </div>
</div>
