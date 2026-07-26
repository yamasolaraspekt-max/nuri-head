<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.layouts.partials.sa-ui') {{-- CRM-CI-Tokens (--sa-accent …) wiederverwenden --}}
    <title>Hausplaner — {{ $objekt->object_name ?: ('Objekt #' . $objekt->id) }}</title>

    {{--
        Hausplaner (ticket) — Landung am Objekt, transplantiert aus playground.
        Insel-Bundle (three + konva) wird eingebettet geladen; die Szene kommt als
        <script type="application/json"> (kein Lade-Fetch). Anker ▲T1 = alternative_id.

        MARKER „in Abnahme": die Code-Schicht ist evaluator-grün (P1), die fünf
        Browser-Sichtproben stehen noch aus — bis dahin ist dies KEINE freigegebene
        Produktivfläche, sondern die transplantierte Insel in Abnahme.
    --}}
    <style>
        html, body { margin: 0; height: 100%; font-family: Inter, system-ui, sans-serif; background: #f3f4f6; color: #1f2937; }
        .hp-bar { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; padding: 8px 14px; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .hp-bar a { color: #374151; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 12px; }
        .hp-bar a:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
        .hp-title { font-size: 14px; font-weight: 800; }
        .hp-obj { font-size: 12.5px; color: var(--sa-info, #6b7280); }
        .hp-abnahme { margin-left: auto; font-size: 12px; font-weight: 700; color: var(--sa-warning-ink, #b45309); background: var(--sa-warning-bg, #fff7ed); border: 1px solid var(--sa-warning-border, #fed7aa); border-radius: 999px; padding: 4px 12px; }
        #hausplaner-root { min-height: calc(100vh - 46px); width: 100%; }
        .hp-skeleton { max-width: 720px; margin: 60px auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px; }
        .hp-skeleton h1 { font-size: 18px; margin: 0 0 6px; }
        .hp-skeleton p { font-size: 13px; color: var(--sa-info, #6b7280); }
        .hp-meta { font-size: 12.5px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 12px; margin-top: 12px; font-family: ui-monospace, monospace; }

        /* W-A: Übernehmen-Knopf + Staleness-Pill + Flash — NUR sa-Tokens (Fallbacks = Token-Werte, Muster P3/P4). */
        .hp-uebernahme { display: flex; align-items: center; gap: 8px; margin: 0; }
        .hp-uebernehmen { font: inherit; font-size: 13px; font-weight: 700; color: var(--sa-accent-ink, #ffffff); background: var(--sa-accent, #93c21c); border: 1px solid var(--sa-accent, #93c21c); border-radius: 8px; padding: 6px 12px; cursor: pointer; }
        .hp-uebernehmen:hover { background: var(--sa-accent-hover, #7baa18); border-color: var(--sa-accent-hover, #7baa18); }
        .hp-uebernehmen:focus-visible { outline: 2px solid var(--sa-accent-hover, #7baa18); outline-offset: 2px; }
        .hp-uebernehmen:disabled { color: var(--sa-info, #6b7280); background: var(--sa-info-bg, #f3f4f6); border-color: var(--sa-info-bg, #f3f4f6); cursor: not-allowed; }
        .hp-hinweis { font-size: 12px; color: var(--sa-info, #6b7280); }
        .hp-pill { font-size: 12px; font-weight: 700; border-radius: 999px; padding: 4px 12px; border: 1px solid; }
        .hp-pill--nie { color: var(--sa-info, #6b7280); background: var(--sa-info-bg, #f3f4f6); border-color: var(--sa-info, #6b7280); }
        .hp-pill--aktuell { color: #1f2937; background: var(--sa-success-bg, #ecfdf5); border-color: var(--sa-success, #10b981); }
        .hp-pill--veraltet { color: var(--sa-warning-ink, #b45309); background: var(--sa-warning-bg, #fff7ed); border-color: var(--sa-warning-border, #fed7aa); }
        .hp-flash { font-size: 13px; font-weight: 600; padding: 8px 14px; border-bottom: 1px solid; }
        .hp-flash--erfolg { color: #1f2937; background: var(--sa-success-bg, #ecfdf5); border-color: var(--sa-success, #10b981); }
        .hp-flash--warnung { color: var(--sa-warning-ink, #b45309); background: var(--sa-warning-bg, #fff7ed); border-color: var(--sa-warning-border, #fed7aa); }
        .hp-flash--info { color: var(--sa-info, #6b7280); background: var(--sa-info-bg, #f3f4f6); border-color: var(--sa-info, #6b7280); }
        .hp-flash--fehler { color: #1f2937; background: var(--sa-danger-bg, #fef2f2); border-color: var(--sa-danger, #ef4444); } /* Text dunkel: --sa-danger auf danger-bg unterschreitet AA; Status trägt Rand+Fläche+Wortlaut */
    </style>
</head>
<body>
    {{-- T-c: BearbeitungsSperre je OBJEKT (weiche Sperre; harte Wahrheit bleibt base_revision->409). --}}
    @include('admin.layouts.partials.bearbeitungs-sperre', ['bereich' => 'hausplaner', 'sperrId' => $objekt->id])
    <div class="hp-bar">
        <span class="hp-title">Hausplaner</span>
        <span class="hp-obj">{{ $objekt->object_name ?: ('Objekt #' . $objekt->id) }}{{ $objekt->full_address ? ' · ' . $objekt->full_address : '' }}</span>
        <a href="{{ url()->previous() }}">‹ Zurück</a>

        {{-- W-A: expliziter Übernehmen-Knopf (Operanden-Gate: Fachentscheidung, Vorschlag + Bestätigung,
             KEIN Automatismus) + Staleness-Pill. Wahrheit: ErmittleUebernahmeStatus (source_hash der
             aktiven Profil-Version vs. Hash der aktuellen Szene) — keine zweite Statusquelle im Blade. --}}
        @php($szeneLeer = empty($dokument->scene_json['nodes'] ?? []))
        <form class="hp-uebernahme" method="POST" action="{{ route('hausplaner.objekt.uebernehmen', $objekt) }}">
            @csrf
            <button type="submit" class="hp-uebernehmen" @disabled($szeneLeer)
                    title="Übernimmt die Szenen-Geometrie als neue Version in die Auslegung (gebaeude_geometrie).">
                In Auslegung übernehmen
            </button>
        </form>
        @if ($szeneLeer)
            <span class="hp-hinweis">Keine Szene vorhanden — erst zeichnen und speichern.</span>
        @endif
        @if (($uebernahme['status'] ?? 'nie') === 'aktuell')
            <span class="hp-pill hp-pill--aktuell">Übernommen — aktuell (Szene Rev. {{ $uebernahme['szene_revision'] }})</span>
        @elseif (($uebernahme['status'] ?? 'nie') === 'veraltet')
            <span class="hp-pill hp-pill--veraltet">Übernommen — VERALTET (Szene geändert seit Übernahme)</span>
        @else
            <span class="hp-pill hp-pill--nie">Noch nie übernommen</span>
        @endif

        <span class="hp-abnahme">In Abnahme — Browser-Sichtproben ausstehend</span>
    </div>

    {{-- W-A: Ergebnis der Übernahme als Flash in Nutzersprache (z. B. „3 Räume übernommen, Version 4."). --}}
    @if (session('hausplaner_uebernahme'))
        <div class="hp-flash hp-flash--{{ session('hausplaner_uebernahme')['typ'] ?? 'info' }}" role="status">
            {{ session('hausplaner_uebernahme')['text'] ?? '' }}
        </div>
    @endif

    {{--
        AUF-60: die Rechte des angemeldeten Nutzers für das Item „Hausplaner" — die VIER, die das
        System kennt, nicht mehr. Quelle ist ausschliesslich `User::hasPermission` (unveraendert);
        hier wird nichts abgeleitet und nichts ergaenzt. Ohne angemeldeten Nutzer bleibt die Liste
        leer — das Minimum. Dieselbe Naht wie `data-speichern-url`, kein neuer Mechanismus.
    --}}
    {{--
        AUF-64: `$hpRechte` kommt aus `HausplanerController::hausplanerRechte()`. Im Blade steht
        bewusst nur die Ausgabe — keine Logik, kein PHP-Block. Diese Datei traegt weiter oben die
        einzeilige Klammer-Form der PHP-Direktive (Uebernahme-Knopf); die hat kein schliessendes
        Gegenstueck, und Blade paart seine Rohbloecke non-greedy und VOR dem Entfernen der
        Kommentare. Ein Block hier wuerde die Datei erneut zerbrechen — so wie in AUF-60 geschehen.
        Festgehalten in BladeKompiliertTest.
    --}}
    <div id="hausplaner-root"
         data-project-id="{{ $objekt->id }}"
         data-rechte="{{ $hpRechte }}"
         {{-- AUF-78: die Liste kommt fertig aus dem Controller. Das Blade reicht weiter, es
              rechnet nicht — und schon gar nicht in einem PHP-Block: genau ein solcher hat in
              AUF-64 diese Route zerbrochen. --}}
         data-projekte="{{ json_encode($hpProjekte, JSON_UNESCAPED_UNICODE) }}"
         data-speichern-url="{{ route('hausplaner.objekt.speichern', $objekt) }}"
         data-snapshots-url="{{ route('hausplaner.objekt.snapshots.liste', $objekt) }}"
         data-katalog-url="{{ route('hausplaner.objekt.katalog') }}">
        <div class="hp-skeleton">
            <h1>Hausplaner — {{ $objekt->object_name ?: ('Objekt #' . $objekt->id) }}</h1>
            <p>Foundation aktiv. Der 2D/3D-Editor mountet hier; die Szene ist bereits eingebettet und versioniert.</p>
            <div class="hp-meta">
                Dokument #{{ $dokument->id }} · Schema v{{ $dokument->schema_version }} · Revision {{ $dokument->revision }}<br>
                Objekt-Anker (alternative_id) {{ $dokument->alternative_id }} · Checksum {{ $dokument->checksum }}
            </div>
        </div>
    </div>

    {{-- Die Szene als eingebettete Daten (kein Lade-Fetch). --}}
    <script type="application/json" id="hausplaner-scene">@json($dokument->scene_json)</script>

    {{-- Editor-Bundle (aus playground transplantiert). Fehlt es, bleibt die Skeleton-Ansicht. --}}
    @if (file_exists(public_path('hausplaner/hausplaner.js')))
        @if (file_exists(public_path('hausplaner/hausplaner.css')))
            <link rel="stylesheet" href="{{ asset('hausplaner/hausplaner.css') }}">
        @endif
        <script type="module" src="{{ asset('hausplaner/hausplaner.js') }}"></script>
    @endif
</body>
</html>
