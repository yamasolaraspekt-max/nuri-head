<div class="container">
    <div class="row">

        {{-- Elektroauto --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Elektroauto</label>
            <select class="form-control" name="electric_car" id="electric_car_select">
                <option disabled></option>
                <option value="Ja" @selected(optional($alternative)->electric_car == 'Ja')>Ja</option>
                <option value="Nein" @selected(optional($alternative)->electric_car == 'Nein')>Nein</option>
                <option value="Geplant" @selected(optional($alternative)->electric_car == 'Geplant')>Geplant</option>
            </select>
        </div>

        {{-- Anzahl E-Autos --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2" 
             id="electric_car_count_group" 
             style="@if(optional($alternative)->electric_car == 'Ja' || optional($alternative)->electric_car == 'Geplant') display:flex; @else display:none; @endif">
            <label class="me-2" style="width:160px;">Anzahl</label>
            <input type="number" step="any" class="form-control" name="electric_car_count" value="{{ old('electric_car_count', optional($alternative)->electric_car_count) }}">
        </div>

        {{-- Fahrleistung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center position-relative mb-2">
            <label class="me-2" style="width:160px;">Fahrleistung</label>
            <input type="number" step="any" class="form-control pe-5" name="car_kilo" value="{{ old('car_kilo', optional($alternative)->car_kilo) }}">
            <span style="position:absolute; right:30px;">km</span>
        </div>

        {{-- Wallboxen --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Wallboxen</label>
            <input type="number" step="any" class="form-control" name="wallbox_count" value="{{ old('wallbox_count', optional($alternative)->wallbox_count) }}">
        </div>

        {{-- Montageort --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Montageort</label>
            <select class="form-control" name="wallbox_location">
                <option value="none" @selected(optional($alternative)->wallbox_location == 'none')>Bitte auswählen</option>
                <option value="garage" @selected(optional($alternative)->wallbox_location == 'garage')>Garage</option>
                <option value="outside" @selected(optional($alternative)->wallbox_location == 'outside')>Draußen</option>
            </select>
        </div>

        {{-- Starkstromkabel --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Starkstromkabel</label>
            <select class="form-control" name="heavy_current_cable">
                <option value=""></option>
                <option value="vorhanden" @selected(optional($alternative)->heavy_current_cable == 'vorhanden')>vorhanden</option>
                <option value="nicht vorhanden" @selected(optional($alternative)->heavy_current_cable == 'nicht vorhanden')>nicht vorhanden</option>
            </select>
        </div>

        {{-- Netzwerkkabel --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Netzwerkkabel</label>
            <select class="form-control" name="network_cable">
                <option value=""></option>
                <option value="vorhanden" @selected(optional($alternative)->network_cable == 'vorhanden')>vorhanden</option>
                <option value="nicht vorhanden" @selected(optional($alternative)->network_cable == 'nicht vorhanden')>nicht vorhanden</option>
            </select>
        </div>

        {{-- Erdarbeiten --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Erdarbeiten</label>
            <select class="form-control" name="groundwork">
                <option value=""></option>
                <option value="bauseits" @selected(optional($alternative)->groundwork == 'bauseits')>bauseits</option>
                <option value="durch uns" @selected(optional($alternative)->groundwork == 'durch uns')>durch uns</option>
            </select>
        </div>

        {{-- Firmenfahrzeug --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Firmenfahrzeug</label>
            <select class="form-control" name="company_vehicle">
                <option value=""></option>
                <option value="1" @selected(optional($alternative)->company_vehicle == '1')>Ja</option>
                <option value="0" @selected(optional($alternative)->company_vehicle == '0')>Nein</option>
            </select>
        </div>

        {{-- Bidirektionales Auto --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width:160px;">Bidirektional</label>
            <select class="form-control" name="bidirectional_car">
                <option value="ja" @selected(optional($alternative)->bidirectional_car == 'ja')>ja</option>
                <option value="nein" @selected(optional($alternative)->bidirectional_car == 'nein')>nein</option>
            </select>
        </div>

        {{-- Bemerkung --}}
        <div class="col-12 mt-3">
            <label>Bemerkung</label>
            <textarea name="car_remark" class="form-control" rows="2">{{ old('car_remark', optional($alternative)->car_remark) }}</textarea>
        </div>

    </div>
</div>
