# W-03 · Wand bearbeiten — PRÜFUNG

## Der Befund zuerst: die GEBAUTE Hälfte ist nicht verriegelt

**Über fünf Muster gemessen, alle in `__tests__/`:**

```text
setzeWandLaenge     0 Testdateien
WANDSTAERKEN        0
aktualisiereWand    0
'Mauerwerk'         0
'Wandstaerke'       1  -> bemassung.test.ts, und dort meint es die WANDDICKE
                          in der Bemassungskette (W-11), nicht dieses Feld
```

> ***Keine einzige Zusage hält die Wandbearbeitung.*** *Nicht die Material-Auswahl, nicht die
> Stärkenliste, nicht die Höhe — und vor allem nicht `setzeWandLaenge()`, das als einziges der vier
> Felder **rechnet** und den Endpunkt bewegt.*

**Was das konkret offen lässt:**

| ungesichert | was ein Fehler dort anrichtet |
|---|---|
| `setzeWandLaenge` verschiebt den **Endpunkt entlang der Achse** | eine vertauschte Richtung verschöbe den **Anfang** — die Wand wanderte, statt zu wachsen |
| die zwei Wächter `!(neu > 0)` und `len === 0` | ein leeres Feld ergäbe `NaN`, eine Wand ohne Ausdehnung eine Division durch null |
| der Sonderfall „(aktuell)" bei der Stärke | eine Wand mit 200 mm zeigte stillschweigend „240 mm" und bekäme sie beim ersten Klick |

> **Der letzte ist der leiseste und der teuerste:** *das Panel sähe richtig aus, und die Wand
> änderte sich, ohne dass jemand sie geändert hat.*

## Was die zwei vorhandenen Wächter WIRKLICH halten

| Wächter | Z | Zusagen | Zugriffsart |
|---|---|---|---|
| `__tests__/eigenschaftenPanel.test.ts` | 168 | 11 | **QUELLE** über `_zerlegteApp` (`teil`, `ohneKommentare`) |
| `__tests__/eigenschaftenPanelStil.test.ts` | 179 | 8 | **QUELLE** (`readFileSync` 3×) |

**Ihre elf Zusagen im Wortlaut — keine betrifft eine Wand:**

```text
K-04  der Sicht-Schalter kehrt den Zustand um, den er ANZEIGT
K-04  der Sperr-Schalter kehrt um UND zeigt seinen Zustand
K-04  vor dem Entsperren wird gefragt
K-04  jede Anbau-Eingabe schreibt in IHR eigenes Feld
K-04  die Anbau-Basis uebernimmt die vorhandenen Werte
K-04  die Schwere steht als SYMBOL und als TEXT — beides, nicht eins
K-04  der Pruefungs-Reiter zeigt die Pruefungen
K-04  jeder Reiter zeigt SEINEN Zustand — kein fester Wert
K-02  das Panel haelt keinen Zustand — der aktive Reiter bleibt in der Hauptfunktion
K-01  das Panel traegt 32 Inline-Zeilen — nach AUF-38-P1
K-01  das Markup steht NICHT mehr ein zweites Mal in der Hauptfunktion
```

> ***„K-04 (blind gewesen)" steht achtmal davor*** — *die Zusagen sind entstanden, nachdem
> Mutationen unbemerkt durchkamen.* **Sie sichern die Schalter, die Anbau-Felder und die Reiter.
> Die Wandfelder waren bei dieser Runde nicht dabei.**

**Und `K-01`/`K-02` sind Struktur-Zusagen:** *kein doppeltes Markup, kein Zustand im Panel.* **Sie
schützen die Datei, nicht ihre Fachlogik.**

## Die fehlende Hälfte ist naturgemäß ungeprüft

**Für `trimmen`, `verlaengern`, `versatz`, `teilen` und `verbinden` gibt es keine Zusagen** — *es
gibt sie ja nicht.* **Ihre Fundamente sind dagegen beide verriegelt:**

```text
geradenGeometrie   __tests__/geradenGeometrie.test.ts   (A-32)
executeCommands    __tests__/sammelBefehle.test.ts      (A-31)
```

> *Wer die fünf baut, findet die Rechnung und die Klammer bereits gesichert vor.* **Was er neu
> sichern muss, ist die Verdrahtung — und die Wandfelder, die heute schon offen sind.**
