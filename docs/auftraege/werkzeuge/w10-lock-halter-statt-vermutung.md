# W-10 — Ein Lock ist ein Rest, wenn ihn NIEMAND HÄLT. Nicht, wenn er still ist.

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 04.08. (Planner)*

```yaml
auftrag:
  id: W-10
  strang: werkzeuge
  status: entwurf   # B8 - Planner-Blatt, Gegenleser ist der Evaluator (er hat Tor Teil 2 votiert und kennt die Messreihe)
  gegengelesen_von:
  gegengelesen_am:
  befund:
  anlass: >
    04.08. 22:47 - das Commit-Tor hat ZWEI vollstaendige Git-Indizes weggezogen:
    next-index-28.lock (887796 B) und next-index-30.lock (888008 B, "Git index, version 2,
    6997 entries"), eingestuft als Rest, weil sie STILL standen.
    KORREKTUR AM EIGENEN SCHNITT (Planner, noch vor der Gegenlesung): meine erste Fassung
    schrieb "das Tor zerstoert laufende Arbeit". Das ist NICHT gemessen - ob die zwei Locks
    gehalten wurden, ist nachtraeglich nicht feststellbar. Belegt ist nur: das Tor hat OHNE
    AUSKUNFT entschieden. Der Anlass bleibt gueltig, die Behauptung war zu gross.
  b13_ausnahme: >
    Der Werkzeugstrang ruht (B13). Benannte Ausnahme: das Tor entscheidet ueber Index-Dateien
    ohne zu fragen, wer sie haelt - und die Auskunft, die das koennte, benutzt der Evaluator
    seit dem 03.08. von Hand bei jeder Blockade. Sie gehoert ins Werkzeug.
```

## Warum es dieses Blatt gibt — ein Zug, der im Vorbeigehen zwei Indizes verlor

**Gemessen, nicht erzaehlt** (eigener Commit-Lauf, 04.08. 22:47):

```text
BEISEITE   .git/next-index-30.lock  (888008 Byte, 150s alt, mtime+Groesse ueber 2s STILL)
BEISEITE   .git/HEAD.lock           (0 Byte, 152s alt, mtime+Groesse ueber 2s STILL)
file next-index-30.lock  ->  Git index, version 2, 6997 entries
```

**`next-index-<pid>.lock` ist kein Rest-Marker, sondern der Index selbst** — git schreibt ihn
vollstaendig und schiebt ihn dann an die Stelle des alten.

**Und hier endet, was ich messen kann.** Ob die beiden gehalten wurden, weiss niemand mehr:
`lsof` haette es gesagt, wurde aber nicht gefragt — weder vom Tor noch von mir, bevor sie
verschoben waren. *Vielleicht waren es Reste eines abgestuerzten Laufs; genau solche hat der
Evaluator am 03.08. zweimal gesehen (317 s alt, 885 kB, dreifach als tot belegt).* **Der Befund
ist deshalb nicht „das Tor zerstoert Arbeit", sondern: das Tor entscheidet ueber 888 kB fremden
Zustand, ohne die eine Frage zu stellen, die eine Antwort haette.**

## Bestand — die Regel, die das tut

```text
scripts/commit-pruefen.sh, Zeile 35:
  if { [ "$GROESSE" -eq 0 ] && [ "$ALTER" -ge 60 ]; } || [ "$STILL" -eq 1 ]; then
```

**Das `||` macht den Stillstand zu einer HINREICHENDEN Bedingung.** Sobald `STILL=1` gilt
(Alter ≥ 120 s, mtime und Groesse ueber 2 s unveraendert), spielt die Groesse keine Rolle mehr —
ein 888-KB-Index wird genauso weggeraeumt wie eine leere Datei.

**Und die Zusagen dazu sind das eigentlich Lehrreiche — es sind ZWEI, und sie teilen den Fall:**

```text
Zeile 101  'ein FRISCHER Lock mit INHALT bricht ab'   Probe: Inhalt,   5 s  -> bleibt liegen
Zeile 116  'ein stillstehender Rest wird beiseite'    Probe: Inhalt, 300 s  -> wird weggezogen
```

**Die zweite ist nicht aus Nachlaessigkeit entstanden, sondern aus einem echten Fall:** der
Evaluator wurde am 03.08. **zweimal von einem 317 s alten 885-kB-Lock blockiert** und hat
dreifach belegt, dass nichts mehr lief — *unter anderem mit `lsof`*. **Sein Fall ist so real wie
meiner. Beide verlangen dieselbe Loesung, und sie steht schon in seinem Beleg.**

*Die Grenze zwischen den zwei Zusagen ist heute die Uhr: unter 120 s lebend, darueber tot. Das
ist keine Eigenschaft des Locks, sondern eine Annahme ueber die Welt.*

## Die Entscheidung

**Ein Lock ist ein Rest, wenn ihn NIEMAND HÄLT. Alles andere ist Rückfall, und Rückfall räumt
im Zweifel NICHT auf.**

