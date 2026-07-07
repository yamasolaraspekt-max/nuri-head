@extends('admin.layouts.app')

@section('title', 'Ist-Heizlast-Rechner')

{{--
    Ist-Heizlast-Rechner (Strang Energie).
    Reduzierte Variante des Sanierungs-Rechners OHNE Maßnahmen: nur die reine IST-Heizlast.
    Server-gerendert: Blade + Bootstrap/Vuexy + minimal jQuery. KEIN Alpine (CLAUDE.md).
    Rechenkern liegt serverseitig im HeizlastProjektService; hier nur Eingabeformular
    (dynamische Bauteil-Zeilen) + Ergebnis-Anzeige (Auslegungsheizlast, Vorlauf, je Raum).
--}}

@php
    $bauteile = old('bauteile', data_get($alt ?? [], 'bauteile', []));
    if (empty($bauteile)) {
        $bauteile = [['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => '', 'u_wert' => '']];
    }
    $projekt = old('projekt', data_get($alt ?? [], 'projekt', []));
    $raum = old('raum', data_get($alt ?? [], 'raum', []));

    $fmt = fn ($v, $d = 0) => $v === null ? '—' : number_format((float) $v, $d, ',', '.');
@endphp

@section('content')
<section class="energie-heizlast">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1">
            <h2 class="content-header-title">Ist-Heizlast-Rechner</h2>
            <p class="text-muted mb-0">
                Gebäude-Eingabe → IST-Heizlast nach DIN EN 12831-1. Einzonen-Modell (Gebäude = ein Raum):
                Auslegungs- und Standardheizlast (kW), spezifische Heizlast, benötigte Vorlauftemperatur und
                optional passende Wärmepumpen. Vorauslegung — kein Ersatz für eine Norm-Heizlastberechnung.
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
    <form method="POST" action="{{ route('energie.heizlast.berechnen') }}" id="heizlast-form">
        @csrf

        {{-- Gebäude-Basisdaten --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Gebäude-Basisdaten</h4></div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-3 col-6">
                        <label>Standort-PLZ <small class="text-muted">(opt.)</small></label>
                        <input type="text" class="form-control" name="projekt[standort_plz]"
                            value="{{ data_get($projekt, 'standort_plz') }}" placeholder="80331">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Baujahr <small class="text-muted">(opt.)</small></label>
                        <input type="number" class="form-control" name="projekt[baujahr]"
                            value="{{ data_get($projekt, 'baujahr') }}" min="1800" max="2100" placeholder="1975">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Ziel-Vorlauf °C</label>
                        <input type="number" step="0.5" class="form-control" name="projekt[ziel_vorlauf_c]"
                            value="{{ data_get($projekt, 'ziel_vorlauf_c') }}" placeholder="55">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Spreizung K</label>
                        <input type="number" step="0.5" class="form-control" name="projekt[spreizung_k]"
                            value="{{ data_get($projekt, 'spreizung_k') }}" placeholder="7">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-6">
                        <label>Grundfläche m²</label>
                        <input type="number" step="0.1" class="form-control" name="raum[grundflaeche_m2]"
                            value="{{ data_get($raum, 'grundflaeche_m2') }}" min="1" required placeholder="120">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Raumhöhe m</label>
                        <input type="number" step="0.1" class="form-control" name="raum[hoehe_m]"
                            value="{{ data_get($raum, 'hoehe_m', '2.5') }}" min="1" max="10" required placeholder="2.5">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Innentemp. °C <small class="text-muted">(opt.)</small></label>
                        <input type="number" step="0.5" class="form-control" name="raum[theta_int_c]"
                            value="{{ data_get($raum, 'theta_int_c') }}" placeholder="20">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Luftwechsel 1/h <small class="text-muted">(opt.)</small></label>
                        <input type="number" step="0.05" class="form-control" name="raum[luftwechsel_1h]"
                            value="{{ data_get($raum, 'luftwechsel_1h') }}" placeholder="0.5">
                    </div>
                </div>
            </div>
        </div>

        {{-- Hüllbauteile --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Hüllbauteile (IST-Zustand)</h4>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-bauteil">+ Bauteil</button>
            </div>
            <div class="card-body">
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
                                        <select class="form-control form-control-sm" name="bauteile[{{ $i }}][typ]" required>
                                            @foreach ($bauteilTypen as $t)
                                                <option value="{{ $t }}" @selected(data_get($b, 'typ') === $t)>{{ ucfirst($t) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm" name="bauteile[{{ $i }}][grenzflaeche]" required>
                                            @foreach ($grenzflaechen as $g)
                                                <option value="{{ $g }}" @selected(data_get($b, 'grenzflaeche') === $g)>{{ ucfirst($g) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.1" min="0.1" class="form-control form-control-sm"
                                        name="bauteile[{{ $i }}][flaeche_m2]" value="{{ data_get($b, 'flaeche_m2') }}" required></td>
                                    <td><input type="number" step="0.01" min="0.01" max="10" class="form-control form-control-sm"
                                        name="bauteile[{{ $i }}][u_wert]" value="{{ data_get($b, 'u_wert') }}" required></td>
                                    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-row">&times;</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Ein Gebäude als eine Zone. Je Bauteil den aktuellen U-Wert erfassen.</small>
            </div>
        </div>

        <div class="d-flex flex-wrap mb-2" style="gap:.5rem;">
            <button type="submit" class="btn btn-primary">Heizlast berechnen</button>
        </div>
    </form>

    {{-- ==================== ERGEBNIS ==================== --}}
    @if (!empty($ergebnis))
        @php
            $g = $ergebnis['gebaeude'];
            $v = $ergebnis['vorlauf'] ?? [];
            $plausi = $g['plausi'] ?? null;
        @endphp

        <div class="card">
            <div class="card-header"><h4 class="card-title">Ergebnis — Ist-Heizlast</h4></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6 mb-1">
                        <div class="text-muted"><small>Auslegungsheizlast</small></div>
                        <h3 class="mb-0">{{ $fmt($g['auslegungsheizlast_kw'], 2) }} kW</h3>
                    </div>
                    <div class="col-md-3 col-6 mb-1">
                        <div class="text-muted"><small>Standardheizlast</small></div>
                        <h3 class="mb-0">{{ $fmt($g['standardheizlast_kw'], 2) }} kW</h3>
                    </div>
                    <div class="col-md-3 col-6 mb-1">
                        <div class="text-muted"><small>Spezifische Heizlast</small></div>
                        <h3 class="mb-0">{{ $fmt($g['spezifische_heizlast_w_m2'], 1) }} W/m²</h3>
                    </div>
                    <div class="col-md-3 col-6 mb-1">
                        <div class="text-muted"><small>Benötigter Vorlauf</small></div>
                        <h3 class="mb-0">
                            @if (($v['erfasst'] ?? false) && ($v['benoetigte_max_vorlauftemp_c'] ?? null) !== null)
                                {{ $fmt($v['benoetigte_max_vorlauftemp_c'], 1) }} °C
                            @else
                                Ziel {{ $fmt($v['ziel_vorlauf_c'] ?? null, 1) }} °C
                            @endif
                        </h3>
                    </div>
                </div>

                @if (!empty($plausi))
                    <p class="text-muted mt-1 mb-0"><small>Plausibilität: {{ $plausi }} · Norm-Außentemperatur θe {{ $fmt($ergebnis['norm_aussentemp_c'], 1) }} °C</small></p>
                @endif

                {{-- Je Raum (Einzonen-Modell: i. d. R. eine Zeile „Gebäude") --}}
                <div class="table-responsive mt-2">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Raum</th>
                                <th class="text-right">Grundfläche</th>
                                <th class="text-right">Standardheizlast</th>
                                <th class="text-right">Auslegungsheizlast</th>
                                <th class="text-right">Spez. Heizlast</th>
                                <th class="text-right">Min. Vorlauf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ergebnis['raeume'] as $r)
                                <tr>
                                    <td>{{ $r['name'] ?: '—' }}</td>
                                    <td class="text-right">{{ $fmt($r['grundflaeche_m2'], 1) }} m²</td>
                                    <td class="text-right">{{ $fmt($r['standardheizlast_w']) }} W</td>
                                    <td class="text-right">{{ $fmt($r['auslegungsheizlast_w']) }} W</td>
                                    <td class="text-right">{{ $fmt($r['spezifische_heizlast_w_m2'], 1) }} W/m²</td>
                                    <td class="text-right">{{ ($r['min_vorlauf_c'] ?? null) === null ? '—' : $fmt($r['min_vorlauf_c'], 1).' °C' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Optional: passende Wärmepumpen --}}
        @if (!empty($wp) && !empty($wp['geraete']))
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Passende Wärmepumpen</h4>
                    @if (!empty($wp['design_punkt']))
                        <small class="text-muted">Auslegungspunkt {{ $wp['design_punkt'] }}</small>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Hersteller / Modell</th>
                                    <th class="text-right">Leistung</th>
                                    <th class="text-right">COP</th>
                                    <th class="text-right">SCOP</th>
                                    <th class="text-right">Deckung</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wp['geraete'] as $geraet)
                                    <tr>
                                        <td>{{ $geraet['hersteller'] }} {{ $geraet['serie'] }} {{ $geraet['modell'] }}</td>
                                        <td class="text-right">{{ $fmt($geraet['leistung_kw'], 2) }} kW</td>
                                        <td class="text-right">{{ $geraet['cop'] === null ? '—' : $fmt($geraet['cop'], 2) }}</td>
                                        <td class="text-right">{{ $geraet['scop'] === null ? '—' : $fmt($geraet['scop'], 2) }}</td>
                                        <td class="text-right">{{ $fmt($geraet['deckung_pct']) }} %</td>
                                        <td>
                                            <span class="badge badge-light">{{ $geraet['status'] }}</span>
                                            @if (!empty($geraet['hinweis']))
                                                <small class="text-muted d-block">{{ $geraet['hinweis'] }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if (!empty($wp['hinweis']))
                        <p class="text-muted mt-1 mb-0"><small>{{ $wp['hinweis'] }}</small></p>
                    @endif
                </div>
            </div>
        @elseif (!empty($wp) && !empty($wp['hinweis']))
            <div class="alert alert-info">{{ $wp['hinweis'] }}</div>
        @endif
    @endif

</section>
@endsection

@push('scripts')
<script>
    // Dynamische Bauteil-Zeilen (jQuery, KEIN Alpine — CLAUDE.md).
    $(function () {
        var bauteilTypen = @json($bauteilTypen);
        var grenzflaechen = @json($grenzflaechen);

        function optionList(values, sel) {
            return values.map(function (v) {
                var label = v.charAt(0).toUpperCase() + v.slice(1);
                return '<option value="' + v + '"' + (v === sel ? ' selected' : '') + '>' + label + '</option>';
            }).join('');
        }

        function bauteilRow(i) {
            return '<tr class="bauteil-row">'
                + '<td><select class="form-control form-control-sm" name="bauteile[' + i + '][typ]" required>' + optionList(bauteilTypen, 'wand') + '</select></td>'
                + '<td><select class="form-control form-control-sm" name="bauteile[' + i + '][grenzflaeche]" required>' + optionList(grenzflaechen, 'aussen') + '</select></td>'
                + '<td><input type="number" step="0.1" min="0.1" class="form-control form-control-sm" name="bauteile[' + i + '][flaeche_m2]" required></td>'
                + '<td><input type="number" step="0.01" min="0.01" max="10" class="form-control form-control-sm" name="bauteile[' + i + '][u_wert]" required></td>'
                + '<td><button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-row">&times;</button></td>'
                + '</tr>';
        }

        var bauteilIdx = $('#bauteil-body tr').length;

        $('#add-bauteil').on('click', function () {
            $('#bauteil-body').append(bauteilRow(bauteilIdx++));
        });

        // Zeile entfernen (mind. 1 behalten).
        $(document).on('click', '.remove-row', function () {
            var $row = $(this).closest('tr');
            if ($('#bauteil-body tr').length <= 1) {
                return;
            }
            $row.remove();
        });
    });
</script>
@endpush
