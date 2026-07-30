> # ⛔ HISTORISCH — dieses Papier beschreibt einen Stand, den es nicht mehr gibt
>
> **Planner, 30.07.2026, 08:45. Auf fünf Befunde des unabhängigen Prüfers, alle nachgemessen und
> alle bestätigt** (`PB-001` bis `PB-005`, siehe `docs/planner/PRUEFER-BEFUNDE.md`).
>
> **Die Wurzel steht in PB-002 und macht alles andere zur Folge:** dieses Papier misst gegen
> `2f12c64` auf `private/app-code-backup` — **einen Sicherungszweig, nicht den Arbeitszweig.**
> *Jede Zahl darin ist gegen einen Ast gemessen, auf dem seit sechs Tagen nichts passiert.*
>
> **Vier Aussagen sind vom Baum widerlegt** — von mir gegen `67ac4ea0` nachgemessen, nicht
> übernommen:
>
> | Papier sagt | gemessen |
> |---|---|
> | 422-Blocker, Bauart-Szenen unspeicherbar (Warnkasten ganz oben) | `npm run schema:hausplaner:check` ⇒ **exit 0** |
> | `HausplanerApp.tsx` ~900 Zeilen | **2 308** |
> | 11 Command-Typen | **19** |
> | `geometry/` 30 Dateien | **50** |
> | Werkzeug-Registry *„NICHT verdrahtet, kein UI-Konsument"* | **9 Konsumenten** in `app/` |
>
> **Der Warnkasten unten war der gefährlichste Teil:** er nennt einen Datenverlust-Zustand, der
> behoben ist, und steht **vor** allem anderen. *Wer ihn heute liest, baut den Fix ein zweites Mal
> — oder misstraut einem Gate, das grün ist.*
>
> **Was noch gilt:** die Entwurfsabschnitte §6/§8/§9, auf die `fahrplan-dashboard-versionen.md`
> zeigt, sind Gestaltungsarbeit und von den Zahlen unabhängig. **Wer sie benutzt, nimmt die
> Gestalt und misst die Zahlen neu.**


---

# tool-dashboard-current-state.md

**Rolle:** PLANNER (read-only Bestandsaufnahme). Kein Produktivcode, keine Migration, keine Route, kein Commit.
**Datum:** 2026-07-23 · **Stand:** HEAD `2f12c64` (v9-Welle), Branch `private/app-code-backup`.
**Auftrag:** Ist-Analyse für ein kontextabhängiges Werkzeug-Dashboard (Master-Prompt §43).
**Grundsatz:** Ist-Beleg aus dem CODE, nicht aus dem Papier. Alle Pfade unter `resources/planner/hausplaner/`.

> ⛔ **Offener Blocker vor jeder Aufbauarbeit (aus der v9-Evaluator-Abnahme, nicht Teil dieser Planung):**
> `970f0cc` ergänzte `produkt.typ` in `domain/validation.ts`, ohne `scene-document-v2.schema.json` zu regenerieren.
> Folge: `schema:hausplaner:check` = Exit 1 (blockt `test:hausplaner` und `build:hausplaner`), und der Server-`SceneDocumentValidator`
> lehnt jede Öffnung mit `produkt.typ` ab (422 → Bauart-Szenen unspeicherbar). Fix ist eine Zeile (`npm run schema:hausplaner` + Commit).
> **Dieses Dashboard-Programm darf nicht auf einem roten Schema-Gate aufsetzen — Blocker zuerst schließen.**

---

## 1. Ist-Architektur in einem Bild

