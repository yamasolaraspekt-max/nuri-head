# PLANNER-SPEC — ProjektionsService: Szene → gebaeude_geometrie (ticket)

**Rolle:** Planner (kein Produktionscode) · **Datum:** 2026-07-17 · **Heimat-App:** ticket (LIVE, additiv-only)
**Grundlage (Code-Wahrheit, gegen-gegrept 2026-07-17):** Zielbild §2/§4, playground `src/hausplaner/projection/raumProjektion.ts`
(eingefrorener Vertrag), ticket `GeometrieAbleitungService`/`RaumGeometrie`/`TopologieGate`/`AnforderungsprofilHeizlastAdapter`.

## 0 · Ist-Stand (Code-belegt) — warum DIES die Naht ist

- **Schreibwahrheit Geometrie = `anforderungsprofile.gebaeude_geometrie`** (JSON: raeume[]+bauteile[], Heizlast-Input,
  versioniert). `GrundrissController` schreibt sie hinter dem `TopologieGate`; `AnforderungsprofilHeizlastAdapter` liest sie.
- **`BuildingModelVersion`** = derived_only-Projektion mit Herkunft (`SourceGeometryRef`→Hash auf gebaeude_geometrie),
  append-only. Server-Geometrie ist damit **kohärent single-truthed** — KEINE zweite Wahrheit.
- **Hausplaner `scene_json`** (an `hausplaner_documents.alternative_id`) = separate Autoren-Fläche (2D/3D-Editor).
  **Grep-Beweis:** es gibt in ticket KEINE Projektion scene_json → gebaeude_geometrie und keine Brücke im Hausplaner.
  → Der Editor produziert Geometrie, die die belastbare Kette nie sieht. Genau das schließt diese Spec.

## 1 · Ziel & Entscheidung

Ein **reiner, server-seitiger PHP-ProjektionsService**, der aus dem Hausplaner-`scene_json` je Geschoss die Räume
erkennt und in die **bestehende `gebaeude_geometrie`-Struktur** (raeume[] mit polygon, wand_segmente, oeffnungen)
übersetzt — der von der playground-Referenz `raumProjektion.ts` als „P2 ProjektionsService" vorgesehene Baustein.
Er ist die Brücke Autoren-Fläche → belastbare Wahrheit. Richtung ist durch das Zielbild gesetzt (Szene ist Quelle).

## 2 · Bewusster Split (Live-App-Vorsicht)

- **P2-1 (erster Bau, additiv/safe):** ProjektionsService als **reine, UNVERDRAHTETE** Funktion + Tests. Schreibt NICHTS,
  ruft kein gebaeude_geometrie-Update. Liefert das raeume[]-Array zurück und prüft jedes Polygon vorab am `TopologieGate`.
- **P2-2 (später, eigener Posten, STOPP bei Yama):** Verdrahtung — Szene beim Speichern in gebaeude_geometrie projizieren
  (neue Profil-Version, wie GrundrissController). Ändert die Schreibkette → erst nach Yama-Go + Referenzfall-Abnahme.

## 3 · Vertrag (an der playground-Referenz orientiert, in PHP neu — kein TS-Code kopiert)

Eingabe: `scene` (dekodiertes scene_json: levels[], nodes[] mit WallNode/OpeningNode, units mm).
Ausgabe je Raum: `{ polygon:[{x,y}mm], hoehe_mm, wand_segmente:[{von,bis,grenzflaeche(innen|aussen),azimut_grad,oeffnungen:[{breite_mm,hoehe_mm,typ}]}], decke, boden }`
— exakt das Format, das `GeometrieAbleitungService::ausGeometrie` / `RaumGeometrie` erwarten.

Regeln (aus dem eingefrorenen Vertrag):
1. Raumerkennung: geschlossene Wand-Umläufe je Geschoss (CCW, Fläche > 0).
2. **innen/aussen:** teilt sich eine ungerichtete Wandkante ZWEI Räume → `innen`, sonst `aussen`.
3. **azimut_grad:** nur für Aussenwände aus der Wand-Normalen abgeleitet (Nord = +y). (Schließt AP-4-Azimut-Lücke.)
4. Öffnungen (window/door/opening → fenster/tuer/oeffnung) werden ihrer Wand zugeordnet.
5. **decke/boden:** ehrlich `null` (kein erfundener bauteil_typ — Operanden-Gate), bis eine Quelle existiert.
6. **Jedes Raum-Polygon zuerst durch `TopologieGate::pruefePolygon`** — ungültige Geometrie wird abgelehnt, nie still projiziert.

## 4 · Nahtstellen

- Neuer Service `App\Services\Geometrie\SzeneProjektionService` (rein, keine DB in P2-1).
- LIEST optional das bestehende `TopologieGate`. Schreibt NICHTS. `GrundrissController`/`gebaeude_geometrie` unberührt.
- Referenz nur lesen (anderer Stack): playground `raumProjektion.ts`, `roomDetection.ts`, `wallGeometry.ts`.

## 5 · Kleinster sicherer erster Schritt (P2-1a)

Ein rechteckiger Ein-Raum (4 Wände, keine Öffnung) → erwartete raeume[0]: polygon 4 Ecken, 4 Aussenwände mit
korrektem azimut (N/O/S/W), decke/boden null. Referenztest mit **handgerechneten** Sollwerten (wie AP-4a-Stil).
Danach: Innenwand-Fall (zwei Räume, geteilte Kante → innen), Öffnungs-Fall. Jede Stufe eigener grüner Test.

## 6 · Abnahmekriterien (Evaluator, mit Gegen-Beweis)

- Service rein/unverdrahtet: grep zeigt keinen Aufruf im Schreibpfad; `gebaeude_geometrie`/`GrundrissController` git-diff = 0.
- Ein-Raum-Rechteck → 4 Aussenwände, Azimute N=0/O=90/S=180/W=270 (Nord=+y) handgerechnet bestätigt.
- Zwei-Raum → geteilte Kante `innen`, Außenkanten `aussen`.
- Ungültiges Polygon (Schmetterling) → über `TopologieGate` abgelehnt, nicht still projiziert.
- Ausgabe füttert `GeometrieAbleitungService::ausGeometrie` ohne Formatbruch (Round-Trip-Beweis).
- Volle Suite grün; nur additive Dateien.

## 7 · Governance / Stopp

Additiv-only, keine Migration, kein Schreiben in gebaeude_geometrie in P2-1. Verdrahtung (P2-2) = eigener Posten nach
Yama-Go. Rollentrennung: diese Spec = Planner; Bau = Generator; Abnahme = unabhängiger Evaluator. Vor dem Bau erneut
`git log`+grep (CLAUDE.md Z.27): prüfen, dass `SzeneProjektionService` nicht bereits existiert.
