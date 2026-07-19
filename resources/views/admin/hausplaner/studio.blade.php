<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.layouts.partials.sa-ui') {{-- CRM-CI-Tokens (--sa-accent …) wiederverwenden --}}
    <title>Hausplaner — Studio (Testfläche)</title>

    {{--
        Hausplaner-Studio (2026-07-18): STANDALONE-Testfläche des neuen Hausplaner-Bundles
        (public/hausplaner/hausplaner.js, mit Dach-Slice). Leere Scratch-Szene, KEINE Persistenz
        (kein data-speichern-url ⇒ Speichern ist im Store ein No-Op). Dient dazu, den 2D/3D-Planer
        inkl. Dach direkt aus der Navi zu sehen und zu testen, ohne ein Objekt zu öffnen.
        Der persistente Planer am Objekt (objekt.blade) bleibt die führende, versionierte Fläche.
    --}}
    <style>
        html, body { margin: 0; height: 100%; font-family: Inter, system-ui, sans-serif; background: #f3f4f6; color: #1f2937; }
        .hp-bar { display: flex; align-items: center; gap: 12px; padding: 8px 14px; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .hp-bar a { color: #374151; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 12px; }
        .hp-bar a:hover { border-color: #93c21c; color: #4d7c0f; }
        .hp-title { font-size: 14px; font-weight: 800; }
        .hp-scratch { margin-left: auto; font-size: 12px; font-weight: 700; color: #b45309; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 999px; padding: 4px 12px; }
        #hausplaner-root { min-height: calc(100vh - 46px); width: 100%; }
        .hp-skeleton { max-width: 720px; margin: 60px auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px; }
        .hp-skeleton h1 { font-size: 18px; margin: 0 0 6px; }
        .hp-skeleton p { font-size: 13px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="hp-bar">
        <span class="hp-title">Hausplaner · Studio</span>
        <a href="{{ url()->previous() }}">‹ Zurück</a>
        <span class="hp-scratch">Testfläche — wird NICHT gespeichert</span>
    </div>

    @php
        $studioScene = [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'projectId' => 0,
            'schemaVersion' => 1,
            'revision' => 1,
            'units' => 'mm',
            'settings' => ['gridSize' => 100, 'snapEnabled' => true, 'angleSnap' => 15],
            'levels' => [[
                'id' => 'level-eg', 'name' => 'Erdgeschoss', 'elevation' => 0,
                'defaultWallHeight' => 2500, 'floorThickness' => 200, 'sortOrder' => 0,
            ]],
            'nodes' => [],
            'materials' => [],
            'metadata' => ['createdAt' => now()->toIso8601String(), 'updatedAt' => now()->toIso8601String()],
        ];
    @endphp

    {{-- Mount-Punkt: KEIN data-speichern-url ⇒ Speichern bleibt No-Op (Scratch). --}}
    <div id="hausplaner-root">
        <div class="hp-skeleton">
            <h1>Hausplaner — Studio</h1>
            <p>Leere Testfläche. Wand ziehen, Taste „d" für Dach, in die 3D-Ansicht wechseln. Nichts wird gespeichert.</p>
        </div>
    </div>

    <script type="application/json" id="hausplaner-scene">@json($studioScene)</script>

    @if (file_exists(public_path('hausplaner/hausplaner.js')))
        @if (file_exists(public_path('hausplaner/hausplaner.css')))
            <link rel="stylesheet" href="{{ asset('hausplaner/hausplaner.css') }}">
        @endif
        <script type="module" src="{{ asset('hausplaner/hausplaner.js') }}"></script>
    @endif
</body>
</html>
