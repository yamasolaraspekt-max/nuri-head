{{--
    Kundenfertiges kombiniertes Energiekonzept-Dokument („Beratungsangebot") — Strang Energie.
    EIGENSTÄNDIGES HTML-Dokument (KEIN @extends) — druck-/PDF-tauglich über die Browser-Druckfunktion
    („Als PDF speichern"). Reines HTML/CSS + ein window.print()-Aufruf, KEIN Alpine, KEIN Framework.
    Fasst WP-Auslegung + Gebäudesanierung zu einem Gesamt-Beratungsangebot zusammen.
    Daten kommen fertig gerechnet aus dem Controller in $konzept (eine Wahrheit).
    Robust gegen null-Abschnitte: fehlt wp bzw. sanierung, entfällt der jeweilige Abschnitt.
--}}
@php
    $eur = fn ($v) => ($v === null || $v === '') ? '–' : number_format((float) $v, 0, ',', '.').' €';
    $kwh = fn ($v) => ($v === null || $v === '') ? '–' : number_format((float) $v, 0, ',', '.').' kWh';
    $num = fn ($v, $d = 0) => ($v === null || $v === '') ? '–' : number_format((float) $v, $d, ',', '.');
    $txt = fn ($v) => ($v === null || $v === '') ? '–' : $v;

    $konzept  = $konzept ?? [];
    $kunde    = $konzept['kunde'] ?? [];
    $wp       = $konzept['wp'] ?? null;
    $san      = $konzept['sanierung'] ?? null;
    $pv       = $konzept['pv'] ?? null;
    $gesamt   = $konzept['gesamt'] ?? [];
    $datum    = $konzept['datum'] ?? now()->format('d.m.Y');

    // WP-Teil
    $wpModell = null;
    $wpF = [];
    if ($wp !== null) {
        $wpModell = trim(($wp['hersteller'] ?? '').' '.($wp['modell'] ?? ''));
        if ($wpModell === '') { $wpModell = 'Wärmepumpe'; }
        $wpF = $wp['foerderung'] ?? [];
    }

    // Sanierungs-Teil
    $sDelta = [];
    $sW = [];
    $sF = [];
    if ($san !== null) {
        $sDelta = $san['delta'] ?? [];
        $sW = $san['wirtschaftlichkeit'] ?? [];
        $sF = $san['foerderung'] ?? [];
    }

    $kName = $txt($kunde['name'] ?? null);
    $kOrt  = trim(($kunde['plz'] ?? '').' '.($kunde['ort'] ?? ''));
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Energiekonzept — Solar Aspekt</title>
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
    .kpi.big{border-width:2px;border-color:var(--brand)}
    .kpi .lab{font-size:9.5px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);font-weight:700}
    .kpi .val{font-size:20px;font-weight:800;margin-top:3px;line-height:1.15}
    .kpi .val.g{color:var(--pos)}
    .kpi .sub{font-size:9.5px;color:var(--muted);margin-top:2px}
    .grid2{display:grid;grid-template-columns:1fr 1fr;gap:18px 28px}
    .section{margin-top:8px}
    .sec-title{display:flex;align-items:baseline;justify-content:space-between;border-bottom:1px solid var(--line);margin:26px 0 4px;padding-bottom:4px}
    .sec-title h2{margin:0;border:none}
    .sec-title .badge{font-size:12px;font-weight:700;color:var(--ink)}
    table{width:100%;border-collapse:collapse;font-size:12px}
    td{padding:4px 0;border-bottom:1px solid var(--line);vertical-align:top}
    td.r{text-align:right;font-weight:600;white-space:nowrap;padding-left:12px}
    .fazit{margin-top:22px;border:2px solid var(--pos);background:#eef6f0;border-radius:10px;padding:14px 16px}
    .fazit h3{margin:0 0 10px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--pos)}
    .fazit table td{border-bottom:1px solid #d5e7dc}
    .fazit .sum td{border-bottom:none;border-top:2px solid var(--pos);padding-top:8px;font-size:15px;font-weight:800}
    .fazit .sum td.r{color:var(--pos)}
    .note{margin-top:16px;font-size:10px;color:var(--muted);border-top:1px solid var(--line);padding-top:10px}
    .empty{margin-top:22px;border:1px dashed var(--line);border-radius:10px;padding:18px;color:var(--muted);text-align:center;background:var(--raised)}
    .print-btn{position:fixed;top:14px;right:14px;background:var(--brand);color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.18)}
    @media print{
        body{background:#fff}
        .noprint{display:none!important}
        .sheet{box-shadow:none;margin:0;max-width:none;padding:0}
        .kpi{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
        .fazit{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
        .empty{background:#fff}
        .section{page-break-inside:avoid}
        @page{size:A4;margin:16mm}
    }
</style>
</head>
<body>
<button type="button" class="print-btn noprint" onclick="window.print()">Drucken / als PDF speichern</button>

<div class="sheet">
    <div class="head">
        <div>
            <div class="brand">Solar Aspekt · Energiekonzept</div>
            <h1>Ihr Energiekonzept</h1>
            <div class="title-sub">Kombiniertes Beratungsangebot — Wärmepumpe &amp; Gebäudesanierung inkl. Förderung</div>
        </div>
        <div class="meta">
            @if ($kName !== '–')<strong>{{ $kName }}</strong><br>@endif
            @if ($kOrt !== '')<span>{{ $kOrt }}</span><br>@endif
            Datum <strong>{{ $txt($datum) }}</strong>
            @if (!empty($kunde['berater']))<br>Berater <strong>{{ $kunde['berater'] }}</strong>@endif
        </div>
    </div>

    {{-- GESAMT-KPI-Zeile (prominent) --}}
    <div class="kpis">
        <div class="kpi big">
            <div class="lab">Gesamt-Investition</div>
            <div class="val">{{ $eur($gesamt['investition_eur'] ?? null) }}</div>
            <div class="sub">netto, Wärmepumpe + Sanierung</div>
        </div>
        <div class="kpi big">
            <div class="lab">Gesamt-Förderung</div>
            <div class="val g">{{ $eur($gesamt['foerderung_eur'] ?? null) }}</div>
            <div class="sub">KfW/BEG + BAFA</div>
        </div>
        <div class="kpi big">
            <div class="lab">Eigenanteil</div>
            <div class="val">{{ $eur($gesamt['eigenanteil_eur'] ?? null) }}</div>
            <div class="sub">nach Förderung</div>
        </div>
        <div class="kpi big">
            <div class="lab">Jährliche Ersparnis</div>
            <div class="val g">{{ $eur($gesamt['einsparung_eur_a'] ?? null) }}</div>
            <div class="sub">Energiekosten / Jahr</div>
        </div>
    </div>

    @if ($wp === null && $san === null && $pv === null)
        <div class="empty">
            Für dieses Energiekonzept liegen noch keine ausgefüllten Teil-Berechnungen vor.
            Sobald die Wärmepumpen-Auslegung oder die Gebäudesanierung erfasst ist, erscheinen die Details hier.
        </div>
    @endif

    {{-- Abschnitt Wärmepumpe --}}
    @if ($wp !== null)
        <div class="section">
            <div class="sec-title">
                <h2>Wärmepumpe</h2>
                <span class="badge">{{ $txt($wpModell) }}</span>
            </div>
            <div class="grid2">
                <div>
                    <table>
                        <tr><td>Hersteller</td><td class="r">{{ $txt($wp['hersteller'] ?? null) }}</td></tr>
                        <tr><td>Modell</td><td class="r">{{ $txt($wp['modell'] ?? null) }}</td></tr>
                        <tr><td>Jahresarbeitszahl (JAZ)</td><td class="r">{{ $num($wp['jaz'] ?? null, 2) }}</td></tr>
                        <tr><td>Jahres-Stromverbrauch</td><td class="r">{{ $kwh($wp['strom_kwh'] ?? null) }}</td></tr>
                        <tr><td>Stromkosten</td><td class="r">{{ $eur($wp['stromkosten_jahr'] ?? null) }} / Jahr</td></tr>
                    </table>
                </div>
                <div>
                    <table>
                        <tr><td>Investition (netto)</td><td class="r">{{ $eur($wp['investition_netto'] ?? null) }}</td></tr>
                        <tr>
                            <td>KfW-/BEG-Zuschuss <span style="color:var(--muted)">({{ $num($wpF['effektiver_satz_pct'] ?? null, 1) }} %)</span></td>
                            <td class="r" style="color:var(--pos)">− {{ $eur($wpF['zuschuss'] ?? null) }}</td>
                        </tr>
                        <tr><td><strong>Netto nach Förderung</strong></td><td class="r"><strong>{{ $eur($wpF['netto_investition'] ?? null) }}</strong></td></tr>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Abschnitt Gebäudesanierung --}}
    @if ($san !== null)
        <div class="section">
            <div class="sec-title">
                <h2>Gebäudesanierung</h2>
                <span class="badge">− {{ $num($sDelta['phi_hl_pct'] ?? null, 1) }} % Heizlast</span>
            </div>
            <div class="grid2">
                <div>
                    <table>
                        <tr>
                            <td>Heizlast-Reduktion</td>
                            <td class="r">{{ $sDelta['phi_hl_kw'] ?? null ? $num($sDelta['phi_hl_kw'], 1).' kW' : '–' }} <span style="color:var(--muted)">(− {{ $num($sDelta['phi_hl_pct'] ?? null, 1) }} %)</span></td>
                        </tr>
                        <tr><td>Energieeinsparung</td><td class="r">{{ $kwh($sDelta['q_a_kwh'] ?? null) }} / Jahr</td></tr>
                        <tr><td>Jährliche Ersparnis</td><td class="r" style="color:var(--pos)">{{ $eur($sW['einsparung_eur_a'] ?? null) }} / Jahr</td></tr>
                        <tr><td>Amortisation</td><td class="r">{{ $sW['amortisation_jahre'] ?? null ? $num($sW['amortisation_jahre'], 1).' Jahre' : '–' }}</td></tr>
                    </table>
                </div>
                <div>
                    <table>
                        <tr><td>Investition (brutto)</td><td class="r">{{ $eur($sW['investition_eur'] ?? null) }}</td></tr>
                        <tr>
                            <td>BAFA-Zuschuss <span style="color:var(--muted)">({{ $num($sF['quote_pct'] ?? null, 0) }} %)</span></td>
                            <td class="r" style="color:var(--pos)">− {{ $eur($sW['foerderung_eur'] ?? null) }}</td>
                        </tr>
                        <tr><td><strong>Netto nach Förderung</strong></td><td class="r"><strong>{{ $eur($sW['netto_investition_eur'] ?? null) }}</strong></td></tr>
                        <tr><td>Gewinn über 30 Jahre</td><td class="r" style="color:var(--pos)">{{ $eur($sW['gewinn_30j_eur'] ?? null) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Abschnitt Photovoltaik --}}
    @if ($pv !== null)
        <div class="section">
            <div class="sec-title">
                <h2>Photovoltaik</h2>
                <span class="badge">{{ $num($pv['kwp'] ?? null, 1) }} kWp</span>
            </div>
            <div class="grid2">
                <div>
                    <table>
                        <tr><td>Anlagengröße</td><td class="r">{{ $num($pv['kwp'] ?? null, 1) }} kWp</td></tr>
                        <tr>
                            <td>Jahresertrag</td>
                            <td class="r">{{ $kwh($pv['jahresertrag_kwh'] ?? null) }} / Jahr <span style="color:var(--muted)">({{ ($pv['quelle'] ?? null) === 'pvgis' ? 'PVGIS' : 'Schätzung' }})</span></td>
                        </tr>
                        <tr><td>Eigenverbrauch <span style="color:var(--muted)">({{ $num($pv['eigenverbrauch_pct'] ?? null, 0) }} %)</span></td><td class="r">{{ $kwh(($pv['jahresertrag_kwh'] ?? 0) * ($pv['eigenverbrauch_pct'] ?? 0) / 100) }} / Jahr</td></tr>
                        <tr><td>Netzeinspeisung</td><td class="r">{{ $kwh(($pv['jahresertrag_kwh'] ?? 0) - ($pv['jahresertrag_kwh'] ?? 0) * ($pv['eigenverbrauch_pct'] ?? 0) / 100) }} / Jahr</td></tr>
                    </table>
                </div>
                <div>
                    <table>
                        <tr><td>Investition</td><td class="r">{{ $eur($pv['investition_eur'] ?? null) }}</td></tr>
                        <tr><td>Eigenverbrauch-Ersparnis</td><td class="r" style="color:var(--pos)">{{ $eur($pv['ersparnis_eigenverbrauch_eur_a'] ?? null) }} / Jahr</td></tr>
                        <tr><td>Einspeise-Vergütung</td><td class="r" style="color:var(--pos)">{{ $eur($pv['verguetung_einspeisung_eur_a'] ?? null) }} / Jahr</td></tr>
                        <tr><td><strong>PV-Ertrag gesamt</strong></td><td class="r" style="color:var(--pos)"><strong>{{ $eur($pv['ertrag_gesamt_eur_a'] ?? null) }} / Jahr</strong></td></tr>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Gesamt-Fazit --}}
    <div class="fazit">
        <h3>Ihr Gesamtergebnis</h3>
        <table>
            <tr>
                <td>Gesamt-Investition</td>
                <td class="r">{{ $eur($gesamt['investition_eur'] ?? null) }}</td>
            </tr>
            <tr>
                <td>Gesamt-Förderung <span style="color:var(--muted)">(KfW/BEG + BAFA)</span></td>
                <td class="r" style="color:var(--pos)">− {{ $eur($gesamt['foerderung_eur'] ?? null) }}</td>
            </tr>
            <tr class="sum">
                <td>Ihr Eigenanteil nach Förderung</td>
                <td class="r">{{ $eur($gesamt['eigenanteil_eur'] ?? null) }}</td>
            </tr>
            <tr>
                <td style="padding-top:8px">Jährliche Energiekosten-Ersparnis</td>
                <td class="r" style="padding-top:8px;color:var(--pos)">{{ $eur($gesamt['einsparung_eur_a'] ?? null) }} / Jahr</td>
            </tr>
        </table>
    </div>

    <div class="note">
        Auslegung nach VDI 4650 / DIN EN 12831; Energiebedarf, Einsparung und Wirtschaftlichkeit sind Richtwerte auf
        Basis der erfassten Angaben. Förderung nach BEG (EM / EH), KfW 458 und BAFA (Stand 2024/25) — dies ist keine
        verbindliche Förderzusage; maßgeblich sind Antrag und Bescheid. Verbindliche Auslegung durch Fachplanung bzw.
        Energie-Effizienz-Experten. Erstellt am {{ $txt($datum) }} · Solar Aspekt.
    </div>
</div>
</body>
</html>
