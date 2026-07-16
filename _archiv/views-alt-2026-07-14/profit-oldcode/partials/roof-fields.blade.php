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

                <div class="col-md-4 mt-2">
                    <label>Name</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][designation]" value="{{ old("roofs.$index.designation", $r->designation ?? '') }}">
                </div>

                <div class="col-md-4 mt-2">
                    <label>Form</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][roof]" value="{{ old("roofs.$index.roof", $r->roof ?? '') }}">
                </div>

                <div class="col-md-4 mt-2">
                    <label>Eindeckung</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][roof_covering_name]" value="{{ old("roofs.$index.roof_covering_name", $r->roof_covering_name ?? '') }}">
                </div>

                <div class="col-md-4 mt-2">
                    <label>Alter</label>
                    <input type="number" step="any" class="form-control" name="roofs[{{ $index }}][roof_age]" value="{{ old("roofs.$index.roof_age", $r->roof_age ?? '') }}">
                </div>

                <div class="col-md-4 mt-2">
                    <label>Dämmstärke</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][thickness_roof_insulation]" value="{{ old("roofs.$index.thickness_roof_insulation", $r->thickness_roof_insulation ?? '') }}">
                </div>

                <div class="col-md-4 mt-2">
                    <label>Dachsanierung</label>
                    <select class="form-control" name="roofs[{{ $index }}][roof_renovation]">
                        <option value="">-</option>
                        <option value="Ja" @selected(old("roofs.$index.roof_renovation", $r->roof_renovation ?? '') == 'Ja')>Ja</option>
                        <option value="Nein" @selected(old("roofs.$index.roof_renovation", $r->roof_renovation ?? '') == 'Nein')>Nein</option>
                    </select>
                </div>

                <div class="col-md-4 mt-2">
                    <label>PV vorhanden</label>
                    <select class="form-control" name="roofs[{{ $index }}][pv_existing]">
                        <option value="">-</option>
                        <option value="Ja" @selected(old("roofs.$index.pv_existing", $r->pv_existing ?? '') == 'Ja')>Ja</option>
                        <option value="Nein" @selected(old("roofs.$index.pv_existing", $r->pv_existing ?? '') == 'Nein')>Nein</option>
                    </select>
                </div>

                <div class="col-md-4 mt-2">
                    <label>Baujahr</label>
                    <input type="number" step="any" class="form-control" name="roofs[{{ $index }}][construction_year]" value="{{ old("roofs.$index.construction_year", $r->construction_year ?? '') }}">
                </div>

                <div class="col-md-4 mt-2">
                    <label>Anzahl Module</label>
                    <input type="number" step="any" class="form-control" name="roofs[{{ $index }}][module_count]" value="{{ old("roofs.$index.module_count", $r->module_count ?? '') }}">
                </div>

                <div class="col-md-4 mt-2">
                    <label>Wattleistung Modul</label>
                    <input type="text" class="form-control" name="roofs[{{ $index }}][module_power]" value="{{ old("roofs.$index.module_power", $r->module_power ?? '') }}">
                </div>

                <div class="col-md-4 mt-2">
                    <label>Größe kWp</label>
                    <input type="number" step="any" class="form-control" name="roofs[{{ $index }}][kwp_size]" value="{{ old("roofs.$index.kwp_size", $r->kwp_size ?? '') }}">
                </div>

                <div class="col-md-4 mt-2">
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
