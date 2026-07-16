<!DOCTYPE html>
<html lang="de">
{{--
    Mahnschreiben (Druckansicht) — Welle A1, Paket 2 (2026-07-16).
    Druckdokument: reines Schwarz ist hier ERLAUBT (Ausnahme der Farbwelt-Regel, wie Rechnungs-PDF).
    Firmenkopf: bewusst schlicht (app.name) — Briefpapier/Logo ist eine offene Yama-Entscheidung.
--}}
<head>
    <meta charset="utf-8">
    <title>{{ $stufe['titel'] }} — {{ $invoice->invoice_no ?: '#' . $invoice->id }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #000; margin: 0; background: #f3f4f6; }
        .sheet { width: 210mm; min-height: 297mm; margin: 18px auto; background: #fff; padding: 25mm 20mm 20mm; box-sizing: border-box; box-shadow: 0 10px 30px rgba(31,41,55,.15); font-size: 11pt; line-height: 1.5; }
        .head { display: flex; justify-content: space-between; margin-bottom: 22mm; }
        .firm { font-weight: 700; font-size: 13pt; }
        .meta { text-align: right; font-size: 10pt; color: #000; }
        .addr { font-size: 11pt; margin-bottom: 16mm; }
        .addr .ret { font-size: 8pt; border-bottom: 1px solid #000; margin-bottom: 4mm; padding-bottom: 1mm; }
        h1 { font-size: 13pt; margin: 0 0 6mm; }
        table.pos { width: 100%; border-collapse: collapse; margin: 6mm 0; font-size: 10.5pt; }
        table.pos th { text-align: left; border-bottom: 1px solid #000; padding: 2mm 2mm; }
        table.pos td { border-bottom: 1px solid #ccc; padding: 2mm 2mm; }
        table.pos .num { text-align: right; }
        table.pos tr.sum td { border-bottom: none; border-top: 2px solid #000; font-weight: 700; }
        .hint { font-size: 9.5pt; margin-top: 8mm; }
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
                Datum: {{ \Carbon\Carbon::parse($item->created_at)->format('d.m.Y') }}<br>
                Rechnung: {{ $invoice->invoice_no ?: '#' . $invoice->id }}<br>
                Kunde: {{ $invoice->customer->customer_no ?? ($invoice->customer_id ?: '–') }}
            </div>
        </div>

        <div class="addr">
            <div class="ret">{{ config('app.name') }}</div>
            {{ $item->recipient_name }}<br>
            @if ($invoice->customer?->street){{ $invoice->customer->street }}<br>@endif
            @if ($invoice->customer?->postcode || $invoice->customer?->city){{ $invoice->customer->postcode }} {{ $invoice->customer->city }}@endif
        </div>

        <h1>{{ $stufe['titel'] }}</h1>

        @if ((int) $item->level === 1)
            <p>Sehr geehrte Damen und Herren,</p>
            <p>sicher ist es Ihrer Aufmerksamkeit entgangen: Für die unten aufgeführte Rechnung konnten wir bis heute keinen Zahlungseingang feststellen. Wir bitten Sie, den offenen Betrag bis zum <strong>{{ \Carbon\Carbon::parse($item->pay_until)->format('d.m.Y') }}</strong> auszugleichen. Sollten Sie zwischenzeitlich gezahlt haben, betrachten Sie dieses Schreiben bitte als gegenstandslos.</p>
        @elseif ((int) $item->level === 2)
            <p>Sehr geehrte Damen und Herren,</p>
            <p>trotz unserer Zahlungserinnerung ist die unten aufgeführte Rechnung weiterhin offen. Wir fordern Sie hiermit auf, den Gesamtbetrag bis zum <strong>{{ \Carbon\Carbon::parse($item->pay_until)->format('d.m.Y') }}</strong> zu begleichen. Für diese Mahnung berechnen wir eine Mahngebühr.</p>
        @else
            <p>Sehr geehrte Damen und Herren,</p>
            <p>auch auf unsere bisherigen Mahnungen ist kein Zahlungseingang erfolgt. Wir fordern Sie <strong>letztmalig</strong> auf, den Gesamtbetrag bis zum <strong>{{ \Carbon\Carbon::parse($item->pay_until)->format('d.m.Y') }}</strong> zu begleichen.</p>
        @endif

        <table class="pos">
            <thead>
                <tr><th>Rechnung</th><th>Rechnungsdatum</th><th>Fällig am</th><th class="num">Offener Betrag</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->invoice_no ?: '#' . $invoice->id }}</td>
                    <td>{{ $invoice->issue_date?->format('d.m.Y') ?? '–' }}</td>
                    <td>{{ $invoice->due_date?->format('d.m.Y') ?? '–' }} ({{ $item->days_overdue }} Tage überfällig)</td>
                    <td class="num">{{ number_format($item->open_amount, 2, ',', '.') }} €</td>
                </tr>
                @if ((float) $item->fee > 0)
                    <tr><td colspan="3">Mahngebühr ({{ $stufe['titel'] }})</td><td class="num">{{ number_format($item->fee, 2, ',', '.') }} €</td></tr>
                @endif
                <tr class="sum"><td colspan="3">Zu zahlender Gesamtbetrag</td><td class="num">{{ number_format($item->total_due, 2, ',', '.') }} €</td></tr>
            </tbody>
        </table>

        @if ((int) $item->level >= 2)
            <p class="hint">{{ config('mahnwesen.zins_hinweis') }}</p>
        @endif
        @if ((int) $item->level >= 3)
            <p class="hint"><strong>{{ config('mahnwesen.stufe3_hinweis') }}</strong></p>
        @endif

        <p style="margin-top: 10mm;">Mit freundlichen Grüßen<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
