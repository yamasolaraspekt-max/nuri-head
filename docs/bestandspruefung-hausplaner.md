# Bestandsprüfung Hausplaner — Evaluator (read-only, existing-first)

> **Rolle:** EVALUATOR. Read-only, kein Code, kein Commit. **Beweis statt Bericht** — jede Aussage
> ist am Code / an einem selbst gefahrenen Gate / an der Norm belegt. Gemessen am Stand
> `auto/hausplaner-dashboard-v1 @ e4693f1` (2026-07-24). Gates selbst gefahren: `tsc:hausplaner` Exit 0 ·
> `schema:hausplaner:check` Exit 0 · `test:hausplaner` **684/684 pass, 0 fail**.

## Runde 1 — Inventar

### 1.1 Funktionsinventar (Modell · Command · Persistenz)
- **Modellwahrheit** (`domain/scene.types.ts`): `SceneDocument = levels[] · nodes[] · roofs[] · ceilings[] · materials[] · settings`. Alle Längen ganze mm.
- **Node-Typen (7):** `wall · window · zone · object · route` + Sammlungen `roof · ceiling`.
- **Commands (20, undo-fähig via inverse Immer-Patches):** `ADD/UPDATE/REMOVE` × `LEVEL · NODE · ROOF · ROOF_AUFBAU · CEILING` + `MOVE_NODE · SET_NODES_SICHTBAR · SET_NODES_GESPERRT · UPDATE_SETTINGS`. `CommandAbgelehnt` statt stillem Erfinden (Operanden-Gate).
- **Persistenz:** `schemaVersion` (Lade-Migration) + `revision`/`base_revision` (**409-Konfliktschutz vorhanden**). Zod (`validation.ts`) ↔ `scene-document-v2.schema.json` (PHP-Validator). Desync ⇒ 422 → Gate `schema:hausplaner:check`.
- **Fixtures (2):** `u-dach`, `decke-treppe` (`?fixture=` — deterministische Sicht-Abnahme).
- **Geführte Planung:** 4 „Schritt"-Referenzen in `HausplanerApp` → Wizard existiert, aber **Schritte hardcoded** (Attrappe, nicht modellgetrieben) → Runde-2-Punkt.

### 1.2 3D-Inventar (was rendert — alles grün abgenommen)
| Bauteil | Stand | Quelle |
|---|---|---|
| Wände | Prisma **mit Gehrung** (eine Ecken-Wahrheit `wandBaender`, 2D+3D geteilt); Öffnungen via `segmentierung`; Fallback-Box | `szene.ts`/`platzierung.ts` |
| Dächer (8 Formen) | `sattel/walm/pult/flach/rect` + `l/t/u-shape` (Verschneidung), platziert am **Bbox-Zentrum** (SSOT `dachRoh`) | `dachMesh.ts`/`dachVerschneidung`/`dachUForm` |
| Dachaufbauten | 5 Gauben-Typen + Kamin + Dachfenster/Lüfter/Sat/Lichtkuppel | `dachAufbautenMesh.ts` |
| Böden/Räume | aus Raumerkennung abgeleitet | `roomDetection` |
| Geschossdecke | Slab-mit-Treppenauge (ersetzt dekorative Decke, eine Wahrheit) | `deckenMesh.ts` |
| Treppen | je Stufe ein Quader | `platzierung.ts` |
| Capture | `?capture=1` Snapshot | `capture.ts` |

→ **Geometrie-/3D-Ebene ist reif und abgenommen** (Bau-Fokus der bisherigen Wellen).

