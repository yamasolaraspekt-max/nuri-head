# Baubericht A-20 — der Zustand steht jetzt an zwei Orten statt an vier

```yaml
auftrag: "A-20"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-20-der-zustand-steht-an-vier-orten.md
basis_sha: 6af2572d
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Ein Fehlgriff steckt in diesem Bau, und er steht nicht am Ende, sondern hier oben:** *mein
> erster Lauf hat bei `A-15` eine Zeile entfernt, die **kein Blattfuß** war, sondern ein datierter
> Meldeblock.* **Die Gegenprobe je Datei hat ihn gefangen, sechs Dateien weit** — und ich habe alles
> zurückgesetzt und mit einer anderen Regel neu gebaut. *Abschnitt „Der Fehlgriff" unten.*

## A-20-1 · §16 nennt die vier Orte

`ARBEITSREGELN.md` §16 trägt jetzt die Ortsliste **vor** dem alten Text:

```text
BLEIBT     docs/STATUS.md · Tafelzeile
BLEIBT     docs/STATUS.md · zustand: im Datensatz
ENTFAELLT  Auftragsblatt  · status: im Kopf       Kopie
ENTFAELLT  Auftragsblatt  · zustand: im Fuss      Kopie der Kopie
BLEIBT     Auftragsblatt  · status_steht_in: docs/STATUS.md
```

*Mit der Messung, die es trägt, und dem belegten Beinahe-Schaden aus W-20 („Findigkeit, kein
Verfahren").*

## A-20-2 · §5 trägt die Entscheidung

> **Wer schneidet, legt Tafelzeile UND Block an — im selben Commit wie das Blatt. Wer danach prüft
> oder baut, ändert Felder in diesem einen Block. Nie einen zweiten.**

*Mit beiden Begründungen aus dem Blatt: das **Fenster** (W-38 — Blatt committet, null Blöcke, null
Tafelzeilen) und der **Doppelblock** (A-17 — der zweite Block trug `zustand`).*

## A-20-3 · Die Blätter sind bereinigt

```text
                              vorher   nachher
status: im Kopf                  33        0
zustand: im Blattfuss            10        0
zustand: in Meldebloecken        17       17   <- BLEIBEN, siehe unten
```

**Und die naive Gegenprobe, damit niemand über die Zahl stolpert:** *`grep -c '^zustand:'` über alle
Blätter findet weiterhin **17** — das sind ausschließlich Meldeblöcke.* **Beide Zahlen stehen hier;
welche das Kriterium meint, entscheidet der Evaluator.**

## Der Fehlgriff — und die Unterscheidung, die daraus wurde

**Mein erster Lauf nahm „die erste `^zustand:`-Zeile" für den Blattfuß.** *Dieselbe Annahme steht in
der Ist-Messung des Auftragsblattes (Abschnitt 1).* **Sie ist falsch.**

```text
A-15, Zeile 446 in HEAD — was mein Lauf entfernt hat:
  ```yaml
  auftrag: "A-15"
  zustand: CODE_FERTIG
  bericht: "docs/BERICHT-A-15-fachaussage-oder-hinweis.md"
  bau_commits: "18a33858 · b5b490c8 · 82d7c31e · a2385d35 · (Abschluss)"
```

**Das ist kein Blattfuß, sondern eine datierte Übergabe-Aufzeichnung.** *`A-15` hat gar keinen
Fuß-Zustand — beide `zustand:`-Zeilen dort sind Meldeblöcke.*

> **Gefangen hat es die Gegenprobe je Datei**, die B6 verlangt: nach dem Schreiben prüft der Lauf
> jede Datei sofort, und bei `A-15` blieb eine `zustand:`-Zeile übrig. **Der Lauf brach ab, sechs
> Dateien weit.** *Ich habe alle sechs mit `git checkout` auf HEAD zurückgesetzt — **kein halber
> Zustand blieb liegen** — und die Regel geändert.*

**Die neue Regel unterscheidet am BLOCK, nicht an der Zeile:**

```text
Blattfuss    yaml-Block OHNE  auftrag:   ->  Kopie,          ENTFAELLT
Meldeblock   yaml-Block MIT   auftrag:   ->  Aufzeichnung,   BLEIBT
```

*Gemessen: **10** Blattfüße und **17** Meldeblöcke — **Achtung auf die Einheit**: die 17 zählt
**Blöcke**, sie verteilen sich auf **14 Blätter**.* **Und 10 + 14 = 24, die Zahl aus der Ist-Messung
des Blattes** — *kein Blatt trägt beides, die beiden Sorten überschneiden sich nicht.* **Die 24 sind
also genau diese zwei Sorten zusammen; die Ist-Messung zählte pro Blatt die erste Zeile, ohne zu
unterscheiden.** *Was daraus für die „17" folgt, steht im übernächsten Abschnitt.*

