# W-25 · Pfetten und Kehlbalken — PRÜFUNG

## Der Wächter: `holzBauteile.test.ts` — 75 Z., SECHS Zusagen, alle grün

**Selbst gefahren:**

```text
✔ Pfetten/Grat-/Kehlsparren werden getrennt + mit echter Länge summiert
✔ Reparatur 7 bleibt unberührt: Sparren/Konter/Latten unverändert aus derselben Liste
✔ Satteldach (Pfetten, keine Grate/Kehlen) -> Grat/Kehl bleiben 0
✔ Flachdach / leere Liste -> alles 0 (keine erfundenen Bauteile)
✔ ungültige/negative Längen -> ignoriert (kein NaN/Infinity/negativ)
✔ offene Holzbauteile sind dokumentiert (Wechsel/Schwelle/Mittelpfette/Schifter)
ℹ tests 6 · pass 6 · fail 0
```

### Die zwei, auf die es ankommt

> ***„Flachdach / leere Liste → alles 0 (keine erfundenen Bauteile)"*** — *das ist die Zusage, die
> zur Haltung des Moduls passt.* **Ein Aggregat, das bei leerer Eingabe irgendetwas ausgibt, ist
> gefährlicher als eines, das gar nichts kann.**

> ***„Reparatur 7 bleibt unberührt"*** — *eine Regressionszusage über die NACHBARSCHAFT: dieselbe
> Holzliste liefert auch Sparren, Konter und Latten (W-21), und die dürfen sich durch diese
> Aggregation nicht verändern.*

## ⚠ Und die sechste Zusage HÄLT DIE ÜBERHOLTE LISTE FEST

```text
holzBauteile.test.ts:71  test("offene Holzbauteile sind dokumentiert …")
                    :72    OFFENE_HOLZBAUTEILE.length >= 3
                    :73    .some(s => /Wechsel|Auswechslung/i.test(s))
                    :74    .some(s => /Mittelpfette/i.test(s))
```

> ***Wer den überholten Eintrag „Wechselholz … nicht eindeutig bestimmt" entfernt — weil
> `auswechslung.ts` ihn seit elf grünen Zusagen rechnet — macht DIESEN Test rot.***
>
> **Der Wächter schützt die Aussage, statt sie zu prüfen.** *Er fragt nicht „stimmt die Liste",
> sondern „steht der Eintrag noch da".* **Damit verteidigt er die Lücke gegen ihre eigene
> Schließung.**
>
> *Das ist kein Fehler des Testautors — zum Zeitpunkt des Schreibens war die Liste richtig.*
> **Es ist die Klasse „ein Wächter, der einen Zustand einfriert statt eine Eigenschaft zu prüfen"**
> *(F-06, dieselbe, wegen der `decke.test.ts:110-114` ausdrücklich die FLÄCHE prüft und nicht die
> Punktliste).*
>
> ***Eine Zusage, die stattdessen tragen würde:*** *„jeder Eintrag in `OFFENE_HOLZBAUTEILE` hat
> keinen Rechner" — dann fiele der Eintrag von selbst, sobald einer gebaut wird.* **Als Vorschlag
> benannt, nicht gebaut: eine Ablesung baut keine Tests.**

## Was KEIN Wächter hält

| ungeprüft | Folge |
|---|---|
| **die Aktualität der Liste** | im Gegenteil — `:73` friert einen Eintrag ein, der überholt ist |
| **`ZimmererFlags`** | dreizehn Flaggen je Dachform, kein Verbraucher, keine Zusage |
| **der Kehlbalken als Menge** | es gibt ihn als Flagge und nicht als Länge |
| **die Anzeige** von `OFFENE_HOLZBAUTEILE` | ob der Bericht sie zeigt, ist außerhalb der Insel und hier nicht gemessen |

**Alle Zahlen mit Träger:** *82 Z. und 4 Ausfuhren für `holzBauteile.ts`, 75 Z. und 6 Zusagen für
den Wächter, 152 Z. und 9 Ausfuhren für `schifterListe.ts`, 13 Flaggen in `ZimmererFlags`.*
