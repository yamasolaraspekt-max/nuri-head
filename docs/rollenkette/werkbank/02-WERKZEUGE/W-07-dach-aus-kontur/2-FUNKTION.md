# W-07 · Dach aus Kontur — FUNKTION

> **Ablesung des vorhandenen Codes, nicht Vorgabe.** *Jede Zeilenangabe ist einzeln geöffnet worden.
> Wo der Code eine Frage offen lässt, steht das hier als offen — nicht als Antwort.*

## Eingabe — ein `RoofNode`, und die Kontur ist das entscheidende Feld

| Was | Typ | Einheit | Pflicht | Prüfung |
|---|---|---|---|---|
| `polygon` (Traufkontur) | `P[]` = `{x,y}[]` | mm | **ja** | **muss nahezu rechteckig sein** — `pruefeRechteckigeKontur` ([dachGeometrie.ts:64](../../../../../resources/planner/hausplaner/geometry/dachGeometrie.ts#L64)), Toleranz **1 %** Flächenabweichung gegen die Bounding-Box |
| `roofType` | `'sattel'·'walm'·'pult'·'flach'` | — | ja | bestimmt Flächenzahl und Azimutsatz |
| `neigungGrad` | `number` | ° | ja | `sichererCos` hält 89° endlich und positiv (Kante 2) |
| `firstAzimutGrad` | `number` | ° | ja | **First**-Richtung, **nicht** Flächenazimut — Nord = `+y` |
| `ueberstandMm` | `number` | mm | ja | Vorgabe im Erzeugungspfad: **500** |
| `traufhoeheMm` | `number` | mm | ja | `level.elevation + level.defaultWallHeight` |

**Woher die Kontur kommt, und was passiert, wenn keine da ist** — [HausplanerApp.tsx:964-971](../../../../../resources/planner/hausplaner/app/HausplanerApp.tsx#L964):

```ts
const ausKontur = letzteKontur !== null && letzteKontur.length >= KONTUR_MIN_PUNKTE;
polygon: ausKontur ? letzteKontur : gebaeudeUmriss(),
```

*Ohne gezeichnete Kontur wird der **Gebäudeumriss** genommen und `setDachNaeherung(true)` gesetzt.
Kein Zwang zum Konturzeichnen — aber die Näherung wird **benannt**.*

## Verarbeitung — der Zustandsautomat

```
Kontur vorhanden?  ──nein──►  gebaeudeUmriss()  ──►  dachNaeherung = true
     │ ja
     ▼
Dach-Objekt vorbereitet (noch NICHT in der Szene)
     │
     ▼
dachFlaechen(dach)  ──wirft DachGeometrieUngueltig──►  setDachAbsage(fehler.message)
     │                                                  setWerkzeug('auswahl')
     │ kein Wurf                                        return  ── KEIN Objekt, KEIN Status
     ▼
executeCommand({ type: 'ADD_ROOF', roof: dach })  ──►  fertig
```

**Jeder Zustand einzeln — was angezeigt wird, was erwartet wird, was bei Abbruch geschieht:**

| Zustand | Angezeigt | Erwartet | Bei Abbruch |
|---|---|---|---|
| **Kontur zeichnen** | die laufende Linie; ab `KONTUR_MIN_PUNKTE` gilt sie | Klicks des Anwenders | Esc → Werkzeug `auswahl`, **nichts geändert** |
| **Prüfung** (kein Bildschirmzustand) | — | nichts; läuft synchron | entfällt |
| **Absage** | der **lesbare Grund** in der Fußleiste ([HausplanerApp.tsx:990](../../../../../resources/planner/hausplaner/app/HausplanerApp.tsx#L990)) | Anwender zeichnet neu | ist selbst der Abbruch: Szene unberührt |
| **Dach steht** | Dachflächen in 2D und 3D | — | über `undo` (siehe unten) |

> **Der Kern dieses Werkzeugs ist die Reihenfolge, nicht die Rechnung.** *Die Prüfung läuft
> **vor** `ADD_ROOF`. Vorher wurde das Dach angelegt und erst beim Zeichnen verworfen — beide Fänger
> in `szene.ts` schluckten den Wurf (`continue`/`return`), und es blieb ein Bauteil mit dem Status
> `bestaetigt` zurück, **das in keiner Ansicht existierte**. Der Kommentar an
> [HausplanerApp.tsx:975-985](../../../../../resources/planner/hausplaner/app/HausplanerApp.tsx#L975) sagt es
> selbst: „Die Schranke war nie das Problem; sie wurde nur nicht gehört."*

## Ausgabe

| Was | Typ | Wohin |
|---|---|---|
| Dachflächen | `DachFlaeche[]` — `flaeche_m2`, `azimut_grad \| null`, `neigung_grad`, `first_laenge_mm` ([:15-20](../../../../../resources/planner/hausplaner/geometry/dachGeometrie.ts#L15)) | Rückgabe von `dachFlaechen(roof)`; Quelle für **PV und Heizlast** |
| Konturmaße | `DachKontur` — `laengeMm`, `spannMm`, `cx`, `cy` ([:49](../../../../../resources/planner/hausplaner/geometry/dachGeometrie.ts#L49)) | **eine** Wahrheit für Fläche **und** Mesh |
| `RoofNode` | Szenenknoten `type: 'roof'` ([scene.types.ts:316](../../../../../resources/planner/hausplaner/domain/scene.types.ts#L316)) | Szene, über `ADD_ROOF` |
| Herkunftsstatus | aus `herkunftFuerNeuesDach(ausKontur)` | am Knoten — **aus der Domäne**, nicht aus dem Klick-Handler (Z-06-N1/B10) |
| Absagegrund | `string` in `dachAbsage` | Fußleiste; **kein** Szenenknoten |

*`azimut_grad = null` heißt **horizontal** (Flachdach) — nicht „unbekannt".*

## Kommando (für Rückgängig)

- **Name:** `ADD_ROOF` — abgesetzt in [HausplanerApp.tsx:999](../../../../../resources/planner/hausplaner/app/HausplanerApp.tsx#L999), ein zweites Mal beim Duplizieren ([:709](../../../../../resources/planner/hausplaner/app/HausplanerApp.tsx#L709)). Die Werkzeug-Landkarte führt es als Deckung: `{ werkzeugId: 'dach', marke: 'deckt', begruendung: 'ADD_ROOF' }` ([werkzeugLandkarte.ts:118](../../../../../resources/planner/hausplaner/app/tools/werkzeugLandkarte.ts#L118)).
- **Ausführen:** fügt **einen** `RoofNode` in die Szene ein — Polygon, Dachtyp, Neigung, First-Azimut, Überstand, Traufhöhe und Herkunftsstatus. **Es wird nichts Bestehendes verändert.**
- **Zurücknehmen:** über die Historie des Stores — `undo()` ([hausplanerStore.ts:140](../../../../../resources/planner/hausplaner/store/hausplanerStore.ts#L140)) holt den Eintrag aus `Historie` ([store/history.ts](../../../../../resources/planner/hausplaner/store/history.ts)); `redo()` bei `:152`. **Da `ADD_ROOF` nur einfügt, ist die Rücknahme das Entfernen genau dieses Knotens.**
- **Bündelung:** **nein, und das ist gemessen, nicht angenommen.** Der Ablauf setzt **ein** Kommando ab; die Kontur selbst entsteht im Zeichenwerkzeug und wird hier nur gelesen. **Offen bleibt:** ob Kontur-Zeichnen und `ADD_ROOF` fachlich **ein** Rücknahmeschritt sein sollten — heute sind es zwei, und wer das Dach zurücknimmt, behält die Kontur. *Das ist eine Bedienfrage, keine Codefrage; sie gehört zu `4-BEDIENUNG` und ist dort nicht entschieden.*

## Schichtzuordnung

- **Ändert Schicht 1 (Domäne): ja** — ein Knoten `type: 'roof'` kommt hinzu ([scene.types.ts:316](../../../../../resources/planner/hausplaner/domain/scene.types.ts#L316)). Der Herkunftsstatus wird **in der Domäne** gebildet, nicht im Handler.
- **Rechnet in Schicht 2 (Geometrie):** `F-010` (Orientierung), `F-013`, **`F-014`, `F-025`, `F-026`** (Kanten/Verschneidung), `F-020`–`F-022`. **Kern ist `dachGeometrie.ts` (153 Z.)**; die Registerzeile deckt acht Module mit **3.626 Zeilen** ab: `dachformVorlagen` 2.399 · `dachAusschnitt` 510 · `dachVerschneidung` 205 · `dachGeometrie` 153 · `dachUForm` 126 · `dachWerte` 103 · `dachOeffnung` 96 · `dachVorlage` 34.
- **Lebt in Schicht 3 (Anwendung):** [`app/HausplanerApp.tsx`](../../../../../resources/planner/hausplaner/app/HausplanerApp.tsx) — Erzeugungspfad `:964-1002`, Absagezustand `:331`. **Es gibt kein eigenes Werkzeugmodul unter `app/tools/`** (gemessen: 0 Treffer auf `dach` dort) — die Führung liegt im App-Handler.
- **Zeigt sich in Schicht 4/5:** Mesh über [`renderers/three-d/dachMesh.ts`](../../../../../resources/planner/hausplaner/renderers/three-d/dachMesh.ts), eingebunden in `szene.ts`; Aufbauten in `dachAufbautenMesh.ts`; nicht darstellbare Fälle in `nichtDarstellbar.ts`. **Der Anwender sieht:** die Dachflächen in 2D und 3D, den **Näherungshinweis** wenn ohne Kontur gebaut wurde, und bei Ablehnung den **Absagegrund im Klartext** in der Fußleiste.

## Der Azimut-Kontrakt ▲D4 — wörtlich, weil daran PV und Heizlast hängen

```text
dachGeometrie.ts:4-6
  ▲D4 Azimut-Kontrakt: Nord = +y. Der Flaechen-Azimut wird NIE gepflegt, sondern aus der
  First-Richtung (roof.firstAzimutGrad) abgeleitet. Satteldach: Flaechen = First ± 90°.
  Das ist die belastbare Quelle fuer PV/Heizlast (Nachfolger des stillgelegten RoofAreaEstimator).
```

> **`firstAzimutGrad` ist die First-Richtung und nicht der Flächenazimut.** *Wer das verwechselt,
> liegt bei einem Satteldach um 90° falsch. Die Doppeldeutigkeit des Wertebereichs steht als
> **F-028 🔴** in der Formelsammlung und als eigene Grenze in `7-GRENZEN`.*
