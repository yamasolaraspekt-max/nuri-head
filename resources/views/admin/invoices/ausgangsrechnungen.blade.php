@extends('admin.layouts.app')
@section('title', 'Ausgangsrechnungen')

{{--
    Ausgangsrechnungen — Welle B1, Paket 1 (2026-07-16). Kaufmännisches Register je Zeitraum
    (ein Hauptjob) — die operative Arbeit an Rechnungen bleibt auf der Rechnungsliste, jede
    Zeile verlinkt dorthin. sa-ui-Tokens, Pills Farbe+Text, kein Schwarz, kein Fremdblau.
--}}

@section('content')
<style>
    .ar-wrap { margin: 0 18px 40px; color: #1f2937; }
    .ar-filter { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 4px 0 18px; }
    .ar-filter select { border: 1px solid #d1d5db; border-radius: 8px; padding: 7px 10px; font-size: 13px; color: #1f2937; background: #fff; }
    .ar-cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 0 0 22px; }
    .ar-card { flex: 1 1 160px; min-width: 160px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .ar-card .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }
    .ar-card .v { font-size: 19px; font-weight: 800; margin-top: 4px; }
    .ar-card .n { font-size: 11.5px; color: #6b7280; margin-top: 2px; }
    .ar-card.total { border-color: var(--sa-accent, #93c21c); background: var(--sa-accent-light, #f4fae7); }
    .ar-card.tone-warning .v { color: #d97706; }
    .ar-card.tone-info .v { color: #374151; }

    .ar-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .ar-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .ar-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .ar-table tbody tr:hover { background: #f9fafb; }
    .ar-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .ar-cust { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ar-link { color: #1f2937; text-decoration: none; font-weight: 600; }
    .ar-link:hover { color: var(--sa-accent-hover, #7baa18); }
    .ar-storniert td { color: #6b7280; text-decoration: line-through; }
    .ar-storniert td .ar-pill, .ar-storniert td .ar-link { text-decoration: none; }

    .ar-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .ar-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .ar-pill-info    { background: var(--sa-info-bg, #f3f4f6);    color: #374151; } .ar-pill-info i    { background: var(--sa-info, #6b7280); }
    .ar-pill-warning { background: var(--sa-warning-bg, #fff7ed); color: #d97706; } .ar-pill-warning i { background: var(--sa-warning, #f59e0b); }
    .ar-pill-danger  { background: var(--sa-danger-bg, #fef2f2);  color: #b91c1c; } .ar-pill-danger i  { background: var(--sa-danger, #ef4444); }
    .ar-pill-success { background: var(--sa-success-bg, #ecfdf5); color: #047857; } .ar-pill-success i { background: var(--sa-success, #10b981); }

    .ar-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .ar-pager { display: flex; gap: 8px; align-items: center; justify-content: flex-end; margin-top: 14px; font-size: 12.5px; color: #6b7280; }
    .ar-pager a { border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 12px; color: #374151; text-decoration: none; font-weight: 600; background: #fff; }
    .ar-pager a:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
</style>

<div class="ar-wrap">
    <x-page-head title="Ausgangsrechnungen" sub="Kaufmännisches Register der ausgestellten Rechnungen — Arbeiten an Rechnungen weiterhin über die Rechnungsliste." current="Ausgangsrechnungen">
        <x-slot:actions>
        <a href="{{ route('admin.invoices.index') }}" style="display:inline-flex;align-items:center;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;border:1px solid #d1d5db;background:#fff;color:#374151;text-decoration:none;">Zu den Rechnungen</a>
        </x-slot:actions>
    </x-page-head>

    <form method="get" class="ar-filter">
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Zeitraum</label>
        <select name="jahr" onchange="this.form.submit()">
            @foreach ($jahre as $j)
                <option value="{{ $j }}" @selected($j === $jahr)>{{ $j }}</option>
            @endforeach
        </select>
        <select name="monat" onchange="this.form.submit()">
            <option value="">Ganzes Jahr</option>
            @foreach (['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'] as $i => $name)
                <option value="{{ $i + 1 }}" @selected($monat === $i + 1)>{{ $name }}</option>
            @endforeach
        </select>
    </form>

    <div class="ar-cards">
        <div class="ar-card total">
            <div class="k">Rechnungssumme</div>
            <div class="v">{{ number_format((float) ($agg->summe_rechnungen ?? 0), 2, ',', '.') }} €</div>
            <div class="n">{{ (int) ($agg->anzahl_rechnungen ?? 0) }} ausgestellte Rechnungen</div>
        </div>
        <div class="ar-card tone-warning">
            <div class="k">Davon offen</div>
            <div class="v">{{ number_format((float) ($agg->summe_offen ?? 0), 2, ',', '.') }} €</div>
            <div class="n">Details: Offene Posten</div>
        </div>
        <div class="ar-card tone-info">
            <div class="k">Gutschriften/Storni</div>
            <div class="v">{{ number_format((float) ($agg->summe_gutschriften ?? 0), 2, ',', '.') }} €</div>
            <div class="n">{{ (int) ($agg->anzahl_gutschriften ?? 0) }} Belege — nachrichtlich, nicht verrechnet</div>
        </div>
        <div class="ar-card tone-info">
            <div class="k">Storniert</div>
            <div class="v">{{ (int) ($agg->anzahl_storniert ?? 0) }}</div>
            <div class="n">im Register gekennzeichnet</div>
        </div>
    </div>

    @if ($invoices->isEmpty())
        <div class="ar-empty">
            Im gewählten Zeitraum wurden keine Rechnungen ausgestellt.<br>
            Anderen Zeitraum wählen — oder eine Rechnung über die Rechnungsliste anlegen.
        </div>
    @else
        <table class="ar-table">
            <thead>
                <tr>
                    <th>Nr.</th>
                    <th>Datum</th>
                    <th>Kunde</th>
                    <th>Typ</th>
                    <th>Status</th>
                    <th class="num">Betrag</th>
                    <th class="num">Offen</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $inv)
                    @php
                        $status = mb_strtolower((string) $inv->status);
                        $istGutschrift = in_array(mb_strtolower(trim((string) $inv->type)), $typenOhneZahlung, true);
                        $pill = match ($status) {
                            'paid' => ['success', 'Bezahlt'],
                            'overdue' => ['danger', 'Überfällig'],
                            'cancelled' => ['info', 'Storniert'],
                            default => ['warning', 'Offen'],
                        };
                        $kunde = $inv->customer?->firma ?: trim(($inv->customer->name ?? '') . ' ' . ($inv->customer->lastname ?? ''));
                    @endphp
                    <tr @class(['ar-storniert' => $status === 'cancelled'])>
                        <td><a class="ar-link" href="{{ route('admin.invoices.show', $inv->id) }}">{{ $inv->invoice_no ?: '#' . $inv->id }}</a></td>
                        <td>{{ $inv->issue_date?->format('d.m.Y') }}</td>
                        <td class="ar-cust">{{ $kunde ?: '—' }}</td>
                        <td>{{ $inv->type ?: 'Rechnung' }}</td>
                        <td><span class="ar-pill ar-pill-{{ $pill[0] }}"><i></i>{{ $pill[1] }}</span></td>
                        <td class="num">{{ number_format((float) $inv->total_amount, 2, ',', '.') }} €</td>
                        <td class="num">{{ $istGutschrift || $status === 'cancelled' ? '—' : number_format((float) $inv->open_amount, 2, ',', '.') . ' €' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ar-pager">
            <span>{{ $invoices->firstItem() }}–{{ $invoices->lastItem() }} von {{ $invoices->total() }}</span>
            @if ($invoices->previousPageUrl())<a href="{{ $invoices->previousPageUrl() }}">‹ Zurück</a>@endif
            @if ($invoices->nextPageUrl())<a href="{{ $invoices->nextPageUrl() }}">Weiter ›</a>@endif
        </div>
    @endif
</div>
@endsection
