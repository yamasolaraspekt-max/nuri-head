@extends('admin.layouts.app')

@section('title', 'Fußbodenheizung-Check')

{{--
    Fußbodenheizung-Schnellcheck (Strang Energie) nach DIN EN 1264.
    Server-gerendert: Blade + Bootstrap/Vuexy + minimal jQuery. KEIN Alpine (CLAUDE.md).
    Rechnung liegt serverseitig im FussbodenheizungService (portierter Kern); hier nur Eingabe + Anzeige.
--}}

@php
    $ampelMap = [
        'gruen' => ['class' => 'badge-success', 'text' => 'Grün — Bedarf gedeckt'],
        'gelb' => ['class' => 'badge-warning', 'text' => 'Gelb — knapp (≥ 90 %)'],
        'rot' => ['class' => 'badge-danger', 'text' => 'Rot — nicht gedeckt'],
        'na' => ['class' => 'badge-secondary', 'text' => '—'],
    ];
    $ampelFallback = ['class' => 'badge-secondary', 'text' => '—'];
@endphp

@section('content')
<section class="energie-fussboden-check">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1">
            <h2 class="content-header-title">Fußbodenheizung-Check</h2>
            <p class="text-muted mb-0">
                Schnellcheck nach DIN EN 1264: prüft aus Raumfläche und Heizlast, ob die Flächenheizung
                den Wärmebedarf bei niedriger Vorlauftemperatur deckt — mit spezifischer Leistung q [W/m²],
                Mindest-Vorlauftemperatur, Heizkreisen und Ampel-Status. Vorauslegung auf Basis von
                EN-1264-Richtwerten — Endauslegung durch die Fachplanung.
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
            <div class="card">
                <div class="card-header"><h4 class="card-title">Eingabe</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('energie.fussboden-check.berechnen') }}">
                        @csrf

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="flaeche_m2">Fläche [m²]</label>
                                <input type="number" step="0.1" min="1" max="1000" class="form-control"
                                    id="flaeche_m2" name="flaeche_m2" value="{{ $eingabe['flaeche_m2'] ?? 20 }}" required>
                            </div>
                            <div class="form-group col-6">
                                <label for="heizlast_w">Heizlast [W]</label>
                                <input type="number" step="1" min="10" max="300000" class="form-control"
                                    id="heizlast_w" name="heizlast_w" value="{{ $eingabe['heizlast_w'] ?? 1500 }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="raumtemp_c">Raumtemperatur [°C]</label>
                                <input type="number" step="0.5" min="10" max="30" class="form-control"
                                    id="raumtemp_c" name="raumtemp_c" value="{{ $eingabe['raumtemp_c'] ?? 20 }}">
                            </div>
                            <div class="form-group col-6">
                                <label for="vorlauf_c">Vorlauf [°C]</label>
                                <input type="number" step="0.5" min="25" max="60" class="form-control"
                                    id="vorlauf_c" name="vorlauf_c" value="{{ $eingabe['vorlauf_c'] ?? 35 }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="spreizung_k">Spreizung [K]</label>
                                <input type="number" step="0.5" min="3" max="15" class="form-control"
                                    id="spreizung_k" name="spreizung_k" value="{{ $eingabe['spreizung_k'] ?? 5 }}">
                            </div>
                            <div class="form-group col-6">
                                <label for="max_oberflaeche_c">Max. Oberflächentemp. [°C]</label>
                                <input type="number" step="0.5" min="25" max="35" class="form-control"
                                    id="max_oberflaeche_c" name="max_oberflaeche_c"
                                    value="{{ $eingabe['max_oberflaeche_c'] ?? 29 }}">
                                <small class="text-muted">29 Aufenthalt · 33 Bad</small>
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-muted mb-1">FBH-Parameter (DIN EN 1264)</h6>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="rohr_aussen_mm">Rohr-Außen-Ø [mm]</label>
                                <input type="number" step="0.5" min="10" max="25" class="form-control"
                                    id="rohr_aussen_mm" name="rohr_aussen_mm" value="{{ $eingabe['rohr_aussen_mm'] ?? 16 }}">
                            </div>
                            <div class="form-group col-6">
                                <label for="verlegeabstand_mm">Verlegeabstand [mm]</label>
                                <input type="number" step="5" min="50" max="400" class="form-control"
                                    id="verlegeabstand_mm" name="verlegeabstand_mm"
                                    value="{{ $eingabe['verlegeabstand_mm'] ?? 100 }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="bodenbelag_r">Bodenbelag R [m²K/W]</label>
                                <input type="number" step="0.01" min="0" max="0.25" class="form-control"
                                    id="bodenbelag_r" name="bodenbelag_r" value="{{ $eingabe['bodenbelag_r'] ?? 0 }}">
                                <small class="text-muted">0 Fliese · 0,10 Parkett · 0,15 Teppich</small>
                            </div>
                            <div class="form-group col-6">
                                <label for="estrich_ueber_mm">Estrich über Rohr [mm]</label>
                                <input type="number" step="1" min="20" max="100" class="form-control"
                                    id="estrich_ueber_mm" name="estrich_ueber_mm"
                                    value="{{ $eingabe['estrich_ueber_mm'] ?? 45 }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Prüfen</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Ergebnis --}}
        <div class="col-lg-7 col-12">
            @if (!empty($ergebnis))
                @php
                    $fbh = $ergebnis['fbh'];
                    $a = $ampelMap[$fbh['status'] ?? 'na'] ?? $ampelFallback;
                @endphp

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title mb-0">Ergebnis</h4>
                        <span class="badge {{ $a['class'] }}">{{ $a['text'] }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-2">
                            <div class="col-4">
                                <h3 class="mb-0">{{ number_format($fbh['q_eff_w_m2'], 1, ',', '.') }}</h3>
                                <small class="text-muted">q effektiv [W/m²]</small>
                            </div>
                            <div class="col-4">
                                <h3 class="mb-0">{{ number_format($ergebnis['raum']['bedarf_w_m2'], 0, ',', '.') }}</h3>
                                <small class="text-muted">Bedarf [W/m²]</small>
                            </div>
                            <div class="col-4">
                                <h3 class="mb-0">
                                    @if ($fbh['min_vorlauf_c'] !== null)
                                        {{ number_format($fbh['min_vorlauf_c'], 1, ',', '.') }} °C
                                    @else
                                        —
                                    @endif
                                </h3>
                                <small class="text-muted">Min. Vorlauf</small>
                            </div>
                        </div>

                        @if (!empty($fbh['oberflaeche_ueberschritten']))
                            <div class="alert alert-warning py-1 mb-2">
                                Bedarf über der zulässigen Oberflächen-Grenzleistung
                                ({{ number_format($fbh['q_max_oberflaeche_w_m2'], 1, ',', '.') }} W/m²) —
                                Fläche allein unzureichend (EN-1264-Grenzkurve).
                            </div>
                        @endif

                        <dl class="row mb-0">
                            <dt class="col-sm-6">Heizlast Raum</dt>
                            <dd class="col-sm-6">{{ number_format($ergebnis['raum']['heizlast_w'], 0, ',', '.') }} W</dd>

                            <dt class="col-sm-6">Deckung durch FBH</dt>
                            <dd class="col-sm-6">
                                {{ $fbh['q_real_w'] }} W
                                @if ($fbh['deckung_pct'] !== null)
                                    ({{ $fbh['deckung_pct'] }} %)
                                @endif
                            </dd>

                            <dt class="col-sm-6">Max. Oberflächen-Leistung</dt>
                            <dd class="col-sm-6">{{ number_format($fbh['q_max_oberflaeche_w_m2'], 1, ',', '.') }} W/m²</dd>

                            <dt class="col-sm-6">Rohr-Gesamtlänge</dt>
                            <dd class="col-sm-6">{{ $fbh['gesamtlaenge_m'] }} m ({{ number_format($fbh['laenge_pro_m2'], 1, ',', '.') }} m/m²)</dd>

                            <dt class="col-sm-6">Heizkreise</dt>
                            <dd class="col-sm-6">{{ $fbh['heizkreise'] }} × {{ $fbh['laenge_pro_kreis_m'] }} m · {{ $fbh['w_pro_kreis'] }} W/Kreis</dd>

                            <dt class="col-sm-6">Gedeckte Heizarbeit</dt>
                            <dd class="col-sm-6">{{ number_format($fbh['gedeckte_kwh_a'], 0, ',', '.') }} / {{ number_format($ergebnis['raum']['kwh_a'], 0, ',', '.') }} kWh/a</dd>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4 class="card-title">Leistungstabelle</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Vorlauf [°C]</th>
                                        <th>Rücklauf [°C]</th>
                                        <th class="text-right">q [W/m²]</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ergebnis['tabelle'] as $zeile)
                                        <tr>
                                            <td>{{ $zeile['vorlauf'] }}</td>
                                            <td>{{ $zeile['ruecklauf'] }}</td>
                                            <td class="text-right">{{ number_format($zeile['q_w_m2'], 1, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">
                            Spezifische Flächenleistung bei gängigen Vorlauf-/Rücklaufpaaren,
                            begrenzt durch die zulässige Oberflächentemperatur.
                        </small>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-muted">
                        Parameter links eingeben und <strong>Prüfen</strong> — das Ergebnis (q pro m²,
                        Mindest-Vorlauf, Ampel-Status, Leistungstabelle) erscheint hier.
                    </div>
                </div>
            @endif
        </div>
    </div>

</section>
@endsection
