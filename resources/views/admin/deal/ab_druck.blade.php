<!DOCTYPE html>
<html lang="de">
{{--
    Auftragsbestätigung (Druck) — Welle A2 (2026-07-16). Rendert AUSSCHLIESSLICH den
    eingefrorenen Stand aus order_confirmations (append-only). Druckdokument: Schwarz erlaubt.
    Variante (a) bestätigt: Kopf + Positionen + Summen + Freitext + AGB-Verweis-Satz.
--}}
<head>
    <meta charset="utf-8">
    <title>Auftragsbestätigung {{ $ab->ab_no ?: '' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #000; margin: 0; background: #f3f4f6; }
        .sheet { width: 210mm; min-height: 297mm; margin: 18px auto; background: #fff; padding: 25mm 20mm 20mm; box-sizing: border-box; box-shadow: 0 10px 30px rgba(31,41,55,.15); font-size: 11pt; line-height: 1.5; }
        .head { display: flex; justify-content: space-between; margin-bottom: 20mm; }
        .firm { font-weight: 700; font-size: 13pt; }
        .meta { text-align: right; font-size: 10pt; }
        .addr { font-size: 11pt; margin-bottom: 14mm; }
        .addr .ret { font-size: 8pt; border-bottom: 1px solid #000; margin-bottom: 4mm; padding-bottom: 1mm; }
        h1 { font-size: 13pt; margin: 0 0 6mm; }
        .hinweis { font-size: 9.5pt; color: #000; background: #f3f4f6; padding: 2mm 3mm; border-radius: 2mm; margin: 4mm 0; }
        table.pos { width: 100%; border-collapse: collapse; margin: 6mm 0; font-size: 10pt; }
        table.pos th { text-align: left; border-bottom: 1px solid #000; padding: 2mm; }
        table.pos td { border-bottom: 1px solid #ccc; padding: 2mm; vertical-align: top; }
        table.pos .num { text-align: right; white-space: nowrap; }
        table.sums { margin-left: auto; border-collapse: collapse; font-size: 10.5pt; margin-top: 4mm; }
        table.sums td { padding: 1.2mm 2mm; }
        table.sums .num { text-align: right; min-width: 30mm; }
        table.sums tr.total td { border-top: 2px solid #000; font-weight: 700; }
        .foot { font-size: 9.5pt; margin-top: 10mm; }
        .print-btn { position: fixed; top: 14px; right: 18px; background: #93c21c; color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 14px; font-weight: 700; cursor: pointer; }
        @media print { body { background: #fff; } .sheet { margin: 0; box-shadow: none; } .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Drucken</button>
    <div class="sheet">
        <div class="head">
            <div class="firm">{{ config('app.name') }}</div>
            <div class="meta">
                Datum: {{ \Carbon\Carbon::parse($ab->created_at)->format('d.m.Y') }}<br>
                Auftragsbestätigung: <strong>{{ $ab->ab_no ?: '— ohne Nummer —' }}</strong><br>
                @if ($deal?->offer_number) Angebot: {{ $deal->offer_number }}<br> @endif
                Kunde: {{ $deal->customer->customer_no ?? '–' }}
            </div>
        </div>

        <div class="addr">
            <div class="ret">{{ config('app.name') }}</div>
            {{ $ab->recipient_name }}<br>
            @if ($deal?->customer?->street){{ $deal->customer->street }}<br>@endif
            @if ($deal?->customer?->postcode || $deal?->customer?->city){{ $deal->customer->postcode }} {{ $deal->customer->city }}@endif
        </div>

        <h1>Auftragsbestätigung</h1>
        <p>Sehr geehrte Damen und Herren,<br>
        hiermit bestätigen wir die Erteilung Ihres Auftrags{{ $ab->ab_no ? ' Nr. ' . $ab->ab_no : '' }}. Wir bedanken uns für Ihr Vertrauen und bestätigen die Ausführung der folgenden Leistungen:</p>

        @if ($ab->ohne_snapshot)
            <div class="hinweis">Zu diesem Auftrag liegt kein Angebots-Positionsstand vor — bestätigt wird der Auftragskopf. Leistungsumfang laut Vereinbarung.</div>
            @if ($deal?->price)
                <table class="sums">
                    <tr class="total"><td>Auftragswert</td><td class="num">{{ number_format((float) $deal->price, 2, ',', '.') }} €</td></tr>
                </table>
            @endif
        @else
            <table class="pos">
                <thead><tr><th style="width:8mm">Pos.</th><th>Leistung / Artikel</th><th class="num">Menge</th><th class="num">Einzelpreis</th><th class="num">Gesamt</th></tr></thead>
                <tbody>
                    @foreach ($positions as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p['titel'] ?? 'Position' }}</td>
                            <td class="num">{{ rtrim(rtrim(number_format((float) ($p['menge'] ?? 1), 2, ',', '.'), '0'), ',') }}</td>
                            <td class="num">{{ number_format((float) ($p['einzel'] ?? 0), 2, ',', '.') }} €</td>
                            <td class="num">{{ number_format((float) ($p['summe'] ?? 0), 2, ',', '.') }} €</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="sums">
                @if ($ab->total_net !== null)<tr><td>Summe netto</td><td class="num">{{ number_format($ab->total_net, 2, ',', '.') }} €</td></tr>@endif
                @if ($ab->total_net !== null && $ab->total_gross !== null)<tr><td>zzgl. USt.{{ $ab->tax_rate !== null ? ' (' . rtrim(rtrim(number_format($ab->tax_rate, 2, ',', '.'), '0'), ',') . ' %)' : '' }}</td><td class="num">{{ number_format($ab->total_gross - $ab->total_net, 2, ',', '.') }} €</td></tr>@endif
                @if ($ab->total_gross !== null)<tr class="total"><td>Gesamtbetrag</td><td class="num">{{ number_format($ab->total_gross, 2, ',', '.') }} €</td></tr>@endif
            </table>
        @endif

        @if ($ab->freitext)
            <p style="margin-top:6mm">{{ $ab->freitext }}</p>
        @endif

        <p class="foot">Es gelten unsere Allgemeinen Geschäftsbedingungen. Änderungen und Ergänzungen dieses Auftrags bedürfen der Textform.</p>

        <p style="margin-top: 8mm;">Mit freundlichen Grüßen<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
