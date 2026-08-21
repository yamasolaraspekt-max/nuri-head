# KONZEPT — GP-0 Modell- und Command-Plan Bodenplatte + Höhenkette (Planner-Output, read-only)

```yaml
zustand: "KONZEPT — Planner-Output 21.08.2026 (planner-architect, read-only am Integrationscheckout gemessen); Freigabe durch Plan-Pruefer ausstehend; BAU erst nach TESTBEREIT (Abschlussmodus Phase 10)"
gehoert_zu: "docs/auftraege/folgeauftrag-golden-path-GP-0-bis-3.md (Scheibe GP-0) · docs/konzept/golden-path-bauwerksprozess.md"
kernbefund: "Die Hoehenkette besteht heute aus DREI unabhaengigen Rechnungen — deckenOberkanteMm (lebendig, nur Wand-OK), naechsteEtageElevationMm (dokumentierte 'eine Wahrheit', aber TOT: 0 Produktivaufrufe), Kopfrahmen.tsx:172 Inline (benutzt, liest Level.floorThickness statt der echten Decke); dazu traufhoeheMm eingefroren (K-2) und WallNode.height als Kopie"
fachfragen_fuer_yama_vor_gp0_bau: "1 Bezugshoehe ±0,00 = OK Fertigfussboden oder OK Bodenplatte? · 2 erdberuehrt-Standard EG? · 4 Feldname herkunft (Kollision mit GeometrieHerkunft) -> Empfehlung polygonQuelle · 5 max. eine Bodenplatte je Level oder je Gebaeude? (+ 3/6/7/8/9 siehe Abschnitt 8) — keine Eile, erst vor GP-0-Bau"
```

# Planungsergebnis — Bodenplatte + Höhenkette (Golden-Path-Slice 1)

Read-only gemessen am HEAD von `/Users/yamanuri/Documents/ticket` (Integrationscheckout). Kein Code verändert, kein Commit, kein Push. Rollenzuordnung: dieser Bericht ist Planner-Output; Freigabe durch Plan-Prüfer; Bau erst nach `TESTBEREIT` (vgl. `docs/konzept/golden-path-bauwerksprozess.md:113-117`).

---

## (1) IST-Höhenkette — gemessen, mit der "zweiten Wahrheit"

**Die Kette besteht heute aus DREI unabhängigen Rechnungen, nicht einer:**

| # | Funktion/Stelle | Formel | Aufrufer | Status |
|---|---|---|---|---|
| A | `deckenOberkanteMm(level)` — `resources/planner/hausplaner/renderers/three-d/deckenMesh.ts:10-12` | `elevation + defaultWallHeight` | Decken-Render `szene.ts:456,483`; Dach-Erzeugung `HausplanerApp.tsx:1008` (`traufhoeheMm: deckenOberkanteMm(level)`) | LEBENDIG, aber nur EINE Station der Kette (Wand-OK), nicht die ganze Kette |
| B | `naechsteEtageElevationMm(level, decke)` — `deckenMesh.ts:32-38` | `elevation + defaultWallHeight + (decke?.dickeMm ?? floorThickness)` | NUR `__tests__/decke.test.ts:91,93`; einziger Produktiv-Import `HausplanerApp.tsx:115`, dort **keine einzige Aufrufstelle** (grep bestätigt: 0 Treffer für `naechsteEtageElevationMm(` außer Deklaration/Import/Tests) | TOT — die "richtige" Etagenkette existiert, wird aber nicht benutzt |
| C | `Kopfrahmen.tsx:172` (Geschoss-anlegen-Handler) | `oben.elevation + oben.defaultWallHeight + oben.floorThickness` | einziger tatsächlich benutzter Weg zur nächsten Geschoss-Elevation (`onAnlegen`) | LEBENDIG, aber **liest `Level.floorThickness` (Vorgabewert), nicht `CeilingNode.dickeMm` der tatsächlichen Decke** — weicht von B ab, sobald jemand die reale Decke per `UPDATE_CEILING` ändert |

**Zweite Wahrheit Nr. 1 — B vs. C**: B ist die dokumentierte "eine Wahrheit" (`deckenMesh.ts:27-30`: *"Etagen-Stapel (eine Wahrheit)"*), C ist der tatsächlich verwendete Duplikat-Code, der eine andere Quelle (Level-Vorgabe statt echter Decke) liest. Der Kommentar in B behauptet Exklusivität, die im Code nicht eingelöst ist.

**Zweite Wahrheit Nr. 2 — `RoofNode.traufhoeheMm` friert ein (bestätigt K-2)**: gesetzt einmalig bei `ADD_ROOF` aus `deckenOberkanteMm(level)` (`HausplanerApp.tsx:1008`). Danach ausschließlich manuell änderbar über `UPDATE_ROOF` (`EigenschaftenPanel.tsx:104-105`). Grep über `app/` bestätigt: **keine** Stelle löst bei `UPDATE_LEVEL` (`applyCommand.ts:380-394`) ein Nachziehen von `traufhoeheMm` aus. Ändert sich `defaultWallHeight` nachträglich, bleibt das Dach auf der alten Höhe stehen — still, ohne Warnung.

