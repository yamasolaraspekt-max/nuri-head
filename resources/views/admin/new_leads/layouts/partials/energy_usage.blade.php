<form class="partial-form" data-section="energy_usage" data-id="{{ $alternative->id }}">
    @csrf

    <div class="fw-shell"> 

        <div class="fw-body">
            <div class="fw-grid-2">

                {{-- LEFT COLUMN --}}
                <div class="fw-col">

                    {{-- STROMVERBRAUCH --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-orange">
                                <i class="feather icon-activity"></i>
                                Stromverbrauch
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Normaler Stromverbrauch im Haus. Erwartet: Zahl (kWh)">
                                    Haushaltsstrom
                                </label>
                                <div class="fw-input-group">
                                    <input type="text"
                                           class="fw-input control-field count-me power-calc-input"
                                           id="power_household_input"
                                           name="power_household"
                                           value="{{ old('power_household', optional($alternative)->power_household) }}">
                                    <span class="fw-addon">kWh</span>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Stromverbrauch der Wärmepumpe. Erwartet: Zahl (kWh)">
                                    WP-Strom
                                </label>
                                <div class="fw-input-group">
                                    <input type="text"
                                           class="fw-input control-field count-me power-calc-input"
                                           id="power_heatpump_input"
                                           name="power_heatpump"
                                           value="{{ old('power_heatpump', optional($alternative)->power_heatpump) }}">
                                    <span class="fw-addon">kWh</span>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Strombedarf für E-Autos. Erwartet: Zahl (kWh)">
                                    E-Auto-Strom
                                </label>
                                <div class="fw-input-group">
                                    <input type="text"
                                           class="fw-input control-field count-me power-calc-input"
                                           id="power_electric_car_input"
                                           name="power_electric_car"
                                           value="{{ old('power_electric_car', optional($alternative)->power_electric_car) }}">
                                    <span class="fw-addon">kWh</span>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Weitere Stromverbraucher, z.B. Klima, Sauna. Erwartet: Zahl (kWh)">
                                    Sonstiges
                                </label>
                                <div class="fw-input-group">
                                    <input type="text"
                                           class="fw-input control-field count-me power-calc-input"
                                           id="power_other_input"
                                           name="power_other"
                                           value="{{ old('power_other', optional($alternative)->power_other) }}">
                                    <span class="fw-addon">kWh</span>
                                </div>
                            </div>

                            <div class="fw-note-box" style="background:#eff6ff;border-color:#bfdbfe;">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                           data-tooltip="Summe aller Verbräuche. Wird automatisch berechnet."
                                           style="color:#1d4ed8;font-weight:800;">
                                        Gesamtverbrauch
                                    </label>

                                    <input type="hidden"
                                           name="power_total"
                                           id="power_total_hidden"
                                           value="{{ old('power_total', optional($alternative)->power_total) }}">

                                    <div class="fw-input-group">
                                        <input type="text"
                                               class="fw-input control-field count-me"
                                               id="power_total"
                                               value="{{ old('power_total', optional($alternative)->power_total) }}"
                                               readonly
                                               style="background:#ffffff;font-weight:800;">
                                        <span class="fw-addon">kWh</span>
                                    </div>

                                    <small id="power_total_year"
                                           style="color:#64748b;display:block;margin-top:6px;font-weight:600;"></small>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- TECHNIK AUFSTELLORT --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-blue">
                                <i class="feather icon-map-pin"></i>
                                Technik & Konnektivität
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Wo Wechselrichter / Speicher aufgestellt werden. Erwartet: Auswahl">
                                    Aufstellungsort Technik
                                </label>
                                <select name="installation_location_power" class="fw-select control-field count-me">
                                    <option value="" @selected(optional($alternative)->installation_location_power == '')>Bitte wählen</option>
                                    <option value="KG" @selected(optional($alternative)->installation_location_power == 'KG')>Keller (KG)</option>
                                    <option value="EG" @selected(optional($alternative)->installation_location_power == 'EG')>Erdgeschoss (EG)</option>
                                    <option value="OG" @selected(optional($alternative)->installation_location_power == 'OG')>Obergeschoss (OG)</option>
                                    <option value="DG" @selected(optional($alternative)->installation_location_power == 'DG')>Dachgeschoss (DG)</option>
                                    <option value="garage" @selected(optional($alternative)->installation_location_power == 'garage')>Garage</option>
                                    <option value="sonstiges" @selected(optional($alternative)->installation_location_power == 'sonstiges')>Sonstiges</option>
                                </select>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Internet-Anbindung für Wechselrichter, Speicher, Wallbox oder Wärmepumpe.">
                                        Netzwerk / WLAN
                                    </label>
                                    <select name="network_wlan" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->network_wlan == '')>Bitte wählen</option>
                                        <option value="vorhanden" @selected(optional($alternative)->network_wlan == 'vorhanden')>Vorhanden</option>
                                        <option value="nicht-vorhanden" @selected(optional($alternative)->network_wlan == 'nicht-vorhanden')>Nicht vorhanden</option>
                                        <option value="geplant" @selected(optional($alternative)->network_wlan == 'geplant')>Geplant</option>
                                        <option value="WLAN" @selected(optional($alternative)->network_wlan == 'WLAN')>WLAN</option>
                                        <option value="LAN" @selected(optional($alternative)->network_wlan == 'LAN')>LAN</option>
                                        <option value="Powerline" @selected(optional($alternative)->network_wlan == 'Powerline')>Powerline</option>
                                        <option value="Dongle" @selected(optional($alternative)->network_wlan == 'Dongle')>Dongle</option>
                                        <option value="Ja" @selected(optional($alternative)->network_wlan == 'Ja')>Ja</option>
                                        <option value="Nein" @selected(optional($alternative)->network_wlan == 'Nein')>Nein</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="SG Ready / EnWG 14a Vorbereitung vorhanden?">
                                        SG Ready / EnWG 14a
                                    </label>
                                    <select name="enwg_14a_ready" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->enwg_14a_ready == '')>Bitte wählen</option>
                                        <option value="1" @selected(optional($alternative)->enwg_14a_ready == '1')>Ja</option>
                                        <option value="0" @selected(optional($alternative)->enwg_14a_ready == '0')>Nein</option>
                                    </select>
                                </div>
                            </div>

                            <label class="fw-check">
                                <input type="checkbox"
                                       name="note_internetSteckdose"
                                       value="1"
                                       class="control-field count-me"
                                       @checked(old('note_internetSteckdose', optional($alternative)->note_internetSteckdose) == '1')>
                                <span>Steckdose für Internet / Technik setzen</span>
                            </label>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Entfernung zur nächsten Steckdose. Erwartet: Text oder Zahl, z.B. 2m">
                                    Entfernung zur nächsten Steckdose
                                </label>
                                <input type="text"
                                       class="fw-input control-field count-me"
                                       name="note_internetSteckdoseDist"
                                       value="{{ old('note_internetSteckdoseDist', optional($alternative)->note_internetSteckdoseDist) }}"
                                       placeholder="z.B. 2m">
                            </div>
                        </div>
                    </section>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="fw-col">

                    {{-- ZÄHLERSCHRANK --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-emerald">
                                <i class="feather icon-grid"></i>
                                Zählerschrank & Elektrik
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Zustand, Bauart oder Ort des Zählerschranks. Erwartet: Freitext">
                                    Zählerschrank
                                </label>
                                <input type="text"
                                       class="fw-input control-field count-me"
                                       name="meter_cabinet"
                                       value="{{ old('meter_cabinet', optional($alternative)->meter_cabinet) }}">
                            </div>

                            <div class="fw-grid-2-inner">
                                <label class="fw-check">
                                    <input type="checkbox"
                                           name="ac_surge_protection"
                                           value="1"
                                           class="control-field count-me"
                                           @checked(old('ac_surge_protection', optional($alternative)->ac_surge_protection) == '1')>
                                    <span>AC-Überspannungsschutz vorhanden</span>
                                </label>

                                <label class="fw-check">
                                    <input type="checkbox"
                                           name="sls_switch"
                                           value="1"
                                           class="control-field count-me"
                                           @checked(old('sls_switch', optional($alternative)->sls_switch) == '1')>
                                    <span>SLS Schalter vorhanden</span>
                                </label>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Anzahl der verbauten Stromzähler. Erwartet: Zahl">
                                        Anzahl Zähler
                                    </label>
                                    <input type="number"
                                           class="fw-input control-field count-me"
                                           name="meter_count"
                                           value="{{ old('meter_count', optional($alternative)->meter_count) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Anzahl der abgerechneten Wohneinheiten. Erwartet: Zahl">
                                        Wohneinheiten
                                    </label>
                                    <input type="number"
                                           class="fw-input control-field count-me"
                                           name="number_we"
                                           id="number_we"
                                           value="{{ old('number_we', optional($alternative)->number_we) }}">
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Was muss mit dem Zählerschrank passieren?">
                                    Zählerschrank Aktion
                                </label>
                                <select name="meter_cabinet_action" class="fw-select control-field count-me">
                                    <option value="" @selected(optional($alternative)->meter_cabinet_action == '')>Bitte wählen</option>
                                    <option value="neuer Zählerschrank notwendig" @selected(optional($alternative)->meter_cabinet_action == 'neuer Zählerschrank notwendig')>Neuer Zählerschrank notwendig</option>
                                    <option value="alter Zählerschrank wird zur Unterverteilung" @selected(optional($alternative)->meter_cabinet_action == 'alter Zählerschrank wird zur Unterverteilung')>Alter Zählerschrank wird zur Unterverteilung</option>
                                    <option value="zusätzliche Unterverteilung" @selected(optional($alternative)->meter_cabinet_action == 'zusätzliche Unterverteilung')>Zusätzliche Unterverteilung</option>
                                </select>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Größe des neuen Zählerschranks.">
                                        Neuer ZS Größe
                                    </label>
                                    <select name="cabinet_size" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->cabinet_size == '')>Bitte wählen</option>
                                        <option value="550" @selected(optional($alternative)->cabinet_size == '550')>550</option>
                                        <option value="800" @selected(optional($alternative)->cabinet_size == '800')>800</option>
                                        <option value="1100" @selected(optional($alternative)->cabinet_size == '1100')>1100</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Ist ein Mieterstrommodell gewünscht?">
                                        Mieterstrommodell
                                    </label>
                                    <select name="tenant_model" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->tenant_model == '')>Bitte wählen</option>
                                        <option value="1" @selected(optional($alternative)->tenant_model == '1')>Ja</option>
                                        <option value="0" @selected(optional($alternative)->tenant_model == '0')>Nein</option>
                                        <option value="individuell" @selected(optional($alternative)->tenant_model == 'individuell')>Individuell</option>
                                        <option value="zentral" @selected(optional($alternative)->tenant_model == 'zentral')>Zentral</option>
                                        <option value="nicht-vorhanden" @selected(optional($alternative)->tenant_model == 'nicht-vorhanden')>Nicht vorhanden</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- ZWISCHENZÄHLER --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-blue">
                                <i class="feather icon-layers"></i>
                                Zwischenzähler
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Ist ein Zwischenzähler gewünscht?">
                                        Gewünscht?
                                    </label>
                                    <select name="note_zwischenzaehler" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->note_zwischenzaehler == '')>Bitte wählen</option>
                                        <option value="Ja" @selected(optional($alternative)->note_zwischenzaehler == 'Ja')>Ja</option>
                                        <option value="Nein" @selected(optional($alternative)->note_zwischenzaehler == 'Nein')>Nein</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Anzahl der Zwischenzähler. Erwartet: Zahl">
                                        Anzahl Zwischenzähler
                                    </label>
                                    <input type="number"
                                           class="fw-input control-field count-me"
                                           name="note_zwischenzaehler_count"
                                           value="{{ old('note_zwischenzaehler_count', optional($alternative)->note_zwischenzaehler_count) }}">
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Zwischenzähler für Wärmepumpe?">
                                        Für Wärmepumpe?
                                    </label>
                                    <select name="note_zwischenzaehlerWp" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->note_zwischenzaehlerWp == '')>Bitte wählen</option>
                                        <option value="Ja" @selected(optional($alternative)->note_zwischenzaehlerWp == 'Ja')>Ja</option>
                                        <option value="Nein" @selected(optional($alternative)->note_zwischenzaehlerWp == 'Nein')>Nein</option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Anzahl Zwischenzähler für Wärmepumpe. Erwartet: Zahl">
                                        WP Anzahl
                                    </label>
                                    <input type="number"
                                           class="fw-input control-field count-me"
                                           name="note_zwischenzaehlerWpCount"
                                           value="{{ old('note_zwischenzaehlerWpCount', optional($alternative)->note_zwischenzaehlerWpCount) }}">
                                </div>
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
                            Bemerkung
                        </span>
                    </div>

                    <div class="fw-field">
                        <label class="fw-label has-tooltip" data-tooltip="Zusätzliche Bemerkungen zum Strom, Speicher, Verbrauch oder Zählerschrank. Erwartet: Freitext">
                            Bemerkung
                        </label>
                        <textarea name="energy_remark"
                                  class="fw-textarea control-field count-me"
                                  rows="3"
                                  placeholder="Notizen zur Energie und Elektrik eintragen...">{{ old('energy_remark', optional($alternative)->energy_remark) }}</textarea>
                    </div>
                </section>
            </div>
        </div>

        <div class="fw-footer">
            <button type="button" onclick="window.goToStep(5)" class="fw-btn fw-btn-secondary">
                <i class="feather icon-arrow-left"></i>
                Zurück
            </button>

            <button type="submit" class="fw-btn fw-btn-primary" style="background:#059669;box-shadow:0 8px 18px rgba(5,150,105,.18);">
                Speichern & Abschließen
                <i class="feather icon-check"></i>
            </button>
        </div>
    </div>
