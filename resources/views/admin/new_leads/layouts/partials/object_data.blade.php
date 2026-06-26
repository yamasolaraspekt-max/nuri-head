<style>
    .fw-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.fw-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}

.fw-col-span-2 {
    grid-column: span 2 / span 2;
}

.fw-mini-card {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 14px;
}

.fw-mini-title {
    font-size: 13px;
    font-weight: 800;
    color: #334155;
    margin-bottom: 10px;
}

.fw-check {
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 42px;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
}

.fw-check input {
    width: 16px;
    height: 16px;
}

.mt-3 {
    margin-top: 14px;
}

@media (max-width: 900px) {
    .fw-grid-3,
    .fw-grid-4 {
        grid-template-columns: 1fr;
    }

    .fw-col-span-2 {
        grid-column: auto;
    }
}
</style>
<form class="partial-form" data-section="object_data" data-id="{{ $alternative->id }}">
    @csrf

    <div class="fw-shell"> 
        <div class="fw-body"> 
            <div class="fw-grid-2"> 
                {{-- LEFT --}}
                <div class="fw-col">

                    {{-- ===================== KLASSIFIZIERUNG ===================== --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-blue">
                                <i class="feather icon-tag"></i>
                                Klassifizierung
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label">Objektart</label>
                                <select name="object_type" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="EFH" @selected(optional($alternative)->object_type == 'EFH')>EFH</option>
                                    <option value="MFH" @selected(optional($alternative)->object_type == 'MFH')>MFH</option>
                                    <option value="Gewerbe" @selected(optional($alternative)->object_type == 'Gewerbe')>Gewerbe</option>
                                    <option value="Sonstiges" @selected(optional($alternative)->object_type == 'Sonstiges')>Sonstiges</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Gebäudeart</label>
                                <select name="building_type" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Einfamilienhaus" @selected(optional($alternative)->building_type == 'Einfamilienhaus')>Einfamilienhaus</option>
                                    <option value="Reihenmittelhaus" @selected(optional($alternative)->building_type == 'Reihenmittelhaus')>Reihenmittelhaus</option>
                                    <option value="Doppelhaushälfte" @selected(optional($alternative)->building_type == 'Doppelhaushälfte')>Doppelhaushälfte</option>
                                    <option value="Mehrfamilienhaus" @selected(optional($alternative)->building_type == 'Mehrfamilienhaus')>Mehrfamilienhaus</option>
                                    <option value="Gewerbe" @selected(optional($alternative)->building_type == 'Gewerbe')>Gewerbe</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Zustand</label>
                                <select name="building_condition" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Neubau" @selected(optional($alternative)->building_condition == 'Neubau')>Neubau</option>
                                    <option value="Sanierung" @selected(optional($alternative)->building_condition == 'Sanierung')>Sanierung</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Nutzungsart</label>
                                <select name="usage_type" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Eigennutzung" @selected(optional($alternative)->usage_type == 'Eigennutzung')>Eigennutzung</option>
                                    <option value="Vermietung" @selected(optional($alternative)->usage_type == 'Vermietung')>Vermietung</option>
                                    <option value="Beides" @selected(optional($alternative)->usage_type == 'Beides')>Beides</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- ===================== KAPAZITÄT & GRÖSSE ===================== --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-emerald">
                                <i class="feather icon-maximize"></i>
                                Kapazität & Größe
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label">Wohnfläche</label>
                                <div class="fw-input-group">
                                    <input type="number" name="living_space" class="fw-input control-field count-me" value="{{ old('living_space', optional($alternative)->living_space ?? optional($alternative)->heated_area) }}">
                                    <span class="fw-addon">m²</span>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Nutzfläche</label>
                                <div class="fw-input-group">
                                    <input type="number" name="unusable_space" class="fw-input control-field count-me" value="{{ old('unusable_space', optional($alternative)->unusable_space) }}">
                                    <span class="fw-addon">m²</span>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Wohneinheiten</label>
                                    <input type="number" name="number_we" class="fw-input control-field count-me" value="{{ old('number_we', optional($alternative)->number_we) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Geschosse</label>
                                    <input type="number" name="story_count" class="fw-input control-field count-me" value="{{ old('story_count', optional($alternative)->story_count) }}">
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Personen</label>
                                    <input type="number" name="number_people" class="fw-input control-field count-me" value="{{ old('number_people', optional($alternative)->number_people ?? optional($alternative)->person_count) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Anzahl Bäder</label>
                                    <input type="number" name="bathroom_count" class="fw-input control-field count-me" value="{{ old('bathroom_count', optional($alternative)->bathroom_count) }}">
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Eigentümer / Besitzer Details</label>
                                <input type="text" name="owner_count" class="fw-input control-field count-me" value="{{ old('owner_count', optional($alternative)->owner_count) }}">
                            </div>
                        </div>
                    </section>

                    {{-- ===================== WP AKTUELLE HEIZUNG ===================== --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-orange">
                                <i class="feather icon-thermometer"></i>
                                WP — Aktuelle Heizung
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Kamin vorhanden?</label>
                                    <select name="fireplace" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="1" @selected(optional($alternative)->fireplace == '1')>Ja</option>
                                        <option value="0" @selected(optional($alternative)->fireplace == '0')>Nein</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Heizungsart</label>
                                    <select name="heating_system_type" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="Öl" @selected(optional($alternative)->heating_system_type == 'Öl')>Öl</option>
                                        <option value="Gas" @selected(optional($alternative)->heating_system_type == 'Gas')>Gas</option>
                                        <option value="Pellets" @selected(optional($alternative)->heating_system_type == 'Pellets')>Pellets</option>
                                        <option value="Sonstiges" @selected(optional($alternative)->heating_system_type == 'Sonstiges')>Sonstiges</option>
                                    </select>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Alte Heizleistung</label>
                                    <div class="fw-input-group">
                                        <input type="number" name="old_heating_power" class="fw-input control-field count-me" value="{{ old('old_heating_power', optional($alternative)->old_heating_power) }}">
                                        <span class="fw-addon">kW</span>
                                    </div>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Aufstellort Geschoss</label>
                                    <select name="installation_location" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="KG" @selected(optional($alternative)->installation_location == 'KG')>KG</option>
                                        <option value="EG" @selected(optional($alternative)->installation_location == 'EG')>EG</option>
                                        <option value="OG" @selected(optional($alternative)->installation_location == 'OG')>OG</option>
                                        <option value="DG" @selected(optional($alternative)->installation_location == 'DG')>DG</option>
                                    </select>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Besonderheiten Heizung</label>
                                <input type="text" name="heating_notes" class="fw-input control-field count-me" value="{{ old('heating_notes', optional($alternative)->heating_notes) }}">
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Heizung Material</label>
                                    <select name="pipe_system_material" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="Kupfer" @selected(optional($alternative)->pipe_system_material == 'Kupfer')>Kupfer</option>
                                        <option value="Kunststoff" @selected(optional($alternative)->pipe_system_material == 'Kunststoff')>Kunststoff</option>
                                        <option value="Stahl" @selected(optional($alternative)->pipe_system_material == 'Stahl')>Stahl</option>
                                        <option value="Mehrschichtverbund" @selected(optional($alternative)->pipe_system_material == 'Mehrschichtverbund')>Mehrschichtverbund</option>
                                        <option value="Sonstiges" @selected(optional($alternative)->pipe_system_material == 'Sonstiges')>Sonstiges</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Heizung Dimension</label>
                                    <input type="text" name="heating_pipe_dimension" class="fw-input control-field count-me" value="{{ old('heating_pipe_dimension', optional($alternative)->heating_pipe_dimension) }}">
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">KW / WW Dimension</label>
                                    <input type="text" name="water_pipe_dimension" class="fw-input control-field count-me" value="{{ old('water_pipe_dimension', optional($alternative)->water_pipe_dimension) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Zirkulation Dimension</label>
                                    <input type="text" name="circulation_pipe_dimension" class="fw-input control-field count-me" value="{{ old('circulation_pipe_dimension', optional($alternative)->circulation_pipe_dimension) }}">
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- RIGHT --}}
                <div class="fw-col">

                    {{-- ===================== BAUWEISE & HÜLLE ===================== --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-orange">
                                <i class="feather icon-package"></i>
                                Bauweise & Hülle
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Baujahr</label>
                                    <input type="number" name="house_year" class="fw-input control-field count-me" value="{{ old('house_year', optional($alternative)->house_year ?? optional($alternative)->building_year) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Mauerwerk</label>
                                    <input type="text" name="masonry" class="fw-input control-field count-me" value="{{ old('masonry', optional($alternative)->masonry) }}">
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Dämmung</label>
                                <input type="text" name="external_insulation_thickness" class="fw-input control-field count-me" value="{{ old('external_insulation_thickness', optional($alternative)->external_insulation_thickness) }}">
                            </div>
                        </div>
                    </section>

                    {{-- ===================== FENSTER & TÜREN ===================== --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge">
                                <i class="feather icon-layout"></i>
                                Fenster & Türen
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Fenster Verglasung</label>
                                    <select name="window_glazing" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="1-fach" @selected(optional($alternative)->window_glazing == '1-fach')>1-fach</option>
                                        <option value="2-fach" @selected(optional($alternative)->window_glazing == '2-fach')>2-fach</option>
                                        <option value="3-fach" @selected(optional($alternative)->window_glazing == '3-fach')>3-fach</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Fenster Rahmen</label>
                                    <select name="window_frame" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="Alu" @selected(optional($alternative)->window_frame == 'Alu')>Alu</option>
                                        <option value="Kunststoff" @selected(optional($alternative)->window_frame == 'Kunststoff')>Kunststoff</option>
                                        <option value="Holz" @selected(optional($alternative)->window_frame == 'Holz')>Holz</option>
                                        <option value="Sonstiges" @selected(optional($alternative)->window_frame == 'Sonstiges')>Sonstiges</option>
                                    </select>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Fenster Bj.</label>
                                    <input type="text" name="window_year" class="fw-input control-field count-me" value="{{ old('window_year', optional($alternative)->window_year) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Tür Bj.</label>
                                    <input type="text" name="door_year" class="fw-input control-field count-me" value="{{ old('door_year', optional($alternative)->door_year) }}">
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Tür Zustand</label>
                                <select name="door_condition" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Alu" @selected(optional($alternative)->door_condition == 'Alu')>Alu</option>
                                    <option value="Kunststoff" @selected(optional($alternative)->door_condition == 'Kunststoff')>Kunststoff</option>
                                    <option value="Holz" @selected(optional($alternative)->door_condition == 'Holz')>Holz</option>
                                    <option value="Sonstiges" @selected(optional($alternative)->door_condition == 'Sonstiges')>Sonstiges</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- ===================== WP BAD / POOL / WARMWASSER ===================== --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-emerald">
                                <i class="feather icon-droplet"></i>
                                Bad, Pool & Warmwasser
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Badewanne vorhanden?</label>
                                    <select name="note_bathtub" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="Nein">Nein</option>
                                        <option value="Ja">Ja</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Badewanne Anzahl</label>
                                    <input type="number" name="bathtub_count" class="fw-input control-field count-me" value="{{ old('bathtub_count', optional($alternative)->bathtub_count) }}">
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Badewanne Abmessung</label>
                                <input type="text" name="note_bathtubDim" class="fw-input control-field count-me" placeholder="z.B. 180 x 80 cm">
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Schwimmbad vorhanden?</label>
                                    <select name="note_pool" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="Nein">Nein</option>
                                        <option value="Ja">Ja</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Pool Volumen</label>
                                    <div class="fw-input-group">
                                        <input type="number" name="note_poolVolume" class="fw-input control-field count-me">
                                        <span class="fw-addon">m³</span>
                                    </div>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Warmwasser Aufbereitung</label>
                                    <select name="hot_water_generation" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="direkt" @selected(optional($alternative)->hot_water_generation == 'direkt')>Direkt</option>
                                        <option value="indirekt" @selected(optional($alternative)->hot_water_generation == 'indirekt')>Indirekt</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Warmwasser Speicher</label>
                                    <div class="fw-input-group">
                                        <input type="number" name="hot_water_tank_liters" class="fw-input control-field count-me" value="{{ old('hot_water_tank_liters', optional($alternative)->hot_water_tank_liters) }}">
                                        <span class="fw-addon">Liter</span>
                                    </div>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Thermische Solaranlage</label>
                                    <select name="solar_thermal" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="1" @selected(optional($alternative)->solar_thermal == '1')>Ja</option>
                                        <option value="0" @selected(optional($alternative)->solar_thermal == '0')>Nein</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Solarthermie Fläche / Module</label>
                                    <input type="number" name="solar_thermal_area" class="fw-input control-field count-me" value="{{ old('solar_thermal_area', optional($alternative)->solar_thermal_area) }}">
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            {{-- ===================== PV ANLAGE ===================== --}}
            <div class="fw-full">
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge fw-badge-orange">
                            <i class="feather icon-sun"></i>
                            PV — Kunde bekommt
                        </span>
                    </div>

                    <div class="fw-grid-3">
                        <div class="fw-field">
                            <label class="fw-label">Projektart PV</label>
                            <select name="objective" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Neuanlage" @selected(optional($alternative)->objective == 'Neuanlage')>Neuanlage</option>
                                <option value="Erweiterung" @selected(optional($alternative)->objective == 'Erweiterung')>Erweiterung</option>
                                <option value="Demontage" @selected(optional($alternative)->objective == 'Demontage')>Demontage alt</option> 
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Anlagengröße</label>
                            <div class="fw-input-group">
                                <input type="number" step="0.1" name="kwp_size" class="fw-input control-field count-me" value="{{ old('kwp_size', optional($alternative)->kwp_size) }}">
                                <span class="fw-addon">kWp</span>
                            </div>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Anzahl Module</label>
                            <input type="number" name="module_count" class="fw-input control-field count-me" value="{{ old('module_count', optional($alternative)->module_count) }}">
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Bei Demontage</label>
                            <select name="note_demontageVerbleib" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Kunde">Module beim Kunden lassen</option>
                                <option value="Lager">Mitnehmen zu uns ins Lager</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Kabelführung ausreichend?</label>
                            <select name="note_kabelAusreichend" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>
                    </div>
                </section>
            </div>

            {{-- ===================== PV ZUSATZKOMPONENTEN ===================== --}}
            <div class="fw-grid-2">
                <div class="fw-col">
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge">
                                <i class="feather icon-battery-charging"></i>
                                PV — Batteriespeicher
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-check">
                                    <input type="checkbox" name="storage_preference" value="Ja" @checked(optional($alternative)->storage_preference == 'Ja')>
                                    <span>Batteriespeicher gewünscht</span>
                                </label>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Batterie Hersteller / Typ</label>
                                <input type="text" name="note_battery_type" class="fw-input control-field count-me">
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Batterie Größe</label>
                                <input type="text" name="note_battery_size" class="fw-input control-field count-me">
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Batterie Aufstellort</label>
                                <input type="text" name="note_battery_location" class="fw-input control-field count-me">
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">WR → ZS</label>
                                    <div class="fw-input-group">
                                        <input type="number" name="note_batteryDistWrZs" class="fw-input control-field count-me">
                                        <span class="fw-addon">m</span>
                                    </div>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">BA → WR</label>
                                    <div class="fw-input-group">
                                        <input type="number" name="note_batteryDistBaWr" class="fw-input control-field count-me">
                                        <span class="fw-addon">m</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="fw-col">
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-emerald">
                                <i class="feather icon-zap"></i>
                                PV — Wärmepumpe / Wallbox
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-check">
                                    <input type="checkbox" name="note_wp_integration" value="Ja">
                                    <span>Wärmepumpe PV-Integration</span>
                                </label>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">WP Hersteller / Typ</label>
                                    <input type="text" name="note_wp_type" class="fw-input control-field count-me">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">WP Status</label>
                                    <select name="note_wpStatus" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="vorhanden">Vorhanden</option>
                                        <option value="geplant">Geplant</option>
                                    </select>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <label class="fw-check">
                                    <input type="checkbox" name="note_wp_heizstab" value="Ja">
                                    <span>Heizstab</span>
                                </label>

                                <label class="fw-check">
                                    <input type="checkbox" name="enwg_14a_ready" value="1" @checked(optional($alternative)->enwg_14a_ready == '1')>
                                    <span>SG Ready / EnWG 14a</span>
                                </label>
                            </div>

                            <hr>

                            <div class="fw-field">
                                <label class="fw-check">
                                    <input type="checkbox" name="wallbox_desired" value="1" @checked(optional($alternative)->wallbox_desired == '1')>
                                    <span>Wallbox gewünscht</span>
                                </label>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Wallbox Aufstellort</label>
                                    <input type="text" name="wallbox_location" class="fw-input control-field count-me" value="{{ old('wallbox_location', optional($alternative)->wallbox_location) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Entfernung zum ZS</label>
                                    <div class="fw-input-group">
                                        <input type="number" name="note_wallbox_distance" class="fw-input control-field count-me">
                                        <span class="fw-addon">m</span>
                                    </div>
                                </div>
                            </div>

                            <label class="fw-check">
                                <input type="checkbox" name="note_wallboxKernbohrung" value="Ja">
                                <span>Kernbohrung Außenwand / WU-Beton</span>
                            </label>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label">Erdarbeiten nötig?</label>
                                    <select name="note_wbErdarbeiten" class="fw-select control-field count-me">
                                        <option value="">Bitte wählen</option>
                                        <option value="Ja">Ja</option>
                                        <option value="Nein">Nein</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label">Erdarbeiten Länge</label>
                                    <input type="text" name="note_wbErdarbeitenLaenge" class="fw-input control-field count-me">
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Erdarbeiten durch</label>
                                <select name="note_wbErdarbeitenDurch" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Solar Aspekt">Durch uns / Gala Bauer</option>
                                    <option value="Kunde">Kunde / Gala-Bauer</option>
                                </select>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            {{-- ===================== WP NEUE ANLAGE ===================== --}}
            <div class="fw-full">
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge fw-badge-emerald">
                            <i class="feather icon-package"></i>
                            WP — Neue Anlage / Aufstellmöglichkeit
                        </span>
                    </div>

                    <div class="fw-grid-3">
                        <div class="fw-field">
                            <label class="fw-label">Neue Wärmequelle</label>
                            <select name="objective_wp" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Luft-Wasser Wärmepumpe">Luft-Wasser Wärmepumpe</option>
                                <option value="Sole-Wasser Wärmepumpe">Sole-Wasser Wärmepumpe</option>
                                <option value="Abluft-Wärmepumpe">Abluft-Wärmepumpe</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Passiv-Kühlung?</label>
                            <select name="note_passivKuehlung" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Lüftung</label>
                            <select name="ventilation_type" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="vorhanden Ja" @selected(optional($alternative)->ventilation_type == 'vorhanden Ja')>Vorhanden Ja</option>
                                <option value="Nein" @selected(optional($alternative)->ventilation_type == 'Nein')>Nein</option>
                                <option value="geplant zentral" @selected(optional($alternative)->ventilation_type == 'geplant zentral')>Geplant zentral</option>
                                <option value="geplant dezentral" @selected(optional($alternative)->ventilation_type == 'geplant dezentral')>Geplant dezentral</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Platz für VVM 500?</label>
                            <select name="note_platzVvm500" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Platz für WM S320?</label>
                            <select name="note_platzWm320" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Einzelkomponenten notwendig?</label>
                            <select name="note_einzelKomponenten" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>
                    </div>
                </section>
            </div>

            {{-- ===================== WP HEIZKREISE / ETAGEN ===================== --}}
            <div class="fw-full">
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge">
                            <i class="feather icon-layers"></i>
                            WP — Heizkreise & Etagen
                        </span>
                    </div>

                    <div class="fw-grid-2">
                        @foreach([
                                'kg' => 'Kellergeschoss (KG)',
                                'eg' => 'Erdgeschoss (EG)',
                                'og' => 'Obergeschoss (OG)',
                                'dg' => 'Dachgeschoss (DG)',
                            ] as $floorKey => $floorLabel)
                                <div class="fw-mini-card">
                                    <h4 class="fw-mini-title">{{ $floorLabel }}</h4>

                                    <div class="fw-grid-3">
                                        <div class="fw-field">
                                            <label class="fw-label">Status</label>
                                            <select name="note_{{ $floorKey }}Heiz" class="fw-select control-field count-me">
                                                <option value="">Bitte wählen</option>
                                                <option value="beheizt">Beheizt</option>
                                                <option value="nicht beheizt">Nicht beheizt</option>
                                            </select>
                                        </div>

                                        <label class="fw-check">
                                            <input type="checkbox" name="note_{{ $floorKey }}Fbh" value="1">
                                            <span>Fußbodenheizung</span>
                                        </label>

                                        <label class="fw-check">
                                            <input type="checkbox" name="note_{{ $floorKey }}Hk" value="1">
                                            <span>Heizkörper</span>
                                        </label>
                                    </div>
                                </div>
                        @endforeach
                    </div>

                    <div class="fw-grid-2-inner mt-3">
                        <div class="fw-field">
                            <label class="fw-label">Heizkreis 1 Vorlauf</label>
                            <div class="fw-input-group">
                                <input type="number" name="flow_temperature" class="fw-input control-field count-me" value="{{ old('flow_temperature', optional($alternative)->flow_temperature) }}">
                                <span class="fw-addon">°C</span>
                            </div>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Heizkreis 2 Vorlauf</label>
                            <div class="fw-input-group">
                                <input type="number" name="note_flow_temperature_2" class="fw-input control-field count-me">
                                <span class="fw-addon">°C</span>
                            </div>
                        </div>
                    </div>

                    <div class="fw-grid-3 mt-3">
                        <div class="fw-field">
                            <label class="fw-label">Regler für Kühlung geeignet?</label>
                            <select name="note_reglerKuehlung" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">HKV für hydr. Abgleich geeignet?</label>
                            <select name="note_hkvAbgleich" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Stellantriebe geeignet?</label>
                            <select name="note_stellantriebAbgleich" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>
                    </div>
                </section>
            </div>

            {{-- ===================== EINBRINGUNG ===================== --}}
            <div class="fw-full">
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge fw-badge-blue">
                            <i class="feather icon-move"></i>
                            WP — Einbringmaße & Zuwegung
                        </span>
                    </div>

                    <div class="fw-grid-3">
                        <div class="fw-field">
                            <label class="fw-label">Zuwegung Heizraum</label>
                            <select name="note_zuwegungHeizraum" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="KG">KG</option>
                                <option value="EG">EG</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Min. Breite zur Installation</label>
                            <div class="fw-input-group">
                                <input type="number" name="door_width_for_installation" class="fw-input control-field count-me" value="{{ old('door_width_for_installation', optional($alternative)->door_width_for_installation) }}">
                                <span class="fw-addon">cm</span>
                            </div>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Treppen vorhanden?</label>
                            <select name="note_treppen" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Nein">Nein</option>
                                <option value="Ja">Ja</option>
                            </select>
                        </div>
                    </div>

                    <div class="fw-grid-4 mt-3">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="fw-field">
                                <label class="fw-label">Türmaße {{ $i }}</label>
                                <div class="fw-grid-2-inner">
                                    <input type="number" name="note_t{{ $i }}Breite" class="fw-input control-field count-me" placeholder="Breite">
                                    <input type="number" name="note_t{{ $i }}Hoehe" class="fw-input control-field count-me" placeholder="Höhe">
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div class="fw-grid-3 mt-3">
                        <div class="fw-field">
                            <label class="fw-label">Treppenart</label>
                            <select name="note_treppenArt" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="gradeläufig">Gradeläufig</option>
                                <option value="L-Form">L-Form</option>
                                <option value="U-Form">U-Form</option>
                                <option value="Wendel">Wendel</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Treppenbreite</label>
                            <div class="fw-input-group">
                                <input type="number" name="note_treppenBreite" class="fw-input control-field count-me">
                                <span class="fw-addon">cm</span>
                            </div>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Länge AE zu IE</label>
                            <div class="fw-input-group">
                                <input type="number" name="heat_pump_pipe_length" class="fw-input control-field count-me" value="{{ old('heat_pump_pipe_length', optional($alternative)->heat_pump_pipe_length) }}">
                                <span class="fw-addon">m</span>
                            </div>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Anschluss außen</label>
                            <select name="note_anschlussAussen" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Wand">Wand</option>
                                <option value="Boden">Boden</option>
                            </select>
                        </div>
                    </div>
                </section>
            </div>

            {{-- ===================== ELEKTRO PV/WP ===================== --}}
            <div class="fw-full">
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge fw-badge-orange">
                            <i class="feather icon-zap"></i>
                            Elektro / Zählerschrank
                        </span>
                    </div>

                    <div class="fw-grid-3">
                        <label class="fw-check">
                            <input type="checkbox" name="ac_surge_protection" value="1" @checked(optional($alternative)->ac_surge_protection == '1')>
                            <span>AC-Überspannungsschutz vorhanden</span>
                        </label>

                        <label class="fw-check">
                            <input type="checkbox" name="sls_switch" value="1" @checked(optional($alternative)->sls_switch == '1')>
                            <span>SLS Schalter vorhanden</span>
                        </label>

                        <div class="fw-field">
                            <label class="fw-label">Mieterstrommodell?</label>
                            <select name="tenant_model" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="1" @selected(optional($alternative)->tenant_model == '1')>Ja</option>
                                <option value="0" @selected(optional($alternative)->tenant_model == '0')>Nein</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Zählerschrank Aktion</label>
                            <select name="meter_cabinet_action" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="neuer Zählerschrank notwendig" @selected(optional($alternative)->meter_cabinet_action == 'neuer Zählerschrank notwendig')>Neuer Zählerschrank notwendig</option>
                                <option value="alter Zählerschrank wird zur Unterverteilung" @selected(optional($alternative)->meter_cabinet_action == 'alter Zählerschrank wird zur Unterverteilung')>Alter Zählerschrank wird zur Unterverteilung</option>
                                <option value="zusätzliche Unterverteilung" @selected(optional($alternative)->meter_cabinet_action == 'zusätzliche Unterverteilung')>Zusätzliche Unterverteilung</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Neuer ZS Größe</label>
                            <select name="cabinet_size" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="550" @selected(optional($alternative)->cabinet_size == '550')>550</option>
                                <option value="800" @selected(optional($alternative)->cabinet_size == '800')>800</option>
                                <option value="1100" @selected(optional($alternative)->cabinet_size == '1100')>1100</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Zwischenzähler gewünscht?</label>
                            <select name="note_zwischenzaehler" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Stromzähler / Zwischenzähler Anzahl</label>
                            <input type="number" name="meter_count" class="fw-input control-field count-me" value="{{ old('meter_count', optional($alternative)->meter_count) }}">
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Zwischenzähler für WP?</label>
                            <select name="note_zwischenzaehlerWp" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja">Ja</option>
                                <option value="Nein">Nein</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">WP Zähler Anzahl</label>
                            <input type="number" name="note_zwischenzaehlerWpCount" class="fw-input control-field count-me">
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Internet-Anbindung</label>
                            <select name="network_wlan" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Ja" @selected(optional($alternative)->network_wlan == 'Ja')>Ja</option>
                                <option value="Nein" @selected(optional($alternative)->network_wlan == 'Nein')>Nein</option>
                                <option value="WLAN" @selected(optional($alternative)->network_wlan == 'WLAN')>WLAN</option>
                                <option value="LAN" @selected(optional($alternative)->network_wlan == 'LAN')>LAN</option>
                                <option value="Powerline" @selected(optional($alternative)->network_wlan == 'Powerline')>Powerline</option>
                                <option value="Dongle" @selected(optional($alternative)->network_wlan == 'Dongle')>Dongle</option>
                            </select>
                        </div>

                        <label class="fw-check">
                            <input type="checkbox" name="note_internetSteckdose" value="1">
                            <span>Steckdose setzen</span>
                        </label>

                        <div class="fw-field">
                            <label class="fw-label">Entfernung Steckdose</label>
                            <input type="text" name="note_internetSteckdoseDist" class="fw-input control-field count-me" placeholder="z.B. 2m">
                        </div>
                    </div>
                </section>
            </div>

            {{-- ===================== PV ABSICHERUNG ===================== --}}
            <div class="fw-full">
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge">
                            <i class="feather icon-shield"></i>
                            PV — Absicherung / Gerüst / Aufzug / Kran
                        </span>
                    </div>

                    <div class="fw-grid-3">
                        <div class="fw-field">
                            <label class="fw-label">Fangschutzgitter</label>
                            <select name="note_fangschutz" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="möglich">Möglich</option>
                                <option value="teilweise">Teilweise</option>
                                <option value="nicht möglich">Nicht möglich</option>
                            </select>
                        </div>

                        <div class="fw-field fw-col-span-2">
                            <label class="fw-label">Begründung Fangschutz</label>
                            <input type="text" name="note_fangschutz_reason" class="fw-input control-field count-me">
                        </div>

                        <label class="fw-check">
                            <input type="checkbox" name="scaffold_usage" value="1" @checked(optional($alternative)->scaffold_usage == '1')>
                            <span>Gerüst muss gestellt werden</span>
                        </label>

                        <div class="fw-field">
                            <label class="fw-label">Gerüst Machbarkeit</label>
                            <select name="note_geruestMachbar" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="möglich">Möglich</option>
                                <option value="teilweise">Teilweise</option>
                                <option value="nicht möglich">Nicht möglich</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Gerüst Begründung</label>
                            <input type="text" name="note_scaffold_reason" class="fw-input control-field count-me">
                        </div>

                        <label class="fw-check">
                            <input type="checkbox" name="note_aufzugMuss" value="1">
                            <span>Aufzug muss gestellt werden</span>
                        </label>

                        <div class="fw-field">
                            <label class="fw-label">Aufzug Machbarkeit</label>
                            <select name="note_aufzugMachbar" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="möglich">Möglich</option>
                                <option value="teilweise">Teilweise</option>
                                <option value="nicht möglich">Nicht möglich</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Aufzug Begründung</label>
                            <input type="text" name="note_aufzug_reason" class="fw-input control-field count-me">
                        </div>

                        <label class="fw-check">
                            <input type="checkbox" name="note_kranMuss" value="1">
                            <span>Kran muss gestellt werden</span>
                        </label>

                        <div class="fw-field">
                            <label class="fw-label">Kran Machbarkeit</label>
                            <select name="note_kranMachbar" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="möglich">Möglich</option>
                                <option value="teilweise">Teilweise</option>
                                <option value="nicht möglich">Nicht möglich</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Kran Begründung</label>
                            <input type="text" name="note_kran_reason" class="fw-input control-field count-me">
                        </div>
                    </div>
                </section>
            </div>

            {{-- ===================== SONSTIGE ARBEITEN / SCHALL ===================== --}}
            <div class="fw-grid-2">
                <div class="fw-col">
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-emerald">
                                <i class="feather icon-tool"></i>
                                WP — Sonstige Arbeiten
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label">Fundament & Erdarbeiten durch</label>
                                <select name="groundwork" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Solar Aspekt" @selected(optional($alternative)->groundwork == 'Solar Aspekt')>Solar Aspekt</option>
                                    <option value="Kunde" @selected(optional($alternative)->groundwork == 'Kunde')>Kunde</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Kondenswasser AE</label>
                                <select name="note_kondenswasser" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Sickergrube">Sickergrube</option>
                                    <option value="Abflussrohr ins Erdreich">Abflussrohr ins Erdreich</option>
                                    <option value="Anschluss im Haus">Anschluss im Haus</option>
                                </select>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="fw-col">
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-blue">
                                <i class="feather icon-volume-2"></i>
                                WP — Schallberechnung
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label">Aufstellgebiet</label>
                                <select name="note_schallGebiet" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Industriegebiet">Industriegebiet</option>
                                    <option value="urbanes Gebiet">Urbanes Gebiet</option>
                                    <option value="Allg. Wohngebiet">Allg. Wohngebiet / Kleinsiedlung</option>
                                    <option value="Gewerbegebiet">Gewerbegebiet</option>
                                    <option value="Kern-, Dorf-, Mischgebiet">Kern-, Dorf-, Mischgebiet</option>
                                    <option value="reines Wohngebiet">Reines Wohngebiet</option>
                                    <option value="Kurgebiet">Kurgebiet</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Aufstellort</label>
                                <select name="note_schallOrt" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Freistehend >3m">Freistehend &gt;3m von Wand</option>
                                    <option value="Wand <3m">An Wand &lt;3m</option>
                                    <option value="Ecke <3m">In Ecke &lt;3m</option>
                                    <option value="Wand <5m">An Wand &lt;5m</option>
                                    <option value="Zwischen Wänden <5m">Zwischen Wänden &lt;5m</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Abschirmung</label>
                                <select name="note_schallAbschirmung" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Sichtkontakt">Sichtkontakt</option>
                                    <option value="kein Sichtkontakt">Kein Sichtkontakt</option>
                                    <option value="auf abgewandter Seite">Auf abgewandter Seite</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Maßgeblicher Immissionsort</label>
                                <div class="fw-input-group">
                                    <input type="number" name="note_schallImmissionOrt" class="fw-input control-field count-me">
                                    <span class="fw-addon">m</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            {{-- ===================== NOTIZEN ===================== --}}
            <div class="fw-full">
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge">
                            <i class="feather icon-file-text"></i>
                            Notizen & Bemerkung
                        </span>
                    </div>

                    <div class="fw-field">
                        <label class="fw-label">Sonstige Kundenwünsche</label>
                        <textarea name="note_sonstigeWunsche" class="fw-textarea control-field count-me" rows="3">{{ old('note_sonstigeWunsche', optional($alternative)->note_sonstigeWunsche) }}</textarea>
                    </div>

                    <div class="fw-field mt-3">
                        <label class="fw-label">Zusätzliche Notizen</label>
                        <textarea name="note" class="fw-textarea control-field count-me" rows="4" placeholder="Besonderheiten zum Objekt hier eintragen...">{{ old('note', optional($alternative)->note ?? optional($alternative)->object_remark) }}</textarea>
                    </div>
                </section>
            </div>
        </div>

        <div class="fw-footer">
            <button type="submit" class="fw-btn fw-btn-primary" onclick="setTimeout(goNext, 500)">
                Speichern & Weiter
                <i class="feather icon-arrow-right"></i>
            </button>
        </div>
    </div>
</form>