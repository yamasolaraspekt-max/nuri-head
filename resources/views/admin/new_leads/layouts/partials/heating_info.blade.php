<form class="partial-form" data-section="heating_info" data-id="{{ $alternative->id }}">
    @csrf

    <div class="fw-shell">

        <div class="fw-body">
            <div class="fw-grid-2">

                {{-- LEFT COLUMN --}}
                <div class="fw-col">

                    {{-- AKTUELLE HEIZUNG --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-orange">
                                <i class="feather icon-thermometer"></i>
                                Aktuelle Heizung
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Aktuelle Heizungsart / Energieträger.">
                                        Heizungsart
                                    </label>
                                    <select class="fw-select control-field count-me" name="heating_system_type">
                                        <option value="" @selected(optional($alternative)->heating_system_type == '')>
                                            Bitte wählen</option>
                                        <option value="Öl" @selected(optional($alternative)->heating_system_type == 'Öl')>
                                            Öl</option>
                                        <option value="Gas"
                                            @selected(optional($alternative)->heating_system_type == 'Gas')>Gas</option>
                                        <option value="Pellets"
                                            @selected(optional($alternative)->heating_system_type == 'Pellets')>Pellets
                                        </option>
                                        <option value="Wärmepumpe"
                                            @selected(optional($alternative)->heating_system_type == 'Wärmepumpe')>
                                            Wärmepumpe</option>
                                        <option value="Nachtspeicher"
                                            @selected(optional($alternative)->heating_system_type == 'Nachtspeicher')>
                                            Nachtspeicher</option>
                                        <option value="Sonstiges"
                                            @selected(optional($alternative)->heating_system_type == 'Sonstiges')>
                                            Sonstiges</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Leistung der alten Heizung in kW.">
                                        Leistung
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" class="fw-input control-field count-me"
                                            name="old_heating_power"
                                            value="{{ old('old_heating_power', optional($alternative)->old_heating_power) }}">
                                        <span class="fw-addon">kW</span>
                                    </div>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Alter der aktuellen Heizung in Jahren.">
                                        Alter
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" class="fw-input control-field count-me"
                                            name="heating_system_age"
                                            value="{{ old('heating_system_age', optional($alternative)->heating_system_age) }}">
                                        <span class="fw-addon">Jahre</span>
                                    </div>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Aufstellort der aktuellen Heizung.">
                                        Aufstellort Geschoss
                                    </label>
                                    <select name="installation_location" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->installation_location == '')>
                                            Bitte wählen</option>
                                        <option value="KG"
                                            @selected(optional($alternative)->installation_location == 'KG')>KG</option>
                                        <option value="EG"
                                            @selected(optional($alternative)->installation_location == 'EG')>EG</option>
                                        <option value="OG"
                                            @selected(optional($alternative)->installation_location == 'OG')>OG</option>
                                        <option value="DG"
                                            @selected(optional($alternative)->installation_location == 'DG')>DG</option>
                                        <option value="SONSTIGES"
                                            @selected(optional($alternative)->installation_location == 'SONSTIGES')>
                                            Sonstiges</option>
                                    </select>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Ist ein Kamin vorhanden?">
                                        Kamin vorhanden?
                                    </label>
                                    <select class="fw-select control-field count-me" name="fireplace">
                                        <option value="" @selected(optional($alternative)->fireplace == '')>Bitte wählen
                                        </option>
                                        <option value="1" @selected(optional($alternative)->fireplace == '1')>Ja</option>
                                        <option value="0" @selected(optional($alternative)->fireplace == '0')>Nein
                                        </option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Berechnete Heizlast in kW.">
                                        Heizlast
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" class="fw-input control-field count-me"
                                            name="heating_load_calculation"
                                            value="{{ old('heating_load_calculation', optional($alternative)->heating_load_calculation) }}">
                                        <span class="fw-addon">kW</span>
                                    </div>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Besonderheiten der aktuellen Heizung.">
                                    Besonderheiten vorhanden
                                </label>
                                <input type="text" class="fw-input control-field count-me" name="heating_notes"
                                    value="{{ old('heating_notes', optional($alternative)->heating_notes) }}"
                                    placeholder="z.B. Defekte, Geräusche, Platzproblem, alte Steuerung">
                            </div>
                        </div>
                    </section>

                    {{-- VERTEILUNG & ROHRE --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge">
                                <i class="feather icon-git-branch"></i>
                                Verteilung & Leitungen
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Art der Wärmeübergabe in den Räumen.">
                                    Wärmeübergabe
                                </label>
                                <select name="heating_type" class="fw-select control-field count-me">
                                    <option value="" @selected(optional($alternative)->heating_type == '')>Bitte wählen
                                    </option>
                                    <option value="underfloor_heating"
                                        @selected(optional($alternative)->heating_type == 'underfloor_heating')>
                                        Fußbodenheizung</option>
                                    <option value="radiators"
                                        @selected(optional($alternative)->heating_type == 'radiators')>Heizkörper</option>
                                    <option value="both" @selected(optional($alternative)->heating_type == 'both')>Beides
                                    </option>
                                    <option value="none" @selected(optional($alternative)->heating_type == 'none')>Keine
                                    </option>
                                </select>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Anzahl der Heizkreise.">
                                        Heizkreise
                                    </label>
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="heating_circuits_count"
                                        value="{{ old('heating_circuits_count', optional($alternative)->heating_circuits_count) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Ein-Rohr-System vorhanden?">
                                        Ein-Rohr-System
                                    </label>
                                    <select name="note_einRohr" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->note_einRohr == '')>Bitte
                                            wählen</option>
                                        <option value="Ja" @selected(optional($alternative)->note_einRohr == 'Ja')>Ja
                                        </option>
                                        <option value="Nein" @selected(optional($alternative)->note_einRohr == 'Nein')>
                                            Nein</option>
                                    </select>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Material der Heizungsleitungen.">
                                        Heizung Material
                                    </label>
                                    <select name="pipe_system_material" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->pipe_system_material == '')>
                                            Bitte wählen</option>
                                        <option value="Kupfer"
                                            @selected(optional($alternative)->pipe_system_material == 'Kupfer')>Kupfer
                                        </option>
                                        <option value="Kunststoff"
                                            @selected(optional($alternative)->pipe_system_material == 'Kunststoff')>
                                            Kunststoff</option>
                                        <option value="Stahl"
                                            @selected(optional($alternative)->pipe_system_material == 'Stahl')>Stahl
                                        </option>
                                        <option value="Mehrschichtverbund"
                                            @selected(optional($alternative)->pipe_system_material == 'Mehrschichtverbund')>
                                            Mehrschichtverbund</option>
                                        <option value="Sonstiges"
                                            @selected(optional($alternative)->pipe_system_material == 'Sonstiges')>
                                            Sonstiges</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Anzahl der Rohrsysteme / Stränge.">
                                        Rohranzahl
                                    </label>
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="pipe_system_count"
                                        value="{{ old('pipe_system_count', optional($alternative)->pipe_system_count) }}">
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Dimension der Heizungsleitung.">
                                        Heizung Dimension
                                    </label>
                                    <input type="text" class="fw-input control-field count-me"
                                        name="heating_pipe_dimension"
                                        value="{{ old('heating_pipe_dimension', optional($alternative)->heating_pipe_dimension) }}"
                                        placeholder="z.B. 22 mm">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Dimension Kaltwasser / Warmwasser.">
                                        KW / WW Dimension
                                    </label>
                                    <input type="text" class="fw-input control-field count-me"
                                        name="water_pipe_dimension"
                                        value="{{ old('water_pipe_dimension', optional($alternative)->water_pipe_dimension) }}"
                                        placeholder="z.B. 18 mm">
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Dimension Zirkulationsleitung.">
                                    Zirkulation Dimension
                                </label>
                                <input type="text" class="fw-input control-field count-me"
                                    name="circulation_pipe_dimension"
                                    value="{{ old('circulation_pipe_dimension', optional($alternative)->circulation_pipe_dimension) }}"
                                    placeholder="z.B. 15 mm">
                            </div>
                        </div>
                    </section>

                    {{-- HEIZKREISE & ETAGEN --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-emerald">
                                <i class="feather icon-layers"></i>
                                Heizkreise & Etagen
                            </span>
                        </div>

                        <div class="fw-fields">

                            {{-- KG --}}
                            <div class="fw-note-box">
                                <div class="fw-section-head" style="margin-bottom:10px;">
                                    <span class="fw-badge fw-badge-blue">Kellergeschoss (KG)</span>
                                </div>

                                <div class="fw-grid-2-inner" style="grid-template-columns:1fr 1fr 1fr;">
                                    <div class="fw-field">
                                        <label class="fw-label">Status</label>
                                        <select name="note_kgHeiz" class="fw-select control-field count-me">
                                            <option value="" @selected(optional($alternative)->note_kgHeiz == '')>Bitte
                                                wählen</option>
                                            <option value="beheizt"
                                                @selected(optional($alternative)->note_kgHeiz == 'beheizt')>Beheizt
                                            </option>
                                            <option value="nicht beheizt"
                                                @selected(optional($alternative)->note_kgHeiz == 'nicht beheizt')>Nicht
                                                beheizt</option>
                                        </select>
                                    </div>

                                    <label class="fw-check">
                                        <input type="checkbox" name="note_kgFbh" value="1"
                                            @checked(optional($alternative)->note_kgFbh == '1')>
                                        <span>Fußbodenheizung</span>
                                    </label>

                                    <label class="fw-check">
                                        <input type="checkbox" name="note_kgHk" value="1"
                                            @checked(optional($alternative)->note_kgHk == '1')>
                                        <span>Heizkörper</span>
                                    </label>
                                </div>
                            </div>

                            {{-- EG --}}
                            <div class="fw-note-box">
                                <div class="fw-section-head" style="margin-bottom:10px;">
                                    <span class="fw-badge fw-badge-blue">Erdgeschoss (EG)</span>
                                </div>

                                <div class="fw-grid-2-inner" style="grid-template-columns:1fr 1fr 1fr;">
                                    <div class="fw-field">
                                        <label class="fw-label">Status</label>
                                        <select name="note_egHeiz" class="fw-select control-field count-me">
                                            <option value="" @selected(optional($alternative)->note_egHeiz == '')>Bitte
                                                wählen</option>
                                            <option value="beheizt"
                                                @selected(optional($alternative)->note_egHeiz == 'beheizt')>Beheizt
                                            </option>
                                            <option value="nicht beheizt"
                                                @selected(optional($alternative)->note_egHeiz == 'nicht beheizt')>Nicht
                                                beheizt</option>
                                        </select>
                                    </div>

                                    <label class="fw-check">
                                        <input type="checkbox" name="note_egFbh" value="1"
                                            @checked(optional($alternative)->note_egFbh == '1')>
                                        <span>Fußbodenheizung</span>
                                    </label>

                                    <label class="fw-check">
                                        <input type="checkbox" name="note_egHk" value="1"
                                            @checked(optional($alternative)->note_egHk == '1')>
                                        <span>Heizkörper</span>
                                    </label>
                                </div>
                            </div>

                            {{-- OG --}}
                            <div class="fw-note-box">
                                <div class="fw-section-head" style="margin-bottom:10px;">
                                    <span class="fw-badge fw-badge-blue">Obergeschoss (OG)</span>
                                </div>

                                <div class="fw-grid-2-inner" style="grid-template-columns:1fr 1fr 1fr;">
                                    <div class="fw-field">
                                        <label class="fw-label">Status</label>
                                        <select name="note_ogHeiz" class="fw-select control-field count-me">
                                            <option value="" @selected(optional($alternative)->note_ogHeiz == '')>Bitte
                                                wählen</option>
                                            <option value="beheizt"
                                                @selected(optional($alternative)->note_ogHeiz == 'beheizt')>Beheizt
                                            </option>
                                            <option value="nicht beheizt"
                                                @selected(optional($alternative)->note_ogHeiz == 'nicht beheizt')>Nicht
                                                beheizt</option>
                                        </select>
                                    </div>

                                    <label class="fw-check">
                                        <input type="checkbox" name="note_ogFbh" value="1"
                                            @checked(optional($alternative)->note_ogFbh == '1')>
                                        <span>Fußbodenheizung</span>
                                    </label>

                                    <label class="fw-check">
                                        <input type="checkbox" name="note_ogHk" value="1"
                                            @checked(optional($alternative)->note_ogHk == '1')>
                                        <span>Heizkörper</span>
                                    </label>
                                </div>
                            </div>

                            {{-- DG --}}
                            <div class="fw-note-box">
                                <div class="fw-section-head" style="margin-bottom:10px;">
                                    <span class="fw-badge fw-badge-blue">Dachgeschoss (DG)</span>
                                </div>

                                <div class="fw-grid-2-inner" style="grid-template-columns:1fr 1fr 1fr;">
                                    <div class="fw-field">
                                        <label class="fw-label">Status</label>
                                        <select name="note_dgHeiz" class="fw-select control-field count-me">
                                            <option value="" @selected(optional($alternative)->note_dgHeiz == '')>Bitte
                                                wählen</option>
                                            <option value="beheizt"
                                                @selected(optional($alternative)->note_dgHeiz == 'beheizt')>Beheizt
                                            </option>
                                            <option value="nicht beheizt"
                                                @selected(optional($alternative)->note_dgHeiz == 'nicht beheizt')>Nicht
                                                beheizt</option>
                                        </select>
                                    </div>

                                    <label class="fw-check">
                                        <input type="checkbox" name="note_dgFbh" value="1"
                                            @checked(optional($alternative)->note_dgFbh == '1')>
                                        <span>Fußbodenheizung</span>
                                    </label>

                                    <label class="fw-check">
                                        <input type="checkbox" name="note_dgHk" value="1"
                                            @checked(optional($alternative)->note_dgHk == '1')>
                                        <span>Heizkörper</span>
                                    </label>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Vorlauftemperatur Heizkreis 1.">
                                        Heizkreis 1 Vorlauf
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" name="flow_temperature"
                                            class="fw-input control-field count-me"
                                            value="{{ old('flow_temperature', optional($alternative)->flow_temperature) }}">
                                        <span class="fw-addon">°C</span>
                                    </div>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Vorlauftemperatur Heizkreis 2.">
                                        Heizkreis 2 Vorlauf
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" name="note_flow_temperature_2"
                                            class="fw-input control-field count-me"
                                            value="{{ old('note_flow_temperature_2', optional($alternative)->note_flow_temperature_2) }}">
                                        <span class="fw-addon">°C</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="fw-col">

                    {{-- ENERGIEBEDARF --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-emerald">
                                <i class="feather icon-zap"></i>
                                Energiebedarf
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Jährlicher Gesamtwärmebedarf.">
                                        Wärmebedarf p.a.
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" class="fw-input control-field count-me"
                                            name="total_heat_consumption"
                                            value="{{ old('total_heat_consumption', optional($alternative)->total_heat_consumption) }}">
                                        <span class="fw-addon">kWh</span>
                                    </div>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Jährlicher Strombedarf.">
                                        Strombedarf p.a.
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" class="fw-input control-field count-me"
                                            name="total_electricity_consumption"
                                            value="{{ old('total_electricity_consumption', optional($alternative)->total_electricity_consumption) }}">
                                        <span class="fw-addon">kWh</span>
                                    </div>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner" style="grid-template-columns:1fr 1fr 1fr;">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Verbrauch aktuelle Heizung.">
                                        Verbrauch
                                    </label>
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="consumption"
                                        value="{{ old('consumption', optional($alternative)->consumption) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Menge / Tankgröße / Fassungsvermögen.">
                                        Menge
                                    </label>
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="quantity" value="{{ old('quantity', optional($alternative)->quantity) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Jährlicher Holzverbrauch.">
                                        Holz p.a.
                                    </label>
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="wood_consumption"
                                        value="{{ old('wood_consumption', optional($alternative)->wood_consumption) }}">
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- SANITÄR & WARMWASSER --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-blue">
                                <i class="feather icon-droplet"></i>
                                Sanitär & Warmwasser
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Art der Warmwasserbereitung.">
                                        Warmwasser Aufbereitung
                                    </label>
                                    <select name="hot_water_generation" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->hot_water_generation == '')>
                                            Bitte wählen</option>
                                        <option value="direkt"
                                            @selected(optional($alternative)->hot_water_generation == 'direkt')>Direkt
                                        </option>
                                        <option value="indirekt"
                                            @selected(optional($alternative)->hot_water_generation == 'indirekt')>Indirekt
                                        </option>
                                        <option value="zentral"
                                            @selected(optional($alternative)->hot_water_generation == 'zentral')>Zentral
                                        </option>
                                        <option value="dezentral"
                                            @selected(optional($alternative)->hot_water_generation == 'dezentral')>
                                            Dezentral</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Speichervolumen in Liter.">
                                        Fassungsvermögen
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" class="fw-input control-field count-me"
                                            name="hot_water_tank_liters"
                                            value="{{ old('hot_water_tank_liters', optional($alternative)->hot_water_tank_liters) }}">
                                        <span class="fw-addon">Liter</span>
                                    </div>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Anzahl der Badezimmer im Haus.">
                                        Anzahl Bäder
                                    </label>
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="bathroom_count"
                                        value="{{ old('bathroom_count', optional($alternative)->bathroom_count) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Badewanne vorhanden?">
                                        Badewanne vorhanden?
                                    </label>
                                    <select name="note_bathtub" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->note_bathtub == '')>Bitte
                                            wählen</option>
                                        <option value="Nein" @selected(optional($alternative)->note_bathtub == 'Nein')>
                                            Nein</option>
                                        <option value="Ja" @selected(optional($alternative)->note_bathtub == 'Ja')>Ja
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Anzahl der Badewannen.">
                                        Badewannen Anzahl
                                    </label>
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="bathtub_count"
                                        value="{{ old('bathtub_count', optional($alternative)->bathtub_count) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Abmessung der Badewanne.">
                                        Badewanne Abmessung
                                    </label>
                                    <input type="text" class="fw-input control-field count-me" name="note_bathtubDim"
                                        value="{{ old('note_bathtubDim', optional($alternative)->note_bathtubDim) }}"
                                        placeholder="z.B. 180 x 80 cm">
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Schwimmbad vorhanden?">
                                        Schwimmbad vorhanden?
                                    </label>
                                    <select name="note_pool" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->note_pool == '')>Bitte wählen
                                        </option>
                                        <option value="Nein" @selected(optional($alternative)->note_pool == 'Nein')>Nein
                                        </option>
                                        <option value="Ja" @selected(optional($alternative)->note_pool == 'Ja')>Ja
                                        </option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Volumen des Schwimmbads.">
                                        Pool Volumen
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" class="fw-input control-field count-me"
                                            name="note_poolVolume"
                                            value="{{ old('note_poolVolume', optional($alternative)->note_poolVolume) }}">
                                        <span class="fw-addon">m³</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- SOLARTHERMIE --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge" style="background:#eef2ff;border-color:#c7d2fe;color:#4338ca;">
                                <i class="feather icon-sun"></i>
                                Thermische Solaranlage
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Ist Solarthermie vorhanden?">
                                        Vorhanden?
                                    </label>
                                    <select name="solar_thermal" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->solar_thermal == '')>Bitte
                                            wählen</option>
                                        <option value="1" @selected(optional($alternative)->solar_thermal == '1')>Ja
                                        </option>
                                        <option value="0" @selected(optional($alternative)->solar_thermal == '0')>Nein
                                        </option>
                                        <option value="Ja" @selected(optional($alternative)->solar_thermal == 'Ja')>Ja
                                        </option>
                                        <option value="Nein" @selected(optional($alternative)->solar_thermal == 'Nein')>
                                            Nein</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Module oder Fläche der Solarthermie.">
                                        Module / Fläche
                                    </label>
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="solar_thermal_area"
                                        value="{{ old('solar_thermal_area', optional($alternative)->solar_thermal_area) }}">
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- FUSSBODENHEIZUNG / HKV --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-emerald">
                                <i class="feather icon-sliders"></i>
                                FBH / HKV Zustand
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Sind die Regler für Kühlung geeignet?">
                                    Regler für Kühlung geeignet?
                                </label>
                                <select name="note_reglerKuehlung" class="fw-select control-field count-me">
                                    <option value="" @selected(optional($alternative)->note_reglerKuehlung == '')>Bitte
                                        wählen</option>
                                    <option value="Ja" @selected(optional($alternative)->note_reglerKuehlung == 'Ja')>Ja
                                    </option>
                                    <option value="Nein" @selected(optional($alternative)->note_reglerKuehlung == 'Nein')>
                                        Nein</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Ist der Heizkreisverteiler für hydraulischen Abgleich geeignet?">
                                    HKV für hydr. Abgleich geeignet?
                                </label>
                                <select name="note_hkvAbgleich" class="fw-select control-field count-me">
                                    <option value="" @selected(optional($alternative)->note_hkvAbgleich == '')>Bitte
                                        wählen</option>
                                    <option value="Ja" @selected(optional($alternative)->note_hkvAbgleich == 'Ja')>Ja
                                    </option>
                                    <option value="Nein" @selected(optional($alternative)->note_hkvAbgleich == 'Nein')>
                                        Nein</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Sind die Stellantriebe für hydraulischen Abgleich geeignet?">
                                    Stellantriebe geeignet?
                                </label>
                                <select name="note_stellantriebAbgleich" class="fw-select control-field count-me">
                                    <option value="" @selected(optional($alternative)->note_stellantriebAbgleich == '')>
                                        Bitte wählen</option>
                                    <option value="Ja"
                                        @selected(optional($alternative)->note_stellantriebAbgleich == 'Ja')>Ja</option>
                                    <option value="Nein"
                                        @selected(optional($alternative)->note_stellantriebAbgleich == 'Nein')>Nein
                                    </option>
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- STANDORT & FÖRDERUNG --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge" style="background:#eef2ff;border-color:#c7d2fe;color:#4338ca;">
                                <i class="feather icon-map-pin"></i>
                                Standort & Förderung
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Falls Aufstellort sonstiges gewählt wurde.">
                                    Aufstellort Zusatz
                                </label>
                                <input type="text" class="fw-input control-field count-me" placeholder="Sonstiges..."
                                    name="installation_location_extra"
                                    value="{{ old('installation_location_extra', optional($alternative)->installation_location_extra) }}">
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Grobe Einkommensstufe für Förderrelevanz.">
                                    Einkommen
                                </label>
                                <select name="income_level" class="fw-select control-field count-me">
                                    <option value="" @selected(optional($alternative)->income_level == '')>Bitte wählen
                                    </option>
                                    <option value="0" @selected(optional($alternative)->income_level == '0')>Über 40.000 €
                                    </option>
                                    <option value="1" @selected(optional($alternative)->income_level == '1')>Unter 40.000
                                        €</option>
                                    <option value="2" @selected(optional($alternative)->income_level == '2')>Unbekannt
                                    </option>
                                </select>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            {{-- FULL WIDTH --}}
            <div class="fw-full">
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge">
                            <i class="feather icon-file-text"></i>
                            Sonstiges
                        </span>
                    </div>

                    <div class="fw-field">
                        <label class="fw-label has-tooltip" data-tooltip="Zusätzliche Notizen zur Heizung.">
                            Bemerkung
                        </label>
                        <textarea name="heating_remark" class="fw-textarea control-field count-me" rows="3"
                            placeholder="Besonderheiten zur Heizung eintragen...">{{ old('heating_remark', optional($alternative)->heating_remark) }}</textarea>
                    </div>
                </section>
            </div>
        </div>

        <div class="fw-footer">
            <button type="button" onclick="window.goToStep(3)" class="fw-btn fw-btn-secondary">
                <i class="feather icon-arrow-left"></i>
                Zurück
            </button>

            <button type="submit" class="fw-btn fw-btn-primary" onclick="setTimeout(goNext, 500)">
                Speichern & Weiter
                <i class="feather icon-arrow-right"></i>
            </button>
        </div>
    </div>
</form>

