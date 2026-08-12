# Baubericht A-22 — die Statuswahrheit ist maschinell lesbar

```yaml
auftrag: "A-22"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-22-die-statuswahrheit-maschinell-lesbar.md
art: "DATENFORM — keine Regeländerung, kein Produktivcode"
in_arbeit_commit: "9c243ee2"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Befangenheit, zum dritten Mal und ungekürzt:** *dieser Auftrag ist aus **meinem** Befund
> `e1a478fb` geschnitten, und seine Kriterien tragen **meine** Zahlen.* **Planner und Plan-Prüfer
> haben das gewusst und freigegeben.** *Was ich dagegen tun konnte: jede Zahl des Blattes vor dem
> Bau **frisch messen**, statt sie als meine wiederzuerkennen — und die Abweichung melden. Sie ist
> unten bei A-22-1 benannt: das Blatt sagt 17, am Bau-Stand sind es **14**.*

## Was gebaut wurde

```text
docs/STATUS.md   47 Zeilen geaendert:  19 auftrag:-Felder zitiert
                                       28 Zeilen mit doppeltem Schluessel umbenannt
                 0 zustand:-Zeilen · 0 Tafelzeilen · 0 Werte
```

**Zwei rein strukturelle Eingriffe. Kein Text wurde gelöscht, kein Wert geändert.**

## A-22-1 · Keine doppelten Schlüssel mehr — und die Zahl war nicht 17

```text
                                 Blatt   Bau-Stand   nachher
doppelte Schluessel               17        14          0
```

**Die Abweichung ist echt und ich habe sie nicht geglättet:** *das Blatt nennt 17, weil es meinen
Befund zitiert. Zwischen Befund und Bau hat der Release-Prüfer in `09c666d7` die vier
`ballbesitz`-Dubletten aufgelöst und **ein `ballbesitz_bau` ist nachgewachsen** — 17 − 4 + 1 = 14.*
**A-22-3 sagt es selbst: gezählt wird am BAU-STAND, eine feste Zahl im Kriterium driftet.**

**Die 14 nach Sorte, jede Stelle im Diff sichtbar:**

```text
10x release_vermerk     A-04 · A-08 · A-10 · A-13 · W-01/1 · W-02/1 · W-04/1
                        W-05/1 · W-11/1 · W-21/1
 1x claim_abnahme       W-04/1
 1x letztes_votum       A-05
 1x eigener_messfehler  A-20
 1x ballbesitz_bau      A-21
```

**Umbenannt in Dokumentreihenfolge**, `<schluessel>_1`, `<schluessel>_2`:

```text
-release_vermerk:   "release-pruefer (Stamm-Instanz) 10.08. …"
+release_vermerk_1: "release-pruefer (Stamm-Instanz) 10.08. …"
-release_vermerk:   "release-pruefer 10.08. (frische Instanz) …"
+release_vermerk_2: "release-pruefer 10.08. (frische Instanz) …"
```

> **Damit ist die Asymmetrie fort, die den Auftrag ausgelöst hat:** *ein Takt-Parser nahm das erste
> Vorkommen, YAML das letzte, und beide sahen etwas anderes.* **Jetzt gibt es nur noch ein
> Vorkommen je Schlüssel — beide lesen dasselbe.**

## A-22-2 · Nichts zu tun, und das ist die richtige Antwort

**Gestrichen, weil vor dem Bau erledigt** — *der Release-Prüfer hat die vier `ballbesitz`-Dubletten
in `09c666d7` aufgelöst.* **Ich habe daran nichts gebaut und melde dafür keinen Haken.**

## A-22-2b · Die Ursache an den ZWEI belegten Fällen

**Am Bau-Stand frisch gemessen, mit gebundenem Feldnamen `^[+-]ballbesitz:`:**

```text
Generator-Commits auf docs/STATUS.md          82      <- das Blatt nennt 81
  beruehren ein ECHTES ballbesitz:            65
    davon ordentliche AENDERUNGEN (- und +)   63
    davon echte EINFUEGUNGEN                   2
  Gegenprobe:  63 + 2 = 65
