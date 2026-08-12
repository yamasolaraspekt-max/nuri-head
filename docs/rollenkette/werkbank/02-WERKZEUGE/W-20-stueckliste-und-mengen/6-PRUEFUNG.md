# W-20 · Stückliste und Mengen — PRÜFUNG

> **Regel: jedes Kriterium muss VOR dem Bau wirksam rot sein.**
> Ein Kriterium, das schon grün ist, bevor gebaut wurde, prüft nichts.

**Besonderheit dieses Blattes: der Kern ist GEBAUT.** *Die Kriterien unten sind deshalb keine
Rot-Lagen, sondern **abgelesene Zusagen** — sie stehen als Tests im Repo und laufen grün.* **Die
Rot-Lage gehört zur Lücke am Ende dieses Blattes, nicht zum Bestand.**

## Vorhandene Zusagen — gezählt und gelesen

```text
resources/planner/hausplaner/__tests__/holzMengen.test.ts
  6 test-Bloecke  ·  24 assert-Aufrufe   (grep -cE 'assert\.')
```

| Nr | Zusage | Steht sie? |
|---|---|---|
| **K-1** | Summe je Bauteilart aus der echten Liste | **ja** |
| **K-2** | **Schiftsparren zählen als Sparren mit** — `holzMengen.ts:56-58` | **ja** — die Zeile mit der Begründung |
| **K-3** | Ungültige, negative, unendliche Längen ergeben **0** statt `NaN` | **ja** — `gueltigeLaenge`, `:40-42` |
| **K-4** | `undefined`/`null` als Liste ergibt vier Nullen statt eines Fehlers | **ja** — `:49` |
| **K-5** | `sparrenAnzahl` zählt nur Stäbe mit `L > 0` | **ja** — `:60` |
| **K-6** | Reinheit: keine React-/THREE-Abhängigkeit | **ja** — Dateikopf `:19` |

## Fangprobe (Mutationsprobe)

*Was müsste man kaputt machen, damit die Zusagen es merken:*

| Mutation | Muss erkannt werden von |
|---|---|
| `startsWith("Schiftsparren")` entfernen | **K-2** — und das ist die teuerste: sie erzeugt einen **Unter-Count**, also einen Fehlbetrag im Angebot |
| `gueltigeLaenge` durch `Number(l)` ersetzen | **K-3** — `NaN` wandert in die Summe |
| `if (L > 0)` bei der Anzahl streichen | **K-5** — Nullstäbe zählen als Stück |
| Die Reihenfolge `type` vor `name` umdrehen | *heute von keiner Zusage erfasst* — **siehe Lücke unten** |

## Automatische Tests

| Datei | Prüft |
|---|---|
| `resources/planner/hausplaner/__tests__/holzMengen.test.ts` | K-1 bis K-6, 24 Assertions in 6 Blöcken |

## Die Lücke — hier ist die Rot-Lage

**Für die Ziegelmenge gibt es heute nichts, auch keinen Test.**

```text
gemessen in geometry/:
  'stueck.*m2'   0 Treffer      <- Stueck je m2 gibt es nicht
  'bedarf'       1 Treffer      <- und der ist eine Gaubenbemerkung
```

| Nr | Kriterium für den späteren Bau | Rot-Beleg heute |
|---|---|---|
| **K-7** | Ziegelmenge = Dachfläche (F-011) × `Bedarf_Stk_m2` (W-23) | **0 Treffer**, siehe oben |
| **K-8** | Jeder übernommene `Bedarf`-Wert führt seine Herkunft mit | es gibt keinen |

*Beide gehören in einen **eigenen** Auftrag: dieses Blatt ist eine Ablesung, und eine Vorgabe darin
wäre die Vermischung zweier Stufen.*

## Sichtprüfung

- [ ] Die Kennzahlen in der Material-/Holzliste stimmen mit der Zeichnung überein
      — **1440 px · 1024 px · 375 px**

> *Das ist die einzige Sichtprüfung, die für dieses Werkzeug zählt: **stimmen Liste und Zeichnung
> nicht überein, ist die zweite Wahrheit zurück**, gegen die es gebaut wurde.*

## Bestandsprobe

- [ ] Ein vor der Änderung gespeichertes Dokument lädt danach unverändert

*Betrifft W-20 nicht unmittelbar — die Funktion ist rein und schreibt nichts.*