**Zweite Wahrheit Nr. 3 — `WallNode.height` ist eine eingefrorene Kopie**: bei Wanderzeugung `height: level.defaultWallHeight` (`HausplanerApp.tsx:939`), gerendert über das eigene Feld `wand.height` (`renderers/three-d/segmentierung.ts:60`), **nicht** über `level.defaultWallHeight` zur Laufzeit. Das ist vermutlich gewollt (individuelle Wandhöhe editierbar über `UPDATE_NODE`), bedeutet aber: die Decke sitzt bei `deckenOberkanteMm(level)` = Level-Vorgabe, nicht bei der tatsächlichen (ggf. individuell geänderten) Wandhöhe. Ungemessen, ob eine Wand je individuell auf eine andere Höhe gebracht wurde (kein UI-Feld dafür in `EigenschaftenPanel.tsx` gefunden — nicht abschließend geprüft).

**Bodenplatte**: nicht vorhanden. `Level.elevation` (`scene.types.ts:66`) ist die einzige Bezugsgröße "±0" — ob sie OK-Fertigfußboden oder OK-Bodenplatte meint, ist **nicht im Code entschieden** (offene Fachfrage, siehe (8)).

**2D-Befund (zusätzlich, für Abnahmekriterium 2D/3D relevant)**: `Buehne.tsx` zeichnet Dächer (`:298`), **keine** Decken — Grep über `app/` liefert für `CeilingNode`/`ceilings` nur `app/dashboard/fahrschritte.ts:87,94` (reine Zählung für den Fortschrittsbalken), kein Rendering. Die Decke existiert 2D nicht sichtbar, nur 3D (`szene.ts:479-506`, `userData.nodeId` gesetzt). Kein Eigenschaften-Panel für Decken (Grep `selectedCeiling`/`CeilingNode` in `app/` → 0 Treffer außer Fahrschritte) — Decke ist heute nur über Werkzeug anlegbar, nicht editierbar/löschbar via UI.

---

## (2) Modellplan additiv — `FoundationSlabNode`

### Typ (analog `CeilingNode`, `scene.types.ts:342-358`)

```ts
// domain/scene.types.ts — additiv, NEBEN nodes[]/roofs[]/ceilings[] (Muster :46-54)
export interface FoundationSlabDurchbruch {                 // Muster CeilingOeffnung :338
  polygon: Array<{ x: number; y: number }>;
}

export interface FoundationSlabNode extends BaseNode, MitHerkunft {   // MitHerkunft :307-313, Pflicht wie Decke/Dach
  type: 'foundation_slab';
  polygon: Array<{ x: number; y: number }>;      // Muster CeilingNode.polygon :351
  dickeMm: number;                                // Muster CeilingNode.dickeMm :353
  oberkanteMm: number;                            // NEU — explizit, Muster RoofNode.traufhoeheMm :327 (kein Pendant bei Decke, die leitet ab)
  erdberuehrt: boolean;                            // NEU — Randbedingung
  materialId?: string;                             // NEU, einfach (kein Array wie schichten)
  schichten?: Array<{ materialId?: string; dickeMm: number }>;  // Muster CeilingNode.schichten :357 / WallNode.schichten :133
  durchbrueche?: FoundationSlabDurchbruch[];        // NUR ausdrücklich, KEINE Automatik (Abgrenzung zu Decke, s. u.)
}
```

`SceneDocument.foundationSlabs?: FoundationSlabNode[]` — additiv neben `roofs`/`ceilings` (Muster `scene.types.ts:46-54`).

### Namenskollision — Fund, nicht stillschweigend übernommen

Der Auftrag verlangt ein Feld `herkunft: 'aus_grundflaeche' | 'manuell' | 'bbox_naeherung' | 'geloest'`. `validation.ts:219-221` legt aber ausdrücklich fest: *"Der Name ist `geometrieHerkunft`, nicht `herkunft`"*, weil `herkunft` im Bundle bereits zweimal anders vergeben ist (`ToolHerkunft`, `FlaechenHerkunft`) — **"Ein Name, eine Bedeutung."** Die geforderten Werte (`aus_grundflaeche`/`bbox_naeherung`/`geloest`) messen zudem eine ANDERE Achse als das bestehende `GeometrieHerkunft`-Enum (`manuell`/`abgeleitet`/`erkannt`/`geschaetzt`, `scene.types.ts:303`): prozedurale Quelle statt Vertrauensklasse. Der Konzepttext selbst listet fünf Herkunftstexte (`golden-path-bauwerksprozess.md:55-56`: *"aus Gebäudegrundfläche abgeleitet", "aus EG übernommen", "manuell gezeichnet", "nur Bounding-Box-Näherung", "nachträglich von Vorlage gelöst"*) — das riecht nach einer geschossübergreifenden dritten Achse, nicht nur einem Bodenplatten-Feld.

Zwei Optionen, keine selbst entschieden:
- **Option A (Feldname umbenennen, additiv, risikoarm)**: Feld heißt `polygonQuelle` (oder `geometrieQuelle`) statt `herkunft`, exakt die vier verlangten Werte. Kein Eingriff in `GeometrieHerkunft`/`MitHerkunft`. **Empfehlung**, weil additiv und ohne Seiteneffekt auf Decke/Dach.
- **Option B (Enum erweitern)**: `GeometrieHerkunft` um `aus_grundflaeche`/`bbox_naeherung`/`geloest` erweitern. Blast-Radius gemessen: **kein** UI-Konsument in `app/` (Grep `geometrieHerkunft` in `app/` → 0 Treffer), also kein kaputter Switch — aber der Text in `validation.ts:219-221` verbietet die Vermischung zweier Bedeutungen unter einem Namen ausdrücklich; Erweiterung wäre ein Bruch dieser eigenen Regel.

