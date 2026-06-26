<form class="partial-form" data-section="e_mobility" data-id="{{ $alternative->id }}">
    @csrf

    <div class="fw-shell">

        <div class="fw-body">
            <div class="fw-grid-2">

                {{-- LEFT COLUMN --}}
                <div class="fw-col">

                    {{-- FAHRZEUGE --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-orange">
                                <i class="feather icon-truck"></i>
                                Fahrzeuge
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Gibt es ein E-Auto im Haushalt? Erwartet: Auswahl">
                                    Elektroauto
                                </label>
                                <select class="fw-select control-field count-me" name="electric_car"
                                    id="electric_car_select">
                                    <option value="" @selected(optional($alternative)->electric_car == '')>Bitte auswählen
                                    </option>
                                    <option value="Ja" @selected(optional($alternative)->electric_car == 'Ja')>Ja</option>
                                    <option value="Nein" @selected(optional($alternative)->electric_car == 'Nein')>Nein
                                    </option>
                                    <option value="Geplant" @selected(optional($alternative)->electric_car == 'Geplant')>
                                        Geplant</option>
                                </select>
                            </div>

                            <div class="fw-field" id="electric_car_count_group"
                                style="@if(optional($alternative)->electric_car == 'Ja' || optional($alternative)->electric_car == 'Geplant') display:flex; @else display:none; @endif flex-direction:column;">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Anzahl der Elektroautos. Erwartet: Zahl">
                                    Anzahl Autos
                                </label>
                                <input type="number" step="any" class="fw-input control-field count-me"
                                    name="electric_car_count"
                                    value="{{ old('electric_car_count', optional($alternative)->electric_car_count) }}">
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Jährliche Fahrleistung aller E-Autos in km. Erwartet: Dezimalzahl">
                                    Fahrleistung p.a.
                                </label>
                                <div class="fw-input-group">
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="car_kilo" value="{{ old('car_kilo', optional($alternative)->car_kilo) }}">
                                    <span class="fw-addon">km</span>
                                </div>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Wird das Fahrzeug geschäftlich genutzt? Erwartet: Auswahl">
                                        Firmenfahrzeug
                                    </label>
                                    <select class="fw-select control-field count-me" name="company_vehicle">
                                        <option value="" @selected(optional($alternative)->company_vehicle == '')>Bitte
                                            auswählen</option>
                                        <option value="1" @selected(optional($alternative)->company_vehicle == '1')>Ja
                                        </option>
                                        <option value="0" @selected(optional($alternative)->company_vehicle == '0')>Nein
                                        </option>
                                    </select>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Soll das Fahrzeug bidirektional laden können? Erwartet: Auswahl">
                                        Bidirektionales Laden
                                    </label>
                                    <select class="fw-select control-field count-me" name="bidirectional_car">
                                        <option value="" @selected(optional($alternative)->bidirectional_car == '')>Bitte
                                            auswählen</option>
                                        <option value="ja" @selected(optional($alternative)->bidirectional_car == 'ja')>Ja
                                        </option>
                                        <option value="nein"
                                            @selected(optional($alternative)->bidirectional_car == 'nein')>Nein</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- WALLBOX WUNSCH --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-emerald">
                                <i class="feather icon-zap"></i>
                                Wallbox Wunsch
                            </span>
                        </div>

                        <div class="fw-fields">
                            <label class="fw-check">
                                <input type="checkbox" name="wallbox_desired" value="1" class="control-field count-me"
                                    @checked(old('wallbox_desired', optional($alternative)->wallbox_desired) == '1')>
                                <span>Wallbox gewünscht</span>
                            </label>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Geplante oder vorhandene Ladepunkte. Erwartet: Zahl">
                                        Wallboxen
                                    </label>
                                    <input type="number" step="any" class="fw-input control-field count-me"
                                        name="wallbox_count"
                                        value="{{ old('wallbox_count', optional($alternative)->wallbox_count) }}">
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Wo die Wallbox installiert wird. Erwartet: Auswahl">
                                        Montageort
                                    </label>
                                    <select class="fw-select control-field count-me" name="wallbox_location">
                                        <option value="" @selected(optional($alternative)->wallbox_location == '')>Bitte
                                            auswählen</option>
                                        <option value="garage"
                                            @selected(optional($alternative)->wallbox_location == 'garage')>Garage
                                        </option>
                                        <option value="outside"
                                            @selected(optional($alternative)->wallbox_location == 'outside')>Draußen
                                        </option>
                                        <option value="Carport"
                                            @selected(optional($alternative)->wallbox_location == 'Carport')>Carport
                                        </option>
                                        <option value="Hauswand"
                                            @selected(optional($alternative)->wallbox_location == 'Hauswand')>Hauswand
                                        </option>
                                        <option value="Stellplatz"
                                            @selected(optional($alternative)->wallbox_location == 'Stellplatz')>Stellplatz
                                        </option>
                                        <option value="Sonstiges"
                                            @selected(optional($alternative)->wallbox_location == 'Sonstiges')>Sonstiges
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Freitext für genauen Aufstellort der Wallbox.">
                                    Aufstellort / Beschreibung
                                </label>
                                <input type="text" class="fw-input control-field count-me" name="wallbox_location_note"
                                    value="{{ old('wallbox_location_note', optional($alternative)->wallbox_location_note) }}"
                                    placeholder="z.B. Garage links, Außenwand, Carport hinten">
                            </div>
                        </div>
                    </section>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="fw-col">

                    {{-- TECHNISCHE VORAUSSETZUNGEN --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-blue">
                                <i class="feather icon-cpu"></i>
                                Technische Voraussetzungen
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Liegt bereits ein 400V Starkstromkabel am Montageort? Erwartet: Auswahl">
                                    Starkstromkabel
                                </label>
                                <select class="fw-select control-field count-me" name="heavy_current_cable">
                                    <option value="" @selected(optional($alternative)->heavy_current_cable == '')>Bitte
                                        auswählen</option>
                                    <option value="vorhanden"
                                        @selected(optional($alternative)->heavy_current_cable == 'vorhanden')>Vorhanden
                                    </option>
                                    <option value="nicht vorhanden"
                                        @selected(optional($alternative)->heavy_current_cable == 'nicht vorhanden')>Nicht
                                        vorhanden</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Gibt es ein LAN/Netzwerkkabel am Ladeort? Erwartet: Auswahl">
                                    Netzwerk / LAN
                                </label>
                                <select class="fw-select control-field count-me" name="network_cable">
                                    <option value="" @selected(optional($alternative)->network_cable == '')>Bitte
                                        auswählen</option>
                                    <option value="vorhanden"
                                        @selected(optional($alternative)->network_cable == 'vorhanden')>Vorhanden</option>
                                    <option value="nicht vorhanden"
                                        @selected(optional($alternative)->network_cable == 'nicht vorhanden')>Nicht
                                        vorhanden</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Internet-Anbindung für Wallbox oder PV-Komponenten.">
                                    Internet-Anbindung
                                </label>
                                <select name="network_wlan" class="fw-select control-field count-me">
                                    <option value="" @selected(optional($alternative)->network_wlan == '')>Bitte wählen
                                    </option>
                                    <option value="WLAN" @selected(optional($alternative)->network_wlan == 'WLAN')>WLAN
                                    </option>
                                    <option value="LAN" @selected(optional($alternative)->network_wlan == 'LAN')>LAN
                                    </option>
                                    <option value="Powerline"
                                        @selected(optional($alternative)->network_wlan == 'Powerline')>Powerline</option>
                                    <option value="Dongle" @selected(optional($alternative)->network_wlan == 'Dongle')>
                                        Dongle</option>
                                    <option value="Ja" @selected(optional($alternative)->network_wlan == 'Ja')>Ja</option>
                                    <option value="Nein" @selected(optional($alternative)->network_wlan == 'Nein')>Nein
                                    </option>
                                </select>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Entfernung von Wallbox bis Zählerschrank.">
                                        Entfernung zum ZS
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" class="fw-input control-field count-me"
                                            name="note_wallbox_distance"
                                            value="{{ old('note_wallbox_distance', optional($alternative)->note_wallbox_distance) }}">
                                        <span class="fw-addon">m</span>
                                    </div>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="SG Ready / EnWG 14a vorbereitet?">
                                        SG Ready / EnWG 14a
                                    </label>
                                    <select name="enwg_14a_ready" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->enwg_14a_ready == '')>Bitte
                                            wählen</option>
                                        <option value="1" @selected(optional($alternative)->enwg_14a_ready == '1')>Ja
                                        </option>
                                        <option value="0" @selected(optional($alternative)->enwg_14a_ready == '0')>Nein
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <label class="fw-check">
                                <input type="checkbox" name="note_wallboxKernbohrung" value="Ja"
                                    class="control-field count-me" @checked(old('note_wallboxKernbohrung', optional($alternative)->note_wallboxKernbohrung) == 'Ja')>
                                <span>Kernbohrung Außenwand / WU-Beton</span>
                            </label>
                        </div>
                    </section>

                    {{-- ERDARBEITEN --}}
                    <section class="fw-section">
                        <div class="fw-section-head">
                            <span class="fw-badge fw-badge-orange">
                                <i class="feather icon-shovel"></i>
                                Erdarbeiten
                            </span>
                        </div>

                        <div class="fw-fields">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Wer übernimmt eventuell anfallende Erdarbeiten? Erwartet: Auswahl">
                                    Erdarbeiten allgemein
                                </label>
                                <select class="fw-select control-field count-me" name="groundwork">
                                    <option value="" @selected(optional($alternative)->groundwork == '')>Bitte auswählen
                                    </option>
                                    <option value="bauseits" @selected(optional($alternative)->groundwork == 'bauseits')>
                                        Bauseits / Kunde</option>
                                    <option value="durch uns" @selected(optional($alternative)->groundwork == 'durch uns')>Durch uns</option>
                                    <option value="Solar Aspekt" @selected(optional($alternative)->groundwork == 'Solar Aspekt')>Solar Aspekt</option>
                                    <option value="Kunde" @selected(optional($alternative)->groundwork == 'Kunde')>Kunde
                                    </option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Sind für die Wallbox Erdarbeiten nötig?">
                                    Erdarbeiten nötig?
                                </label>
                                <select name="note_wbErdarbeiten" class="fw-select control-field count-me">
                                    <option value="" @selected(optional($alternative)->note_wbErdarbeiten == '')>Bitte
                                        wählen</option>
                                    <option value="Ja" @selected(optional($alternative)->note_wbErdarbeiten == 'Ja')>Ja
                                    </option>
                                    <option value="Nein" @selected(optional($alternative)->note_wbErdarbeiten == 'Nein')>
                                        Nein</option>
                                </select>
                            </div>

                            <div class="fw-grid-2-inner">
                                <div class="fw-field">
                                    <label class="fw-label has-tooltip" data-tooltip="Länge der Erdarbeiten in Metern.">
                                        Länge
                                    </label>
                                    <div class="fw-input-group">
                                        <input type="number" step="any" class="fw-input control-field count-me"
                                            name="note_wbErdarbeitenLaenge"
                                            value="{{ old('note_wbErdarbeitenLaenge', optional($alternative)->note_wbErdarbeitenLaenge) }}">
                                        <span class="fw-addon">m</span>
                                    </div>
                                </div>

                                <div class="fw-field">
                                    <label class="fw-label has-tooltip"
                                        data-tooltip="Wer macht die Erdarbeiten für die Wallbox?">
                                        Erdarbeiten durch
                                    </label>
                                    <select name="note_wbErdarbeitenDurch" class="fw-select control-field count-me">
                                        <option value="" @selected(optional($alternative)->note_wbErdarbeitenDurch == '')>
                                            Bitte wählen</option>
                                        <option value="Solar Aspekt"
                                            @selected(optional($alternative)->note_wbErdarbeitenDurch == 'Solar Aspekt')>
                                            Durch uns / Gala Bauer</option>
                                        <option value="Kunde"
                                            @selected(optional($alternative)->note_wbErdarbeitenDurch == 'Kunde')>Kunde /
                                            Gala-Bauer</option>
                                    </select>
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
                            Sonstiges
                        </span>
                    </div>

                    <div class="fw-grid-2">
                        <div class="fw-field">
                            <label class="fw-label has-tooltip" data-tooltip="Sonstige Wünsche zur Ladeinfrastruktur.">
                                Sonstige Kundenwünsche
                            </label>
                            <textarea name="note_sonstigeWunsche" class="fw-textarea control-field count-me" rows="3"
                                placeholder="Weitere Wünsche zur Wallbox oder E-Mobilität...">{{ old('note_sonstigeWunsche', optional($alternative)->note_sonstigeWunsche) }}</textarea>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label has-tooltip"
                                data-tooltip="Zusätzliche Notizen zur Elektromobilität. Erwartet: Freitext">
                                Bemerkung
                            </label>
                            <textarea name="car_remark" class="fw-textarea control-field count-me" rows="3"
                                placeholder="Besonderheiten eintragen...">{{ old('car_remark', optional($alternative)->car_remark) }}</textarea>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="fw-footer">
            <button type="button" onclick="window.goToStep(4)" class="fw-btn fw-btn-secondary">
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

        .fw-section.fw-collapsible-card>.fw-section-head {
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .fw-section.fw-collapsible-card>.fw-section-head .fw-badge {
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

        .fw-section.fw-collapsible-card.is-collapsed>.fw-section-head {
            margin-bottom: 0;
        }

        .fw-section.fw-collapsible-card.is-collapsed> :not(.fw-section-head) {
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

            .fw-section.fw-collapsible-card>.fw-section-head {
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