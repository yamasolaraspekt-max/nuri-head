@extends('admin.layouts.app')

@section('title', 'Wärmepumpen-Auslegung')

{{--
    Wärmepumpen-Auslegung (Strang Energie) — Heizlast → JAZ/Verbrauch → Wirtschaftlichkeit + KfW/BEG.
    Server-gerendert: Blade + Bootstrap/Vuexy + minimal jQuery. KEIN Alpine (CLAUDE.md).
    Rechnung liegt serverseitig in JazService/WarmwasserService/VerbrauchsService/KostenService/
    FoerderungService (portierte Kerne); hier nur Auswahl + Anzeige.
--}}

@php
    $eur = fn ($v) => number_format((float) $v, 0, ',', '.').' €';
    $kwh = fn ($v) => number_format((float) $v, 0, ',', '.').' kWh';
@endphp

@section('content')
<section class="energie-wp-auslegung">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1">
            <h2 class="content-header-title">Wärmepumpen-Auslegung &amp; Wirtschaftlichkeit</h2>
            <p class="text-muted mb-0">
                Vorauslegung nach Heizlast, Heizsystem und Warmwasser inkl. Jahresarbeitszahl (JAZ),
                geschätztem Stromverbrauch und KfW-/BEG-Heizungsförderung (BEG EM / KfW 458, Stand 2024/25).
                Richtwerte auf Basis hinterlegter Datenblatt- und Förderparameter — verbindliche Auslegung
                und Förderzusage durch Fachplanung bzw. Energie-Effizienz-Experten.
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

    <div class="row">
        {{-- Eingabe --}}
        <div class="col-lg-5 col-12">
            <form method="POST" action="{{ route('energie.wp-auslegung.berechnen') }}">
                @csrf

                <div class="card">
                    <div class="card-header"><h4 class="card-title">Wärmepumpe &amp; Gebäude</h4></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="wp_index">Wärmepumpe</label>
                            <select class="form-control energie-select" id="wp_index" name="wp_index" required>
                                <option value="">— Wärmepumpe wählen —</option>
                                @foreach ($wpOptions as $opt)
                                    <option value="{{ $opt['index'] }}" @selected(($eingabe['wp_index'] ?? null) === $opt['index'])>{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="heizlast_kw">Heizlast (kW)</label>
                                <input type="number" step="0.1" min="0.1" class="form-control" id="heizlast_kw"
                                    name="heizlast_kw" value="{{ $eingabe['heizlast_kw'] }}" required>
                            </div>
                            <div class="form-group col-6">
                                <label for="wp_typ">Wärmequelle</label>
                                <select class="form-control" id="wp_typ" name="wp_typ" required>
                                    <option value="luft_wasser" @selected($eingabe['wp_typ']==='luft_wasser')>Luft/Wasser</option>
                                    <option value="sole_sonde" @selected($eingabe['wp_typ']==='sole_sonde')>Sole/Wasser — Erdsonde</option>
                                    <option value="sole_kollektor" @selected($eingabe['wp_typ']==='sole_kollektor')>Sole/Wasser — Kollektor</option>
                                    <option value="wasser_wasser" @selected($eingabe['wp_typ']==='wasser_wasser')>Wasser/Wasser</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="heizsystem">Heizsystem / Vorlauf</label>
                            <select class="form-control" id="heizsystem" name="heizsystem" required>
                                <option value="fussbodenheizung" @selected($eingabe['heizsystem']==='fussbodenheizung')>Fußbodenheizung (~35 °C)</option>
                                <option value="heizkoerper" @selected($eingabe['heizsystem']==='heizkoerper')>Heizkörper (~55 °C)</option>
                                <option value="beides" @selected($eingabe['heizsystem']==='beides')>Gemischt (~45 °C)</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="personen_im_haushalt">Personen im Haushalt</label>
                                <input type="number" min="1" class="form-control" id="personen_im_haushalt"
                                    name="personen_im_haushalt" value="{{ $eingabe['personen_im_haushalt'] }}" required>
                            </div>
                            <div class="form-group col-6">
                                <label for="strompreis">Strompreis (€/kWh)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="strompreis"
                                    name="strompreis" value="{{ $eingabe['strompreis'] }}">
                            </div>
                        </div>

                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="ww_mit_wp" name="ww_mit_wp" value="1" @checked($eingabe['ww_mit_wp'])>
                            <label class="custom-control-label" for="ww_mit_wp">Warmwasser über die Wärmepumpe</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="badewanne_vorhanden" name="badewanne_vorhanden" value="1" @checked($eingabe['badewanne_vorhanden'])>
                            <label class="custom-control-label" for="badewanne_vorhanden">Badewanne vorhanden (höhere Zapfspitze)</label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4 class="card-title">Wirtschaftlichkeit &amp; Förderung</h4></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="investition">Investitionskosten (€, netto)</label>
                            <input type="number" step="1" min="0" class="form-control" id="investition"
                                name="investition" value="{{ $eingabe['investition'] }}" required>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="heizungsart">Bisherige Heizung (Austausch)</label>
                                <select class="form-control" id="heizungsart" name="heizungsart">
                                    <option value="oel" @selected($eingabe['heizungsart']==='oel')>Ölheizung</option>
                                    <option value="gas" @selected($eingabe['heizungsart']==='gas')>Gasheizung</option>
                                    <option value="fluessiggas" @selected($eingabe['heizungsart']==='fluessiggas')>Flüssiggas</option>
                                    <option value="kohle" @selected($eingabe['heizungsart']==='kohle')>Kohle</option>
                                    <option value="nacht" @selected($eingabe['heizungsart']==='nacht')>Nachtspeicher</option>
                                    <option value="holz" @selected($eingabe['heizungsart']==='holz')>Holz/Pellets</option>
                                    <option value="fernwaerme" @selected($eingabe['heizungsart']==='fernwaerme')>Fernwärme</option>
                                    <option value="keine" @selected($eingabe['heizungsart']==='keine')>keine / andere</option>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="heizung_alter">Alter der Heizung (Jahre)</label>
                                <input type="number" min="0" class="form-control" id="heizung_alter"
                                    name="heizung_alter" value="{{ $eingabe['heizung_alter'] }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="anzahl_we">Wohneinheiten</label>
                                <input type="number" min="1" class="form-control" id="anzahl_we"
                                    name="anzahl_we" value="{{ $eingabe['anzahl_we'] }}">
                            </div>
                            <div class="form-group col-6">
                                <label for="selbst_bewohnte_we">davon selbst bewohnt</label>
                                <input type="number" min="0" class="form-control" id="selbst_bewohnte_we"
                                    name="selbst_bewohnte_we" value="{{ $eingabe['selbst_bewohnte_we'] }}">
                            </div>
                        </div>

                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="effizienzbonus" name="effizienzbonus" value="1" @checked($eingabe['effizienzbonus'])>
                            <label class="custom-control-label" for="effizienzbonus">Effizienzbonus (+5 %: nat. Kältemittel bzw. Erd-/Wasserquelle)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="einkommensbonus" name="einkommensbonus" value="1" @checked($eingabe['einkommensbonus'])>
                            <label class="custom-control-label" for="einkommensbonus">Einkommensbonus (+30 %: zvE ≤ 40.000 €, Selbstnutzer)</label>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Klimageschwindigkeitsbonus (+20 %) wird automatisch aus Heizungsart &amp; -alter ermittelt.
                        </small>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4 class="card-title">Plausibilisierung <small class="text-muted">(optional)</small></h4></div>
                    <div class="card-body">
                        <p class="text-muted"><small>Bisheriger Energieverbrauch zur Gegenprobe der Heizlast (Methode V). Leer lassen, wenn unbekannt.</small></p>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="verbrauch_menge">Verbrauch/Zeitraum</label>
                                <input type="number" step="0.1" min="0" class="form-control" id="verbrauch_menge"
                                    name="verbrauch_menge" value="{{ $eingabe['verbrauch_menge'] }}">
                            </div>
                            <div class="form-group col-6">
                                <label for="verbrauch_einheit">Einheit</label>
                                <select class="form-control" id="verbrauch_einheit" name="verbrauch_einheit">
                                    @foreach (['kWh','m3','Liter','kg','Ster'] as $ein)
                                        <option value="{{ $ein }}" @selected($eingabe['verbrauch_einheit']===$ein)>{{ $ein }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="aktuelles_heizmedium">Heizmedium</label>
                                <select class="form-control" id="aktuelles_heizmedium" name="aktuelles_heizmedium">
                                    @foreach (['erdgas'=>'Erdgas','heizoel'=>'Heizöl','fluessiggas'=>'Flüssiggas','pellets'=>'Pellets','scheitholz'=>'Scheitholz','strom'=>'Strom','fernwaerme'=>'Fernwärme'] as $k=>$v)
                                        <option value="{{ $k }}" @selected($eingabe['aktuelles_heizmedium']===$k)>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="verbrauch_zeitraum_jahre">Zeitraum (Jahre)</label>
                                <input type="number" min="1" class="form-control" id="verbrauch_zeitraum_jahre"
                                    name="verbrauch_zeitraum_jahre" value="{{ $eingabe['verbrauch_zeitraum_jahre'] }}">
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="enthaelt_warmwasser" name="enthaelt_warmwasser" value="1" @checked($eingabe['enthaelt_warmwasser'])>
                            <label class="custom-control-label" for="enthaelt_warmwasser">Verbrauch enthält Warmwasser</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mb-2">Auslegen &amp; berechnen</button>
            </form>
        </div>

        {{-- Ergebnis --}}
        <div class="col-lg-7 col-12">
            @if (!empty($ergebnis))
                {{-- Wirtschaftlichkeit / Förderung (hervorgehoben) --}}
                @php $f = $ergebnis['foerderung']; @endphp
                <div class="card border-success">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title mb-0 text-success">Wirtschaftlichkeit &amp; KfW-/BEG-Förderung</h4>
                        <span class="badge badge-success">Effektiver Satz {{ number_format($f['effektiver_satz_pct'], 1, ',', '.') }} %</span>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 col-12 mb-1">
                                <div class="text-muted"><small>Investition (netto)</small></div>
                                <h3 class="mb-0">{{ $eur($ergebnis['investition_netto']) }}</h3>
                            </div>
                            <div class="col-md-4 col-12 mb-1">
                                <div class="text-muted"><small>KfW-/BEG-Zuschuss</small></div>
                                <h3 class="mb-0 text-success">− {{ $eur($f['zuschuss']) }}</h3>
                                <small class="text-muted">{{ number_format($f['effektiver_satz_pct'], 1, ',', '.') }} % der förderfähigen Kosten</small>
                            </div>
                            <div class="col-md-4 col-12 mb-1">
                                <div class="text-muted"><small>Netto-Investition</small></div>
                                <h3 class="mb-0 text-primary">{{ $eur($f['netto_investition']) }}</h3>
                            </div>
                        </div>

                        <hr>
                        <dl class="row mb-0">
                            <dt class="col-sm-6">Förderfähige Kosten (nach Deckel)</dt>
                            <dd class="col-sm-6">{{ $eur($f['foerderfaehig']) }} <small class="text-muted">(Deckel {{ $eur($f['deckel']) }})</small></dd>
                            <dt class="col-sm-6">Grundförderung</dt>
                            <dd class="col-sm-6">{{ $f['saetze']['grund'] }} %</dd>
                            <dt class="col-sm-6">Effizienzbonus</dt>
                            <dd class="col-sm-6">{{ $f['saetze']['effizienz'] }} %</dd>
                            <dt class="col-sm-6">Klimageschwindigkeitsbonus</dt>
                            <dd class="col-sm-6">
                                {{ $f['saetze']['klima'] }} %
                                @if ($f['klima_berechtigt'])
                                    <span class="badge badge-light-success">berechtigt</span>
                                @else
                                    <span class="badge badge-light-secondary">nicht berechtigt</span>
                                @endif
                            </dd>
                            <dt class="col-sm-6">Einkommensbonus</dt>
                            <dd class="col-sm-6">{{ $f['saetze']['einkommen'] }} %</dd>
                            <dt class="col-sm-6">Fördersatz (Selbstnutzer)</dt>
                            <dd class="col-sm-6">
                                {{ $f['saetze']['selbst_mit_bonus'] > $f['saetze']['selbst_ohne_pct'] ? $f['saetze']['selbst_mit_pct'] : $f['saetze']['selbst_ohne_pct'] }} %
                                <small class="text-muted">(max. 70 %)</small>
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- WP-Kenndaten + Energie --}}
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Auslegung &amp; Kenndaten</h4></div>
                    <div class="card-body">
                        <h6 class="text-muted">{{ $ergebnis['wp_label'] }}</h6>
                        <dl class="row mb-1">
                            <dt class="col-sm-5">Gerätetyp / Serie</dt>
                            <dd class="col-sm-7">{{ $ergebnis['wp']['geraetetyp'] ?? '—' }}@if(!empty($ergebnis['wp']['serie'])) · {{ $ergebnis['wp']['serie'] }}@endif</dd>
                            <dt class="col-sm-5">Kältemittel</dt>
                            <dd class="col-sm-7">{{ $ergebnis['wp']['kaeltemittel'] ?? '—' }}</dd>
                            <dt class="col-sm-5">SCOP (35 °C / 55 °C)</dt>
                            <dd class="col-sm-7">{{ $ergebnis['wp']['scop_35'] ?? '—' }} / {{ $ergebnis['wp']['scop_55'] ?? '—' }}</dd>
                            <dt class="col-sm-5">Heizleistung A7/W35 · A−7/W35</dt>
                            <dd class="col-sm-7">{{ $ergebnis['wp']['heizleistung_a7_w35_kw'] ?? '—' }} kW · {{ $ergebnis['wp']['heizleistung_am7_w35_kw'] ?? '—' }} kW</dd>
                            <dt class="col-sm-5">max. Vorlauf</dt>
                            <dd class="col-sm-7">{{ $ergebnis['wp']['max_vorlauf_c'] ?? '—' }} °C</dd>
                        </dl>
                        <hr>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Heizlast</dt>
                            <dd class="col-sm-7">{{ number_format($ergebnis['heizlast_kw'], 1, ',', '.') }} kW</dd>
                            <dt class="col-sm-5">Heizsystem</dt>
                            <dd class="col-sm-7">{{ $ergebnis['heizsystem_label'] }}</dd>
                            <dt class="col-sm-5">Wärmequelle</dt>
                            <dd class="col-sm-7">{{ $ergebnis['wp_typ_label'] }}</dd>
                            <dt class="col-sm-5">Auslegungs-Vorlauf</dt>
                            <dd class="col-sm-7">{{ number_format($ergebnis['vorlauf_temp'], 0, ',', '.') }} °C</dd>
                            <dt class="col-sm-5"><strong>Jahresarbeitszahl (JAZ)</strong></dt>
                            <dd class="col-sm-7"><strong>{{ number_format($ergebnis['jaz'], 2, ',', '.') }}</strong> <small class="text-muted">(Richtwert)</small></dd>
                        </dl>
                    </div>
                </div>

                {{-- Verbrauch + Kosten --}}
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Jahresverbrauch &amp; Warmwasser</h4></div>
                    <div class="card-body">
                        <dl class="row mb-1">
                            <dt class="col-sm-6">Jahres-Heizarbeit (Heizlast × {{ $ergebnis['b_vh'] }} h)</dt>
                            <dd class="col-sm-6">{{ $kwh($ergebnis['q_heiz_kwh']) }}</dd>
                            @if ($ergebnis['ww_mit_wp'])
                                <dt class="col-sm-6">Warmwasser-Wärmebedarf</dt>
                                <dd class="col-sm-6">{{ $kwh($ergebnis['q_ww_kwh']) }}</dd>
                            @endif
                            <dt class="col-sm-6"><strong>WP-Stromverbrauch</strong></dt>
                            <dd class="col-sm-6"><strong>{{ $kwh($ergebnis['strom_kwh']) }}</strong> / Jahr</dd>
                            <dt class="col-sm-6">Stromkosten <small class="text-muted">({{ number_format($ergebnis['strompreis'], 2, ',', '.') }} €/kWh)</small></dt>
                            <dd class="col-sm-6"><strong>{{ $eur($ergebnis['stromkosten_jahr']) }}</strong> / Jahr</dd>
                        </dl>
                        <hr>
                        <dl class="row mb-0">
                            <dt class="col-sm-6">Warmwasser-Komfort</dt>
                            <dd class="col-sm-6">{{ ucfirst($ergebnis['ww']['komfort']) }} <small class="text-muted">({{ $ergebnis['ww']['ww_kwh_pa_pro_person'] }} kWh/Person·a)</small></dd>
                            <dt class="col-sm-6">Empf. Speichervolumen</dt>
                            <dd class="col-sm-6">{{ $ergebnis['ww']['speicher_liter'] }} Liter</dd>
                        </dl>
                    </div>
                </div>

                {{-- Plausibilisierung (nur wenn Verbrauch angegeben) --}}
                @if (!empty($ergebnis['verbrauch_plausi']))
                    @php $vp = $ergebnis['verbrauch_plausi']; @endphp
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Plausibilisierung (Verbrauchsmethode)</h4></div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-6">Endenergie / Jahr</dt>
                                <dd class="col-sm-6">{{ $kwh($vp['endenergie_kwh']) }} <small class="text-muted">(η {{ number_format($vp['nutzungsgrad']*100,0) }} %)</small></dd>
                                <dt class="col-sm-6">Nutzwärme / Jahr</dt>
                                <dd class="col-sm-6">{{ $kwh($vp['q_nutz_kwh']) }}</dd>
                                <dt class="col-sm-6">Heizlast (Gegenprobe)</dt>
                                <dd class="col-sm-6">
                                    {{ number_format($vp['phi_hl_kw'], 1, ',', '.') }} kW
                                    <small class="text-muted">vs. Eingabe {{ number_format($ergebnis['heizlast_kw'], 1, ',', '.') }} kW</small>
                                </dd>
                            </dl>
                        </div>
                    </div>
                @endif

                <p class="text-muted"><small>
                    JAZ und Verbrauch sind Richtwerte nach VDI 4650/DIN EN 12831; Förderung nach BEG EM / KfW 458
                    (Stand 2024/25, max. 70 %). Keine Förderzusage — maßgeblich sind Antrag und Bescheid.
                </small></p>
            @else
                <div class="card">
                    <div class="card-body text-muted">
                        Bitte Wärmepumpe wählen, Heizlast, Heizsystem, Personen und Investition eingeben und
                        „Auslegen &amp; berechnen" klicken. Das Ergebnis (JAZ, Stromverbrauch, Warmwasser und die
                        KfW-/BEG-Wirtschaftlichkeit) erscheint hier.
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Optionale Verschönerung des WP-Selects via Select2 (global in ticket geladen). Reine Kosmetik,
    // das Ergebnis ist server-gerendert und funktioniert ohne JS.
    $(function () {
        if ($.fn && $.fn.select2) {
            $('.energie-select').select2({ width: '100%' });
        }
    });
</script>
@endpush
