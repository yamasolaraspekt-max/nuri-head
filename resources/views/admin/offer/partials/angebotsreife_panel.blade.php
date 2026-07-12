{{--
    Paket 1 — Angebotsreife-Panel (READ-ONLY, reine Anzeige).
    Erwartet: $reife (Array aus App\Services\Offer\OfferReadinessService).
    Die Blade rechnet NICHT — sie zeigt nur das vom Service gelieferte DTO an.
--}}
@php
    $r = $reife ?? [];
    $percent = (int) ($r['percent'] ?? 0);
    $status = (string) ($r['status'] ?? 'unvollstaendig');
    $angebotsfaehig = (bool) ($r['angebotsfaehig'] ?? false);
    $aufgaben = $r['aufgaben'] ?? ['pflicht_gesamt' => 0, 'erledigt' => 0, 'offen' => 0, 'blockierend' => 0, 'optional' => 0];
    $badge = [
        'angebotsfaehig' => ['#166534', '#dcfce7', 'angebotsfähig'],
        'in_pruefung'    => ['#92400e', '#fef3c7', 'in Prüfung'],
        'unvollstaendig' => ['#3730a3', '#e0e7ff', 'unvollständig'],
        'blockiert'      => ['#991b1b', '#fee2e2', 'blockiert'],
    ][$status] ?? ['#374151', '#f3f4f6', $status];
    $barColor = $angebotsfaehig ? '#16a34a' : ($status === 'blockiert' ? '#ef4444' : '#f59e0b');
    $gruppeLabel = ['blocker' => 'Blocker', 'pflicht' => 'Pflicht', 'optional' => 'Optional'];
@endphp

<div class="ar-panel" style="max-width:720px;border:1px solid #e5e7eb;border-radius:16px;padding:18px;font-family:system-ui,Arial,sans-serif;background:#fff;box-shadow:0 8px 24px -18px rgba(0,0,0,.35);">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px;">
        <div style="font-weight:800;font-size:16px;color:#111827;">
            Angebotsreife — {{ $r['gewerk'] ?? 'Gewerk' }}
            <span style="color:#6b7280;font-weight:600;font-size:12px;">(Vorgang #{{ $r['lead_product_list_id'] ?? '?' }})</span>
        </div>
        <span style="padding:5px 12px;border-radius:999px;font-weight:800;font-size:12px;color:{{ $badge[0] }};background:{{ $badge[1] }};">
            {{ $badge[2] }}
        </span>
    </div>

    {{-- Fortschrittsbalken --}}
    <div style="display:flex;justify-content:space-between;font-size:12px;font-weight:800;color:#6b7280;margin-bottom:6px;">
        <span>Fortschritt</span><span>{{ $percent }}%</span>
    </div>
    <div style="height:12px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:999px;overflow:hidden;margin-bottom:14px;">
        <div style="height:100%;width:{{ $percent }}%;background:{{ $barColor }};border-radius:999px;transition:width .25s;"></div>
    </div>

    {{-- Aufgaben-Zusammenfassung --}}
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
        @foreach([
            ['Pflicht gesamt', $aufgaben['pflicht_gesamt'], '#111827'],
            ['erledigt', $aufgaben['erledigt'], '#166534'],
            ['offen', $aufgaben['offen'], '#92400e'],
            ['blockierend', $aufgaben['blockierend'], '#991b1b'],
            ['optional', $aufgaben['optional'], '#374151'],
        ] as [$lbl, $val, $col])
            <span style="padding:4px 10px;border-radius:8px;background:#f9fafb;border:1px solid #e5e7eb;font-size:12px;font-weight:700;color:#374151;">
                {{ $lbl }}: <strong style="color:{{ $col }};">{{ $val }}</strong>
            </span>
        @endforeach
    </div>

    {{-- Nächster Schritt --}}
    @if(!empty($r['naechster_schritt']))
        <div style="font-size:13px;font-weight:700;color:#3730a3;background:#eef2ff;border:1px solid #c7d2fe;border-radius:10px;padding:9px 12px;margin-bottom:12px;">
            Nächster Schritt: {{ $r['naechster_schritt'] }}
        </div>
    @endif

    {{-- Blocker-Hinweise --}}
    @if(!empty($r['blocker_offen']))
        <div style="font-size:12px;color:#991b1b;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:9px 12px;margin-bottom:12px;">
            <strong>Blockierend offen:</strong> {{ implode(', ', $r['blocker_offen']) }}
        </div>
    @endif

    {{-- Kriterien-Checkliste --}}
    <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;margin-bottom:14px;">
        @foreach(($r['kriterien'] ?? []) as $k)
            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:9px 12px;border-top:1px solid #f3f4f6;">
                <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                    <span style="width:20px;height:20px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;font-weight:900;font-size:12px;color:#fff;background:{{ $k['erfuellt'] ? '#16a34a' : ($k['gruppe'] === 'blocker' ? '#ef4444' : '#d1d5db') }};">
                        {{ $k['erfuellt'] ? '✓' : ($k['gruppe'] === 'blocker' ? '!' : '·') }}
                    </span>
                    <span style="font-size:13px;font-weight:600;color:#374151;">{{ $k['label'] }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex:0 0 auto;">
                    @if($k['grad'] > 0 && $k['grad'] < 1)
                        <span style="font-size:11px;color:#92400e;font-weight:800;">{{ (int) round($k['grad'] * 100) }}%</span>
                    @endif
                    <span style="font-size:10px;color:#6b7280;background:#f3f4f6;border-radius:6px;padding:2px 7px;font-weight:800;text-transform:uppercase;">
                        {{ $gruppeLabel[$k['gruppe']] ?? $k['gruppe'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Angebot-erstellen-Gate (nur Anzeige/Hinweis; kein Angebotslogik-Eingriff) --}}
    <div style="display:flex;align-items:center;gap:12px;">
        @if($angebotsfaehig)
            <button type="button" style="padding:10px 16px;border:0;border-radius:10px;background:#16a34a;color:#fff;font-weight:800;font-size:14px;cursor:pointer;">
                Angebot erstellen
            </button>
            <span style="font-size:12px;color:#166534;font-weight:700;">{{ $r['hinweis'] ?? '' }}</span>
        @else
            <button type="button" disabled title="{{ $r['hinweis'] ?? '' }}" style="padding:10px 16px;border:0;border-radius:10px;background:#e5e7eb;color:#9ca3af;font-weight:800;font-size:14px;cursor:not-allowed;">
                Angebot erstellen
            </button>
            <span style="font-size:12px;color:#991b1b;font-weight:700;">{{ $r['hinweis'] ?? 'Noch nicht angebotsfähig.' }}</span>
        @endif
    </div>
    <div style="margin-top:10px;font-size:11px;color:#9ca3af;">
        Read-only-Anzeige (Paket 1). Der Button ist ein Gate-Hinweis; die Angebotserstellung wird hier NICHT ausgelöst/umgebaut.
    </div>
</div>
