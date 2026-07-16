@extends('admin.layouts.app')
@section('title', 'Nachfassen fällig')

{{--
    Nachfassen fällig — Welle A2 (2026-07-16). Reine Lese-Fläche nach Styleguide:
    aktive Angebote ohne Bewegung seit X Tagen (config/vertrieb.php), älteste zuerst.
--}}

@section('content')
<style>
    .nf-wrap { margin: 0 18px 40px; color: #1f2937; }
    .nf-cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 4px 0 22px; }
    .nf-card { flex: 1 1 170px; min-width: 170px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .nf-card .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .nf-card .v { font-size: 19px; font-weight: 800; margin-top: 4px; color: #1f2937; }
    .nf-card .n { font-size: 11.5px; color: #6b7280; margin-top: 2px; }
    .nf-card.hot { border-color: var(--sa-warning, #f59e0b); } .nf-card.hot .v { color: #d97706; }
    .nf-card.esc { border-color: var(--sa-danger, #ef4444); } .nf-card.esc .v { color: #b91c1c; }
    .nf-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .nf-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .nf-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .nf-table tbody tr:hover { background: #f9fafb; }
    .nf-cust { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .nf-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .nf-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .nf-pill-info    { background: var(--sa-info-bg, #f3f4f6);    color: #374151; } .nf-pill-info i    { background: var(--sa-info, #6b7280); }
    .nf-pill-warning { background: var(--sa-warning-bg, #fff7ed); color: #d97706; } .nf-pill-warning i { background: var(--sa-warning, #f59e0b); }
    .nf-pill-danger  { background: var(--sa-danger-bg, #fef2f2);  color: #b91c1c; } .nf-pill-danger i  { background: var(--sa-danger, #ef4444); }
    .nf-link { color: #1f2937; text-decoration: none; font-weight: 600; }
    .nf-link:hover { color: var(--sa-accent-hover, #7baa18); }
    .nf-btn-soft { display: inline-flex; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; }
    .nf-btn-soft:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
    .nf-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .nf-rules { background: var(--sa-info-bg, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; font-size: 12.5px; color: #374151; margin: 0 0 18px; max-width: 980px; }
</style>

<x-page-head title="Nachfassen fällig"
    sub="Aktive Angebote, bei denen sich seit {{ $tage }} Tagen nichts bewegt hat — älteste zuerst. Hier liegt der Umsatz, der nur einen Anruf entfernt ist."
    current="Nachfassen fällig">
    <x-slot:actions>
        <a href="{{ url('admin/offers') }}" class="nf-btn-soft">Zur Angebotsliste</a>
    </x-slot:actions>
</x-page-head>

<div class="nf-wrap">
    <div class="nf-rules">
        <strong>Regeln</strong> (config/vertrieb.php): fällig nach {{ $tage }} Tagen ohne Bewegung ·
        rot ab {{ $eskalation }} Tagen · zählt nur aktive Status (gesendet, gesehen, Verhandlung, überarbeitet, wartet auf Freigabe).
    </div>

    <div class="nf-cards">
        <div class="nf-card hot">
            <div class="k">Nachfassen fällig</div>
            <div class="v">{{ $rows->count() }}</div>
            <div class="n">Angebote ohne Bewegung seit ≥ {{ $tage }} Tagen</div>
        </div>
        <div class="nf-card esc">
            <div class="k">Davon eskaliert</div>
            <div class="v">{{ $eskaliertCount }}</div>
            <div class="n">seit ≥ {{ $eskalation }} Tagen still</div>
        </div>
        <div class="nf-card">
            <div class="k">Noch in der Frist</div>
            <div class="v">{{ $inFrist }}</div>
            <div class="n">aktive Angebote, jünger als {{ $tage }} Tage</div>
        </div>
    </div>

    @if ($rows->isEmpty())
        <div class="nf-empty">Nichts nachzufassen — alle aktiven Angebote sind in Bewegung. Stark.</div>
    @else
        <table class="nf-table">
            <thead>
                <tr>
                    <th>Kunde</th>
                    <th>Angebot</th>
                    <th>Produkt</th>
                    <th>Angebotsstatus</th>
                    <th>Stillstand</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $r)
                    <tr>
                        <td class="nf-cust" title="{{ $r['kunde'] }}">{{ $r['kunde'] }}</td>
                        <td><a class="nf-link" href="{{ route('admin.offers.folders.show', $r['folder']->id) }}">{{ $r['folder']->name ?: 'Mappe #' . $r['folder']->id }}</a></td>
                        <td>{{ $r['produkt'] ?? '–' }}</td>
                        <td><span class="nf-pill nf-pill-info"><i></i> {{ $r['status'] }}</span></td>
                        <td>
                            <span class="nf-pill {{ $r['eskaliert'] ? 'nf-pill-danger' : 'nf-pill-warning' }}"><i></i>
                                {{ $r['tage'] }} {{ $r['tage'] === 1 ? 'Tag' : 'Tage' }} ohne Bewegung
                            </span>
                        </td>
                        <td><a class="nf-btn-soft" href="{{ route('admin.offers.folders.show', $r['folder']->id) }}">Öffnen</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
