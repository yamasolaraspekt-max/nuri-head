# W-20 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck | Zustand |
|---|---|---|---|
| 1 Domäne | — | die Holzliste kommt aus der Engine, W-20 ändert sie nicht | entfällt |
| **2 Geometrie** | **`resources/planner/hausplaner/geometry/holzMengen.ts`** | **die Aggregation — 64 Zeilen, 3 Exporte** | **gebaut, getestet** |
| 3 Werkzeug | — | kein Modus, kein Kommando | entfällt |
| 4 Darstellung | — | W-20 zeichnet nicht | entfällt |
| 5 Oberfläche | Material-/Holzliste | **verbraucht** die vier Kennzahlen | vorhanden, hier nicht Gegenstand |

> **Der Code steht im Repo, nicht in diesem Ordner.** Hier liegen nur Schnittstellenbeschreibung
> und der eine Ausschnitt, auf den es ankommt.

## Schnittstelle — abgelesen, nicht entworfen

```ts
// holzMengen.ts:23-27
export interface HolzStueck { type?: string; name?: string; laenge?: number; }

// holzMengen.ts:29-38
export interface HolzMengen {
  sparrenLaenge: number;   // :31  Summe der echten Sparrenlängen (lfm), OHNE Konterlatten
  konterLaenge: number;    // :33  Summe der echten Konterlattenlängen (lfm)
  lattenLaenge: number;    // :35  Summe der echten Traglattenlängen (lfm)
  sparrenAnzahl: number;   // :37  Anzahl echter Sparren (Stück)
}

// holzMengen.ts:44
export function holzMengenAusListe(
  holzliste: ReadonlyArray<HolzStueck> | undefined | null
): HolzMengen
```

**Drei Exporte, gezählt und gelesen: `:23`, `:29`, `:44`.**

## Kernstelle — die Typunterscheidung, und warum sie so aussieht

```ts
// holzMengen.ts:52-61
if (stk.type === "latte") {
  lattenLaenge += L;
} else if (stk.name === "Konterlatte") {
  konterLaenge += L;
} else if (typeof stk.name === "string" && (stk.name.startsWith("Sparren") || stk.name.startsWith("Schiftsparren"))) {
  // EA28: Schiftsparren sind Gemeinsparren (nur verkürzt/angeschnitten) — sie MÜSSEN hier mitzählen,
  // sonst fallen die an Kehle/Grat geclippten Sparren aus Bauholz-m³ und Lohn heraus (Unter-Count).
  sparrenLaenge += L;
  if (L > 0) sparrenAnzahl += 1;
}
```

*Die Begründung steht als Kommentar **im Code** (`:57-58`) und nicht nur hier — deshalb ist sie
zitierbar statt behauptet.*

## Die Gültigkeitsprüfung

```ts
// holzMengen.ts:40-42
function gueltigeLaenge(l: unknown): number {
  return typeof l === "number" && Number.isFinite(l) && l > 0 ? l : 0;
}
```

**Zusage aus dem Dateikopf (`:18`): „ungültige/negative Längen werden zu 0; niemals
NaN/Infinity/negativ."** *Sie ist der Grund, warum die Aggregation an keiner kaputten Zahl
zerbricht — und zugleich die Stelle, an der ein Fehler still verschwindet.*

## Prüfstand

```text
resources/planner/hausplaner/__tests__/holzMengen.test.ts
  test-Bloecke     6
  assert-Aufrufe  24        (grep -cE 'assert\.')
```

> **Zur Zahl, weil zwei im Umlauf sind:** *das Auftragsblatt nennt „sechs Testzusagen", der
> DoR-Beleg nennt „25 assert-Aufrufe".* **Gemessen: 6 Blöcke, 24 Assertions.** *Die 25 entsteht mit
> `grep -c 'assert'` — sie zählt die `import assert …`-Zeile mit.* **Alle drei Zahlen sind für ihr
> Muster richtig; die Zahl der Zusagen ist 24.**

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| `RoofEngine.holzliste` | die echten Stablängen (`:11`) | **ja**, einseitig — W-20 liest nur |
| **W-07 / W-08** | ohne Dach keine Holzliste | ja — Registerzeile nennt W-05 und W-08 als Voraussetzung |
| React · THREE | **nein, ausdrücklich** (`:19`) | rein, vollständig prüfbar |
| **W-23** | *nur für die noch fehlende Ziegelmenge* | ja, einseitig — heute nicht verdrahtet |
