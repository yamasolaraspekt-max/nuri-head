<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Tagesbericht – {{ $employee->name }} – {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11.5px;
            color: #333;
            padding: 20px;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        header h2 {
            margin-bottom: 5px;
            font-size: 20px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .summary-row td {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .signature-block {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 45%;
            text-align: center;
        }

        .timestamp {
            text-align: right;
            font-size: 10px;
            color: #777;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <header>
        <h2>Tagesbericht</h2>
        <table class="info-table">
            <tr>
                <td><strong>Mitarbeiter:</strong> {{ $employee->name }}</td>
                <td><strong>Datum:</strong> {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td colspan="2" class="timestamp">
                    Bericht erstellt am: {{ \Carbon\Carbon::now()->format('d.m.Y H:i') }}
                </td>
            </tr>
        </table>
    </header>

    <table>
        <thead>
            <tr>
                <th>Start</th>
                <th>Ende</th>
                <th>Stunden</th>
                <th>Typ</th>
                <th>Arbeitsplatz</th>
                <th>Beschreibung</th>
                <th>Kunde</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach ($entries as $entry)
                <tr>
                    <td>{{ $entry['time_start'] }}</td>
                    <td>{{ $entry['time_end'] }}</td>
                    <td>{{ number_format($entry['hours'], 2, ',', '.') }}</td>
                    <td>{{ $entry['type'] }}</td>
                    <td>{{ $entry['place'] ?? 'Nicht klar' }}</td>
                    <td>{{ $entry['description'] ?? '' }}</td>
                    <td>
                        @php
                            $customer = !empty($entry['customer_id']) ? \App\Models\NewLeads::find($entry['customer_id']) : null;
                        @endphp
                        {{ $customer ? ($customer->name . ' ' . $customer->lastname) : '' }}
                    </td>
                </tr>
                @php $total += $entry['hours']; @endphp
            @endforeach
            <tr class="summary-row">
                <td colspan="2">Gesamt:</td>
                <td>{{ number_format($total, 2, ',', '.') }} Std.</td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    <div class="signature-block">
        <div class="signature">
            <p>__________________________</p>
            <p><strong>Mitarbeiter</strong></p>
        </div>
        <div class="signature">
            <p>__________________________</p>
            <p><strong>Vorgesetzter</strong></p>
        </div>
    </div>

</body>
</html>
