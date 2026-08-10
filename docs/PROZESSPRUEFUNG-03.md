# §13-Prozessprüfung 03 — die erste Barriere für meine Fehlerklasse, und sie kam von jemand anderem

```yaml
pruefung: "§13-Prozesspruefung 03"
ausgeloest_durch: "8d91b7a2 — plan-pruefer, SOFORTAUSLOESER formal gezogen"
durchfuehrung: planner (nach P-02-Praezedenz)
anlass_klassen:
  - "zweite Wiederholung: Satz ohne Befehl"
  - "NEUE Klasse: gruen gemeldet, nicht im Commit"
  - "B3 weiter ungebaut"
gemessen_am: "2026-08-10 abends"
ballbesitz: "yama (Entscheidung), plan-pruefer (Gegenlese)"
einschub: "JA — Yamas Ansage lautet 'A vollstaendig fertigstellen'. Dies ist keine
           Auftragsarbeit, sondern die von §13 erzwungene Pruefung. Danach Runde 2."
```

> **Warum diese Prüfung überhaupt läuft.** Die SOFORT-Klausel (B3) verlangt die Prüfung bei der
> **zweiten** Wiederholung einer Fehlerklasse. Die Tabelle im Auftragszähler zeigt **vier** Klassen
> über der Schwelle und **keine** gezogene Prüfung — *„alle haben auf die Zehn geschaut, niemand auf
> die Zwei."* **Der Plan-Prüfer hat sie jetzt gezogen und dabei die Ironie-Stufe vermieden:** eine
> Klausel, die man benennt und nicht auslöst, ist keine Klausel.

## Befund 1 — die neue Klasse: „grün gemeldet, nicht im Commit"

**Gefunden vom Generator an sich selbst** (`3e7e19d6`), und zwar nachdem der Evaluator die
Voraussetzung dafür geschaffen hatte:

```text
Kriterium W-02/1-2   im abgelieferten Bau ROT
§11-Bericht          meldete GRUEN
Ursache              die Korrektur war geschrieben, vollstaendig und richtig —
                     aber NIE COMMITTET. 801e2daa trug die ausgeschriebene
                     Azimut-Rechnung in Z.17-19.
Gegenprobe des        4 Formelzeichen bei 801e2daa · 0 bei HEAD
Generators
```

**Seine eigene Ursachenanalyse, wörtlich:**

> *„Meine Gegenprobe las den **Arbeitsbaum**, dort war alles grün; der Bau ist aber der **Commit** —
> und den hat niemand gemessen, auch ich nicht."*

> ### Das ist keine Variante von „halb korrigiert" — und es ist auch keine neue Klasse
>
> **Es ist die vierte Klasse in ihrer teuersten Form: „Stellvertreter statt Quelle".** Ich habe die
> Unterform in `PROZESSPRUEFUNG-02-ANTEIL-PLANNER.md` beschrieben — mit drei Fällen:
>
> ```text
> Wegwerf-Repo         statt  echtes Repo          (anderer ORT)
> $TMPDIR              statt  $TMPDIR/ticket-index (anderer ORT)
> Remote-Tracking-Ref  statt  Remote               (anderer ZEITPUNKT)
> NEU: Arbeitsbaum     statt  Commit               (anderer ZUSTAND)
> ```
>
> **Der Arbeitsbaum ist der klassische Stellvertreter:** verfügbar, plausibel, und in 99 % der Fälle
> identisch mit dem Commit. **Genau deshalb fällt er nicht auf.** *Und er ist teurer als meine drei:
> bei mir wurden Zahlen falsch, hier wurde ein rotes Kriterium als grün abgeliefert.*

## Befund 2 — und hier ist das Neue: es gibt eine Barriere

**Prozessprüfung 02 schloss mit:** *„Die vierte Fehlerklasse bleibt ohne Barriere — ausdrücklich. Ich
habe eine gesucht und keine gefunden … die fünf Fälle sind **semantisch** … es gibt kein Textmuster,
das sie von einer belegten Aussage trennt."*

**Der Generator hat eine gefunden, und sie ist mechanisch:**

```text
git show HEAD:<pfad> | diff - <pfad>     ueber ALLE beruehrten Dateien,
                                          VOR jeder CODE_FERTIG-Meldung
```

> **Warum das die Aussage von Prüfung 02 nicht widerlegt, sondern präzisiert:**
>
> ```text
> Pruefung 02 suchte eine Barriere fuer die KLASSE  ("Zuordnung annehmen")  -> semantisch,
>                                                                             keine gefunden
> Der Generator fand eine fuer eine UNTERFORM       ("falsches Objekt")     -> mechanisch,
>                                                                             gefunden
> ```
>
> **Die Klasse ist semantisch, ihre Unterformen sind es nicht.** „Ursache oder Reichweite behaupten"
> hat kein Textmuster. **„Am Stellvertreter statt an der Quelle messen" hat eines: den Befehl.**
>
> *Und damit tragen die beiden Vorschläge aus meinem Anteil, die ich als „vielleicht zu schwach"
> markiert hatte:*
>
> ```text
> V1  Remote-Aussagen verlangen `git fetch` im selben zitierten Befehl
> V2  Zahlen ueber Dateimengen tragen den ABSOLUTEN Pfad daneben
> NEU Aussagen ueber den BAU verlangen `git show HEAD:<p> | diff` — nicht den Arbeitsbaum
> ```
>
> **Alle drei sind dieselbe Bauart: die Quelle statt des Stellvertreters befragen, und den Befehl
> beilegen.** *Das ist prüfbar — der Befehl steht in der Ausgabe oder nicht. Es ist kein Vorsatz.*

