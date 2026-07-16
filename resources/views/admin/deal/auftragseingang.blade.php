@extends('admin.layouts.app')
@section('title', 'Auftragseingang')

{{--
    Auftragseingang & AB — Welle A2 (2026-07-16, Spec: docs/planner-spec-auftragseingang-ab.md).
    Eine Fläche, zwei Blöcke: Eingang (deals im Zeitraum, AB-Stand je Auftrag) + AB-Historie.
    Lese-Fläche; einzige Schreibaktion = „AB erzeugen" (ein Insert, append-only).
--}}

@section('content')
<style>
    .ae-wrap { margin: 0 18px 40px; color: #1f2937; }
    .ae-cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 4px 0 18px; }
    .ae-card { flex: 1 1 170px; min-width: 170px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .ae-card .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .ae-card .v { font-size: 19px; font-weight: 800; margin-top: 4px; color: #1f2937; }
    .ae-card .n { font-size: 11.5px; color: #6b7280; margin-top: 2px; }
    .ae-card.sum { border-color: var(--sa-accent, #93c21c); background: var(--sa-accent-light, #f4fae7); }
    .ae-switch { display: flex; gap: 8px; margin: 0 0 16px; }
    .ae-chip { border: 1px solid #d1d5db; background: #fff; border-radius: 999px; padding: 5px 14px; font-size: 12px; color: #374151; text-decoration: none; }
    .ae-chip.is-active { background: var(--sa-accent, #93c21c); border-color: var(--sa-accent, #93c21c); color: #fff; font-weight: 700; }
    .ae-chip:hover { border-color: var(--sa-accent, #93c21c); }
    .ae-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .ae-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .ae-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .ae-table tbody tr:hover { background: #f9fafb; }
    .ae-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .ae-cust { max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ae-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .ae-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .ae-pill-info    { background: var(--sa-info-bg, #f3f4f6);    color: #374151; } .ae-pill-info i    { background: var(--sa-info, #6b7280); }
    .ae-pill-warning { background: var(--sa-warning-bg, #fff7ed); color: #d97706; } .ae-pill-warning i { background: var(--sa-warning, #f59e0b); }
    .ae-pill-success { background: var(--sa-success-bg, #ecfdf5); color: #15803d; } .ae-pill-success i { background: var(--sa-success, #10b981); }
    .ae-btn-soft { display: inline-flex; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; cursor: pointer; }
    .ae-btn-soft:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
    .ae-btn { display: inline-flex; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 700; border: 1px solid transparent; background: var(--sa-accent, #93c21c); color: #fff; cursor: pointer; }
    .ae-btn:hover { background: var(--sa-accent-hover, #7baa18); }
    .ae-h2 { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: #374151; margin: 28px 0 10px; }
    .ae-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .ae-hint { background: var(--sa-info-bg, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; font-size: 12.5px; color: #374151; margin: 0 0 16px; max-width: 980px; }
</style>

<x-page-head title="Auftragseingang"
    sub="Neue Aufträge im Zeitraum — mit Stand der Auftragsbestätigung je Auftrag. Die AB friert Kopf + Angebots-Positionen unveränderlich ein."
    current="Auftragseingang">
    <x-slot:actions>
        <a href="{{ route('deal.all.list') }}" class="ae-btn-soft">Zur Auftragsliste</a>
    </x-slot:actions>
</x-page-head>

<div class="ae-wrap">
    @unless ($abTabelle)
        <div class="ae-hint">Die AB-Tabelle fehlt noch: einmal <code>php artisan migrate</code> ausführen (Migration <code>2026_07_16_130001</code>, additiv). Die Eingangs-Liste funktioniert schon jetzt.</div>
    @endunless

    <div class="ae-switch">
        @foreach ([7, 30, 90, 365] as $window)
            <a class="ae-chip {{ $days === $window ? 'is-active' : '' }}" href="{{ route('deal.auftragseingang', ['days' => $window]) }}">{{ $window }} Tage</a>
        @endforeach
    </div>

    <div class="ae-cards">
        <div class="ae-card">
            <div class="k">Aufträge ({{ $days }} Tage)</div>
            <div class="v">{{ $rows->count() }}</div>
            <div class="n">ohne Spam/Papierkorb</div>
        </div>
        <div class="ae-card sum">
            <div class="k">Auftragswert</div>
            <div class="v">{{ number_format($summe, 2, ',', '.') }} €</div>
            <div class="n">Summe der Auftragspreise</div>
        </div>
        <div class="ae-card">
            <div class="k">Ohne AB</div>
            <div class="v">{{ $rows->filter(fn ($r) => !$r['ab'])->count() }}</div>
            <div class="n">Bestätigung noch nicht erzeugt</div>
        </div>
    </div>

    @if ($rows->isEmpty())
        <div class="ae-empty">Kein Auftragseingang in den letzten {{ $days }} Tagen.</div>
    @else
        <table class="ae-table">
            <thead>
                <tr>
                    <th>Eingegangen</th>
                    <th>Auftrag</th>
                    <th>Kunde</th>
                    <th class="num">Wert</th>
                    <th>Auftragsbestätigung</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $r)
                    @php $deal = $r['deal']; @endphp
                    <tr>
                        <td>{{ $deal->created_at?->format('d.m.Y') }}</td>
                        <td>{{ $deal->order_number ?: '– keine Auftragsnummer –' }}</td>
                        <td class="ae-cust" title="{{ $r['kunde'] }}">{{ $r['kunde'] }}</td>
                        <td class="num">{{ $deal->price !== null ? number_format((float) $deal->price, 2, ',', '.') . ' €' : '–' }}</td>
                        <td>
                            @if ($r['ab'])
                                <span class="ae-pill ae-pill-success"><i></i> AB vom {{ \Carbon\Carbon::parse($r['ab']->created_at)->format('d.m.Y') }}{{ $r['ab']->printed_at ? ' · gedruckt' : '' }}</span>
                            @else
                                <span class="ae-pill ae-pill-warning"><i></i> keine AB</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            @if ($r['ab'])
                                <a class="ae-btn-soft" target="_blank" href="{{ route('deal.auftragseingang.ab', $r['ab']->id) }}">AB öffnen</a>
                            @endif
                            @if ($abTabelle)
                                <form method="POST" action="{{ route('deal.auftragseingang.ab.erzeugen', $deal->id) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="ae-btn" onclick="return confirm('Auftragsbestätigung für {{ $deal->order_number ?: 'Auftrag #' . $deal->id }} erzeugen?')">{{ $r['ab'] ? 'Neue AB' : 'AB erzeugen' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="ae-h2" id="ab-historie">Auftragsbestätigungen (letzte 30)</div>
    @if ($abHistorie->isEmpty())
        <div class="ae-empty">Noch keine Auftragsbestätigung erzeugt.</div>
    @else
        <table class="ae-table">
            <thead><tr><th>Erzeugt</th><th>AB-Nummer</th><th>Empfänger</th><th class="num">Brutto</th><th>Stand</th><th></th></tr></thead>
            <tbody>
                @foreach ($abHistorie as $ab)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($ab->created_at)->format('d.m.Y H:i') }}</td>
                        <td>{{ $ab->ab_no ?: '– ohne Nummer –' }}</td>
                        <td class="ae-cust" title="{{ $ab->recipient_name }}">{{ $ab->recipient_name }}</td>
                        <td class="num">{{ $ab->total_gross !== null ? number_format($ab->total_gross, 2, ',', '.') . ' €' : '–' }}</td>
                        <td>
                            @if ($ab->ohne_snapshot)
                                <span class="ae-pill ae-pill-info"><i></i> ohne Positionsliste</span>
                            @endif
                            <span class="ae-pill {{ $ab->printed_at ? 'ae-pill-success' : 'ae-pill-info' }}"><i></i> {{ $ab->printed_at ? 'gedruckt' : 'erzeugt' }}</span>
                        </td>
                        <td><a class="ae-btn-soft" target="_blank" href="{{ route('deal.auftragseingang.ab', $ab->id) }}">Öffnen</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
