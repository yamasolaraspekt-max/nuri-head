@extends('admin.layouts.app')
@section('title', 'Offene Posten')

{{--
    Offene Posten — Welle A1, Paket 1 (2026-07-16). Reine Lese-Fläche auf invoices.
    UI nach Styleguide (/admin/styleguide): sa-ui-Tokens, Pills mit hellem Grund + dunkler Tinte,
    kein Schwarz, kein Fremdblau. Leerzustand + Extremfälle (langer Kundenname) bedacht.
--}}

@section('content')
<style>
    .op-wrap { margin: 0 18px 40px; color: #1f2937; }
    .op-cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 4px 0 22px; }
    .op-card { flex: 1 1 150px; min-width: 150px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .op-card .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .op-card .v { font-size: 19px; font-weight: 800; margin-top: 4px; }
    .op-card .n { font-size: 11.5px; color: #6b7280; margin-top: 2px; }
    .op-card.tone-warning .v { color: #d97706; }
    .op-card.tone-danger .v { color: #b91c1c; }
    .op-card.tone-info .v { color: #374151; }
    .op-card.total { border-color: var(--sa-accent, #93c21c); background: var(--sa-accent-light, #f4fae7); }
    .op-card.total .v { color: #1f2937; }

    .op-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .op-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .op-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; color: #1f2937; vertical-align: middle; }
    .op-table tbody tr:hover { background: #f9fafb; }
    .op-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .op-table .open { font-weight: 700; }
    .op-cust { max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .op-link { color: #1f2937; text-decoration: none; font-weight: 600; }
    .op-link:hover { color: var(--sa-accent-hover, #7baa18); }

    .op-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .op-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .op-pill-info    { background: var(--sa-info-bg, #f3f4f6);    color: #374151; } .op-pill-info i    { background: var(--sa-info, #6b7280); }
    .op-pill-warning { background: var(--sa-warning-bg, #fff7ed); color: #d97706; } .op-pill-warning i { background: var(--sa-warning, #f59e0b); }
    .op-pill-danger  { background: var(--sa-danger-bg, #fef2f2);  color: #b91c1c; } .op-pill-danger i  { background: var(--sa-danger, #ef4444); }

    .op-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .op-foot { display: flex; justify-content: flex-end; font-size: 13px; font-weight: 700; padding: 12px 4px; color: #1f2937; }
    @media (max-width: 767px) { .op-cards { flex-direction: column; } .op-cust { max-width: 160px; } }
</style>

<x-page-head title="Offene Posten"
    sub="Alle Forderungen aus der führenden Rechnungs-Schiene: offen = Rechnungsbetrag minus Zahlungen. Entwürfe und Storni zählen nicht."
    current="Offene Posten">
    <x-slot:actions>
        <a href="{{ route('admin.invoices.index') }}" class="sg-btn sg-btn-soft" style="display:inline-flex;align-items:center;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;border:1px solid #d1d5db;background:#fff;color:#374151;text-decoration:none;">Zu den Rechnungen</a>
    </x-slot:actions>
</x-page-head>

<div class="op-wrap">
    <div class="op-cards">
        <div class="op-card total">
            <div class="k">Offen gesamt</div>
            <div class="v">{{ number_format($sumOpen, 2, ',', '.') }} €</div>
            <div class="n">{{ count($rows) }} {{ count($rows) === 1 ? 'Posten' : 'Posten' }}</div>
        </div>
        @foreach ($buckets as $bucket)
            <div class="op-card tone-{{ $bucket['tone'] }}">
                <div class="k">{{ $bucket['label'] }}</div>
                <div class="v">{{ number_format($bucket['sum'], 2, ',', '.') }} €</div>
                <div class="n">{{ $bucket['count'] }} Posten</div>
            </div>
        @endforeach
    </div>

    @if (empty($rows))
        <div class="op-empty">Keine offenen Posten — alle festgeschriebenen Rechnungen sind ausgeglichen.</div>
    @else
        <table class="op-table">
            <thead>
                <tr>
                    <th>Rechnung</th>
                    <th>Kunde</th>
                    <th>Rechnungsdatum</th>
                    <th>Fällig am</th>
                    <th>Fälligkeit</th>
                    <th class="num">Betrag</th>
                    <th class="num">Bezahlt</th>
                    <th class="num">Offen</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    @php
                        $inv = $row['invoice'];
                        $d = $row['days_overdue'];
                        $pillClass = 'op-pill-info';
                        if ($d !== null && $d > 30) { $pillClass = 'op-pill-danger'; }
                        elseif ($d !== null && $d > 0) { $pillClass = 'op-pill-warning'; }
                    @endphp
                    <tr>
                        <td><a class="op-link" href="{{ route('admin.invoices.show', $inv->id) }}">{{ $inv->invoice_no ?: '#' . $inv->id }}</a></td>
                        <td class="op-cust" title="{{ $inv->customer->name ?? '' }}">{{ $inv->customer->name ?? '— kein Kunde verknüpft —' }}</td>
                        <td>{{ $inv->issue_date?->format('d.m.Y') ?? '–' }}</td>
                        <td>{{ $inv->due_date?->format('d.m.Y') ?? '–' }}</td>
                        <td>
                            @if ($d === null)
                                <span class="op-pill op-pill-info"><i></i> Ohne Zahlungsziel</span>
                            @elseif ($d > 0)
                                <span class="op-pill {{ $pillClass }}"><i></i> {{ $d }} {{ $d === 1 ? 'Tag' : 'Tage' }} überfällig</span>
                            @elseif ($d === 0)
                                <span class="op-pill op-pill-warning"><i></i> Heute fällig</span>
                            @else
                                <span class="op-pill op-pill-info"><i></i> Fällig in {{ abs($d) }} {{ abs($d) === 1 ? 'Tag' : 'Tagen' }}</span>
                            @endif
                        </td>
                        <td class="num">{{ number_format((float) $inv->total_amount, 2, ',', '.') }} €</td>
                        <td class="num">{{ number_format((float) $inv->paid_amount, 2, ',', '.') }} €</td>
                        <td class="num open">{{ number_format($row['open'], 2, ',', '.') }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="op-foot">Summe offen: {{ number_format($sumOpen, 2, ',', '.') }} €</div>
    @endif
</div>
@endsection