```
Blade (admin/hausplaner/*.blade) ── mountet ──► main.tsx
  └─ liest eingebettete Szene (#hausplaner-scene), Zod-validiert, migriert v1→v2
  └─ useHausplanerStore.init(scene, speichernUrl, csrf)
  └─ <HausplanerStudio/>            (v9-Shell: Modus start|guided|expert)
        ├─ StartView / GuidedView / ConfigWizard      (geführt)
        └─ <HausplanerApp/>                            (Experte = die eigentliche CAD-Fläche)
              ├─ Konva-Stage (2D)  + <DreiDBereich/> (three, 3D)   ← beide lesen DENSELBEN Store
              ├─ lokale Werkzeugleiste (Werkzeuge + FACHPLANER-Navi)
              ├─ rechtes Eigenschaftenpanel (kontextabhängig, aber nur 1-Selektion)
              └─ Statusleiste
Store (store/hausplanerStore.ts)  ← einzige Modell-Wahrheit
  └─ executeCommand (Immer produceWithPatches) → history.ts (inverse Patches) → undo/redo
Domain (domain/*)  ← SceneDocument/Zod/JSON-Schema; commands.types.ts (11 Command-Typen)
Geometry (geometry/*)  ← 30+ REINE Fachlogik-/Zeichen-Module (DIN, Uw, PV, TGA, Treppe …)
Renderers (renderers/three-d/*)  ← 3D; 2D-Renderer liegt inline in HausplanerApp.tsx
Backend  ← HausplanerController + SpeichereHausplanerDokumentRequest + SceneDocumentValidator (opis/json-schema)
```

**Zentrale gute Nachricht:** Die „eine-Wahrheit"-Regel steht schon — der **Store ist alleiniger Modellbesitzer**, alle Mutationen laufen über **Commands**, Undo/Redo über **inverse Patches**. Das ist genau das Fundament, das der Master-Prompt §23/§24 fordert. Das Dashboard ist überwiegend ein **UI-/State-Orchestrierungs-Projekt oben drauf**, kein Modell-Neubau.

---

## 2. Vorhandene Komponenten (konkrete Dateipfade)

