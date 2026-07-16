<form class="partial-form" data-section="object_data" data-id="{{ $customer->alternative->id }}">
    @csrf

    <div class="row">
        {{-- Objektart --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Objektart</label>
            <select name="object_type" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="EFH" @selected(optional($customer->alternative)->object_type == "EFH")>EFH</option>
                <option value="MFH" @selected(optional($customer->alternative)->object_type == "MFH")>MFH</option>
                <option value="Gewerbe" @selected(optional($customer->alternative)->object_type == "Gewerbe")>Gewerbe</option>
                <option value="Sonstiges" @selected(optional($customer->alternative)->object_type == "Sonstiges")>Sonstiges</option>
            </select>
        </div>

        {{-- Zustand --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Zustand</label>
            <select name="building_condition" class="form-control">
                <option value="" @selected(optional($customer->alternative)->building_condition == "")>Bitte wählen</option>
                <option value="Neubau" @selected(optional($customer->alternative)->building_condition == "Neubau")>Neubau</option>
                <option value="Sanierung" @selected(optional($customer->alternative)->building_condition == "Sanierung")>Sanierung</option>
            </select>
        </div>

        {{-- Nutzungsart --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Nutzungsart</label>
            <select name="usage_type" class="form-control">
                <option value="" @selected(optional($customer->alternative)->usage_type == "")>Bitte wählen</option>
                <option value="Eigennutzung" @selected(optional($customer->alternative)->usage_type == "Eigennutzung")>Eigennutzung</option>
                <option value="Vermietung" @selected(optional($customer->alternative)->usage_type == "Vermietung")>Vermietung</option>
                <option value="Beides" @selected(optional($customer->alternative)->usage_type == "Beides")>Beides</option>
            </select>
        </div>

        {{-- Eigentümer --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Eigentümer</label>
            <input type="text" name="owner_count" class="form-control" value="{{ old('owner_count', optional($customer->alternative)->owner_count) }}">
        </div>

        {{-- Wohneinheiten --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Wohneinheiten</label>
            <input type="number" name="number_we" class="form-control" value="{{ old('number_we', optional($customer->alternative)->number_we) }}">
        </div>

        {{-- Personenanzahl --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Personen</label>
            <input type="number" name="person_count" class="form-control" value="{{ old('person_count', optional($customer->alternative)->person_count) }}">
        </div>

        {{-- Baujahr --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Baujahr</label>
            <input type="number" name="building_year" class="form-control" value="{{ old('building_year', optional($customer->alternative)->building_year) }}">
        </div>

        {{-- Geschosse --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Geschosse</label>
            <input type="number" name="story_count" class="form-control" value="{{ old('story_count', optional($customer->alternative)->story_count) }}">
        </div>

        {{-- Wohnfläche --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Wohnfläche</label>
            <div class="input-group">
                <input type="text" name="heated_area" class="form-control" value="{{ old('heated_area', optional($customer->alternative)->heated_area) }}">
                <span class="input-group-text">m²</span>
            </div>
        </div>

        {{-- Dämmung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Dämmung</label>
            <input type="text" name="external_insulation_thickness" class="form-control" value="{{ old('external_insulation_thickness', optional($customer->alternative)->external_insulation_thickness) }}">
        </div>

        {{-- Mauerwerk --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Mauerwerk</label>
            <input type="text" name="masonry" class="form-control" value="{{ old('masonry', optional($customer->alternative)->masonry) }}">
        </div>

        {{-- Fensterverglasung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Verglasung</label>
            <select name="window_glazing" class="form-control">
                <option value="" @selected(optional($customer->alternative)->window_glazing == "")>Bitte wählen</option> 
                <option value="1-fach" @selected(optional($customer->alternative)->window_glazing == "1-fach")>1 Fach</option>
                <option value="2-fach" @selected(optional($customer->alternative)->window_glazing == "2-fach")>2 Fach</option>
                <option value="3-fach" @selected(optional($customer->alternative)->window_glazing == "3-fach")>3 Fach</option>
            </select>
        </div>

        {{-- Fensterrahmen --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Rahmen</label>
            <select name="window_frame" class="form-control">
                <option value="" @selected(optional($customer->alternative)->window_frame == "")>Bitte wählen</option> 
                <option value="Alu" @selected(optional($customer->alternative)->window_frame == "Alu")>Alu</option>
                <option value="Kunststoff" @selected(optional($customer->alternative)->window_frame == "Kunststoff")>Kunststoff</option>
                <option value="Sonstiges" @selected(optional($customer->alternative)->window_frame == "Sonstiges")>Sonstiges</option>
            </select>
        </div>

        {{-- Fenster Baujahr --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Fenster Bj.</label>
            <input type="text" name="window_year" class="form-control" value="{{ old('window_year', optional($customer->alternative)->window_year) }}">
        </div>

        {{-- Haustür Baujahr --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Tür Bj.</label>
            <input type="text" name="door_year" class="form-control" value="{{ old('door_year', optional($customer->alternative)->door_year) }}">
        </div>

        {{-- Tür Zustand --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Tür Zustand</label>
            <select name="door_condition" class="form-control">
                <option value=""  @selected(optional($customer->alternative)->door_condition == "")>Bitte wählen</option> 
                <option value="Alu" @selected(optional($customer->alternative)->door_condition == "Alu")>Alu</option>
                <option value="Kunststoff" @selected(optional($customer->alternative)->door_condition == "Kunststoff")>Kunststoff</option>
                <option value="Sonstiges" @selected(optional($customer->alternative)->door_condition == "Sonstiges")>Sonstiges</option>
            </select>
        </div>

        {{-- Bemerkung --}}
        <div class="col-12 mt-3">
            <label>Bemerkung</label>
            <textarea name="object_remark" class="form-control" rows="2">{{ old('object_remark', optional($customer->alternative)->object_remark) }}</textarea>
        </div>
    </div>

    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-success save-partial-form"  data-section="object_data"  >Speichern</button>
    </div>
</form>
