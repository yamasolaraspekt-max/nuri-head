@extends('admin.layouts.app')
@section('title', 'Gebäudeakte')

{{--
    Gebäudeakte — Objektliste (Welle A4, V1 lesend · 2026-07-16).
    Sicht auf das kanonische Objekt (LeadAlternativeAdd); Vollständigkeits-Ampel je Kapitel
    zeigt, wo Operanden fehlen. Gepflegt wird in der Kundenakte (Objekt-Erfassung).
--}}

@section('content')
<style>
    .ga-wrap { margin: 0 18px 40px; color: #1f2937; }
    .ga-bar { display: flex; gap: 10px; align-items: center; margin: 4px 0 16px; flex-wrap: wrap; }
    .ga-search { border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px; color: #1f2937; min-width: 280px; }
    .ga-search:focus { outline: none; border-color: var(--sa-accent, #93c21c); box-shadow: 0 0 0 3px var(--sa-accent-light, #f4fae7); }
    .ga-btn { display: inline-flex; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 700; border: none; background: var(--sa-accent, #93c21c); color: #fff; cursor: pointer; }
    .ga-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .ga-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .ga-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .ga-table tbody tr:hover { background: #f9fafb; }
    .ga-cust { max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ga-dots { display: flex; gap: 5px; align-items: center; }
    .ga-dot { width: 10px; height: 10px; border-radius: 50%; background: #e5e7eb; }
    .ga-dot.full { background: var(--sa-success, #10b981); }
    .ga-dot.half { background: var(--sa-warning, #f59e0b); }
    .ga-dot.low { background: var(--sa-danger, #ef4444); }
    .ga-btn-soft { display: inline-flex; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; }
    .ga-btn-soft:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
    .ga-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .ga-legend { font-size: 11.5px; color: #9ca3af; margin: 8px 2px 0; }
    .ga-pagination { margin-top: 14px; }
</style>

<x-page-head title="Gebäudeakte"
    sub="Alle Objekte mit Vollständigkeits-Ampel je Kapitel (Standort · Hülle · Dach · Heizung · Verbräuche). Einmal erfasst — alle Rechner lesen daraus."
    current="Gebäudeakte" />

<div class="ga-wrap">
    <form class="ga-bar" method="GET" action="{{ route('objekte.index') }}">
        <input class="ga-search" type="text" name="q" value="{{ $q }}" placeholder="Kunde, Straße, PLZ, Ort, Objektname…">
        <button type="submit" class="ga-btn">Suchen</button>
    </form>

    @if ($rows->isEmpty())
        <div class="ga-empty">{{ $q !== '' ? 'Kein Objekt passt zur Suche.' : 'Noch keine Objekte erfasst.' }}</div>
    @else
        <table class="ga-table">
            <thead>
                <tr>
                    <th>Objekt</th>
                    <th>Kunde</th>
                    <th>Adresse</th>
                    <th>Vollständigkeit ({{ implode(' · ', $kapitel) }})</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $r)
                    @php $o = $r['objekt']; @endphp
                    <tr>
                        <td>{{ $o->object_name ?: 'Objekt #' . $o->id }}{{ $o->main ? ' · Hauptobjekt' : '' }}</td>
                        <td class="ga-cust" title="{{ $r['kunde'] }}">{{ $r['kunde'] }}</td>
                        <td>{{ trim(($o->street ? $o->street . ', ' : '') . trim(($o->postcode ?? '') . ' ' . ($o->city ?? ''))) ?: '– keine Adresse –' }}</td>
                        <td>
                            <div class="ga-dots">
                                @foreach ($r['ampel'] as $name => $quote)
                                    <span class="ga-dot {{ $quote >= 80 ? 'full' : ($quote >= 40 ? 'half' : 'low') }}" title="{{ $name }}: {{ $quote }} %"></span>
                                @endforeach
                            </div>
                        </td>
                        <td><a class="ga-btn-soft" href="{{ route('objekte.akte', $o->id) }}">Akte öffnen</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="ga-legend">Ampel je Kapitel: grün ≥ 80 % · amber ≥ 40 % · rot darunter. Reihenfolge: {{ implode(' · ', $kapitel) }}.</div>
        <div class="ga-pagination">{{ $objekte->links() }}</div>
    @endif
</div>
@endsection
