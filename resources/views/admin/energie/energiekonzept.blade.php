@extends('admin.layouts.app')

@section('title', 'Energiekonzept')

{{--
    Energiekonzept-Assembler (Strang Energie).
    Fasst WP-Auslegung + Sanierungs-Wirtschaftlichkeit zu einem kundenfertigen Konzept.
    Server-gerendert: Blade + Bootstrap/Vuexy + minimal jQuery. KEIN Alpine (CLAUDE.md).
    Beide Fach-Abschnitte sind optional (Checkbox blendet sie ein/aus). Die Rechenketten
    liegen serverseitig in EnergiekonzeptController::baueKonzept(); hier nur Eingabe + Vorschau.
--}}

@php
    $kunde = old('kunde', []);
    $wp = old('wp', []);
    $wpAktiv = old('wp_aktiv', !empty($wp));
    $sanAktiv = old('sanierung_aktiv', false);

    $bauteile = old('bauteile', []);
    if (empty($bauteile)) {
        $bauteile = [['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => '', 'u_wert' => '']];
    }
    $massnahmen = old('massnahmen', []);
    $annahmen = old('annahmen', []);
    $projekt = old('projekt', []);
    $raum = old('raum', []);
    $pv = old('pv', []);
    $pvAktiv = old('pv_aktiv', false);

    $wd = $wpDefaults;
    $g = fn ($src, $key, $def = null) => data_get($src, $key, $def);

    $fmt = fn ($v, $d = 0) => $v === null ? '—' : number_format((float) $v, $d, ',', '.');
@endphp

@section('content')
<section class="energie-energiekonzept">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1">
            <h2 class="content-header-title">Energiekonzept</h2>
            <p class="text-muted mb-0">
                Kundenfertiges Gesamt-Konzept aus Wärmepumpen-Auslegung und Gebäudehüllen-Sanierung.
                Beide Bausteine sind optional zuschaltbar. Gesamt-Investition, Förderung und Eigenanteil
                werden zusammengeführt. Vorauslegung — kein Ersatz für eine Energieberatung nach GEG/BEG.
            </p>
        </div>
    </div>

    @if (!empty($fehler))
        <div class="alert alert-danger">{{ $fehler }}</div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ==================== EINGABE ==================== --}}
    <form method="POST" action="{{ route('energie.energiekonzept.berechnen') }}" id="konzept-form">
        @csrf

        {{-- Kunde --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Kunde</h4></div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-12">
                        <label>Name</label>
                        <input type="text" class="form-control" name="kunde[name]"
                            value="{{ $g($kunde, 'name') }}" placeholder="z. B. Familie Muster">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>PLZ</label>
                        <input type="text" class="form-control" name="kunde[plz]"
                            value="{{ $g($kunde, 'plz') }}" placeholder="80331">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Ort</label>
                        <input type="text" class="form-control" name="kunde[ort]"
                            value="{{ $g($kunde, 'ort') }}" placeholder="München">
                    </div>
                    <div class="form-group col-md-3 col-12">
                        <label>Berater</label>
                        <input type="text" class="form-control" name="kunde[berater]"
                            value="{{ $g($kunde, 'berater') }}" placeholder="Name Berater:in">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── WP-Abschnitt (optional) ── --}}
        <div class="card">
            <div class="card-header">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input toggle-section" id="wp_aktiv"
                        name="wp_aktiv" value="1" data-target="#wp-section" @checked($wpAktiv)>
                    <label class="custom-control-label" for="wp_aktiv"><h4 class="card-title mb-0 d-inline">Wärmepumpe einbeziehen</h4></label>
                </div>
            </div>
            <div class="card-body section-body" id="wp-section" @style(['display:none' => !$wpAktiv])>
                <div class="row">
                    <div class="form-group col-md-8 col-12">
                        <label>Wärmepumpe</label>
                        <select class="form-control" name="wp[wp_index]">
                            <option value="">— bitte wählen —</option>
                            @foreach ($wpOptions as $opt)
                                <option value="{{ $opt['index'] }}" @selected((string) $g($wp, 'wp_index') === (string) $opt['index'])>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-6">
                        <label>Investition € (netto/brutto Angebot)</label>
                        <input type="number" step="1" min="0" class="form-control" name="wp[investition]"
                            value="{{ $g($wp, 'investition', $wd['investition']) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-6">
                        <label>Heizlast kW</label>
                        <input type="number" step="0.1" min="0.1" max="100" class="form-control" name="wp[heizlast_kw]"
                            value="{{ $g($wp, 'heizlast_kw', $wd['heizlast_kw']) }}">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Heizsystem</label>
                        <select class="form-control" name="wp[heizsystem]">
                            @foreach (['fussbodenheizung' => 'Fußbodenheizung', 'heizkoerper' => 'Heizkörper', 'beides' => 'Gemischt'] as $k => $lbl)
                                <option value="{{ $k }}" @selected($g($wp, 'heizsystem', $wd['heizsystem']) === $k)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>WP-Typ</label>
                        <select class="form-control" name="wp[wp_typ]">
                            @foreach (['luft_wasser' => 'Luft/Wasser', 'sole_sonde' => 'Sole — Erdsonde', 'sole_kollektor' => 'Sole — Kollektor', 'wasser_wasser' => 'Wasser/Wasser'] as $k => $lbl)
                                <option value="{{ $k }}" @selected($g($wp, 'wp_typ', $wd['wp_typ']) === $k)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Personen im Haushalt</label>
                        <input type="number" step="1" min="1" max="20" class="form-control" name="wp[personen_im_haushalt]"
                            value="{{ $g($wp, 'personen_im_haushalt', $wd['personen_im_haushalt']) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-6">
                        <label>Alte Heizungsart</label>
                        <select class="form-control" name="wp[heizungsart]">
                            @foreach (['keine' => 'keine/unbekannt', 'gas' => 'Gas', 'oel' => 'Öl', 'fluessiggas' => 'Flüssiggas', 'kohle' => 'Kohle', 'nacht' => 'Nachtspeicher', 'holz' => 'Holz', 'fernwaerme' => 'Fernwärme'] as $k => $lbl)
                                <option value="{{ $k }}" @selected($g($wp, 'heizungsart', $wd['heizungsart']) === $k)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Alter Heizung (Jahre)</label>
                        <input type="number" step="1" min="0" max="120" class="form-control" name="wp[heizung_alter]"
                            value="{{ $g($wp, 'heizung_alter', $wd['heizung_alter']) }}">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Strompreis €/kWh</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="wp[strompreis]"
                            value="{{ $g($wp, 'strompreis', $wd['strompreis']) }}">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Anzahl WE</label>
                        <input type="number" step="1" min="1" class="form-control" name="wp[anzahl_we]"
                            value="{{ $g($wp, 'anzahl_we', $wd['anzahl_we']) }}">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Selbst bew. WE</label>
                        <input type="number" step="1" min="0" class="form-control" name="wp[selbst_bewohnte_we]"
                            value="{{ $g($wp, 'selbst_bewohnte_we', $wd['selbst_bewohnte_we']) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="custom-control custom-checkbox mt-1">
                            <input type="checkbox" class="custom-control-input" id="wp_ww_mit_wp" name="wp[ww_mit_wp]" value="1"
                                @checked($g($wp, 'ww_mit_wp', $wd['ww_mit_wp']))>
                            <label class="custom-control-label" for="wp_ww_mit_wp">Warmwasser über WP</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="custom-control custom-checkbox mt-1">
                            <input type="checkbox" class="custom-control-input" id="wp_effizienzbonus" name="wp[effizienzbonus]" value="1"
                                @checked($g($wp, 'effizienzbonus'))>
                            <label class="custom-control-label" for="wp_effizienzbonus">Effizienzbonus (+5 %)</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="custom-control custom-checkbox mt-1">
                            <input type="checkbox" class="custom-control-input" id="wp_einkommensbonus" name="wp[einkommensbonus]" value="1"
                                @checked($g($wp, 'einkommensbonus'))>
                            <label class="custom-control-label" for="wp_einkommensbonus">Einkommensbonus (+30 %)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Sanierungs-Abschnitt (optional) ── --}}
        <div class="card">
            <div class="card-header">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input toggle-section" id="sanierung_aktiv"
                        name="sanierung_aktiv" value="1" data-target="#san-section" @checked($sanAktiv)>
                    <label class="custom-control-label" for="sanierung_aktiv"><h4 class="card-title mb-0 d-inline">Sanierung einbeziehen</h4></label>
                </div>
            </div>
            <div class="card-body section-body" id="san-section" @style(['display:none' => !$sanAktiv])>

                {{-- Gebäude-Basisdaten --}}
                <div class="row">
                    <div class="form-group col-md-2 col-6">
                        <label>Standort-PLZ</label>
                        <input type="text" class="form-control" name="projekt[standort_plz]"
                            value="{{ $g($projekt, 'standort_plz', $g($kunde, 'plz')) }}" placeholder="80331">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Baujahr</label>
                        <input type="number" class="form-control" name="projekt[baujahr]"
                            value="{{ $g($projekt, 'baujahr') }}" min="1800" max="2100" placeholder="1975">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Ziel-Vorlauf °C</label>
                        <input type="number" step="0.5" class="form-control" name="projekt[ziel_vorlauf_c]"
                            value="{{ $g($projekt, 'ziel_vorlauf_c') }}" placeholder="35">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Spreizung K</label>
                        <input type="number" step="0.5" class="form-control" name="projekt[spreizung_k]"
                            value="{{ $g($projekt, 'spreizung_k') }}" placeholder="7">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Grundfläche m²</label>
                        <input type="number" step="0.1" min="1" class="form-control" name="raum[grundflaeche_m2]"
                            value="{{ $g($raum, 'grundflaeche_m2') }}" placeholder="140">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Raumhöhe m</label>
                        <input type="number" step="0.1" min="1" max="10" class="form-control" name="raum[hoehe_m]"
                            value="{{ $g($raum, 'hoehe_m', '2.5') }}" placeholder="2.5">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-6">
                        <label>Innentemp. °C <small class="text-muted">(opt.)</small></label>
                        <input type="number" step="0.5" class="form-control" name="raum[theta_int_c]"
                            value="{{ $g($raum, 'theta_int_c') }}" placeholder="20">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Luftwechsel 1/h <small class="text-muted">(opt.)</small></label>
                        <input type="number" step="0.05" class="form-control" name="raum[luftwechsel_1h]"
                            value="{{ $g($raum, 'luftwechsel_1h') }}" placeholder="0.5">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Energiepreis ct/kWh</label>
                        <input type="number" step="0.1" min="0" class="form-control" name="annahmen[energiepreis_ct_kwh]"
                            value="{{ $g($annahmen, 'energiepreis_ct_kwh', 12) }}">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Anzahl WE (Förderung)</label>
                        <input type="number" step="1" min="1" class="form-control" name="annahmen[anzahl_we]"
                            value="{{ $g($annahmen, 'anzahl_we', 1) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-12">
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="san_isfp" name="annahmen[isfp]" value="1"
                                @checked($g($annahmen, 'isfp'))>
                            <label class="custom-control-label" for="san_isfp">iSFP-Bonus (+5 %)</label>
                        </div>
                    </div>
                </div>

                {{-- Hüllbauteile --}}
                <div class="d-flex justify-content-between align-items-center mt-1 mb-1">
                    <h5 class="mb-0">Hüllbauteile (IST-Zustand)</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-bauteil">+ Bauteil</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0" id="bauteil-table">
                        <thead>
                            <tr>
                                <th style="min-width:130px;">Typ</th>
                                <th style="min-width:130px;">Grenzfläche</th>
                                <th style="min-width:110px;">Fläche m²</th>
                                <th style="min-width:130px;">U-Wert W/m²K</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="bauteil-body">
                            @foreach ($bauteile as $i => $b)
                                <tr class="bauteil-row">
                                    <td>
                                        <select class="form-control form-control-sm" name="bauteile[{{ $i }}][typ]">
                                            @foreach ($bauteilTypen as $t)
                                                <option value="{{ $t }}" @selected(data_get($b, 'typ') === $t)>{{ ucfirst($t) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm" name="bauteile[{{ $i }}][grenzflaeche]">
                                            @foreach ($grenzflaechen as $gf)
                                                <option value="{{ $gf }}" @selected(data_get($b, 'grenzflaeche') === $gf)>{{ ucfirst($gf) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.1" min="0.1" class="form-control form-control-sm"
                                        name="bauteile[{{ $i }}][flaeche_m2]" value="{{ data_get($b, 'flaeche_m2') }}"></td>
                                    <td><input type="number" step="0.01" min="0.01" max="10" class="form-control form-control-sm"
                                        name="bauteile[{{ $i }}][u_wert]" value="{{ data_get($b, 'u_wert') }}"></td>
                                    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-row">&times;</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Maßnahmen --}}
                <div class="d-flex justify-content-between align-items-center mt-2 mb-1">
                    <h5 class="mb-0">Sanierungs-Maßnahmen</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-massnahme">+ Maßnahme</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0" id="massnahme-table">
                        <thead>
                            <tr>
                                <th style="min-width:150px;">Ziel-Bauteil</th>
                                <th style="min-width:150px;">Neuer U-Wert W/m²K</th>
                                <th style="min-width:150px;">Kosten brutto €</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="massnahme-body">
                            @foreach ($massnahmen as $i => $m)
                                <tr class="massnahme-row">
                                    <td>
                                        <select class="form-control form-control-sm" name="massnahmen[{{ $i }}][ziel_typ]">
                                            @foreach ($bauteilTypen as $t)
                                                <option value="{{ $t }}" @selected(data_get($m, 'ziel_typ') === $t)>{{ ucfirst($t) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0.01" max="10" class="form-control form-control-sm"
                                        name="massnahmen[{{ $i }}][u_neu]" value="{{ data_get($m, 'u_neu') }}"></td>
                                    <td><input type="number" step="1" min="0" class="form-control form-control-sm"
                                        name="massnahmen[{{ $i }}][kosten_brutto]" value="{{ data_get($m, 'kosten_brutto') }}"></td>
                                    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-row">&times;</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Je Maßnahme wird der neue U-Wert auf ALLE Bauteile des gewählten Typs angewandt.</small>
            </div>
        </div>

        {{-- ── PV-Abschnitt (optional) ── --}}
        <div class="card">
            <div class="card-header">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input toggle-section" id="pv_aktiv"
                        name="pv_aktiv" value="1" data-target="#pv-section" @checked($pvAktiv)>
                    <label class="custom-control-label" for="pv_aktiv"><h4 class="card-title mb-0 d-inline">Photovoltaik einbeziehen</h4></label>
                </div>
            </div>
            <div class="card-body section-body" id="pv-section" @style(['display:none' => !$pvAktiv])>
                <div class="row">
                    <div class="form-group col-md-3 col-6">
                        <label>Anlagengröße kWp</label>
                        <input type="number" step="0.1" min="0" class="form-control" name="pv[kwp]"
                            value="{{ $g($pv, 'kwp', 10) }}" placeholder="10">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Investition €</label>
                        <input type="number" step="1" min="0" class="form-control" name="pv[investition]"
                            value="{{ $g($pv, 'investition', 18000) }}">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Neigung ° <small class="text-muted">(Dach)</small></label>
                        <input type="number" step="1" min="0" max="90" class="form-control" name="pv[angle]"
                            value="{{ $g($pv, 'angle', 30) }}">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Ausrichtung <small class="text-muted">(0=Süd, −90=Ost, 90=West)</small></label>
                        <input type="number" step="1" min="-180" max="180" class="form-control" name="pv[aspect]"
                            value="{{ $g($pv, 'aspect', 0) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-6">
                        <label>Strompreis €/kWh</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="pv[strompreis]"
                            value="{{ $g($pv, 'strompreis', 0.30) }}">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Einspeisevergütung €/kWh</label>
                        <input type="number" step="0.001" min="0" class="form-control" name="pv[einspeiseverguetung]"
                            value="{{ $g($pv, 'einspeiseverguetung', 0.08) }}">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Eigenverbrauch %</label>
                        <input type="number" step="1" min="0" max="100" class="form-control" name="pv[eigenverbrauch_pct]"
                            value="{{ $g($pv, 'eigenverbrauch_pct', 30) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-6">
                        <label>Breite (lat) <small class="text-muted">(opt.)</small></label>
                        <input type="number" step="0.00001" min="-90" max="90" class="form-control" name="pv[lat]"
                            value="{{ $g($pv, 'lat') }}" placeholder="48.14">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Länge (lon) <small class="text-muted">(opt.)</small></label>
                        <input type="number" step="0.00001" min="-180" max="180" class="form-control" name="pv[lon]"
                            value="{{ $g($pv, 'lon') }}" placeholder="11.58">
                    </div>
                    <div class="col-12">
                        <small class="text-muted">
                            Ertrag über PVGIS. Ohne lat/lon werden die Koordinaten aus der Standort-PLZ
                            (Abschnitt Sanierung) bzw. Kunden-PLZ aufgelöst; sind keine verfügbar oder
                            PVGIS nicht erreichbar, greift ein Schätz-Spezifischertrag (~950 kWh/kWp).
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap mb-2" style="gap:.5rem;">
            <button type="submit" class="btn btn-primary">Energiekonzept berechnen</button>
            <button type="submit" class="btn btn-outline-secondary" formaction="{{ route('energie.energiekonzept.dokument') }}" formtarget="_blank">
                Als Beratungsangebot / PDF
            </button>
        </div>
    </form>

    {{-- ==================== ERGEBNIS-VORSCHAU ==================== --}}
    @if (!empty($konzept))
        @php
            $ges = $konzept['gesamt'];
            $wpErg = $konzept['wp'];
            $sanErg = $konzept['sanierung'];
            $pvErg = $konzept['pv'] ?? null;
        @endphp

        <div class="card border-primary">
            <div class="card-header">
                <h4 class="card-title">Energiekonzept — Vorschau</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-1">
                    {{ $konzept['kunde']['name'] ?: 'Kunde' }}
                    @if ($konzept['kunde']['plz'] || $konzept['kunde']['ort'])
                        · {{ trim(($konzept['kunde']['plz'] ?? '').' '.($konzept['kunde']['ort'] ?? '')) }}
                    @endif
                    · Stand {{ $konzept['datum'] }}
                    @if ($konzept['kunde']['berater']) · Berater: {{ $konzept['kunde']['berater'] }} @endif
                </p>

                {{-- Gesamt-KPIs --}}
                <div class="row">
                    <div class="col-md-3 col-6 mb-1">
                        <div class="p-1 border rounded h-100">
                            <div class="text-muted"><small>Gesamt-Investition</small></div>
                            <div class="h3 mb-0">{{ $fmt($ges['investition_eur']) }} €</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-1">
                        <div class="p-1 border rounded h-100">
                            <div class="text-muted"><small>Förderung</small></div>
                            <div class="h3 mb-0 text-success">−{{ $fmt($ges['foerderung_eur']) }} €</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-1">
                        <div class="p-1 border rounded h-100">
                            <div class="text-muted"><small>Eigenanteil</small></div>
                            <div class="h3 mb-0"><strong>{{ $fmt($ges['eigenanteil_eur']) }} €</strong></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-1">
                        <div class="p-1 border rounded h-100">
                            <div class="text-muted"><small>Einsparung / Jahr</small></div>
                            <div class="h3 mb-0">{{ $fmt($ges['einsparung_eur_a']) }} €</div>
                        </div>
                    </div>
                </div>

                <div class="row mt-1">
                    {{-- WP-Block --}}
                    <div class="col-lg-6 col-12">
                        <h5>Wärmepumpe</h5>
                        @if ($wpErg)
                            <dl class="row mb-0">
                                <dt class="col-6">Gerät</dt><dd class="col-6 text-right">{{ trim(($wpErg['hersteller'] ?? '').' '.($wpErg['modell'] ?? '')) ?: '—' }}</dd>
                                <dt class="col-6">JAZ</dt><dd class="col-6 text-right">{{ $fmt($wpErg['jaz'], 2) }}</dd>
                                <dt class="col-6">Stromverbrauch</dt><dd class="col-6 text-right">{{ $fmt($wpErg['strom_kwh']) }} kWh/a</dd>
                                <dt class="col-6">Stromkosten</dt><dd class="col-6 text-right">{{ $fmt($wpErg['stromkosten_jahr']) }} €/a</dd>
                                <dt class="col-6">Investition (netto)</dt><dd class="col-6 text-right">{{ $fmt($wpErg['investition_netto']) }} €</dd>
                                <dt class="col-6">KfW/BEG-Zuschuss</dt><dd class="col-6 text-right text-success">−{{ $fmt($wpErg['foerderung']['zuschuss']) }} € ({{ $fmt($wpErg['foerderung']['effektiver_satz_pct'], 1) }} %)</dd>
                                <dt class="col-6">Netto-Investition</dt><dd class="col-6 text-right"><strong>{{ $fmt($wpErg['foerderung']['netto_investition']) }} €</strong></dd>
                            </dl>
                        @else
                            <p class="text-muted">Nicht einbezogen.</p>
                        @endif
                    </div>

                    {{-- Sanierungs-Block --}}
                    <div class="col-lg-6 col-12">
                        <h5>Sanierung</h5>
                        @if ($sanErg)
                            <dl class="row mb-0">
                                <dt class="col-6">Heizlast-Reduktion</dt><dd class="col-6 text-right">−{{ $fmt($sanErg['delta']['phi_hl_kw'], 2) }} kW ({{ $fmt($sanErg['delta']['phi_hl_pct'], 1) }} %)</dd>
                                <dt class="col-6">Q_a-Einsparung</dt><dd class="col-6 text-right">−{{ $fmt($sanErg['delta']['q_a_kwh']) }} kWh/a</dd>
                                <dt class="col-6">Investition</dt><dd class="col-6 text-right">{{ $fmt($sanErg['wirtschaftlichkeit']['investition_eur']) }} €</dd>
                                <dt class="col-6">BAFA-Förderung</dt><dd class="col-6 text-right text-success">−{{ $fmt($sanErg['wirtschaftlichkeit']['foerderung_eur']) }} € ({{ $fmt($sanErg['foerderung']['quote_pct'], 0) }} %)</dd>
                                <dt class="col-6">Netto-Investition</dt><dd class="col-6 text-right"><strong>{{ $fmt($sanErg['wirtschaftlichkeit']['netto_investition_eur']) }} €</strong></dd>
                                <dt class="col-6">Einsparung / Jahr</dt><dd class="col-6 text-right">{{ $fmt($sanErg['wirtschaftlichkeit']['einsparung_eur_a']) }} €</dd>
                                <dt class="col-6">Amortisation</dt><dd class="col-6 text-right">{{ $sanErg['wirtschaftlichkeit']['amortisation_jahre'] === null ? '—' : $fmt($sanErg['wirtschaftlichkeit']['amortisation_jahre'], 1).' J' }}</dd>
                                <dt class="col-6">Gewinn 30 Jahre</dt><dd class="col-6 text-right">{{ $fmt($sanErg['wirtschaftlichkeit']['gewinn_30j_eur']) }} €</dd>
                            </dl>
                        @else
                            <p class="text-muted">Nicht einbezogen.</p>
                        @endif
                    </div>
                </div>

                @if ($pvErg)
                    {{-- PV-Block --}}
                    <div class="row mt-1">
                        <div class="col-12">
                            <h5>Photovoltaik</h5>
                            <dl class="row mb-0">
                                <dt class="col-6 col-md-3">Anlagengröße</dt><dd class="col-6 col-md-3 text-right">{{ $fmt($pvErg['kwp'], 1) }} kWp</dd>
                                <dt class="col-6 col-md-3">Jahresertrag</dt><dd class="col-6 col-md-3 text-right">{{ $fmt($pvErg['jahresertrag_kwh']) }} kWh/a <small class="text-muted">({{ $pvErg['quelle'] === 'pvgis' ? 'PVGIS' : 'Schätzung' }})</small></dd>
                                <dt class="col-6 col-md-3">Eigenverbrauch-Ersparnis</dt><dd class="col-6 col-md-3 text-right text-success">{{ $fmt($pvErg['ersparnis_eigenverbrauch_eur_a']) }} €/a</dd>
                                <dt class="col-6 col-md-3">Einspeise-Vergütung</dt><dd class="col-6 col-md-3 text-right text-success">{{ $fmt($pvErg['verguetung_einspeisung_eur_a']) }} €/a</dd>
                                <dt class="col-6 col-md-3">PV-Ertrag gesamt</dt><dd class="col-6 col-md-3 text-right"><strong>{{ $fmt($pvErg['ertrag_gesamt_eur_a']) }} €/a</strong></dd>
                                <dt class="col-6 col-md-3">Investition</dt><dd class="col-6 col-md-3 text-right">{{ $fmt($pvErg['investition_eur']) }} €</dd>
                            </dl>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

</section>
@endsection

@push('scripts')
<script>
    // Ein-/Ausblenden der Fach-Abschnitte + dynamische Bauteil-/Maßnahmen-Zeilen (jQuery, KEIN Alpine).
    $(function () {
        var bauteilTypen = @json($bauteilTypen);
        var grenzflaechen = @json($grenzflaechen);

        // Abschnitte umschalten.
        $('.toggle-section').on('change', function () {
            var $target = $($(this).data('target'));
            $target.toggle(this.checked);
        }).each(function () {
            $($(this).data('target')).toggle(this.checked);
        });

        function optionList(values, sel) {
            return values.map(function (v) {
                var label = v.charAt(0).toUpperCase() + v.slice(1);
                return '<option value="' + v + '"' + (v === sel ? ' selected' : '') + '>' + label + '</option>';
            }).join('');
        }

        function bauteilRow(i) {
            return '<tr class="bauteil-row">'
                + '<td><select class="form-control form-control-sm" name="bauteile[' + i + '][typ]">' + optionList(bauteilTypen, 'wand') + '</select></td>'
                + '<td><select class="form-control form-control-sm" name="bauteile[' + i + '][grenzflaeche]">' + optionList(grenzflaechen, 'aussen') + '</select></td>'
                + '<td><input type="number" step="0.1" min="0.1" class="form-control form-control-sm" name="bauteile[' + i + '][flaeche_m2]"></td>'
                + '<td><input type="number" step="0.01" min="0.01" max="10" class="form-control form-control-sm" name="bauteile[' + i + '][u_wert]"></td>'
                + '<td><button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-row">&times;</button></td>'
                + '</tr>';
        }

        function massnahmeRow(i) {
            return '<tr class="massnahme-row">'
                + '<td><select class="form-control form-control-sm" name="massnahmen[' + i + '][ziel_typ]">' + optionList(bauteilTypen, 'wand') + '</select></td>'
                + '<td><input type="number" step="0.01" min="0.01" max="10" class="form-control form-control-sm" name="massnahmen[' + i + '][u_neu]"></td>'
                + '<td><input type="number" step="1" min="0" class="form-control form-control-sm" name="massnahmen[' + i + '][kosten_brutto]"></td>'
                + '<td><button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-row">&times;</button></td>'
                + '</tr>';
        }

        var bauteilIdx = $('#bauteil-body tr').length;
        var massnahmeIdx = $('#massnahme-body tr').length;

        $('#add-bauteil').on('click', function () {
            $('#bauteil-body').append(bauteilRow(bauteilIdx++));
        });
        $('#add-massnahme').on('click', function () {
            $('#massnahme-body').append(massnahmeRow(massnahmeIdx++));
        });

        $(document).on('click', '.remove-row', function () {
            var $row = $(this).closest('tr');
            if ($row.hasClass('bauteil-row') && $('#bauteil-body tr').length <= 1) {
                return;
            }
            $row.remove();
        });
    });
</script>
@endpush
