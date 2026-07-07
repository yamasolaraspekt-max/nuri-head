{{--
    Kundenfertiges Wärmepumpen-Auslegungs-Dokument (Strang Energie).
    EIGENSTÄNDIGES HTML-Dokument (KEIN @extends) — druck-/PDF-tauglich über die Browser-Druckfunktion
    („Als PDF speichern"). Reines HTML/CSS + ein window.print()-Aufruf, KEIN Alpine, KEIN Framework.
    Daten kommen fertig gerechnet aus EnergieAuslegungController::wpErgebnis() (eine Wahrheit).
--}}
@php
    $eur = fn ($v) => ($v === null || $v === '') ? '–' : number_format((float) $v, 0, ',', '.').' €';
    $eur2 = fn ($v) => ($v === null || $v === '') ? '–' : number_format((float) $v, 2, ',', '.').' €';
    $kwh = fn ($v) => ($v === null || $v === '') ? '–' : number_format((float) $v, 0, ',', '.').' kWh';
    $num = fn ($v, $d = 0) => ($v === null || $v === '') ? '–' : number_format((float) $v, $d, ',', '.');
    $txt = fn ($v) => ($v === null || $v === '') ? '–' : $v;

    $wp = $ergebnis['wp'] ?? [];
    $f = $ergebnis['foerderung'] ?? [];
    $ww = $ergebnis['ww'] ?? [];
    $modell = trim(($wp['hersteller'] ?? '').' '.($wp['modell'] ?? ''));
    if ($modell === '') { $modell = $ergebnis['wp_label'] ?? 'Wärmepumpe'; }

    $zuschuss = $f['zuschuss'] ?? ($f['gesamt_foerderung'] ?? null);
    $nettoInvest = $f['netto_investition'] ?? null;
    $modMin = $wp['modulation_min_kw'] ?? null;
    $modMax = $wp['modulation_max_kw'] ?? null;
    $modText = ($modMin !== null || $modMax !== null)
        ? trim($num($modMin, 1).' – '.$num($modMax, 1)).' kW'
        : '–';
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Wärmepumpen-Auslegung — {{ $modell }}</title>
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
    td{padding:4px 0;border-bottom:1px solid var(--line);vertical-align:top}
    td.r{text-align:right;font-weight:600;white-space:nowrap;padding-left:12px}
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
            <div class="brand">Solar Aspekt · Wärmepumpen-Auslegung</div>
            <h1>Ihre Wärmepumpen-Auslegung</h1>
            <div class="title-sub">Auslegung, Jahresarbeitszahl &amp; Wirtschaftlichkeit inkl. KfW-/BEG-Förderung</div>
        </div>
        <div class="meta">
            Datum <strong>{{ now()->format('d.m.Y') }}</strong><br>
            Wärmepumpe<br><strong>{{ $modell }}</strong>
        </div>
    </div>

    {{-- KPI-Karten --}}
    <div class="kpis">
        <div class="kpi">
            <div class="lab">Jahresarbeitszahl</div>
            <div class="val">{{ $num($ergebnis['jaz'] ?? null, 2) }}</div>
            <div class="sub">JAZ (Richtwert)</div>
        </div>
        <div class="kpi">
            <div class="lab">Stromverbrauch</div>
            <div class="val">{{ $num($ergebnis['strom_kwh'] ?? null) }}</div>
            <div class="sub">kWh / Jahr</div>
        </div>
        <div class="kpi">
            <div class="lab">KfW-/BEG-Zuschuss</div>
            <div class="val g">{{ $eur($zuschuss) }}</div>
            <div class="sub">{{ $num($f['effektiver_satz_pct'] ?? null, 1) }} % der förderf. Kosten</div>
        </div>
        <div class="kpi">
            <div class="lab">Netto-Investition</div>
            <div class="val">{{ $eur($nettoInvest) }}</div>
            <div class="sub">nach Förderung</div>
        </div>
    </div>

    <div class="grid2">
        {{-- Wärmepumpe / Datenblatt --}}
        <div>
            <h2>Wärmepumpe</h2>
            <table>
                <tr><td>Hersteller</td><td class="r">{{ $txt($wp['hersteller'] ?? null) }}</td></tr>
                <tr><td>Modell</td><td class="r">{{ $txt($wp['modell'] ?? null) }}</td></tr>
                <tr><td>Gerätetyp{{ !empty($wp['serie']) ? ' / Serie' : '' }}</td>
                    <td class="r">{{ $txt($wp['geraetetyp'] ?? null) }}@if(!empty($wp['serie'])) · {{ $wp['serie'] }}@endif</td></tr>
                <tr><td>Kältemittel</td><td class="r">{{ $txt($wp['kaeltemittel'] ?? null) }}</td></tr>
                <tr><td>SCOP (35 °C / 55 °C)</td><td class="r">{{ $txt($wp['scop_35'] ?? null) }} / {{ $txt($wp['scop_55'] ?? null) }}</td></tr>
                <tr><td>Heizleistung A7/W35</td><td class="r">{{ $wp['heizleistung_a7_w35_kw'] ?? null ? $num($wp['heizleistung_a7_w35_kw'], 1).' kW' : '–' }}</td></tr>
                <tr><td>Heizleistung A−7/W35</td><td class="r">{{ $wp['heizleistung_am7_w35_kw'] ?? null ? $num($wp['heizleistung_am7_w35_kw'], 1).' kW' : '–' }}</td></tr>
                <tr><td>max. Vorlauftemperatur</td><td class="r">{{ $wp['max_vorlauf_c'] ?? null ? $num($wp['max_vorlauf_c']).' °C' : '–' }}</td></tr>
                <tr><td>Modulation</td><td class="r">{{ $modText }}</td></tr>
            </table>
        </div>

        {{-- Auslegung --}}
        <div>
            <h2>Auslegung</h2>
            <table>
                <tr><td>Heizlast</td><td class="r">{{ $ergebnis['heizlast_kw'] ?? null ? $num($ergebnis['heizlast_kw'], 1).' kW' : '–' }}</td></tr>
                <tr><td>Heizsystem</td><td class="r">{{ $txt($ergebnis['heizsystem_label'] ?? null) }}</td></tr>
                <tr><td>Wärmequelle</td><td class="r">{{ $txt($ergebnis['wp_typ_label'] ?? null) }}</td></tr>
                <tr><td>Auslegungs-Vorlauf</td><td class="r">{{ $ergebnis['vorlauf_temp'] ?? null ? $num($ergebnis['vorlauf_temp']).' °C' : '–' }}</td></tr>
                <tr><td>Jahres-Heizarbeit</td><td class="r">{{ $kwh($ergebnis['q_heiz_kwh'] ?? null) }}</td></tr>
                @if (!empty($ergebnis['ww_mit_wp']))
                    <tr><td>Warmwasser-Wärmebedarf</td><td class="r">{{ $kwh($ergebnis['q_ww_kwh'] ?? null) }}</td></tr>
                    <tr><td>Warmwasser-Komfort</td><td class="r">{{ $txt(isset($ww['komfort']) ? ucfirst($ww['komfort']) : null) }}@if(!empty($ww['speicher_liter'])) · {{ $ww['speicher_liter'] }} l @endif</td></tr>
                @endif
                <tr><td>Stromkosten <span style="color:var(--muted)">({{ $num($ergebnis['strompreis'] ?? null, 2) }} €/kWh)</span></td>
                    <td class="r">{{ $eur($ergebnis['stromkosten_jahr'] ?? null) }} / Jahr</td></tr>
            </table>
        </div>
    </div>

    {{-- Wirtschaftlichkeit / Fazit --}}
    <div class="fazit">
        <h3>Wirtschaftlichkeit &amp; Förderung</h3>
        <table>
            <tr>
                <td>Investition (netto)</td>
                <td class="r">{{ $eur($ergebnis['investition_netto'] ?? null) }}</td>
            </tr>
            <tr>
                <td>KfW-/BEG-Zuschuss <span style="color:var(--muted)">({{ $num($f['effektiver_satz_pct'] ?? null, 1) }} % der förderfähigen Kosten)</span></td>
                <td class="r" style="color:var(--pos)">− {{ $eur($zuschuss) }}</td>
            </tr>
            <tr class="sum">
                <td>Netto-Investition nach Förderung</td>
                <td class="r">{{ $eur($nettoInvest) }}</td>
            </tr>
        </table>
    </div>

    <div class="note">
        JAZ und Stromverbrauch sind Richtwerte nach VDI 4650 / DIN EN 12831. Förderung nach BEG EM / KfW 458
        (Stand 2024/25, maximaler Fördersatz 70 %) — dies ist keine verbindliche Förderzusage; maßgeblich sind
        Antrag und Bescheid. Verbindliche Auslegung durch Fachplanung bzw. Energie-Effizienz-Experten.
        Erstellt am {{ now()->format('d.m.Y') }} · Solar Aspekt.
    </div>
</div>
</body>
</html>