### 2.1 App-/UI-Schicht — `app/`
| Datei | Rolle | Für das Dashboard relevant |
|---|---|---|
| `app/HausplanerStudio.tsx` | v9-Shell: Kopf (Marke, Speicherstatus + `Rev.`, Modus-Umschalter Übersicht/Experte), persistente Navigation, Toast, Nav-Auto-Einklappen <900px | **Layout-Skelett** (§5), Modus-Shell (§4), Statusanzeige (§31) — teilweise vorhanden |
| `app/StartView.tsx` | Start-Launcher (Kacheln) | Projektübersicht-Workspace (§9) |
| `app/GuidedView.tsx` | Geführte WizardBase; zeigt echte `scene.nodes`-Zahlen | Geführter Modus (§4.1) |
| `app/ConfigWizard.tsx` | Fenster/Tür/Treppe/Heizkörper-Konfigurator; „Übernehmen" → `ADD_NODE` auf gewählte Wand ODER JSON-Download (`configuratorPackage`) | Vollkonfigurator (§3, Ebene 6), Schreibpfad |
| `app/HausplanerApp.tsx` | **Die eigentliche CAD-Fläche** (~900 Z.): Konva-2D + three-3D, Werkzeugleiste, FACHPLANER-Navi, Kontext-Optionen (teils), Eigenschaftenpanel, Statusleiste, Keyboard | **Kern des Dashboards** — heute monolithisch |
| `app/DreiDBereich.tsx` | 3D-Viewport-Wrapper (mountet three-Szene, „Ansicht einpassen") | 3D-Ansicht (§18) |
| `app/studioUi.tsx` / `app/studioDaten.ts` | UI-Tokens/Daten der Shell | Design-Tokens |

### 2.2 State & Commands
| Datei | Inhalt | Bewertung |
|---|---|---|
| `store/hausplanerStore.ts` | Zustand: `scene`, `modus` (`'2d'\|'split'\|'3d'` = **Ansicht**), `selectedNodeIds`, `activeLevelId`, `speicherStatus`, `letzteAblehnung`. Aktionen: `init/setModus/setActiveLevel/selectNodes/executeCommand/undo/redo/kannUndo/kannRedo/istDirty/save` | **Solides Command-Fundament.** ABER: enthält NICHT das aktive Werkzeug, keinen Hover, keine Workspace-/View-Trennung, keinen Panel-Zustand |
| `store/history.ts` | Undo/Redo-Stack über inverse Patches | Wiederverwendbar 1:1 |
| `commands/applyCommand.ts` | Reducer aller Commands inkl. Fachprüfungen (Wandlänge≥Dicke, Öffnung-auf-Wand, Dach-je-Level …), wirft `CommandAbgelehnt` | **Validierungs-/Guard-Schicht existiert** — Basis für „warum deaktiviert" |
| `domain/commands.types.ts` | **11 Command-Typen:** `ADD_NODE, REMOVE_NODE, MOVE_NODE, UPDATE_NODE, ADD_ROOF, UPDATE_ROOF, REMOVE_ROOF, ADD_LEVEL, UPDATE_LEVEL, REMOVE_LEVEL, UPDATE_SETTINGS` | Deckt §24 zu ~70 %; fehlen: Select/Transform-Gruppen-Commands, Command-Zustandsmaschine |
| `domain/scene.types.ts` | `SceneDocument, Level, WallNode, OpeningNode(+produkt), ObjectNode(objectType inkl. 'stair'), ZoneNode, RoofNode, MaterialDefinition` | Datenmodell trägt Fachobjekte |
| `domain/validation.ts` + `scene-document-v2.schema.json` | Zod (Client) + generierter JSON-Schema (Server/PHP) | **Doppelquelle mit Generator** (`scripts/hausplaner-schema.mts`); additiv halten |

### 2.3 Werkzeug-Registry (Teil-Foundation, **NICHT verdrahtet**)
`geometry/werkzeugRegistry.ts` (aus `0f05052`) definiert `WerkzeugNode<T>`: `kind, schemaVersion, kategorie('bau'|'bauelement'|'haustechnik'|'pv'), kuerzel, label, beschreibung, parametrik(daten)→{bestanden,kennwerte}, faehigkeiten{waehlbar,ziehbar,dupliziert,loeschbar}, migrate?`. Funktionen: `registriereWerkzeug / werkzeug(kind) / alleWerkzeuge(kategorie?) / _leereRegistry`.
→ **Genau der Keim des vom Prompt geforderten Tool-Registry-Modells (§22)** — aber (a) kein UI-Konsument, (b) es fehlen `supportedWorkspaces/Views/SelectionTypes`, `activationRules`, `optionsSchema`, `commandFactory`, `disabledReasonResolver`, `shortcut/cursor` aus §22.

### 2.4 Fachlogik-/Zeichen-Module — `geometry/` (30 Dateien, **primäre Wiederverwendungsquelle**)
Reine Funktionen, kein React/Konva/three, viele DIN-/Norm-belegt und getestet:
`wallGeometry` (Wandbänder/Gehrung, Azimut), `bemassung`+`masskette` (Maßketten), `editierGeometrie` (Move/Spiegeln/Bbox), `fangKern` (Endpunkt/Ortho/Raster/Wandfangpunkte), `treppe2D/treppe3D/treppeObjekt/treppenTypen/treppeSvg/treppenBerechnung` (Treppe DIN 18065), `oeffnungsTypen/oeffnungsBauarten` (Fenster/Tür-Kataloge), `fensterProdukt` (Uw ISO 10077-1, RC EN 1627, Preis), `dachGeometrie/dachVorlage/dachMesh/sparrenBerechnung` (Dach/Eurocode), `roomDetection`, `geschossVorlage` (Geschoss duplizieren), `wandaufbau` (U-Wert DIN EN ISO 6946), `heizkoerperLeistung/heizkoerperTypen/heizkreisVerteiler/fbhAuslegung` (Heizung), `abwassergefaelle`, `kuecheArbeitsdreieck`, `pvBelegung`, `configuratorPackage`, `integrationAbgleich`.
→ **Die „Fachwahrheit" für Kontext-Werkzeuge/Panels existiert bereits als Rechenkern.** Das Dashboard sammelt Eingaben und ruft diese — es baut keine zweite Rechenlogik (deckt §16/§40 „keine konkurrierende Berechnung").

