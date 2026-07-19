# PV-Belegungs- & Dachform-Fachreferenz (Konserve aus dem Alt-Dachplaner)

**Zweck (2026-07-19):** Der Alt-Dachplaner-Prototyp (public/planer/planer.js) wurde abgerissen.
Seine PV-Belegungs- und Dachform-Logik ist erarbeitetes Fachwissen, das der geplante Posten
„Dachbelegung (PV)" wieder braucht (Evaluator-Blocker B6, Runde 4). Diese vier QUELLDATEIEN
(nicht das Minifikat) sind die git-getrackte Konserve — 1:1-Kopien aus dem Playground-Archiv.
REFERENZ, KEIN BUILD: liegt bewusst unter docs/, ist in keinem tsconfig/vite-Include.

## Inhalt
- `dachformVorlagen.ts` (2399 Z.) — der Kern: Dachform-Katalog inkl. walm-l/walm-t
  (VorlagenShapeKey, status verfuegbar/geplant), Geometrie-Formeln (Sattel-/Pult-/Walm-
  Sparrenlaengen, First-Rise, Walm-Ruecksprung, Gratsparren-3D), PV-Interface
  (VorlagenPv: belegbareSeiten, Reihenabstaende inkl. flachdachReihenabstand),
  Zimmerer-/Dachdecker-Flags, Warnung-Codes, Apply/Validierungs-Logik.
- `dachformVorlagen.test.ts` (1410 Z.) — REFERENZFAELLE mit erwarteten Zahlen; beim
  Nachbau in ticket zuerst diese Tests transplantieren (Soll-Werte = eine Wahrheit).
- `linienBauteile.ts` (167 Z.) — First/Grat/Kehle/Traufe/Ortgang als Linien-Bauteile
  (Grate = PV-Sperrzone).
- `DachplanerProPage.tsx` (3786 Z.) — Anwendungslogik: Belegungs-Regeln im Kontext
  („Trapez-Haupthaenge belegbar, Walmdreiecke Restbelegung, Grate sind Sperrzone",
  Geometrie-Aenderung entfernt vorhandene Belegung, Sperrzonen/Randabstaende).

## Kernregeln (Kurzextrakt fuer den Wiedereinstieg)
1. Belegbarkeit je Dachform: Sattel = beide Haupthaenge; Walm = Trapez-Haupthaenge
   belegbar, Walm-Dreiecke nur Restbelegung; Grate/Kehlen = Sperrzone.
2. Geometrie-Aenderung invalidiert die Belegung (bewusst: neu belegen statt still falsch).
3. Reihenabstand ist form-/neigungsabhaengig; Flachdach-Reihenabstand eigener (geplanter) Zweig.
4. walm-l / walm-t existieren als Katalogformen mit eigener Geometrie (Ruecksprung je Fluegel) —
   das NEUE Bundle kann bisher nur einfaches Walm.

## Herkunft & Integritaet
Quelle: docs/_playground-archiv/src/... (gitignoriert). SHA-256 der Kopien = Archiv-Original
(vom Evaluator in Runde 5 gegenzupruefen). Weitere Konzept-Dokus im Archiv:
docs/konzepte/layout-2026-06/vorgabe-3d-dachplanung.md, plan-3d-dachplanung-umsetzung.md,
pruefbericht-pv-installateur-2026-06-12.md, pruefbericht-zimmermannmeister-2026-06-12.md.

## Nachtrag (Runde 5, A10-Nacharbeit)
- `belegungStatus.ts` (42 Z.) — Traeger von Kernregel 2 (Geometrie-Aenderung invalidiert die
  Belegung): BelegungStatus keine|gueltig|pruefpflichtig, geometrieMachtPruefpflichtig(),
  BELEGUNG_WARNUNG. Loest den Import in DachplanerProPage.tsx Z.27 auf.
- `konzepte/` — die vier Konzept-Dokus aus dem gitignorierten Archiv (Vorgabe 3D-Dachplanung,
  Umsetzungsplan, Pruefberichte PV-Installateur + Zimmermannmeister 2026-06-12): gleiche
  Verlust-Logik, fachliche Begruendung hinter den Regeln.
