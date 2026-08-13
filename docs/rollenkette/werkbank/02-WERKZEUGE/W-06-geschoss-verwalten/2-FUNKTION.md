# W-06 · Geschoss verwalten — FUNKTION

## Drei Schichten, sauber getrennt — und die Trennung steht im TYPSYSTEM

**355 Zeilen, ZEHN Ausfuhren. Jede Zahl am Bau-Stand gezählt, nicht aus dem Auftrag übernommen.**

| Schicht | Modul | Z | Ausfuhren |
|---|---|---|---|
| **1 · reine Geometrie** | `resources/planner/hausplaner/geometry/geschossVorlage.ts` | 78 | `LevelVorlage` (11) · `GeschossDuplikat` (32) · `dupliziereGeschoss()` (43) |
| **2 · Daten** | `resources/planner/hausplaner/app/dashboard/geschossStapel.ts` | 104 | `StapelEintrag` (22) · `Stapel` (34) · `hoehenLabel()` (51) · `stapel()` (66) · `kurzfassung()` (94) · `nachbar()` (100) |
| **3 · Oberfläche** | `resources/planner/hausplaner/app/dashboard/GeschossFlaeche.tsx` | 173 | `GeschossFlaeche()` (56) |

## Die Grenze, wörtlich: kein Schreibpfad

> *„**Kein Schreibpfad, keine Szene-Mutation**; das Ergebnis füttert die Commands (`ADD_LEVEL` +
> `ADD_NODE` + `ADD_ROOF`)."* — `geschossVorlage.ts:7-8`

**`dupliziereGeschoss()` gibt ein Datenpaket zurück und fasst die Szene nicht an.** *Wer die Befehle
daraus bildet, entscheidet die Anwendungsschicht* — heute `app/sammelBefehle.ts`
(`befehleGeschossDuplizieren`), seit A-31 als **eine** Liste, damit ein Undo das ganze Geschoss
zurücknimmt und nicht eine Wand.

**Dieselbe Trennung gilt für Schicht 2:** *„Rein — nimmt Daten, gibt Daten, verändert nichts"*
(`geschossStapel.ts:62`), und *„Kein neuer Zustand. Diese Datei liest `levels` und die aktive id;
welches Geschoss aktiv ist, weiß allein `setActiveLevel` im Store"* (`:17-18`).

## Die Generics sind eine AUSSAGE, keine Verzierung

```ts
export function dupliziereGeschoss<N extends NodeBasis, R extends RoofBasis>(…)

interface NodeBasis { id: string; levelId: string; type: string; hostWallId?: string }
interface RoofBasis { id: string; levelId: string }
```

> **Die Geometrieschicht arbeitet gegen MINDESTANFORDERUNGEN, nicht gegen das Szenendokument.** *Sie
> kennt von einem Knoten vier Felder und von einem Dach zwei — mehr braucht das Kopieren nicht.*
> **Damit kann sie nicht versehentlich ins Szenendokument greifen: was sie nicht kennt, kann sie
> nicht anfassen.** *Die Trennung ist im Typsystem verankert und nicht in einer Absprache.*

**Und `NodeBasis`/`RoofBasis` sind ausdrücklich NICHT exportiert** (`:20`, `:27`) — *sie sind die
Innenseite des Vertrags. Ein Aufrufer nennt seinen eigenen Typ und bekommt ihn zurück (`N[]`,
`R | null`); er muss nichts umdeuten.*

## Verarbeitung — `dupliziereGeschoss()`, Schritt für Schritt am Code

```text
1. neues Level        elevation = quelle.elevation + defaultWallHeight + floorThickness   (:54)
                      sortOrder = quelle.sortOrder + 1                                     (:57)
                      -> ein Stockwerk HOEHER, und die Reihenfolge zieht mit
2. id-Karte           fuer JEDEN Knoten eine frische id, VOR dem Kopieren                  (:60-63)
                      -> zwei Durchlaeufe, weil eine Oeffnung die neue id ihrer Wand
                         braucht, die im selben Durchlauf noch nicht existierte
3. Knoten kopieren    { ...n, id: neu, levelId: neuesLevelId }                              (:66)
   id-REMAP           hostWallId !== undefined  ->  idMap.get(hostWallId)                  (:67-71)
                      Wirtswand NICHT mitkopiert  ->  undefined (Referenz fallen lassen)
4. Dach               { ...roof, id: neu, levelId: neuesLevelId }  oder null               (:75)
```

> **Schritt 2 ist der Grund für die zwei Durchläufe, und er ist der ganze Trick.** *Eine Öffnung
> kann an einer Wand hängen, die in der Liste **später** kommt. Wer in einem Durchlauf kopiert und
> remappt, findet die neue Wand-id noch nicht — und schreibt die alte.* **Genau der Schaden aus
> `1-ZWECK`.**

## Ausgabe

| Was | Typ | Wohin |
|---|---|---|
| `level` | `LevelVorlage` | → `ADD_LEVEL` |
| `nodes` | `N[]` (neue ids, remappte `hostWallId`) | → je `ADD_NODE` |
| `roof` | `R \| null` | → `ADD_ROOF`, wenn vorhanden |
| `Stapel` | Daten für die Anzeige | → `GeschossFlaeche`, `palette.ts`, `HausplanerApp` |

## Kommando (für Rückgängig)

- **Namen:** `ADD_LEVEL`, `ADD_NODE`, `ADD_ROOF` — **W-06 bringt keinen eigenen Befehl mit.**
- **Bündelung:** **ja, seit A-31.** *`befehleGeschossDuplizieren()` baut die ganze Liste, und
  `executeCommands()` führt sie in **einem** `produceWithPatches` mit **einem** Historien-Eintrag
  aus.* **Vorher waren es `N+2` Undo-Schritte für ein Geschoss** — Geschoss, jede Wand einzeln, Dach.
- **Zurücknehmen:** ein Undo, über die inversen Patches.

## Schichtzuordnung

- **Ändert Schicht 1 (Domäne):** *nein* — kein Schreibpfad, kein Schema-Eingriff.
- **Rechnet in Schicht 2 (Geometrie):** **keine F-Nummer.** Siehe `3-FORMELN`.
- **Lebt in Schicht 3 (Anwendung):** `geschossStapel.ts`, angebunden über `HausplanerApp.tsx`,
  `palette.ts`, `Kopfrahmen.tsx`.
- **Zeigt sich in Schicht 4/5:** `GeschossFlaeche.tsx` — Knopf mit Kurzfassung, dahinter der Stapel
  von oben nach unten.
