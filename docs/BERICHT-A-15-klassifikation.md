# A-15 — Messbericht: Fachaussage oder Hinweis

**Rolle:** Generator · **Stand:** 12.08.2026, **IN ARBEIT** — Achse 1 und die Plakettenlage sind
gemessen, Achse 2/3 und die Klassifikation folgen. **Kein Code angefasst.**

## A-15-1 · Die Menge — Pfad, Muster, Summe, und was NICHT dazugehört

```text
PFAD     resources/planner/hausplaner/geometry/  +  resources/planner/hausplaner/app/dashboard/
MUSTER   \bbestanden\b        (Wortgrenze, wie im Auftrag)
DATEIEN  *.ts, *.tsx
SUMME    13
```

**Die dreizehn:** `enginePanels.ts` · `abwassergefaelle.ts` · `configuratorPackage.ts` ·
`fbhAuslegung.ts` · `heizkreisVerteiler.ts` · `kuecheArbeitsdreieck.ts` · `sparrenBerechnung.ts` ·
`treppe2D.ts` · `treppe3D.ts` · `treppenBerechnung.ts` · `treppenTypen.ts` · `wandaufbau.ts` ·
`werkzeugRegistry.ts`

**Die Zahl des Auftrags stimmt.** *Ich hatte vorab „acht" gemeldet — das waren die Engine-**Panels**,
eine andere Menge. Gut, dass ich es als zu klären gemeldet habe und nicht als Widerspruch.*

### Was NICHT in der Menge ist — und warum

| ausgeschlossen | trägt das Wort? | Grund |
|---|---|---|
| `app/EngineFlaeche.tsx` | ja | **zeigt** die Plakette, rechnet nichts — die Hülle ist keine Rechnung |
| `app/rahmen/Buehne.tsx`, `EigenschaftenPanel.tsx` | ja | Darstellung |
| `__tests__/**` (14 Dateien) | ja | Zusagen über die Rechnungen, nicht die Rechnungen |
| `renderers/`, `app/tools/` | nein | **0 Treffer** — sie kommen gar nicht in Frage |

*Die Ausschlüsse sind gemessen, nicht gesetzt: `renderers/` und `app/tools/` tragen das Wort nicht.*

## A-15-2 · Achse 1 — Normnennung, je Datei mit Zeile

**ACHT nennen eine Norm:**

| Datei | Zeile | Norm |
|---|---|---|
| `sparrenBerechnung.ts` | **2**, **7** | Eurocode; DIN EN 1991-1-3 |
| `treppenBerechnung.ts` | **5**, **58** | DIN 18065 (auch als Grenzwert im Code) |
| `treppe2D.ts` | **6** | DIN 18065 (verweisend auf `berechneTreppe`) |
| `wandaufbau.ts` | **4**, **19** | DIN EN ISO 6946 |
| `abwassergefaelle.ts` | **4** | DIN 1986-100 (vereinfacht) |
| `kuecheArbeitsdreieck.ts` | **4** | DIN 18022 / Küchenergonomie |
| `enginePanels.ts` | **124**, **125** | DIN 18065 (im `zweck` und in `grundlage`) |
| `werkzeugRegistry.ts` | **13** | DIN 18065 (im Kommentar eines Feldes) |

**FÜNF nennen keine:** `configuratorPackage.ts` · `fbhAuslegung.ts` · `heizkreisVerteiler.ts` ·
`treppe3D.ts` · `treppenTypen.ts`

**Auch die Fünf stimmen mit dem Auftrag überein.**

## A-15-3 · Die zwei Unschärfen — beide BESTÄTIGT

1. **`kuecheArbeitsdreieck` nennt DIN 18022** — belegt, Zeile 4. Und der Kopf nennt sie selbst
   ergonomisch: *„Reine Ergonomie-Prüfung nach DIN 18022 / gängiger Küchenergonomie."*
   **Eine Norm zu nennen heißt hier nicht, ein Sicherheitsurteil zu fällen.**
2. **`fbhAuslegung` und `heizkreisVerteiler` nennen keine Norm** — belegt, 0 Treffer in beiden.
   **Und beide legen trotzdem eine Anlage aus.**

**Damit trägt der Zwei-Achsen-Vorschlag des Planners** — *„nennt eine Norm" reicht in beide
Richtungen nicht.*

## A-15-5 · Plakette je Engine — heute, gemessen

**Acht Engine-Panels** (`enginePanels.ts`), blockgenau gelesen:

| engineId | Rechnung | Plakette heute |
|---|---|---|
| `engine-treppe` | `berechneTreppe` | **ja** — im Browser gesehen: „Alle Prüfungen bestanden" (y=413) |
| `engine-sparren` | `berechneSparren` | **NEIN** — seit A-14 unterdrückt (`keinGesamturteil`) |
| `engine-fbh` | `fbhAuslegung` | ja, wenn `bestanden` boolean |
| `engine-heizkoerper` | mehrzeiliger Block | **ja** — im Browser: „Eine Prüfung ist nicht bestanden" (y=380) |
| `engine-fensterprodukt` | `berechneUw` | **nein** — liefert kein `bestanden` (Kommentar in `EngineFlaeche.tsx`) |
| `engine-abwasser` | `pruefeAbwasser` | ja, wenn `bestanden` boolean |
| `engine-kueche` | `bewerteArbeitsdreieck` | ja, wenn `bestanden` boolean |
| `engine-pv` | `pvSchnellBelegung` | **nein** — liefert kein `bestanden` |

> **`enginePanels.ts` und `werkzeugRegistry.ts` sind keine Engines** und könnten gar keine Plakette
> zeigen — der Auftrag sagt es, die Messung bestätigt es.

**Ein eigener Messfehler, vor dem Melden gefunden:** mein erstes Muster ordnete
`engine-heizkoerper` die Funktion `berechneUw` zu und übersprang `engine-fensterprodukt`. Ursache:
`engine-heizkoerper` hat einen **mehrzeiligen** `berechne:`-Block, über den das Muster hinweglief.
*Blockgenau nachgemessen — acht Panels, jede Zuordnung einzeln.*

## Noch offen

**A-15-4, -6, -9 bis -14** — Achse 2 und 3, die Klassifikation je Engine, Yamas drei Regeln im
Wortlaut. **Diese Seite wird fortgeschrieben; sie ist noch kein Ergebnis.**

## A-15-7 · must_preserve

**Kein Code angefasst** — Nachweis im Abschlussbericht.
