<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>3D-Dachplaner</title>

    {{--
        Hausplaner W1 (2026-07-16): Vollbild-Hülle für die 3D-Dachplaner-INSEL aus playground
        (Standalone-Bundle public/planer/planer.js — Muster wie playground dachplaner-pro).
        Bewusst OHNE layouts.app: das Bundle bringt seine eigene Tailwind-Pipeline mit;
        der Planer ist ein Vollbild-Werkzeug.
        AP-4-Rahmen (docs/ap4-geometrie-3d-gebaeudemodell-validierung.md): die UI/Engine ist
        ein gekennzeichneter PROTOTYP — keine Persistenz, keine führende Wahrheit. Die vier
        Pflicht-Gates (Topologie-Validierung, Referenztests, versionierte Persistenz am Objekt,
        Azimut-Quelle) sind Inhalt der Hausplaner-Foundation (W2), erst danach fließen Ergebnisse.
        React-Scope-Freigabe (Yama 2026-07-16): React AUSSCHLIESSLICH in dieser Insel.
    --}}
    <link rel="stylesheet" href="{{ asset('planer/planer.css') }}">
    <style>
        html, body { margin: 0; height: 100%; }
        #roof-planner { min-height: 100vh; width: 100%; }
        .hp-back {
            position: fixed; top: 8px; right: 10px; z-index: 9999;
            font: 600 11px Inter, system-ui, sans-serif;
            color: #4d7c0f; background: #f4fae7; border: 1px solid #93c21c;
            padding: 5px 10px; border-radius: 8px; text-decoration: none;
        }
        .hp-proto {
            position: fixed; bottom: 10px; left: 10px; z-index: 9999;
            font: 600 11px Inter, system-ui, sans-serif;
            color: #d97706; background: #fff7ed; border: 1px solid #f59e0b;
            padding: 5px 10px; border-radius: 8px;
        }
    </style>
</head>
<body>
    <a href="{{ url('/employee_dashboard') }}" class="hp-back">← Zurück zum CRM</a>
    <div class="hp-proto">Prototyp — Werte informativ, wird noch nicht gespeichert</div>

    {{-- Mount-Punkt: hier rendert das Insel-Bundle die DachplanerProPage. --}}
    <div id="roof-planner"></div>

    <script type="module" src="{{ asset('planer/planer.js') }}"></script>
</body>
</html>
