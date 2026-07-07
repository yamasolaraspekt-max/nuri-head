{{--
    Kundenfertiges Wechselrichter-Auslegungs-Dokument (Strang Energie).
    EIGENSTÄNDIGES HTML-Dokument (KEIN @extends) — druck-/PDF-tauglich über die Browser-Druckfunktion
    („Als PDF speichern"). Reines HTML/CSS + ein window.print()-Aufruf, KEIN Alpine, KEIN Framework.
    Daten kommen fertig gerechnet aus EnergieAuslegungController::wrErgebnis() (eine Wahrheit).
--}}
@php
    $num = fn ($v, $d = 0) => ($v === null || $v === '') ? '–' : number_format((float) $v, $d, ',', '.');
    $txt = fn ($v) => ($v === null || $v === '') ? '–' : $v;

    // Ampel → Farbe/Text (deckungsgleich zur Rechenseite wr_auslegung.blade.php).
    $ampelMap = [
        'gruen' => ['bg' => '#eef6f0', 'border' => '#3f7d5a', 'ink' => '#2f6046', 'text' => 'Grün — Auslegung plausibel', 'kurz' => 'Grün'],
        'gelb' => ['bg' => '#fbf4e6', 'border' => '#c08a2d', 'ink' => '#8a6212', 'text' => 'Gelb — mit Einschränkungen', 'kurz' => 'Gelb'],
        'rot' => ['bg' => '#fbecec', 'border' => '#c0453f', 'ink' => '#93332e', 'text' => 'Rot — nicht zulässig', 'kurz' => 'Rot'],
    ];
    $ampelFallback = ['bg' => '#f4f4f4', 'border' => '#9aa0ab', 'ink' => '#6b7180', 'text' => '—', 'kurz' => '—'];
    $a = $ampelMap[$ergebnis['ampel'] ?? ''] ?? $ampelFallback;

    $regeln = $ergebnis['regeln'] ?? [];
    $modul = $ergebnis['modul_label'] ?? 'PV-Modul';
    $wrLabel = $ergebnis['wr_label'] ?? 'Wechselrichter';
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Wechselrichter-Auslegung — {{ $wrLabel }}</title>
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
    .status-badge{display:inline-block;margin-top:12px;padding:6px 14px;border-radius:999px;font-size:12px;font-weight:800;border:2px solid}
    .kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:16px}
    .kpi{border:1px solid var(--line);border-radius:10px;padding:10px 12px;background:var(--raised)}
    .kpi .lab{font-size:9.5px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);font-weight:700}
    .kpi .val{font-size:20px;font-weight:800;margin-top:3px;line-height:1.15}
    .kpi .sub{font-size:9.5px;color:var(--muted);margin-top:2px}
    .grid2{display:grid;grid-template-columns:1fr 1fr;gap:18px 28px}
    table{width:100%;border-collapse:collapse;font-size:12px}
    td{padding:4px 0;border-bottom:1px solid var(--line);vertical-align:top}
    td.r{text-align:right;font-weight:600;white-space:nowrap;padding-left:12px}
    .rules{margin-top:8px}
    .rules table{border-collapse:collapse}
    .rules th{font-size:9.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);font-weight:700;text-align:left;padding:6px 8px;border-bottom:2px solid var(--line)}
    .rules td{padding:6px 8px;border-bottom:1px solid var(--line);vertical-align:top}
    .rules td.c{white-space:nowrap}
    .pill{display:inline-block;padding:2px 9px;border-radius:999px;font-size:10px;font-weight:800;border:1.5px solid}
    .fazit{margin-top:18px;border:2px solid;border-radius:10px;padding:14px 16px}
    .fazit h3{margin:0 0 6px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}
    .fazit p{margin:0;font-size:12px;color:var(--ink)}
    .note{margin-top:16px;font-size:10px;color:var(--muted);border-top:1px solid var(--line);padding-top:10px}
    .print-btn{position:fixed;top:14px;right:14px;background:var(--brand);color:#fff;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.18)}
    @media print{
        body{background:#fff}
        .noprint{display:none!important}
        .sheet{box-shadow:none;margin:0;max-width:none;padding:0}
        .kpi{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
        .status-badge,.pill,.fazit{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        @page{size:A4;margin:16mm}
    }
</style>
</head>
<body>
<button type="button" class="print-btn noprint" onclick="window.print()">Drucken / als PDF speichern</button>

<div class="sheet">
    <div class="head">
        <div>
            <div class="brand">Solar Aspekt · Wechselrichter-Auslegung</div>
            <h1>Ihre Wechselrichter-Auslegung</h1>
            <div class="title-sub">String-Vorauslegung nach Spannungsfenster, Strömen, DC-Überdimensionierung &amp; Netzregeln (VDE-AR-N 4105)</div>
            <span class="status-badge" style="background:{{ $a['bg'] }};border-color:{{ $a['border'] }};color:{{ $a['ink'] }}">{{ $a['text'] }}</span>
        </div>
        <div class="meta">
            Datum <strong>{{ now()->format('d.m.Y') }}</strong><br>
            Wechselrichter<br><strong>{{ $wrLabel }}</strong>
        </div>
    </div>

    {{-- KPI-Karten --}}
    <div class="kpis">
        <div class="kpi">
            <div class="lab">Gesamt-Bewertung</div>
            <div class="val" style="color:{{ $a['ink'] }}">{{ $a['kurz'] }}</div>
            <div class="sub">Ampel-Status</div>
        </div>
        <div class="kpi">
            <div class="lab">DC-Leistung</div>
            <div class="val">{{ $num($ergebnis['p_dc_w'] ?? null) }}</div>
            <div class="sub">Wp ({{ $num($ergebnis['module_gesamt'] ?? null) }} Module)</div>
        </div>
        <div class="kpi">
            <div class="lab">DC/AC-Verhältnis</div>
            <div class="val">{{ $num($ergebnis['ratio'] ?? null, 3) }}</div>
            <div class="sub">Überdimensionierung</div>
        </div>
        <div class="kpi">
            <div class="lab">Spannungsfenster</div>
            <div class="val" style="color:{{ !empty($ergebnis['gueltig']) ? 'var(--pos)' : '#93332e' }}">{{ !empty($ergebnis['gueltig']) ? 'gültig' : 'ungültig' }}</div>
            <div class="sub">String-Auslegbarkeit</div>
        </div>
    </div>

    <div class="grid2">
        {{-- Konfiguration --}}
        <div>
            <h2>Konfiguration</h2>
            <table>
                <tr><td>PV-Modul</td><td class="r">{{ $txt($modul) }}</td></tr>
                <tr><td>Wechselrichter</td><td class="r">{{ $txt($wrLabel) }}</td></tr>
                <tr><td>Module gesamt</td><td class="r">{{ $num($ergebnis['module_gesamt'] ?? null) }} Stück</td></tr>
                <tr><td>Parallel-Strings</td><td class="r">{{ $num($ergebnis['parallel_strings'] ?? null) }}</td></tr>
            </table>
        </div>

        {{-- Auslegung / Kennzahlen --}}
        <div>
            <h2>Auslegung</h2>
            <table>
                <tr><td>DC-Generatorleistung</td><td class="r">{{ $num($ergebnis['p_dc_w'] ?? null) }} Wp</td></tr>
                <tr><td>DC/AC-Verhältnis</td><td class="r">{{ $num($ergebnis['ratio'] ?? null, 3) }}</td></tr>
                <tr><td>Spannungsfenster</td>
                    <td class="r" style="color:{{ !empty($ergebnis['gueltig']) ? 'var(--pos)' : '#93332e' }}">
                        {{ !empty($ergebnis['gueltig']) ? 'gültig' : 'ungültig (n_min > n_max)' }}
                    </td></tr>
                <tr><td>Gesamt-Ampel</td><td class="r" style="color:{{ $a['ink'] }}">{{ $a['kurz'] }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Prüfregeln --}}
    <h2>Prüfregeln</h2>
    <div class="rules">
        <table>
            <thead>
                <tr>
                    <th style="width:64px">Status</th>
                    <th>Regel</th>
                    <th>Detail</th>
                    <th>Grenze</th>
                    <th>Norm</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($regeln as $regel)
                    @php $ra = $ampelMap[$regel['status'] ?? ''] ?? $ampelFallback; @endphp
                    <tr>
                        <td class="c"><span class="pill" style="background:{{ $ra['bg'] }};border-color:{{ $ra['border'] }};color:{{ $ra['ink'] }}">{{ $ra['kurz'] }}</span></td>
                        <td>{{ $txt($regel['titel'] ?? null) }}</td>
                        <td>{{ $txt($regel['wert_text'] ?? null) }}</td>
                        <td style="color:var(--muted)">{{ $txt($regel['grenze_text'] ?? null) }}</td>
                        <td style="color:var(--muted)">{{ $txt($regel['norm'] ?? null) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="color:var(--muted)">Keine Prüfregeln vorhanden.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Fazit (farblich an die Ampel gebunden) --}}
    <div class="fazit" style="background:{{ $a['bg'] }};border-color:{{ $a['border'] }}">
        <h3 style="color:{{ $a['ink'] }}">Gesamt-Ergebnis</h3>
        <p>{{ $a['text'] }}. DC-Leistung {{ $num($ergebnis['p_dc_w'] ?? null) }} Wp bei einem DC/AC-Verhältnis von {{ $num($ergebnis['ratio'] ?? null, 3) }} — Spannungsfenster {{ !empty($ergebnis['gueltig']) ? 'gültig' : 'ungültig' }}.</p>
    </div>

    <div class="note">
        Vorauslegung auf Basis hinterlegter Datenblattwerte nach VDE-AR-N 4105. Dies ist keine verbindliche
        Auslegung — die Endauslegung (Strang-/MPPT-Verschaltung, Schutzkonzept, Netzanschluss) erfolgt durch
        eine qualifizierte Elektrofachkraft. Erstellt am {{ now()->format('d.m.Y') }} · Solar Aspekt.
    </div>
</div>
</body>
</html>
