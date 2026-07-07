{{--
    Kundenfertiges Sanierungs-Wirtschaftlichkeits-Dokument (Strang Energie).
    EIGENSTÄNDIGES HTML-Dokument (KEIN @extends) — druck-/PDF-tauglich über die Browser-Druckfunktion
    („Als PDF speichern"). Reines HTML/CSS + ein window.print()-Aufruf, KEIN Alpine, KEIN Framework.
    Daten kommen fertig gerechnet aus SanierungsWirtschaftlichkeitService::berechneVergleich() (eine Wahrheit).
--}}
@php
    $eur = fn ($v) => ($v === null || $v === '') ? '–' : number_format((float) $v, 0, ',', '.').' €';
    $kwh = fn ($v) => ($v === null || $v === '') ? '–' : number_format((float) $v, 0, ',', '.').' kWh';
    $num = fn ($v, $d = 0) => ($v === null || $v === '') ? '–' : number_format((float) $v, $d, ',', '.');
    $txt = fn ($v) => ($v === null || $v === '') ? '–' : $v;

    $ist   = $ergebnis['ist'] ?? [];
    $nach  = $ergebnis['nachher'] ?? [];
    $delta = $ergebnis['delta'] ?? [];
    $raeume = $ergebnis['raeume'] ?? [];
    $f = $ergebnis['foerderung'] ?? [];
    $w = $ergebnis['wirtschaftlichkeit'] ?? [];
    $a = $ergebnis['annahmen'] ?? [];

    $preisHinweis = 'Energiepreis-Annahme '.$num($a['energiepreis_ct_kwh'] ?? null, 1).' ct/kWh';
    if (!empty($a['energiepreissteigerung_pct'])) {
        $preisHinweis .= ', Preissteigerung '.$num($a['energiepreissteigerung_pct'], 1).' % p. a.';
    }
    $preisHinweis .= '.';
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sanierungsfahrplan — Solar Aspekt</title>
<style>
    :root{--brand:#72436b;--ink:#24262b;--muted:#6b7180;--line:#e6e3dc;--raised:#faf8f3;--pos:#3f7d5a}
    *{box-sizing:border-box}
    body{margin:0;background:#eceae3;color:var(--ink);font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;font-size:12px;line-height:1.45}
    .sheet{max-width:820px;margin:18px auto;background:#fff;padding:32px 36px;box-shadow:0 2px 16px rgba(0,0,0,.08)}
    h1{font-size:20px;font-weight:800;margin:0}
    h2{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--brand);margin:22px 0 8px}
    .head{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid var(--ink);padding-bottom:12px}
    .brand{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.16em;color:var(--muted)}
    .title-sub{font-size:12px;color:var(--muted);margin-top:4px}
    .meta{text-align:right;font-size:11px;color:var(--muted);line-height:1.7}
    .meta strong{color:var(--ink)}
    .kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:16px}
    .kpi{border:1px solid var(--line);border-radius:10px;padding:10px 12px;background:var(--raised)}
    .kpi .lab{font-size:9.5px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);font-weight:700}
    .kpi .val{font-size:20px;font-weight:800;margin-top:3px;line-height:1.15}
    .kpi .val.g{color:var(--pos)}
    .kpi .sub{font-size:9.5px;color:var(--muted);margin-top:2px}
    .grid2{display:grid;grid-template-columns:1fr 1fr;gap:18px 28px}
    table{width:100%;border-collapse:collapse;font-size:12px}
    th{text-align:left;font-size:9.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);font-weight:700;padding:4px 0;border-bottom:1px solid var(--line)}
    th.r,td.r{text-align:right}
    td{padding:4px 0;border-bottom:1px solid var(--line);vertical-align:top}
    td.r{font-weight:600;white-space:nowrap;padding-left:12px}
    .fazit{margin-top:18px;border:2px solid var(--pos);background:#eef6f0;border-radius:10px;padding:14px 16px}
    .fazit h3{margin:0 0 10px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--pos)}
    .fazit table td{border-bottom:1px solid #d5e7dc}
    .fazit .sum td{border-bottom:none;border-top:2px solid var(--pos);padding-top:8px;font-size:15px;font-weight:800}
    .fazit .sum td.r{color:var(--pos)}
    .note{margin-top:16px;font-size:10px;color:var(--muted);border-top:1px solid var(--line);padding-top:10px}
    .print-btn{position:fixed;top:14px;right:14px;background:var(--brand);color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.18)}
    @media print{
        body{background:#fff}
        .noprint{display:none!important}
        .sheet{box-shadow:none;margin:0;max-width:none;padding:0}
        .kpi{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
        .fazit{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
        @page{size:A4;margin:16mm}
    }
</style>
</head>
<body>
<button type="button" class="print-btn noprint" onclick="window.print()">Drucken / als PDF speichern</button>

<div class="sheet">
    <div class="head">
        <div>
            <div class="brand">Solar Aspekt · Sanierungs-Wirtschaftlichkeit</div>
            <h1>Ihr Sanierungsfahrplan</h1>
            <div class="title-sub">Heizlast-Reduktion, Energieeinsparung &amp; Wirtschaftlichkeit inkl. BAFA-Förderung</div>
        </div>
        <div class="meta">
            Datum <strong>{{ now()->format('d.m.Y') }}</strong>
        </div>
    </div>

    {{-- KPI-Karten --}}
    <div class="kpis">
        <div class="kpi">
            <div class="lab">Heizlast-Reduktion</div>
            <div class="val">{{ $delta['phi_hl_kw'] ?? null ? $num($delta['phi_hl_kw'], 1).' kW' : '–' }}</div>
            <div class="sub">− {{ $num($delta['phi_hl_pct'] ?? null, 1) }} % geringere Heizlast</div>
        </div>
        <div class="kpi">
            <div class="lab">Energieeinsparung</div>
            <div class="val">{{ $kwh($delta['q_a_kwh'] ?? null) }}</div>
            <div class="sub">pro Jahr</div>
        </div>
        <div class="kpi">
            <div class="lab">Jährliche Ersparnis</div>
            <div class="val g">{{ $eur($w['einsparung_eur_a'] ?? null) }}</div>
            <div class="sub">Heizkosten / Jahr</div>
        </div>
        <div class="kpi">
            <div class="lab">Amortisation</div>
            <div class="val">{{ $w['amortisation_jahre'] ?? null ? $num($w['amortisation_jahre'], 1).' J.' : '–' }}</div>
            <div class="sub">nach Förderung</div>
        </div>
    </div>

    {{-- Vorher / Nachher --}}
    <h2>Vorher / Nachher</h2>
    <table>
        <tr>
            <th>Kennwert</th>
            <th class="r">Ist (unsaniert)</th>
            <th class="r">Nachher (saniert)</th>
        </tr>
        <tr>
            <td>Gebäude-Heizlast</td>
            <td class="r">{{ $ist['phi_hl_kw'] ?? null ? $num($ist['phi_hl_kw'], 1).' kW' : '–' }}</td>
            <td class="r">{{ $nach['phi_hl_kw'] ?? null ? $num($nach['phi_hl_kw'], 1).' kW' : '–' }}</td>
        </tr>
        <tr>
            <td>Jahres-Heizarbeit</td>
            <td class="r">{{ $kwh($ist['q_a_kwh'] ?? null) }}</td>
            <td class="r">{{ $kwh($nach['q_a_kwh'] ?? null) }}</td>
        </tr>
        <tr>
            <td>Min. Vorlauftemperatur</td>
            <td class="r">{{ $ist['min_vorlauf_c'] ?? null ? $num($ist['min_vorlauf_c']).' °C' : '–' }}</td>
            <td class="r">{{ $nach['min_vorlauf_c'] ?? null ? $num($nach['min_vorlauf_c']).' °C' : '–' }}</td>
        </tr>
        <tr>
            <td>Vorlauf-Absenkung</td>
            <td class="r" colspan="2">− {{ $delta['vorlauf_k'] ?? null ? $num($delta['vorlauf_k'], 1).' K' : '–' }}</td>
        </tr>
    </table>

    @if (!empty($raeume))
        <h2>Heizlast je Raum</h2>
        <table>
            <tr>
                <th>Raum</th>
                <th class="r">Ist</th>
                <th class="r">Nachher</th>
            </tr>
            @foreach ($raeume as $r)
                <tr>
                    <td>{{ $txt($r['name'] ?? null) }}</td>
                    <td class="r">{{ isset($r['phi_hl_ist_w']) && $r['phi_hl_ist_w'] !== null ? $num($r['phi_hl_ist_w']).' W' : '–' }}</td>
                    <td class="r">{{ isset($r['phi_hl_nachher_w']) && $r['phi_hl_nachher_w'] !== null ? $num($r['phi_hl_nachher_w']).' W' : '–' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- Wirtschaftlichkeit / Fazit --}}
    <div class="fazit">
        <h3>Wirtschaftlichkeit &amp; Förderung</h3>
        <table>
            <tr>
                <td>Investition (brutto)</td>
                <td class="r">{{ $eur($w['investition_eur'] ?? null) }}</td>
            </tr>
            <tr>
                <td>BAFA-Zuschuss <span style="color:var(--muted)">({{ $num($f['quote_pct'] ?? null, 0) }} % der förderfähigen Kosten)</span></td>
                <td class="r" style="color:var(--pos)">− {{ $eur($w['foerderung_eur'] ?? null) }}</td>
            </tr>
            <tr class="sum">
                <td>Netto-Investition nach Förderung</td>
                <td class="r">{{ $eur($w['netto_investition_eur'] ?? null) }}</td>
            </tr>
            <tr>
                <td style="padding-top:8px">Gewinn über 30 Jahre <span style="color:var(--muted)">(Einsparung abzgl. Netto-Investition)</span></td>
                <td class="r" style="padding-top:8px;color:var(--pos)">{{ $eur($w['gewinn_30j_eur'] ?? null) }}</td>
            </tr>
        </table>
    </div>

    <div class="note">
        Heizlast nach DIN EN 12831, Energiebedarf und Einsparung als Richtwerte auf Basis der erfassten Bauteile
        und Maßnahmen. {{ $preisHinweis }}
        Förderung nach BEG EM / BAFA (Effizienz-Bonus, ISFP-Bonus) — dies ist keine verbindliche Förderzusage;
        maßgeblich sind Antrag und Bescheid. Verbindliche Auslegung durch Fachplanung bzw. Energie-Effizienz-Experten.
        Erstellt am {{ now()->format('d.m.Y') }} · Solar Aspekt.
    </div>
</div>
</body>
</html>
