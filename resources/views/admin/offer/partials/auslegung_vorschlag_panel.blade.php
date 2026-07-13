{{-- P3-c-preview — READ-ONLY Vorschau: Auslegung → Positionsvorschlag (WP). Kein Preis, keine Übernahme. --}}
@php
    $v = $vorschlag ?? [];
    $ampelFarbe = [
        'gruen' => ['#166534', '#dcfce7'],
        'gelb'  => ['#92400e', '#fef3c7'],
        'rot'   => ['#991b1b', '#fee2e2'],
        'grau'  => ['#374151', '#f3f4f6'],
    ];
    $datenlageFarbe = [
        'gemessen'  => ['#166534', '#dcfce7', 'gemessen'],
        'berechnet' => ['#1e40af', '#dbeafe', 'berechnet'],
        'geschaetzt'=> ['#92400e', '#fef3c7', 'geschätzt'],
        'fehlt'     => ['#374151', '#f3f4f6', 'fehlt'],
    ];
    $gesamt = $ampelFarbe[$v['gesamt_ampel'] ?? 'grau'] ?? $ampelFarbe['grau'];
@endphp

<div class="p3c-auslegung-vorschau" style="border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;background:#fff;font-size:13px;">

    {{-- Kurzergebnis oben --}}
    <div style="display:flex;align-items:flex-start;gap:10px;justify-content:space-between;flex-wrap:wrap;">
        <div>
            <div style="font-weight:800;font-size:14px;color:#111827;">Auslegungs-Vorschlag (Vorschau)</div>
            <div style="color:#4b5563;margin-top:2px;">{{ $v['kurzergebnis'] ?? '—' }}</div>
        </div>
        <span style="display:inline-flex;align-items:center;font-weight:800;font-size:12px;padding:3px 10px;border-radius:999px;color:{{ $gesamt[0] }};background:{{ $gesamt[1] }};">
            {{ ($v['offene_aufgaben'] ?? 0) }} offene Aufgabe(n)
        </span>
    </div>

    {{-- read-only Hinweis --}}
    <div style="margin-top:8px;padding:6px 10px;border-radius:8px;background:#f9fafb;color:#6b7280;font-size:12px;">
        🔒 {{ $v['hinweis'] ?? 'Read-only Vorschau.' }}
    </div>

    @if (empty($v['anwendbar']))
        <div style="margin-top:12px;color:#6b7280;">Keine Vorschau für diesen Vorgang.</div>
    @else

        {{-- Katalog-Hinweis --}}
        @php $kat = $v['katalog_hinweis'] ?? []; @endphp
        <div style="margin-top:12px;padding:8px 10px;border-radius:8px;border:1px dashed #f59e0b;background:#fffbeb;color:#92400e;font-size:12px;">
            <strong>Katalog:</strong> {{ $kat['text'] ?? 'Kein Katalog-Status.' }}
        </div>

        {{-- Positionen --}}
        <div style="margin-top:12px;">
            @foreach (($v['sections'][0]['items'] ?? []) as $item)
                @php
                    $amp = $ampelFarbe[$item['ampel'] ?? 'grau'] ?? $ampelFarbe['grau'];
                    $dl = $datenlageFarbe[$item['datenlage'] ?? 'fehlt'] ?? $datenlageFarbe['fehlt'];
                    $auto = ($item['bestaetigung'] ?? '') === 'automatisch_berechnet';
                @endphp
                <div style="border:1px solid #eef2f7;border-radius:8px;padding:10px 12px;margin-bottom:8px;">
                    <div style="display:flex;align-items:center;gap:8px;justify-content:space-between;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="width:10px;height:10px;border-radius:50%;background:{{ $amp[0] }};display:inline-block;"></span>
                            <span style="font-weight:700;color:#111827;">{{ $item['titel'] ?? '—' }}</span>
                            <span style="color:#6b7280;font-size:12px;">{{ rtrim(rtrim(number_format((float)($item['menge'] ?? 1), 2, ',', '.'), '0'), ',') }} {{ $item['einheit'] ?? '' }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                            {{-- Datenlage --}}
                            <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;color:{{ $dl[0] }};background:{{ $dl[1] }};">Datenlage: {{ $dl[2] }}</span>
                            {{-- automatisch vs. zu bestätigen --}}
                            <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;color:{{ $auto ? '#1e40af' : '#92400e' }};background:{{ $auto ? '#dbeafe' : '#fef3c7' }};">
                                {{ $auto ? 'automatisch berechnet' : 'zu bestätigen' }}
                            </span>
                            {{-- Preisstatus (immer katalog_anker_fehlt in P3-c) --}}
                            <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;color:#9a3412;background:#ffedd5;">
                                nicht bepreist · Katalog-Anker fehlt
                            </span>
                        </div>
                    </div>

                    @if (! empty($item['kennzahl']))
                        <div style="margin-top:6px;color:#374151;font-size:12px;">
                            {{ $item['kennzahl']['label'] }}:
                            <strong>{{ rtrim(rtrim(number_format((float)($item['kennzahl']['wert'] ?? 0), 2, ',', '.'), '0'), ',') }} {{ $item['kennzahl']['einheit'] ?? '' }}</strong>
                        </div>
                    @endif

                    @if (! empty($item['begruendung']))
                        <div style="margin-top:4px;color:#6b7280;font-size:12px;">{{ $item['begruendung'] }}</div>
                    @endif

                    @if (! empty($item['aufgabe']))
                        <div style="margin-top:6px;padding:5px 8px;border-radius:6px;background:#fef2f2;color:#991b1b;font-size:12px;">
                            ⚠ Aufgabe: {{ $item['aufgabe'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Förderung-Hinweis --}}
        <div style="margin-top:4px;padding:8px 10px;border-radius:8px;background:#eff6ff;color:#1e40af;font-size:12px;">
            <strong>Hinweis:</strong> {{ $v['foerderung_hinweis']['text'] ?? '' }}
        </div>

        {{-- read-only: keine Übernahme --}}
        <div style="margin-top:12px;">
            <button type="button" disabled
                style="border:none;border-radius:8px;padding:8px 14px;background:#e5e7eb;color:#9ca3af;font-weight:700;cursor:not-allowed;"
                title="P3-c ist read-only — Übernahme in das Angebot erst in einem späteren Paket.">
                In Angebot übernehmen (später)
            </button>
        </div>
    @endif
</div>
