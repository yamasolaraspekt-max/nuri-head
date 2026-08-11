# W-13 · Auswahl und Griffe — FUNKTION

## Vier Module, vier Fragen

| Modul | Z / Ausfuhren | beantwortet |
|---|---|---|
| `resources/planner/hausplaner/app/tools/auswahlModus.ts` | 98 / 7 | **Was tut ein Klick?** — ersetzen, hinzufügen, entfernen, umschalten |
| `resources/planner/hausplaner/app/tools/trefferSuche.ts` | 75 / 4 | **Was habe ich getroffen?** |
| `resources/planner/hausplaner/app/tools/auswahlDarstellung.ts` | 71 / 3 | **Wie sieht das Getroffene aus?** |
| `resources/planner/hausplaner/app/tools/auswahlUebersicht.ts` | 77 / 4 | **Was zeigt das Panel bei mehreren?** |
| | **321 / 18** | selbst nachgezählt |

## Der Modifikator-Vorrang — eine Kette, kein Schalterfeld

```text
auswahlModus.ts:42-47
  altKey              -> 'remove'
  ctrlKey oder metaKey -> 'toggle'
  shiftKey            -> 'add'
  sonst               -> 'replace'
```

**Die Reihenfolge ist der Vertrag.** *Wer `Alt`+`Shift` drückt, bekommt `remove` — nicht beides und
nicht das zuletzt Geprüfte.*

## Was die vier Modi tun

| Modus | Ergebnis | Zeile |
|---|---|---|
| `replace` | nur der Treffer, er wird primär | 67-68 |
| `add` | anhängen, **wenn nicht schon drin**; primär wird der Treffer | 69-71 |
| `remove` | entfernen | 72-73 |
| `toggle` | drin → entfernen, sonst anhängen | 74-75 |

**Der Primärbegriff ist eigen und wichtig:** wird das **primäre** Objekt entfernt, rückt das
**zuletzt** verbliebene nach — sonst bleibt der Primärstand, wie er ist (`auswahlModus.ts:84-85`).
*Ein „Primär" ist nicht einfach das erste; es wird verwaltet.*

## Der Treffer: oben schlägt nah

```text
trefferSuche.ts:56-65
  1. nur sichtbare
  2. nur waehlbare (waehlbar !== false — undefined zaehlt als waehlbar)
  3. nur innerhalb der Toleranz
  4. sortiert: zuerst ZEICHENREIHENFOLGE absteigend, DANN Distanz aufsteigend
```

**Bei mehreren gleich nahen gewinnt nicht das nähere, sondern das obere.** Erst bei gleicher
Zeichenreihenfolge entscheidet die Distanz. *Das ist die Regel, die ein Canvas bedienbar macht.*

`besterTreffer()` (44) ist der erste dieser Liste; `trefferInReihenfolge()` (55) ist **öffentlich**,
weil ein späterer „Durchklicken"-Modus genau diese Liste braucht — *„und weil sich die Sortierung so
einzeln prüfen lässt"* (Z.69-72).
