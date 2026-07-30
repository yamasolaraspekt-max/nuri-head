{{--
    Hausplaner (ticket) — Landung am Objekt, transplantiert aus playground.
    Insel-Bundle (three + konva) wird eingebettet geladen; die Szene kommt als
    <script type="application/json"> (kein Lade-Fetch). Anker ▲T1 = alternative_id.

    MARKER „in Abnahme": die Code-Schicht ist evaluator-grün (P1), die fünf
    Browser-Sichtproben stehen noch aus — bis dahin ist dies KEINE freigegebene
    Produktivfläche, sondern die transplantierte Insel in Abnahme.

    AUF-83-T1b: Diese Datei war ein eigenständiges HTML-Dokument mit eigenem <head> — eine zweite
    App-Shell neben der des CRM. Sie erbt jetzt von `admin.layouts.app`; damit ist die
    Ticket-Navigation da und der Planer sitzt zwischen den beiden Ticket-Seitenleisten.

    **Die Warnung aus AUF-60/64 gilt unverändert:** in dieser Datei steht die Übernahme-Direktive
    in der einzeiligen Klammer-Form. Blade paart seine Rohblöcke non-greedy und VOR dem Entfernen
    der Kommentare — die mehrzeilige Block-Form hat die Route hier schon zweimal zerbrochen. Beim
    Umbau ist deshalb keine entstanden. Festgehalten in BladeKompiliertTest, und der Test zaehlt
    die schliessende Direktive im ROHTEXT: sie darf auch in einem Kommentar nicht vorkommen.
--}}
@extends('admin.layouts.app')

@section('title', 'Hausplaner — ' . ($objekt->object_name ?: ('Objekt #' . $objekt->id)))

