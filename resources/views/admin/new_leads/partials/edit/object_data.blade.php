<div class="container">
    <div class="row">
        {{-- Objektart --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Objektart</label>
            <select name="objective" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="EFH" @selected(optional($alternative)->objective == "EFH")>EFH</option>
                <option value="MFH" @selected(optional($alternative)->objective == "MFH")>MFH</option>
                <option value="Gewerbe" @selected(optional($alternative)->objective == "Gewerbe")>Gewerbe</option>
                <option value="Sonstiges" @selected(optional($alternative)->objective == "Sonstiges")>Sonstiges</option>
            </select>
        </div>

        {{-- Zustand --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Zustand</label>
            <select name="building_condition" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="Neubau" @selected(optional($alternative)->building_condition == "Neubau")>Neubau</option>
                <option value="Bestand" @selected(optional($alternative)->building_condition == "Bestand")>Bestand</option>
                <option value="Sanierung" @selected(optional($alternative)->building_condition == "Sanierung")>Sanierung</option>
                <option value="Sanierungsbedürftig" @selected(optional($alternative)->building_condition == "Sanierungsbedürftig")>Sanierungsbedürftig</option>
            </select>
        </div>

        {{-- Nutzungsart --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Nutzungsart</label>
            <select name="usage_type" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="Eigennutzung" @selected(optional($alternative)->usage_type == "Eigennutzung")>Eigennutzung</option>
                <option value="Vermietung" @selected(optional($alternative)->usage_type == "Vermietung")>Vermietung</option>
                <option value="Beides" @selected(optional($alternative)->usage_type == "Beides")>Beides</option>
            </select>
        </div>

        {{-- Eigentümer --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Eigentümer</label>
            <input type="text" name="owner_count" class="form-control" value="{{ old('owner_count', optional($alternative)->owner_count) }}">
        </div>

        {{-- Wohneinheiten --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Wohneinheiten</label>
            <input type="text" name="number_we" class="form-control" value="{{ old('number_we', optional($alternative)->number_we) }}">
        </div>

        {{-- Personenanzahl --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Personen</label>
            <input type="text" name="number_people" class="form-control" value="{{ old('number_people', optional($alternative)->number_people) }}">
        </div>

        {{-- Baujahr --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Baujahr</label>
            <input type="text" name="house_year" class="form-control" value="{{ old('house_year', optional($alternative)->house_year) }}">
        </div>

        {{-- Geschosse --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Geschosse</label>
            <input type="text" name="number_stories" class="form-control" value="{{ old('number_stories', optional($alternative)->number_stories) }}">
        </div>

        {{-- Wohnfläche --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Wohnfläche</label>
            <div class="input-group">
                <input type="text" name="living_space" class="form-control" value="{{ old('living_space', optional($alternative)->living_space) }}">
                <span class="input-group-text">m²</span>
            </div>
        </div>

        {{-- Dämmung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Dämmung</label>
            <input type="text" name="external_insulation_thickness" class="form-control" value="{{ old('external_insulation_thickness', optional($alternative)->external_insulation_thickness) }}">
        </div>

        {{-- Mauerwerk --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Mauerwerk</label>
            <input type="text" name="masonry" class="form-control" value="{{ old('masonry', optional($alternative)->masonry) }}">
        </div>

        {{-- Fensterverglasung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Verglasung</label>
            <select name="window_glazing" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="1-fach" @selected(optional($alternative)->window_glazing == "1-fach")>1 Fach</option>
                <option value="2-fach" @selected(optional($alternative)->window_glazing == "2-fach")>2 Fach</option>
                <option value="3-fach" @selected(optional($alternative)->window_glazing == "3-fach")>3 Fach</option>
            </select>
        </div>

        {{-- Fensterrahmen --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Rahmen</label>
            <select name="window_frame" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="Alu" @selected(optional($alternative)->window_frame == "Alu")>Alu</option>
                <option value="Kunststoff" @selected(optional($alternative)->window_frame == "Kunststoff")>Kunststoff</option>
                <option value="Sonstiges" @selected(optional($alternative)->window_frame == "Sonstiges")>Sonstiges</option>
            </select>
        </div>

        {{-- Fenster Bj --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Fenster Bj.</label>
            <input type="text" name="window_year" class="form-control" value="{{ old('window_year', optional($alternative)->window_year) }}">
        </div>

        {{-- Tür Bj --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Tür Bj.</label>
            <input type="text" name="door_year" class="form-control" value="{{ old('door_year', optional($alternative)->door_year) }}">
        </div>

        {{-- Tür Material --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Tür Material</label>
            <select name="door_condition" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="Alu" @selected(optional($alternative)->door_condition == "Alu")>Alu</option>
                <option value="Kunststoff" @selected(optional($alternative)->door_condition == "Kunststoff")>Kunststoff</option>
                <option value="Sonstiges" @selected(optional($alternative)->door_condition == "Sonstiges")>Sonstiges</option>
            </select>
        </div>

        {{-- Bemerkung --}}
        <div class="col-12 mt-3">
            <label>Bemerkung</label>
            <textarea name="object_remark" class="form-control" rows="3">{{ old('object_remark', optional($alternative)->object_remark) }}</textarea>
        </div>
    </div>
</div>
