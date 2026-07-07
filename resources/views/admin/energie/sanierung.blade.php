@extends('admin.layouts.app')

@section('title', 'Sanierungs-Wirtschaftlichkeit')

{{--
    Sanierungs-Wirtschaftlichkeits-Rechner (Strang Energie).
    Server-gerendert: Blade + Bootstrap/Vuexy + minimal jQuery. KEIN Alpine (CLAUDE.md).
    Vorher/Nachher-Vergleich liegt serverseitig im SanierungsWirtschaftlichkeitService;
    hier nur Eingabeformular (dynamische Bauteil-/Maßnahmen-Zeilen) + Ergebnis-Anzeige.
--}}

@php
    $bauteile = old('bauteile', data_get($alt ?? [], 'bauteile', []));
    if (empty($bauteile)) {
        $bauteile = [['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => '', 'u_wert' => '']];
    }
    $massnahmen = old('massnahmen', data_get($alt ?? [], 'massnahmen', []));
    $annahmen = old('annahmen', data_get($alt ?? [], 'annahmen', []));
    $projekt = old('projekt', data_get($alt ?? [], 'projekt', []));
    $raum = old('raum', data_get($alt ?? [], 'raum', []));

    $fmt = fn ($v, $d = 0) => $v === null ? '—' : number_format((float) $v, $d, ',', '.');
@endphp

@section('content')
<section class="energie-sanierung">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1">
            <h2 class="content-header-title">Sanierungs-Wirtschaftlichkeit</h2>
            <p class="text-muted mb-0">
                Vorher/Nachher-Vergleich einer Gebäudehülle: Heizlast Φ_HL (kW), Jahresheizwärmebedarf
                Q_a (kWh/a), benötigte Vorlauftemperatur, BAFA-Hülle-Förderung und Amortisation. Einzonen-Modell
                (Gebäude = ein Raum). Vorauslegung — kein Ersatz für eine Energieberatung nach GEG/BEG.
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
    <form method="POST" action="{{ route('energie.sanierung.berechnen') }}" id="sanierung-form">
        @csrf

        {{-- Gebäude-Basisdaten --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Gebäude-Basisdaten</h4></div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4 col-12">
                        <label>Projektname</label>
                        <input type="text" class="form-control" name="projekt[name]"
                            value="{{ data_get($projekt, 'name') }}" placeholder="z. B. EFH Musterstraße">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Standort-PLZ</label>
                        <input type="text" class="form-control" name="projekt[standort_plz]"
                            value="{{ data_get($projekt, 'standort_plz') }}" placeholder="80331">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Baujahr</label>
                        <input type="number" class="form-control" name="projekt[baujahr]"
                            value="{{ data_get($projekt, 'baujahr') }}" min="1800" max="2100" placeholder="1975">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Ziel-Vorlauf °C</label>
                        <input type="number" step="0.5" class="form-control" name="projekt[ziel_vorlauf_c]"
                            value="{{ data_get($projekt, 'ziel_vorlauf_c') }}" placeholder="35">
                    </div>
                    <div class="form-group col-md-2 col-6">
                        <label>Spreizung K</label>
                        <input type="number" step="0.5" class="form-control" name="projekt[spreizung_k]"
                            value="{{ data_get($projekt, 'spreizung_k') }}" placeholder="7">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-6">
                        <label>Grundfläche m²</label>
                        <input type="number" step="0.1" class="form-control" name="raum[grundflaeche_m2]"
                            value="{{ data_get($raum, 'grundflaeche_m2') }}" min="1" required placeholder="140">
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

        {{-- Maßnahmen --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Sanierungs-Maßnahmen</h4>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-massnahme">+ Maßnahme</button>
            </div>
            <div class="card-body">
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
                                        <select class="form-control form-control-sm" name="massnahmen[{{ $i }}][ziel_typ]" required>
                                            @foreach ($bauteilTypen as $t)
                                                <option value="{{ $t }}" @selected(data_get($m, 'ziel_typ') === $t)>{{ ucfirst($t) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0.01" max="10" class="form-control form-control-sm"
                                        name="massnahmen[{{ $i }}][u_neu]" value="{{ data_get($m, 'u_neu') }}" required></td>
                                    <td><input type="number" step="1" min="0" class="form-control form-control-sm"
                                        name="massnahmen[{{ $i }}][kosten_brutto]" value="{{ data_get($m, 'kosten_brutto') }}" required></td>
                                    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-row">&times;</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Je Maßnahme wird der neue U-Wert auf ALLE Bauteile des gewählten Typs angewandt.</small>
            </div>
        </div>

        {{-- Annahmen --}}
        <div class="card">
            <div class="card-header"><h4 class="card-title">Wirtschaftlichkeits-Annahmen</h4></div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="form-group col-md-3 col-6">
                        <label>Energiepreis ct/kWh</label>
                        <input type="number" step="0.1" min="0" class="form-control" name="annahmen[energiepreis_ct_kwh]"
                            value="{{ data_get($annahmen, 'energiepreis_ct_kwh', 12) }}">
                    </div>
                    <div class="form-group col-md-3 col-6">
                        <label>Anzahl WE</label>
                        <input type="number" step="1" min="1" class="form-control" name="annahmen[anzahl_we]"
                            value="{{ data_get($annahmen, 'anzahl_we', 1) }}">
                    </div>
                    <div class="form-group col-md-3 col-12">
                        <div class="custom-control custom-checkbox mt-1">
                            <input type="checkbox" class="custom-control-input" id="isfp" name="annahmen[isfp]" value="1"
                                @checked(data_get($annahmen, 'isfp'))>
                            <label class="custom-control-label" for="isfp">iSFP-Bonus (+5 %)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap mb-2" style="gap:.5rem;">
            <button type="submit" class="btn btn-primary">Wirtschaftlichkeit berechnen</button>
            <button type="submit" class="btn btn-outline-secondary" formaction="{{ route('energie.sanierung.dokument') }}" formtarget="_blank">
                Als Dokument / PDF
            </button>
        </div>
    </form>

    {{-- ==================== ERGEBNIS ==================== --}}
    @if (!empty($ergebnis))
        @php
            $d = $ergebnis['delta'];
            $w = $ergebnis['wirtschaftlichkeit'];
            $f = $ergebnis['foerderung'];
            $ist = $ergebnis['ist'];
            $na = $ergebnis['nachher'];
        @endphp

        <div class="card border-primary">
            <div class="card-header"><h4 class="card-title">Ergebnis</h4></div>
            <div class="card-body">

                {{-- KPI-Kacheln --}}
                <div class="row">
                    <div class="col-md-3 col-6 mb-1">
                        <div class="p-1 border rounded h-100">
                            <div class="text-muted"><small>Heizlast-Reduktion</small></div>
                            <div class="h3 mb-0">{{ $fmt($d['phi_hl_kw'], 2) }} kW</div>
                            <small class="text-success">−{{ $fmt($d['phi_hl_pct'], 1) }} %</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-1">
                        <div class="p-1 border rounded h-100">
                            <div class="text-muted"><small>Einsparung</small></div>
                            <div class="h3 mb-0">{{ $fmt($w['einsparung_eur_a']) }} €/a</div>
                            <small class="text-muted">−{{ $fmt($d['q_a_pct'], 1) }} % Q_a</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-1">
                        <div class="p-1 border rounded h-100">
                            <div class="text-muted"><small>Amortisation</small></div>
                            <div class="h3 mb-0">{{ $w['amortisation_jahre'] === null ? '—' : $fmt($w['amortisation_jahre'], 1).' J' }}</div>
                            <small class="text-muted">Gewinn 30 J: {{ $fmt($w['gewinn_30j_eur']) }} €</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-1">
                        <div class="p-1 border rounded h-100">
                            <div class="text-muted"><small>BAFA-Zuschuss</small></div>
                            <div class="h3 mb-0">{{ $fmt($f['bafa_huelle_eur']) }} €</div>
                            <small class="text-muted">Quote {{ $fmt($f['quote_pct'], 0) }} %</small>
                        </div>
                    </div>
                </div>

                @if (!empty($f['deckel_hinweis']))
                    <div class="alert alert-warning py-1 mb-1"><small>{{ $f['deckel_hinweis'] }}</small></div>
                @endif

                {{-- Ist / Nachher --}}
                <div class="row mt-1">
                    <div class="col-lg-7 col-12">
                        <h5>Vorher / Nachher</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Kennwert</th><th class="text-right">IST</th><th class="text-right">Nachher</th><th class="text-right">Δ</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Heizlast Φ_HL</td>
                                        <td class="text-right">{{ $fmt($ist['phi_hl_kw'], 2) }} kW</td>
                                        <td class="text-right">{{ $fmt($na['phi_hl_kw'], 2) }} kW</td>
                                        <td class="text-right text-success">−{{ $fmt($d['phi_hl_kw'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jahresheizwärme Q_a</td>
                                        <td class="text-right">{{ $fmt($ist['q_a_kwh']) }} kWh</td>
                                        <td class="text-right">{{ $fmt($na['q_a_kwh']) }} kWh</td>
                                        <td class="text-right text-success">−{{ $fmt($d['q_a_kwh']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Benötigter Vorlauf</td>
                                        <td class="text-right">{{ $ist['vorlauf_c'] === null ? '—' : $fmt($ist['vorlauf_c'], 1).' °C' }}</td>
                                        <td class="text-right">{{ $na['vorlauf_c'] === null ? '—' : $fmt($na['vorlauf_c'], 1).' °C' }}</td>
                                        <td class="text-right">{{ $d['vorlauf_k'] === null ? '—' : '−'.$fmt($d['vorlauf_k'], 1).' K' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Transmission+Lüftung H</td>
                                        <td class="text-right">{{ $fmt($ist['h_w_k'], 1) }} W/K</td>
                                        <td class="text-right">{{ $fmt($na['h_w_k'], 1) }} W/K</td>
                                        <td class="text-right">—</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if (!empty($na['vorlauf_hinweis']))
                            <small class="text-muted d-block mb-1">{{ $na['vorlauf_hinweis'] }}</small>
                        @endif
                    </div>

                    {{-- Wirtschaftlichkeit + Förderung --}}
                    <div class="col-lg-5 col-12">
                        <h5>Wirtschaftlichkeit</h5>
                        <dl class="row mb-0">
                            <dt class="col-7">Investition</dt><dd class="col-5 text-right">{{ $fmt($w['investition_eur']) }} €</dd>
                            <dt class="col-7">BAFA-Förderung</dt><dd class="col-5 text-right text-success">−{{ $fmt($w['foerderung_eur']) }} €</dd>
                            <dt class="col-7">Netto-Investition</dt><dd class="col-5 text-right"><strong>{{ $fmt($w['netto_investition_eur']) }} €</strong></dd>
                            <dt class="col-7">Einsparung / Jahr</dt><dd class="col-5 text-right">{{ $fmt($w['einsparung_eur_a']) }} €</dd>
                            <dt class="col-7">Amortisation</dt><dd class="col-5 text-right">{{ $w['amortisation_jahre'] === null ? '—' : $fmt($w['amortisation_jahre'], 1).' J' }}</dd>
                            <dt class="col-7">Gewinn 30 Jahre</dt><dd class="col-5 text-right">{{ $fmt($w['gewinn_30j_eur']) }} €</dd>
                        </dl>
                        <hr>
                        <small class="text-muted">
                            Förderfähig: {{ $fmt($f['foerderfaehig_eur']) }} € · Deckel: {{ $fmt($f['deckel_eur']) }} € ·
                            Energiepreis: {{ $fmt($ergebnis['annahmen']['energiepreis_ct_kwh'], 1) }} ct/kWh ·
                            Erzeuger-Faktor: {{ $fmt($ergebnis['annahmen']['erzeuger_faktor'], 2) }}
                        </small>
                    </div>
                </div>

                {{-- Räume --}}
                @if (!empty($ergebnis['raeume']))
                    <h5 class="mt-1">Zonen</h5>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr><th>Zone</th><th class="text-right">Φ_HL IST</th><th class="text-right">Φ_HL Nachher</th><th class="text-right">Vorlauf IST</th><th class="text-right">Vorlauf Nachher</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($ergebnis['raeume'] as $r)
                                    <tr>
                                        <td>{{ $r['name'] }}</td>
                                        <td class="text-right">{{ $fmt($r['phi_hl_ist_w']) }} W</td>
                                        <td class="text-right">{{ $fmt($r['phi_hl_nachher_w']) }} W</td>
                                        <td class="text-right">{{ $r['min_vorlauf_ist_c'] === null ? '—' : $fmt($r['min_vorlauf_ist_c'], 1).' °C' }}</td>
                                        <td class="text-right">{{ $r['min_vorlauf_nachher_c'] === null ? '—' : $fmt($r['min_vorlauf_nachher_c'], 1).' °C' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <p class="text-muted mt-1 mb-0"><small>{{ $ergebnis['annahmen']['hinweis'] ?? '' }}</small></p>
            </div>
        </div>
    @endif

</section>
@endsection

@push('scripts')
<script>
    // Dynamische Zeilen für Bauteile + Maßnahmen (jQuery, KEIN Alpine — CLAUDE.md).
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

        function massnahmeRow(i) {
            return '<tr class="massnahme-row">'
                + '<td><select class="form-control form-control-sm" name="massnahmen[' + i + '][ziel_typ]" required>' + optionList(bauteilTypen, 'wand') + '</select></td>'
                + '<td><input type="number" step="0.01" min="0.01" max="10" class="form-control form-control-sm" name="massnahmen[' + i + '][u_neu]" required></td>'
                + '<td><input type="number" step="1" min="0" class="form-control form-control-sm" name="massnahmen[' + i + '][kosten_brutto]" required></td>'
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

        // Zeile entfernen (Bauteile: mind. 1 behalten).
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
