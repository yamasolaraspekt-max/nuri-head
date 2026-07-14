@extends('admin.layouts.app')

@section('title', 'Konfigurationsprojekt (Objekt)')

@section('content')
@php
    $c = $konfig ?? [];
    $module = $c['module'] ?? [];
    $blFarbe = [
        'belastbar'      => ['#166534', '#dcfce7'],
        'eingeschraenkt' => ['#92400e', '#fef3c7'],
        'vorlaeufig'     => ['#9a3412', '#ffedd5'],
        'unzureichend'   => ['#991b1b', '#fee2e2'],
    ];
    $reifeFarbe = [
        'angebotsfaehig' => ['#166534', '#dcfce7', 'angebotsfähig'],
        'in_pruefung'    => ['#92400e', '#fef3c7', 'in Prüfung'],
        'unvollstaendig' => ['#3730a3', '#e0e7ff', 'unvollständig'],
        'blockiert'      => ['#991b1b', '#fee2e2', 'blockiert'],
    ];
@endphp

<div class="content-wrapper"><div class="content-body">
<div style="max-width:1100px;margin:16px auto;padding:0 8px;">

    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
        <div>
            <h4 style="margin:0;font-weight:800;color:#111827;">Konfigurationsprojekt</h4>
            <div style="color:#6b7280;font-size:13px;">{{ $c['vorgang_label'] ?? '—' }} · read-only Objekt-Sicht</div>
        </div>
    </div>

    @if (empty($c['anwendbar']))
        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:16px;background:#fff;color:#6b7280;">
            {{ $c['hinweis'] ?? 'Nicht anwendbar.' }}
        </div>
    @else

        {{-- Objekt-Kopf: aktivierte Gewerke --}}
        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;background:#fff;margin-bottom:12px;">
            <div style="font-size:13px;color:#374151;">
                <strong>{{ (int) ($c['gewerke_gesamt'] ?? 0) }}</strong> aktivierte Gewerke/Module ·
                <strong>{{ (int) ($c['gewerke_verdrahtet'] ?? 0) }}</strong> mit verdrahteter Auslegung (heute nur WP) ·
                Rest: Fachmodul folgt.
            </div>
        </div>

        @if (count($module) === 0)
            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:16px;background:#fff;color:#6b7280;">
                {{ $c['hinweis'] ?? 'Kein Gewerk aktiviert.' }}
            </div>
        @endif

        {{-- Gewerk-Kacheln --}}
        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:14px;">
            @foreach ($module as $m)
                @php
                    $verdrahtet = (bool) ($m['verdrahtet'] ?? false);
                    $rf = $reifeFarbe[$m['reife_status'] ?? ''] ?? ['#374151','#f3f4f6', $m['reife_status'] ?? '—'];
                    $bl = $m['heizlast_belastbarkeit'] ?? null;
                    $blF = $blFarbe[$bl['stufe'] ?? ''] ?? ['#374151','#f3f4f6'];
                    $cardBg = $verdrahtet ? '#fff' : '#f9fafb';
                @endphp
                <div style="border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;background:{{ $cardBg }};">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                        <div style="font-weight:800;color:#111827;">
                            {{ $m['gewerk'] ?? 'Gewerk' }}
                            <span style="color:#9ca3af;font-weight:600;font-size:12px;">(Vorgang #{{ $m['lead_product_list_id'] ?? '?' }})</span>
                        </div>
                        @if ($verdrahtet)
                            <span style="padding:4px 10px;border-radius:999px;font-size:11px;font-weight:800;color:{{ $rf[0] }};background:{{ $rf[1] }};">{{ $rf[2] }}</span>
                        @else
                            <span style="padding:4px 10px;border-radius:999px;font-size:11px;font-weight:800;color:#6b7280;background:#e5e7eb;">nicht verdrahtet</span>
                        @endif
                    </div>

                    @if ($verdrahtet)
                        {{-- Technik / Preis getrennt --}}
                        <div style="margin-top:10px;display:flex;gap:18px;flex-wrap:wrap;align-items:center;">
                            <div style="min-width:160px;">
                                <div style="font-size:11px;color:#6b7280;">Technik (Reife) {{ (int) ($m['technik_prozent'] ?? 0) }}%</div>
                                <div style="height:8px;background:#f3f4f6;border-radius:6px;overflow:hidden;"><div style="height:100%;width:{{ (int) ($m['technik_prozent'] ?? 0) }}%;background:#2563eb;"></div></div>
                            </div>
                            <div style="min-width:160px;">
                                <div style="font-size:11px;color:#92400e;">Preisfähigkeit — {{ $m['preis_hinweis'] ?? 'OMD/IDS offen' }}</div>
                                <div style="height:8px;background:#f3f4f6;border-radius:6px;overflow:hidden;"><div style="height:100%;width:0%;background:#f59e0b;"></div></div>
                            </div>
                        </div>

                        @if ($bl)
                            <div style="margin-top:8px;">
                                <span style="padding:3px 9px;border-radius:999px;font-size:11px;font-weight:800;color:{{ $blF[0] }};background:{{ $blF[1] }};">
                                    Heizlast: {{ $bl['label'] ?? '—' }}{{ ($bl['verbindlich'] ?? false) ? '' : ' · unverbindlich' }}
                                </span>
                                <span style="font-size:11px;color:#6b7280;margin-left:6px;">Auslegung {{ ($m['auslegung_vorhanden'] ?? false) ? 'vorhanden' : 'offen' }}</span>
                            </div>
                        @endif

                        @if (! empty($m['naechste_aktion']))
                            <div style="margin-top:8px;font-size:12px;color:#3730a3;">Nächste Aktion: {{ $m['naechste_aktion'] }}</div>
                        @endif
                        @if (! empty($m['blocker_offen']))
                            <div style="margin-top:6px;font-size:12px;color:#991b1b;">Blocker: {{ implode(', ', $m['blocker_offen']) }}</div>
                        @endif

                        <div style="margin-top:10px;">
                            <a href="{{ route('offers.workflow', $m['lead_product_list_id']) }}" style="font-size:12px;color:#2563eb;text-decoration:none;">WP-Cockpit öffnen →</a>
                        </div>
                    @else
                        <div style="margin-top:8px;font-size:12px;color:#6b7280;">
                            Status: {{ $m['status'] ?? '—' }} · {{ $m['hinweis'] ?? 'Fachmodul noch nicht verdrahtet.' }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Fach-Abhängigkeiten (read-only Hinweise) --}}
        @if (! empty($c['abhaengigkeiten']))
            <div style="border:1px solid #dbeafe;border-radius:10px;padding:10px 14px;background:#eff6ff;margin-bottom:14px;">
                <div style="font-size:12px;font-weight:800;color:#1e40af;">Modul-Abhängigkeiten (Hinweis)</div>
                <ul style="margin:6px 0 0 18px;color:#1e3a8a;font-size:12px;">
                    @foreach ($c['abhaengigkeiten'] as $a)<li>{{ $a }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div style="font-size:11px;color:#9ca3af;">🔒 {{ $c['hinweis'] ?? '' }}</div>
    @endif

</div>
</div></div>
@endsection