→ **Fachfrage an Yama, siehe (8).**

### Zod/JSON-Schema-Stellen

- `foundationSlabDurchbruchSchema` (Muster `ceilingOeffnungSchema`, `validation.ts:263`)
- `foundationSlabNodeSchema` (Muster `ceilingNodeSchema`, `validation.ts:264-274`) inkl. `oberkanteMm: mm` (nicht `mmPos`, da Werte auch negativ sein können — Unterkellerung), `erdberuehrt: z.boolean()`, `...herkunftsFelder` (`validation.ts:231-236`, direkt wiederverwendbar, R1)
- `sceneDocumentSchema`: `foundationSlabs: z.array(foundationSlabNodeSchema).optional()` — Präzedenz `ceilings` (`validation.ts:294`, optional, additiv, kein 422 für Bestand)
- `migriereSzene` (`validation.ts:308-354`): `mitSammlungen()` um `foundationSlabs: Array.isArray(q.foundationSlabs) ? q.foundationSlabs : []` erweitern (Muster Zeile 313-317); `mitHerkunft()` bereits generisch, direkt auf `q.foundationSlabs` anwendbar in `aufV3` (Zeile 339-344) — **Präzedenz zweifach belegt (`aufbauten`/`anbau`), kein `schemaVersion`-Sprung nötig**, analog zur bereits getroffenen Aussage der Dachschichten-Matrix (`dachschichten-reuse-matrix.md:29`). Nicht selbst am Schema getestet in dieser Runde — muss im Bauauftrag mit Zod-Test abgesichert werden (gleicher Vorbehalt wie dort, Zeile 86,111).
- `scene-document-v2.schema.json`: `additionalProperties: false` auf Root-Ebene (Zeile 998) und `roofs` in `required` (Zeile 995), `ceilings` NICHT in `required` — Präzedenz für `foundationSlabs` als optionales, nicht-required Top-Level-Property. Muss manuell nachgezogen werden; kein Generierungsscript gefunden (**diese Aussage aus `dachschichten-reuse-matrix.md:30,110` übernommen, in dieser Runde nicht erneut gemessen**).

### Teilbare vs. nicht-teilbare Funktionen

| Funktion | Pfad:Zeile | Teilbar? | Begründung |
|---|---|---|---|
| `polygonFlaecheM2` | `geometry/polygonFlaeche.ts` | **R1 teilbar** | bauteil-neutrale Flächenrechnung |
| `pruefeGanzzahlig` | `applyCommand.ts:28-32` | **R1 teilbar** | generisch |
| `pruefeDeckeGanzzahlig`/`pruefeDeckeProLevel` | `applyCommand.ts:104-116` | **R2 Muster kopieren**, nicht Funktion selbst | eigene Feldmenge (`oberkanteMm`, `erdberuehrt`) |
| `herkunftFuerNeueDecke`/`herkunftFuerNeuesDach` | `geometry/freigabe.ts:85-107` | **R1/R2** | `MitFreigabe`-Typ ist bereits generisch (`freigabe.ts:23-28`), `herkunftFuerNeueBodenplatte(ausKontur)` kann dieselbe Form direkt übernehmen |
| `gebaeudeUmriss()` | `HausplanerApp.tsx:753-763` | **R1 teilbar** | liefert Default-Polygon für `polygonQuelle: 'aus_grundflaeche'`-Fall unverändert |
| `treppenDurchbrueche` | `applyCommand.ts:119-137` | **NICHT teilbar, bewusst** | Auftrag verlangt für Bodenplatte NUR ausdrückliche Durchbrüche, keine Automatik — Abgrenzung zur Decke ist fachlich gewollt, nicht technische Schwäche |
| `deckenOberkanteMm` | `deckenMesh.ts:10-12` | **NICHT direkt teilbar** | Bedeutung differiert: Decke = Unterkante auf Wand-OK (abgeleitet), Bodenplatte = eigene, von Wandhöhe unabhängige Bezugsgröße |
| Decken-Render-Block | `szene.ts:477-506` | **R2 Struktur kopieren** | eigene Sammlung `dokument.foundationSlabs`, eigene Höhe (`oberkanteMm` statt `deckenOberkanteMm`), eigene Farbe — kein Renderer-Umbau (Nicht-Ziel), additiver neuer Block |
| `executeCommands`/A-31-Muster | `store/hausplanerStore.ts:65,147-170`, `app/sammelBefehle.ts` | **R1 direkt wiederverwendbar** | s. (3) |

### Migrationstest (additiv)

Fixture-Bestandsdokument (analog `__tests__/fixtures/a01-bestandsdokument-l-dach.json`) OHNE `foundationSlabs`-Feld durch `migriereSzene` + `sceneDocumentSchema.safeParse` schicken → `success: true`, `foundationSlabs === []` oder `undefined` (je nach gewählter Optionalitätsform), **keine** sonstige Feldänderung (Diff-Test wie bei `migriereSzene` bereits üblich).

