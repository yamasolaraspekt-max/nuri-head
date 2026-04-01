<div class="container">
    <div class="row">
        {{-- Objektart --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Objektart</label>
            <select name="objective" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="EFH">EFH</option>
                <option value="MFH">MFH</option>
                <option value="Gewerbe">Gewerbe</option>
                <option value="Sonstiges">Sonstiges</option>
            </select>
        </div>

        {{-- Zustand --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Zustand</label>
            <select name="building_condition" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="Neubau">Neubau</option>
                <option value="Bestand">Bestand</option>
                <option value="Sanierung">Sanierung</option>
                <option value="Sanierungsbedürftig">Sanierungsbedürftig</option>
            </select>
        </div>

        {{-- Nutzungsart --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Nutzungsart</label>
            <select name="usage_type" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="Eigennutzung">Eigennutzung</option>
                <option value="Vermietung">Vermietung</option>
                <option value="Beides">Beides</option>
            </select>
        </div>

        {{-- Anzahl Eigentümer --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Eigentümer</label>
            <input type="number" name="owner_count" class="form-control" value="{{ old('owner_count') }}">
        </div>

        {{-- Wohneinheiten --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Wohneinheiten</label>
            <input type="number" name="number_we" class="form-control" value="">
        </div>

        {{-- Personenanzahl --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Personen</label>
            <input type="number" name="number_people" class="form-control" value="{{ old('number_people') }}">
        </div>

        {{-- Baujahr --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Baujahr</label>
            <input type="number" name="house_year" class="form-control" value="{{ old('house_year') }}">
        </div>

        {{-- Geschosse --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Geschosse</label>
            <input type="number" name="number_stories" class="form-control" value="{{ old('number_stories') }}">
        </div>

        {{-- Wohnfläche --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Wohnfläche</label>
            <div class="input-group">
                <input type="number" name="living_space" class="form-control" value="{{ old('living_space') }}">
                <span class="input-group-text">m²</span>
            </div>
        </div>

        {{-- Dämmung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Dämmung</label>
            <input type="number" name="external_insulation_thickness" class="form-control" value="{{ old('external_insulation_thickness') }}">
        </div>

        {{-- Mauerwerk --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Mauerwerk</label>
            <input type="text" name="masonry" class="form-control" value="{{ old('masonry') }}">
        </div>

        {{-- Fensterverglasung --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Verglasung</label>
            <select name="window_glazing" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="1-fach">1 Fach</option>
                <option value="2-fach">2 Fach</option>
                <option value="3-fach">3 Fach</option>
            </select>
        </div>

        {{-- Fensterrahmen --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Rahmen</label>
            <select name="window_frame" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="Alu">Alu</option>
                <option value="Kunststoff">Kunststoff</option>
                <option value="Sonstiges">Sonstiges</option>
            </select>
        </div>

        {{-- Fenster Baujahr --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Fenster Bj.</label>
            <input type="number" name="window_year" class="form-control" value="{{ old('window_year') }}">
        </div>

        {{-- Haustür Baujahr --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Tür Bj.</label>
            <input type="number" name="door_year" class="form-control" value="{{ old('door_year') }}">
        </div>

        {{-- Haustür Material --}}
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-center mb-2">
            <label class="me-2" style="width: 140px;">Tür Material</label>
            <select name="door_condition" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="Alu">Alu</option>
                <option value="Kunststoff">Kunststoff</option>
                <option value="Sonstiges">Sonstiges</option>
            </select>
        </div>

        {{-- Bemerkung --}}
        <div class="col-12 mt-3">
            <label>Bemerkung</label>
            <textarea name="object_remark" class="form-control" rows="3">{{ old('object_remark') }}</textarea>
        </div>
    </div>
</div>
