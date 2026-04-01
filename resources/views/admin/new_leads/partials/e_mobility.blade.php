<div class="container">
    <div class="row">

        {{-- Elektroauto --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Elektroauto</label>
            <select class="form-control" name="electric_car" id="electric_car_select">
                <option disabled selected></option>
                <option value="Ja">Ja</option>
                <option value="Nein">Nein</option>
                <option value="Geplant">Geplant</option>
            </select>
        </div>

        {{-- Anzahl E-Auto --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2" id="electric_car_count_group" style="display: none;">
            <label class="me-2" style="width: 160px;">Anzahl</label>
            <input type="number" step="any" class="form-control" name="electric_car_count">
        </div>

        {{-- Fahrleistung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2 position-relative">
            <label class="me-2" style="width: 160px;">Fahrleistung</label>
            <input type="number" step="any" class="form-control pe-5" name="car_kilo">
            <span style="position: absolute; right: 30px;">km</span>
        </div>

        {{-- Wallboxen --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Wallboxen</label>
            <input type="number" step="any" class="form-control" name="wallbox_count">
        </div>

        {{-- Montageort --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Montageort</label>
            <select class="form-control" name="wallbox_location">
                <option value="none">Bitte auswählen</option>
                <option value="garage">Garage</option>
                <option value="outside">Draußen</option>
            </select>
        </div>

        {{-- Starkstromkabel --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Starkstromkabel</label>
            <select class="form-control" name="heavy_current_cable">
                <option value=""></option>
                <option value="vorhanden">vorhanden</option>
                <option value="nicht vorhanden">nicht vorhanden</option>
            </select>
        </div>

        {{-- Netzwerkkabel --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Netzwerkkabel</label>
            <select class="form-control" name="network_cable">
                <option value=""></option>
                <option value="vorhanden">vorhanden</option>
                <option value="nicht vorhanden">nicht vorhanden</option>
            </select>
        </div>

        {{-- Erdarbeiten --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Erdarbeiten</label>
            <select class="form-control" name="groundwork">
                <option value="">-</option>
                <option value="bauseits">bauseits</option>
                <option value="durch uns">durch uns</option>
            </select>
        </div>

        {{-- Firmenfahrzeug --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Firmenfahrzeug</label>
            <select class="form-control" name="company_vehicle">
                <option value="">-</option>
                <option value="1">Ja</option>
                <option value="0">Nein</option>
            </select>
        </div>

        {{-- Bidirektionales Auto --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 160px;">Bidirektional</label>
            <select class="form-control" name="bidirectional_car">
                <option value="ja">ja</option>
                <option value="nein">nein</option>
            </select>
        </div>

        {{-- Bemerkung --}}
        <div class="col-12 mt-3">
            <label>Bemerkung</label>
            <textarea name="car_remark" class="form-control" rows="2"></textarea>
        </div>

    </div>
</div>