```text
1  AUSKUNFT (neu):  lsof <lock>  ->  Halter vorhanden?  JA = LEBEND, ausnahmslos.
                    Das ist eine Tatsache, keine Schaetzung. Sie schlaegt jedes andere Merkmal.

2  RUECKFALL (nur wenn lsof fehlt oder nichts sagt):
                    Rest  <=>  GROESSE = 0  UND  ALTER >= 60
                    Der Stillstand entfaellt als eigenstaendige Bedingung - er darf ein
                    Merkmal SCHAERFEN, nie eines ERSETZEN.

3  HARTE GRENZE:    Ein Lock MIT INHALT wird ohne lsof-Auskunft NIE weggezogen.
                    Egal wie alt, egal wie still.
```

**Warum nicht einfach `&&` statt `||`:** *weil dann ein 0-Byte-Rest, der laenger als 120 s liegt,
zusaetzlich still sein muesste — das ist er meistens, aber „meistens" ist der Grund, warum wir
hier stehen.* **Die Auskunft ersetzt die Schaetzung; die Schaetzung wird nur konservativer.**

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/commit-pruefen.sh                  die Halter-Abfrage + die entschaerfte Rueckfall-Regel
  scripts/__tests__/commitPruefen.test.mjs   die Zusagen, darunter die ZURUECKGEHOLTE

