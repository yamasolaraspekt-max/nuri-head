@extends('admin.layouts.app')
@section('title', 'Materialbedarf & Bestellungen')

{{--
    Materialbedarf & Bestellungen — Arbeitsvorbereitung (2026-07-16). Lese-Übersicht:
    je Auftrag der Materialstand; gepflegt wird auf der bestehenden Materialliste (deal.material.list).
--}}

@section('content')
<style>
    .mb-wrap { margin: 0 18px 40px; color: #1f2937; }
    .mb-cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 4px 0 18px; }
    .mb-card { flex: 1 1 170px; min-width: 170px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .mb-card .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .mb-card .v { font-size: 19px; font-weight: 800; margin-top: 4px; color: #1f2937; }
    .mb-card .n { font-size: 11.5px; color: #6b7280; margin-top: 2px; }
    .mb-card.warn { border-color: var(--sa-warning, #f59e0b); } .mb-card.warn .v { color: #d97706; }
    .mb-card.order { border-color: var(--sa-accent, #93c21c); background: var(--sa-accent-light, #f4fae7); }
    .mb-switch { display: flex; gap: 8px; margin: 0 0 16px; }
    .mb-chip { border: 1px solid #d1d5db; background: #fff; border-radius: 999px; padding: 5px 14px; font-size: 12px; color: #374151; text-decoration: none; }
    .mb-chip.is-active { background: var(--sa-accent, #93c21c); border-color: var(--sa-accent, #93c21c); color: #fff; font-weight: 700; }
    .mb-chip:hover { border-color: var(--sa-accent, #93c21c); }
    .mb-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .mb-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .mb-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .mb-table tbody tr:hover { background: #f9fafb; }
    .mb-cust { max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .mb-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .mb-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .mb-pill-info    { background: var(--sa-info-bg, #f3f4f6);    color: #374151; } .mb-pill-info i    { background: var(--sa-info, #6b7280); }
    .mb-pill-warning { background: var(--sa-warning-bg, #fff7ed); color: #d97706; } .mb-pill-warning i { background: var(--sa-warning, #f59e0b); }
    .mb-pill-success { background: var(--sa-success-bg, #ecfdf5); color: #15803d; } .mb-pill-success i { background: var(--sa-success, #10b981); }
    .mb-pill-danger  { background: var(--sa-danger-bg, #fef2f2);  color: #b91c1c; } .mb-pill-danger i  { background: var(--sa-danger, #ef4444); }
    .mb-btn-soft { display: inline-flex; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; }
    .mb-btn-soft:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
    .mb-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
</style>

<x-page-head title="Materialbedarf & Bestellungen"
    sub="Der Materialstand aller laufenden Aufträge auf einen Blick — gepflegt wird in der Materialliste des Auftrags. Verhindert den teuersten Fall: Monteur ohne Material."
    current="Materialbedarf">
    <x-slot:actions>
        <a href="{{ route('deal.auftragseingang') }}" class="mb-btn-soft">Zum Auftragseingang</a>
    </x-slot:actions>
</x-page-head>

<div class="mb-wrap">
    <div class="mb-switch">
        @foreach ([30, 90, 180, 365] as $window)
            <a class="mb-chip {{ $days === $window ? 'is-active' : '' }}" href="{{ route('deal.materialbedarf', ['days' => $window]) }}">{{ $window }} Tage</a>
        @endforeach
    </div>

    <div class="mb-cards">
        <div class="mb-card">
            <div class="k">Laufende Aufträge</div>
            <div class="v">{{ $rows->count() }}</div>
            <div class="n">letzte {{ $days }} Tage, ohne abgeschlossene</div>
        </div>
        <div class="mb-card warn">
            <div class="k">Material ungepflegt</div>
            <div class="v">{{ $ohnePflege }}</div>
            <div class="n">Liste vorhanden, noch nie gespeichert</div>
        </div>
        <div class="mb-card order">
            <div class="k">Letzte Aktion: bestellen</div>
            <div class="v">{{ $bestellen }}</div>
            <div class="n">offener Bestellbedarf vermerkt</div>
        </div>
        <div class="mb-card">
            <div class="k">Ohne Materialliste</div>
            <div class="v">{{ $ohneListe }}</div>
            <div class="n">Auftrag ohne Angebots-Detail</div>
        </div>
    </div>

    @if ($rows->isEmpty())
        <div class="mb-empty">Keine laufenden Aufträge im Fenster.</div>
    @else
        <table class="mb-table">
            <thead>
                <tr>
                    <th>Auftrag</th>
                    <th>Kunde</th>
                    <th>Feinaufmaß</th>
                    <th>Material zuletzt gepflegt</th>
                    <th>Letzte Material-Aktion</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $r)
                    @php $deal = $r['deal']; @endphp
                    <tr>
                        <td>{{ $deal->order_number ?: '#' . $deal->id }}</td>
                        <td class="mb-cust" title="{{ $r['kunde'] }}">{{ $r['kunde'] }}</td>
                        <td><span class="mb-pill mb-pill-info"><i></i> {{ $deal->measurement_status ?: 'offen' }}</span></td>
                        <td>
                            @if (!$r['detail_id'])
                                <span class="mb-pill mb-pill-info"><i></i> ohne Materialliste</span>
                            @elseif ($r['saved_at'])
                                {{ \Carbon\Carbon::parse($r['saved_at'])->format('d.m.Y H:i') }}
                            @else
                                <span class="mb-pill mb-pill-warning"><i></i> noch nie</span>
                            @endif
                        </td>
                        <td>
                            @if ($r['last_status'] === 'bestellen')
                                <span class="mb-pill mb-pill-danger"><i></i> bestellen</span>
                            @elseif ($r['last_status'] === 'teilweise')
                                <span class="mb-pill mb-pill-warning"><i></i> teilweise</span>
                            @elseif ($r['last_status'] === 'lager')
                                <span class="mb-pill mb-pill-success"><i></i> Lager</span>
                            @elseif ($r['last_status'])
                                <span class="mb-pill mb-pill-info"><i></i> {{ $r['last_status'] }}</span>
                            @else
                                <span style="color:#9ca3af">–</span>
                            @endif
                            @if ($r['last_at'])
                                <span style="color:#9ca3af; font-size:11.5px"> {{ \Carbon\Carbon::parse($r['last_at'])->format('d.m.Y') }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($r['detail_id'])
                                <a class="mb-btn-soft" href="{{ route('deal.material.list', $r['detail_id']) }}">Materialliste öffnen</a>
                            @else
                                <a class="mb-btn-soft" href="{{ route('deal.all.list') }}">Auftrag öffnen</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
