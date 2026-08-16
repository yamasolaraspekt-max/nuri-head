# W-30 · Flachdach-Aufbau — GRENZEN

## ZWEI Flachdächer, zwei Definitionen, kein Übergang

```text
INSEL      dachVorlage.ts:23        { form: 'flach', neigungGrad: 0 }
           EigenschaftenPanel:258   Neigung 0 .. 89 frei
           Klemmung                 KEINE (clampPitchGrad: 0 Verbraucher in app/)

VORLAGEN   dachformVorlagen.ts:497  category 'flat' -> clampPitchGrad(pitch, 1.5, 8)
                                    + Warnung PITCH_GEKLEMMT
Beruehrung der beiden:              KEINE
```

> ***Das eine hat als Vorgabe genau die Neigung, gegen die das andere klemmt.*** **0° ist der Fall
> „Wasser steht"** — *und `spannweiteHinweis` nennt ihn beim Namen: „Durchbiegung/Pfützenbildung
> beachten".*
>
> **Welche der beiden Definitionen gelten soll, ist eine Fachentscheidung und keine Ablesefrage.**
> *Festgehalten ist, dass es zwei gibt und dass keine Zusage sie vergleicht.*

## Der Hinweistext nennt eine andere Zahl als die Klemmung

```text
spannweiteHinweis (:1599)   „Gefaelle >= 2 % (Richtwert)"     =  1,15 Grad
clampPitchGrad     (:497)   min 1,5 Grad                      =  2,62 %
```

> **Der Code ist strenger als sein eigener Text.** *Wer 2 % eingibt, wird auf 1,5° hochgeklemmt und
> bekommt `PITCH_GEKLEMMT`.* **Im Ergebnis ist das die sichere Richtung; in der Auskunft ist es ein
> Widerspruch** — *der Anwender liest 2 % als zulässig und erlebt eine Warnung.*
>
> *Die Umrechnung Grad↔Prozent gibt es im Code nirgends; der einzige Prozentwert steht in einem
> Freitext. **So konnte die Abweichung entstehen, ohne dass sie jemandem auffällt.***

## Abläufe: im Titel, nirgends im Code

```text
'ablauf'/'abläufe' als GEGENSTAND im Inselbaum:   nichts
  (die Treffer in StartView, werkzeugThemen, EigenschaftenPanel u. a.
   sind das deutsche Wort „Ablauf" im Sinne von Vorgang)
```

> ***Die klassische Flachdach-Rechnung — Einzugsfläche je Ablauf — fehlt vollständig.*** **Nicht
> „unfertig", sondern nicht begonnen.** *Als Grenze benannt.*

## Die Attika ist eine Zahl, die zu einem Strich wird

```text
:163  attika?: number   // m
:648  attika ? svgRect(24, 39, 84, 4, ...) : ''      im Sinnbild
Verbraucher ausserhalb dachformVorlagen.ts:  KEINER
```

> **Aus Metern wird `ja/nein`.** *Die Höhe wird erfasst und nirgends verwendet* — *keine
> Attika-Fläche, keine Abwicklung, keine Blechlänge.*

## Was hier NICHT gilt, obwohl es für andere Dächer gilt

```text
FLAGS_FLACH (:1355-1359)   alle DREIZEHN ZimmererFlags auf false
abbundhinweis (:1598)      „Tragdecke mit Gefaelledaemmung; kein Sparrendach."
```

> ***Die gesamte Holzbau-Rechnung aus W-21 und W-25 trägt hier nicht.*** *Eine Tragdecke hat keine
> Sparren, keine Pfetten, keinen Kehlbalken.* **Das ist keine Lücke, sondern eine fachliche
> Abgrenzung — und sie ist an der knappsten möglichen Stelle festgehalten: dreizehn `false`.**

## Nachbarschaft

```text
W-07   Dach aus Kontur      liefert die Flaeche; Flachdach ist dort category 'flat'
W-21   Sparren und Lattung  traegt hier NICHT (FLAGS_FLACH)
W-25   Pfetten/Kehlbalken   traegt hier NICHT
W-29   Dachdurchdringungen  gilt auch fuer Flachdaecher (dachAusschnitt kennt 'flach')
```