Hier bewusst NICHT:
  Stufe 4 (Ort und Zeitpunkt der Aufraeumung)  Sie ist richtig: VOR dem Commit, rekursiv.
                                               W-10 aendert nur, WAS als Rest gilt.
  Stufe 5 (Index ausserhalb des Mounts)        Eigener Posten. Ihr Preis ist getrennt gemeldet
                                               (Phantom-Drift, PB-055/PB-056).
  Die Nachsorge am Ende                        Unveraendert - sie raeumt, was DIESER Lauf erzeugt.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/commit-pruefen.sh
    - scripts/__tests__/commitPruefen.test.mjs
  population_command: "node scripts/zaehle.mjs scripts/commit-pruefen.sh 'lsof' --raute"
  ausschluesse:
    - stelle: "Stufe 4 und Stufe 5"
      grund: "W-10 aendert, WAS ein Rest ist - nicht wann oder wo aufgeraeumt wird."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Das Tor FRAGT, wer den Lock haelt, statt es zu schaetzen."
    pruefung:
      befehl: "node scripts/zaehle.mjs scripts/commit-pruefen.sh 'lsof' --raute"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 04.08.; Partner 'stat' -> 5, die Messung ist nicht leer)"

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "Der Stillstand ist keine HINREICHENDE Bedingung mehr - das `||` ist weg."
    pruefung:
      befehl: "grep -c 'STILL. -eq 1 .; then' scripts/commit-pruefen.sh"
      erwartet: "0"
    ausgangswert: "1 (Zeile 35, die Ursache des Vorfalls)"
    gegenbeweis: |
      Bleibt das `||` stehen, aendert die lsof-Abfrage nichts: sobald lsof schweigt (nicht
      installiert, Netzlaufwerk, Rechte), greift wieder der Stillstand allein - und der
      888-KB-Index faellt beim naechsten Mal genauso.

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "DER VORFALL VON 22:47: ein Lock MIT INHALT, alt und still, bleibt liegen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Wegwerf-Repo. Lock mit Inhalt (>= 1000 Byte), mtime 300 s alt, waehrend der Probe
        unveraendert. KEIN Halter (lsof leer).
          -> LEBENDER LOCK, Commit bricht ab, Lock liegt danach noch da.
        Das ist die Zusage, die am 04.08. gefehlt hat. Sie ist die WICHTIGSTE des Blattes.
      erwartet: "Abbruch, Lock unveraendert vorhanden"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Ein GEHALTENER Lock bleibt liegen - auch als 0-Byte-Datei, auch uralt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B4 - mit Partner-Treffer, sonst misst die Probe nichts:
          (a) Lock von einem lebenden Prozess offen gehalten, 0 Byte, mtime 300 s alt
              -> LEBENDER LOCK, bleibt liegen        <- die neue Auskunft wirkt
          (b) DERSELBE Lock, Prozess beendet
              -> BEISEITE, Commit gelingt            <- ohne (b) waere (a) auch mit
                                                       "raeumt nie auf" gruen
        Gemessen wurde die Trennschaerfe von lsof bereits (Generator, 03.08.):
        Halter -> 1 Zeile, verwaist -> 0 Zeilen.
      erwartet: "zwei Zusagen, die zweite ist die Kontrolle"

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "OHNE lsof faellt das Tor auf die KONSERVATIVE Regel zurueck - und raeumt weniger, nie mehr."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        lsof im PATH unauffindbar machen (PATH einschraenken, NICHT deinstallieren).
          0-Byte-Lock, 300 s alt   -> BEISEITE   (Rueckfall greift)
          Lock mit Inhalt, 300 s   -> LIEGT      (harte Grenze greift)
        Ein Werkzeug, das ohne sein Messgeraet MEHR aufraeumt als mit, ist die
        gefaehrlichste Bauart ueberhaupt.
      erwartet: "zwei Zusagen"

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "DER FALL DES EVALUATORS BLEIBT GRUEN - sein 317s/885kB-Lock wird weiterhin aufgeraeumt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        **Das ist die Zusage, die den Widerspruch aufloest, und sie ist der Grund, warum W-10
        keine Ruecknahme von Tor Teil 2 ist.**
        Die bestehende Zusage in commitPruefen.test.mjs:116 ("ein stillstehender Rest wird
        beiseitegelegt", Inhalt + 300 s) MUSS gruen bleiben. Sie bildet einen echten Vorfall
        ab: ein toter 885-kB-Lock, der den Evaluator zweimal blockiert hat.
        Nach W-10 wird sie aus einem ANDEREN Grund gruen: nicht weil der Lock still ist,
        sondern weil ihn niemand haelt (lsof leer).
          -> derselbe Testfall, dasselbe Ergebnis, andere Begruendung.
        Faellt sie, ist W-10 falsch geschnitten und der Bau haelt an - NICHT die Zusage
        anpassen. *Eine Zusage, die sich an den Bau anpasst, misst den Bau nicht mehr.*
      erwartet: "unveraendert gruen, Begruendung im Kommentar nachgezogen"

  - id: K-06b
    typ: absence
    kritikalitaet: P1
    aussage: "Keine Zusage begruendet das Wegraeumen noch mit dem ALTER allein."
    pruefung:
      befehl: "grep -c 'ganz gleich was drinsteht' scripts/__tests__/commitPruefen.test.mjs"
      erwartet: "0"
    ausgangswert: "1 (Zeile 105 - der Satz beschreibt die Regel, die W-10 ersetzt)"
    gegenbeweis: |
      Bleibt der Satz stehen, liest der naechste ihn als geltende Regel und baut wieder eine
      Uhr-Entscheidung ein. Ein Kommentar, der eine abgeloeste Regel erklaert, ist eine Falle
      mit Halbwertszeit - dieselbe Klasse wie ein Beleg, der auf einen Kommentar zeigt (F-09).

  - id: K-07
    typ: behavioural
    aussage: "Die bestehenden Zusagen bleiben gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/*.mjs"
      erwartet: "0 fail. Ausgangswert misst der Bauende VOR dem Zug und benennt ihn (F-20)."

  - id: K-08
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: lsof-Abfrage entfernt · lsof-Ergebnis ignoriert ·
        `||` wieder eingesetzt · harte Grenze fuer Inhalt entfernt ·
        Rueckfall raeumt MEHR statt weniger · Altersschwelle auf 0.
        Wie viele kommen durch?
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  lsof ist nicht installiert.                          -> K-05 (konservativer Rueckfall)
2  lsof braucht zu lange (Netzlaufwerk).                 -> Zeitgrenze im Aufruf; laeuft sie ab,
                                                            gilt "Halter unbekannt" = LEBEND.
                                                            OHNE ZUSAGE, mit Grund: eine kuenstliche
                                                            Verzoegerung ist im Wegwerf-Repo nicht
                                                            herstellbar, ohne das Messgeraet selbst
                                                            zu bauen. Der Zweig wird am Code belegt.
3  Der Halter ist ein FREMDER Prozess (andere Rolle).     -> K-04a; genau der Schutzfall.
4  Der Halter ist der eigene Lauf.                        -> kommt nicht vor: Stufe 4 laeuft VOR
                                                            dem ersten git-Aufruf dieses Laufs.
5  lsof meldet einen Halter fuer eine geloeschte Datei.   -> `[ -e "$lock" ] || continue` steht
                                                            bereits am Schleifenanfang.
6  Zwei Laeufe raeumen gleichzeitig.                      -> `mv` ist atomar; der Zweite findet
                                                            nichts mehr und faellt auf `continue`.
7  Der Lock liegt tief (.git/refs/heads/<zweig>.lock).    -> die Suche ist rekursiv (W-09-Nachtrag),
                                                            unveraendert.
```

## Rückweg & Entdeckung

**Rückweg:** ein Commit ohne Datenmigration — `git revert` stellt die alte Regel wieder her.
*Die beiseitegelegten Locks sind nie geloescht, nur verschoben; ein Rueckbau verliert nichts.*

**Entdeckung:** die Meldezeile des Tors nennt ab jetzt den GRUND der Einstufung
(`kein Halter` bzw. `Rueckfall: 0 Byte, Ns alt`). *Wer im Log eine BEISEITE-Zeile ohne
Halter-Auskunft sieht, weiss sofort, dass geschaetzt wurde.*

## Was dieses Blatt NICHT behauptet

**Tor Teil 2 war kein Fehlbau.** Es hat einen echten Fall geloest — der Evaluator wurde zweimal
zu Unrecht blockiert, und die drei Wirkungsproben trugen. *Was fehlte, war die Gegenrichtung:
niemand hat gefragt, was die neue Bedingung ZUSAETZLICH einfaengt.* **Das ist die Lehre, nicht
der Schuldspruch** — und sie gehoert als Fehlerklasse notiert: *eine Bedingung, die mit `ODER`
angehaengt wird, erweitert immer; wer sie einbaut, muss ihre Erweiterung messen, nicht ihre
Absicht.*
