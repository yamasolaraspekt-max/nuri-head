<div class="row">
    <!-- Left Column -->
    <div class="col-12 col-lg-6">
        <!-- Objektart -->
        <div class="form-group">
            <label class="font-weight-bold">Objektart</label>
            <select name="objective" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="EFH" @if($alternative->objective == "EFH") selected @endif>EFH</option>
                <option value="MFH" @if($alternative->objective == "MFH") selected @endif>MFH</option>
                <option value="Gewerbe" @if($alternative->objective == "Gewerbe") selected @endif>Gewerbe</option>
                <option value="others" @if($alternative->objective == "others") selected @endif>Sonstiges</option>
            </select>
        </div>

        <!-- Baujahr -->
        <div class="form-group">
            <label class="font-weight-bold">Baujahr</label>
            <input type="text" class="form-control" name="house_year" value="{{ old('house_year', $alternative->house_year) }}">
        </div>

        <!-- Wohneinheiten -->
        <div class="form-group">
            <label class="font-weight-bold">Wohneinheiten</label>
            <input type="text" class="form-control" name="number_we" value="{{ old('number_we', $alternative->number_we) }}">
        </div>

        <!-- Geschosse -->
        <div class="form-group">
            <label class="font-weight-bold">Geschosse</label>
            <input type="text" class="form-control" name="number_stories" value="{{ old('number_stories', $alternative->number_stories) }}">
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-12 col-lg-6">
        <!-- Wohnfläche -->
        <div class="form-group position-relative">
            <label class="font-weight-bold">Wohnfläche</label>
            <input type="text" class="form-control pe-5" name="living_space" value="{{ old('living_space', $alternative->living_space) }}">
            <span class="position-absolute" style="right: 15px; top: 38px;">m²</span>
        </div>

        <!-- Nutzfläche -->
        <div class="form-group position-relative">
            <label class="font-weight-bold">Nutzfläche</label>
            <input type="text" class="form-control pe-5" name="unusable_space" value="{{ old('unusable_space', $alternative->unusable_space) }}">
            <span class="position-absolute" style="right: 15px; top: 38px;">m²</span>
        </div>

        <!-- Personenanzahl -->
        <div class="form-group">
            <label class="font-weight-bold">Personenanzahl</label>
            <input type="text" class="form-control" name="number_people" value="{{ old('number_people', $alternative->number_people) }}">
        </div>
    </div>

    <!-- Bemerkung (Full Width) -->
    <div class="col-12">
        <div class="form-group">
            <label class="font-weight-bold">Bemerkung</label>
            <textarea name="object_remark" class="form-control" rows="2" style="resize: vertical;">{{ old('object_remark', $alternative->object_remark) }}</textarea>
        </div>
    </div>
</div>
<div class="row">
    <!-- Left Column -->
    <div class="col-12 col-lg-6">
        <!-- Objektart -->
        <div class="form-group">
            <label class="font-weight-bold">Objektart</label>
            <select name="objective" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="EFH" @if($alternative->objective == "EFH") selected @endif>EFH</option>
                <option value="MFH" @if($alternative->objective == "MFH") selected @endif>MFH</option>
                <option value="Gewerbe" @if($alternative->objective == "Gewerbe") selected @endif>Gewerbe</option>
                <option value="others" @if($alternative->objective == "others") selected @endif>Sonstiges</option>
            </select>
        </div>

        <!-- Baujahr -->
        <div class="form-group">
            <label class="font-weight-bold">Baujahr</label>
            <input type="text" class="form-control" name="house_year" value="{{ old('house_year', $alternative->house_year) }}">
        </div>

        <!-- Wohneinheiten -->
        <div class="form-group">
            <label class="font-weight-bold">Wohneinheiten</label>
            <input type="text" class="form-control" name="number_we" value="{{ old('number_we', $alternative->number_we) }}">
        </div>

        <!-- Geschosse -->
        <div class="form-group">
            <label class="font-weight-bold">Geschosse</label>
            <input type="text" class="form-control" name="number_stories" value="{{ old('number_stories', $alternative->number_stories) }}">
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-12 col-lg-6">
        <!-- Wohnfläche -->
        <div class="form-group position-relative">
            <label class="font-weight-bold">Wohnfläche</label>
            <input type="text" class="form-control pe-5" name="living_space" value="{{ old('living_space', $alternative->living_space) }}">
            <span class="position-absolute" style="right: 15px; top: 38px;">m²</span>
        </div>

        <!-- Nutzfläche -->
        <div class="form-group position-relative">
            <label class="font-weight-bold">Nutzfläche</label>
            <input type="text" class="form-control pe-5" name="unusable_space" value="{{ old('unusable_space', $alternative->unusable_space) }}">
            <span class="position-absolute" style="right: 15px; top: 38px;">m²</span>
        </div>

        <!-- Personenanzahl -->
        <div class="form-group">
            <label class="font-weight-bold">Personenanzahl</label>
            <input type="text" class="form-control" name="number_people" value="{{ old('number_people', $alternative->number_people) }}">
        </div>
    </div>

    <!-- Bemerkung (Full Width) -->
    <div class="col-12">
        <div class="form-group">
            <label class="font-weight-bold">Bemerkung</label>
            <textarea name="object_remark" class="form-control" rows="2" style="resize: vertical;">{{ old('object_remark', $alternative->object_remark) }}</textarea>
        </div>
    </div>
</div>