@push('style')
<style>
    /*
        AUF-83-T1b — nur noch die eigenen `hp-`-Klassen dieser Fläche.

        **Was hier NICHT mehr steht:** die alten Regeln für `html, body`. Sie gehörten zu einem
        eigenständigen Dokument; aus einer Seite heraus gesetzt, würden sie jede andere
        Admin-Ansicht mit umstellen — eine Änderung an der Shell, die der Auftrag ausschließt.
        **Ebenfalls fort:** der eigene Einschluss der CI-Tokens; die Shell bindet sie selbst ein
        (`app.blade.php:2541`).
    */
    .hp-bar { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; padding: 8px 14px; background: #fff; border-bottom: 1px solid #e5e7eb; }
    .hp-obj { font-size: 12.5px; color: var(--sa-info, #6b7280); }
    .hp-abnahme { margin-left: auto; font-size: 12px; font-weight: 700; color: var(--sa-warning-ink, #b45309); background: var(--sa-warning-bg, #fff7ed); border: 1px solid var(--sa-warning-border, #fed7aa); border-radius: 999px; padding: 4px 12px; }

    /*
        **Die Höhe kommt aus dem Behälter, nicht aus dem Fenster.** Vorher stand hier
        eine Viewport-Höhe abzüglich der eigenen Leiste (46 px) — beides gehörte zum alten Dokument.
        In der Shell sitzt der Inhalt in `.main-content-scroll`; eine Fensterhöhe erzeugt dort einen
        ZWEITEN Bildlauf. **Keine Pixelkonstante als Ersatz:** findet die Insel keine Höhe
        im Behälter, misst sie selbst (`buehnenHoehe.ts`) — dieselbe Regel wie bei der Breite.
    */
    #hausplaner-root { width: 100%; height: 100%; }

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
@endpush

@section('content')
{{-- T-c: BearbeitungsSperre je OBJEKT (weiche Sperre; harte Wahrheit bleibt base_revision->409). --}}
@include('admin.layouts.partials.bearbeitungs-sperre', ['bereich' => 'hausplaner', 'sperrId' => $objekt->id])
{{--
    AUF-83-T2 (erweiterter Umfang, Planner-Entscheid 21:10): **Der doppelte Teil dieser Leiste
    faellt, der einzigartige bleibt.**

    * **Fort:** die Marke „Hausplaner" und der Zurueck-Link. Beide sagen dasselbe wie die
      Ticket-Navigation, die seit T1b danebensteht und den Bereich selbst markiert.
    * **Bleibt:** Objektname mit Adresse und der Uebernehmen-Knopf samt Staleness-Pille. Das ist
      echter, einzigartiger Inhalt dieser Flaeche — er wandert mit **T3** in die Kopfleiste,
      dorthin, wo ohnehin *Projekt · Geschoss* steht.

    **Warum beide Blades in EINEM Posten:** es ist derselbe Mangel aus derselben Ursache. Ihn nur
    im Studio zu beheben hiesse, zwei Hausplaner-Flaechen mit verschiedenem Kopf zu hinterlassen —
    genau die zweite Wahrheit, gegen die dieser Auftrag laeuft.
--}}
{{--
    AUF-83-T3 / K-01 — **die Leiste ist fort; ihr Inhalt steht jetzt in der Kopfleiste der Insel.**

    T2 hat den doppelten Teil dieser Leiste abgeraeumt (Marke, Zurueck-Link) und den einzigartigen
    ausdruecklich stehen lassen, mit dem Vermerk zwei Absaetze weiter oben: *„er wandert mit T3 in
    die Kopfleiste, dorthin, wo ohnehin Projekt · Geschoss steht."* **Das ist dieser Umzug.**

    * **Fort:** die eigene Zeile ueber der Insel. Sie war eine vierte Zeile ueber dem Zeichenbereich
      und hat genau die Hoehe gekostet, die K-08 gewinnen soll.
    * **Geblieben ist jeder Inhalt** — Name, Adresse, Uebernehmen-Knopf, Staleness-Pille — nur eine
      Zeile hoeher, in `HausplanerApp`.
    * **Der Weg ist die vorhandene Naht** (`data-objektkopf`, gelesen in `main.tsx`), dieselbe wie
      `data-rechte` und `data-projekte`. Kein neuer Mechanismus, so wie es der Kommentar am
      Mount-Knoten seit AUF-64 verlangt.
    * **Die Wahrheit bleibt, wo sie war:** `ErmittleUebernahmeStatus` liefert den Status, das Blade
      reicht ihn weiter. Weder Blade noch Insel leiten ihn aus der Szene ab — das waere die zweite
      Statusquelle, die dieser Datei ausdruecklich verboten ist.
    * **Der Hinweis „Keine Szene vorhanden"** ist in den `title` des gesperrten Knopfes gewandert:
      er stand als eigener Text daneben und sagte dasselbe wie der gesperrte Zustand.
    * **Der Satz „In Abnahme — Browser-Sichtproben ausstehend"** faellt ersatzlos. Er ist eine
      Aussage ueber den BAUZUSTAND, nicht ueber das Objekt, und gehoert damit in den Ledger und
      nicht auf die Flaeche des Nutzers — dieselbe Regel wie bei den Vertroestungen aus AUF-56.
--}}
@php($szeneLeer = empty($dokument->scene_json['nodes'] ?? []))

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
     {{-- AUF-81: Ziel fuer das Speichern von Konfigurator-Paketen. Nur hier, nicht auf der
          Studio-Flaeche — dieselbe Ueberlegung wie bei der Projektliste (AUF-78). --}}
     data-pakete-url="{{ route('hausplaner.objekt.pakete.speichern') }}"
     {{-- AUF-83-T3 / K-01: der Objektkopf in EINEM Attribut — Name, Adresse, Ziel und
          Uebernahme-Stand. Dieselbe Naht wie `data-projekte`, und wie dort gilt: fertig vom
          Server, die Insel rechnet nichts nach. Nur `$szeneLeer` ist eine Frage an die Szene, und
          die stand vorher an genau derselben Stelle. --}}
     data-objektkopf="{{ json_encode([
         'name' => $objekt->object_name ?: ('Objekt #' . $objekt->id),
         'adresse' => $objekt->full_address ?: '',
         'uebernehmenUrl' => route('hausplaner.objekt.uebernehmen', $objekt),
         'status' => $uebernahme['status'] ?? 'nie',
         'revision' => $uebernahme['szene_revision'] ?? null,
         'szeneLeer' => $szeneLeer,
     ], JSON_UNESCAPED_UNICODE) }}"
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
@endsection

@push('scripts')
    {{-- Editor-Bundle (aus playground transplantiert). Fehlt es, bleibt die Skeleton-Ansicht. --}}
    @if (file_exists(public_path('hausplaner/hausplaner.js')))
        @if (file_exists(public_path('hausplaner/hausplaner.css')))
            <link rel="stylesheet" href="{{ asset('hausplaner/hausplaner.css') }}">
        @endif
        <script type="module" src="{{ asset('hausplaner/hausplaner.js') }}"></script>
    @endif
@endpush
