<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .hp-bar { display: flex; align-items: center; gap: 12px; padding: 8px 14px; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .hp-bar a { color: #374151; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 12px; }
        .hp-bar a:hover { border-color: #93c21c; color: #4d7c0f; }
        .hp-title { font-size: 14px; font-weight: 800; }
        .hp-obj { font-size: 12.5px; color: #6b7280; }
        .hp-abnahme { margin-left: auto; font-size: 12px; font-weight: 700; color: #b45309; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 999px; padding: 4px 12px; }
        #hausplaner-root { min-height: calc(100vh - 46px); width: 100%; }
        .hp-skeleton { max-width: 720px; margin: 60px auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px; }
        .hp-skeleton h1 { font-size: 18px; margin: 0 0 6px; }
        .hp-skeleton p { font-size: 13px; color: #6b7280; }
        .hp-meta { font-size: 12.5px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 12px; margin-top: 12px; font-family: ui-monospace, monospace; }
    </style>
</head>
<body>
    {{-- T-c: BearbeitungsSperre je OBJEKT (weiche Sperre; harte Wahrheit bleibt base_revision->409). --}}
    @include('admin.layouts.partials.bearbeitungs-sperre', ['bereich' => 'hausplaner', 'sperrId' => $objekt->id])
    <div class="hp-bar">
        <span class="hp-title">Hausplaner</span>
        <span class="hp-obj">{{ $objekt->object_name ?: ('Objekt #' . $objekt->id) }}{{ $objekt->full_address ? ' · ' . $objekt->full_address : '' }}</span>
        <a href="{{ url()->previous() }}">‹ Zurück</a>
        <span class="hp-abnahme">In Abnahme — Browser-Sichtproben ausstehend</span>
    </div>

    <div id="hausplaner-root"
         data-project-id="{{ $objekt->id }}"
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
