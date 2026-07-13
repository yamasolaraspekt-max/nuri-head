@extends('admin.layouts.app')

@section('title', 'WP-Angebotsworkflow')

@section('content')
@php
    $c = $cockpit ?? [];
    $statusFarbe = [
        'erledigt'        => ['#166534', '#dcfce7', 'erledigt'],
        'bereit'          => ['#166534', '#dcfce7', 'bereit'],
        'technisch_bereit'=> ['#1e40af', '#dbeafe', 'technisch bereit'],
        'offen'           => ['#374151', '#f3f4f6', 'offen'],
        'blockiert'       => ['#991b1b', '#fee2e2', 'blockiert'],
        'gesperrt'        => ['#991b1b', '#fee2e2', 'gesperrt'],
    ];
    $ampelDot = ['gruen'=>'#16a34a','gelb'=>'#f59e0b','rot'=>'#dc2626','grau'=>'#9ca3af'];
    $badge = function ($status) use ($statusFarbe) {
        $s = $statusFarbe[$status] ?? ['#374151','#f3f4f6',$status];
        return "color:{$s[0]};background:{$s[1]};";
    };
    $zoneLabel = ['technik'=>'Technik','kaufmaennisch'=>'Kaufmännisch','ergebnis'=>'Ergebnis'];
@endphp

