# VOTUM Z1-E0-1 — Höhenkette, eine Wahrheit

**ABGENOMMEN (BROWSER) — fünf von fünf Kriterien.**

| Feld | Wert |
|---|---|
| Blattstand | `0d10461d` (mit beiden Mangelanzeigen des Planners) |
| Bau | `ad2ac724` · Ausgang `3a4aafa1` |
| Mein Stand | `726dc026` |
| gelesen_bis | 2026-08-22T22:14:28+02:00 |
| Bühne | Port 8106, Chrome **headful**, DB am Kindprozess `ticket_testing`, DB-Lease Token 35 |

## Die zwei Zahlenfragen — entschieden, wie vorab angekündigt

Der Planner hat **zwei Mängel seines eigenen Blattes** gemeldet und die Entscheidung mir
überlassen, ohne die Zielzahlen nachträglich passend zu machen. Der Dirigent hat um 22:01
(Punkt 4) geklärt: *„EINE Wahrheit = EINE Quelle (`geometry/hoehenkette.ts`), nicht EIN
Funktionsname."* Das deckt sich mit der Linie, die ich um 20:22 und 20:56 **vorab** offengelegt
hatte. Ich entscheide beide Fragen so:

**Frage 1 — „0 Treffer" auf `elevation + …defaultWallHeight`: ERFÜLLT.**
Gemessen im Arbeitsbaum, mit dem Messbefehl des Blattes (normales `grep`, das `\b` kennt):

```
Treffer ausserhalb hoehenkette.ts (ohne __tests__):  2
  renderers/three-d/szene.ts:453   // (level.elevation + defaultWallHeight). RUECKSEITEN-CULLING …
  domain/scene.types.ts:327        traufhoeheMm: number;   // Default: level.elevation + defaultWallHeight
AKTIVE Rechnungen darunter:        0
```

**Beide Treffer sind Kommentare** — der eine vorangestellt, der andere nachgestellt hinter einer
Felddeklaration. *Mein erster Filter stufte den zweiten falsch als aktive Rechnung ein, weil er nur
den Zeilenanfang prüfte; die Zeile beginnt mit `traufhoeheMm: number;` und trägt das Muster erst im
Kommentar dahinter.* Und **vor** dem Bau waren es **fünf** Dateien — der Bau hat drei echte
Rechnungen beseitigt und keine hinzugefügt. Die Absage-Regel („eine vierte Funktion daneben") ist
gewahrt.

**Frage 2 — „drei Aufrufer": ERFÜLLT, im Sinne der Wirkung.**

```
naechsteEtageElevationMm(   Kopfrahmen.tsx:175 · geschossVorlage.ts:75          2 Leser
deckenOberkanteMm(          HausplanerApp.tsx:1013                              1 Leser
                            (Import Zeile 115: from '../geometry/hoehenkette')
```

**Drei Stellen lesen aus derselben einen Quelle.** Der im Kriterium genannte dritte Ort
(`HausplanerApp.tsx`) setzt die **Traufhöhe** — dafür ist `deckenOberkanteMm` die richtige
Funktion, ein dritter direkter Aufruf von `naechsteEtageElevationMm` wäre dort fachlich falsch.
Das hat der Planner selbst festgestellt.

**Und die zwei Exporte sind EINE Kette, nicht zwei Wahrheiten** — selbst gemessen,
`hoehenkette.ts:58`:

```
return Math.round(deckenOberkanteMm(level) + deckeDickeMm);
```

Der zweite Export ruft den ersten. Die Rechnung `elevation + defaultWallHeight` steht genau
**einmal**, in `deckenOberkanteMm`.

## Z1-E0-1-b · Rot-Probe 2700 → 2740, im Browser — ERFÜLLT

Fixture `?fixture=etagen-hoehenkette` (EG `defaultWallHeight` 2500, Decke `dickeMm` 240),
Bedienweg „+ Geschoss" im Geschoss-Stapel:

```
NEU   STAPEL · 2 GESCHOSSE · Geschoss 2  +2 740 mm
ALT   STAPEL · 2 GESCHOSSE · Geschoss 2  +2 700 mm
```

**Beide Zahlen unabhängig nachgerechnet:** 0 + 2500 + 240 = **2740** (Kette liest die Decke);
0 + 2500 + 200 = **2700** (alter Weg addiert `floorThickness`). Der alte Rechenweg ist im
Vorstand sichtbar — `Kopfrahmen.tsx:172`: `oben.elevation + oben.defaultWallHeight + oben.floorThickness`.

**Zur Rot-Probe, offengelegt:** Die Fixture `etagen-hoehenkette` gibt es im Vorstand `3a4aafa1`
noch nicht (0 Treffer). Ich habe deshalb **nur die Fixture** in den Wegwerf-Klon des Vorstands
eingespielt und den **alten Rechenweg unangetastet** gelassen — sonst hätte ich zwei Dinge
zugleich verändert und könnte nicht sagen, welche Zahl warum wandert. Bildbelege
`belege/Z1-E0-1-geschoss-gruen.png` und `-alt.png`.

Die Absage-Regel ist eingehalten: gemessen wurde im **Bedienweg „Geschoss anlegen"**, nicht in
einem Testlauf.

## Z1-E0-1-c · Bestand bitgleich, wo keine Decke modelliert ist — ERFÜLLT, AUSGELÖST

Nicht am Code abgelesen, sondern gefahren — dieselbe Fixture **ohne** Decke (`wand-schichten`),
beide Stände:

```
NEU   Geschoss 2  +3 000 mm
ALT   Geschoss 2  +3 000 mm
```

**Bitgleich.** Nachgerechnet: 0 + 2800 (`EG.defaultWallHeight`) + 200 (`floorThickness`) = 3000.
Der Rückfall im Code trägt das: `const deckeDickeMm = decke ? decke.dickeMm : level.floorThickness;`
— ohne Decke rechnet die neue Kette **denselben** Wert wie die alte. Die Vereinheitlichung ändert
nur dort etwas, wo heute falsch gerechnet wird; genau das verlangt (c).

## Z1-E0-1-d · Lieferung grün und vollständig — ERFÜLLT

```
npm run test:hausplaner   1785 / 1785 · 0 fail
npm run tsc:hausplaner    Rueckgabe 0
Buendel im Bau-Diff       1   (public/hausplaner/hausplaner.js, mitcommittet)
```

## Z1-E0-1-e · Kein Modell-, kein Schema-Diff — ERFÜLLT

`git diff --name-only 3a4aafa1..ad2ac724 -- domain/` → **0 Dateien**. Der Bau berührt neun Pfade,
keiner davon in `domain/`.

## Anerkennung

Zwei Mangelanzeigen gegen das eigene Blatt, beide gemessen, beide ohne Torpfostenverschieben —
und der Hinweis, dass der Namensfehler (`berechneHoehenkette`) aus der Vorbedingung stammt und
nicht aus dem Kriterium. Ich habe alle drei Punkte nachgemessen; sie stimmen. Ein Blatt, dessen
Autor seine eigenen Lücken meldet, macht die Abnahme schneller, nicht langsamer.

**Ball:** Integrator (Transport).