> **Warum die Meldeblöcke bleiben:** *sie tragen `bau_commit`, `runde`, `befund_von` — sie sind
> **Belege**, keine Statusbehauptung.* **A-20-4 sagt selbst: „Wer nur löscht, ohne zu sagen was
> gegolten hat, vernichtet einen Befund." Das gilt für sie doppelt.** *Sollte der Evaluator sie als
> Kopien werten, sind es zwei Zeilen Nachbesserung — die Unterscheidung steht jetzt gemessen da.*

## Welche Menge „17 von 24" bezeichnet — die erbetene Zeile, und was beim Nachmessen auffiel

**Der Plan-Prüfer hat in der DoR ausdrücklich darum gebeten:** *„Ich bitte nur um eine Zeile im
Bericht, welche Menge die 17 und die 24 bezeichnen — sonst prüft der Evaluator gegen eine Zahl,
deren Grundmenge er nicht kennt."* **Gemessen gegen `HEAD`, wo die Kopien noch stehen:**

```text
Blaetter in docs/auftraege/aktiv/                            43
  mit  status:  im Kopf                                      33
  mit IRGENDEINER ^zustand:-Zeile          (naiv)            24   <- die "24"
    davon Kopf != erste ^zustand:-Zeile                      17   <- die "17"
  mit ECHTEM Blattfuss (yaml-Block ohne auftrag:)            10
    davon Kopf != Blattfuss                                   4
  mit mindestens einem MELDEBLOCK                            14
```

> **Die 17 sind 4 + 13.** *Vier sind echte Widersprüche zwischen Kopf und Blattfuß.* **Dreizehn
> vergleichen den Kopf mit einem MELDEBLOCK** — *also mit einer datierten Bauaufzeichnung, die
> zurecht etwas anderes sagt als der Kopf.*

```text
ECHT (4)        A-17 · B7 · W-27   Kopf BEREIT       Fuss ENTWURF
                W-20               Kopf CODE_FERTIG  Fuss ENTWURF

NUR NAIV (13)   A-13 A-14 A-15 W-01 W-02 W-04 W-05 W-08 W-09 W-11 W-13 W-21 W-22
                alle: Kopf ENTWURF  gegen  Meldeblock CODE_FERTIG
```

**Die Ist-Messung des Auftragsblattes ist derselben Verwechslung aufgesessen wie mein erster Lauf**
— *dieselbe Regel „erste `^zustand:`-Zeile", derselbe Irrtum.* **Ich habe zwei Stellen mit eigenen
Augen gelesen, statt dem Muster zu glauben:**

```text
A-13, Zeile 225-230   auftrag: "A-13" / zustand: CODE_FERTIG / bau_commit / art: "BAU"
                      -> Bauaufzeichnung. Der Kopf stand auf ENTWURF; das ist kein
                         Widerspruch, sondern eine veraltete Kopie neben einem Beleg.

W-20, Zeile 185-187   ```yaml / zustand: ENTWURF / ballbesitz: "plan-pruefer (DoR)"
                      -> kein auftrag:. Echter Blattfuss, echter Widerspruch zum Kopf.