<div class="content-wrapper"><div class="content-body">
<div style="max-width:1100px;margin:16px auto;padding:0 8px;">

    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
        <div>
            <h4 style="margin:0;font-weight:800;color:#111827;">WP-Angebotsworkflow</h4>
            <div style="color:#6b7280;font-size:13px;">{{ $c['vorgang_label'] ?? '—' }} · Gewerk: {{ $c['gewerk'] ?? '—' }}</div>
        </div>
        <a href="{{ route('offers.angebotsreife', $leadProductList) }}" style="font-size:12px;color:#2563eb;text-decoration:none;">Reife-Detailseite →</a>
    </div>

    @if (empty($c['anwendbar']))
        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:16px;background:#fff;color:#6b7280;">
            {{ $c['hinweis'] ?? 'Nicht anwendbar.' }}
        </div>
    @else

        {{-- Fortschritt: Gesamt + Technik/Preis getrennt --}}
        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;background:#fff;margin-bottom:12px;">
            <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
                <div style="min-width:130px;">
                    <div style="font-size:30px;font-weight:800;color:#111827;line-height:1;">{{ (int)($c['gesamt_prozent'] ?? 0) }}<span style="font-size:16px;">%</span></div>
                    <div style="font-size:12px;color:#6b7280;">Prozessreife gesamt</div>
                </div>
                <div style="flex:1;min-width:260px;">
                    @php $ag = $c['aufgaben'] ?? []; @endphp
                    <div style="font-size:13px;color:#374151;margin-bottom:6px;">
                        <strong>{{ (int)($ag['erledigt'] ?? 0) }}</strong> von <strong>{{ (int)($ag['pflicht_gesamt'] ?? 0) }}</strong> Pflichtpunkten erledigt ·
                        {{ (int)($ag['offen'] ?? 0) }} offen ·
                        <span style="color:{{ (int)($ag['blockierend'] ?? 0) > 0 ? '#991b1b' : '#6b7280' }};">{{ (int)($ag['blockierend'] ?? 0) }} Blocker</span>
                    </div>
                    {{-- Technik / Preis getrennte Balken --}}
                    <div style="margin-bottom:6px;">
                        <div style="font-size:11px;color:#6b7280;">Technik {{ (int)($c['technik_prozent'] ?? 0) }}%</div>
                        <div style="height:8px;background:#f3f4f6;border-radius:6px;overflow:hidden;"><div style="height:100%;width:{{ (int)($c['technik_prozent'] ?? 0) }}%;background:#2563eb;"></div></div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6b7280;">Preisfähigkeit {{ (int)($c['preis_prozent'] ?? 0) }}% {{ ($c['preis_moeglich'] ?? false) ? '' : '· OMD/IDS offen' }}</div>
                        <div style="height:8px;background:#f3f4f6;border-radius:6px;overflow:hidden;"><div style="height:100%;width:{{ (int)($c['preis_prozent'] ?? 0) }}%;background:{{ ($c['preis_moeglich'] ?? false) ? '#16a34a' : '#f59e0b' }};"></div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Prozessleiste --}}
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
            @foreach (($c['schritte'] ?? []) as $s)
                @php $sf = $statusFarbe[$s['status']] ?? ['#374151','#f3f4f6',$s['status']]; @endphp
                <a href="{{ $s['anker'] ?? '#' }}" style="flex:1;min-width:170px;text-decoration:none;border:1px solid #eef2f7;border-radius:8px;padding:10px 12px;background:#fff;">
                    <div style="display:flex;align-items:center;gap:6px;justify-content:space-between;">
                        <span style="font-weight:700;color:#111827;">{{ $s['label'] }}</span>
                        <span style="width:9px;height:9px;border-radius:50%;background:{{ $ampelDot[$s['ampel']] ?? '#9ca3af' }};"></span>
                    </div>
                    <div style="margin-top:4px;font-size:11px;font-weight:700;display:inline-block;padding:2px 8px;border-radius:999px;{{ $badge($s['status']) }}">{{ $sf[2] }}</div>
                    <div style="margin-top:4px;font-size:11px;color:#9ca3af;">{{ $zoneLabel[$s['zone']] ?? $s['zone'] }}</div>
                </a>
            @endforeach
        </div>

        {{-- Nächste Aktion --}}
        @php $na = $c['naechste_aktion'] ?? []; @endphp
        <div style="border:1px solid #dbeafe;border-radius:10px;padding:12px 16px;background:#eff6ff;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div>
                <div style="font-size:12px;color:#1e40af;font-weight:700;">Nächste Aktion</div>
                <div style="color:#111827;">{{ $na['label'] ?? '—' }}</div>
                @if (! empty($na['hinweis']))<div style="font-size:12px;color:#92400e;margin-top:2px;">{{ $na['hinweis'] }}</div>@endif
            </div>
            @if (! empty($na['angebot_link']) && empty($na['disabled']))
                <a href="{{ route('offers.wizard') }}" style="border:none;border-radius:8px;padding:8px 16px;background:#2563eb;color:#fff;font-weight:700;text-decoration:none;">Angebot erstellen</a>
            @elseif (! empty($na['anker']))
                <a href="{{ $na['anker'] }}" style="border:1px solid #2563eb;border-radius:8px;padding:8px 14px;color:#2563eb;font-weight:700;text-decoration:none;">Dorthin</a>
            @else
                <button type="button" disabled style="border:none;border-radius:8px;padding:8px 16px;background:#e5e7eb;color:#9ca3af;font-weight:700;cursor:not-allowed;" title="Noch nicht möglich">Angebot erstellen</button>
            @endif
        </div>

        {{-- Blocker-Liste --}}
        @if (! empty($c['blocker_offen']))
            <div style="border:1px solid #fecaca;border-radius:10px;padding:10px 14px;background:#fef2f2;margin-bottom:16px;">
                <div style="font-size:12px;font-weight:800;color:#991b1b;">Offene Blocker</div>
                <ul style="margin:6px 0 0 18px;color:#991b1b;font-size:13px;">
                    @foreach ($c['blocker_offen'] as $b)<li>{{ $b }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Eingebettete read-only Panels (lazy) --}}
        @php $panels = $c['panels'] ?? []; @endphp
        <div id="reife" style="margin-bottom:14px;">
            <div style="font-weight:800;color:#111827;margin-bottom:6px;">Angebotsreife</div>
            <div class="wf-lazy" data-wf-url="{{ route($panels['reife'], $leadProductList) }}"><div style="font-size:12px;color:#6b7280;">wird geladen …</div></div>
        </div>
        <div id="auslegung" style="margin-bottom:14px;">
            <div style="font-weight:800;color:#111827;margin-bottom:6px;">Auslegung (Vorschau) <span style="font-size:11px;color:#6b7280;font-weight:600;">— Technik</span></div>
            <div class="wf-lazy" data-wf-url="{{ route($panels['auslegung'], $leadProductList) }}"><div style="font-size:12px;color:#6b7280;">wird geladen …</div></div>
        </div>
        <div id="preis" style="margin-bottom:14px;">
            <div style="font-weight:800;color:#111827;margin-bottom:6px;">Preisfähigkeit / Katalog <span style="font-size:11px;color:#92400e;font-weight:600;">— Kaufmännisch / OMD-IDS</span></div>
            <div class="wf-lazy" data-wf-url="{{ route($panels['preis'], $leadProductList) }}"><div style="font-size:12px;color:#6b7280;">wird geladen …</div></div>
        </div>

        <div style="font-size:11px;color:#9ca3af;margin-top:8px;">🔒 {{ $c['hinweis'] ?? '' }}</div>
    @endif

</div>
</div></div>
@endsection

@section('script')
<script>
(function () {
    function load(el) {
        if (el.getAttribute('data-loaded')) { return; }
        el.setAttribute('data-loaded', '1');
        fetch(el.getAttribute('data-wf-url'), { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { if (!r.ok) { throw new Error(r.status); } return r.text(); })
            .then(function (html) { el.innerHTML = html; })
            .catch(function () { el.innerHTML = '<div style="font-size:12px;color:#9ca3af;">Panel konnte nicht geladen werden.</div>'; });
    }
    function init() { document.querySelectorAll('.wf-lazy[data-wf-url]').forEach(load); }
    if (document.readyState !== 'loading') { init(); } else { document.addEventListener('DOMContentLoaded', init); }
})();
</script>
@endsection
