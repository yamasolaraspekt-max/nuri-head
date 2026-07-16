@extends('admin.layouts.app')
@section('title', 'Gutschriften')

{{--
    Gutschriften — Welle B1, Paket 2 (2026-07-16). Register aller Gutschriften/Stornorechnungen
    (Invoice::TYPEN_OHNE_ZAHLUNG). Erstellung weiterhin am Rechnungs-Workflow (Storno S1-04).
    Ehrlichkeits-Grenze: keine „Bezug"-Spalte — das Schema kennt keine Ursprungs-Verknüpfung.
--}}

@section('content')
<style>
    .gs-wrap { margin: 0 18px 40px; color: #1f2937; }
    .gs-filter { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 4px 0 18px; }
    .gs-filter select { border: 1px solid #d1d5db; border-radius: 8px; padding: 7px 10px; font-size: 13px; color: #1f2937; background: #fff; }
    .gs-cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 0 0 18px; }
    .gs-card { flex: 1 1 180px; min-width: 180px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .gs-card .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .gs-card .v { font-size: 19px; font-weight: 800; margin-top: 4px; }
    .gs-card .n { font-size: 11.5px; color: #6b7280; margin-top: 2px; }

    .gs-hinweis { background: var(--sa-info-bg, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; font-size: 12.5px; color: #374151; margin: 0 0 18px; }

    .gs-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .gs-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .gs-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .gs-table tbody tr:hover { background: #f9fafb; }
    .gs-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .gs-cust { max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .gs-link { color: #1f2937; text-decoration: none; font-weight: 600; }
    .gs-link:hover { color: var(--sa-accent-hover, #7baa18); }

    .gs-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .gs-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .gs-pill-info    { background: var(--sa-info-bg, #f3f4f6);    color: #374151; } .gs-pill-info i    { background: var(--sa-info, #6b7280); }
    .gs-pill-warning { background: var(--sa-warning-bg, #fff7ed); color: #d97706; } .gs-pill-warning i { background: var(--sa-warning, #f59e0b); }

    .gs-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .gs-pager { display: flex; gap: 8px; align-items: center; justify-content: flex-end; margin-top: 14px; font-size: 12.5px; color: #6b7280; }
    .gs-pager a { border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 12px; color: #374151; text-decoration: none; font-weight: 600; background: #fff; }
    .gs-pager a:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
</style>

<div class="gs-wrap">
    <x-page-head title="Gutschriften" sub="Register aller Gutschriften und Stornorechnungen — erstellt wird am Rechnungs-Workflow (Storno)." current="Gutschriften">
        <x-slot:actions>
        <a href="{{ route('admin.invoices.index') }}" style="display:inline-flex;align-items:center;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;border:1px solid #d1d5db;background:#fff;color:#374151;text-decoration:none;">Zu den Rechnungen</a>
        </x-slot:actions>
    </x-page-head>

    <form method="get" class="gs-filter">
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Zeitraum</label>
        <select name="jahr" onchange="this.form.submit()">
            <option value="alle" @selected($jahr === 'alle')>Alle Jahre</option>
            @foreach ($jahre as $j)
                <option value="{{ $j }}" @selected((string) $j === $jahr)>{{ $j }}</option>
            @endforeach
        </select>
    </form>

    <div class="gs-cards">
        @php
            $g = $jeTyp->get('gutschrift');
            $s = $jeTyp->get('stornorechnung');
        @endphp
        <div class="gs-card">
            <div class="k">Gutschriften</div>
            <div class="v">{{ number_format((float) ($g->summe ?? 0), 2, ',', '.') }} €</div>
            <div class="n">{{ (int) ($g->anzahl ?? 0) }} Belege</div>
        </div>
        <div class="gs-card">
            <div class="k">Stornorechnungen</div>
            <div class="v">{{ number_format((float) ($s->summe ?? 0), 2, ',', '.') }} €</div>
            <div class="n">{{ (int) ($s->anzahl ?? 0) }} Belege</div>
        </div>
    </div>

    <div class="gs-hinweis">
        Gutschriften und Stornorechnungen tragen kein Zahlungsziel und zählen nie als bezahlt
        (Typ-Regel der Rechnung). Eine Verknüpfung zur Ursprungsrechnung ist im Datenbestand
        nicht hinterlegt — sie wird hier bewusst nicht erfunden.
    </div>

    @if ($gutschriften->isEmpty())
        <div class="gs-empty">
            Keine Gutschriften oder Stornorechnungen im gewählten Zeitraum.<br>
            Eine Gutschrift entsteht über die betroffene Rechnung (Storno-Workflow).
        </div>
    @else
        <table class="gs-table">
            <thead>
                <tr>
                    <th>Nr.</th>
                    <th>Datum</th>
                    <th>Kunde</th>
                    <th>Typ</th>
                    <th>Status</th>
                    <th class="num">Betrag</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gutschriften as $inv)
                    @php
                        $kunde = $inv->customer?->firma ?: trim(($inv->customer->name ?? '') . ' ' . ($inv->customer->lastname ?? ''));
                        $istStorno = mb_strtolower(trim((string) $inv->type)) === 'stornorechnung';
                    @endphp
                    <tr>
                        <td><a class="gs-link" href="{{ route('admin.invoices.show', $inv->id) }}">{{ $inv->invoice_no ?: '#' . $inv->id }}</a></td>
                        <td>{{ $inv->issue_date?->format('d.m.Y') }}</td>
                        <td class="gs-cust">{{ $kunde ?: '—' }}</td>
                        <td><span class="gs-pill gs-pill-{{ $istStorno ? 'warning' : 'info' }}"><i></i>{{ $istStorno ? 'Stornorechnung' : 'Gutschrift' }}</span></td>
                        <td>{{ ucfirst((string) $inv->status) }}</td>
                        <td class="num">{{ number_format((float) $inv->total_amount, 2, ',', '.') }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="gs-pager">
            <span>{{ $gutschriften->firstItem() }}–{{ $gutschriften->lastItem() }} von {{ $gutschriften->total() }}</span>
            @if ($gutschriften->previousPageUrl())<a href="{{ $gutschriften->previousPageUrl() }}">‹ Zurück</a>@endif
            @if ($gutschriften->nextPageUrl())<a href="{{ $gutschriften->nextPageUrl() }}">Weiter ›</a>@endif
        </div>
    @endif
</div>
@endsection
