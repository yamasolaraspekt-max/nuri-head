<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11pt; color: #333; margin: 0; padding: 0; }
        .header { color: {{ $branding['color'] }}; font-size: 24pt; font-weight: bold; margin-bottom: 20px; }
        .section { margin-bottom: 30px; }
        .section-title { font-weight: bold; color: {{ $branding['color'] }}; border-bottom: 1px solid #ccc; margin-bottom: 10px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 8px; border-bottom: 2px solid {{ $branding['color'] }}; font-size: 10pt; }
        td { padding: 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        .total-box { margin-top: 50px; text-align: right; font-size: 14pt; font-weight: bold; color: {{ $branding['color'] }}; }
    </style>
</head>
<body>
    <div class="header">{{ $branding['company'] }}</div>
    
    <div style="margin-bottom: 40px;">
        <strong>{{ $offer->customer->display_name ?? 'Kunde' }}</strong><br>
        {{ $offer->customer->street ?? '' }}<br>
        {{ $offer->customer->city ?? '' }}
    </div>

    @foreach($sections as $section)
        <div class="section">
            <div class="section-title">{{ $section['title'] }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="10%">Pos.</th>
                        <th width="60%">Bezeichnung</th>
                        <th width="10%">Menge</th>
                        <th width="20%" style="text-align:right">Gesamt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($section['items'] as $index => $item)
                        @if($item['active'] ?? true)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item['name'] }}</strong><br><small>{!! $item['desc_html'] ?? '' !!}</small></td>
                            <td>{{ $item['qty'] }} {{ $item['unit'] }}</td>
                            <td style="text-align:right">{{ number_format($item['price'] * $item['qty'], 2, ',', '.') }} €</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="total-box">
        Gesamtbetrag: {{ number_format($total, 2, ',', '.') }} €
    </div>
</body>
</html>