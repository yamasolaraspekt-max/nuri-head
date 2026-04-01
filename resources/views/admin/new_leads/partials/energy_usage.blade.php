<div class="container">
    <div class="row">

        {{-- Haushaltsstrom --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Haushaltsstrom</label>
            <input type="text" class="form-control" name="power_household">
        </div>

        {{-- WP-Strom --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">WP-Strom</label>
            <input type="text" class="form-control" name="power_heatpump">
        </div>

        {{-- E-Auto-Strom --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">E-Auto-Strom</label>
            <input type="text" class="form-control" name="power_electric_car">
        </div>

        {{-- Sonstiges --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Sonstiges</label>
            <input type="text" class="form-control" name="power_other">
        </div>

        {{-- Gesamtverbrauch (readonly) --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;"><strong style="color:#3c98d6">Gesamtverbrauch</strong></label>
            <input type="text" class="form-control" name="power_total" id="power_total" readonly>
        </div>

        {{-- Zählerschrank --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Zählerschrank</label>
            <input type="text" class="form-control" name="meter_cabinet">
        </div>

        {{-- Anzahl Zähler --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Anzahl Zähler</label>
            <input type="text" class="form-control" name="meter_count">
        </div>

        {{-- Mieterstrommodell --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Mieterstrommodell</label>
            <select name="tenant_model" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="individuell">Individuell</option>
                <option value="zentral">Zentral</option>
                <option value="nicht-vorhanden">Nicht vorhanden</option>
            </select>
        </div>

        {{-- Aufstellungsort --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Aufstellungsort</label>
            <select name="installation_location_power" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="KG">Keller (KG)</option>
                <option value="EG">Erdgeschoss (EG)</option>
                <option value="OG">Obergeschoss (OG)</option>
                <option value="DG">Dachgeschoss (DG)</option>
                <option value="garage">Garage</option>
                <option value="sonstiges">Sonstiges</option>
            </select>
        </div>

        {{-- Netzwerk/WLAN --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Netzwerk / WLAN</label>
            <select name="network_wlan" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="vorhanden">Vorhanden</option>
                <option value="nicht-vorhanden">Nicht vorhanden</option>
                <option value="geplant">Geplant</option>
            </select>
        </div>

        {{-- Bemerkung --}}
        <div class="col-12 mt-3">
            <label>Bemerkung</label>
            <textarea name="energy_remark" class="form-control" rows="2"></textarea>
        </div>
    </div>
</div>
