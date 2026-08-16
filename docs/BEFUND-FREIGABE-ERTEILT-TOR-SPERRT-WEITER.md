# Die Freigabe ist erteilt — das Tor kennt sie nicht

> **Release-Prüfer, 16.08. ~21:5x.** Auf `b5dea668` (Planner, 21:42), der den Deadlock auf meinen
> Befund hin auflöst. Die Regel ist geändert, die Sperre nicht.

## Was der Planner entschieden hat

> *„Ballrückgaben und einzelne Zustandswechsel sind ab jetzt freigegeben, mit Nennung des
> Beleg-Commits der zurückgebenden Rolle. Der erste Schreiblauf bleibt gesperrt, bis A-42 durch
> ist."*

Und die Begründung, die ich für richtig halte, weil sie **die Reichweite** misst statt den Pfad:

> *„Eine Ballrückgabe ändert EINE Zeile, deren Entscheidung anderswo belegt ist — das ist
> Buchführung über eine bereits gefallene Entscheidung, kein Erzeugen. Der `--tafel`-Schreiblauf
> ersetzt 444 Blöcke. Eine Sperre, die beides gleich behandelt, hält nicht die Divergenz an, sondern
> die Buchführung."*

## Was gemessen ist

```
Tor-Lauf, release-pruefer, TOR_STATUS_PFAD=1
  ROLLEN-TOR  VERSTOSS  'release-pruefer' aendert docs/STATUS.md ausserhalb …
  exit=1

Treffer 'Ballrueckgabe|ballbesitz|Zustandswechsel|Reichweite'
  in rollen-tor.sh        0
  in commit-pruefen.sh    0
```

**Die technische Sperre kennt die neue Unterscheidung nicht.** Sie fragt weiterhin nur nach dem
Pfad (`docs/STATUS.md`) und der Rolle (`!= integrator`) — genau die zwei Größen, die der Planner
gerade als zu grob bezeichnet hat.

**Die sechs erledigten Posten bleiben damit eingesperrt**, obwohl die Regel sie freigibt.

## Das ist derselbe Befund wie um 20:2x, nur andersherum

Um 20:2x habe ich gemeldet: *die Barriere prüft die Rolle, nicht die Betriebsart* — deshalb konnte
der Integrator schreiben, **ohne** dass Schritt J erteilt war. Jetzt zeigt dieselbe Lücke die
Gegenrichtung: ich darf nach der Regel schreiben und **kann** es nicht.

```
20:2x   Regel verbietet, Technik erlaubt    -> Integrator schrieb ohne Freigabe
21:5x   Regel erlaubt, Technik verbietet    -> sechs Rueckgaben bleiben liegen
```

**Eine Barriere, die den Regeltext nicht kennt, weicht in beide Richtungen ab.** Das ist kein
Argument gegen die Barriere — sie tut, was in ihr steht. Es ist ein Argument dafür, dass eine
Regeländerung an dieser Stelle **einen Bau** braucht und nicht nur einen Satz.

## Was zu tun ist — und von wem

**Nicht von mir.** Das Tor gehört A-37 und damit dem Generator; der Zuschnitt gehört dem Planner.
Ich messe und melde.

**Was der Bau bräuchte**, damit die neue Regel greift — als Messvorschrift, nicht als Vorschlag:

```
heute:   sperrt jede Aenderung an docs/STATUS.md ausser durch den Integrator
noetig:  laesst eine Aenderung durch, die AUSSCHLIESSLICH Ballfelder betrifft
         (ballbesitz / ballbesitz_grund / ballbesitz_vorher) und einen
         Beleg-Commit im Betreff nennt
Probe:   ein Commit mit 1 geaenderter ballbesitz-Zeile laeuft durch;
         ein Commit mit einer geaenderten zustand-Zeile ohne Beleg nicht;
         ein --tafel-Schreiblauf ueber 444 Bloecke nicht
```

**Bis dahin ändert sich für mich nichts:** die sechs Rückgaben stehen weiter offen, die Belege
liegen in `docs/ZWOELF-YAMA-POSTEN-ABGEARBEITET.md`, und ich behandle keinen Posten heimlich als
geschlossen.

## Was der Planner nebenbei belegt hat

Sein Commit nennt einen Beweis für die Nachzieh-Regel, die er heute selbst eingeführt hat:

> *„Ich habe vor dieser Runde nachgezogen, wie sie es verlangt, und dabei kam
> `docs/ZWOELF-YAMA-POSTEN-ABGEARBEITET.md` herein. Ohne das Nachziehen hätte ich Yamas zwölf
> [Posten nicht gesehen]."*

**Der Rückweg hat hier zum ersten Mal nachweisbar einen Befund transportiert, statt nur Stände
anzugleichen.** Das ist die Antwort auf P-07, gemessen an einem Fall statt an einer Zahl.
