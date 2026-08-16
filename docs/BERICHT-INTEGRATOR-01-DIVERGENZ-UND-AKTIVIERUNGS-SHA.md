# BERICHT INTEGRATOR 01 — Divergenz, Herkunft, `AKTIVIERUNGS_SHA`

```yaml
rolle: integrator
betriebsart: NUR_LESEND        # Schritt D/E/F des Ablaufplans, 2-WANN-BIN-ICH-DRAN.md:62-64
gemessen: "16.08.2026, 16:15 CEST, im Integrations-Checkout /Users/yamanuri/Documents/ticket"
gegenstand: "Erzeugnisse 1, 2, 3, 8 und 9 nach 4-WAS-ICH-ABLIEFERE.md:7-15 —
             die fuenf, die KEINE Schreib-Betriebsart brauchen."
kein_eingriff: "Diese Lieferung ist EIN neues Blatt. Kein Merge, kein Push, kein Byte in
                docs/STATUS.md, keine Git-Verwaltungsaenderung. Verbot 13 unberuehrt."
```

## Warum dieses Blatt erst heute existiert — mein Fehler, benannt

Yama am 16.08.: *„du hast wieder nicht committet."* Er hat recht, und der Grund war zur Haelfte
meiner.

Ich habe `NUR_LESEND` als **„nichts schreiben"** gelesen. Das Blatt sagt etwas Engeres. Die
Betriebsart-Tabelle (`2-WANN-BIN-ICH-DRAN.md:19-21`) bindet an `SCHREIBEND` genau zwei Handlungen:
**„integrieren · `docs/STATUS.md` schreiben"**. Und Schritt F derselben Datei (:64) lautet woertlich
**„`AKTIVIERUNGS_SHA` begruendet bestimmen und zunaechst nur BERICHTEN"** — Betriebsart
`NUR_LESEND`. Berichten ist meine Pflicht in dieser Betriebsart, nicht meine Belohnung nach ihr.

**Fuenf der neun Erzeugnisse brauchen keine Freigabe** — 1, 2, 3, 8, 9 sind Berichte. Keines der
dreizehn Verbote in `5-WAS-ICH-NICHT-DARF.md` nennt ein additives Blatt. Ich habe sie trotzdem
nur muendlich geliefert, wo sie mit der Sitzung verschwinden.

> **Ein Bericht, der nur im Gespraech steht, ist kein Erzeugnis.** Er ist von einem nicht
> gemessenen Bericht nicht zu unterscheiden, sobald das Fenster zu ist.

**Was weiterhin auf Yamas Wort wartet, ist unveraendert:** Schritt J. Integrieren und
`docs/STATUS.md` schreiben tue ich bis dahin nicht.

## 1 · Divergenzbericht

Messbefehle mitgefuehrt; die Zahlen driften, die Befehle nicht.

```text
$ git log -1 --format='%H %ai'
f4e7ad792eb97c0077f3674ee150ddf0ba860dcf 2026-08-16 15:00:39 +0200

$ git status --porcelain --untracked-files=all
(leer)                       tracked geaendert 0 · untracked 0

$ git remote -v | awk '/fetch/{print $1"  "$2}'
backup-private  https://github.com/yamasolaraspekt-max/nurihead.git
fork            https://github.com/yamasolaraspekt-max/nuri-head.git
origin          https://github.com/yamasolaraspekt-max/nuri-head.git
upstream        https://github.com/raminsadid2021/nuri-head.git
```

**JE GEGENSTELLE, NICHT JE NAME** — das Rollenblatt verlangt die Unterscheidung, und sie aendert
die Zahl: `fork` und `origin` zeigen auf **dieselbe URL**. Es gibt **zwei eigene Kopien**, nicht
drei. `upstream` liegt auf einem fremden Konto und ist keine eigene Gegenstelle.

```text
$ for r in fork origin backup-private; do git rev-list --count $r/auto/hausplaner-integration..HEAD; git rev-list --count HEAD..$r/auto/hausplaner-integration; done
fork/auto/hausplaner-integration              voraus 0    zurueck 150
origin/auto/hausplaner-integration            voraus 0    zurueck 150
backup-private/auto/hausplaner-integration    voraus 0    zurueck 150
```