### 1.3 Werkzeugmatrix
- **Aktiv (bedienbar):** 6 Modus-Werkzeuge (Auswahl · Wand · Fenster · Tür · Dach · Treppe) + Aktionen (Löschen · Duplizieren) mit Aktivierungs-Begründung (`resolveToolState.reason`, `activation.test.ts`). CAD-Katalog-Teilmenge (UI-3b).
- **13 Fach-Engines — reine, getestete Module mit `engineModul`+`engineExport`, jetzt ehrlich `in_entwicklung` („Panel folgt"), noch OHNE Eingang→Panel→Ausgang:**
  FBH · Heizkörper (EN 442) · Heizkreis-Verteiler · Abwasser-Gefälle (DIN 1986-100) · Küchen-Arbeitsdreieck (DIN 18022) · PV-Schnellbelegung · U-Wert (ISO 6946) · Fenster Uw/RC/Preis (ISO 10077 / EN 1627) · Sparren (EC 5) · Treppen-Auslegung (DIN 18065) · Holz-Mengen · Holz-Bauteile · Schifter-Liste.

**Kern-Befund R1:** Modell/Render/Persistenz sind fertig; der Nachholbedarf ist **Verdrahtung**, nicht Neubau. 13 Engines existieren fertig + getestet, brauchen nur je ein Panel nach dem Muster von Verdrahtung #1 (L/T/U).

## Runde 2 — Ursachen · Top-10-UX · Reihenfolge (existing-first)

### 2.1 Ursachenanalyse
1. **Geometrie reif, Fachlogik schläft** — die bisherigen Wellen bauten Modell/Topologie/Render (Dächer D-a, Gehrung, Decke). Die 13 Engines sind als reine Module portiert, aber Verdrahtung ist der jüngste Schritt (Verdrahtung #1 = L/T/U erst gerade). Ursache ist Reihenfolge, nicht fehlende Substanz.
2. **„Schläft ohne Grund"** (behoben in Dashboard Batch 2) — Zustände waren binär `aktiv/schlaeft` statt der echten vier; jetzt `verfuegbar/voraussetzung/nur_ergebnis/in_entwicklung` mit Text+Icon.
3. **Geführte Planung = Attrappe** (4 hardcoded Schritte). Wizard-Rahmen ist Tor-1-frei; **Guardrail:** an vorhandene Services verdrahten (`CanonicalHash`, `DerivedBuildingModelVersionStore`, `ProjectionConflictException`/409, `SzeneProjektionService`, `TopologieGate`, `WpAuslegungskette`, `HydraulicService`) — **kein zweiter Snapshot-/Hash-/Projektions-Mechanismus**.
4. *(Zwischenbefund korrigiert:)* Der Magnet-Umschalter ist **verdrahtet** (`HausplanerApp.tsx:773` — `fang`-Icon → `UPDATE_SETTINGS{snapEnabled}`, `aktiv`-State, Tooltip; Snap-Logik Z.357). Ein früheres „fehlt" beruhte auf einem `head`-Abschneide-Fehler beim Grep und ist zurückgezogen.

### 2.2 Top-10-UX (existing-first — vorhandenes verdrahten, nicht neu bauen)
1. **Erstes Engine-Panel-Muster** an EINER risikoarmen Engine (U-Wert oder Sparren — reine Funktion) → Vorlage für die 13. *(Höchster existing-first-Hebel: 13 fertige Module warten auf je ein Panel.)*
3. **Geführte Planung modellgetrieben** — Schritte aus dem Modell ableiten, an BuildingModel-Services (Guardrail oben).
4. **Katalog ↔ Registry abgleichen** — CAD-Katalog vs. aktive Modus-Tools + Registry: welche Tools echt, welche Karteileiche.
5. **Speicher-Status-Zone sichtbar** — Bestätigung vor Datenverlust ist in Batch 2 da; die Status-Anzeige-Zone selbst prüfen/vervollständigen.
6. **2D/3D-Selektions-Sync** — Auswahl/Hover synchron zwischen Konva-2D und three-3D (dieselben Daten, ein Selektionszustand).
7. **Aktivierungs-Grund sichtbar** — `resolveToolState.reason` im UI als Tooltip zeigen (warum ein Tool inaktiv ist).
8. **Undo/Redo-Sichtbarkeit** verifizieren (Batch 1).
9. **Geschoss-Stepper** bei vielen Etagen (Batch 1, Sprung-Wähler ab ~8) — Extremfall prüfen.
10. **A11y-Kontrast** der neuen `ZustandBadge`-Farben (T.warnInk/okInk/muted auf Soft-BG) in 3 Viewports messen.

### 2.3 Umsetzungsreihenfolge (existing-first)
1. Ein Engine-Panel-Muster an EINER risikoarmen Engine (U-Wert/Sparren) — Vorlage.
3. Rest der 13 Engines nach dem Muster wecken, gruppenweise (dach-zimmerei zuerst: 5 Engines + Render-Bezug).
4. Geführte Planung an BuildingModel-Services verdrahten (Wizard-Welle, Tor-1-frei; gated hinter Dashboard-Abschluss + Guardrail).
5. Katalog ↔ Registry konsolidieren (eine Werkzeug-Wahrheit).
6. 2D/3D-Selektions-Sync.

> **Nicht-Ziele (bewusst):** keine KI-Grundriss-Generierung, keine Auto-Benotung, keine neuen Fach-Rechen-Wahrheiten (Reuse der 13 Engines). Fach-/Rechts-entscheidende Slices brauchen Konzept + Tor-1. Tor 2 (Merge/Deploy) bleibt Yama.