### 2.5 Renderer
`renderers/three-d/{szene,platzierung,segmentierung,adapter,dachMesh}.ts` (3D). **2D-Renderer ist inline in `HausplanerApp.tsx`** (Konva-JSX). Beide lesen den Store; kein zweiter Datenbestand.

### 2.6 Icons
Inline-SVG in `HausplanerApp.tsx` (`werkzeugIcon`, `fachIcon` — Feather-Stil, 24er-Viewbox, `currentColor`) + **Premium-SVG-Bauart-Raster** (`oeffnungsBauarten.ts`, `treppenBauarten.ts`) via `import.meta.url`. → Ansatz für §27 (Toolbar- vs. Katalog-Icons) ist da, aber ohne zentrales Icon-System-Dokument/Registry.

### 2.7 Backend / Rechte / Sperre
`app/Http/Controllers/Hausplaner/HausplanerController.php`, `SpeichereHausplanerDokumentRequest.php`, `App\Domain\Hausplaner\Validation\SceneDocumentValidator.php`; Routen `permission:Hausplaner,read|update`; 409-Konflikt im Store; „von Yama Admin bearbeitet"-Banner. → Rechte/Sperre existieren serverseitig; **im UI-State nicht als Per-Objekt-Sperre/Freigabe modelliert**.

### 2.8 Tests
50 Test-Dateien / 286 Tests (Node-Runner + Zod/Schema-Check). Deckung: Geometrie/Fachkerne, Command-Verhalten, Schema-Additivität. **Keine** UI-/Aktivierungs-/Shortcut-/A11y-Tests (die vom Prompt §39 geforderten Kategorien fehlen weitgehend).

---

## 3. Deckungs-Matrix: Master-Prompt ↔ Ist

| Prompt-Anforderung | Ist-Status | Beleg |
|---|---|---|
| Command-System + Undo/Redo (§24) | **vorhanden** | `store` + `history.ts` + `applyCommand.ts` |
| Eine Modell-Wahrheit, keine UI besitzt Modell (§23/§40) | **vorhanden** | Store-only, Renderer nur lesend |
| Fachrechenkerne (keine 2. Berechnung, §16/§40) | **vorhanden** | 30 `geometry/*`-Module |
| Modus geführt/Experte (§4) | **teilweise** | `HausplanerStudio` (start/guided/expert), gleiche IDs/Revision |
| Tool-Registry-Modell (§22) | **Teil-Keim** | `werkzeugRegistry.ts`, aber ohne UI + ohne activationRules |
| Aktivierungs-/Capability-Engine (§21) | **fehlt** | Werkzeuge immer aktiv; kein „disabled + Grund" |
| Kontextabhängige Sichtbarkeit (§2/§3) | **fehlt/rudimentär** | Kontext-Optionen teils (Geschoss, Fenstertyp) im Kopf; keine Werkzeug-Options-Leiste je Tool |
| Workspaces (§9) | **fehlt** | FACHPLANER-Navi sind reine `<div title="geplant">`-Platzhalter |
| Ansichts-gebundene Werkzeuge (§18) | **rudimentär** | 2D/Split/3D vorhanden; Werkzeuge nicht view-gescoped |
| Auswahl: Mehrfach/Bereich/Typ/Isolieren (§7) | **rudimentär** | `selectedNodeIds: string[]` existiert, UI wählt aber nur 1; kein Rubber-Band/Filter |
| Transform-Werkzeuge (§8) | **teilweise** | Move/Spiegeln/Duplizieren/Löschen für Wand/Öffnung/Treppe; kein Rotate/Scale/Array, kein Achsen-Constraint |
| Kontextleiste je Werkzeug (§19) | **fehlt** (verstreut) | einzelne Optionen im Kopf, keine dedizierte Leiste |
| Eigenschaftenpanel + Tabs + Mehrfach (§20) | **teilweise** | rechtes Panel je Selektion; **nur `length===1`**, keine Tabs/Beziehungen/Prüfungen/Historie |
| Projektbrowser (§32) | **fehlt** | keine Szenen-Baum-UI (Geschosse/Räume/Systeme/Ansichten) |
| Command-Palette (§30) | **fehlt** | — |
| Command-Zustandsmaschine mehrschrittig (§25) | **implizit** | Mehrschritt über lokale `useState` (`wandStart`, `treppeStart`) statt `ActiveCommandState` |
| Fang/Zeichenhilfen (§26) | **teilweise** | `fangKern` (Endpunkt/Ortho/Raster/Wandpunkt) rein vorhanden; UI-Schnellumschalter „Fang" existiert |
| Prüfungscenter (§34) | **fehlt** (Guards da) | `applyCommand` wirft `CommandAbgelehnt`, „geklemmt"-Marker im 2D; kein Issue-Panel |
| Icon-System getrennt Toolbar/Katalog (§27) | **teilweise** | Inline-SVG + Bauart-Premium-SVGs; kein zentrales System/Doc |
| Speicher/Revision an einem Ort (§31) | **vorhanden** | Studio-Kopf spiegelt `speicherStatus` + `scene.revision`; 409-Pfad |
| Rechte/Sperre/Freigabe im UI (§21) | **fehlt** | serverseitig ja, UI-State nein |
| Responsive/A11y (§35/§36) | **teilweise** | <900px Nav-Einklappen, `:focus-visible`; keine Drawer/keine A11y-Tests |
| Performance-Entkopplung (§37) | **Risiko** | `HausplanerApp` monolithisch → Toolbar-Änderung kann Canvas-Rerender auslösen |

