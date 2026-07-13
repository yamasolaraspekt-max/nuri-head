{{-- P3-d0a — READ-ONLY WP-Katalog-Matching-Diagnose. Kein Preisanker, kein component_id, keine Übernahme. --}}
@php
    $m = $matching ?? [];
    $statusFarbe = [
        'eindeutig_gemappt'  => ['#166534', '#dcfce7'],
        'mehrere_treffer'    => ['#92400e', '#fef3c7'],
        'kein_treffer'       => ['#374151', '#f3f4f6'],
        'set_ohne_specs'     => ['#9a3412', '#ffedd5'],
        'kandidat_unbepreist'=> ['#9a3412', '#ffedd5'],
        'konflikt'           => ['#991b1b', '#fee2e2'],
    ];
    $badge = function ($status) use ($statusFarbe) {
        $c = $statusFarbe[$status] ?? ['#374151', '#f3f4f6'];
        return "color:{$c[0]};background:{$c[1]};";
    };
    $eur = fn ($v) => $v === null ? null : number_format((float) $v, 2, ',', '.') . ' €';
@endphp

<div class="p3d0a-wp-katalog-matching" style="border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;background:#fff;font-size:13px;">

    <div style="font-weight:800;font-size:14px;color:#111827;">WP-Katalog-Matching (Diagnose)</div>
    <div style="margin-top:6px;padding:6px 10px;border-radius:8px;background:#f9fafb;color:#6b7280;font-size:12px;">
        🔒 {{ $m['hinweis'] ?? 'Read-only Diagnose.' }}
    </div>

    @if (empty($m['anwendbar']))
        <div style="margin-top:12px;color:#6b7280;">{{ $m['banner'] ?? 'Nicht anwendbar.' }}</div>
    @else

        {{-- Schnittmenge + Banner --}}
        @php $schnitt = (int) ($m['schnittmenge'] ?? 0); @endphp
        <div style="margin-top:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <span style="font-weight:800;font-size:13px;padding:4px 12px;border-radius:999px;color:{{ $schnitt === 0 ? '#991b1b' : '#166534' }};background:{{ $schnitt === 0 ? '#fee2e2' : '#dcfce7' }};">
                Schnittmenge (gemeinsames product_id): {{ $schnitt }}
            </span>
            @if (! empty($m['bedarf_kw']))
                <span style="font-size:12px;color:#4b5563;">Bedarf: <strong>{{ rtrim(rtrim(number_format((float) $m['bedarf_kw'], 2, ',', '.'), '0'), ',') }} kW</strong></span>
            @endif
        </div>

        @if ($schnitt === 0)
            <div style="margin-top:10px;padding:10px 12px;border-radius:8px;border:1px solid #ef4444;background:#fef2f2;color:#991b1b;font-weight:600;">
                ⛔ {{ $m['banner'] }}
            </div>
        @else
            <div style="margin-top:10px;padding:10px 12px;border-radius:8px;border:1px solid #f59e0b;background:#fffbeb;color:#92400e;">
                {{ $m['banner'] }}
            </div>
        @endif

        {{-- Zwei-Spalten-Vergleich --}}
        <div style="margin-top:14px;display:flex;gap:14px;flex-wrap:wrap;">

            {{-- LINKS: technische Kandidaten --}}
            <div style="flex:1;min-width:280px;">
                <div style="font-weight:800;color:#111827;margin-bottom:8px;">Technische Geräte-Kandidaten <span style="color:#6b7280;font-weight:600;">(Kennlinie, unbepreist)</span></div>
                @forelse (($m['kandidaten'] ?? []) as $k)
                    <div style="border:1px solid #eef2f7;border-radius:8px;padding:8px 10px;margin-bottom:6px;">
                        <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                            <span style="font-weight:700;color:#111827;">{{ $k['hersteller'] }} {{ $k['modell'] }}</span>
                            <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;{{ $badge($k['status']) }}">{{ $k['status'] }}</span>
                        </div>
                        <div style="color:#4b5563;font-size:12px;margin-top:3px;">
                            @if (! is_null($k['leistung_kw'])) Leistung: {{ rtrim(rtrim(number_format((float)$k['leistung_kw'], 2, ',', '.'), '0'), ',') }} kW @endif
                            @if (! is_null($k['scop'])) · SCOP {{ rtrim(rtrim(number_format((float)$k['scop'], 2, ',', '.'), '0'), ',') }} @endif
                        </div>
                        <div style="margin-top:4px;display:flex;gap:6px;flex-wrap:wrap;">
                            <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;color:{{ $k['bepreist'] ? '#166534' : '#9a3412' }};background:{{ $k['bepreist'] ? '#dcfce7' : '#ffedd5' }};">{{ $k['bepreist'] ? 'bepreist' : 'unbepreist' }}</span>
                            <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;color:{{ $k['im_set'] ? '#166534' : '#374151' }};background:{{ $k['im_set'] ? '#dcfce7' : '#f3f4f6' }};">{{ $k['im_set'] ? 'im Set' : 'nicht im Set' }}</span>
                        </div>
                        @if (! empty($k['konflikt_felder']))
                            <div style="margin-top:4px;color:#991b1b;font-size:12px;">⚠ Konflikt: {{ implode(', ', $k['konflikt_felder']) }}</div>
                        @endif
                    </div>
                @empty
                    <div style="color:#6b7280;font-size:12px;">Keine technischen Kandidaten.</div>
                @endforelse
            </div>

            {{-- RECHTS: Preis-Set-Produkte --}}
            <div style="flex:1;min-width:280px;">
                <div style="font-weight:800;color:#111827;margin-bottom:8px;">Preis-Set-Produkte <span style="color:#6b7280;font-weight:600;">(bepreist, ohne Kennlinie)</span></div>
                @forelse (($m['set_produkte'] ?? []) as $s)
                    <div style="border:1px solid #eef2f7;border-radius:8px;padding:8px 10px;margin-bottom:6px;">
                        <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                            <span style="font-weight:700;color:#111827;">{{ $s['hersteller'] }} · {{ $s['name'] }}</span>
                            <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;{{ $badge($s['status']) }}">{{ $s['status'] }}</span>
                        </div>
                        <div style="color:#4b5563;font-size:12px;margin-top:3px;">
                            Art-Nr: {{ $s['article_no'] }}
                            @if (! is_null($s['vk_diagnostisch'])) · VK (diagn.): {{ $eur($s['vk_diagnostisch']) }} @endif
                            @if (! is_null($s['ek_diagnostisch'])) · EK (diagn.): {{ $eur($s['ek_diagnostisch']) }} @endif
                        </div>
                        <div style="margin-top:4px;display:flex;gap:6px;flex-wrap:wrap;">
                            <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:999px;color:{{ $s['hat_specs'] ? '#166534' : '#9a3412' }};background:{{ $s['hat_specs'] ? '#dcfce7' : '#ffedd5' }};">{{ $s['hat_specs'] ? 'Kennlinie vorhanden' : 'keine Kennlinie' }}</span>
                        </div>
                        @if (! empty($s['konflikt_felder']))
                            <div style="margin-top:4px;color:#991b1b;font-size:12px;">⚠ Konflikt: {{ implode(', ', $s['konflikt_felder']) }}</div>
                        @endif
                    </div>
                @empty
                    <div style="color:#6b7280;font-size:12px;">Kein WP-Preis-Set gefunden.</div>
                @endforelse
            </div>
        </div>

        {{-- Nächste Aufgabe (Entscheidung, die der Nutzer später treffen müsste) --}}
        <div style="margin-top:12px;padding:10px 12px;border-radius:8px;background:#eff6ff;color:#1e40af;font-size:12px;">
            <strong>Nächste Aufgabe:</strong> {{ $m['naechste_aufgabe'] }}
        </div>

        {{-- read-only: bewusst KEIN Übernahme-Button / KEINE Angebotsposition --}}
    @endif
</div>
