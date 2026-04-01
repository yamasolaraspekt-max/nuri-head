<form class="partial-form" data-section="e_mobility" data-id="{{ $customer->alternative->id }}">
    @csrf
    <div class="container">
        <div class="row">

            {{-- Elektroauto --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
                <label class="me-2" style="width:160px;">Elektroauto</label>
                <select class="form-control" name="electric_car" id="electric_car_select">
                    <option  @selected(optional($customer->alternative)->electric_car == '')>Bitte auswählen</option>
                    <option value="Ja" @selected(optional($customer->alternative)->electric_car == 'Ja')>Ja</option>
                    <option value="Nein" @selected(optional($customer->alternative)->electric_car == 'Nein')>Nein</option>
                    <option value="Geplant" @selected(optional($customer->alternative)->electric_car == 'Geplant')>Geplant</option>
                </select>
            </div>

            {{-- Anzahl (nur sichtbar wenn E-Auto Ja/Geplant) --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2" 
                 id="electric_car_count_group" 
                 style="@if(optional($customer->alternative)->electric_car == 'Ja' || optional($customer->alternative)->electric_car == 'Geplant') display:flex; @else display:none; @endif">
                <label class="me-2" style="width:160px;">Anzahl</label>
                <input type="number" step="any" class="form-control" name="electric_car_count" value="{{ old('electric_car_count', optional($customer->alternative)->electric_car_count) }}">
            </div>

            {{-- Fahrleistung --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center position-relative mb-2">
                <label class="me-2" style="width:160px;">Fahrleistung</label>
                <input type="number" step="any" class="form-control pe-5" name="car_kilo" value="{{ old('car_kilo', optional($customer->alternative)->car_kilo) }}">
                <span style="position:absolute; right:30px;">km</span>
            </div>

            {{-- Anzahl Wallboxen --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
                <label class="me-2" style="width:160px;">Wallboxen</label>
                <input type="number" step="any" class="form-control" name="wallbox_count" value="{{ old('wallbox_count', optional($customer->alternative)->wallbox_count) }}">
            </div>

            {{-- Montageort --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
                <label class="me-2" style="width:160px;">Montageort</label>
                <select class="form-control" name="wallbox_location">
                    <option value="none" @selected(optional($customer->alternative)->wallbox_location == 'none')>Bitte auswählen</option>
                    <option value="garage" @selected(optional($customer->alternative)->wallbox_location == 'garage')>Garage</option>
                    <option value="outside" @selected(optional($customer->alternative)->wallbox_location == 'outside')>Draußen</option>
                </select>
            </div>

            {{-- Starkstromkabel --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
                <label class="me-2" style="width:160px;">Starkstrom</label>
                <select class="form-control" name="heavy_current_cable">
                    <option value=""></option>
                    <option value="vorhanden" @selected(optional($customer->alternative)->heavy_current_cable == 'vorhanden')>vorhanden</option>
                    <option value="nicht vorhanden" @selected(optional($customer->alternative)->heavy_current_cable == 'nicht vorhanden')>nicht vorhanden</option>
                </select>
            </div>

            {{-- Netzwerkkabel --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
                <label class="me-2" style="width:160px;">Netzwerk</label>
                <select class="form-control" name="network_cable">
                    <option value="" @selected(optional($customer->alternative)->network_cable == '')>Bitte auswählen</option>
                    <option value="vorhanden" @selected(optional($customer->alternative)->network_cable == 'vorhanden')>vorhanden</option>
                    <option value="nicht vorhanden" @selected(optional($customer->alternative)->network_cable == 'nicht vorhanden')>nicht vorhanden</option>
                </select>
            </div>

            {{-- Erdarbeiten --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
                <label class="me-2" style="width:160px;">Erdarbeiten</label>
                <select class="form-control" name="groundwork">
                    <option value="" @selected(optional($customer->alternative)->groundwork == '')>Bitte auswählen</option>
                    <option value="bauseits" @selected(optional($customer->alternative)->groundwork == 'bauseits')>bauseits</option>
                    <option value="durch uns" @selected(optional($customer->alternative)->groundwork == 'durch uns')>durch uns</option>
                </select>
            </div>

            {{-- Firmenfahrzeug --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
                <label class="me-2" style="width:160px;">Firmenfahrzeug</label>
                <select class="form-control" name="company_vehicle">
                    <option value="" @selected(optional($customer->alternative)->company_vehicle == '')>Bitte auswählen</option>
                    <option value="1" @selected(optional($customer->alternative)->company_vehicle == '1')>Ja</option>
                    <option value="0" @selected(optional($customer->alternative)->company_vehicle == '0')>Nein</option>
                </select>
            </div>

            {{-- Bidirektional --}}
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
                <label class="me-2" style="width:160px;">Bidirektional</label>
                <select class="form-control" name="bidirectional_car">
                    <option value="" @selected(optional($customer->alternative)->bidirectional_car == '')>Bitte auswählen</option>
                    <option value="ja" @selected(optional($customer->alternative)->bidirectional_car == 'ja')>ja</option>
                    <option value="nein" @selected(optional($customer->alternative)->bidirectional_car == 'nein')>nein</option>
                </select>
            </div>

            {{-- Bemerkung --}}
            <div class="col-12 mt-3">
                <label>Bemerkung</label>
                <textarea name="car_remark" class="form-control" rows="2">{{ old('car_remark', optional($customer->alternative)->car_remark) }}</textarea>
            </div>

        </div>

        <div class="mt-3 text-end">
            <button type="submit" class="btn btn-success save-partial-form" data-section="e_mobility" >Speichern</button>
        </div>
    </div>
</form>
