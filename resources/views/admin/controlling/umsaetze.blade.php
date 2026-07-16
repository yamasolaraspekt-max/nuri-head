@extends('admin.layouts.app')
@section('title', 'Umsätze')

{{--
    Umsätze — Welle B1, Paket 3 (2026-07-16). Erste Controlling-Fläche: Monatsraster aus der
    einzigen Umsatz-Wahrheit invoices (ausgestellte echte Rechnungen nach issue_date), mit
    Vorjahresvergleich. Gutschriften NACHRICHTLICH in eigener Spalte — keine stille Verrechnung.
--}}

@section('content')
<style>
    .um-wrap { margin: 0 18px 40px; color: #1f2937; }
    .um-filter { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 4px 0 18px; }
    .um-filter select { border: 1px solid #d1d5db; border-radius: 8px; padding: 7px 10px; font-size: 13px; color: #1f2937; background: #fff; }
    .um-cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 0 0 22px; }
    .um-card { flex: 1 1 170px; min-width: 170px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .um-card .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .um-card .v { font-size: 19px; font-weight: 800; margin-top: 4px; }
    .um-card .n { font-size: 11.5px; color: #6b7280; margin-top: 2px; }
    .um-card.total { border-color: var(--sa-accent, #93c21c); background: var(--sa-accent-light, #f4fae7); }
    .um-card.tone-success .v { color: #047857; }
    .um-card.tone-info .v { color: #374151; }

    .um-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .um-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .um-table th.num, .um-table td.num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .um-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .um-table tbody tr:hover { background: #f9fafb; }
    .um-table tfoot td { font-weight: 800; background: #f9fafb; border-top: 2px solid #e5e7eb; padding: 10px 12px; }
    .um-balken { display: inline-block; height: 8px; border-radius: 4px; background: var(--sa-accent, #93c21c); vertical-align: middle; min-width: 2px; }
    .um-delta-plus { color: #047857; font-weight: 700; }
    .um-delta-minus { color: #b91c1c; font-weight: 700; }
    .um-leer { color: #9ca3af; }
    .um-hinweis { background: var(--sa-info-bg, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; font-size: 12.5px; color: #374151; margin: 14px 0 0; }
</style>

<div class="um-wrap">
    <x-page-head title="Umsätze" sub="Monatsumsatz aus ausgestellten Rechnungen (Rechnungsdatum) — mit Vorjahresvergleich." current="Umsätze">
        <x-slot:actions>
        <a href="{{ route('admin.invoices.ausgangsrechnungen') }}" style="display:inline-flex;align-items:center;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;border:1px solid #d1d5db;background:#fff;color:#374151;text-decoration:none;">Zum Register</a>
        </x-slot:actions>
    </x-page-head>

    <form method="get" class="um-filter">
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Jahr</label>
        <select name="jahr" onchange="this.form.submit()">
            @foreach ($jahre as $j)
                <option value="{{ $j }}" @selected($j === $jahr)>{{ $j }}</option>
            @endforeach
        </select>
    </form>

    <div class="um-cards">
        <div class="um-card total">
            <div class="k">Umsatz {{ $jahr }}</div>
            <div class="v">{{ number_format($kpi['summe'], 2, ',', '.') }} €</div>
            <div class="n">{{ $kpi['anzahl'] }} ausgestellte Rechnungen</div>
        </div>
        <div class="um-card tone-success">
            <div class="k">Davon bezahlt</div>
            <div class="v">{{ number_format($kpi['bezahlt'], 2, ',', '.') }} €</div>
            <div class="n">Status „bezahlt" (volle Rechnungssumme)</div>
        </div>
        <div class="um-card tone-info">
            <div class="k">Vorjahr {{ $jahr - 1 }}</div>
            <div class="v">{{ number_format($kpi['vorjahr'], 2, ',', '.') }} €</div>
            <div class="n">
                @if ($kpi['delta_prozent'] !== null)
                    <span class="{{ $kpi['delta_prozent'] >= 0 ? 'um-delta-plus' : 'um-delta-minus' }}">
                        {{ ($kpi['delta_prozent'] >= 0 ? '+' : '') . number_format($kpi['delta_prozent'], 1, ',', '.') }} %
                    </span> gegenüber Vorjahr
                @else
                    kein Vorjahreswert
                @endif
            </div>
        </div>
        <div class="um-card tone-info">
            <div class="k">Gutschriften/Storni</div>
            <div class="v">{{ number_format($kpi['gutschriften_summe'], 2, ',', '.') }} €</div>
            <div class="n">nachrichtlich — nicht verrechnet</div>
        </div>
    </div>

    @php
        $maxSumme = max(1, max(array_column($monate, 'summe')));
        $monatsNamen = [1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
    @endphp

    <table class="um-table">
        <thead>
            <tr>
                <th>Monat</th>
                <th class="num">Rechnungen</th>
                <th class="num">Umsatz</th>
                <th style="width:22%;"></th>
                <th class="num">Davon bezahlt</th>
                <th class="num">Vorjahr</th>
                <th class="num">Δ Vorjahr</th>
                <th class="num">Gutschriften (nachr.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monate as $m => $w)
                <tr>
                    <td>{{ $monatsNamen[$m] }}</td>
                    <td class="num">{{ $w['anzahl'] > 0 ? $w['anzahl'] : '' }}</td>
                    <td class="num">{{ $w['summe'] > 0 ? number_format($w['summe'], 2, ',', '.') . ' €' : '' }}</td>
                    <td>@if ($w['summe'] > 0)<span class="um-balken" style="width: {{ max(2, round($w['summe'] / $maxSumme * 100)) }}%;"></span>@endif</td>
                    <td class="num">{{ $w['bezahlt'] > 0 ? number_format($w['bezahlt'], 2, ',', '.') . ' €' : '' }}</td>
                    <td class="num um-leer">{{ $w['vorjahr'] > 0 ? number_format($w['vorjahr'], 2, ',', '.') . ' €' : '' }}</td>
                    <td class="num">
                        @if ($w['delta_prozent'] !== null)
                            <span class="{{ $w['delta_prozent'] >= 0 ? 'um-delta-plus' : 'um-delta-minus' }}">{{ ($w['delta_prozent'] >= 0 ? '+' : '') . number_format($w['delta_prozent'], 0, ',', '.') }} %</span>
                        @endif
                    </td>
                    <td class="num um-leer">{{ $w['gutschriften_summe'] > 0 ? number_format($w['gutschriften_summe'], 2, ',', '.') . ' €' : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Gesamt</td>
                <td class="num">{{ $kpi['anzahl'] }}</td>
                <td class="num">{{ number_format($kpi['summe'], 2, ',', '.') }} €</td>
                <td></td>
                <td class="num">{{ number_format($kpi['bezahlt'], 2, ',', '.') }} €</td>
                <td class="num">{{ number_format($kpi['vorjahr'], 2, ',', '.') }} €</td>
                <td class="num">
                    @if ($kpi['delta_prozent'] !== null)
                        <span class="{{ $kpi['delta_prozent'] >= 0 ? 'um-delta-plus' : 'um-delta-minus' }}">{{ ($kpi['delta_prozent'] >= 0 ? '+' : '') . number_format($kpi['delta_prozent'], 1, ',', '.') }} %</span>
                    @endif
                </td>
                <td class="num">{{ number_format($kpi['gutschriften_summe'], 2, ',', '.') }} €</td>
            </tr>
        </tfoot>
    </table>

    <div class="um-hinweis">
        Umsatz = Summe ausgestellter echter Rechnungen (Status gesendet/bezahlt/überfällig) nach
        Rechnungsdatum. Gutschriften und Stornorechnungen werden nachrichtlich ausgewiesen und
        bewusst <strong>nicht</strong> verrechnet — die Verrechnungsregel ist ein eigener Entscheid.
    </div>
</div>
@endsection
