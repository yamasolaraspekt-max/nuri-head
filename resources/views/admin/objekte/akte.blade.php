@extends('admin.layouts.app')
@section('title', 'Gebäudeakte')

{{--
    Gebäudeakte — Akte-Seite (Welle A4, V1 LESEND · 2026-07-16).
    Zeigt das kanonische Objekt in Kapiteln; Lücken werden benannt, nie gefüllt.
    Pflege: Kundenakte → Objekt-Erfassung (Schreib-Wahrheit bleibt dort).
--}}

@section('content')
<style>
    .gk-wrap { margin: 0 18px 40px; color: #1f2937; }
    .gk-grid { display: flex; flex-wrap: wrap; gap: 14px; }
    .gk-card { flex: 1 1 320px; min-width: 300px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; }
    .gk-card h3 { margin: 0 0 8px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: #374151; display: flex; justify-content: space-between; align-items: center; }
    .gk-quote { font-size: 11px; font-weight: 700; border-radius: 999px; padding: 2px 9px; }
    .gk-quote.full { background: var(--sa-success-bg, #ecfdf5); color: #15803d; }
    .gk-quote.half { background: var(--sa-warning-bg, #fff7ed); color: #d97706; }
    .gk-quote.low { background: var(--sa-danger-bg, #fef2f2); color: #b91c1c; }
    .gk-row { display: flex; justify-content: space-between; gap: 10px; font-size: 12.5px; padding: 3px 0; border-bottom: 1px dashed #f3f4f6; }
    .gk-row .k { color: #6b7280; }
    .gk-row .v { font-weight: 600; text-align: right; }
    .gk-fehlt { margin-top: 8px; font-size: 11.5px; color: #b91c1c; }
    .gk-fehlt span { color: #9ca3af; }
    .gk-reife { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    .gk-reife td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; }
    .gk-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 2px 10px; font-size: 11.5px; font-weight: 700; }
    .gk-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .gk-pill.ok { background: var(--sa-success-bg, #ecfdf5); color: #15803d; } .gk-pill.ok i { background: var(--sa-success, #10b981); }
    .gk-pill.fehlt { background: var(--sa-danger-bg, #fef2f2); color: #b91c1c; } .gk-pill.fehlt i { background: var(--sa-danger, #ef4444); }
    .gk-btn-soft { display: inline-flex; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; }
    .gk-btn-soft:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
    .gk-hint { background: var(--sa-info-bg, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; font-size: 12.5px; color: #374151; margin: 0 0 16px; max-width: 980px; }
</style>

<x-page-head title="{{ $objekt->object_name ?: 'Objekt #' . $objekt->id }}"
    sub="{{ $kunde }} · {{ trim(($objekt->street ? $objekt->street . ', ' : '') . trim(($objekt->postcode ?? '') . ' ' . ($objekt->city ?? ''))) ?: 'keine Adresse hinterlegt' }}"
    current="Gebäudeakte">
    <x-slot:actions>
        <a class="gk-btn-soft" href="{{ route('objekte.index') }}">Zur Objektliste</a>
        @if ($objekt->lead_id)
            <a class="gk-btn-soft" href="{{ route('new.lead.profile', $objekt->lead_id) }}">Kundenakte öffnen (pflegen)</a>
        @endif
    </x-slot:actions>
</x-page-head>

<div class="gk-wrap">
    <div class="gk-hint">V1 lesend: gepflegt wird in der Kundenakte (Objekt-Erfassung) — diese Akte zeigt denselben Stand und benennt, was fehlt.</div>

    <div class="gk-grid">
        @foreach ($kapitel as $name => $k)
            <div class="gk-card">
                <h3>{{ $name }}
                    <span class="gk-quote {{ $k['quote'] >= 80 ? 'full' : ($k['quote'] >= 40 ? 'half' : 'low') }}">{{ $k['quote'] }} %</span>
                </h3>
                @forelse ($k['werte'] as $label => $wert)
                    <div class="gk-row"><span class="k">{{ $label }}</span><span class="v">{{ $wert }}</span></div>
                @empty
                    <div class="gk-row"><span class="k">Noch nichts erfasst.</span></div>
                @endforelse
                @if (!empty($k['fehlt']))
                    <div class="gk-fehlt"><span>Fehlt:</span> {{ implode(' · ', array_slice($k['fehlt'], 0, 6)) }}{{ count($k['fehlt']) > 6 ? ' …' : '' }}</div>
                @endif
            </div>
        @endforeach

        <div class="gk-card">
            <h3>Profile &amp; Berechnungen</h3>
            @if ($profil)
                <div class="gk-row"><span class="k">Anforderungsprofil</span><span class="v">v{{ $profil->version }} · aktiv</span></div>
                @if ($profil->bezeichnung)<div class="gk-row"><span class="k">Bezeichnung</span><span class="v">{{ $profil->bezeichnung }}</span></div>@endif
                <div class="gk-row"><span class="k">Gebäude-Geometrie</span><span class="v">{{ is_array($profil->gebaeude_geometrie) && !empty($profil->gebaeude_geometrie) ? 'hinterlegt' : 'leer' }}</span></div>
            @else
                <div class="gk-row"><span class="k">Anforderungsprofil</span><span class="v">keins aktiv</span></div>
                <div class="gk-fehlt"><span>Folge:</span> Heizlast (phiHl) für die WP-Kette noch nicht belegbar.</div>
            @endif
        </div>

        <div class="gk-card" style="flex: 1 1 100%">
            <h3>Auslegungs-Reife <span style="font-weight:600; text-transform:none; letter-spacing:0; color:#9ca3af">Operanden der WP-Auslegungskette — belegt oder fehlt, nichts wird geraten</span></h3>
            <table class="gk-reife">
                @foreach ($reife as $r)
                    <tr>
                        <td style="width:280px">{{ $r['operand'] }}</td>
                        <td style="width:110px">
                            <span class="gk-pill {{ $r['ok'] ? 'ok' : 'fehlt' }}"><i></i> {{ $r['ok'] ? 'belegt' : 'fehlt' }}</span>
                        </td>
                        <td style="color:#6b7280">{{ $r['wert'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
