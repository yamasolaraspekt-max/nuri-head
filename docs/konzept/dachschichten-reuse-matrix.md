# KONZEPT — Reuse-/Extraktions-Matrix: erster vertikaler Schnitt Dachschichten

```yaml
zustand: KONZEPT (Planner-Output, read-only erstellt 21.08.2026 durch planner-architect; kein Code verändert)
gehoert_zu: docs/konzept/dachschichten-modell-zielkonzept.md
bau: NACH TESTBEREIT (Welle 2 Produkt) — dann Auftrag über planner-slice-orchestrator
entscheidungen_dirigent_21_08: (1) Ansichtsprofil Stufe 1 = dokumentgebunden, projektweit geteilt, NICHT undo-faehig (Ansicht ist kein Inhalt; je-Nutzer-Profile spaeter) — Yama kann widersprechen. (2) Schichten-Sichtbarkeit in Stufe 1 nur 3D; „2D/3D konsistent" gilt fuer roof.visible als Ganzes (Scope-Grenze, Kriterium sonst unerfuellbar).
nicht_ziele_pflicht: dachformVorlagen.ts + dachformVorlagen.test.ts:561 (deckungsneutral, W-26) unangetastet; holzBauteile/holzMengen NICHT anschliessen (Herkunftsvermerk RoofEngine); sparrenBerechnung bleibt unverdrahtet; Playground nur lesen, keine Zahl 1:1 (m vs mm)
```

# Reuse-/Extraktions-Matrix — RoofNode-Schichten → Commands → Speichern → Ebenenpanel → 3D-Explosion

Planungsaufgabe, read-only. Kein Code verändert, kein Commit, kein Push. Grundlage: `docs/konzept/dachschichten-modell-zielkonzept.md`, Skills `planner-architecture`/`planner-slice-orchestrator`/`ticket-code-reuse`.

## Schichten-Zuordnung (planner-architecture)

- **Gemeinsame CRM-Shell / Designsystem**: EigenschaftenPanel-Styleguide-Bausteine `panelLabel`/`panelInput`/`hp-ep-titel`/`hp-ep-untertitel` (`resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx:246-264`) — R1, kein zweites Designsystem.
- **Planner-Fachmodul / BuildingDocument**: `SceneDocument`/`RoofNode` (`domain/scene.types.ts:315-335`), Zod-Validierung (`domain/validation.ts:240-260`), Commands (`domain/commands.types.ts`, `commands/applyCommand.ts`) — hier wird additiv erweitert.
- **2D/3D-Renderer**: `renderers/three-d/dachMesh.ts` (Geometrie-SSOT), `renderers/three-d/szene.ts` (Licht/Material/Draw) — neuer Schichten-Renderer setzt AUF dieser SSOT auf, ersetzt sie nicht.
- **Integrationsschicht**: keine neuen Adapter nötig für diesen Slice — Schichten bleiben reines CAD-Modell, keine Projektion in Heizlast/PHP in dieser Ausbaustufe (Tragwerk-Rechnung `sparrenBerechnung.ts` bleibt unverdrahtet, s. u.).
- **Fachlogik-Schutzgrenze W-26**: `docs/rollenkette/werkbank/02-WERKZEUGE/W-26-dachschichten/` — „deckungsneutral" ist eine bewusste, achtfach dokumentierte Entscheidung (`geometry/dachformVorlagen.ts:113-114`) mit Wächtertest (`__tests__/dachformVorlagen.test.ts:561`). Das neue `schichten`-Datenfeld darf diese Grenze NICHT berühren (s. Risiken).

## (1) Reuse-Matrix