## Befund 3 — „Satz ohne Befehl", zweite Wiederholung, und es ist MEIN Kriterium

```text
1. Fall  35e90eb8   §3-Beleg gefordert (W-02/1-9), geliefert: 0 Befehlszeilen, 0 Ausgabewerte
2. Fall  (vom Evaluator als zweite Wiederholung benannt)
behoben  5c06f5ca   2 Befehlszeilen, 2 Ausgabewerte, plus Gegenprobe
```

> **Das Kriterium ist meines** (`W-01/1-8`, `W-02/1-9`, `W-13/1-10`, `W-04/1-10`, `W-08/1-9`,
> `W-11/1-10`). Ich habe es geschrieben, um §3 vom Hinweis zum Nachweis zu machen — **und es wurde
> zweimal mit einer Behauptung erfüllt statt mit einem Befehl.**
>
> **Mein Anteil daran, präzise:** Das Kriterium sagt *„enthält den Befehl mit Ausgabe"*. Es sagt
> **nicht**, wie viele Befehlszeilen und wie viele Ausgabewerte. **Ein Kriterium, dessen Erfüllung
> man nicht zählen kann, wird geschätzt** — und geschätzt heißt hier: behauptet.
>
> **Ableitung für alle sechs Blätter:** das Kriterium bekommt eine zählbare Form —
> *„mindestens zwei Befehlszeilen und zwei Ausgabewerte, je Ort einer"*. `5c06f5ca` hat genau das
> geliefert; damit ist die Zahl nicht erfunden, sondern aus dem behobenen Fall abgelesen.

## Was diese Prüfung NICHT vorschlägt

- **Kein Skill-Nachschärfen.** Prüfung 02 hat es verworfen, weil es fünfmal nicht getragen hat.
  *Eine sechste Notiz wäre die Wiederholung des Fehlers auf der Metaebene — das gilt hier weiter.*
- **Keine neue Fehlerklasse in der Tabelle.** „Grün gemeldet, nicht im Commit" ist eine **Unterform**
  der vierten Klasse, keine fünfte. *Sie als neue Klasse zu führen würde die Zählung verwässern und
  die Schwelle zurücksetzen.*
- **Kein Vorschlag zu B3.** Es ist weiter ungebaut, und der Grund steht schon in Prüfung 02: *eine
  echte Barriere bräuchte ein Werkzeug, das die Klassen selbst zählt — dafür fehlt die zählbare Zeile
  je Auftrag.* **Das ist Befund 0, und A-11 hat gerade den ersten Teil davon gebaut** (die
  Rollenmarke). *Ob daraus eine Klassenzählung wird, ist eine eigene Frage.*

## Die Entscheidung, die ich vorlege

```text
E1  DIE BARRIERE UEBERNEHMEN.  "Aussagen ueber den Bau werden am COMMIT gemessen,
    nicht am Arbeitsbaum" — als Meldepflicht vor CODE_FERTIG, mit dem Befehl in der
    Ausgabe. Der Generator hat sie selbst gefunden und selbst gefahren (sieben
    Blaetter und REGISTER, alle im Commit geprueft).
    -> KOSTET nichts, ist gefahren, und schliesst die teuerste Unterform.

E2  DAS §3-KRITERIUM ZAEHLBAR MACHEN.  In allen sechs W-Blaettern: mindestens zwei
    Befehlszeilen und zwei Ausgabewerte, je Ort einer. Aus dem behobenen Fall
    abgelesen, nicht erfunden.
    -> KOSTET sechs Zeilen. Verhindert die dritte Wiederholung.

E3  DIE UNTERFORM IN DIE TABELLE, NICHT ALS KLASSE.  Die vierte Klasse bekommt eine
    Spalte "Unterformen mit Barriere": Ort (V2), Zeitpunkt (V1), Zustand (NEU).
    -> macht sichtbar, dass die Klasse semantisch ist und ihre Unterformen nicht.
```

**Ich empfehle alle drei.** *E1 ist gefahren und kostenlos, E2 verhindert eine Wiederholung, die
schon zweimal kam, E3 hält die Zählung ehrlich.*

## Zur Ehrlichkeit dieser Prüfung

**Sie ist von der Rolle geschrieben, die die meisten Fälle der geprüften Klasse produziert hat.**
Mein Anteil zu Prüfung 02 zählt sechs Fälle an einem Tag; seither sind drei dazugekommen (Halden-Ort,
Abschlusszahl, W-08 in Runde 1). **Das ist ein Interessenkonflikt und er gehört benannt.**

*Was ihn erträglich macht: **keiner der drei neuen Befunde stammt von mir.** Der Generator fand seinen
selbst, der Evaluator machte es möglich, der Plan-Prüfer löste die Klausel aus. Ich führe die Prüfung
durch, weil §13 es so vorsieht — **aber die Substanz kommt von den anderen drei Rollen**, und das
steht hier, damit niemand diese Prüfung für eine Selbstauskunft nimmt.*

```yaml
fehlerklasse_gepruefte: "Zuordnung annehmen statt messen (vierte Klasse), Unterform Zustand"
neue_barriere: "git show HEAD:<pfad> | diff - <pfad> vor CODE_FERTIG — vom Generator gefunden"
aenderung_an_pruefung_02: "ihre Aussage 'keine Barriere' gilt fuer die KLASSE, nicht fuer
                           die Unterformen — praezisiert, nicht widerlegt"
ballbesitz: yama
danach: "Runde 2 der Klasse A (W-05, W-21, W-22)"
```
