@extends('admin.layouts.app')
@section('title', 'Styleguide')

{{--
    STYLEGUIDE — lebende Komponentenbibliothek (UI-Bauordnung 2026-07-16).
    Zweck: JEDE UI-Grundform liegt hier EINMAL mit allen Zuständen, gebaut aus den sa-ui-Tokens.
    Regel (CLAUDE.md / docs/architektur/ui-bauordnung.md):
      Vor jedem neuen UI-Element diese Seite prüfen. Existiert die Komponente → verwenden.
      Existiert sie nicht → ZUERST hier anlegen, dann einsetzen.
    Diese Seite ist außerdem die Referenzfläche für visuelle Regression (Screenshot-Diff je Welle).
    Farbwelt: NUR die gewachsene Palette (sa-ui-Tokens). Kein Schwarz (Dunkelgrau #1f2937),
    kein Navy, kein Fremdblau, kein Fremdrot.
--}}

{{--
    PB-023 — die Hausplaner-Insel liegt ab hier MIT auf der Referenzfläche.

    Die Insel bringt ihre eigene Stilschicht mit (`resources/planner/hausplaner/hausplaner.css`,
    gebaut nach `public/hausplaner/hausplaner.css`). Sie wird hier geladen, weil ein Musterblock
    ohne sie ungestyltes Markup zeigt — und ungestyltes Markup sieht im Screenshot-Diff aus wie
    eine Komponente, die es gibt.

    GEMESSEN, BEVOR SIE GELADEN WURDE (das Leck-Risiko einer fremden Stilschicht in einer
    CRM-Seite ist der Grund, warum das hier steht und nicht nur getan wurde):

        653 Zeilen, jeder Selektor beginnt mit `.hp-`  —  einzige Ausnahme: ein `:root`-Block
        dieser `:root`-Block definiert 0 `--sa-*`  —  er kann die CRM-Tokens nicht überschreiben
        `--hp-*` wird ausserhalb der Insel nirgends gelesen

    Sie ist also gekapselt: kein Selektor dieser Datei greift auf eine Seite dieses Styleguides
    zu, ausser wir schreiben eine `hp-`-Klasse hin. Genau das tun die Blöcke in Abschnitt 9.
--}}
@push('style')
    @if (file_exists(public_path('hausplaner/hausplaner.css')))
        <link rel="stylesheet" href="{{ asset('hausplaner/hausplaner.css') }}">
    @endif
@endpush

@section('content')
<style>
    /* sg = Styleguide. Gekapselt, additiv — überschreibt nichts Bestehendes. */
    .sg-wrap { margin: 0 18px 40px; color: #1f2937; }
    .sg-lede { max-width: 860px; font-size: 13.5px; color: #6b7280; margin: 0 0 6px; }
    .sg-rule { background: var(--sa-accent-light, #f4fae7); border: 1px solid var(--sa-accent, #93c21c); border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #1f2937; margin: 12px 0 26px; max-width: 860px; }
    .sg-rule strong { color: #1f2937; }
    .sg-h2 { font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: #374151; margin: 34px 0 4px; }
    .sg-h2 small { font-weight: 600; text-transform: none; letter-spacing: 0; color: #9ca3af; margin-left: 8px; }
    .sg-grid { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 10px; }
    .sg-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; min-width: 240px; }
    .sg-card-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; margin-bottom: 10px; }
    .sg-note { font-size: 11.5px; color: #9ca3af; margin-top: 8px; }

    /* Farbwelt */
    .sg-swatch { display: flex; align-items: center; gap: 10px; margin: 6px 0; font-size: 12.5px; }
    .sg-chip { width: 42px; height: 26px; border-radius: 6px; border: 1px solid #e5e7eb; flex: none; }
    .sg-code { font-family: ui-monospace, monospace; font-size: 11.5px; color: #6b7280; }

    /* Buttons */
    .sg-btn { display: inline-flex; align-items: center; gap: 7px; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 600; border: 1px solid transparent; cursor: pointer; line-height: 1.2; }
    .sg-btn-primary { background: var(--sa-accent); color: var(--sa-accent-ink); }
    .sg-btn-primary:hover { background: var(--sa-accent-hover); color: var(--sa-accent-ink); }
    .sg-btn-soft { background: #fff; color: #374151; border-color: #d1d5db; }
    .sg-btn-soft:hover { border-color: var(--sa-accent); color: var(--sa-accent-hover); }
    .sg-btn-danger { background: var(--sa-danger-bg); color: #b91c1c; border-color: var(--sa-danger); }
    .sg-btn[disabled] { opacity: .45; cursor: not-allowed; }

    /* Status-Pills (Muster Arbeitsliste: heller Grund + dunkle Tinte, AA-sicher) */
    .sg-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 600; }
    .sg-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .sg-pill-success { background: var(--sa-success-bg); color: #15803d; } .sg-pill-success i { background: var(--sa-success); }
    .sg-pill-warning { background: var(--sa-warning-bg); color: #d97706; } .sg-pill-warning i { background: var(--sa-warning); }
    .sg-pill-danger  { background: var(--sa-danger-bg);  color: #b91c1c; } .sg-pill-danger i  { background: var(--sa-danger); }
    .sg-pill-info    { background: var(--sa-info-bg);    color: #374151; } .sg-pill-info i    { background: var(--sa-info); }
    .sg-pill-accent  { background: var(--sa-accent-light); color: #4d7c0f; } .sg-pill-accent i { background: var(--sa-accent); }

    /* Formularfelder */
    .sg-field { margin: 10px 0; max-width: 320px; }
    .sg-label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px; }
    .sg-input { width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 13px; color: #1f2937; background: #fff; }
    .sg-input:focus { outline: none; border-color: var(--sa-accent); box-shadow: 0 0 0 3px var(--sa-accent-light); }
    .sg-input.is-error { border-color: var(--sa-danger); background: var(--sa-danger-bg); }
    .sg-error-text { font-size: 11.5px; color: #b91c1c; margin-top: 3px; }
    .sg-hint { font-size: 11.5px; color: #9ca3af; margin-top: 3px; }

    /* Tabelle */
    .sg-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    .sg-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 8px 10px; }
    .sg-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 10px; color: #1f2937; }
    .sg-table tbody tr:hover { background: #f9fafb; }

    /* Filterleiste */
    .sg-filterbar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; }
    .sg-search { flex: 1 1 220px; max-width: 320px; }
    .sg-chip-filter { border: 1px solid #d1d5db; background: #fff; border-radius: 999px; padding: 5px 13px; font-size: 12px; color: #374151; cursor: pointer; }
    .sg-chip-filter.is-active { background: var(--sa-accent); border-color: var(--sa-accent); color: #fff; }

    /* Modal (statisch dargestellt) */
    .sg-modal-stage { background: #f3f4f6; border-radius: 10px; padding: 26px; display: flex; justify-content: center; }
    .sg-modal { background: #fff; border-radius: 12px; box-shadow: 0 18px 44px rgba(31, 41, 55, .18); width: 380px; max-width: 100%; overflow: hidden; }
    .sg-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #f3f4f6; font-weight: 700; font-size: 14px; color: #1f2937; }
    .sg-modal-body { padding: 16px 18px; font-size: 13px; color: #374151; }
    .sg-modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; background: #f9fafb; }
    .sg-x { color: #9ca3af; font-size: 16px; line-height: 1; cursor: pointer; }

    .sg-count { display: inline-flex; min-width: 20px; height: 20px; align-items: center; justify-content: center; border-radius: 999px; background: var(--sa-accent); color: #fff; font-size: 11px; font-weight: 700; padding: 0 6px; }

    /* Hausplaner-Insel: eine begrenzte Bühne für Klassen, die in der Insel absolut liegen.
       `.hp-mb-flaeche` und `.hp-schiene-overlay` tragen `position: absolute; inset: 0` — ohne
       einen Vorfahren mit `position: relative` verankerten sie sich an der Seite und legten sich
       über den Styleguide. Dieselbe Lösung wie bei `.sg-modal-stage`: statisch dargestellt. */
    .sg-hp-buehne { position: relative; height: 250px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .sg-hp-flaeche { background: var(--hp-bg, #f7f8f9); border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; }
</style>

<x-page-head title="Styleguide" sub="Lebende Komponentenbibliothek — jede UI-Grundform genau einmal, mit allen Zuständen, aus den sa-ui-Tokens. Referenzfläche für visuelle Regression." current="Styleguide" />

<div class="sg-wrap">
    <div class="sg-rule">
        <strong>Regel (UI-Bauordnung):</strong> Vor jedem neuen UI-Element diese Seite prüfen.
        Existiert die Komponente → verwenden. Existiert sie nicht → <strong>zuerst hier anlegen</strong>, dann einsetzen.
        Farbwerte kommen ausschließlich aus den sa-ui-Tokens — kein Schwarz (Dunkelgrau <span class="sg-code">#1f2937</span>), kein Navy, kein Fremdblau.
    </div>

    <div class="sg-h2">1 · Farbwelt <small>Quelle: partials/sa-ui.blade.php — geändert wird nur dort</small></div>
    <div class="sg-grid">
        <div class="sg-card">
            <div class="sg-card-label">Marke (Aktionsfarbe)</div>
            <div class="sg-swatch"><span class="sg-chip" style="background:var(--sa-accent)"></span> Aktion <span class="sg-code">--sa-accent · #93c21c</span></div>
            <div class="sg-swatch"><span class="sg-chip" style="background:var(--sa-accent-hover)"></span> Hover <span class="sg-code">--sa-accent-hover · #7baa18</span></div>
            <div class="sg-swatch"><span class="sg-chip" style="background:var(--sa-accent-light)"></span> Fläche hell <span class="sg-code">--sa-accent-light · #f4fae7</span></div>
        </div>
        <div class="sg-card">
            <div class="sg-card-label">Status (semantisch — nicht Marke)</div>
            <div class="sg-swatch"><span class="sg-chip" style="background:var(--sa-danger)"></span> Gefahr <span class="sg-code">--sa-danger · #ef4444</span></div>
            <div class="sg-swatch"><span class="sg-chip" style="background:var(--sa-warning)"></span> Warnung <span class="sg-code">--sa-warning · #f59e0b</span></div>
            <div class="sg-swatch"><span class="sg-chip" style="background:var(--sa-success)"></span> Erfolg <span class="sg-code">--sa-success · #10b981</span></div>
            <div class="sg-swatch"><span class="sg-chip" style="background:var(--sa-info)"></span> Neutral <span class="sg-code">--sa-info · #6b7280</span></div>
        </div>
        <div class="sg-card">
            <div class="sg-card-label">Text (kein Schwarz)</div>
            <div class="sg-swatch"><span class="sg-chip" style="background:#1f2937"></span> Fließtext <span class="sg-code">#1f2937</span></div>
            <div class="sg-swatch"><span class="sg-chip" style="background:#111827"></span> Überschrift <span class="sg-code">#111827</span></div>
            <div class="sg-swatch"><span class="sg-chip" style="background:#6b7280"></span> Gedämpft <span class="sg-code">#6b7280</span></div>
            <div class="sg-note">Reines #000 nur im Druck (@media print — Rechnungen/Angebots-PDF).</div>
        </div>
    </div>

    <div class="sg-h2">2 · Seitenkopf <small>&lt;x-page-head&gt; — der Kopf dieser Seite ist das Muster</small></div>
    <div class="sg-grid"><div class="sg-card" style="flex:1 1 100%">
        <div class="sg-card-label">Verwendung</div>
        <div class="sg-code">&lt;x-page-head title="Filialen" sub="Beschreibung…" current="Filialen"&gt;&lt;x-slot:actions&gt;…&lt;/x-slot:actions&gt;&lt;/x-page-head&gt;</div>
        <div class="sg-note">Titel 26px/800 GROSS · Untertitel gedämpft · Breadcrumb „Dashboard › Seite" · Aktionen rechts. 14 Seiten nutzen ihn bereits.</div>
    </div></div>

    <div class="sg-h2">3 · Buttons</div>
    <div class="sg-grid"><div class="sg-card" style="flex:1 1 100%">
        <button type="button" class="sg-btn sg-btn-primary">Speichern</button>
        <button type="button" class="sg-btn sg-btn-soft">Abbrechen</button>
        <button type="button" class="sg-btn sg-btn-danger">Löschen</button>
        <button type="button" class="sg-btn sg-btn-primary" disabled>Speichern (inaktiv)</button>
        <div class="sg-note">Primär = eine Aktion je Fläche. Gefahr-Button nie als Primärfarbe. Dunkle/Navy-Buttons sind verboten.</div>
    </div></div>

    <div class="sg-h2">4 · Status-Pills <small>heller Grund + dunkle Tinte (AA-Kontrast)</small></div>
    <div class="sg-grid"><div class="sg-card" style="flex:1 1 100%">
        <span class="sg-pill sg-pill-accent"><i></i> Neu</span>
        <span class="sg-pill sg-pill-info"><i></i> In Arbeit</span>
        <span class="sg-pill sg-pill-warning"><i></i> Wartet</span>
        <span class="sg-pill sg-pill-danger"><i></i> Überfällig</span>
        <span class="sg-pill sg-pill-success"><i></i> Erledigt</span>
        <span class="sg-count" style="margin-left:14px">12</span> <span class="sg-note" style="display:inline">← Zähler-Badge (Navi/Listen)</span>
    </div></div>

    <div class="sg-h2">5 · Formularfelder</div>
    <div class="sg-grid">
        <div class="sg-card">
            <div class="sg-card-label">Normal + Fokus</div>
            <div class="sg-field"><label class="sg-label">Kunde</label><input class="sg-input" placeholder="Name eingeben…"><div class="sg-hint">Fokus = grüner Rahmen + heller Hof.</div></div>
            <div class="sg-field"><label class="sg-label">Filiale</label><select class="sg-input"><option>Hauptsitz</option><option>Nord</option></select></div>
        </div>
        <div class="sg-card">
            <div class="sg-card-label">Fehlerzustand</div>
            <div class="sg-field"><label class="sg-label">E-Mail</label><input class="sg-input is-error" value="keine-mail"><div class="sg-error-text">Bitte eine gültige E-Mail-Adresse angeben.</div></div>
        </div>
    </div>

    <div class="sg-h2">6 · Tabelle (Listen-Standard)</div>
    <div class="sg-grid"><div class="sg-card" style="flex:1 1 100%">
        <table class="sg-table">
            <thead><tr><th>Kunde</th><th>Vorgang</th><th>Status</th><th style="text-align:right">Betrag</th></tr></thead>
            <tbody>
                <tr><td>Mustermann-Immobilienverwaltung Rhein-Main GmbH &amp; Co. KG</td><td>WP-Auslegung</td><td><span class="sg-pill sg-pill-warning"><i></i> Wartet</span></td><td style="text-align:right">18.420,00 €</td></tr>
                <tr><td>Aydın</td><td>PV 9,8 kWp</td><td><span class="sg-pill sg-pill-success"><i></i> Erledigt</span></td><td style="text-align:right">-</td></tr>
            </tbody>
        </table>
        <div class="sg-note">Erste Zeile absichtlich mit Extremfall (sehr langer Name) — Komponenten müssen mit Echtdaten halten, nicht mit Demozeilen.</div>
    </div></div>

    <div class="sg-h2">7 · Filterleiste</div>
    <div class="sg-grid"><div class="sg-card" style="flex:1 1 100%">
        <div class="sg-filterbar">
            <input class="sg-input sg-search" placeholder="Suchen…">
            <button type="button" class="sg-chip-filter is-active">Alle</button>
            <button type="button" class="sg-chip-filter">Offen</button>
            <button type="button" class="sg-chip-filter">Überfällig</button>
            <span style="flex:1"></span>
            <button type="button" class="sg-btn sg-btn-primary">Neu anlegen</button>
        </div>
    </div></div>

    <div class="sg-h2">8 · Modal <small>statisch dargestellt</small></div>
    <div class="sg-modal-stage">
        <div class="sg-modal">
            <div class="sg-modal-head">Eintrag löschen? <span class="sg-x">✕</span></div>
            <div class="sg-modal-body">Der Vorgang <strong>#4711</strong> wird in den Papierkorb verschoben. Das lässt sich rückgängig machen.</div>
            <div class="sg-modal-foot">
                <button type="button" class="sg-btn sg-btn-soft">Abbrechen</button>
                <button type="button" class="sg-btn sg-btn-danger">In den Papierkorb</button>
            </div>
        </div>
    </div>

    {{-- ============================================================================================
         9 · HAUSPLANER-INSEL (PB-023)

         WARUM DIESER ABSCHNITT ECHTE `hp-`-KLASSEN TRÄGT UND KEINE NACHBAUTEN.

         Der naheliegende Weg wäre gewesen, die Insel-Formen mit `sg-`-Klassen nachzubauen. Dann
         zeigte diese Fläche eine KOPIE — und eine Kopie altert getrennt vom Original. Der
         Screenshot-Diff bliebe grün, während sich die Insel verändert; die Referenzfläche
         behauptete dann eine Übereinstimmung, die es nicht mehr gibt. Deshalb: dieselbe
         Stilschicht, dieselben Klassennamen, dieselbe Verschachtelung wie im JSX.

         Die Struktur jedes Blocks ist aus der laufenden Quelle abgelesen, nicht erinnert:
           hp-ok-       app/dashboard/Kopfrahmen.tsx:197 · app/dashboard/ObjektkopfUeberlauf.tsx:78
           hp-ep-       app/rahmen/EigenschaftenPanel.tsx:188-198
           hp-ef-       app/EngineFlaeche.tsx:64-82
           hp-wg-       app/dashboard/WerkzeugGruppenMenue.tsx:98
           hp-schiene-  app/rahmen/GruppenzeileUndSchiene.tsx:271
           hp-mb-       app/rahmen/MindestbreiteHinweis.tsx:69-85
    ============================================================================================= --}}
    <div class="sg-h2">9 · Hausplaner-Insel <small>Quelle: resources/planner/hausplaner/hausplaner.css — geändert wird nur dort</small></div>
    <div class="sg-lede" style="margin-bottom:10px">
        Die Insel ist ein gekapseltes React-Bündel mit eigener Stilschicht und eigenen
        <span class="sg-code">--hp-</span>-Tokens. Sie liegt hier, weil eine Referenzfläche, die einen
        ganzen Bereich auslässt, für ihn auch nicht schützt: <strong>was hier nicht liegt, fällt beim
        Screenshot-Diff nicht auf.</strong> Die Blöcke tragen die echten Insel-Klassen — kein Nachbau.
    </div>

    {{-- Der ehrliche Vermerk. Er verschwindet, sobald die Stilschicht die Farbtokens selbst trägt;
         eine Zusage in `HausplanerInselTest` hält beide Richtungen fest. --}}
    <div class="sg-rule" style="background:var(--sa-warning-bg);border-color:var(--sa-warning-border)">
        <strong>Diese Blöcke zeigen Struktur, noch nicht Farbe.</strong>
        Die Insel setzt ihre <span class="sg-code">--hp-</span>-Tokens erst beim Start des React-Bündels
        (<span class="sg-code">setzeTokenVariablen()</span>, gespeist aus <span class="sg-code">studioDaten.ts</span>) —
        die Stilschicht allein führt sie nicht. Auf dieser Seite läuft das Bündel nicht, also lösen alle
        <span class="sg-code">var(--hp-…)</span> ins Leere auf und die Flächen bleiben durchsichtig.
        <strong>Maße, Abstände, Verschachtelung und Typografie stimmen und sind ab hier gegen Regression gesichert;
        die Farben sind es noch nicht.</strong>
        Die Tokens hier von Hand einzutragen wäre eine zweite Wahrheit neben
        <span class="sg-code">studioDaten.ts</span> — das ist ausdrücklich nicht gewollt. Der Weg dorthin ist
        <span class="sg-code">PB-024-N2</span> (Laufzeit-Auflösung).
    </div>

    <div class="sg-grid">
        <div class="sg-card" style="flex:1 1 340px">
            <div class="sg-card-label">Objektkopf <span class="sg-code">hp-ok-</span></div>
            <div class="sg-hp-flaeche" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <span class="hp-ok-name" title="Mustermann-Immobilienverwaltung Rhein-Main GmbH">Mustermann-Immobilienverwaltung Rhein-Main GmbH</span>
                <span class="hp-ok-pille hp-ok-pille--aktuell">aktuell</span>
                <button type="button" class="hp-ok-knopf">Übernehmen</button>
            </div>
            <div class="sg-hp-flaeche" style="margin-top:8px;display:flex;align-items:center;gap:8px">
                <span class="hp-ok-pille hp-ok-pille--veraltet">veraltet</span>
                <span class="hp-ok-pille hp-ok-pille--nie">nie übernommen</span>
            </div>
            <div class="sg-note">Erste Zeile mit Extremfall: der lange Name wird gekürzt, statt die Kopfzeile aufzublähen.</div>
        </div>

        <div class="sg-card" style="flex:1 1 340px">
            <div class="sg-card-label">Befunde im Eigenschaften-Panel <span class="sg-code">hp-ep-</span></div>
            <div class="sg-hp-flaeche">
                <ul class="hp-ep-befundliste" style="list-style:none;margin:0;padding:0">
                    <li class="hp-ep-befund">
                        <span aria-hidden class="hp-ep-schwere-symbol">✋</span>
                        <span><strong class="hp-ep-schwere-text">Abgelehnt</strong> – Wand schneidet eine tragende Achse.</span>
                    </li>
                    <li class="hp-ep-befund">
                        <span aria-hidden class="hp-ep-schwere-symbol">✋</span>
                        <span><strong class="hp-ep-schwere-text">Abgelehnt</strong> – Öffnung liegt ausserhalb der Wand.</span>
                    </li>
                </ul>
                <div class="hp-ep-umfang">Geprüft wurden Topologie und Öffnungslage.</div>
            </div>
            <div class="sg-note">Schwere trägt Symbol <em>und</em> Text — nicht nur Farbe (A11y).</div>
        </div>

        <div class="sg-card" style="flex:1 1 340px">
            <div class="sg-card-label">Eingabefelder <span class="sg-code">hp-ef-</span></div>
            <div class="sg-hp-flaeche">
                <h3 class="hp-ef-rubrik" style="margin:0 0 8px">Eingaben (2)</h3>
                <div class="hp-ef-felder">
                    <label class="hp-ef-feld" style="margin-bottom:10px">
                        <span class="hp-ef-feldkopf">
                            <span class="hp-ef-label">Wandstärke</span>
                            <span class="hp-ef-einheit">mm</span>
                            <span class="hp-ef-pflicht">Pflicht</span>
                        </span>
                        <input class="hp-ef-eingabe" value="240">
                    </label>
                    <label class="hp-ef-feld">
                        <span class="hp-ef-feldkopf">
                            <span class="hp-ef-label">Bauteil</span>
                        </span>
                        <select class="hp-ef-eingabe"><option>Aussenwand</option><option>Innenwand</option></select>
                    </label>
                </div>
            </div>
        </div>

        <div class="sg-card" style="flex:1 1 340px">
            <div class="sg-card-label">Gruppenzeile &amp; Wegweiser <span class="sg-code">hp-gz-</span></div>
            <div class="sg-hp-flaeche" style="padding:0">
                <div class="hp-gz-wegweiser">
                    <span aria-hidden class="hp-gz-wegweiser-pfeil">→</span>
                    <span class="hp-gz-wegweiser-satz">Wähle zuerst ein Geschoss — die Werkzeuge richten sich danach.</span>
                </div>
                <div class="hp-gz-gruppenkopf">
                    <span class="hp-gz-gruppenname">Wände</span>
                    <span class="hp-gz-gruppenzahl">6</span>
                </div>
                <div class="hp-gz-leerzustand">
                    <span>In dieser Gruppe ist nichts angeheftet.</span>
                    <span class="hp-gz-kuerzel">⌘K</span>
                </div>
            </div>
        </div>

        <div class="sg-card" style="flex:1 1 340px">
            <div class="sg-card-label">Werkzeuggruppen-Menü <span class="sg-code">hp-wg-</span></div>
            <div class="sg-hp-flaeche">
                <div class="hp-wg-zeile">
                    <div class="hp-wg-text">
                        <span>Wand zeichnen</span>
                        <span class="hp-wg-unterzeile">Zwei Punkte setzen, Esc bricht ab</span>
                    </div>
                    <span class="hp-wg-kuerzel">W</span>
                </div>
                <div class="hp-wg-zeile">
                    <div class="hp-wg-text">
                        <span>Treppe setzen</span>
                        <span class="hp-wg-unterzeile">Braucht zwei Geschosse</span>
                    </div>
                    <span class="hp-wg-kuerzel">T</span>
                </div>
                <div class="hp-wg-zaehler">2 von 8 angeheftet</div>
            </div>
        </div>

        <div class="sg-card" style="flex:1 1 340px">
            <div class="sg-card-label">Schienenkopf &amp; Fähigkeiten <span class="sg-code">hp-schiene-</span> · <span class="sg-code">hp-fn-</span></div>
            <div class="sg-hp-flaeche">
                <div class="hp-schiene-kopf">
                    <div class="hp-schiene-kopf-reiter">Eigenschaften</div>
                    <button type="button" class="hp-schiene-schalter" aria-label="Schiene einklappen">›</button>
                </div>
                <div class="hp-fn-rubrik">Geometrie</div>
                <div style="display:flex;padding:0 12px"><span class="hp-fn-label">Topologie prüfen und Abweichungen melden</span></div>
                <div class="hp-fn-fuss">Weitere Fähigkeiten folgen mit L7.</div>
            </div>
        </div>
    </div>

    <div class="sg-h2">9.1 · Mindestbreiten-Sperre <small>hp-mb- — statisch dargestellt, wie das Modal</small></div>
    <div class="sg-grid"><div class="sg-card" style="flex:1 1 100%">
        <div class="sg-hp-buehne">
            <div class="hp-mb-flaeche" role="status">
                <div class="hp-mb-kasten">
                    <p class="hp-mb-titel">Der Planer braucht mehr Breite</p>
                    <p class="hp-mb-satz">
                        Ab 1024 px Fensterbreite ist er vollständig bedienbar. Auf schmaleren
                        Bildschirmen liegen Werkzeuge ausserhalb des sichtbaren Bereichs — sie sind
                        dann nicht erreichbar, auch nicht durch Scrollen.
                    </p>
                    <p class="hp-mb-fussnote">Eine Bedienung auf schmalen Geräten ist geplant, aber noch nicht gebaut.</p>
                </div>
            </div>
        </div>
        <div class="sg-note">
            Die Fläche liegt in der Insel <span class="sg-code">position: absolute; inset: 0</span>. Hier hält sie eine
            begrenzte Bühne — ohne sie verankerte sie sich an der Seite und legte sich über den Styleguide.
        </div>
    </div></div>
</div>
@endsection