---

## (3) Command-Plan

### Neue Commands (Muster `ADD/UPDATE/REMOVE_CEILING`, `commands.types.ts:29-31`, `applyCommand.ts:288-330`)

```ts
| { type: 'ADD_FOUNDATION_SLAB'; slab: FoundationSlabNode }
| { type: 'UPDATE_FOUNDATION_SLAB'; slabId: string; changes: Record<string, unknown> }
| { type: 'REMOVE_FOUNDATION_SLAB'; slabId: string }
```

`AblehnungsGrund` (`commands.types.ts:50-64`) erweitern um `'bodenplatte_pro_level_vorhanden'`, `'bodenplatte_unbekannt'` (Muster `'decke_pro_level_vorhanden'`/`'decke_unbekannt'`). Undo-Granularität: **eine Bodenplatte, ein Command** — Immer-Draft liefert Undo/Redo automatisch (identisches Muster wie Decke/Dach, kein Sonderfall nötig).

**Fachfrage (nicht selbst entschieden)**: "max. 1 je Level" wie bei Decke/Dach, oder "max. 1 je GEBÄUDE" (nur unterstes/EG-Level braucht eine Bodenplatte, Zwischengeschosse nicht)? Die vorhandene Prüfung `pruefeDeckeProLevel` (`applyCommand.ts:112-116`) ist levelbezogen — für die Bodenplatte ist das fachlich vermutlich falsch (nur EIN Level im Gebäude hat eine Bodenplatte). Siehe (8).

### Höhenkette als EINE Funktion

**Ort**: neue Datei `resources/planner/hausplaner/geometry/hoehenkette.ts` — reine Funktion, kein Command, kein Renderer-Zugriff (Muster `geometry/`-Ordner, dieselbe Ebene wie `freigabe.ts`).

```ts
export interface HoehenkettenErgebnis {
  bodenplattenOberkanteMm: number;        // NEU
  wandOberkanteMm: number;                // = heutiges deckenOberkanteMm(level)
  zwischendeckenOberkanteMm: number;      // = heutiges naechsteEtageElevationMm(level, decke) — endlich verdrahtet statt tot
  naechstesGeschossElevationMm: number;   // = heutiges Kopfrahmen.tsx:172-Inline — ab jetzt hierher verlagert
  traufhoeheMm: number;                   // Dachauflager, nur oberstes Level
}
export function berechneHoehenkette(eingabe: { level: …; bodenplatte?: …; decke?: … }): HoehenkettenErgebnis
```

**Bestehende Stellen, die die Funktion LESEN sollten statt eigener Rechnung** (die drei Zweite-Wahrheit-Fundstellen aus (1)):
1. `HausplanerApp.tsx:1008` (`traufhoeheMm: deckenOberkanteMm(level)`) → `berechneHoehenkette(...).traufhoeheMm`
2. `Kopfrahmen.tsx:172` (Inline-Formel `oben.elevation + oben.defaultWallHeight + oben.floorThickness`) → `berechneHoehenkette(...).naechstesGeschossElevationMm`
3. `deckenMesh.ts:10-12,32-38` bleiben bestehen und werden VON `berechneHoehenkette` intern aufgerufen (keine Löschung, keine Verhaltensänderung an bestehenden direkten Aufrufern in `szene.ts:456,483` — additiv, kein Umbau der Renderer per Nicht-Ziel)

**Regel "Zwischendeckenänderung aktualisiert OG-Höhe nur über bestätigtes Command"**: Trennung Vorschau (reine Funktion, kein Store-Schreibzugriff, liefert `{nodeId, feld, altWert, neuWert}[]`) vs. Commit (Schreib-Command). Für den Commit **existiert der Mechanismus bereits und ist R1 direkt wiederverwendbar**: `store/hausplanerStore.ts:65,147-170` (`executeCommands: (commands: HausplanerCommand[]) => boolean`) führt eine Liste von Commands in EINEM `produceWithPatches`-Draft aus — *"ALLE Befehle in EINEM Draft: wirft einer, verwirft Immer den ganzen Entwurf"* (`hausplanerStore.ts:158`) — und schreibt EINEN Historien-Eintrag (EIN Undo-Schritt für die ganze Kette). Das Muster ist an vier Stellen in `app/sammelBefehle.ts` bereits produktiv (A-31: `befehleSpiegeln`, `befehleDuplizieren`, `befehleGeschossDuplizieren`, `befehleLoeschen`). Eine neue Funktion `befehleHoehenkettenAktualisierung(vorschau)` nach demselben Muster liefert `HausplanerCommand[]` (`UPDATE_LEVEL`/`UPDATE_CEILING`/`UPDATE_ROOF` gebündelt), die UI ruft `store.getState().executeCommands(...)` erst NACH Bestätigung der Geistervorschau auf. **Kein neuer Store-Mechanismus nötig — reine Anwendung des bestehenden Musters.**

---

## (4) Abhängigkeitsmatrix

