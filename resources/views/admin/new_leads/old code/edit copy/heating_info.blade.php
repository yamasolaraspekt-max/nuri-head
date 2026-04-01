<div class="row">
    <!-- Heiztechnik -->
    <div class="col-12 col-md-6 mt-1">
        <label class="font-weight-bold">Heiztechnik</label>
        <select class="form-control" name="heating_system_type" id="heating_system_type">
            <option value="">Bitte wählen</option>
            <option value="Gas" @if($alternative->heating_system_type == "Gas") selected @endif>Gas</option>
            <option value="Öl" @if($alternative->heating_system_type == "Öl") selected @endif>Öl</option>
            <option value="Wärmepumpe" @if($alternative->heating_system_type == "Wärmepumpe") selected @endif>Wärmepumpe</option>
            <option value="Nachtspeicher" @if($alternative->heating_system_type == "Nachtspeicher") selected @endif>Nachtspeicher</option>
        </select>
    </div>

    <!-- Alter -->
    <div class="col-12 col-md-6 mt-1 position-relative">
        <label class="font-weight-bold">Alter</label>
        <input type="text" class="form-control" name="heating_system_age" id="heating_system_age" value="{{ old('heating_system_age', $alternative->heating_system_age) }}">
        <input type="hidden" name="heating_system_year" id="heating_system_year" value="{{ old('heating_system_year', $alternative->heating_system_year) }}">
        <span class="position-absolute" style="right: 15px; top: 38px;">Jahr</span>
        <small class="text-danger" id="heating_system_age_error"></small>
    </div>

    <!-- Heizsystem -->
    <div class="col-12 col-md-6 mt-1">
        <label class="font-weight-bold">Heizsystem</label>
        <select name="heating_type" id="heating_type" class="form-control">
            <option value="">Bitte wählen</option>
            <option value="underfloor_heating" @if($alternative->heating_type == "underfloor_heating") selected @endif>Fußbodenheizung</option>
            <option value="heating_system" @if($alternative->heating_type == "heating_system") selected @endif>Heizkörper</option>
            <option value="both" @if($alternative->heating_type == "both") selected @endif>Fußbodenheizung + Heizkörper</option>
            <option value="none" @if($alternative->heating_type == "none") selected @endif>Keine</option>
        </select>
    </div>

    <!-- Ort -->
    <div class="col-12 col-md-6 mt-1">
        <label class="font-weight-bold">Ort</label>
        <div class="d-flex flex-column flex-md-row gap-1">
            <select name="installation_location" id="installation_location" class="form-control me-md-2">
                <option value="">Bitte wählen</option>
                <option value="KG" @if($alternative->installation_location == "KG") selected @endif>KG</option>
                <option value="EG" @if($alternative->installation_location == "EG") selected @endif>EG</option>
                <option value="OG" @if($alternative->installation_location == "OG") selected @endif>OG</option>
                <option value="DG" @if($alternative->installation_location == "DG") selected @endif>DG</option>
                <option value="SONSTIGES" @if($alternative->installation_location == "SONSTIGES") selected @endif>SONSTIGES</option>
            </select>
            <input type="text" class="form-control mt-1 mt-md-0" name="installation_location_extra" id="installation_location_extra" value="{{ old('installation_location_extra', $alternative->installation_location_extra) }}" placeholder="SONSTIGES...">
        </div>
    </div>

    <!-- Bemerkung -->
    <div class="col-12 mt-2">
        <label class="font-weight-bold">Bemerkung</label>
        <textarea name="heating_remark" class="form-control" rows="2" style="resize: vertical;">{{ old('heating_remark', $alternative->heating_remark) }}</textarea>
    </div>
</div>