```

> **Das ändert am Auftrag nichts und schmälert ihn nicht.** *Die 17/24 stehen in der **Ist-Messung**
> des Blattes, in keinem Kriterium.* **A-20-3 verlangt alle Kopien — entfernt sind 33 Köpfe und 10
> Füße, also mehr als jede der beiden Lesarten umfasst.** *Und der eigentliche Schaden liegt gar
> nicht hier: **29 der 33 Köpfe wichen von `docs/STATUS.md` ab** — das ist die größere Zahl und die
> nächste Tabelle.*

**Beide Lesarten stehen hier mit ihrer Zahl. Welche das Blatt gemeint hat, entscheidet der
Evaluator, nicht ich.**

## A-20-4 · Jede entfernte Kopie, die abwich — was stand, was gilt

**29 abweichend · 1 übereinstimmend · 3 ohne Datensatz** *(Summe 33)*

| Blatt | Kopf stand | Fuß stand | **GILT** |
|---|---|---|---|
| A-13 · A-14 · A-15 | `ENTWURF` | — | **BETRIEBSBESTAETIGT** |
| A-16 | `BEREIT` | `BEREIT` | **BETRIEBSBESTAETIGT** |
| A-17 · B7 · W-27 | `BEREIT` | `ENTWURF` | **BETRIEBSBESTAETIGT** |
| A-18 · A-19 · B5N · W-23 | `ENTWURF` | `ENTWURF` | **BETRIEBSBESTAETIGT** |
| A-20 *(dieser Auftrag)* | `BEREIT` | — | **IN_ARBEIT** |
| B5 · B6 | `ENTWURF` | — | **BETRIEBSBESTAETIGT** |
| W-01 · W-02 · W-04 · W-05 · W-08 · W-09 · W-11 · W-13 · W-15 · W-21 · W-22 | `ENTWURF` | — | **BETRIEBSBESTAETIGT** |
| W-01N | `BEREIT` | — | **BETRIEBSBESTAETIGT** |
| W-07N | `CODE_FERTIG` | — | **BETRIEBSBESTAETIGT** |
| W-20 | `CODE_FERTIG` | `ENTWURF` | **BETRIEBSBESTAETIGT** |
| W-21L | `ENTWURF` | — | **ZURUECKGESTELLT** |

**Gezählt statt eingestuft:** *über alle Zeilen hinweg standen **20 der 33 Köpfe** auf `ENTWURF`,
während `BETRIEBSBESTAETIGT` galt* — **A-13 A-14 A-15 A-18 A-19 B5 B5N B6 W-01 W-02 W-04 W-05 W-08
W-09 W-11 W-13 W-15 W-21 W-22 W-23.** *Das ist die größte einzelne Falle des Bestands gewesen, und
**nur ein einziger Kopf von 33 stimmte**.*

**Drei Blätter haben KEINEN Datensatz in `docs/STATUS.md`** — *dort ist kein Vergleich möglich:*

```text
A-09-repo-bezug-nicht-nur-cwd.md    Kopf stand auf ENTWURF
A-11-rollenmarke-im-tor.md          Kopf stand auf ENTWURF
A-12-f026-ausfuehren.md             Kopf stand auf ENTWURF
```

> **Das ist ein Befund, kein Nebensatz:** *nach der Regel, die dieser Auftrag gerade in §5 schreibt,
> muss jeder geschnittene Auftrag einen Block haben.* **Diese drei haben keinen — ihre Kopie war die
> einzige Zustandsangabe, die es zu ihnen gab, und sie ist jetzt fort.** *Ich habe sie trotzdem
> entfernt, weil A-20-3 alle Kopien verlangt; **die Lücke gehört dem Planner**, und sie ist hier
> benannt statt stillschweigend geschlossen.*

## A-20-5 · Kein Zustand in `STATUS.md` geändert

```text
git diff --name-only -- docs/STATUS.md   ->   0
```

*Die einzige Statusänderung dieses Auftrags ist meine eigene §3-Markierung (`A-20` auf `IN_ARBEIT`),
und sie steht in einem **eigenen** Commit vor dem Bau (`6af2572d`). **Die Bereinigung selbst hat
keine Tafelzeile und kein `zustand`-Feld angefasst.***

## A-20-6 · Die Rollenvorlagen — erhoben, eine umgestellt

```text
grep -rn 'Blattkopf|status:'  ueber docs/rollenkette/rollen/ und /uebergaben/   ->  1 Treffer
  1-planner/1-AUFTRAG.md:79   "Ein Zustand steht an bis zu VIER Orten: Tafelzeile,
                               Datensatz-Feld, Blattkopf, Blattfuss."
```

**Umgestellt:** *die Prüfliste zählt weiter Stellen (das bleibt richtig), aber für den **Zustand**
steht jetzt dort: **genau zwei Orte**, beide in `docs/STATUS.md`.* **Die Fälle A-16, W-07N und W-27
bleiben als Beleg stehen — sie sind der Grund für diesen Auftrag.**

## A-20-7 · B6 gewahrt

```text
33 Dateien · 0 Einfuegungen · 43 Loeschungen        (33 Kopf + 10 Fuss)
Dateien mit mehr als 2 Loeschungen                  0
Dateien mit Einfuegungen                            0
```

**Je Datei geändert, je Datei sofort gegengeprobt** — *und die Gegenprobe hat den Fehlgriff oben
tatsächlich gefangen. Ohne sie wären 33 Meldeblöcke stumm verschwunden.* **Kein dateiweites Muster.**

## `must_preserve` und Rückweg

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien** |
| `docs/STATUS.md` | **0 Änderungen** |
| Rückweg | `git checkout` hat den Fehlgriff bereits zerstörungsfrei zurückgenommen — der Bau ist reiner Zeilenentfall, `git revert` genügt |

## Berührte Dateien

```text
docs/ARBEITSREGELN.md                         §16 Ortsliste · §5 Entscheidung
docs/auftraege/aktiv/*.md                     33 Blaetter, 43 Zeilen entfernt
docs/rollenkette/rollen/1-planner/1-AUFTRAG.md   Pruefliste umgestellt
docs/BERICHT-A-20-zustand-an-vier-orten.md    dieser Bericht
docs/STATUS.md                                Zustand an beiden Orten
```
