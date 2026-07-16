@extends('admin.layouts.app')
@section('title', 'Mahnwesen')

{{--
    Mahnwesen — Welle A1, Paket 2 (2026-07-16). Vorschlag + Bestätigung: das System schlägt
    Kandidaten und Stufe vor, gemahnt wird erst nach menschlichem Klick. Regeln: config/mahnwesen.php.
    UI nach Styleguide (sa-ui-Tokens, kein Schwarz/Fremdblau).
--}}

@section('content')
<style>
    .mw-wrap { margin: 0 18px 40px; color: #1f2937; }
    .mw-rules { background: var(--sa-info-bg, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; font-size: 12.5px; color: #374151; margin: 4px 0 18px; max-width: 980px; }
    .mw-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .mw-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .mw-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .mw-table tbody tr:hover { background: #f9fafb; }
    .mw-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .mw-cust { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .mw-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .mw-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .mw-pill-info    { background: var(--sa-info-bg, #f3f4f6);    color: #374151; } .mw-pill-info i    { background: var(--sa-info, #6b7280); }
    .mw-pill-warning { background: var(--sa-warning-bg, #fff7ed); color: #d97706; } .mw-pill-warning i { background: var(--sa-warning, #f59e0b); }
    .mw-pill-danger  { background: var(--sa-danger-bg, #fef2f2);  color: #b91c1c; } .mw-pill-danger i  { background: var(--sa-danger, #ef4444); }
    .mw-btn { display: inline-flex; align-items: center; gap: 7px; border-radius: 8px; padding: 9px 16px; font-size: 13px; font-weight: 700; border: 1px solid transparent; cursor: pointer; background: var(--sa-accent, #93c21c); color: #fff; }
    .mw-btn:hover { background: var(--sa-accent-hover, #7baa18); }
    .mw-btn-soft { display: inline-flex; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; }
    .mw-btn-soft:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
    .mw-h2 { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: #374151; margin: 28px 0 10px; }
    .mw-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 34px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .mw-flash { border-radius: 10px; padding: 11px 14px; font-size: 13px; font-weight: 600; margin: 0 0 14px; }
    .mw-flash.ok { background: var(--sa-success-bg, #ecfdf5); color: #15803d; }
    .mw-flash.err { background: var(--sa-danger-bg, #fef2f2); color: #b91c1c; }
    .mw-footbar { display: flex; justify-content: flex-end; padding: 14px 0; }
    .mw-run-head { display: flex; gap: 14px; align-items: baseline; font-size: 13px; font-weight: 700; margin: 14px 0 6px; color: #1f2937; }
    .mw-run-head .sub { font-weight: 500; color: #6b7280; font-size: 12px; }
</style>

<x-page-head title="Mahnwesen"
    sub="Vorschlag nach Standard-Regeln — gemahnt wird erst nach deiner Bestätigung. Stufen: Zahlungserinnerung → 1. Mahnung (5 €) → 2. Mahnung (10 €, letzte)."
    current="Mahnwesen">
    <x-slot:actions>
        <a href="{{ route('admin.invoices.offene-posten') }}" class="mw-btn-soft">Zu den Offenen Posten</a>
    </x-slot:actions>
</x-page-head>

<div class="mw-wrap">
    @if (session('success'))<div class="mw-flash ok">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="mw-flash err">{{ session('error') }}</div>@endif

    @if ($migrationFehlt)
        <div class="mw-empty">
            Die Mahn-Tabellen fehlen noch in der Datenbank.<br>
            Einmal ausführen: <code>php artisan migrate</code> (Migration <code>2026_07_16_120001_create_ticket_dunning_tables</code> — additiv, nur neue Tabellen).
        </div>
    @else
        <div class="mw-rules">
            <strong>Standard-Regeln</strong> (config/mahnwesen.php): Karenz {{ config('mahnwesen.karenz_tage') }} Tage nach Fälligkeit ·
            Mindestabstand {{ config('mahnwesen.stufen_abstand_tage') }} Tage zwischen Stufen ·
            Rechnungen ohne Zahlungsziel werden nicht gemahnt ·
            Verzugszinsen werden im Schreiben angekündigt, nicht automatisch berechnet.
        </div>

        <div class="mw-h2">Mahn-Kandidaten ({{ count($kandidaten) }})</div>
        @if (empty($kandidaten))
            <div class="mw-empty">Keine Mahn-Kandidaten — nichts ist über Fälligkeit + Karenz hinaus offen.</div>
        @else
            <form method="POST" action="{{ route('admin.invoices.mahnwesen.execute') }}">
                @csrf
                <table class="mw-table">
                    <thead>
                        <tr>
                            <th style="width:34px"><input type="checkbox" checked onclick="document.querySelectorAll('.mw-check').forEach(c => c.checked = this.checked)"></th>
                            <th>Kunde</th>
                            <th>Rechnung</th>
                            <th>Fällig am</th>
                            <th class="num">Offen</th>
                            <th>Bisher</th>
                            <th>Vorschlag</th>
                            <th class="num">Gebühr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kandidaten as $k)
                            @php $stufe = $stufen[$k['next_level']]; @endphp
                            <tr>
                                <td><input class="mw-check" type="checkbox" name="invoice_ids[]" value="{{ $k['invoice']->id }}" checked></td>
                                <td class="mw-cust" title="{{ $k['recipient'] }}">{{ $k['recipient'] }}</td>
                                <td><a class="mw-btn-soft" style="border:none;padding:0;font-size:12.5px" href="{{ route('admin.invoices.show', $k['invoice']->id) }}">{{ $k['invoice']->invoice_no ?: '#' . $k['invoice']->id }}</a></td>
                                <td>{{ $k['invoice']->due_date?->format('d.m.Y') }} <span style="color:#9ca3af">({{ $k['days_overdue'] }} Tage)</span></td>
                                <td class="num" style="font-weight:700">{{ number_format($k['open'], 2, ',', '.') }} €</td>
                                <td>
                                    @if ($k['last_level'])
                                        <span class="mw-pill mw-pill-info"><i></i> Stufe {{ $k['last_level'] }} am {{ \Carbon\Carbon::parse($k['last_date'])->format('d.m.Y') }}</span>
                                    @else
                                        <span style="color:#9ca3af">noch nicht gemahnt</span>
                                    @endif
                                </td>
                                <td><span class="mw-pill {{ $k['next_level'] >= 3 ? 'mw-pill-danger' : ($k['next_level'] === 2 ? 'mw-pill-warning' : 'mw-pill-info') }}"><i></i> {{ $stufe['titel'] }}</span></td>
                                <td class="num">{{ number_format($stufe['gebuehr'], 2, ',', '.') }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mw-footbar">
                    <button type="submit" class="mw-btn" onclick="return confirm('Mahnlauf für die ausgewählten Rechnungen erzeugen?')">Mahnlauf für Auswahl erzeugen</button>
                </div>
            </form>
        @endif

        @if (!empty($gesperrt))
            <div class="mw-h2">Zurückgestellt ({{ count($gesperrt) }})</div>
            <table class="mw-table">
                <thead><tr><th>Kunde</th><th>Rechnung</th><th class="num">Offen</th><th>Grund</th></tr></thead>
                <tbody>
                    @foreach ($gesperrt as $g)
                        <tr>
                            <td class="mw-cust" title="{{ $g['recipient'] }}">{{ $g['recipient'] }}</td>
                            <td>{{ $g['invoice']->invoice_no ?: '#' . $g['invoice']->id }}</td>
                            <td class="num">{{ number_format($g['open'], 2, ',', '.') }} €</td>
                            <td><span class="mw-pill {{ str_contains($g['grund'], 'Inkasso') ? 'mw-pill-danger' : 'mw-pill-info' }}"><i></i> {{ $g['grund'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="mw-h2">Mahnläufe (letzte 20)</div>
        @if ($runs->isEmpty())
            <div class="mw-empty">Noch kein Mahnlauf ausgeführt.</div>
        @else
            @foreach ($runs as $run)
                <div class="mw-run-head">
                    Lauf vom {{ \Carbon\Carbon::parse($run->run_date)->format('d.m.Y') }}
                    <span class="sub">{{ $run->items_count }} Schreiben · Forderung gesamt {{ number_format($run->total_amount, 2, ',', '.') }} €</span>
                </div>
                <table class="mw-table">
                    <thead><tr><th>Empfänger</th><th>Stufe</th><th class="num">Offen</th><th class="num">Gebühr</th><th class="num">Gesamt</th><th>Zahlbar bis</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($run->items as $item)
                            <tr>
                                <td class="mw-cust" title="{{ $item->recipient_name }}">{{ $item->recipient_name }}</td>
                                <td><span class="mw-pill {{ $item->level >= 3 ? 'mw-pill-danger' : ($item->level == 2 ? 'mw-pill-warning' : 'mw-pill-info') }}"><i></i> Stufe {{ $item->level }}</span></td>
                                <td class="num">{{ number_format($item->open_amount, 2, ',', '.') }} €</td>
                                <td class="num">{{ number_format($item->fee, 2, ',', '.') }} €</td>
                                <td class="num" style="font-weight:700">{{ number_format($item->total_due, 2, ',', '.') }} €</td>
                                <td>{{ \Carbon\Carbon::parse($item->pay_until)->format('d.m.Y') }}</td>
                                <td><a class="mw-btn-soft" target="_blank" href="{{ route('admin.invoices.mahnwesen.brief', $item->id) }}">Schreiben {{ $item->printed_at ? '(gedruckt)' : 'drucken' }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endif
    @endif
</div>
@endsection
