# W-06 · Geschoss verwalten — GRENZEN

## Das Wichtigste zuerst: F-032 ist eine FORMEL und keine Sperre

**Vier Werkzeuge tragen `F-032` in ihrer Registerspalte, und alle vier stehen auf `LEER`:**

```text
02-WERKZEUGE/REGISTER.md:38   W-12  Ansicht und Kamera
                        :48   W-16  Grundriss unterlegen
                        :54   W-06  Geschoss verwalten
                        :67   W-14  Kopieren/Spiegeln/Drehen
```

> **Das SIEHT aus wie ein gemeinsamer Blocker — vier Werkzeuge, eine Referenz, alle leer.** *Es ist
> keiner.* **`F-032` ist „Transformation eines Punktes"** (`FORMELSAMMLUNG.md`, Abschnitt
> `### F-032 · Transformation eines Punktes`) — *eine homogene 4×4-Matrix. Keine Ampel, keine
> Auflage, nichts, worauf man wartet.*

**Dieser Satz spart später viermal Messzeit.** *Wer die Referenz für eine Sperre hält, lässt vier
Werkzeuge unbeschrieben — und zwar aus demselben Grund, aus dem ein Muster die Schreibweise misst
statt die Sache.*

## Was das Werkzeug NICHT tut

| Grenze | Beleg |
|---|---|
| **Kein Schreibpfad, keine Szene-Mutation** | `geschossVorlage.ts:7` |
| **Kein neuer Zustand** — `setActiveLevel` bleibt die einzige Wahrheit | `geschossStapel.ts:17-18` |
| **Keine Sortierumkehr** — die Ordnung `sortOrder`, dann `elevation` wird nur ANGEZEIGT | `geschossStapel.ts:14-15` |
| **Kein eigener Modellbefehl** | vier vorhandene reichen: `ADD_LEVEL`, `ADD_NODE`, `ADD_ROOF`, `UPDATE_LEVEL` |
| **Keine Treppe zwischen den Geschossen** | das ist W-09 |
| **Keine Formel der Sammlung** | siehe `3-FORMELN` — gemessen, nicht angenommen |

## Die Grenze, die man beim Lesen übersieht: das letzte Geschoss

**Löschen ist gesperrt, solange nur eines da ist** (`GeschossFlaeche.tsx:165`) — *und der Titel nennt
den Grund statt nur auszugrauen.* **Das ist keine Bequemlichkeit:** ein Dokument ohne Geschoss hätte
keinen Ort für Knoten, und `stapel()` müsste einen Zustand beschreiben, den das Werkzeug nicht kennt.

## Wo `dupliziereGeschoss()` bewusst eine Lücke hinterlässt statt zu raten

```text
geschossVorlage.ts:67-71
  hostWallId !== undefined  ->  kopie.hostWallId = idMap.get(n.hostWallId)
  Wirtswand NICHT mitkopiert -> idMap.get(...) ist undefined -> Referenz FAELLT WEG
```

> **Der Kommentar sagt den Grund: sonst „bände das die Öffnung an eine Wand des Quell-Geschosses".**
> *Eine gedroppte Referenz ist sichtbar; eine auf die falsche Wand zeigende nicht.* **Dieselbe
> Haltung wie `OFFENE_HOLZBAUTEILE` bei W-21: lieber eine gemeldete Lücke als ein stiller Fehler.**

**Was das für den Aufrufer heißt:** *dupliziert er eine Teilmenge der Knoten, bekommt er Öffnungen
ohne Wirtswand.* **Ob das gültig ist, entscheidet nicht dieses Modul** — `applyCommand` prüft beim
Anlegen.

## Die stille Falschauskunft, die dieses Werkzeug behoben hat

**`elevation` lag im Modell und erschien nirgends** (AUF-43). *Kein falscher Wert — ein vorhandener,
den niemand sah.* **`hoehenLabel()` ist die Antwort**, und ihr Format ist eine Entscheidung: `±0 mm`
statt `+0 mm`, U+2212 statt Bindestrich, U+202F als geschütztes Trennzeichen.

> ***Die Grenze dabei:*** *das Label ist ein **Anzeigeformat**, kein Rechenwert.* **Wer daraus wieder
> eine Zahl gewinnen will, parst schmale geschützte Leerzeichen und ein Unicode-Minus** — der Rohwert
> steht als `elevation` daneben (`StapelEintrag.elevation`, `geschossStapel.ts:26`). *Beide Felder
> stehen absichtlich nebeneinander.*

## Nicht ausgewertet

**Die Zahl „ein angelegtes Geschoss entsperrt 34 der 110 Werkzeuge"** (`GeschossFlaeche.tsx:5-7`)
ist **nicht nachgemessen**. *Sie steht als zitierter Befund. Der Nenner ist inzwischen **111**
(A-29, Kontur-Vertrag aus `1fba9a1d`); ob der Zähler noch 34 ist, hat dieses Blatt nicht geprüft und
behauptet es deshalb nicht.*