Empfehlung zur Trägerfrage zuerst: **kein Feld am Node, kein separates Register in Stufe 1** — additiv ein reiner Berechnungsdienst (`geometry/abhaengigkeiten.ts`, analog `hoehenkette.ts`), der bei einer Änderung die betroffene Liste ON-DEMAND aus dem aktuellen `SceneDocument` ableitet (wer referenziert `levelId X`, wer liegt auf `hostWallId Y`). Begründung: ein Statusfeld am Node (`pruefungErforderlich: boolean`) wäre eine GESPEICHERTE Ableitung, die bei jeder Fremdänderung woanders nachgezogen werden müsste — genau das Muster, das bei `traufhoeheMm` bereits zum K-2-Befund geführt hat (eingefrorener Snapshot statt Live-Ableitung). Ein separates Abhängigkeitsregister (eigene Sammlung `SceneDocument.abhaengigkeiten`) wäre eine zweite Datenwahrheit zusätzlich zu den Nodes selbst. **Ungemessen**, ob diese Empfehlung mit UI-Performance bei großen Szenen kollidiert (keine Messung in dieser Runde).

| Änderung an ↓ / betrifft → | EG-Wände | Öffnungen | Räume | Treppe | Zwischendecke | OG-Level (Elevation) | OG-Wände | Dach | Dachaufbauten | Dachschichten |
|---|---|---|---|---|---|---|---|---|---|---|
| **Bodenplatte Dicke/Lage** | PRÜFUNG (Wand-Fußpunkt/Aufstandsfläche) | nein | nein | nein | nein | JA, wenn `oberkanteMm` in Höhenkette einfließt | nein | nein | nein | nein |
| **Wandhöhe (`defaultWallHeight`/`WallNode.height`)** | — | PRÜFUNG (Brüstung/Sturz relativ) | nein | PRÜFUNG (Geschosshöhe der Treppenberechnung, `treppeZuParametern` nutzt `geschosshoehe`) | JA (`deckenOberkanteMm`) | JA (`naechsteEtageElevationMm`) | nein direkt | nein direkt | JA (`traufhoeheMm`, heute NICHT automatisch, s. (1)) | nein | nein |
| **Zwischendecke Dicke** | nein | nein | nein | PRÜFUNG (Treppenauge-Durchbruch, `treppenDurchbrueche`) | — | JA (`naechsteEtageElevationMm`, heute TOT s. (1)) | JA (Fußpunkt) | PRÜFUNG (falls Traufhöhe an OG-Wand-OK hängt) | nein | nein |
| **Treppe Lage** | nein | nein | PRÜFUNG (Raumerkennung, Wandachsen unverändert aber Möblierung) | — | JA (Durchbruch-Neuberechnung, `applyCommand.ts:119-137`) | nein | nein | nein | nein | nein |
| **OG-Kontur (Wände OG)** | nein | nein | nein | nein | PRÜFUNG (falls Decke nicht aus Kontur, sondern `gebaeudeUmriss()`-Näherung) | nein | — | JA (Dachpolygon `gebaeudeUmriss()`-Fall) | JA (relative x/y 0..1) | nein |
| **Grundfläche (EG-Wände verschoben)** | — | PRÜFUNG (Bindung an Wirtswand bleibt, aber Fläche ändert sich) | JA (Raumerkennung neu) | PRÜFUNG (Lauflinie ggf. außerhalb) | PRÜFUNG (`gebaeudeUmriss()`-Fall) | nein | nein | PRÜFUNG (`gebaeudeUmriss()`-Fall) | nein | nein |

Reuse: die Referenzierungsmuster (`hostWallId`, `levelId`) sind bereits vorhanden und direkt abfragbar (**R1**), keine neue Referenzstruktur nötig.

---

## (5) Phasendefinition 1–15

Abkürzungen: **EV**=Einstiegsvoraussetzung · **HA**=Hauptaktion · **PE**=Planerentscheidung · **AD**=ableitbare Daten · **HK**=Herkunft · **VB**=Vorwärtsbedingung · **ZR**=Zurück · **UR**=Undo/Redo · **SÄ**=spätere Änderungen · **SL**=Speichern/Laden-Nachweis · **AB**=2D/3D-Abnahme. "Zurück" ≠ Undo ≠ "Phase zurücksetzen" (Konzept-Regel, `golden-path-bauwerksprozess.md:47-50`) — als eigene Spalten geführt (ZR / UR), "Phase zurücksetzen" separat vermerkt wo relevant.

