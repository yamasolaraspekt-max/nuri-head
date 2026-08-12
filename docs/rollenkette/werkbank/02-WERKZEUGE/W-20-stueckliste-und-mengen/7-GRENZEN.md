# W-20 · Stückliste und Mengen — GRENZEN

> **Dieses Blatt ist Pflicht.**
> Der teuerste Fehler des Projekts bisher: ein Dach, das bei nicht-rechteckiger
> Kontur unsichtbar verschwand statt eine Absage zu geben. Die Domäne verweigerte
> korrekt — der Renderer schluckte die Absage mit `catch { continue; }`.
> **Ein Werkzeug ohne benannte Grenze baut genau diesen Fehler wieder ein.**

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| **Ziegelmenge** | es gibt sie nicht — gemessen, siehe unten | in der Liste fehlt die Zeile schlicht |
| **Lattweite** (Abstand der Latten) | andere Frage, anderes Werkzeug: **W-21L / F-053** | — |
| Bauholz in **m³** | W-20 summiert **Längen**; Querschnitte kommen aus W-21 | — |
| **Meldung bei kaputter Länge** | `gueltigeLaenge` macht daraus **still eine 0** | **nichts — und das ist der Befund unten** |
| Stäbe, die keiner Art zuzuordnen sind | fallen durch alle drei Zweige (`:52-61`) | zählen nirgends, ohne Hinweis |

## Die einzige echte Lücke: die Ziegelmenge — gemessen, nicht geschätzt

```text
in resources/planner/hausplaner/geometry/ gesucht:
  'stueck.*m2'   0 Treffer     <-  Stueck je m2 gibt es nicht
  'bedarf'       1 Treffer     <-  und der ist eine Gaubenbemerkung, keine Menge
```

> **Und die Gegenprobe, damit niemand die falschen Treffer für eine Mengenrechnung hält:**
> *`ziegel` hat **16** Treffer — aber als **TYP** (`RoofCovering` in `dachformVorlagen`). `deckung`
> hat **79** — davon der erste eine **LASTannahme** in `sparrenBerechnung`, also Deckung als
> **Gewicht**.* **Wer diese 95 Treffer für eine Mengenrechnung hält, sucht falsch.**

**Die Zieladresse, damit die Lücke nicht verwaist:**

```text
Ziegelmenge (Stk)  =  Dachflaeche  ×  Bedarf_Stk_m2
                      └─ F-011        └─ W-23, Spalte 28/29 (Bedarf_min/max_Stk_m2)
```

*Beide Faktoren liegen vor: **F-011** über W-08, **W-23** seit dem 12.08. `BESCHRIEBEN` mit Herkunft
je Wert.* **Was fehlt, ist die Multiplikation — und sie gehört in einen eigenen Auftrag.** *Ein
ENTWORFEN-Teil in einem BESCHRIEBEN-Blatt wäre die Vermischung zweier Stufen, die Yamas Legende
ausdrücklich trennt.*

## Der stille Nullwert — die Grenze, die niemand sieht

**`gueltigeLaenge` (`holzMengen.ts:40-42`) macht aus jeder ungültigen, unendlichen oder negativen
Länge eine `0`.** *Das ist richtig: eine Stückliste, die wegen eines kaputten Stabes gar nichts
liefert, ist unbrauchbarer als eine, die ihn auslässt.*

> **Aber niemand erfährt es.** *Es gibt keine Meldung, keine Zählung, keinen Hinweis — der Stab
> verschwindet aus der Summe, und die Liste sieht vollständig aus.* **In einer Stückliste, aus der
> ein Angebot wird, ist eine stille Null die unangenehmste Zahl von allen.**
>
> *Das ist **keine** Absagekette im Sinne des Dach-Vorfalls — hier wird nichts geworfen und nichts
> geschluckt. **Es ist die leisere Verwandte davon: ein Ergebnis, das eine Auslassung nicht
> mitteilt.*** **Als Posten benannt, nicht in diesem Auftrag behoben.**

## Die Absagekette

**Entfällt — dieses Werkzeug sagt nie ab.** *Es gibt keinen Fehlerpfad: eine fehlende Liste ergibt
vier Nullen (`:49`), eine kaputte Länge ergibt `0` (`:41`).* **Genau deshalb steht der stille
Nullwert oben als eigener Posten: wo es keine Absage gibt, muss die Auslassung sichtbar sein.**

## Fänger-Prüfung

- [x] Kein `catch { }` ohne Weiterreichen — *es gibt kein `catch` in der Datei*
- [x] Kein stilles `return` bei ungültiger Eingabe — *es wird `0` zurückgegeben, dokumentiert (`:18`)*
- [ ] **Offen:** eine Auslassung wird gemeldet statt nur bereinigt

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|
| Summen in lfm | **keine** — es wird addiert, nicht umgerechnet | — |
| ausgelassene Stäbe | je Stab die volle Länge | sobald **ein** Stab eine kaputte Länge trägt |

## Was später kommen könnte

- **Die Ziegelmenge** — beide Faktoren liegen vor, eigener Auftrag.
- **Ein Hinweis auf ausgelassene Stäbe** — Zahl statt Stille.
- **Bauholz in m³** — braucht Querschnitte aus W-21.
- **Andere Deckungsarten als Ziegel** — die Quelle trägt heute nur Braas-Modelle (W-23).