| Baustein | Ticket-Kandidat | Pfad:Zeile | Klasse | Begründung |
|---|---|---|---|---|
| Datenmodell-Muster für Schichten | `WallNode.schichten`, `CeilingNode.schichten` | `domain/scene.types.ts:133`, `:357` | **R1/R2** | Feldform `{materialId?, dickeMm}` bereits etabliert (AUF-76, "feldgleich"-Kommentar `:115-116`) — RoofNode bekommt additiv dasselbe Muster, erweitert um `typ` |
| Zod-Schema RoofNode | `roofNodeSchema` | `domain/validation.ts:240-260` | **R2** | additiv erweitern wie `aufbauten`/`anbau` bereits vorgemacht (`:252-255`) — kein Versionssprung nötig, Muster bereits zweimal bewiesen |
| schemaVersion/Migration | `migriereSzene`, `sceneDocumentSchema` | `domain/validation.ts:276-297`, `:308-354` | **R1** | bleibt bei `literal(3)`; additive optionale Felder brauchen laut Bestandsmuster (`ceilings` optional `:294`) keine neue Version |
| JSON-Schema (Doku/Contract) | `scene-document-v2.schema.json` | `domain/scene-document-v2.schema.json` | **R2, Nacharbeit nötig** | Datei ist bereits jetzt fehlbenannt (Titel „v2", Inhalt `const 3`, Zeile 18) — muss bei jedem Feldzusatz manuell nachgezogen werden; kein Generierungsscript im Baum gefunden (ungemessen, ob eines existiert) |
| Command-Infrastruktur (Undo/Redo/409) | `UPDATE_ROOF`, `ADD_ROOF_AUFBAU`/`UPDATE_ROOF_AUFBAU`/`REMOVE_ROOF_AUFBAU` | `domain/commands.types.ts:19-26`, `commands/applyCommand.ts:261-274`, `:332-364` | **R1 (Muster) / R2 (neue Varianten)** | granulares Listen-Command-Muster für `roof.aufbauten` ist direktes Vorbild für `roof.schichten`; Immer-Drafts liefern Undo/Redo automatisch |
| mm-Ganzzahl-Prüfung | `pruefeGanzzahlig`, `pruefeAufbauGanzzahlig` | `commands/applyCommand.ts:28-32`, `:88-90` | **R1** | Muster direkt kopierbar auf `pruefeSchichtGanzzahlig(dickeMm)` |
| Dachflächen-Geometrie (SSOT) | `dachRoh`, `dachMeshWelt`, `dachflaechen` | `renderers/three-d/dachMesh.ts:203-344` | **R1** | einzige Geometriequelle für Flächen/Normalen — jede Schichten-Explosion MUSS hierauf aufsetzen, nicht neu rechnen (SSOT-Kommentar `:203-207`) |
| Dach-Rendering (Material/Mesh) | `rendereDachMesh` | `renderers/three-d/szene.ts:569-599` | **R5 (neuer Renderer nötig)** | heute EIN `MeshStandardMaterial` pro Dach, kein Gruppen-/Schichtenbaum — reines Ersetzen reicht nicht |
| Sub-Mesh-/Marker-Präzedenz | `rendereAufbauKoerper`/`aufbauMarker` | `renderers/three-d/szene.ts:540-563` | **R1 (Muster)** | Vorbild für „Schicht nicht darstellbar → Marker statt stiller Lücke" |
| Sichtbarkeits-Mechanik | `visible`-Filter | `szene.ts:351,459,479,513`; `Buehne.tsx:298` (2D, nur Dächer); `ableitungen.ts:52` (2D-Nodes, kein `visible`) | **R1 mit Vorbehalt** | Roof-Ebene ist in beiden Ansichten bereits konsistent verdrahtet; Node-Ebene in 2D nicht (Befund A-1, `docs/FACHPRUEFUNG-DREI-LINSEN.md:76-97`) — für Schichten NICHT dieselbe Lücke neu bauen |
| Ebenenpanel-UI (Listen-Editor) | — | `EigenschaftenPanel.tsx:243-318` (nur Skalarfelder) | **R5, echt neu** | KEIN Listen-Editor im Bestand: `ADD_ROOF_AUFBAU`/`UPDATE_ROOF_AUFBAU`/`REMOVE_ROOF_AUFBAU` haben laut Grep nur 4 Fundstellen gesamt (`werkzeugLandkarte.ts`, `__tests__/dachAufbauten.test.ts`, `commands.types.ts`, `applyCommand.ts`) — **keine davon in `app/`**; `schichten` (Wand/Decke) hat in `app/` genau 1 Treffer (`werkzeugLandkarte.ts`, Registrierungstext, keine editierbare Liste) |
| Tragwerk-Mengenrechnung | `sparrenBerechnung.ts` | `geometry/sparrenBerechnung.ts:52-70` | **R1, aber unverdrahtet** | reine Rechenfunktion, Konsument ist `app/dashboard/enginePanels.ts:24,179-227,428-450` mit MANUELLEN Eingaben (Default 10m/38°), NICHT an `RoofNode.neigungGrad`/Grundriss gekoppelt — Verdrahtung ist eigener R2-Schritt, nicht Teil der Ausbaustufe 1 |
| Holzlisten-Aggregation | `holzBauteile.ts`, `holzMengen.ts` | `geometry/holzBauteile.ts:24-37`, `geometry/holzMengen.ts:25-38` | **NICHT verwenden (Herkunftsvermerk)** | beide gebaut gegen `class RoofEngine`, die nur in `docs/planner/pv-belegung-referenz/DachplanerProPage.tsx:369` (Referenzkopie, außerhalb Produktivbaum) existiert — 0 Definitionen in `resources/`/`app/`; Vermerk verlangt ausdrücklich Entkräftung vor Anschluss |
| Playground: Layer-Gruppen-Steuerung | `syncLayers()` | Playground `src/pages/energie/DachplanerProPage.tsx:2171-2185` | **R3, Idee extrahieren, Code nicht** | Mapping `LayerConfig.id → THREE.Group.visible` ist eine gute Idee (15 feste Gruppen), aber `layers` ist reiner lokaler `useState` (`:2255`) — Extraktion = Konzept übernehmen (Aufwand gering-mittel), nicht Code kopieren |
| Playground: Transparenz | `globalOpacity` auf `tileRed/tileDark/gravel/metal` | Playground `:2182-2184`, `:3628` | **R3 mit Einschränkung** | KEINE echte Pro-Schicht-Transparenz im Playground, nur EINE globale Deckungs-Transparenz auf 4 geteilten Materialien — als Vorbild für "Auge" (binär) tauglich, für "Transparenz-Regler je Schicht" nicht |
| Playground: Explosion | `layerSpread` in `BuildingParams`, verwendet in `buildRoofFace` | Playground `:93-102`, `:942`, `:959`, `:1272/1284/1365/1496/1509/1549` | **R3, Idee extrahieren, Code nicht** | Offset-Prinzip (Schicht-Y-Position += Vielfaches von `layerSpread`) ist extrahierbar, aber von Hand in eine ~3700-Zeilen-Funktion verwoben, in Metern statt mm — Neuformulierung als reine Funktion gegen die Ticket-SSOT (`dachMesh.ts`) nötig, mittlerer Aufwand |
| Playground: Persistenz des Ansichtszustands | `RoofSlice` | Playground `src/stores/roofTypes.ts:90-96` | **Negativ-Befund bestätigt** | `RoofSlice` enthält NUR `build/additionalRoofs/cover/selectedTile/selectedCovering` — `layers` (Sichtbarkeit/Name/Transparenz) ist NICHT enthalten, bestätigt Konzeptaussage „nach Neuladen nicht wiederherstellbar" |

## (2) Datenmodell-Vorschlag (additiv)

```ts
// domain/scene.types.ts — additiv, analog CeilingNode.schichten (:357), erweitert um Typ
export type DachSchichtTyp =
  | 'tragwerk' | 'schalung' | 'unterspannbahn' | 'daemmung' | 'dampfbremse'
  | 'konterlattung' | 'traglattung' | 'deckung' | 'pv_montage';

export interface RoofSchicht {
  id: string;
  typ: DachSchichtTyp;
  dickeMm: number;        // mm-Ganzzahl (Muster pruefeGanzzahlig)
  materialId?: string;    // optional, wie CeilingNode.schichten
}
```
`RoofNode.schichten?: RoofSchicht[]` — additiv/optional wie `aufbauten`/`anbau` (`scene.types.ts:329-334`), kein `schemaVersion`-Sprung nötig (Präzedenz zweifach belegt). Reihenfolge = Array-Index (konsistent mit Bestandsmuster), aber mit Gegenteil-Kommentar zu `scene.types.ts:129-131` — bei Dach-Schichten TRÄGT die Reihenfolge fachliche Bedeutung (Tragwerk→Deckung), das muss explizit dokumentiert werden, um nicht denselben Vorbehalt wie bei Wänden zu erben.

**Ansichtsprofil (Auge/Solo/Transparenz/Explosion) — Vorschlag zur Bestätigung, keine Yama-Entscheidung:** EIGENES optionales Top-Level-Feld auf `SceneDocument`, nicht auf `RoofNode`, additiv wie `ceilings` (`validation.ts:294`):
```ts
SceneDocument.ansicht?: {
  dachSchichten?: Record<string /* roofId */, {
    ausgeblendetTyp?: DachSchichtTyp[]; soloTyp?: DachSchichtTyp; explosionMm?: number;
  }>;
};
```
**Begründung gegen den K-2/A-1-Befund**: Der Playground-Mangel ist genau, dass Sichtbarkeit/Transparenz NUR lokaler React-Zustand ist (Konzept Zeile 31-33) und nach Neuladen verschwindet — das ist im Zielkonzept als „Schwachpunkt" benannt, nicht als Vorbild. Rein lokaler State im Ticket würde denselben Mangel replizieren und stünde im Widerspruch zur bestehenden Ticket-Stärke „Speichern/Undo" (Konzept Zeile 22). Ein getrenntes, aber DOKUMENT-gebundenes Feld erfüllt „Ansichtszustand getrennt von Konstruktion" (Zielkonzept Regel 4) strukturell, ohne den Persistenz-Rückschritt zu erben. **Offene Fachfrage, die ich nicht selbst entscheide**: ob Sichtbarkeit projektweit geteilt (ein Betrachter blendet aus, alle sehen es so) oder je Nutzer individuell sein soll — das gehört ins Bauauftrag-Gate.

## (3) Command-Schnitt

Neu, nach Muster `ADD_ROOF_AUFBAU`/`UPDATE_ROOF_AUFBAU`/`REMOVE_ROOF_AUFBAU` (`commands.types.ts:24-26`, `applyCommand.ts:332-364`):
- `ADD_ROOF_SCHICHT { roofId, schicht: RoofSchicht }`
- `UPDATE_ROOF_SCHICHT { roofId, schichtId, changes: Record<string, unknown> }`
- `REMOVE_ROOF_SCHICHT { roofId, schichtId }`
- ggf. `REORDER_ROOF_SCHICHTEN { roofId, schichtIds: string[] }` — nur falls Drag&Drop im Ebenenpanel gefordert ist

**Bewusst NICHT** über das bestehende `UPDATE_ROOF` (`applyCommand.ts:261-274`) zweckentfremden — das würde das ganze `schichten`-Array pro Änderung ersetzen und die Undo-Granularität von „eine Schicht" auf „ganzes Array" vergröbern. `AblehnungsGrund` um `'schicht_unbekannt'` erweitern (Muster `'aufbau_unbekannt'`, `commands.types.ts:62`). Neue `pruefeSchichtGanzzahlig(dickeMm)` analog `pruefeAufbauGanzzahlig` (`:88-90`).

Für das Ansichtsprofil: falls dokumentgebunden (empfohlene Variante), offene Designfrage, ob es undo-fähig sein soll (Strg+Z, das ein Auge zurückschaltet, ist evtl. unerwünscht) — nicht selbst entschieden, nur benannt.

## (4) Integrationspunkte und Risiken

- **schemaVersion-Migration**: additiv, kein Sprung nötig für `RoofNode.schichten` (Präzedenz `aufbauten`/`anbau`). Für `SceneDocument.ansicht` als neues Top-Level-Feld gilt dieselbe Erwartung (Präzedenz `ceilings` optional), aber am echten `sceneDocumentSchema.strict()`-Verhalten (`validation.ts:276-297`) NICHT nachgetestet — muss im Bauauftrag mit Zod-Test abgesichert werden.
- **JSON-Schema-Datei**: `scene-document-v2.schema.json` ist bereits jetzt inhaltlich v3, muss nachgezogen werden; kein Generierungsscript gefunden (ungemessen, ob eines existiert).
- **Einheiten m→mm**: Playground rechnet komplett in Metern (`BuildingParams`, Range-Komponente `unit="m"`, `:3629`), Ticket zwingend mm-Ganzzahl (`applyCommand.ts:28-32`). Keine Zahl/Konstante 1:1 aus dem Playground übernehmen — jede Umrechnung einzeln prüfen (Rundungsrisiko).
- **Performance Explosion**: `dachMeshWelt()` liefert heute EIN Mesh pro Dach (`dachMesh.ts:298-310`); eine Schichten-Explosion mit z. B. 9 Schichttypen × bis zu 4 Flächen (Walm, `:268-285`) erhöht Draw-Calls je Dach deutlich. Playground begegnet dem mit 15 festen Gruppen + geteilten Materialinstanzen (`syncLayers`, `:2171-2185`) statt Material pro Mesh — dieses Muster übernehmen, um Regression zu vermeiden. Kein FPS-/Drawcall-Messwert vorhanden (Browserabnahme nicht Teil dieser Planungsaufgabe).
- **visible-Flag-Inkonsistenz 2D/3D**: bestehender Befund A-1 (`docs/FACHPRUEFUNG-DREI-LINSEN.md:76-97`) betrifft die 2D-Node-Ebene, nicht Dach-Schichten. Es gibt heute KEINE 2D-Darstellung von Dachschichten (nur Grundriss) — das Abnahmekriterium „2D/3D konsistent" aus dem Konzept (Zeile 58-60) sollte sich auf `roof.visible` als Ganzes beziehen (bereits konsistent verdrahtet), nicht auf einzelne Schichten in 2D. Diese Lesart ist eine Annahme, keine Messung — im Bauauftrag bestätigen lassen, sonst ist das Kriterium unerfüllbar.
- **„deckungsneutral"-Konflikt (W-26)**: `RoofSchicht.typ='deckung'` + `materialId` als reines Datenfeld verletzt die Bauregel nicht — verletzt wird sie erst, wenn daraus automatisch Warnungen/Ableitungen entstehen. `geometry/dachformVorlagen.ts` und `__tests__/dachformVorlagen.test.ts:561` dürfen von diesem Slice NICHT angefasst werden; das muss im Bauauftrag ausdrücklich als Nicht-Ziel stehen.
- **holzBauteile.ts/holzMengen.ts**: bleiben unangetastet, Herkunftsvermerk nicht entkräftet in diesem Slice.
- **sparrenBerechnung.ts**: bleibt unverdrahtet mit `RoofNode` — Ausbaustufe 1 verlangt keine Mengenrechnung, nur generische Schichten.
- **Rollentrennung**: dieser Bericht ist Planner-Output; Bau folgt einem separaten Generator-Auftrag erst nach `TESTBEREIT` (Konzept Zeile 5, 64-68).

## (5) Abnahmekriterien Ausbaustufe 1 (5 Schichten)

1. **Anlegen**: `ADD_ROOF_SCHICHT` erhöht `roof.schichten.length` um 1; Undo entfernt sie wieder (ein Schritt).
2. **Ändern**: `UPDATE_ROOF_SCHICHT` ändert `dickeMm`/`typ`/`materialId`; ein Bruch-mm-Wert wird mit `CommandAbgelehnt('nicht_ganzzahlig')` abgelehnt, Dokument bleibt unverändert.
3. **Ein-/Ausblenden je Schicht**: eine Schicht lässt sich unabhängig ausblenden, ohne die übrigen zu beeinflussen (kein Übersprechen).
4. **Explodieren**: Regler versetzt Schichten entlang der Flächennormale gestaffelt nach Reihenfolge; bei Regler=0 keine Restversetzung.
5. **Speichern/Laden**: Dokument mit befüllten `roof.schichten` ist nach Neuladen bytegleich (bis auf `updatedAt`/`revision`); ein Bestandsdokument ohne `schichten` lädt weiter ohne 422.
6. **Undo/Redo**: `ADD_ROOF_SCHICHT`/`UPDATE_ROOF_SCHICHT`/`REMOVE_ROOF_SCHICHT` sind einzeln undo-/redofähig.
7. **2D/3D konsistent**: das GESAMTE Dach bleibt in 2D und 3D gleich sichtbar (bestehender `roof.visible`-Mechanismus); Schichten-Sichtbarkeit ist explizit auf 3D begrenzt (Scope-Grenze, s. Risiken).
8. **Regressionsschutz „deckungsneutral"**: `dachformVorlagen.test.ts:561` bleibt grün.
9. **Governance-Tor**: `tsc` exit 0, bestehende Testsuite grün.

## Ungemessen / offen

- Ob ein Skript `scene-document-v2.schema.json` automatisch generiert — kein Treffer gefunden.
- Ob `SceneDocument.ansicht` als neues Top-Level-Feld im `sceneDocumentSchema.strict()` tatsächlich additiv ohne Versionssprung durchgeht — nur aus Präzedenz (`ceilings`) abgeleitet, nicht selbst getestet.
- Ob Sichtbarkeit/Ansichtsprofil projektweit geteilt oder je Betrachter individuell sein soll — Fachfrage, nicht von mir entschieden.
- FPS-/Drawcall-Werte einer echten Explosionsansicht — keine Browserabnahme in dieser Planungsaufgabe.
- Ob ein Produktkatalog existiert, den `RoofSchicht.materialId`/`produktId` sinnvoll referenzieren könnte — nicht geprüft.

## Relevante Pfade

- `/Users/yamanuri/Documents/ticket/docs/konzept/dachschichten-modell-zielkonzept.md`
- `/Users/yamanuri/Documents/ticket/.claude/skills/planner-architecture/SKILL.md`
- `/Users/yamanuri/Documents/ticket/.claude/skills/planner-slice-orchestrator/SKILL.md`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/domain/scene.types.ts`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/domain/validation.ts`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/domain/commands.types.ts`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/commands/applyCommand.ts`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/renderers/three-d/szene.ts`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/renderers/three-d/dachMesh.ts`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/geometry/sparrenBerechnung.ts`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/geometry/holzBauteile.ts`
- `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/geometry/holzMengen.ts`
- `/Users/yamanuri/Documents/ticket/docs/rollenkette/werkbank/02-WERKZEUGE/W-26-dachschichten/`
- `/Users/yamanuri/Documents/ticket/docs/FACHPRUEFUNG-DREI-LINSEN.md`
- `/Users/yamanuri/Documents/Playground/src/pages/energie/DachplanerProPage.tsx` (nur gelesen)
- `/Users/yamanuri/Documents/Playground/src/stores/roofTypes.ts` (nur gelesen)

**Nächster Schritt**: Yama bestätigt/entscheidet die offenen Fachfragen (Ansichtszustand geteilt vs. individuell, 2D-Scope-Grenze für Schichten) und gibt danach den Bauauftrag an den `planner-slice-orchestrator`/Generator frei — Bau erst nach `TESTBEREIT`.