| Ph | EV | HA | PE | AD | HK | VB | ZR | UR | SÄ | SL | AB |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | leeres Projekt | Grundlagen | Einheit, Nord, Geschosse, Standardhöhen | — | manuell | ≥1 Level valide | n/a (erste Phase) | Feldwerte | öffnet Prüfliste für alle Folgephasen | Projekt-Anlage-Test | n/a |
| 2 | Phase 1 fertig | EG-Bezugsebene | `elevation=±0`, Bezugspunkt | — | manuell | Level existiert | Phase 1 | Feldwerte | ändert `bodenplattenOberkanteMm` (Höhenkette) | Level-Persistenz | n/a |
| 3 | Phase 2 fertig | Grundfläche | Kontur/Räume/manuell | `gebaeudeUmriss()`-Näherung möglich | `manuell`/`abgeleitet` | ≥3 Punkte | Phase 2 | Punkte/Kontur | markiert Bodenplatte+Wände zur Prüfung | Kontur-Persistenz | 2D-Kontur sichtbar |
| 4 | Phase 3 fertig (Grundriss/Umriss vorhanden) | **Bodenplatte erzeugen** (`ADD_FOUNDATION_SLAB`) | Dicke, Material, `oberkanteMm`, `erdberuehrt` | Polygon aus Grundfläche | `aus_grundflaeche`/`manuell`/`bbox_naeherung` | Ganzzahligkeit, max. 1 je Gebäude | Phase 3 | Command-Undo | Wandhöhe/EG-Bezug erhalten Vorschau | Slab-Persistenz-Test (neu) | 3D-Slab sichtbar (2D optional, s. Nicht-Ziele) |
| 5 | Phase 4 fertig | Außen-/Innenwände EG | Wandaufbau, Bezugslinie | Wandzug aus Klicks | manuell | ≥1 Wand valide | Phase 4 | Command-Undo | Räume/Öffnungen/Decke neu geprüft | Wall-Persistenz (bestehend) | 2D/3D Wände |
| 6 | Phase 5 fertig | Fenster/Türen | Maße, Brüstung, Anschlag | Wirtswand-Bindung | manuell | passt in Wirtswand | Phase 5 | Command-Undo | wandert mit Wirtswand (bestehend, `clamped`) | Opening-Persistenz (bestehend) | 2D/3D Öffnungen |
| 7 | Phase 6 fertig | Räume + Treppe | Treppenlage, Treppenauge | Raumerkennung (`roomDetection.ts`) | `derived: true` (Zwang, `validation.ts:136-138`) | Treppe geometrisch gültig (DIN 18065, `treppenBerechnung.ts`) | Phase 6 | Command-Undo | markiert Zwischendecke automatisch betroffen | Raum/Treppe-Persistenz (bestehend) | 2D/3D Treppe |
| 8 | Phase 7 fertig | **Zwischendecke erzeugen** (`ADD_CEILING`, bestehend) | Kontur, Dicke, Aufbau, Öffnungen | Treppendurchbruch automatisch (`treppenDurchbrueche`) | `manuell`/`abgeleitet` | max. 1 je Level, Ganzzahligkeit | Phase 7 | Command-Undo | OG-Elevation erhält Vorschau (`naechsteEtageElevationMm`, künftig `berechneHoehenkette`) | Ceiling-Persistenz (bestehend, `decke.test.ts`) | NUR 3D (2D-Lücke gemessen, s. (1)) |
| 9 | Phase 8 fertig | OG erzeugen | leer oder aus EG ableiten | EG-Kopie (`befehleGeschossDuplizieren`) | `abgeleitet`/`geloest` bei Lösen | `ADD_LEVEL` erfolgreich | Phase 8 | Transaktion (A-31-Muster, `sammelBefehle.ts`) | Ableitung bleibt lösbar (Herkunft-Flag) | Level-Persistenz (bestehend) | n/a |
| 10 | Phase 9 fertig | OG bearbeiten | übernommene/eigene Geometrie | wie EG (Phase 5-7 wiederholt) | manuell/geloest | wie Phase 5-7 | Phase 9 | Command-Undo | Dachkontur zur Prüfung markiert | wie EG-Persistenz | 2D/3D OG |
| 11 | Phase 10 fertig | Dachgrundform | Form, Neigung, Traufe, First | `traufhoeheMm` via Höhenkette (heute eingefroren, s. (1)) | `manuell`/`abgeleitet` | `dachFlaechen()` wirft nicht | Phase 10 | Command-Undo | Aufbauten/Schichten geprüft | Roof-Persistenz (bestehend) | 2D/3D Dach — **verweis `dachschichten-reuse-matrix.md` Phase 11** |
| 12 | Phase 11 fertig | Dachaufbauten | Gaube/Fenster/Kamin | relative x/y | manuell | Geometrie gültig | Phase 11 | Command-Undo | Ausschnitte aktualisiert | Aufbau-Persistenz (bestehend) | **verweis Dachschichten-Matrix Phase 12** |
| 13 | Phase 12 fertig | Dachschichten | Reihenfolge, Dicke, Material | — | manuell | Ganzzahligkeit | Phase 12 | Command-Undo | Visualisierung folgt | **s. `dachschichten-reuse-matrix.md`, bereits durchgeplant** | **verweis Matrix (5), 3D-Explosion** |
| 14 | Phase 13 fertig | Prüfen/Präsentieren | CAD/Konstruktion/Präsentation | reiner Ansichtszustand | n/a | keine Modelländerung | Phase 13 | n/a (Ansicht, nicht Undo-fähig — offene Frage bereits in Dachschichten-Matrix Zeile 82) | keine Modelldaten ändern sich | n/a (Ansicht nicht persistiert außer ggf. `SceneDocument.ansicht`) | 3 feste Screenshots je Ansicht |
| 15 | Phase 14 fertig | Speichern/Neuladen | — | — | n/a | Speichern erfolgreich | n/a | n/a | n/a | **Referenzhaus bytegleich nach Reload (bis auf `updatedAt`/`revision`)** | 2D/3D nach Reload identisch |

Phasen 11–13 sind bereits vollständig durchgeplant in `docs/konzept/dachschichten-reuse-matrix.md` — hier bewusst nicht dupliziert, nur referenziert.

