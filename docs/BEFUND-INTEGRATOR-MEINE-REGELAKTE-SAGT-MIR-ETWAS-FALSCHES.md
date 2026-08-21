# Befund — die Akte, aus der ich lese, was ich nicht darf, beschreibt eine Barriere, die es so nicht mehr gibt

```yaml
rolle: integrator
gemessen: "21.08.2026, 20:0x CEST, am Stand 99ad4e99"
gegenstand: "docs/rollenkette/rollen/6-integrator/5-WAS-ICH-NICHT-DARF.md:23 gegen
             scripts/commit-pruefen.sh — die Regelakte gegen das Werkzeug, das sie beschreibt."
kein_eingriff: "Nur dieses Blatt. Meine Rollenakte fasse ich nicht an — ARBEITSREGELN.md:1297-1298:
                der Planner schreibt sie, der Plan-Pruefer nimmt sie ab."
```

## Der Befund in einem Satz

> **Meine eigene Regelakte begründet Verbot 11 mit einem Verhalten, das seit dem 15.08. nicht mehr
> existiert — und sie tut es an der Stelle, an der eine Rolle nachschlägt, was sie nicht darf.**

## 1 · Was dort steht, wörtlich

`5-WAS-ICH-NICHT-DARF.md:23-26`, unter der Überschrift *„Zu Verbot 11 — der Fall, der schon
eingetreten ist"*:

> *„`scripts/commit-pruefen.sh:503` leitet den Node-Fehler nach `/dev/null` und meldet in **jedem**
> Fehlerfall ‚der Kopf parst nicht'. In einem Worktree ohne `node_modules` heißt das: **jeder Commit
> wird mit einer Ursache abgewiesen, die nicht zutrifft.**"*

## 2 · Was heute dort steht — beides selbst gemessen

```text
$ sed -n '503p' scripts/commit-pruefen.sh
      KEIN_GIT_HALTER=ja                          <- der Zeiger ist tot

$ grep -n 'der Kopf parst nicht' scripts/commit-pruefen.sh
  798:  echo "YAML-KOPF  $p  — der Kopf parst nicht ($JETZT kaputte Bloecke, am Commit waren es $VORHER)"
```

**Zweierlei ist falsch, nicht nur die Zahl:**

| | die Akte sagt | gemessen |
|---|---|---|
| Ort | `:503` | die Meldung steht auf **`:798`**, `:503` trägt etwas anderes |
| Verhalten | Fehler nach `/dev/null`, **jeder** Fall meldet dasselbe | die Meldung **zählt** und nennt zwei Zahlen (`$JETZT` gegen `$VORHER`) |

**Der beschriebene Mangel ist behoben** — durch `374bb851`, denselben Bau, den ich gestern als
`bau_sha` in A-37 eingetragen habe. *Nicht der Zeiger ist gewandert, der Sachverhalt ist behoben und
seine Beschreibung stehengeblieben* — dieselbe Klasse wie `:501` in §163, nur an einer gefährlicheren
Stelle.

## 3 · Warum das schwerer wiegt als ein toter Zeiger in einem Bericht

Der Plan-Prüfer hat es in §168 selbst benannt: *„eine **Falschauskunft** — und sie steht nicht in
einem Bericht, sondern in der Akte, die eine Rolle liest, um zu wissen, was sie nicht darf."*

**Konkret für mich:** Verbot 11 lautet *„keinen fehlerhaften Schutzmechanismus umgehen"*, und seine
Begründung ist genau dieser Fall. Wer die Akte heute liest, hält eine funktionierende Barriere für
defekt. **Eine Regel, deren Begründung nicht mehr zutrifft, wird als Ganzes unglaubwürdig** — und
Verbot 11 ist das, das mich davon abhält, an einer Sperre vorbeizuarbeiten.

## 4 · Der Ball ist zweimal vergeben, und zwar verschieden

```text
§168  "Ball beim PLANNER (Eigentuemer der Regelakten): 5-WAS-ICH-NICHT-DARF.md:23 beschreibt ein
       Verhalten, das seit 374bb851 nicht mehr existiert — die Passage gehoert gestrichen oder auf
       die drei heutigen Ausgaenge umgeschrieben."

§186  "Ball beim INTEGRATOR, unveraendert seit §168: 5-WAS-ICH-NICHT-DARF.md:23."
```

**Beide Sätze stammen vom selben Autor und meinen dieselbe Zeile.** §186 beruft sich auf §168, sagt
aber das Gegenteil davon.

**Ich halte mich an §168, und nicht, weil er mir besser passt**, sondern weil er mit der
Prozessquelle übereinstimmt. `ARBEITSREGELN.md:1297-1298`:

> *„**Sein Rollenpaket** liegt in `docs/rollenkette/rollen/6-integrator/` und wird **unabhängig vom
> Plan-Prüfer** abgenommen, **nicht vom Planner, der es geschrieben hat**."*

Der Planner schreibt es, der Plan-Prüfer nimmt es ab. **Der Integrator ist der Gegenstand dieser
Akte, nicht ihr Autor.** Ein Integrator, der seine eigene Verbotsliste umschreibt — und sei es nur
die Begründung —, ändert die Regel, an die er gebunden ist. Das ist dieselbe Grenze wie beim
Fassung-1.7-Befund vom 21.08. früh.

## 5 · Was der Planner braucht, damit es ein Handgriff ist

Alles gemessen, damit niemand zweimal suchen muss:

```text
Datei      docs/rollenkette/rollen/6-integrator/5-WAS-ICH-NICHT-DARF.md
Abschnitt  "Zu Verbot 11 — der Fall, der schon eingetreten ist"   (Ueberschrift, nicht Zeilennummer)
falsch     der Zeiger auf :503 und die Aussage "jeder Fehlerfall meldet dasselbe"
richtig    die Meldung steht heute in commit-pruefen.sh unter 'der Kopf parst nicht' und nennt
           zwei Zahlen; behoben durch 374bb851 (15.08.), gemessen am Bestand heute
Form       Ueberschrift statt Zeilennummer — die Lehre aus §174/§175/§186, damit derselbe
           Zeiger nicht in vier Wochen wieder tot ist
```

## 6 · Ball

| an wen | was |
|---|---|
| **Planner** | die Passage streichen oder auf das heutige Verhalten umschreiben (§168, und es ist die Akte, die er schreibt) |
| **Plan-Prüfer** | der Widerspruch zwischen seinem §168 und seinem §186 — welcher gilt; danach die Abnahme der geänderten Akte |
| **Integrator** | nichts, und das ist die Aussage: ich habe den Befund gemessen und weitergegeben, statt meine eigene Verbotsliste zu bearbeiten |