</form>


@once
    <style>
        /*
         |--------------------------------------------------------------------------
         | FW Collapsible Cards
         |--------------------------------------------------------------------------
         | Works automatically for every .fw-section inside .partial-form.
         | No field names, submit logic, count-me classes or layout logic are changed.
         */
        .fw-collapse-toolbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            margin: 0 0 14px 0;
            flex-wrap: wrap;
        }

        .fw-collapse-action {
            border: 1px solid #dbe4ef;
            background: #ffffff;
            color: #334155;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 7px 18px rgba(15, 23, 42, .06);
            transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease, background .16s ease;
        }

        .fw-collapse-action:hover {
            transform: translateY(-1px);
            border-color: #b9c8d8;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .10);
            background: #f8fafc;
        }

        .fw-section.fw-collapsible-card {
            overflow: hidden;
            transition: box-shadow .18s ease, border-color .18s ease, transform .18s ease;
        }

        .fw-section.fw-collapsible-card > .fw-section-head {
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .fw-section.fw-collapsible-card > .fw-section-head .fw-badge {
            min-width: 0;
        }

        .fw-collapse-toggle {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            border-radius: 999px;
            border: 1px solid #dbe4ef;
            background: #ffffff;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(15, 23, 42, .07);
            transition: transform .18s ease, background .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .fw-collapse-toggle:hover {
            background: #f8fafc;
            border-color: #b9c8d8;
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(15, 23, 42, .10);
        }

        .fw-collapse-toggle i {
            width: 16px;
            height: 16px;
            transition: transform .2s ease;
        }

        .fw-section.fw-collapsible-card.is-collapsed {
            box-shadow: 0 7px 18px rgba(15, 23, 42, .045);
        }

        .fw-section.fw-collapsible-card.is-collapsed > .fw-section-head {
            margin-bottom: 0;
        }

        .fw-section.fw-collapsible-card.is-collapsed > :not(.fw-section-head) {
            display: none !important;
        }

        .fw-section.fw-collapsible-card.is-collapsed .fw-collapse-toggle i {
            transform: rotate(-90deg);
        }

        @media (max-width: 640px) {
            .fw-collapse-toolbar {
                justify-content: stretch;
            }

            .fw-collapse-action {
                flex: 1 1 auto;
                justify-content: center;
            }

            .fw-section.fw-collapsible-card > .fw-section-head {
                align-items: flex-start;
            }
        }
    </style>

    <script>
        (function () {
            const STORAGE_KEY = 'fwCollapsibleCardsStateV2';

            function readState() {
                try {
                    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}') || {};
                } catch (e) {
                    return {};
                }
            }

            function writeState(state) {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                } catch (e) {
                    // localStorage can be blocked in some browsers; collapse still works for the current page.
                }
            }

            const state = readState();

            function refreshIcons() {
                if (window.feather && typeof window.feather.replace === 'function') {
                    window.feather.replace();
                }

                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            }

            function cardTitle(section) {
                const badge = section.querySelector(':scope > .fw-section-head .fw-badge');
                return (badge ? badge.textContent : 'card').replace(/\s+/g, ' ').trim();
            }

            function cardKey(section, index) {
                const form = section.closest('.partial-form');
                const sectionName = form?.dataset?.section || 'form';
                const formId = form?.dataset?.id || 'new';
                return `${sectionName}:${formId}:${index}:${cardTitle(section).toLowerCase()}`;
            }

            function setCollapsed(section, collapsed, persist = true) {
                const btn = section.querySelector(':scope > .fw-section-head .fw-collapse-toggle');

                section.classList.toggle('is-collapsed', collapsed);

                if (btn) {
                    btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    btn.setAttribute('title', collapsed ? 'Karte öffnen' : 'Karte einklappen');
                }

                if (persist && section.dataset.fwCollapseKey) {
                    state[section.dataset.fwCollapseKey] = collapsed ? 1 : 0;
                    writeState(state);
                }
            }

            function getCards(scope) {
                return Array.from(scope.querySelectorAll('.partial-form .fw-section.fw-collapsible-card'));
            }

            function addToolbar(shell) {
                if (shell.querySelector(':scope > .fw-collapse-toolbar')) {
                    return;
                }

                const body = shell.querySelector(':scope > .fw-body');
                if (!body) {
                    return;
                }

                const toolbar = document.createElement('div');
                toolbar.className = 'fw-collapse-toolbar';
                toolbar.innerHTML = `
                    <button type="button" class="fw-collapse-action" data-fw-collapse-action="open">
                        <i data-feather="maximize-2"></i>
                        Alle öffnen
                    </button>
                    <button type="button" class="fw-collapse-action" data-fw-collapse-action="close">
                        <i data-feather="minimize-2"></i>
                        Alle einklappen
                    </button>
                `;

                shell.insertBefore(toolbar, body);

                toolbar.addEventListener('click', function (event) {
                    const btn = event.target.closest('[data-fw-collapse-action]');
                    if (!btn) {
                        return;
                    }

                    const shouldClose = btn.dataset.fwCollapseAction === 'close';

                    getCards(shell).forEach(function (section) {
                        setCollapsed(section, shouldClose, true);
                    });

                    refreshIcons();
                });
            }

            function initFwCollapsibleCards(root = document) {
                root.querySelectorAll('.partial-form .fw-shell').forEach(addToolbar);

                root.querySelectorAll('.partial-form .fw-section').forEach(function (section, index) {
                    if (section.dataset.fwCollapsibleReady === '1') {
                        return;
                    }

                    const head = section.querySelector(':scope > .fw-section-head');
                    if (!head) {
                        return;
                    }

                    section.dataset.fwCollapsibleReady = '1';
                    section.classList.add('fw-collapsible-card');

                    const key = cardKey(section, index);
                    section.dataset.fwCollapseKey = key;

                    head.classList.add('fw-collapsible-head');
                    head.setAttribute('role', 'button');
                    head.setAttribute('tabindex', '0');
                    head.setAttribute('aria-label', cardTitle(section) + ' öffnen oder einklappen');

                    if (!head.querySelector(':scope > .fw-collapse-toggle')) {
                        const toggle = document.createElement('button');
                        toggle.type = 'button';
                        toggle.className = 'fw-collapse-toggle';
                        toggle.setAttribute('aria-expanded', 'true');
                        toggle.setAttribute('title', 'Karte einklappen');
                        toggle.innerHTML = '<i data-feather="chevron-down"></i>';
                        head.appendChild(toggle);
                    }

                    setCollapsed(section, state[key] === 1, false);

                    head.addEventListener('click', function (event) {
                        if (event.target.closest('a, button, input, select, textarea, label')) {
                            if (!event.target.closest('.fw-collapse-toggle')) {
                                return;
                            }
                        }

                        event.preventDefault();
                        setCollapsed(section, !section.classList.contains('is-collapsed'), true);
                        refreshIcons();
                    });

                    head.addEventListener('keydown', function (event) {
                        if (event.key !== 'Enter' && event.key !== ' ') {
                            return;
                        }

                        event.preventDefault();
                        setCollapsed(section, !section.classList.contains('is-collapsed'), true);
                        refreshIcons();
                    });
                });

                refreshIcons();
            }

            window.initFwCollapsibleCards = initFwCollapsibleCards;

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    initFwCollapsibleCards(document);
                });
            } else {
                initFwCollapsibleCards(document);
            }

            if (!window.__fwCollapsibleObserverReady) {
                window.__fwCollapsibleObserverReady = true;

                new MutationObserver(function (mutations) {
                    const shouldInit = mutations.some(function (mutation) {
                        return Array.from(mutation.addedNodes || []).some(function (node) {
                            return node.nodeType === 1 && (
                                node.matches?.('.partial-form, .fw-section, .fw-shell') ||
                                node.querySelector?.('.partial-form .fw-section, .partial-form .fw-shell')
                            );
                        });
                    });

                    if (shouldInit) {
                        initFwCollapsibleCards(document);
                    }
                }).observe(document.documentElement, {
                    childList: true,
                    subtree: true
                });
            }
        })();
    </script>
@endonce

