@extends('admin.layouts.app')
@section('title', 'Hausplaner')

{{--
    Hausplaner — Gebäude-Auswahl (Tools-Einstieg 2026-07-19).
    Führt in den PERSISTENTEN Objekt-Planer (hausplaner.objekt.seite), rechte-gated
    (permission:Hausplaner). Muster gespiegelt aus admin.objekte.index (Reuse: x-page-head,
    --sa-Tokens, Tabellenstil). Die Studio-Testfläche (ohne Objekt) ist separat verlinkt.
--}}

@section('content')
<style>
    .hp-wrap { margin: 0 18px 40px; color: #1f2937; }
    .hp-bar { display: flex; gap: 10px; align-items: center; margin: 4px 0 16px; flex-wrap: wrap; }
    .hp-search { border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px; color: #1f2937; min-width: 280px; }
    .hp-search:focus { outline: none; border-color: var(--sa-accent, #93c21c); box-shadow: 0 0 0 3px var(--sa-accent-light, #f4fae7); }
    .hp-btn { display: inline-flex; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 700; border: none; background: var(--sa-accent, #93c21c); color: var(--sa-accent-ink, #fff); cursor: pointer; }
    .hp-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .hp-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .hp-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .hp-table tbody tr:hover { background: #f9fafb; }
    .hp-cust { max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .hp-btn-soft { display: inline-flex; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; }
    .hp-btn-soft:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
    .hp-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .hp-pagination { margin-top: 14px; }
    .hp-studio { margin-left: auto; font-size: 12.5px; color: #6b7280; text-decoration: none; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; }
    .hp-studio:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
</style>

<x-page-head title="Hausplaner"
    sub="Gebäude wählen und den 2D/3D-Planer öffnen. Der Plan wird pro Gebäude gespeichert (versioniert)."
    current="Hausplaner" />

<div class="hp-wrap">
    <form class="hp-bar" method="GET" action="{{ route('hausplaner.index') }}">
        <input class="hp-search" type="text" name="q" value="{{ $q }}" placeholder="Kunde, Straße, PLZ, Ort, Objektname…">
        <button type="submit" class="hp-btn">Suchen</button>
        <a class="hp-studio" href="{{ route('hausplaner.studio') }}">Studio · Testfläche ohne Objekt ›</a>
    </form>

    @if ($objekte->isEmpty())
        <div class="hp-empty">{{ $q !== '' ? 'Kein Gebäude passt zur Suche.' : 'Noch keine Gebäude erfasst.' }}</div>
    @else
        <table class="hp-table">
            <thead>
                <tr>
                    <th>Gebäude</th>
                    <th>Kunde</th>
                    <th>Adresse</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($objekte as $o)
                    @php
                        $kunde = $o->lead?->firma ?: trim(($o->lead?->name ?? '') . ' ' . ($o->lead?->lastname ?? ''));
                        $adresse = trim(($o->street ? $o->street . ', ' : '') . trim(($o->postcode ?? '') . ' ' . ($o->city ?? '')));
                    @endphp
                    <tr>
                        <td>{{ $o->object_name ?: 'Objekt #' . $o->id }}</td>
                        <td class="hp-cust" title="{{ $kunde }}">{{ $kunde ?: '–' }}</td>
                        <td>{{ $adresse ?: '– keine Adresse –' }}</td>
                        <td><a class="hp-btn-soft" href="{{ route('hausplaner.objekt.seite', $o->id) }}">Hausplaner öffnen ›</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="hp-pagination">{{ $objekte->links() }}</div>
    @endif
</div>
@endsection
