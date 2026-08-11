# W-13 · Auswahl und Griffe — GRENZEN

## Kein Treffer — zwei verschiedene Fälle, beide gebaut

| Fall | Was passiert | Fundstelle |
|---|---|---|
| Klick ins Leere **ohne** Modifikator | Auswahl wird **aufgehoben** | `resources/planner/hausplaner/app/tools/auswahlModus.ts:93-95` |
| Klick ins Leere **mit** Modifikator | Auswahl **bleibt unverändert** | dieselbe Stelle |
| kein Kandidat in Toleranz | `besterTreffer()` liefert **`null`** | `resources/planner/hausplaner/app/tools/trefferSuche.ts:45-46` |

**`null` und nicht ein leerer Kandidat** — *das ist die ehrliche Antwort: es gibt keinen Treffer, und
der Aufrufer muss es entscheiden.* Für „nichts ausgewählt" gibt es genau eine Fassung:
`LEERE_AUSWAHL` (Z.98) — *„eine Stelle, damit ‚nichts ausgewählt' überall dasselbe heißt."*

## Mehrere gleich nahe Treffer — OBEN gewinnt, nicht NAH

```text
trefferSuche.ts:60-65
  sort:  zuerst  Zeichenreihenfolge ABSTEIGEND   (was oben liegt, gewinnt)
         danach  Distanz AUFSTEIGEND             (nur bei Gleichstand)
```

**Die Distanz ist das zweite Kriterium, nicht das erste.** *Wer die Sortierung „nächstes Objekt
gewinnt" nennt, beschreibt den Ausnahmefall.*

## Der Filter, der leicht falsch gelesen wird

```text
trefferSuche.ts:58   .filter((k) => k.waehlbar !== false)
```

**Nicht `=== true`.** Ein Kandidat **ohne** gesetztes `waehlbar` ist **wählbar**. *Wer das Feld
einführt und irgendwo vergisst, ändert nichts — wer die Prüfung auf `=== true` umstellt, macht
stillschweigend alles unwählbar, was es nicht ausdrücklich setzt.*

## Zoom 0 — keine Division durch null

```text
trefferSuche.ts:72-74   zoom > 0 ? pixel / zoom : pixel
```

**Die Toleranz bleibt dann in Pixeln stehen** — sie ist nicht richtig, aber sie ist endlich.
*Eine Absage, die gebaut ist und nicht vergessen wurde.*

## Unbekannter Typ in der Übersicht

`benenne()` (`resources/planner/hausplaner/app/tools/auswahlUebersicht.ts:73-77`) gibt bei unbekanntem Typ **den Typnamen
selbst** zurück. **Es erfindet keine Bezeichnung** — der Anwender sieht `wall` statt „Wand", aber er
sieht nichts Falsches.

## Was dieses Werkzeug NICHT tut

| Fall | wohin es gehört |
|---|---|
| Verschieben, Spiegeln, Drehen | **W-14** — `geometry/editierGeometrie.ts` ist Ausschluss |
| Abstände messen | die `distanz` wird **mitgeliefert**, nicht berechnet |
| Griffe zeichnen | Darstellung ist **Zustand als Daten**, kein Markup (`auswahlDarstellung.ts:3-5`) |

## Und die dünnste Stelle

**Null dedizierte Zusagen** bei 321 Zeilen — siehe `6-PRUEFUNG`. *Das ist keine Grenze des Werkzeugs,
sondern eine Grenze dessen, was man über es weiß.*