**Die Richtung ist einseitig, und das ist der Befund:** voraus **0** an allen drei Zweigen — der
Integrations-Checkout hat der Kopie nichts zu geben. Zurueck **150** — er ist der einzige Ort, an
dem der Rueckfluss nicht ankommt, weil dort niemand zieht. Der Planner hat denselben Punkt am
16.08. gemessen (`docs/STATUS.md`, Block *„DER EINE ECHTE RESTPUNKT"*) und dort mit 6 Commits
beziffert; **innerhalb von rund drei Stunden sind daraus 150 geworden.**

## 2 · Herkunftszuordnung der 150 Commits

```text
$ git log HEAD..fork/auto/hausplaner-integration --format='%s' | sed -E 's/^([a-z-]+)( \(in Yamas Namen\))?:.*/\1/' | sort | uniq -c | sort -rn
  77  release-pruefer
  43  plan-pruefer
  20  generator
   6  planner
   2  evaluator
$ … | grep -cvE '^(planner|plan-pruefer|generator|evaluator|release-pruefer|integrator)'
   0        ohne Rollenmarke
$ git rev-list --count --merges HEAD..fork/auto/hausplaner-integration
  62        Merge-Commits
```

*(Stand der Aufteilung: 148 Commits, 16:14 CEST. Die Summe 148 gegen die 150 aus 16:15 ist kein
Fehler, sondern die Driftgeschwindigkeit selbst — zwei Commits in einer Minute.)*

**Kein „unklar".** Erzeugnis 2 laesst „unklar" ausdruecklich zu und wuerde zur Ablehnung fuehren;
hier trifft es null Mal zu. **Die Herkunft ist sauber, der Rueckstand ist es nicht.**

Auffaellig und ohne Bewertung gemeldet: **77 der 150 tragen die Marke `release-pruefer`**, die
Mehrzahl davon mit dem Betreff *„… Commits transportiert"*. Der Rueckfluss laeuft faktisch ueber
eine Rolle, die ihn nicht benannt bekommen hat — genau die Lage, die der Planner Yama als
Entscheidung R1/R2/R3 vorgelegt hat.

## 3 · `AKTIVIERUNGS_SHA` — begruendet, nicht geraten

```text
AKTIVIERUNGS_SHA = a041590f
$ git log -1 --format='%h %ai %s' a041590f
a041590f 2026-08-15 10:43:20 +0200 release-pruefer: fuenfzehnter Merge-Konflikt aufgeloest …
$ git merge-base --is-ancestor a041590f HEAD  →  exit 0   (Vorfahr von HEAD: JA)
```

**Der Grund, in drei Saetzen:** `a041590f` ist der erste Commit, der **beide Linien vereinigt** —
Arbeitszweig und Release-Linie; vor ihm gibt es keinen Stand, der beides traegt. Alle sechs zu
diesem Zeitpunkt enthaltenen Auftraege stehen auf `ABGENOMMEN` oder `BETRIEBSBESTAETIGT`, keiner
auf `IN_ARBEIT`. Ausgeschlossen ist damit ausdruecklich `c6cc7edc`, der einen laufenden Bau
traegt.

**Verbot 7 eingehalten:** `FORENSISCHER_SHA` = `36e60030` wird **nicht** als Aktivierungsbasis
ausgegeben. Er ist Untersuchungsstand.

**Die zwei Definitionen des Ausgangs-SHA stehen weiterhin nebeneinander** —
`docs/rollenkette/UMSTELLUNG-GETRENNTE-WORKTREES-CHECKLISTE.md` Zeile 10 und Zeile 15. Mein
Vorschlag zur Aufloesung steht seit dem 16.08. mittags: **Zeile 15 behalten** (sie traegt die
Begruendung), Zeile 10 auf einen Verweis reduzieren. **Das ist eine Aenderung an fremdem Text und
wartet auf ein Wort.** Ich fasse sie nicht an.

## Die Barriere ist im Integrations-Checkout NICHT vorhanden

Das gehoert in diesen Bericht, weil es die Voraussetzung von Schritt J beruehrt.

```text
$ git ls-files scripts/rollen-tor.sh                                     0     (mein Stand)
$ git ls-tree fork/auto/hausplaner-integration scripts/rollen-tor.sh     1     (die Kopie)
$ git show fork/…:scripts/commit-pruefen.sh | grep -c 'rollen-tor'       3     (Haken, nur dort)
$ git log -1 --format='%h %ad' --date=format:'%d.%m %H:%M' fork/… -- scripts/rollen-tor.sh
13236d52 16.08 15:56
```

**Das Tor kam um 15:56 auf die Kopie — 56 Minuten nach meinem HEAD.** Es ist also nicht
abgeschaltet und nicht defekt; **es ist an dem einen Ort nicht da, den es schuetzen soll.** Ein
Commit aus diesem Checkout laeuft heute an keiner Pruefung vorbei, weil keine da ist. Nach
Verbot 11 melde ich das, statt es zu umgehen oder mir seine Wirkung zuzuschreiben.

**Und die Reihenfolge ist damit die umgekehrte zu der, die naheliegt:** die Sperre kann die
Divergenz nicht verhindern, denn sie prueft Baum gegen Zweig gegen Rolle — und **jede Rolle
committet in ihrem eigenen Baum regelkonform.** Die Divergenz entsteht genau dann, wenn alle
regelkonform arbeiten. Was fehlt, ist nicht ein Tor, sondern der Zusammenfuehrende.

## Was sich an meinem Ball bewegt hat — P2H-09

**Geschlossen am 16.08. um 13:46, Commit `59ffda57`, auf Yamas Freigabe** — und zwar auf einem
dritten Weg, den weder Planner noch Plan-Pruefer vorgeschlagen hatten: der Release-Pruefer hat
**den Zweig in den ausgestatteten Baum geholt**, statt den ausgestatteten Baum aufzugeben. Vorher
belegt: Branch Vorfahr von HEAD, 0 eigene Commits, 0 uncommittiert. Keine Loeschung.

**Damit ist meine stehende Zahl „0 von 5 Rollenbaeumen haben `.env`" ueberholt.** Sie war richtig
gemessen und hat ihren tragenden Fall verloren. Frisch gemessen, 16:12 CEST:

| Baum | `.env` | `node_modules` | `vendor` | `public/build` |
|---|---|---|---|---|
| `ticket-release-pruefung` *(arbeitet, auf `rolle/release-pruefer`)* | **JA** | JA | JA | JA |
| `ticket-rolle-planner` | nein | nein | nein | nein |
| `ticket-rolle-plan-pruefer` | nein | nein | nein | nein |
| `ticket-rolle-generator` | nein | JA | nein | nein |
| `ticket-rolle-evaluator` | nein | JA | nein | nein |
| `ticket-rolle-release` *(detached, Rest)* | nein | JA | nein | nein |

**Der Fall, der die `.env`-Frage getragen hat, ist ohne `.env`-Kopie geloest.** Es bleiben vier
Baeume ohne `.env` — und **keiner von ihnen hat `vendor` oder `public/build`**, kann also heute
weder einen PHP-Lauf noch eine Browserabnahme fahren. Die `.env`-Entscheidung bleibt Yamas
(sie traegt Geheimnisse), aber **sie blockiert den Integrator nicht mehr.**

**Zwei Reste, gemeldet und nicht angefasst:**

- **Die Checkliste weiss es nicht.** `UMSTELLUNG-GETRENNTE-WORKTREES-CHECKLISTE.md:308` fuehrt
  P2H-09 auch auf der Kopie weiter als **OFFEN**, waehrend der Vorgang seit 13:46 geschlossen ist.
  Ein Zustand, zwei Orte, einer nachgezogen — dieselbe Klasse wie A-20.
- **Mein einziger Ball ist ein Phantom.** `ballbesitz: integrator` steht auf der Kopie in
  Zeile 2467 — **innerhalb des Blocks, den der Release-Pruefer selbst mit „(ueberholt)"
  ueberschrieben hat** (Kopfzeile 2437); der gueltige Block steht darueber auf 2389. Das Ballfeld
  ist im ueberholten Block stehengeblieben. **In meinem Checkout traegt derselbe Block das Wort
  „ueberholt" noch nicht** — die Divergenz, an einem einzigen Feld sichtbar.

## Ein fertiger Auftrag wartet auf meine Hand — A-33

`A-33` steht auf der Kopie auf `BETRIEBSBESTAETIGT`, Ballfeld geraeumt, mit dem Zusatz woertlich:
**„Kette vollstaendig; die AUSFUEHRUNG gehoert dem Integrator"**. Der Planner hat es beim Umschnitt
am 16.08. 13:16 (`b6af3207`) so geschnitten und ausdruecklich **gegen** eine Personalunion
entschieden: *„das Skript liegt unter `scripts/`, AUSGEFUEHRT wird es vom Integrator … die
Einzelschreiber-Regel, angewandt bevor der Integrator existiert."*

Liefergegenstand: `scripts/a33-kennungen-nachziehen.sh`, im Checkout vorhanden, mit
`--trocken`-Betriebsart. **Der Lauf schreibt `docs/STATUS.md` und ist damit `SCHREIBEND`.** Ich
habe ihn nicht gefahren. Der Trockenlauf schreibt nichts und steht bereit.

## 8 · Liste der noch nicht integrierten Bestandteile

**Sie ist nicht leer.** Erzeugnis 8 verlangt sie ausdruecklich auch dann — *„eine fehlende Liste
liest sich wie ‚alles drin'"*.

```text
Zaehlstand je Vorgang:   uebernommen 0 · abgelehnt 0 · offen 150   Summe 150 = vorgelegte Commits
```

**Nichts ist integriert.** Kein Commit dieses Rueckstands ist geprueft, uebernommen oder abgelehnt
worden. Die 150 sind vollstaendig offen und werden es bleiben, bis Schritt J vorliegt.

## 9 · Duerfen die Rollen-Worktrees angelegt werden — ja oder nein

**Die Frage ist durch die Tatsachen ueberholt, und ich sage das statt sie zu beantworten, als
haette ich noch die Wahl:** die fuenf Baeume **existieren bereits** und werden benutzt; angelegt
hat sie Yama nach B-2. `P2H-06` steht auf `UMGESETZT_UNGEPRUEFT` (*„ALLE FUENF UMGEZOGEN"*), vom
Planner am 16.08. 14:56 gemessen.

**Meine eindeutige Aussage, mit Bedingung:** **JA** — sie duerfen bestehen und benutzt werden.
**Bedingung:** vier von ihnen sind unvollstaendig ausgestattet (Tabelle oben) und koennen die
Gates nicht fahren, die ihre Rollen brauchen. **Das ist eine Ausstattungsluecke, keine
Zweigfrage** — und `UNABHAENGIG_BESTAETIGT` sage ich zu `P2H-06` nicht, denn dazu braucht es
nach Verbot 12 einen fremden Pruefer am exakten Commit; meine eigene Messung ist keiner.

## Was ich NICHT geliefert habe, und warum

| Erzeugnis | Zustand | Grund |
|---|---|---|
| 4 · Integrationsprotokoll je Commit | **fehlt** | es gab keine Integration — Schritt J steht aus |
| 5 · Konflikt-/Ablehnungsbericht | **fehlt** | nichts abgelehnt, weil nichts vorgelegt bearbeitet wurde |
| 6 · aktualisierte Statuszeilen | **fehlt** | `docs/STATUS.md` schreiben ist `SCHREIBEND` |
| 7 · Nachweis Abschlusszustand | **fehlt** | es gibt keinen Abschluss zu bezeugen |

**Vier von neun fehlen, und alle vier aus demselben Grund.** Sie stehen hier, damit dieser Bericht
nicht vollstaendiger aussieht, als er ist.