**"Phase zurücksetzen"** (dritte Funktion neben Zurück/Undo): in dieser Tabelle nicht separat je Phase geführt, weil sie bei ALLEN Phasen 4-13 dieselbe Regel trägt — *"bewusst alle Änderungen einer Phase entfernen — erst nach Auswirkungsanzeige und Bestätigung"* (`golden-path-bauwerksprozess.md:48-50`) — technisch eine Anwendung der Abhängigkeitsmatrix (4) auf REMOVE-Commands derselben Phase, kein neuer Mechanismus. Ungebaut/ungemessen in dieser Runde, ob ein Phase-Tag pro Command nötig wäre, um "alle Commands dieser Phase" zu identifizieren — **offene Frage, siehe (8)**.

---

## (6) Referenzhaus — Testdaten (ausdrücklich Testdaten, keine bauliche Empfehlung)

Fixture-Vorschlag im Stil von `fixtures/studioFixtures.ts:63-92` (`deckeTreppe()`), erweitert um Bodenplatte/OG/Dach:

```
EG-Umriss (Rechteck):        10.000 × 8.000 mm  (Muster RECHTECK_UMRISS, studioFixtures.ts:54-56)
Level EG:  elevation 0, defaultWallHeight 2.500, floorThickness 200  (Muster EG, :23-30)
Bodenplatte: dickeMm 250, oberkanteMm 0 (= Bezugshöhe, Fachfrage 8 offen), erdberuehrt true
Treppe:      Lauflinie (2.000,2.000)→(5.000,2.000), Laufbreite 1.000 (Muster :68, treppeZuParametern)
Zwischendecke EG→OG: dickeMm 200, Treppenauge automatisch (treppenDurchbrueche)
Level OG:  elevation = berechneHoehenkette(...).naechstesGeschossElevationMm, defaultWallHeight 2.500
OG-Kontur: aus EG abgeleitet (befehleGeschossDuplizieren-Muster), 1 Innenwand verschoben (Abweichungs-Testfall)
Dach:      sattel, neigungGrad 35, ueberstandMm 500, firstAzimutGrad 0, traufhoeheMm = berechneHoehenkette(...).traufhoeheMm
Dachaufbau: 1 Dachfenster (Muster RoofAufbau)
Dachschichten: 5 Schichten nach dachschichten-reuse-matrix.md (2), Reihenfolge tragwerk→…→deckung
```

Fixture-Datei: neuer Eintrag `'referenzhaus-golden-path'` in `STUDIO_FIXTURES` (`studioFixtures.ts:113-116`), reine Funktion, feste ISO-Zeit (Muster `ISO` Zeile 21) — R1 direkt wiederverwendbares Registrierungsmuster.

---

## (7) Abnahmekriterien Slice "Bodenplatte + Höhenkette" (messbar)

1. `ADD_FOUNDATION_SLAB` erhöht `foundationSlabs.length` um 1; Undo entfernt sie (ein Schritt) — Muster `decke.test.ts:50`.
2. Zweite Bodenplatte im selben Gebäude wird abgelehnt (`'bodenplatte_pro_level_vorhanden'` oder gebäudeweite Variante, je nach Fachantwort (8)) — Dokument bleibt unverändert.
3. `berechneHoehenkette(...)` liefert für das Referenzhaus-Fixture (6) exakt die Werte, die heute `deckenOberkanteMm`/`naechsteEtageElevationMm`/`Kopfrahmen.tsx:172` GETRENNT liefern (Rot-Probe: drei alte Werte gegen einen neuen Rückgabewert vergleichen).
4. `traufhoeheMm` ändert sich NACH `UPDATE_LEVEL` auf `defaultWallHeight` nur über einen bestätigten Commit-Command (`executeCommands`), NICHT automatisch beim bloßen `UPDATE_LEVEL` — Test für "Vorschau ≠ Commit".
5. Bestandsprojekt ohne `foundationSlabs` lädt unverändert (kein 422), `migriereSzene`-Diff zeigt NUR das neue leere Array, sonst nichts.
6. Speichern/Laden: Dokument mit befüllter Bodenplatte ist nach Neuladen bytegleich (bis auf `updatedAt`/`revision`).
7. `tsc` exit 0, bestehende Testsuite grün (Governance-Tor, Muster Dachschichten-Matrix Kriterium 9).

**Nicht-Ziele dieses Slice**:
- Dachschichten: verweis `dachschichten-reuse-matrix.md`, hier NICHT gebaut.
- Kein Renderer-Umbau: bestehende Decke/Dach-Render-Blöcke (`szene.ts:452-599`) bleiben unangetastet, nur additiver neuer Block für Bodenplatte.
- Keine Vermischung Fußbodenaufbau (`schichten?`, AUF-76/M0) und tragende Platte (`dickeMm`) — beide Felder bleiben getrennt geführt wie bei `WallNode.thickness` vs. `WallNode.schichten` (`scene.types.ts:104-133`, Kommentar dort: *"beide Bezugsmaße werden geführt"*).
- Keine Projektion in `RaumGeometrieProjektion.boden` (`scene.types.ts:386`, heute `null` — `raumProjektion.ts:107`) — bleibt in diesem Slice bewusst `null`, kein PHP-Anschluss.
- Kein 2D-Rendering der Bodenplatte zwingend (Präzedenz: Decke ist heute auch nur 3D) — falls gefordert, eigener Nachtrag.

