{{--
    Hausplaner-Studio — Testfläche des Hausplaner-Bundles (public/hausplaner/hausplaner.js).
    Leere Scratch-Szene, KEINE Persistenz (kein data-speichern-url ⇒ Speichern ist im Store ein
    No-Op). Dient dazu, den 2D/3D-Planer inkl. Dach direkt aus der Navi zu sehen und zu testen,
    ohne ein Objekt zu öffnen. Der persistente Planer am Objekt (objekt.blade) bleibt die
    führende, versionierte Fläche.

    AUF-83-T1b: Diese Datei war ein eigenständiges HTML-Dokument mit eigenem <head> — eine zweite
    App-Shell neben der des CRM. Sie erbt jetzt von `admin.layouts.app`; damit ist die
    Ticket-Navigation da, der Eintrag „Hausplaner" markiert sich selbst, und der Planer sitzt
    zwischen den beiden Ticket-Seitenleisten statt daneben.
--}}
@extends('admin.layouts.app')

@section('title', 'Hausplaner — Studio')

@push('style')
<style>
    /*
        AUF-83-T1b — nur noch die eigenen `hp-`-Klassen dieser Fläche.

        **Was hier NICHT mehr steht, und warum:** die alten Regeln für `html, body` (Rand, Höhe,
        Schriftfamilie, Hintergrund, Textfarbe). Sie gehörten zu einem eigenständigen Dokument.
        Aus einer Seite heraus gesetzt, würden sie **jede andere Admin-Ansicht** mit umstellen —
        das wäre eine Änderung an der Shell, und die schließt der Auftrag ausdrücklich aus.
        Die Shell bringt sie ohnehin mit.

        **Auch fort: der eigene Einschluss von `admin.layouts.partials.sa-ui`.** Die Shell bindet
        die CRM-CI-Tokens selbst ein (`app.blade.php:2541`); ein zweiter Einschluss wäre eine
        zweite Quelle für dieselben Werte.
    */

    /*
        **Die Höhe kommt aus dem Behälter, nicht aus dem Fenster.** Vorher stand hier
        eine Viewport-Höhe abzüglich der eigenen Leiste (46 px) — beides gehörte zum alten Dokument.
        In der Shell sitzt der Inhalt in `.main-content-scroll`; eine Fensterhöhe erzeugt dort einen
        ZWEITEN Bildlauf und schiebt den Zeichenbereich unter die Falz.
        **Keine Pixelkonstante als Ersatz:** findet die Insel keine Höhe im Behälter, misst sie
        selbst (`buehnenHoehe.ts`, AUF-72/73) — dieselbe Regel wie bei der Breite seit AUF-83-T1a.
    */
    #hausplaner-root { width: 100%; height: 100%; }

    .hp-skeleton { max-width: 720px; margin: 60px auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px; }
    .hp-skeleton h1 { font-size: 18px; margin: 0 0 6px; }
    .hp-skeleton p { font-size: 13px; color: var(--sa-info, #6b7280); }
</style>
@endpush

@section('content')
{{--
    AUF-83-T2: **Hier stand die eigene Leiste des Studios** — Marke, Zurück-Link und der
    Testflächen-Hinweis. Alle drei sind fort, und jeder aus einem eigenen Grund:

    * **Die Marke:** die Ticket-Navigation markiert den Bereich seit T1b selbst
      (`sidebar.blade.php`, `active_routes`). Eine zweite Marke daneben ist Wiederholung.
    * **Der Zurück-Link:** er war der einzige Weg aus dem Studio, **solange es keine
      Ticket-Navigation gab**. Seit T1b gibt es sie — der Weg führt jetzt über die Navigation,
      wie überall sonst im CRM.
    * **Der Testflächen-Hinweis:** er stand zweimal auf demselben Schirm. Der hier war eine feste
      Zeichenkette und stand **immer** da, unabhängig davon, ob wirklich nichts gespeichert wird.
      Der in der Insel ist an das fehlende `data-speichern-url` gekoppelt und testverriegelt
      (`speicherAnzeige.test.ts`) — **er sagt die Wahrheit, dieser sagte sie nur meistens.**
      Deshalb bleibt der gekoppelte und dieser fällt.
--}}

{{-- Scratch-Dummy (bewusst absurd hoch): Studio ist persistenzfrei (kein data-speichern-url => save() ist No-Op) - NIE als echten Objekt-Anker verwenden. --}}
@php
    $studioScene = [
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'projectId' => 999999999,
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
@endsection

@push('scripts')
    @if (file_exists(public_path('hausplaner/hausplaner.js')))
        @if (file_exists(public_path('hausplaner/hausplaner.css')))
            <link rel="stylesheet" href="{{ asset('hausplaner/hausplaner.css') }}">
        @endif
        <script type="module" src="{{ asset('hausplaner/hausplaner.js') }}"></script>
    @endif
@endpush