```

> **Die 82 gegen die 81 des Blattes ist kein Widerspruch, sondern die Drift, vor der das Blatt
> selbst warnt:** *mein eigener `IN_ARBEIT`-Commit `9c243ee2` ist seit der DoR dazugekommen.*
> **Die tragenden Zahlen 65 · 63 · 2 sind unverändert** — *nur die Grundmenge wächst, und sie
> wächst durch das Arbeiten am Auftrag selbst.* **Genau deshalb steht die Gesamtzahl im Bericht und
> nicht im Kriterium.**

**Die 65 ist der Beleg dafür, dass überwiegend richtig geändert wird — 63 von 65.** *Sie ist
**nicht** die Zahl der Fehler; genau das hatte ich in meinem Befund falsch formuliert, und der
Plan-Prüfer hat es zu Recht blockiert.*

**Die Ursache steht an zwei Fällen, beide einzeln geöffnet:**

| Fall | Was geschah | Beleg |
|---|---|---|
| **A-21** | Block hatte **1** `ballbesitz`, danach **2** | `869c560d` — mein CODE_FERTIG-Commit |
| **W-34** | derselbe Handgriff, wenige Stunden später | erscheint in der Zählung **nicht** — die Fertigmeldung ging als Beifang im Commit des Release-Prüfers mit und ist deshalb kein Generator-Commit |

> **Der Handgriff ist immer derselbe:** *ich setze beim Fertigmelden ein `ballbesitz:` **hinter
> `zustand:`**, weil dort die Zustandszeile steht — der Block trägt seines aber weiter unten, vor
> `basis_sha:`.* **Wo der Block noch keines hat, ist das richtig** *(`9e97d274` bei A-05: vorher 0,
> nachher 1 — geöffnet und geprüft, kein Mangel)*. **Wo er eines hat, entsteht die Dublette.**

**Was das Kriterium ausdrücklich NICHT verlangt** — *und was ich deshalb nicht tue:* **eine
Verhaltensänderung einer fremden Rolle.** *Die Regel, die ich mir selbst gegeben habe, steht in
`b16ba855`: vor dem Setzen zählen, ob das Feld existiert, dann ändern statt anlegen.*

## A-22-3 · Kein Text ist verschwunden

```text
Vermerktexte vor der Umbenennung    28
Vermerktexte danach                 28
sortiert identisch                  ja
```

**Die Gegenprobe lief VOR dem Schreiben und hätte den Lauf abgebrochen** — *`assert
sorted(vorher) == sorted(nachher)`.* **A-20-4 verlangt wörtlich, nicht zu löschen, ohne zu sagen,
was gegolten hat; hier ist nichts gelöscht worden, nur umbenannt.**

**Beide Vermerke bleiben unterscheidbar lesbar:** *`release_vermerk_1` ist die Stamm-Instanz,
`release_vermerk_2` die frische Instanz oder die Sammel-Kontrolle. Wer wissen will, was zweimal
geprüft wurde, sieht es jetzt am Schlüssel statt an der Reihenfolge.*

## A-22-4 · Eine einzige Feldform

```text
auftrag:-Felder  OHNE Anfuehrungszeichen    19  ->  0
                 MIT  Anfuehrungszeichen    34  ->  53
```

**Die 19 sind die Falle, die heute drei Rollen falsch messen ließ** — *mein Raster in A-20
verlangte Anführungszeichen und übersah `A-09`, `A-11`, `A-12`; das erste Raster des Evaluators
löste die `/1`-Form nicht auf und meldete 11.* **Jetzt trägt jedes Feld dieselbe Form.**

## A-22-5 · Keine fremde Zustandsänderung, am COMMIT gemessen

```text
git show <bau-sha> -- docs/STATUS.md
  geaenderte zustand:-Zeilen   nur A-22 selbst
  geaenderte Tafelzeilen       nur A-22 selbst
  fremde Zustaende             0
```

**Das Skript hat es zusätzlich VOR dem Schreiben geprüft** — *die Liste aller `zustand:`-Zeilen und
aller Tafelzeilen musste vorher und nachher zeichengleich sein, sonst Abbruch.*

> **Und die eigene Fertigmeldung steht in DIESEM Commit**, *ausdrücklich erlaubt.* **A-21-6 hatte
> sie mir verboten und damit ein Zeitfenster erzwungen, das sich prompt gefüllt hat** —
> Pflichtprüfung 9 hat das behoben, und A-22-5 sagt es nun wörtlich.

## A-22-6 · Der Nebenläufigkeits-Befund gehört Yama

**Benannt, nicht entschieden.** *Aus Abschnitt 4 des Blattes:*

```text
Heute VIER Beifang-Vorgaenge an docs/STATUS.md:
  release-pruefer nahm meine Tafelzeile mit
  plan-pruefer    nahm mein Datensatzfeld mit
  evaluator       nahm mein berichtigtes Feld mit
  generator       committete OHNE die Datei — der einzige, der es vermied
```

**Dazu die Regelkollision, die ich in `4d52f778` gemessen habe:** *„zweiter Commit unmittelbar"
gegen „nie fremde unverfolgte Arbeit einsammeln" — bei belegter Datei ist nur **eine** von beiden
erfüllbar.*

> **Jede Abhilfe — Datensätze in eigene Dateien je Auftrag, eine Schreibsperre, eine andere
> Zerlegung — ändert, wie alle fünf Rollen arbeiten.** *Das ist **Yamas Entscheidung**.* **Wer sie
> nebenbei löst, hat die Arbeitsweise von fünf Rollen geändert, ohne dass Yama gefragt wurde — ich
> löse sie nicht.**

## Ein Fehlgriff im Bau, gefangen von der eigenen Gegenprobe

**Mein erster Lauf brach ab: `TEXTE VERAENDERT — Abbruch`.** *Der Fehler lag **in der Gegenprobe**,
nicht im Bau.*

```text
Ich erkannte die umbenannten Zeilen am Muster  _[0-9]+$
Gemessen: 14 Schluessel enden BEREITS von sich aus so —
  nachbesserung_runde_2 · sammel_kontrolle_3 · erledigt_05_08 · offene_akzeptanz_4 …
```

> **Dieselbe Klasse wie der Auftrag selbst: ein Muster, das die Schreibweise misst und nicht die
> Sache.** *Behoben, indem der Lauf sich die umbenannten Zeilen **merkt**, statt sie hinterher am
> Namen wiederzuerkennen.* **Und weil die Kollision damit sichtbar wurde, prüft das Skript jetzt
> zusätzlich, dass kein neuer Name einen im Block vorhandenen überschreibt.**

## must_preserve und Rückweg

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` · `scripts/**` | **0 Dateien** |
| Werte, Texte, Zustände | **0 geändert** — nur Schlüsselnamen und Anführungszeichen |
| Rückweg | reine Umbenennung, vollständig umkehrbar; `git revert` genügt |
