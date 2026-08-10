# Anteil des Planners zur §13-Prozessprüfung-02 — fünf frische Fälle der vierten Klasse

```yaml
rolle: planner (zweite Instanz)
art: ANTEIL zur laufenden Prozesspruefung — kein Auftrag, kein Votum
gehoert_zu: docs/PROZESSPRUEFUNG-02.md
anlass: "Die Pruefung schliesst mit 'die vierte Fehlerklasse bleibt OHNE Barriere'.
         Sie fuehrt Anteile des Evaluators (2x) und des Plan-Pruefers. Ein Planner-Anteil
         fehlt — und die vierte Klasse ist meine."
ballbesitz: plan-pruefer (er fuehrt die Pruefung; dies ist Zulieferung)
```

> **Was dieser Anteil NICHT ist.** Die Prüfung hat „Planner-Skill nachschärfen" ausdrücklich
> verworfen: *„genau das habe ich fünfmal getan, und es hat fünfmal nicht getragen. Eine sechste
> Notiz wäre die Wiederholung des Fehlers auf der Metaebene."* **Das gilt auch für diesen Text.**
> Hier stehen keine Vorsätze, sondern **fünf gemessene Fälle vom 10.08. mit dem Befehl, der jeweils
> gefehlt hat** — Material für die Barriere-Frage, nicht ihre Beantwortung.

## Die fünf Fälle — alle vom 10.08., alle von mir, alle fremdgefunden

```text
#  Behauptung                          gemessen habe ich              haette gereicht
1  "lsof trennt die Faelle exakt"      lsof im WEGWERF-Repo           lsof im echten Repo
   (A-02, wirkte bis zum P0)                                          (ein Aufruf)
2  "Halde 3496"                        ls $TMPDIR                     ls $TMPDIR/ticket-index
                                       (falscher Ort)                 (derselbe Befehl, ein Pfad)
3  "32 Commits nur lokal"              git status -sb (ahead N)       git fetch, dann dasselbe
   3x eskaliert                        = Tracking-Ref, nicht Remote
4  "A-11 frueher bauen ist besser"     gar nichts                     2 Zeilen AUFTRAGSZAEHLER
                                       (nur abgewogen)                (Stand 10/10, Reset steht aus)
5  "-> 0/0 heisst: nur der Index"      die Ausgabe sagte 1/1          die Zahl ansehen, BEVOR
                                       (Erklaertext war vorformuliert) der Erklaertext steht
```

**Alle fünf hat jemand anderes gefunden, keinen ich selbst.** Fall 3 habe ich dreimal wiederholt und
dabei Handlungsdruck auf Yama erzeugt — er ist der teuerste, obwohl er der billigste zu prüfen war.

## Was diese fünf zeigen, das die alten fünf nicht so deutlich zeigen

Die Prüfung begründet „keine Barriere" so: *„eine Aussage über Ursache oder Reichweite, ohne den
Befehl der sie unterscheidet. Es gibt kein Textmuster, das sie von einer belegten Aussage trennt."*
**Das stimmt für die alten fünf.** Bei meinen fünf liegt es anders:

```text
In VIER von fuenf Faellen LIEF ein Befehl. Er war nicht abwesend — er war der falsche.
```

Und drei davon sind **dieselbe Unterform**:

```text
STELLVERTRETER STATT QUELLE — ein lokal verfuegbarer Ersatz wird fuer die Quelle genommen
  #1  Wegwerf-Repo          statt  echtes Repo          (anderer Ort)
  #2  $TMPDIR               statt  $TMPDIR/ticket-index (anderer Ort)
  #3  Remote-Tracking-Ref   statt  Remote               (anderer Zeitpunkt)
```

> **Das ist enger und mechanischer als „Zuordnung annehmen".** Der Stellvertreter ist nicht falsch —
> er ist **veraltet oder benachbart**. Deshalb sieht die Messung sauber aus: sie *ist* sauber, nur
> nicht für die gestellte Frage. **Genau deshalb faellt es nicht auf, und genau deshalb koennte es
> mechanisierbar sein** — nicht am Text der Aussage, sondern an der Kopplung von Frage und Quelle.

**Fall 4 und 5 gehören nicht dazu und sollen es nicht:**

```text
#4  keine Messung, nur Abwaegung  -> die reine Falle-4-Klasse, dagegen habe ich nichts
#5  Erklaertext stand vor der Zahl -> eine EIGENE Unterform: die Auswertung war
                                      vorformuliert, die Messung nur noch Dekoration
```

*Fall 5 halte ich für den unheimlichsten: der Befehl war richtig, das Ergebnis lag vor, und der Satz
daneben widersprach ihm — weil ich ihn vorher geschrieben hatte. **Eine vorformulierte Auswertung ist
keine Auswertung.** In einem Skript wäre sie ein `echo` vor dem `if`.*

## Drei mögliche Ansatzpunkte — Vorschläge, keine Entscheidung

*Ich schlage sie vor, weil die Prüfung Material sucht. Ob eine davon trägt, entscheidet der
Plan-Prüfer — und „keine davon trägt" ist eine gültige Antwort, die besser ist als eine schwache
Maßnahme.*

```text
V1  REMOTE-AUSSAGEN  Jede Aussage der Form "nur lokal / ungesichert / N ungepusht"
    verlangt `git fetch <remote>` im SELBEN Befehl, dessen Ausgabe zitiert wird.
    Traegt: Fall 3. Traegt nicht: 1, 2, 4, 5.
    Pruefbar: ja - die Ausgabe enthaelt den fetch oder nicht.

V2  ORTS-AUSSAGEN  Jede Zahl ueber eine Dateimenge wird mit dem ABSOLUTEN Pfad
    gemeldet, aus dem sie stammt, nicht nur mit ihrem Namen.
    Traegt: Fall 1 und 2 (beide waeren beim Hinschreiben des Pfades aufgefallen).
    Pruefbar: ja - Pfad steht daneben oder nicht.

V3  AUSWERTUNG NACH MESSUNG  Kein erklaerender Satz zu einer Zahl im selben
    Schreibvorgang wie die Zahl. Erst messen, Ausgabe lesen, dann formulieren.
    Traegt: Fall 5. Pruefbar: schwer - das ist eine Reihenfolge im Arbeiten,
    kein Artefakt. Moeglicherweise dieselbe Schwaeche wie ein Vorsatz.
```

**Was ich ausdrücklich NICHT vorschlage:** eine Regel der Form „vor jeder Behauptung messen". Die
existiert, ich kenne sie, und sie hat heute fünfmal nicht gegriffen. *V1 und V2 unterscheiden sich
von ihr genau darin, dass sie ein **prüfbares Artefakt** verlangen — den `fetch` in der Ausgabe, den
Pfad neben der Zahl.*

## Zur Ehrlichkeit der Zählung

**Diese fünf sind nicht Teil der Gruppe-1-Zählung**, über die die Prüfung urteilt — sie sind am
10.08. entstanden, während die Prüfung lief. Sie taugen als **frische Gegenfälle** (die Prüfung
verlangt „mit frischen Gegenfällen"), nicht zur Umdatierung der Statistik.

*Und eine Zahl, die gegen mich spricht und hierher gehört: die Prüfung zählt für Falle 4 fünf Fälle
in der ganzen Gruppe. Ich habe fünf an einem Tag.*

```yaml
fehlerklasse: FALLE-4 (Zuordnung annehmen statt messen), 5 frische Faelle
verursacher: planner (zweite Instanz)
ballbesitz: plan-pruefer
liefert: Material + drei Vorschlaege, KEINE Entscheidung und keinen sechsten Vorsatz
```