---

## 4. Technische Schulden / konkurrierende Zustände (die vor dem Aufbau geklärt gehören)

1. **Aktives Werkzeug liegt lokal, nicht im Store.** `HausplanerApp.tsx:137` `const [werkzeug,setWerkzeug]=useState<Werkzeug>('auswahl')` + hartcodierte Union `'auswahl'|'wand'|'fenster'|'tuer'|'dach'|'treppe'` (Z. 32) + if/else-Keyboard (Z. 469–498). → Weder Shell, Kontextleiste noch Command-Palette können den Werkzeugzustand lesen/setzen. **Muss in einen geteilten UI-State.**
2. **Zwei „Modus"-Begriffe.** `store.modus` = **Ansicht** (2d/split/3d); `HausplanerStudio` lokaler `modus` = **Bedienmodus** (start/guided/expert). Gleicher Name, verschiedene Ebenen, verschiedene Besitzer. → Umbenennen/trennen (`activeView` vs. `studioMode`) bevor weitere Leser dazukommen.
3. **werkzeugRegistry ungenutzt.** Foundation existiert, aber Toolbar/Keyboard/Platzierung sind daneben hartcodiert. → Doppelte Werkzeug-Wahrheit droht; erst Registry als führend etablieren, dann UI datengetrieben.
4. **Eigenschaftenpanel nur 1-Selektion.** `selectedNodeIds` ist ein Array, das Panel behandelt aber nur genau eine Auswahl (`length===1`). Mehrfachauswahl (§7/§20 „gemeinsame Werte") ist nicht abbildbar.
5. **Mehrschritt-Commands ohne Zustandsmaschine.** Zeichnen läuft über lokale Punkte-States; kein `ActiveCommandState` (awaiting-first-point …) → Command-Leiste (§25) und Abbruch-/Preview-Konsistenz sind nicht zentral.
6. **Monolith `HausplanerApp.tsx`.** ~900 Z. Ein Component-Baum für Toolbar+Canvas+Panel+Status → Re-Render-Kopplung (Performance-Risiko §37) und schwer erweiterbar für 20 Workspaces.
7. **Schema-Doppelquelle.** Zod ↔ JSON-Schema per Generator; die v9-Regel-Verletzung (produkt.typ) zeigt: **jede additive Feldänderung MUSS `npm run schema:hausplaner` durchlaufen** — sonst 422. (Offener Blocker, s. Kopf.)
8. **FACHPLANER-Navi = Attrappe.** `<div title="geplant">`-Einträge ohne Verhalten. Ehrlich beschriftet, aber kein Workspace-Gerüst dahinter.

---

## 5. Fehlende Grundlagen (Foundations, die zuerst gebaut werden müssen)

- **F1 · Geteilter UI-State** (`PlannerUiState` §23): `activeWorkspace, activeView, activeToolId, selectionIds, hoveredObjectId, focusedViewportId, openPanels, pinnedPanels, snapSettings, visibilityState, commandState, saveState`. Getrennt von Canvas-/Fach-State. **Ohne diesen State ist nichts kontextabhängig steuerbar.**
- **F2 · Tool-Registry (voll)** nach §22 auf Basis von `werkzeugRegistry.ts` erweitert um `supportedWorkspaces/Views/SelectionTypes`, `min/maxSelectionCount`, `requiredPermissions/Capabilities`, `activationRules`, `shortcut`, `optionsSchema`, `commandFactory`, `disabledReasonResolver`.
- **F3 · Activation-Engine** (§21): pure Funktion `(tool, uiState, selection, rights) → {enabled, reason}`. Speist Toolbar-Enable/Disable + Tooltip-Grund. Testbar ohne DOM.
- **F4 · Workspace-System** (§9): Workspace = Werkzeuggruppen + Standardansicht + Sichtbarkeitsvoreinstellung + Prüfungen; datengetrieben, additiv (bestehende FACHPLANER-Labels wiederverwenden).
- **F5 · Command-Zustandsmaschine** (§25): `ActiveCommandState` (idle→awaiting-*→preview→valid/invalid→committing→completed) ersetzt lokale Zeichenpunkte; Drag = **eine** gruppierte Transaktion.
- **F6 · Selektionsmodell** (§7): Rubber-Band/Typ-/Eigenschafts-Filter, Isolieren/Sperren/Ausblenden; Mehrfach-Selektion im Panel mit „gemeinsame Werte".

---

## 6. Architekturvorschlag (Zielbild, additiv auf dem Bestand)

```
┌───────────────────────────────────────────────────────────┐
│ UI-State-Layer (neu):  usePlannerUiStore  (F1)            │  ← besitzt KEIN Modell
│   activeWorkspace/View/Tool · selection · panels · snap    │
├───────────────────────────────────────────────────────────┤
│ Tool-Registry (F2, aus werkzeugRegistry) + Activation (F3) │  ← rein, testbar
│   ToolDefinition[] · activationRules · disabledReason      │
├───────────────────────────────────────────────────────────┤
│ Präsentation (zerlegt aus HausplanerApp):                  │
│   <Toolbar/> <ContextOptionsBar/> <PropertiesPanel/>       │  ← lesen UI-State + Registry
│   <ProjectBrowser/> <StatusBar/> <CommandPalette/>         │
│   <Viewport2D(Konva)/> <Viewport3D(three)/>                │  ← lesen Modell-Store (read)
├───────────────────────────────────────────────────────────┤
│ Modell-Store (BESTAND, unverändert): executeCommand/undo   │  ← einzige Modell-Wahrheit
│ Geometry/Fachkerne (BESTAND): DIN/Uw/PV/TGA/Treppe …       │  ← Kontext-Rechnung
│ Backend (BESTAND): Validator/Rechte/409                    │
└───────────────────────────────────────────────────────────┘
```

Leitplanken (aus CLAUDE.md/Bauordnung/ux-design):
- **Reuse vor Neubau:** Store/Commands/Geometry/Studio-Shell/Icons/Kataloge bleiben führend; das Dashboard orchestriert sie.
- **Keine zweite Modell- oder Rechenwahrheit.** UI-State ≠ Modell-State ≠ Fachrechnung (drei getrennte Besitzer).
- **Additiv an DB/Schema.** Jede Feldänderung durch den Schema-Generator; PHP-Validator bleibt additiv.
- **Design-System:** ein Token-System (studioUi/ux-design), Marke nur als Akzent, Status nie nur Farbe.
- **React-Scope:** bleibt im gekapselten Hausplaner-Insel-Bundle (kein React-Wildwuchs im CRM).

---

## 7. Risiken

| # | Risiko | Wirkung | Gegenmaßnahme |
|---|---|---|---|
| R1 | **Offener Schema-Blocker (produkt.typ)** | Save 422, Gate rot, Suite/Build blockiert | **Vor UI-1 schließen** (Generator: Schema-Nachzug), Re-Abnahme |
| R2 | Zweite Werkzeug-Wahrheit (Registry vs. hartcodiert) | Divergenz, Bugs | Registry ZUERST führend, dann UI umstellen; alte Union löschen |
| R3 | Monolith-Zerlegung bricht 2D/3D/Store-Kopplung | Regression Zeichnen/Selektion/Save | Charakterisierungstests VOR Extraktion; kleine Slices; Store-API unverändert |
| R4 | UI-State ↔ Modell-State vermischen | UI wird Modellbesitzer (verletzt §40) | Klarer Vertrag: UI-State liest Modell nur über Store-Selektoren |
| R5 | Performance: Toolbar-Rerender rendert Canvas neu | Ruckeln bei großen Szenen | selektive Store-Abos, Renderer vom UI-State entkoppeln, Messung am Referenz-EFH |
| R6 | Scope-Explosion (20 Workspaces, 300 Werkzeuge) | Nie fertig | Registry macht Werkzeuge zu Daten; Workspaces schrittweise; MVP = Architektur-Workspace |
| R7 | A11y/Responsive nachrangig behandelt | rechtsrelevante Lücke, Baustellen-Tauglichkeit | A11y-/Responsive-Slice als eigene Phase mit Tests (§39) |
| R8 | Parallele Schreiber (One-Writer verletzt) | Merge-/Bundle-Kollisionen (in der v9-Welle mehrfach gesehen) | Ein benannter Schreiber je Strang; Bundle-Commit diszipliniert |

---

## 8. Vorgeschlagene Umsetzungsslices (jede eigener Planner→Generator→Evaluator-Zyklus, eigener Startblock + Yama-Freigabe)

> Reihenfolge folgt Master-Prompt §41, an den Bestand angepasst. **S0 ist Pflicht-Vorbedingung.**

- **S0 · Blocker schließen (nicht Teil des Dashboards):** Schema-Nachzug `produkt.typ` (`npm run schema:hausplaner` + Commit + Bundle). Abnahme: schema:check Exit 0, produkt.typ speicherbar (kein 422), Suite/Build über Standard-Kommandos.
- **UI-1 · Ist-Inventar (dieses Dokument)** + Tool-Inventar-Tabelle vervollständigen. Ergebnis: `tool-registry.md` (Entwurf), `ui-state-model.md`.
- **UI-2 · F1 UI-State + F2 Tool-Registry + F3 Activation-Engine** (rein, ohne UI-Umbau). Aktives Werkzeug wandert store-seitig; `werkzeugRegistry` wird führend. Abnahme: Unit-Tests Aktivierungsregeln/Shortcuts/disabled-Gründe; keine sichtbare Regression.
- **UI-3 · HausplanerApp-Zerlegung + globale Toolbar datengetrieben** aus der Registry (Icons/Shortcuts/Enable-Grund). Abnahme: Zeichnen/Selektion/Save unverändert (Charakterisierung), 0 Pageerrors.
- **UI-4 · Kontext-Options-Leiste (§19)** je aktivem Werkzeug (optionsSchema). Abnahme: Wand/Fenster/Verschieben zeigen ihre Optionen; Werte fließen in Commands.
- **UI-5 · Selektionsmodell + Eigenschaftenpanel mit Tabs + Mehrfach (§7/§20).** Abnahme: 3 Fenster → gemeinsame Werte anwendbar; Isolieren/Sperren.
- **UI-6 · Workspace-System (§9)** mit Architektur-Workspace als erstem echten (bestehende Fach-Navi anbinden). Abnahme: Workspacewechsel ändert Werkzeuge/Ansicht/Sichtbarkeit ohne Modelländerung.
- **UI-7 · Command-Zustandsmaschine (§25) + Command-Leiste.** Abnahme: mehrschrittiges Wand-/Treppe-Zeichnen als Zustandsmaschine, Drag = 1 Undo.
- **UI-8 · Projektbrowser (§32) + Sichtbarkeit/Layer (§33).**
- **UI-9 · Command-Palette + konfigurierbare Shortcuts (§30/§29).**
- **UI-10 · Prüfungscenter (§34)** auf Basis der `CommandAbgelehnt`-Guards + Fachkern-Prüfungen.
- **UI-11 · Responsive/Drawer + A11y-Härtung + Tests (§35/§36/§39).**
- **UI-12 · Performance-Budget + E2E-Abnahme (§37/§39).**

---

## 9. Abnahmekriterien (für die Gesamtinitiative, je Slice zu schärfen)

Ein Slice ist grün nur, wenn (messbar, am echten Rendern/Bundle):
1. Der **aktive Workspace/Ansicht/Werkzeug** ist im UI-State ablesbar und im Kopf sichtbar.
2. **Werkzeug-Sichtbarkeit/-Aktivierung** stammt aus Registry+Activation-Engine, nicht aus verstreutem `if`; jedes deaktivierte Werkzeug liefert einen **Grund** (Tooltip, nicht nur Farbe).
3. **Keine UI-Komponente** hält Modell-Zustand; alle Mutationen über Commands; **Undo/Redo vollständig**; Drag = eine Transaktion.
4. **Keine zweite Fachrechnung** — Kontextwerte kommen aus `geometry/*`.
5. **Additiv:** DB/Schema unverändert-kompatibel; `schema:hausplaner:check` Exit 0; PHP-Validator akzeptiert die neuen Felder (kein 422).
6. **Reuse belegt:** je Slice eine Reuse-Matrix (welcher Bestand genutzt, was neu, Begründung R5).
7. **Wächter:** `tsc` Exit 0; `test:hausplaner` grün und Anzahl ≥ vorher; **A11y** (Kontrast AA, Fokus sichtbar, Tastatur, Status nicht nur Farbe); **Performance** (Toolwechsel ohne Canvas-Vollrender, Messung am Referenz-EFH); 0 Planer-Pageerrors.
8. **Regression:** bestehendes Zeichnen/Konfigurieren/Speichern/409 unverändert funktionsfähig.

---

## 10. Pflichtdokumente (Master-Prompt §42) — Status

Noch zu erstellen (in den jeweiligen Slices, nicht jetzt): `tool-registry.md`, `workspace-system.md`, `tool-activation-rules.md`, `command-system.md`, `keyboard-shortcuts.md`, `ui-state-model.md`, `accessibility-tooling.md`, `tool-icon-guidelines.md`, `frontend-testing-strategy.md`, `performance-budget.md`. Dieses Dokument (`tool-dashboard-current-state.md`) ist die read-only-Grundlage dafür.

---

## STOP — Planner-Pflichtstopp

Bestandsaufnahme abgeschlossen. **Keine Implementierung, keine Route/Migration/Installation, kein Commit, kein Push.**
Empfehlung an Yama: **erst S0 (Schema-Blocker) freigeben+schließen**, dann UI-2 (UI-State + Tool-Registry + Activation-Engine) als ersten Aufbau-Slice mit eigenem Startblock. Ballbesitz zurück an Yama.
