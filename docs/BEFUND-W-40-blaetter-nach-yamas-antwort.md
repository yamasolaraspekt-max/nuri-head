# Befund — Yamas Antwort überholt DREIZEHN Stellen in W-40s Blättern

```yaml
rolle: "generator"
art: "BEFUND, read-only — nichts geaendert, nichts gezogen"
gemessen_am: "12.08.2026"
stand: 50c93248
anlass: "Yama hat in 2e7504ec beide offenen Fachfragen entschieden. Beide standen in W-40s
         Blaettern als offene Frage — von mir, weil die Quelle sie nicht hergab."
warum_ich_nichts_aendere: "W-40 ist BETRIEBSBESTAETIGT; seine Blaetter sind das abgenommene
         Ergebnis. W-40/1 ist genau dafuer geschnitten (4c7ba68b) und steht beim plan-pruefer."
```

> **Der Grund für diesen Zettel ist H-8:** *eine Angabe steht an mehreren Stellen, und wer eine
> berichtigt, hinterlässt Widersprüche, die **beide belegt aussehen**.* **Hier sind es dreizehn
> Stellen in vier Blättern.**

## Entscheidung 1 — `review-required` ist keine Zahlenlücke

**Yamas Antwort:** *die vier und die drei liegen nicht auf derselben Achse, `4 + 3` muss nicht `8`
ergeben. `review-required` entspricht `checked`, das existiert samt Übergängen und ist der einzige
Weg nach `approved`.* **Drei der vier Stufen sind gebaut; `blocked` ist die einzige Erweiterung.**

**Was in meinen Blättern jetzt überholt ist — neun Stellen:**

```text
3-FORMELN.md:33     „4 + 3 = 7. Die achte faellt aus der Rechnung, und dieses Blatt
                     beantwortet nicht, warum."
6-PRUEFUNG.md:12    K-3 „Die Zahlenluecke ist gestellt, nicht beantwortet"
7-GRENZEN.md:48     Ueberschrift „Die Zahlenluecke: acht gegen sieben"
7-GRENZEN.md:54     „4 + 3 = 7,  nicht 8."
7-GRENZEN.md:56     „Die achte ist review-required …"
7-GRENZEN.md:61     „Entweder ist review-required bewusst nicht Teil der Gueltigkeitsachse,
                     oder die Zahl DREI ist zu niedrig."
7-GRENZEN.md:65-66  „4 + 3 = 7 ist der Hinweis, dass eine Angabe fehlt"
7-GRENZEN.md:106    Tabelle „Gehoert review-required zur Gueltigkeitsachse?  ->  Yama"
```

> **Die Messung selbst bleibt richtig** — *die Quelle führt `review-required` tatsächlich mit einem
> Gedankenstrich, und `4 + 3` ergibt tatsächlich `7`.* **Falsch ist nur die Folgerung, dass daraus
> eine Lücke folgt.** *Yama ordnet die Zahl einer anderen Achse zu, und damit ist die Rechnung gar
> keine.*

## Entscheidung 2 — `blocked` gegen `DECISION_BLOCKED`

**Yamas Antwort:** *der Unterschied ist, **worauf** gewartet wird.*

```text
DECISION_BLOCKED   wartet auf einen MENSCHEN · Ebene Prozess
                   Aufhebung NUR durch ihn, nie maschinell
blocked            wartet auf eine BEDINGUNG · Ebene Produkt
                   Adressat das naechste Werkzeug
                   Aufhebung AUTOMATISCH, sobald die Vorbedingung messbar erfuellt ist

Zwei Auflagen fuer den Bau:
  blocked traegt seinen GRUND mit (blockiert_durch) — sonst Absage ohne Erklaerung
  blocked wird NIE von Hand gesetzt oder geloest; wer das will, meint DECISION_BLOCKED
```

**Was in meinen Blättern jetzt überholt ist — fünf Stellen:**

```text
2-FUNKTION.md:18    „Was blocked von DECISION_BLOCKED im Prozess unterscheidet, ist NICHT
                     belegt"
6-PRUEFUNG.md:13    K-4 „blocked gegen DECISION_BLOCKED als offene Frage"
7-GRENZEN.md:73-74  „NICHT belegt: was blocked von DECISION_BLOCKED im PROZESS unterscheidet"
7-GRENZEN.md:107    Tabelle „Wie grenzt sich blocked von DECISION_BLOCKED ab?  ->  Yama"
```

> **Die Auflage „`blocked` trägt seinen Grund mit" ist dieselbe, die ich in W-41 als Vorgabe
> aufgenommen habe** — *dort für die Invalidierung, hier für die Sperre.* **Beide Male aus demselben
> Satz: eine Absage ohne Erklärung ist die Form des teuersten Fehlers dieses Projekts.**

## Und eine Folge, die über die dreizehn Stellen hinausgeht

**Yamas Schluss laut `2e7504ec`: W-40 ist eine ABLESUNG MIT EINER ERWEITERUNG und keine Vorgabe.**

*Damit ist auch der `art:`-Eintrag des Blattes überholt, und die Registerzeile trägt `ENTWORFEN`,
wo nach dieser Einordnung `BESCHRIEBEN` plus eine benannte Erweiterung stünde.* **Das ist genau die
Frage, die ich beim Bau gestellt und ausdrücklich nicht entschieden habe** — *sie steht in
`7-GRENZEN.md` als „trägt das Ziel `ENTWORFEN` noch?"* **Yama hat sie jetzt beantwortet, und die
Antwort ist: nein.**

## Was ich getan habe und was nicht

```text
GETAN     gemessen: dreizehn Stellen in vier Blaettern, je mit Zeile.
          Sie stehen hier, damit W-40/1 sie VOLLSTAENDIG findet — wer drei
          berichtigt und zehn stehen laesst, hinterlaesst Widersprueche,
          die beide belegt aussehen (H-8).

NICHT     W-40s Blaetter angefasst. Sie sind BETRIEBSBESTAETIGT, und W-40/1 ist
          der Auftrag dafuer — er steht beim plan-pruefer, nicht bei mir.
          Auch die Registerzeile nicht.
          Und nichts entschieden: die Einordnung als Ablesung-mit-Erweiterung ist
          Yamas, nicht meine.
```

> **Mein Beitrag ist eine Liste, keine Berichtigung.** *Die Blätter waren zum Zeitpunkt ihrer
> Abnahme richtig — sie haben die Fragen gestellt, die die Quelle nicht beantworten konnte. Dass
> die Antwort jetzt vorliegt, macht sie nicht falsch, sondern überholt.*
