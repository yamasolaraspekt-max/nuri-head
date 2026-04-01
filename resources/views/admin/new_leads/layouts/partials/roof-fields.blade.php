<div class="card mb-3 roof-group">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>{{ $index + 1 }}. Dachfläche</strong>
        <div>
            <button class="btn btn-sm btn-secondary" type="button" data-toggle="collapse" data-target="#roof-collapse-{{ $index }}">
                Ein-/Ausklappen
            </button>
            <button type="button" class="btn btn-sm btn-danger ms-2" onclick="this.closest('.roof-group').remove()">
                Entfernen
            </button>
        </div>
    </div>

    <div class="collapse show" id="roof-collapse-{{ $index }}">
        <div class="card-body">
            <div class="row">
                @php $r = $roof ?? null; @endphp

                <div class="col-md-4 mt-1">
                    <label>Name</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][designation]" value="{{ old("roofs.$index.designation", $r->designation ?? '') }}">
                </div>

                <div class="col-md-4 mt-1">
                    <label>Form</label>
                    <!-- <input type="text" class="form-control" name="roofs[{{ $index }}][roof]" value="{{ old("roofs.$index.roof", $r->roof ?? '') }}"> -->
                    <select name="roofs[{{ $index }}][type]" id="" class="form-control">
                        <option value="1">Flachdach</option>
                        <option value="2">Schrägdach</option>
                    </select>
                </div>

                <div class="col-md-4 mt-1">
                    <label>Typ</label>
                    <select name="roofs[{{ $index }}][roof_type]" id="roof_type" class="form-control">
                        <option value="">-- Bitte auswählen --</option>
                        <option value="1">Satteldach (Giebeldach)</option>
                        <option value="2">Walmdach</option>
                        <option value="3">Krüppelwalmdach</option>
                        <option value="4">Pultdach</option>
                        <option value="5">Mansarddach</option>
                        <option value="6">Zeltdach</option>
                    </select>
                </div>

                <div class="col-md-4 mt-1">
                    <label>Eindeckung</label>
                    <select name="roofs[{{ $index }}][roof_covering_name]" id="" class="form-control">
                        <optgroup label="Schrägdach">
                        <option value="Dachziegel">Dachziegel</option>
                        <option value="Betondachstein">Betondachstein</option>
                        <option value="Schiefer">Schiefer</option>
                        <option value="Reetdach">Reetdach</option>
                        </optgroup>
                        <optgroup label="Flachdach">
                        <option value="Foliendach">Foliendach</option>
                        <option value="Gründach">Gründach</option>
                        </optgroup>
                        <optgroup label="Metall">
                        <option value="Blechdach">Blechdach</option>
                        <option value="Trapezblech">Trapezblech</option>
                        <option value="Sandwichpaneele">Sandwichpaneele</option>
                        </optgroup>
                    </select>

                 </div>

                <div class="col-md-4 mt-1">
                    <label>Hersteller</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][roof_covering_company]" value="{{ old("roofs.$index.roof", $r->roof ?? '') }}">
                </div>

                <div class="col-md-4 mt-1">
                    <label>Model</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][roof_covering_model]" value="{{ old("roofs.$index.roof_covering_name", $r->roof_covering_name ?? '') }}">
                </div>
 

                <div class="col-md-4 mt-1">
                    <label>Dachausrichtung</label>
                    <select name="roofs[{{ $index }}][roof_orientation]" id="roof_orientation" class="form-control">
                        <option value="">-- Bitte auswählen --</option>
                        <option value="Nord">Nord</option>
                        <option value="Nord-Ost">Nord-Ost</option>
                        <option value="Ost">Ost</option>
                        <option value="Süd-Ost">Süd-Ost</option>
                        <option value="Süd">Süd</option>
                        <option value="Süd-West">Süd-West</option>
                        <option value="West">West</option>
                        <option value="Nord-West">Nord-West</option>
                        <option value="Flachdach">Flachdach (keine spezifische Ausrichtung)</option>
                    </select>
      
                </div>


                <div class="col-md-4 mt-1">
                    <label>Dachneigung</label>
                    <select name="roofs[{{ $index }}][roof_pitch]" id="roof_pitch" class="form-control">
                        <option value="">-- Bitte auswählen --</option>
                        <option value="0">0° (Flachdach)</option>
                        <option value="5">5°</option>
                        <option value="10">10°</option>
                        <option value="15">15°</option>
                        <option value="20">20°</option>
                        <option value="25">25°</option>
                        <option value="30">30°</option>
                        <option value="35">35°</option>
                        <option value="40">40°</option>
                        <option value="45">45°</option>
                        <option value="50">50°</option>
                        <option value="55">55°</option>
                        <option value="60">60°+</option>
                    </select> 
                </div>


                <div class="col-md-4 mt-1">
                    <label>Traufhöhe</label>
                    <input type="number" step="any" class="form-control" name="roofs[{{ $index }}][roof_height]" value="{{ old("roofs.$index.roof_age", $r->roof_age ?? '') }}">
                </div>
                <div class="col-md-4 mt-1">
                    <label>Alter</label>
                    <input type="number" step="any" class="form-control" name="roofs[{{ $index }}][roof_age]" value="{{ old("roofs.$index.roof_age", $r->roof_age ?? '') }}">
                </div>

                <div class="col-md-4 mt-1">
                    <label>Dämmstärke</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][thickness_roof_insulation]" value="{{ old("roofs.$index.thickness_roof_insulation", $r->thickness_roof_insulation ?? '') }}">
                </div>

                <div class="col-md-4 mt-1">
                    <label>Dämmmaterial</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][insulation_material]" value="{{ old("roofs.$index.thickness_roof_insulation", $r->thickness_roof_insulation ?? '') }}">
                </div>

                <div class="col-md-4 mt-1">
                    <label>Dachsanierung</label>
                    <select class="form-control" name="roofs[{{ $index }}][roof_renovation]">
                        <option value="">-</option>
                        <option value="Ja" @selected(old("roofs.$index.roof_renovation", $r->roof_renovation ?? '') == 'Ja')>Ja</option>
                        <option value="Nein" @selected(old("roofs.$index.roof_renovation", $r->roof_renovation ?? '') == 'Nein')>Nein</option>
                    </select>
                </div>



                <div class="col-md-4 mt-1">
                    <label>PV vorhanden</label>
                    <select class="form-control" name="roofs[{{ $index }}][pv_existing]">
                        <option value="">-</option>
                        <option value="Ja" @selected(old("roofs.$index.pv_existing", $r->pv_existing ?? '') == 'Ja')>Ja</option>
                        <option value="Nein" @selected(old("roofs.$index.pv_existing", $r->pv_existing ?? '') == 'Nein')>Nein</option>
                    </select>
                </div>

                <div class="col-md-4 mt-1">
                    <label>Baujahr</label>
                    <input type="number" step="any" class="form-control" name="roofs[{{ $index }}][construction_year]" value="{{ old("roofs.$index.construction_year", $r->construction_year ?? '') }}">
                </div>

                <div class="col-md-4 mt-1">
                    <label>Anzahl Module</label>
                    <input type="number" step="any" class="form-control" name="roofs[{{ $index }}][module_count]" value="{{ old("roofs.$index.module_count", $r->module_count ?? '') }}">
                </div>

                <div class="col-md-4 mt-1">
                    <label>Wattleistung Modul</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][module_power]" value="{{ old("roofs.$index.module_power", $r->module_power ?? '') }}">
                </div>

                <div class="col-md-4 mt-1">
                    <label>Größe kWp</label>
                    <input type="number" step="any" class="form-control" name="roofs[{{ $index }}][kwp_size]" value="{{ old("roofs.$index.kwp_size", $r->kwp_size ?? '') }}">
                </div>

                <div class="col-md-4 mt-1">
                    <label>weiteres Vorgehen</label>
                    <select class="form-control" name="roofs[{{ $index }}][intention]">
                        <option value="Interesse" @selected(old("roofs.$index.intention", $r->intention ?? '') == 'Interesse')>Interesse</option>
                        <option value="vorhanden" @selected(old("roofs.$index.intention", $r->intention ?? '') == 'vorhanden')>vorhanden</option>
                        <option value="Erweiterung" @selected(old("roofs.$index.intention", $r->intention ?? '') == 'Erweiterung')>Erweiterung</option>
                        <option value="später" @selected(old("roofs.$index.intention", $r->intention ?? '') == 'später')>später</option>
                    </select>
                </div>

                <div class="col-12 mt-2">
                    <label>Bemerkung</label>
                    <textarea class="form-control" rows="2" name="roofs[{{ $index }}][notes]">{{ old("roofs.$index.notes", $r->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