---

## (8) Risiken / offene Fachfragen für Yama

1. **Bezugshöhe ±0,00**: OK-Fertigfußboden EG oder OK-Bodenplatte? Bestimmt, ob `oberkanteMm` der Bodenplatte bei `erdberuehrt=true` standardmäßig `Level.elevation - dickeMm` (Unterseite an ±0) oder `Level.elevation` selbst (Oberseite an ±0) ist. **Nicht im Code entschieden**, direkt Auswirkung auf `berechneHoehenkette`.
2. **Erdberührungs-Standard**: EG-Bodenplatte defaultet auf `erdberuehrt: true`? Kellergeschoss-Fall (falls später relevant) auf `false`? Keine Vorgabe im Bestand.
3. **Durchbrüche-Modell**: Auftrag verlangt "nur ausdrücklich" (keine Automatik wie bei Treppen-Öffnung der Decke) — bewusster Unterschied zur Decke bestätigt, aber ist das Yamas Wunsch oder nur Auftragswortlaut? Rückfrage sinnvoll, da es inkonsistent zur Decken-UX wirkt (Anwender gewöhnt an Automatik).
4. **Namenskollision `herkunft`** (Abschnitt 2): Feldname umbenennen (Option A, empfohlen) oder `GeometrieHerkunft`-Enum erweitern (Option B)?
5. **Bodenplatte je Level oder je Gebäude**: die Prüfung "max. 1" — auf welcher Ebene? Level-bezogen (Muster Decke/Dach) passt fachlich vermutlich nicht (nur EG braucht eine Platte).
6. **"Phase zurücksetzen" braucht evtl. ein Phasen-Tag pro Command** — ungemessen, ob die Historie (`store/hausplanerStore.ts`) heute Commands einer Phase zuordnen kann. Nicht Teil dieses Slice, aber Voraussetzung für Phase 14-Funktion insgesamt.
7. **`WallNode.height` vs. `Level.defaultWallHeight`-Divergenz** (Abschnitt 1, Zweite Wahrheit Nr. 3): ungemessen, ob dies in der Praxis genutzt wird (kein UI-Feld gefunden) — falls ja, muss `berechneHoehenkette` entscheiden, ob sie den Level-Default oder die tatsächliche Wandhöhe liest.
8. **`scene-document-v2.schema.json`-Pflege**: kein Generierungsscript gefunden (Befund aus Dachschichten-Matrix übernommen, nicht neu gemessen) — jede additive Erweiterung (Bodenplatte UND Dachschichten) braucht manuelles Nachziehen; Risiko von Drift zwischen Zod und JSON-Schema wächst mit jedem weiteren additiven Feld.
9. **K-2-Fix (Traufhöhe-Nachziehen bei `UPDATE_LEVEL`)**: ist das Bestandteil DIESES Slice oder ein Folge-Slice? Der Auftrag verlangt die Höhenkette "als EINE Funktion", was den Fix technisch ermöglicht, aber die tatsächliche Verdrahtung von `UPDATE_LEVEL` → Dach-Nachfrage ist eine UX-Entscheidung (automatisch vs. Vorschau-Bestätigung) und damit möglicherweise über den reinen Modell-/Command-Plan hinausgehend.

---

**Relevante Pfade** (alle unter `/Users/yamanuri/Documents/ticket/resources/planner/hausplaner/` sofern nicht anders angegeben):
`domain/scene.types.ts`, `domain/validation.ts`, `domain/scene-document-v2.schema.json`, `domain/commands.types.ts`, `commands/applyCommand.ts`, `store/hausplanerStore.ts`, `app/sammelBefehle.ts`, `app/HausplanerApp.tsx`, `app/dashboard/Kopfrahmen.tsx`, `app/tools/toolRegistry.ts`, `app/rahmen/Buehne.tsx`, `app/rahmen/EigenschaftenPanel.tsx`, `app/dashboard/fahrschritte.ts`, `renderers/three-d/deckenMesh.ts`, `renderers/three-d/szene.ts`, `renderers/three-d/segmentierung.ts`, `geometry/roomDetection.ts`, `geometry/freigabe.ts`, `geometry/polygonFlaeche.ts`, `projection/raumProjektion.ts`, `fixtures/studioFixtures.ts`, `__tests__/decke.test.ts`; sowie `/Users/yamanuri/Documents/ticket/docs/rollenkette/werkbank/02-WERKZEUGE/W-10-decke-und-boden/` (alle 6 Blätter), `/Users/yamanuri/Documents/ticket-rolle-dirigent/docs/konzept/golden-path-bauwerksprozess.md`, `/Users/yamanuri/Documents/ticket-rolle-dirigent/docs/konzept/dachschichten-reuse-matrix.md`.

**Nächster Schritt**: Freigabe/Widerspruch durch Plan-Prüfer zu diesem Modell-/Command-Plan; parallel Yamas Entscheidung zu den neun Fachfragen in (8) — insbesondere 1/2/4/5, da sie das Feldmodell selbst betreffen und vor jedem Bauauftrag geklärt sein müssen. Bau frühestens nach `TESTBEREIT`, als erster Produkt-Slice vor/mit dem Dachschichten-Schnitt (`golden-path-bauwerksprozess.md:116-117`).
