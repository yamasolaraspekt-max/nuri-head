# W-11 · Maß und Bemaßung — GRENZEN

## Entartete Eingabe bei `masskette()` — am Code gelesen

`masskette(werte, toleranz = 1)` (Z.29):

| Eingabe | Ergebnis | warum |
|---|---|---|
| leere Liste | **keine Segmente** | die Schleife beginnt bei `i = 1` |
| ein einziger Wert | **keine Segmente** | ein Punkt hat keinen Abstand zu sich |
| zwei Werte näher als `toleranz` | **ein Punkt, kein Segment** | *„verhindert 0-Segmente, z. B. wenn zwei Wände denselben Eckpunkt teilen"* (Z.25-27) |
| unsortierte Werte | **sortiert** | `sort((a,b) => a-b)` vor der Kettenbildung |
| Kommazahlen | **gerundet** | `Math.round` — mm-Integer-Welt |

**Die Toleranz ist standardmäßig 1 mm** und ein Parameter, kein fester Wert.

## Was `istBrauchbareLaenge()` prüft — vollständig

```text
typeof laengeMm === 'number'   kein Text, kein undefined
Number.isFinite(laengeMm)      kein NaN, kein Infinity
laengeMm > 0                   NULL ist ausgeschlossen, negativ auch
```

Die Begründung steht daneben: *„Null ist keine Länge und negativ ist keine Richtung: beides wäre kein
Punkt, sondern ein stehengebliebener Zug."* (`masseingabe.ts:37-38`)

## `MassPunkt` ist ZWEIMAL definiert — und das ist heute Absicht

```text
resources/planner/hausplaner/geometry/masskette.ts:9     export interface MassPunkt { x: number; y: number }
resources/planner/hausplaner/geometry/masseingabe.ts:25  export interface MassPunkt { x: number; y: number }
```

**Beide sind heute identisch** — Feld für Feld nachgelesen, nicht angenommen. Der Grund für die
Doppelung steht im Code: *„Bewusst lokal: dieses Modul kennt keine Szene."* (`masseingabe.ts:24`)

**Und hier wird es gefährlich:** ändert eine Seite — ein `z`, ein optionales Feld, eine Einheit —,
dann **divergieren sie stumm**, weil **kein Import sie verbindet**. Kein Übersetzerfehler, keine
Warnung; erst die Zahlen stimmen nicht mehr.

> **Weder übersehen noch voreilig aufräumen.** Ein Import von `masskette` nach `masseingabe` würde
> die Unabhängigkeit der Eingabeschicht aufgeben, die hier ausdrücklich gewollt ist. *Wer sie
> zusammenlegt, entscheidet etwas — er räumt nicht auf.*

## Braucht die Bemaßung die Auswahl (W-13)? — GEMESSEN: nein

| Messung | Ergebnis |
|---|---|
| `auswahl`/`select`/`markiert` in `bemassung.ts` und `masskette.ts` | **0 Treffer** |
| Signatur `bemassung()` (Z.52-56) | `waende`, `oeffnungen`, `toleranz` — **kein Auswahl-Parameter** |
| Aufrufstelle `HausplanerApp.tsx:1268` | übergibt **alle** Wände und **alle** Öffnungen |

**Das Register behauptet „W-11 braucht W-13". Der Code trägt das nicht.** Die Bemaßung rechnet über
den ganzen Grundriss, unabhängig davon, was ausgewählt ist. *Nicht korrigiert, sondern gemeldet —
die Zuordnung gehört dem Planner.*
