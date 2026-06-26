@php
    $r = $roof ?? null;

    $oldVal = function ($field, $fallback = '') use ($index, $r) {
        return old("roofs.$index.$field", optional($r)->{$field} ?? $fallback);
    };

    $checkedVal = function ($field) use ($oldVal) {
        return (string) $oldVal($field, '0') === '1';
    };
@endphp

<div class="fw-roof-card roof-group" data-roof-index="{{ $index }}">
    <style>
        .fw-roof-card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
            overflow: hidden;
        }

        .fw-roof-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
        }

        .fw-roof-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .fw-roof-toggle {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: .2s ease;
        }

        .fw-roof-card.is-collapsed .fw-roof-toggle {
            transform: rotate(-90deg);
        }

        .fw-roof-title-wrap {
            min-width: 0;
        }

        .fw-roof-title {
            margin: 0;
            font-size: 14px;
            font-weight: 900;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .fw-roof-subtitle {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
        }

        .fw-roof-meta {
            margin-top: 5px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .fw-roof-meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
        }

        .fw-roof-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .fw-icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid #fecaca;
            background: #fff1f2;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .2s ease;
        }

        .fw-icon-btn:hover {
            background: #fee2e2;
            transform: translateY(-1px);
        }

        .fw-roof-body {
            padding: 16px;
        }

        .fw-roof-card.is-collapsed .fw-roof-body {
            display: none;
        }

        .fw-roof-progress {
            min-width: 90px;
            text-align: right;
        }

        .fw-roof-progress-text {
            font-size: 11px;
            font-weight: 900;
            color: #64748b;
            margin-bottom: 5px;
        }

        .fw-roof-progress-track {
            width: 90px;
            height: 7px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .fw-roof-progress-fill {
            height: 100%;
            width: 0%;
            background: #74b2d4;
            transition: width .25s ease;
        }

        .fw-roof-card.is-complete .fw-roof-progress-fill {
            background: #10b981;
        }

        @media (max-width: 768px) {
            .fw-roof-header {
                align-items: flex-start;
            }

            .fw-roof-actions {
                flex-direction: column-reverse;
            }

            .fw-roof-progress {
                display: none;
            }
        }
    </style>

    <div class="fw-roof-header" onclick="toggleRoofCard(this)">
        <div class="fw-roof-header-left">
            <span class="fw-roof-toggle">
                <i class="feather icon-chevron-down toggle-icon"></i>
            </span>

            <div class="fw-roof-title-wrap">
                <h4 class="fw-roof-title">
                    {{ $index + 1 }}. Dachfläche
                    <span class="fw-roof-subtitle js-roof-title">
                        @if($oldVal('designation'))
                            — {{ $oldVal('designation') }}
                        @else
                            — Neue Dachfläche
                        @endif
                    </span>
                </h4>

                <div class="fw-roof-meta">
                    <span class="fw-roof-meta-badge js-roof-form">
                        <i class="feather icon-home"></i>
                        {{ $oldVal('roof_form') ?: 'Dachform offen' }}
                    </span>

                    <span class="fw-roof-meta-badge js-roof-orientation">
                        <i class="feather icon-compass"></i>
                        {{ $oldVal('roof_orientation') ?: 'Ausrichtung offen' }}
                    </span>

                    <span class="fw-roof-meta-badge js-roof-area">
                        <i class="feather icon-maximize"></i>
                        {{ $oldVal('roof_area') ? $oldVal('roof_area') . ' m²' : 'Fläche offen' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="fw-roof-actions">
            <div class="fw-roof-progress">
                <div class="fw-roof-progress-text js-roof-progress-text">0/0</div>
                <div class="fw-roof-progress-track">
                    <div class="fw-roof-progress-fill js-roof-progress-fill"></div>
                </div>
            </div>

            <button type="button" onclick="event.stopPropagation(); removeRoofCard(this);" title="Dach entfernen"
                class="fw-icon-btn">
                <i class="feather icon-trash-2"></i>
            </button>
        </div>
    </div>

    <div class="fw-roof-body">
        <input type="hidden" name="roofs[{{ $index }}][id]" value="{{ $r->id ?? '' }}">

        <div class="fw-grid-2">
            {{-- LEFT --}}
            <div class="fw-col">

                {{-- BASIS --}}
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge fw-badge-blue">
                            <i class="feather icon-info"></i>
                            Basis & Dachform
                        </span>
                    </div>

                    <div class="fw-fields">
                        <div class="fw-field">
                            <label class="fw-label has-tooltip" data-tooltip="Interne Bezeichnung der Dachfläche.">
                                Name der Fläche
                            </label>

                            <input type="text" class="fw-input control-field count-me js-roof-designation"
                                name="roofs[{{ $index }}][designation]" value="{{ $oldVal('designation') }}"
                                placeholder="z.B. Hauptdach Süd">
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Flach- oder Schrägdach.">
                                    Dachform
                                </label>

                                <select name="roofs[{{ $index }}][roof_form]"
                                    class="fw-select control-field count-me js-roof-form-input">
                                    <option value="">Bitte wählen</option>
                                    <option value="Flachdach" @selected($oldVal('roof_form') == 'Flachdach' || $oldVal('roof_form') == '1')>Flachdach</option>
                                    <option value="Schrägdach" @selected($oldVal('roof_form') == 'Schrägdach' || $oldVal('roof_form') == '2')>Schrägdach</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Dachbauweise.">
                                    Dachtyp
                                </label>

                                <select name="roofs[{{ $index }}][roof_type]" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Satteldach" @selected($oldVal('roof_type') == 'Satteldach' || $oldVal('roof_type') == '1')>Satteldach</option>
                                    <option value="Walmdach" @selected($oldVal('roof_type') == 'Walmdach' || $oldVal('roof_type') == '2')>Walmdach</option>
                                    <option value="Pultdach" @selected($oldVal('roof_type') == 'Pultdach' || $oldVal('roof_type') == '4')>Pultdach</option>
                                    <option value="Flachdach" @selected($oldVal('roof_type') == 'Flachdach')>Flachdach
                                    </option>
                                    <option value="Sonstiges" @selected($oldVal('roof_type') == 'Sonstiges')>Sonstiges
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Optional: Dachnummer oder Sortierung.">
                                    Dach Nr.
                                </label>

                                <input type="number" min="1" class="fw-input control-field count-me"
                                    name="roofs[{{ $index }}][sort_order]"
                                    value="{{ $oldVal('sort_order', $index + 1) }}">
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Ist diese Dachfläche für PV nutzbar?">
                                    Für PV nutzbar?
                                </label>

                                <select name="roofs[{{ $index }}][pv_usable]" class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Ja" @selected($oldVal('pv_usable') == 'Ja')>Ja</option>
                                    <option value="Teilweise" @selected($oldVal('pv_usable') == 'Teilweise')>Teilweise
                                    </option>
                                    <option value="Nein" @selected($oldVal('pv_usable') == 'Nein')>Nein</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- GEOMETRIE --}}
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge fw-badge-emerald">
                            <i class="feather icon-compass"></i>
                            Geometrie & Ausrichtung
                        </span>
                    </div>

                    <div class="fw-fields">
                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Himmelsrichtung der Dachfläche.">
                                    Ausrichtung
                                </label>

                                <select name="roofs[{{ $index }}][roof_orientation]"
                                    class="fw-select control-field count-me js-roof-orientation-input">
                                    <option value="">Bitte wählen</option>
                                    <option value="Süd" @selected($oldVal('roof_orientation') == 'Süd')>Süd</option>
                                    <option value="Süd-West" @selected($oldVal('roof_orientation') == 'Süd-West')>Süd-West
                                    </option>
                                    <option value="West" @selected($oldVal('roof_orientation') == 'West')>West</option>
                                    <option value="Ost" @selected($oldVal('roof_orientation') == 'Ost')>Ost</option>
                                    <option value="Süd-Ost" @selected($oldVal('roof_orientation') == 'Süd-Ost')>Süd-Ost
                                    </option>
                                    <option value="Nord" @selected($oldVal('roof_orientation') == 'Nord')>Nord</option>
                                    <option value="Nord-West" @selected($oldVal('roof_orientation') == 'Nord-West')>
                                        Nord-West</option>
                                    <option value="Nord-Ost" @selected($oldVal('roof_orientation') == 'Nord-Ost')>Nord-Ost
                                    </option>
                                    <option value="Flachdach" @selected($oldVal('roof_orientation') == 'Flachdach')>
                                        Flachdach</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Dachneigung in Grad.">
                                    Neigung
                                </label>

                                <div class="fw-input-group">
                                    <input type="number" step="any" min="0" name="roofs[{{ $index }}][roof_pitch]"
                                        class="fw-input control-field count-me" value="{{ $oldVal('roof_pitch') }}"
                                        placeholder="z.B. 30">
                                    <span class="fw-addon">°</span>
                                </div>
                            </div>
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Dachbreite in Metern.">
                                    Breite
                                </label>

                                <div class="fw-input-group">
                                    <input type="number" step="any" min="0" name="roofs[{{ $index }}][roof_width]"
                                        class="fw-input control-field count-me roof-calc-input"
                                        value="{{ $oldVal('roof_width') }}">
                                    <span class="fw-addon">m</span>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Dachlänge in Metern.">
                                    Länge
                                </label>

                                <div class="fw-input-group">
                                    <input type="number" step="any" min="0" name="roofs[{{ $index }}][roof_length]"
                                        class="fw-input control-field count-me roof-calc-input"
                                        value="{{ $oldVal('roof_length') }}">
                                    <span class="fw-addon">m</span>
                                </div>
                            </div>
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Gesamte Dachfläche.">
                                    Dachfläche
                                </label>

                                <div class="fw-input-group">
                                    <input type="number" step="any" min="0" name="roofs[{{ $index }}][roof_area]"
                                        class="fw-input control-field count-me js-roof-area-input"
                                        value="{{ $oldVal('roof_area') }}">
                                    <span class="fw-addon">m²</span>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Für PV nutzbare Fläche.">
                                    Nutzbare PV-Fläche
                                </label>

                                <div class="fw-input-group">
                                    <input type="number" step="any" min="0" name="roofs[{{ $index }}][usable_area]"
                                        class="fw-input control-field count-me" value="{{ $oldVal('usable_area') }}">
                                    <span class="fw-addon">m²</span>
                                </div>
                            </div>
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Höhe bis zur Traufe.">
                                    Traufhöhe
                                </label>

                                <div class="fw-input-group">
                                    <input type="number" step="any" min="0" name="roofs[{{ $index }}][roof_height]"
                                        class="fw-input control-field count-me" value="{{ $oldVal('roof_height') }}">
                                    <span class="fw-addon">m</span>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Firsthöhe, falls bekannt.">
                                    Firsthöhe
                                </label>

                                <div class="fw-input-group">
                                    <input type="number" step="any" min="0" name="roofs[{{ $index }}][ridge_height]"
                                        class="fw-input control-field count-me" value="{{ $oldVal('ridge_height') }}">
                                    <span class="fw-addon">m</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- DACHOBJEKTE --}}
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge">
                            <i class="feather icon-grid"></i>
                            Dachobjekte & Verschattung
                        </span>
                    </div>

                    <div class="fw-fields">
                        <div class="fw-grid-2-inner">
                            <label class="fw-check">
                                <input type="hidden" name="roofs[{{ $index }}][has_chimney]" value="0">
                                <input type="checkbox" name="roofs[{{ $index }}][has_chimney]" value="1"
                                    class="control-field count-me" @checked($checkedVal('has_chimney'))>
                                <span>Kamin vorhanden</span>
                            </label>

                            <label class="fw-check">
                                <input type="hidden" name="roofs[{{ $index }}][has_roof_window]" value="0">
                                <input type="checkbox" name="roofs[{{ $index }}][has_roof_window]" value="1"
                                    class="control-field count-me" @checked($checkedVal('has_roof_window'))>
                                <span>Dachfenster vorhanden</span>
                            </label>
                        </div>

                        <div class="fw-grid-2-inner">
                            <label class="fw-check">
                                <input type="hidden" name="roofs[{{ $index }}][has_dormer]" value="0">
                                <input type="checkbox" name="roofs[{{ $index }}][has_dormer]" value="1"
                                    class="control-field count-me" @checked($checkedVal('has_dormer'))>
                                <span>Gaube vorhanden</span>
                            </label>

                            <label class="fw-check">
                                <input type="hidden" name="roofs[{{ $index }}][has_satellite]" value="0">
                                <input type="checkbox" name="roofs[{{ $index }}][has_satellite]" value="1"
                                    class="control-field count-me" @checked($checkedVal('has_satellite'))>
                                <span>Sat-Schüssel / Antenne</span>
                            </label>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Verschattung</label>

                            <select name="roofs[{{ $index }}][shading]" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Keine" @selected($oldVal('shading') == 'Keine')>Keine</option>
                                <option value="Leicht" @selected($oldVal('shading') == 'Leicht')>Leicht</option>
                                <option value="Mittel" @selected($oldVal('shading') == 'Mittel')>Mittel</option>
                                <option value="Stark" @selected($oldVal('shading') == 'Stark')>Stark</option>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Verschattung Beschreibung</label>

                            <input type="text" name="roofs[{{ $index }}][shading_description]"
                                class="fw-input control-field count-me" value="{{ $oldVal('shading_description') }}"
                                placeholder="z.B. Baum links, Kamin mittig, Nachbarhaus">
                        </div>
                    </div>
                </section>
            </div>

            {{-- RIGHT --}}
            <div class="fw-col">

                {{-- MATERIAL --}}
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge fw-badge-orange">
                            <i class="feather icon-layers"></i>
                            Material & Zustand
                        </span>
                    </div>

                    <div class="fw-fields">
                        <div class="fw-field">
                            <label class="fw-label has-tooltip" data-tooltip="Dachmaterial / Eindeckung.">
                                Eindeckung
                            </label>

                            <select name="roofs[{{ $index }}][roof_covering_name]"
                                class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Dachziegel" @selected($oldVal('roof_covering_name') == 'Dachziegel')>
                                    Dachziegel</option>
                                <option value="Betondachstein"
                                    @selected($oldVal('roof_covering_name') == 'Betondachstein')>Betondachstein</option>
                                <option value="Blechdach" @selected($oldVal('roof_covering_name') == 'Blechdach')>
                                    Blechdach</option>
                                <option value="Foliendach" @selected($oldVal('roof_covering_name') == 'Foliendach')>
                                    Foliendach</option>
                                <option value="Bitumen" @selected($oldVal('roof_covering_name') == 'Bitumen')>Bitumen
                                </option>
                                <option value="Schiefer" @selected($oldVal('roof_covering_name') == 'Schiefer')>Schiefer
                                </option>
                                <option value="Sonstiges" @selected($oldVal('roof_covering_name') == 'Sonstiges')>
                                    Sonstiges</option>
                            </select>
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Hersteller der Eindeckung.">
                                    Hersteller
                                </label>

                                <input type="text" name="roofs[{{ $index }}][roof_covering_company]"
                                    class="fw-input control-field count-me"
                                    value="{{ $oldVal('roof_covering_company') }}">
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Modell der Eindeckung.">
                                    Modell
                                </label>

                                <input type="text" name="roofs[{{ $index }}][roof_covering_model]"
                                    class="fw-input control-field count-me"
                                    value="{{ $oldVal('roof_covering_model') }}">
                            </div>
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Alter des Daches in Jahren.">
                                    Alter
                                </label>

                                <div class="fw-input-group">
                                    <input type="number" min="0" name="roofs[{{ $index }}][roof_age]"
                                        class="fw-input control-field count-me" value="{{ $oldVal('roof_age') }}">
                                    <span class="fw-addon">Jahre</span>
                                </div>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="Ist eine Dachsanierung nötig?">
                                    Sanierung nötig?
                                </label>

                                <select name="roofs[{{ $index }}][roof_renovation]"
                                    class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Nein" @selected($oldVal('roof_renovation') == 'Nein')>Nein</option>
                                    <option value="Ja" @selected($oldVal('roof_renovation') == 'Ja')>Ja</option>
                                    <option value="Unklar" @selected($oldVal('roof_renovation') == 'Unklar')>Unklar
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label">Dachzustand</label>

                                <select name="roofs[{{ $index }}][roof_condition]"
                                    class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Gut" @selected($oldVal('roof_condition') == 'Gut')>Gut</option>
                                    <option value="Mittel" @selected($oldVal('roof_condition') == 'Mittel')>Mittel
                                    </option>
                                    <option value="Schlecht" @selected($oldVal('roof_condition') == 'Schlecht')>Schlecht
                                    </option>
                                    <option value="Unklar" @selected($oldVal('roof_condition') == 'Unklar')>Unklar
                                    </option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Unterkonstruktion</label>

                                <select name="roofs[{{ $index }}][substructure]"
                                    class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Holz" @selected($oldVal('substructure') == 'Holz')>Holz</option>
                                    <option value="Stahl" @selected($oldVal('substructure') == 'Stahl')>Stahl</option>
                                    <option value="Beton" @selected($oldVal('substructure') == 'Beton')>Beton</option>
                                    <option value="Unbekannt" @selected($oldVal('substructure') == 'Unbekannt')>Unbekannt
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- PV PLANUNG --}}
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge" style="background:#eef2ff;border-color:#c7d2fe;color:#4338ca;">
                            <i class="feather icon-sun"></i>
                            PV & Planung
                        </span>
                    </div>

                    <div class="fw-fields">
                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label has-tooltip"
                                    data-tooltip="Bestehende PV-Anlage auf dieser Dachfläche?">
                                    PV vorhanden?
                                </label>

                                <select name="roofs[{{ $index }}][pv_existing]"
                                    class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Nein" @selected($oldVal('pv_existing') == 'Nein')>Nein</option>
                                    <option value="Ja" @selected($oldVal('pv_existing') == 'Ja')>Ja</option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label has-tooltip" data-tooltip="PV-Leistung auf dieser Dachfläche.">
                                    Größe
                                </label>

                                <div class="fw-input-group">
                                    <input type="number" step="any" min="0" name="roofs[{{ $index }}][kwp_size]"
                                        class="fw-input control-field count-me" value="{{ $oldVal('kwp_size') }}">
                                    <span class="fw-addon">kWp</span>
                                </div>
                            </div>
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label">Anzahl Module</label>

                                <input type="number" min="0" name="roofs[{{ $index }}][module_count]"
                                    class="fw-input control-field count-me" value="{{ $oldVal('module_count') }}">
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Modulbelegung möglich?</label>

                                <select name="roofs[{{ $index }}][module_layout_possible]"
                                    class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Ja" @selected($oldVal('module_layout_possible') == 'Ja')>Ja</option>
                                    <option value="Teilweise" @selected($oldVal('module_layout_possible') == 'Teilweise')>
                                        Teilweise</option>
                                    <option value="Nein" @selected($oldVal('module_layout_possible') == 'Nein')>Nein
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label has-tooltip" data-tooltip="Was ist mit dieser Dachfläche geplant?">
                                Weiteres Vorgehen
                            </label>

                            <select name="roofs[{{ $index }}][intention]" class="fw-select control-field count-me">
                                <option value="">Bitte wählen</option>
                                <option value="Interesse" @selected($oldVal('intention') == 'Interesse')>Interesse an PV
                                </option>
                                <option value="Neuanlage" @selected($oldVal('intention') == 'Neuanlage')>Neuanlage geplant
                                </option>
                                <option value="Erweiterung" @selected($oldVal('intention') == 'Erweiterung')>Erweiterung
                                    geplant</option>
                                <option value="Demontage" @selected($oldVal('intention') == 'Demontage')>Demontage geplant
                                </option>
                                <option value="vorhanden" @selected($oldVal('intention') == 'vorhanden')>Bleibt wie es ist
                                </option>
                            </select>
                        </div>
                    </div>
                </section>

                {{-- MONTAGE --}}
                <section class="fw-section">
                    <div class="fw-section-head">
                        <span class="fw-badge fw-badge-emerald">
                            <i class="feather icon-tool"></i>
                            Montage & Zugang
                        </span>
                    </div>

                    <div class="fw-fields">
                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label">Dachzugang</label>

                                <select name="roofs[{{ $index }}][roof_access]"
                                    class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Gut" @selected($oldVal('roof_access') == 'Gut')>Gut</option>
                                    <option value="Eingeschränkt" @selected($oldVal('roof_access') == 'Eingeschränkt')>
                                        Eingeschränkt</option>
                                    <option value="Schwierig" @selected($oldVal('roof_access') == 'Schwierig')>Schwierig
                                    </option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Montagesystem</label>

                                <select name="roofs[{{ $index }}][mounting_system]"
                                    class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Dachhaken" @selected($oldVal('mounting_system') == 'Dachhaken')>
                                        Dachhaken</option>
                                    <option value="Stockschrauben"
                                        @selected($oldVal('mounting_system') == 'Stockschrauben')>Stockschrauben</option>
                                    <option value="Ballastierung"
                                        @selected($oldVal('mounting_system') == 'Ballastierung')>Ballastierung</option>
                                    <option value="Kurzschiene" @selected($oldVal('mounting_system') == 'Kurzschiene')>
                                        Kurzschiene</option>
                                    <option value="Unklar" @selected($oldVal('mounting_system') == 'Unklar')>Unklar
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="fw-grid-2-inner">
                            <div class="fw-field">
                                <label class="fw-label">Gerüst notwendig?</label>

                                <select name="roofs[{{ $index }}][scaffold_required]"
                                    class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Ja" @selected($oldVal('scaffold_required') == 'Ja')>Ja</option>
                                    <option value="Nein" @selected($oldVal('scaffold_required') == 'Nein')>Nein</option>
                                    <option value="Unklar" @selected($oldVal('scaffold_required') == 'Unklar')>Unklar
                                    </option>
                                </select>
                            </div>

                            <div class="fw-field">
                                <label class="fw-label">Kabelweg möglich?</label>

                                <select name="roofs[{{ $index }}][cable_route_possible]"
                                    class="fw-select control-field count-me">
                                    <option value="">Bitte wählen</option>
                                    <option value="Ja" @selected($oldVal('cable_route_possible') == 'Ja')>Ja</option>
                                    <option value="Teilweise" @selected($oldVal('cable_route_possible') == 'Teilweise')>
                                        Teilweise</option>
                                    <option value="Nein" @selected($oldVal('cable_route_possible') == 'Nein')>Nein
                                    </option>
                                    <option value="Unklar" @selected($oldVal('cable_route_possible') == 'Unklar')>Unklar
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="fw-field">
                            <label class="fw-label">Kabelweg Beschreibung</label>

                            <input type="text" name="roofs[{{ $index }}][cable_route_description]"
                                class="fw-input control-field count-me" value="{{ $oldVal('cable_route_description') }}"
                                placeholder="z.B. über Fallrohr, Dachboden, Schacht, Fassade">
                        </div>
                    </div>
                </section>
            </div>
        </div>

        {{-- BEMERKUNGEN --}}
        <div style="margin-top:22px;">
            <div class="fw-note-box">
                <div class="fw-field">
                    <label class="fw-label has-tooltip" data-tooltip="Notizen zum Dach.">
                        Bemerkungen zur Dachfläche
                    </label>

                    <textarea class="fw-textarea control-field count-me" rows="3" name="roofs[{{ $index }}][notes]"
                        placeholder="Besonderheiten, Verschattungen, beschädigte Ziegel, Montagehinweise, Kabelwege etc. eintragen...">{{ $oldVal('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
 