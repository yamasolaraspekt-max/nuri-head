# A-11 — Die Rollenkennung entsteht im Tor, nicht in der Selbstdisziplin

```yaml
auftrag: A-11
titel: "Commit-Tor: die Rolle kommt aus der Umgebung und wird der Botschaft vorangestellt - fehlt sie, gibt es keinen Commit"
spur: A                            # Werkzeug am Commit-Weg
heimat_app: ticket
status_steht_in: docs/STATUS.md    # §16: EINE Statuswahrheit. Hier steht keine zweite.
basis_sha: 229ad0be
anlass: "PROZESSPRUEFUNG-02, Befund 0 · B4 angenommen in 229ad0be · Messung des Evaluators in 12982e6c"
prioritaet: P1
claim: "planner 10.08. 18:4x — Claim VOR dem Schnitt. B4 ist angenommen, der Bau braucht laut
        229ad0be ausdruecklich einen Auftrag, und das Schneiden ist nach §4 Planner-Sache. Die vom
        Melder verlangte Konfliktpruefung steht unten und ist selbst gemessen."
ballbesitz: plan-pruefer (DoR), danach generator
zaehlfrage_offen: "Ob dieser Auftrag in die alte oder die neue Zehnergruppe zaehlt, entscheidet der
                   Plan-Pruefer. Der Melder hat sich das ausdruecklich versagt, weil er die Pruefung
                   geschrieben hat, deren Zaehler daran haengt — ich versage es mir aus demselben
                   Grund nicht, sondern weil es keine Planner-Entscheidung ist."
```

## Der Befund — nicht das Lesen ist schuld, sondern der Schreibweg

Der Evaluator hat gemessen (`12982e6c`), nachdem er seine eigene erste Formulierung
richtiggestellt hatte:

```text
64 Commits tragen '^evaluator'
davon mit Marke 'evaluator-2'          1     — gesetzt, nicht erzwungen
Autor und Committer                    bei ALLEN Rollen Yama, trennen nichts
Betreffe ohne Auftragsnennung          46 von 64
b29bb79d 'evaluator: A-05 ABGENOMMEN'  stammt nicht von ihm, ist aber wie seine signiert
```

> **Der entscheidende Satz, und er ist scharf:** Die Trennung scheitert **nicht am Lesen** und nicht
> an einem besseren `grep`, sondern daran, dass **beim Schreiben nichts Unterscheidendes entstanden
> ist**. Befund 0 ist keine Lesefehler-Klasse, sondern eine **Lücke im Schreibweg**.

**Eigene Gegenmessung an den letzten 40 Commits:** `25` tragen ein Rollen-Präfix, **`15` nicht**.
Die Selbstdisziplin greift also in gut sechzig Prozent der Fälle — und das ist genau die Quote, an
der eine Zählung zerbricht.

## Ist-Zustand, am Tor gemessen

```text
commit-pruefen.sh:51    BOTSCHAFT="$1"; shift        <- Botschaft wird ungeprueft uebernommen
commit-pruefen.sh:384   git commit -q -m "$BOTSCHAFT" -- "$@"
Pruefung auf eine Rollenkennung    NICHT VORHANDEN (0 Treffer)
```

**Rot heute:** jeder Commit ohne Marke laeuft durch. Das Tor prueft Pfade, Syntax und Locks — die
Herkunft der Aenderung prueft es nicht.

## DECISION — die Marke kommt aus der Umgebung und wird bei der Annahme gesetzt

```text
QUELLE      Umgebungsvariable  TICKET_ROLLE
FORM        klein geschrieben, Bindestriche, optionale Instanznummer:  ^[a-z][a-z-]*(-[0-9]+)?$
            Beispiele: planner · generator · evaluator · evaluator-2 · plan-pruefer · release-pruefer
FEHLT SIE   KEIN Commit, exit 2 (Aufruffehler-Klasse, wie die fehlende Botschaft in Z.47-50)
WIRKUNG     "<marke>: " wird der ERSTEN Zeile der Botschaft vorangestellt
```

**Die Marke wird bei der Botschaft-Annahme gesetzt (Z.51), NICHT am Commit-Aufruf (Z.384).**

> **Das ist eine Bauvorgabe aus der Konfliktprüfung, nicht Geschmack.** A-07 arbeitet am Ende des
> Skripts (Index-Angleich **nach** dem Commit) und damit in der Nachbarschaft von Z.384. Wird die
> Marke am Kopf in `BOTSCHAFT` eingesetzt, bleibt Z.384 **unverändert** und A-11 ist vollständig auf
> Z.46–52 begrenzt. Damit ist der Konflikt nicht verwaltet, sondern beseitigt.

**Doppelung und Widerspruch — die zwei Fälle, die eine naive Fassung falsch macht:**

```text
Botschaft beginnt mit GENAU dieser Marke      -> nichts voranstellen (kein "planner: planner: …")
Botschaft beginnt mit einer ANDEREN Rollenmarke -> WIDERSPRUCH, kein Commit, exit 2
Botschaft beginnt mit keiner Marke            -> voranstellen
```

> Der Widerspruchsfall ist der wertvollere von beiden: er faengt eine Botschaft, die sich als
> `evaluator:` ausgibt, waehrend die Umgebung `generator` sagt. **Genau diese Verwechslung ist in
> `b29bb79d` schon einmal aufgetreten** — ein Commit mit Evaluator-Betreff, der nicht vom Evaluator
> stammt. Eine Marke, die man uebersteuern kann, ohne dass es auffaellt, ist keine Marke.

## Nicht-Ziele

- **Die Auftragsnennung im Betreff.** 46 von 64 Betreffen nennen keinen Auftrag — das ist ein
  **eigener** Mangel und braucht eine eigene Entscheidung. *Hier mitzubauen waere die eigenmaechtige
  Umfangserweiterung, die §7 dem Generator verbietet; sie steht dem Planner genauso nicht zu.*
- **Rueckwirkende Reparatur.** Die 64 Commits der abgeschlossenen Gruppe bleiben ununterscheidbar;
  Befund 0 gilt fuer sie weiter. `229ad0be` sagt das ausdruecklich.
- **Keine Aenderung an Autor/Committer.** Beide bleiben Yama. *Eine Rolle ist keine Identitaet — sie
  in `user.name` zu schreiben waere eine zweite Wahrheit neben der Marke.*
- **Kein Erzwingen einer bestimmten Instanznummer.** Das Tor prueft die Form, nicht die Richtigkeit
  der Nummer. *Wer sich als `evaluator-2` ausgibt, ohne es zu sein, ist ein Governance- und kein
  Werkzeugproblem.*

## Scope

```text
scripts/commit-pruefen.sh                  Z.46-52: Marke annehmen, pruefen, voranstellen
scripts/__tests__/commitPruefen.test.mjs   die Zusagen
```

*Ausdruecklich NICHT im Scope: Z.384 und alles nach dem Commit-Aufruf (A-07-Zone), Z.73-106
(`repo_git_laeuft`, A-09-Zone).*

## Konfliktprüfung (§5) — der dritte Anwärter auf dieselbe Datei, selbst gemessen

**Der Melder hat sie ausdruecklich vor dem Schnitt verlangt. Sie ist der Grund, warum die DECISION
oben eine Bauvorgabe enthaelt.**

```text
Blatt   Zustand      Zone in commit-pruefen.sh (397 Zeilen)
A-11    DIESES       Z.46-52    Botschaft-Annahme
A-07    ENTWURF      Z.58-61    INDEX_HEIMAT/GIT_INDEX_FILE + trap
                     nach 384   Index-Angleich nach dem Commit
A-09    ENTWURF      Z.73-106   repo_git_laeuft()
A-04    IN_ARBEIT    andere Datei: scripts/buehnen-waechter.sh — KEINE Beruehrung

-> Die drei Zonen sind DISJUNKT, solange A-11 die Marke am Kopf setzt und Z.384 nicht anfasst.
   Genau das schreibt die DECISION vor.
```

**REIHENFOLGE — gilt: `A-07 → A-09 → A-11`** (entschieden in `e3d7b2c8` von der ersten
Planner-Instanz).

```text
1  A-07   P0-Rest, groesster Eingriff (trap + Angleich)
2  A-09   P2, kleine Funktionsaenderung in repo_git_laeuft()
3  A-11   DIESES — zuletzt, und das ist richtig so
```

> **⚠ KORRIGIERT — meine erste Fassung stellte A-11 vor A-09, und die Begruendung war falsch.**
>
> Ich hatte argumentiert: *„A-07s Schaden ist reparierbar, A-11s nicht — jeder Tag ohne A-11
> vergroessert eine Menge, die niemand mehr aufloest."* Die erste Planner-Instanz hat dagegen
> gesetzt: **„A-11s Nutzen beginnt erst mit der naechsten Zehnergruppe, und die kann nicht
> beginnen, solange der Zaehler auf 10 steht — frueher bauen bringt nichts frueher."**
>
> **Selbst nachgemessen statt uebernommen** (`docs/AUFTRAGSZAEHLER.md`):
>
> ```text
> Z.50   "Stand: 10 von 10 — die §13-PROZESSPRUEFUNG IST FAELLIG"
> Z.74   "Zaehler-Reset: steht aus. §13 laesst die neue Zehnergruppe erst nach
>         Umsetzung beginnen"
> ```
>
> **Ihr Argument traegt, meines nicht.** Mein Satz war formal richtig — unmarkierte Commits bleiben
> ununterscheidbar — aber er ging am **Zweck** vorbei: die laufende Gruppe ist bereits als
> ununterscheidbar abgeschrieben (`229ad0be`: *„rueckwirkend repariert B4 nichts, die 64 Commits
> dieser Gruppe bleiben ununterscheidbar"*). Der Nutzen der Marke ist die Zaehlbarkeit der
> **naechsten** Gruppe, und die beginnt vor der Prozesspruefung nicht.
>
> **Der Denkfehler, benannt:** Ich habe „Schaden waechst" gegen „Schaden reparierbar" abgewogen,
> **ohne zu pruefen, ob der Schaden im relevanten Zeitfenster ueberhaupt zaehlt.** Zwei Zeilen im
> Auftragszaehler haetten es gezeigt — dieselbe Unterlassung wie bei meiner Kantenzeile in A-08
> (dort: zwei Zeilen `grep` im Tor) und bei meinem Push-Alarm (dort: ein `git fetch`).
>
> Ihre weiteren zwei Gruende, die ich uebernehme: **Maengel vor Faehigkeit bei geteilter Datei** —
> A-07 und A-09 beheben Defekte, A-11 fuegt eine Faehigkeit hinzu. Und A-11 aendert als einziges die
> **Meldeform** des Tors und trifft zuletzt einen Stand, in dem die anderen beiden abgenommen sind.

**Zur Doppelentscheidung selbst — ein Beleg fuer P-02, nicht nur eine Panne:** Zwei
Planner-Instanzen haben dieselbe Reihenfolgefrage binnen **drei Minuten** verschieden entschieden
(ich 18:42, sie 18:45). Beide hielten sich fuer zustaendig, und beide waren es: der Claim liegt auf
dem *Blatt*, die Reihenfolge *ueber Blaetter hinweg* ist Planner-Sache — nur sagt das nicht, welche
Planner-Instanz. **Es gilt ihre Fassung, weil sie besser begruendet ist, nicht weil sie spaeter kam.**
*Das gehoert in P-02 (parallele Instanzen derselben Rolle) als gemessener Fall.*

## Akzeptanzkriterien

**Jedes P1 ist an `229ad0be` wirksam rot** — der Plan-Pruefer bestaetigt das vor dem Bau. *Die
Rot-Lage zu A-11-1 ist strukturell: `grep` auf eine Rollenpruefung im Tor liefert 0 Treffer.*

**A-11-1 (P1, der Befund):** Ist `TICKET_ROLLE` **nicht gesetzt** (oder nach Trimmen leer), gibt es
**keinen Commit**, `exit 2`, und eine Zeile nach stderr, die die Variable und die erlaubte Form
nennt. *Rot an der Basis: heute committet das Tor ohne jede Rollenpruefung.*

**A-11-2 (P1, die Wirkung):** Ist sie gesetzt und die Botschaft traegt **kein** Präfix, beginnt die
erste Zeile des Commits mit `"<marke>: "`. *Rot an der Basis: die Botschaft geht unveraendert durch.*

**A-11-3 (P1, keine Doppelung):** Beginnt die Botschaft bereits mit **genau dieser** Marke, wird
**nichts** vorangestellt — der Betreff bleibt byte-identisch zur Eingabe. *Rot an der Basis nicht
messbar (es gibt keine Marke), deshalb gegen die NEUE Fassung zu pruefen: ohne dieses Kriterium
entstehen `planner: planner: …`-Betreffe bei den heute schon 25 von 40 praefigierten Botschaften.*

**A-11-4 (P1, der Widerspruch):** Beginnt die Botschaft mit einer **anderen** bekannten Rollenmarke
als `TICKET_ROLLE`, gibt es **keinen Commit**, `exit 2`. *Der Fall `b29bb79d` — Evaluator-Betreff
ohne Evaluator-Herkunft.*

**A-11-5 (P1, Form):** Eine Marke, die der Form `^[a-z][a-z-]*(-[0-9]+)?$` **nicht** entspricht
(Leerzeichen, Doppelpunkt, Grossbuchstaben, fuehrende Ziffer), wird abgewiesen -> `exit 2`.
*Sonst wandert beliebiger Text in den Betreff und die Zaehlung ist wieder kaputt.*

**A-11-6 (`must_preserve`):** **Alle bestehenden Zusagen bleiben gruen** — insbesondere die
Pfad-, Syntax- und Lock-Prüfungen und die A-08-Kette. Die Suite laeuft dazu mit gesetzter
`TICKET_ROLLE`. *§7: keine Abschwaechung bestehender Tests.*

**A-11-7 (P1, Mutationsprobe):** Mindestens **vier** Mutationen fallen: Marken-Prüfung entfernt ·
fehlende Marke nur gewarnt statt abgebrochen · Doppelungs-Schutz entfernt · Widerspruchs-Prüfung
entfernt. *Nach dem Vorbild `A-08-6`: eine Barriere ohne Mutationszusage ist stumm entfernbar.*

**A-11-8 (P2, Mehrzeiligkeit):** Bei einer mehrzeiligen Botschaft wird die Marke **nur der ersten
Zeile** vorangestellt; der Rumpf bleibt byte-identisch. *Die Botschaften dieser Gruppe sind
regelmaessig 20+ Zeilen lang — eine Marke im Rumpf waere Unsinn und wuerde Zitate verfaelschen.*

## Kantenliste

```text
TICKET_ROLLE nicht gesetzt                     -> exit 2, kein Commit
gesetzt, aber leer / nur Leerzeichen           -> wie nicht gesetzt
"Planner" (grossgeschrieben)                   -> Formfehler, exit 2
"evaluator-2"                                  -> gueltig (Instanznummer ist der Zweck)
"evaluator:" (mit Doppelpunkt)                 -> Formfehler, exit 2
Botschaft beginnt "planner: …", Marke planner   -> unveraendert (A-11-3)
Botschaft beginnt "evaluator: …", Marke planner -> exit 2 (A-11-4)
Botschaft beginnt "A-07: …" (Auftrag, keine Rolle) -> Marke voranstellen, kein Widerspruch
mehrzeilige Botschaft                          -> nur erste Zeile (A-11-8)
Botschaft beginnt mit Leerzeichen              -> nach Trimmen bewerten
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle     KEINE
Produktivcode    scripts/commit-pruefen.sh (Z.46-52) + scripts/__tests__/commitPruefen.test.mjs
Testdaten-Ziel   KEINES
Prozessbindung   ENTFAELLT - kein Serverstart, keine Datenbank; Proben im Wegwerf-Repo
Werkzeuge        node-Testsuite - vorhanden UND in Gebrauch (38 Zusagen nach A-08)
```

**Erstnutzer** (§5 — das Tor ist vorhanden, die Pflicht ist neu): **jede Rolle beim naechsten
Commit, ohne eigenen Aufruf.** *Und zwar sofort blockierend: wer `TICKET_ROLLE` nicht setzt, kommt
nicht durch. Das ist gewollt — B4 heisst ausdruecklich „Barriere im Befehl, kein Vorsatz" —, aber es
gehoert in die Uebergabe, sonst laeuft die erste Rolle nach dem Bau in eine Sperre, die sie nicht
erwartet.*

## Rueckweg und Entdeckung

**Rueckweg:** Skriptaenderung ohne Datenmigration, der Commit ist zurueckdrehbar. *Zusaetzlich ist
die Sperre im Notfall von aussen ueberbrueckbar, indem `TICKET_ROLLE` gesetzt wird — der Rueckweg
ist also nicht einmal ein Revert, sondern eine Zuweisung.*

**Entdeckung:** Der Zweck ist die Zaehlbarkeit, also ist das Signal ein `grep`:

```text
git log --format='%s' | grep -cvE '^[a-z][a-z-]*(-[0-9]+)?: '
-> muss fuer alle Commits NACH dem Bau 0 sein.
   Steigt die Zahl wieder, ist die Barriere umgangen oder entfernt worden.
```

*Genau diese Zeile ist es, die Befund 0 heute nicht schreiben kann — sie ist die Abnahme und die
Dauerkontrolle in einem.*

```yaml
fehlerklasse: SPEC
verursacher: prozess (kein Bau-Fehler — die Marke war nie spezifiziert)
prioritaet: P1
warteschlange: A-07 -> A-09 -> A-11 (korrigiert nach e3d7b2c8; meine erste Fassung war falsch begruendet)
```

---

## §11-Bericht des Generators (Bau 10.08., frische Instanz)

```yaml
auftrag: A-11
basis: bc1470bc          # Bau-Basis; Blatt-basis_sha 229ad0be ist der Schnitt-Stand, scripts/ zwischen meiner Rot-Messung (0fef1a56) und dem Bau unveraendert (diff 0 Zeilen)
commit: b0f4c444         # der Bau (2 Dateien); dieser Blatt-Commit ist der Pruef-Stand
scope:
  - scripts/commit-pruefen.sh                # NUR Botschaft-Annahme: Einbau Z.53-85 direkt nach `BOTSCHAFT="$1"; shift` (Z.51). EIN Einfuege-Hunk; Commit-Aufruf (vorher Z.508, durch den Einbau Z.543) und A-07/A-08/A-09-Zonen content-unangetastet
  - scripts/__tests__/commitPruefen.test.mjs # 1 zentrale Umgebungszeile im Kopf + Rollen-Helfer + 11 Zusagen, alles ANGEHAENGT; kein Bestandstest veraendert
  - docs/auftraege/aktiv/A-11-rollenmarke-im-tor.md   # dieser Bericht
  - docs/STATUS.md                           # IN_ARBEIT ffd06c1a; CODE_FERTIG + Mitteilung an alle Rollen folgt
tests:
  statisch: pass         # bash -n exit 0 · node --check exit 0
  unit: "61/61"          # node --test scripts/__tests__/commitPruefen.test.mjs — Basis 50/50, neu 61/61 (50 Bestand + 11 A-11)
  backend: nicht_anwendbar
  schema: nicht_anwendbar
  build: nicht_anwendbar
  browser: nicht_anwendbar
abweichungen:
  - "MARKE mechanisch definiert: ein Praefix zaehlt nur in exakt der verbuchten Form '<marke>: ' (Doppelpunkt UND Leerzeichen) — deckungsgleich mit dem Entdeckungs-grep des Blatts. Folge: 'generator:x' ohne Leerzeichen ist KEINE Marke und wird praefigiert ('generator: generator:x'); Widerspruch kann nur ein form-echtes Praefix ausloesen."
  - "A-11-3 + Leerzeichen-Kante zusammen: BEWERTET wird nach Trimmen, VERBUCHT byte-identisch. Eine Botschaft '  generator: x' mit Marke generator bleibt samt fuehrender Leerzeichen stehen (A-11-3 verlangt byte-identisch) — der Entdeckungs-grep zaehlt so eine Zeile dann als unmarkiert. Bewusst nicht geglaettet: Glaetten waere eine Veraenderung, die das Blatt nicht bestellt."
  - "Suite-Integration mit kleinstem Eingriff: TICKET_ROLLE='probe' EINMAL zentral im Testkopf (process.env) statt in 50 Einzeltests; wirkt ueber ...process.env auch auf die vier Zusagen, die das Tor direkt per execFileSync fahren. Kein Bestandstest umgeschrieben, keiner abgeschwaecht."
  - "§3-Wartezeit vor dem Start: W-02/1 stand IN_ARBEIT, als die Station besetzt wurde — GEWARTET bis 58342f47 (CODE_FERTIG), erst dann IN_ARBEIT gesetzt (ffd06c1a)."
  - "BEIFANG-OFFENLEGUNG: meine vorbereitete Tafelzeile (A-11 IN_ARBEIT) wurde vom parallelen STATUS-Commit 7f592b20 mitgenommen, BEVOR IN_ARBEIT wirklich galt — die Zeile stand damit frueher im Log als der Zustand. Gemessen, im STATUS-Datensatz (beginn_bau) und hier deklariert; W-02/1 (58342f47) hat die Zeile bereits als gueltig gelesen und §3 danach bemessen."
offene_akzeptanz: []
```

### Rot an der Basis, selbst gemessen (bc1470bc; scripts/ identisch mit 0fef1a56)

```text
grep Rollenpruefung im Tor            0 funktionale Treffer (2x 'Rolle' sind Kommentare Z.93/Z.490)
Wegwerf-Repo, TICKET_ROLLE entfernt   exit=0, Commit 'Basisprobe ohne Marke' lief durch   -> A-11-1 rot
Wegwerf-Repo, TICKET_ROLLE=generator  Betreff unveraendert, KEIN Praefix                  -> A-11-2 rot
Suite an der Basis                    tests 50 / pass 50 / fail 0
```

### Kriterien, je mit Beleg

```text
A-11-1  ERFUELLT  2 Zusagen (fehlt · nur Leerzeichen): exit 2, rev-list --count bleibt 1,
                  stderr nennt TICKET_ROLLE und die Form ^[a-z][a-z-]*(-[0-9]+)?$ woertlich
A-11-2  ERFUELLT  'Anbau ohne Praefix' -> Betreff 'generator: Anbau ohne Praefix'
A-11-3  ERFUELLT  'generator: schon markiert' -> byte-identisch, kein Stapeln (gegen die NEUE
                  Fassung gemessen, wie im Blatt deklariert)
A-11-4  ERFUELLT  'evaluator: fremde Feder' bei Marke generator -> exit 2, kein Commit, Meldung
                  nennt WIDERSPRUCH + beide Marken; Kante Instanznummer: evaluator-2 gegen
                  evaluator ist Widerspruch; Kante getarnt: '  evaluator: ...' faellt nach Trimmen
A-11-5  ERFUELLT  'Planner' · 'evaluator:' · '2evaluator' · 'plan pruefer' -> je exit 2, 0 Commits;
                  'evaluator-2' GUELTIG -> 'evaluator-2: Zweitinstanz am Werk'
A-11-6  ERFUELLT  alle 50 Bestandszusagen laufen unveraendert und namensgleich gruen (61 = 50 + 11,
                  nur Anhang, kein Bestandstest beruehrt); Suite mit gesetzter TICKET_ROLLE
A-11-7  ERFUELLT  vier Mutationen, alle gefallen — Tabelle unten, md5 vor/nach jeder Probe identisch
A-11-8  ERFUELLT  Mehrzeiler: %B == 'generator: Kopfzeile\n\n<rumpf>\n' byte-genau, Rumpf enthaelt
                  absichtlich eine markenaehnliche eingerueckte Zeile — unangetastet
Kante 'A-07: ...' ERFUELLT  -> 'generator: A-07: Auftrag, keine Rolle' (Auftrag ist keine Rolle)
```

### Mutationsproben (A-11-7) — Gruen/Rot/Gruen, am ECHTEN Tor

```text
Ausgang                                   61/61 gruen · md5 e5fece559500d5c90869cf6c2ada40da
M1 Marken-Pruefung entfernt (Block weg)   tests 61 / pass 51 / FAIL 10
M2 fehlende Marke nur gewarnt             tests 61 / pass 59 / FAIL 2   (beide A-11-1-Zusagen)
M3 Doppelungs-Schutz entfernt             tests 61 / pass 60 / FAIL 1   (A-11-3)
M4 Widerspruchs-Pruefung entfernt         tests 61 / pass 58 / FAIL 3   (A-11-4 x2 + Tarn-Kante)
Nach JEDER Ruecknahme                     md5 e5fece559500d5c90869cf6c2ada40da (byte-identisch)
Abschluss                                 tests 61 / pass 61 / fail 0
```

### Realtest und Dauerkontrolle

Der Bau-Commit `b0f4c444` ging **ohne** Praefix ins Tor; die Marke `generator: ` davor stammt vom
Tor selbst. Erstnutzer-Lage geprueft: **kein Skript** ruft das Tor auf (grep ueber scripts/ —
die einzigen Treffer sind Zeichenketten in der W-07-Erlaubnislisten-Zusage und Kommentare);
Erstnutzer sind also ausschliesslich die Rollen, und fuer die steht die Mitteilung in STATUS.md.
Die Dauerkontrolle des Blatts gilt ab `b0f4c444`:
`git log --format='%s' <bau>.. | grep -cvE '^[a-z][a-z-]*(-[0-9]+)?: '` muss 0 bleiben.

---

## Evaluator-Votum A-11 (10.08.2026, frische Instanz)

```yaml
auftrag: A-11
commit: 28760966          # Pruef-SHA; Bau b0f4c444, Skript am Mess-HEAD byte-identisch (diff 0, md5 e5fece559500d5c90869cf6c2ada40da)
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "Zwei eigene Mutationen am Tor im eigenen Worktree am Pruef-SHA: Widerspruchs-Pruefung entfernt -> 58/61 (exakt die drei Widerspruchs-Zusagen rot); fehlende Marke nur gewarnt -> 59/61 (beide A-11-1 rot, 'kam 0' statt exit 2). Nach JEDER Ruecknahme md5 e5fece559500d5c90869cf6c2ada40da, Abschluss 61/61. Zusaetzlich schaerfere P8-Probe: Rumpf mit 'generator: ' am ZEILENANFANG im Rumpf — byte-identisch verbucht, KEIN Widerspruchs-Fehlalarm (nur die erste Zeile wird bewertet)."
browser: nicht_anwendbar
befunde: []
```

**Mess-Stand:** HEAD bei Beginn `fd0d61de`, bei Abschluss `51fab811` — die drei Zwischen-Commits sind
reine Doku (`git diff fd0d61de 51fab811 -- scripts/` = 0 Zeilen, md5 des Tors unveraendert `e5fece55…`).
Deklarierte Basis `bc1470bc` gegen Bau-Elter `def5d826` geprueft: `git diff bc1470bc def5d826 -- scripts/`
= 0 Zeilen (3 Doku-Commits dazwischen) — die Basis-Angabe traegt.

### Je Kriterium, selbst gemessen (Proben im Wegwerf-Repo, Aufbau wie `wegwerfRepo()` der Suite)

```text
Suite HEAD          tests 61 / pass 61 / fail 0  (node --test scripts/__tests__/commitPruefen.test.mjs)
Suite Basis         tests 50 / pass 50 / fail 0  (worktree an b0f4c444^ = def5d826)
Statik              bash -n exit 0 · node --check exit 0

A-11-1  ERFUELLT    ohne TICKET_ROLLE: exit=2, commits=1 (kein Commit), stderr nennt TICKET_ROLLE
                    UND die Form ^[a-z][a-z-]*(-[0-9]+)?$ woertlich; Marke '   ' (nur Leerzeichen)
                    identisch: exit=2, commits=1
A-11-2  ERFUELLT    Marke evaluator, Botschaft 'Anbau ohne Praefix' -> Betreff 'evaluator: Anbau ohne Praefix'
A-11-3  ERFUELLT    'evaluator: schon markiert' bei Marke evaluator -> %B per od geprueft:
                    'e v a l u a t o r :   s c h o n   m a r k i e r t \n' — byte-identisch, kein Stapeln
A-11-4  ERFUELLT    'evaluator: fremde Feder' bei Marke generator -> exit=2, commits=1, stderr:
                    "WIDERSPRUCH: … 'evaluator' … TICKET_ROLLE='generator'"; Kante Instanznummer:
                    'evaluator-2: …' gegen evaluator -> exit=2 (WIDERSPRUCH nennt beide)
A-11-5  ERFUELLT    'Planner' / '2evaluator' / 'evaluator:' -> je exit=2, commits=1;
                    'evaluator-2' GUELTIG -> 'evaluator-2: Zweitinstanz am Werk'
A-11-6  ERFUELLT    61 = 50 Bestand + 11 neu; Namensvergleich der test('…')-Zeilen Basis vs. HEAD:
                    comm -23 = 0 (kein Bestandstest entfernt/umbenannt/abgeschwaecht). Zonen:
                    Bau-Diff am Tor ist EIN Einfuege-Hunk an der Botschaft-Annahme (@@ -49,6 +49,41),
                    A-07-Angleich/A-08-Kette/A-09-Wege content-unangetastet; HEAD-Skript = Bau-Skript
A-11-7  ERFUELLT    zwei EIGENE Mutationen (s. gegenprobe), davon die verlangte Widerspruchs-Ruecknahme;
                    Fail-Bilder decken die Generator-Tabelle M2/M4 (fail 2 bzw. fail 3)
A-11-8  ERFUELLT    Mehrzeiler mit markenaehnlicher eingerueckter UND unmarkiert-linksbuendiger
                    'generator: '-Rumpfzeile: %B == 'evaluator: <botschaft>\n' per cmp byte-identisch,
                    kein Widerspruchs-Abbruch — schaerfer als die Suite-Zusage
Kante 'A-07: …'     ERFUELLT -> 'evaluator: A-07: Auftrag, keine Rolle'

Entdeckungs-grep    git log --format='%s' b0f4c444.. | grep -cvE '^[a-z][a-z-]*(-[0-9]+)?: '  ->  0
                    (5, spaeter 8 Commits seit dem Bau, alle markiert)
```

### Realtest, nachvollzogen — mit einer ehrlichen Grenze

Die Marke an `b0f4c444` (`%B` beginnt `generator: A-11 gebaut: …`) ist **rueckwirkend nicht
unabhaengig beweisbar**: A-11-3 laesst ein selbst getipptes Praefix absichtlich byte-identisch
stehen, ein nachtraeglicher Beobachter kann „vom Tor gesetzt" und „selbst getippt" nicht
unterscheiden. Der Bericht ist konsistent (Botschaftstext im Blatt ohne Praefix, Betreff mit),
mehr gibt die Vergangenheit nicht her. **Der lebende Realtest ist dieser Votum-Commit selbst:**
er geht ohne Praefix mit `TICKET_ROLLE=evaluator` ins Tor — die Marke am Betreff stammt vom Tor.

### Die fuenf deklarierten Abweichungen, je gewuerdigt

```text
1 Marken-Definition '<marke>: '   GEDECKT. Deckungsgleich mit dem Entdeckungs-grep, mechanisch statt
                                  Rollen-Liste. RANDNOTIZ (kein P0/P1): jedes form-echte Praefix gilt
                                  als Marke — Praefix-Zensus der Historie: 129x 'docs: ', 2x 'test: ',
                                  1x 'fix: ' u. a. Eine kuenftige Botschaft 'docs: …' bei anderer
                                  TICKET_ROLLE faellt als WIDERSPRUCH (Richtung Blockade, sichtbar,
                                  durch Umformulieren behebbar — kein stilles Loch). Ob 'docs' u. ae.
                                  zulaessige Marken sein sollen, waere eine Planner-Entscheidung.
2 Trimm-Bewertung vs. Verbuchung  GEDECKT. '  evaluator: …' mit Marke evaluator bliebe byte-identisch
                                  und zaehlte im Entdeckungs-grep als unmarkiert — FEHLALARM-Richtung
                                  (grep > 0 ohne echte Umgehung), kein stilles Loch. A-11-3 verlangt
                                  byte-identisch; Glaetten waere unbestellt. Dokumentierte Unschaerfe.
3 zentrale Suite-Umgebung         GEDECKT. Eine Zeile process.env.TICKET_ROLLE='probe' im Testkopf,
                                  am Diff verifiziert; kein Bestandstest angefasst (comm-Beleg oben).
4 §3-Wartezeit auf W-02/1         GEDECKT. ffd06c1a (21:13, IN_ARBEIT) liegt nach 58342f47
                                  (W-02/1 CODE_FERTIG); Kette vom Plan-Pruefer bestaetigt.
5 Beifang-Offenlegung 7f592b20    GEGENGELESEN. 7f592b20 (release-pruefer, 21:03) traegt die
                                  A-11-Tafelzeile ENTWURF->IN_ARBEIT, ffd06c1a (21:13) setzte den
                                  Datensatz erst danach — die Zeile lief dem Zustand voraus, genau
                                  wie deklariert. Kein Schaden im Pruefgegenstand.
```

**Gesamturteil: ABGENOMMEN.** Alle acht Kriterien selbst nachgemessen, jede Gegenprobe ueberstanden,
keine offene P0/P1-Abweichung, Bestand 50/50 unversehrt im 61/61-Lauf. Ball beim Release-Pruefer (§10).

---

## Release-Pruefung A-11 (§10) — 10.08.2026, frische Instanz

```yaml
auftrag: A-11
abnahme_commit: efe38d1d      # Evaluator-Votum ABGENOMMEN, Fehlerklasse KEINE
release_commit: 28760966      # Pruef-SHA; Bau b0f4c444, Tor am HEAD byte-identisch (md5 e5fece559500d5c90869cf6c2ada40da)
votum: RELEASE_FREI
ci: pass                      # Suite selbst am HEAD: 61/61 · bash -n exit 0 · node --check exit 0
artefakte_reproduzierbar: true # kein Build-Artefakt; das Artefakt IST das Skript, HEAD = Bau (diff 0)
migration: nicht_anwendbar    # reine Skript-/Testaenderung, kein Datenpfad, kein Schema
rueckweg: pass                # git show b0f4c444 | git apply --check -R exit 0; siehe Wuerdigung unten
smoke_test_plan: "Realtest am lebenden Tor: die beiden Abschluss-Commits dieser Pruefung gehen OHNE Praefix mit TICKET_ROLLE=release-pruefer ins Tor und muessen markiert herauskommen; Dauerkontrolle git log --format='%s' b0f4c444.. | grep -cvE '^[a-z][a-z-]*(-[0-9]+)?: ' muss 0 bleiben"
befunde: []
```

### Alles selbst gemessen, Rohausgaben

**Kette** — sechsmal `git merge-base --is-ancestor`, je exit 0 (HEAD bei Beginn `5823ada0`):

```text
1dee4771 (BEREIT)      -> ffd06c1a (IN_ARBEIT)   exit=0
ffd06c1a (IN_ARBEIT)   -> b0f4c444 (Bau)         exit=0
b0f4c444 (Bau)         -> 28760966 (Pruef-SHA)   exit=0
28760966 (Pruef-SHA)   -> 63c83a53 (CODE_FERTIG) exit=0
63c83a53 (CODE_FERTIG) -> efe38d1d (ABGENOMMEN)  exit=0
efe38d1d (ABGENOMMEN)  -> HEAD 5823ada0          exit=0
```

**Qualitaetstore am HEAD, selbst gefahren:**

```text
node --test scripts/__tests__/commitPruefen.test.mjs   tests 61 / pass 61 / fail 0
bash -n scripts/commit-pruefen.sh                      exit 0
node --check scripts/__tests__/commitPruefen.test.mjs  exit 0
```

**Release-Diff nur Freigegebenes** — Drift seit Bau und Scope:

```text
git log --oneline b0f4c444..HEAD -- scripts/           0 Commits (11 Commits seit Bau, keiner an scripts/)
git diff b0f4c444 HEAD -- scripts/commit-pruefen.sh    0 Zeilen
md5 scripts/commit-pruefen.sh                          e5fece559500d5c90869cf6c2ada40da (= Bau-Stand)
git show b0f4c444 --stat                               exakt 2 Dateien, 165 insertions(+), 0 deletions
git show b0f4c444 -- scripts/commit-pruefen.sh         EIN Hunk: @@ -49,6 +49,41 @@ — direkt nach
                                                       `BOTSCHAFT="$1"; shift` (Z.51), also an der
                                                       Botschaft-Annahme; Commit-Aufruf und
                                                       A-07/A-08/A-09-Zonen unangetastet
```

**Rueckweg, geprueft und gewuerdigt:**

```text
git show b0f4c444 | git apply --check -R               exit 0 — Commit sauber zurueckdrehbar
```

Die Blatt-Aussage haelt und verdient die Wuerdigung: der Rueckweg ist hier sogar **ohne Revert eine
Zuweisung** — `TICKET_ROLLE` setzen genuegt, um die Sperre im Notfall zu ueberbruecken. Der Rueckweg
ist damit doppelt vorhanden (Zuweisung sofort, Revert vollstaendig), kein Datenpfad, keine Migration.

**Entdeckungs-grep als Dauerkontrolle:**

```text
git log --format='%s' b0f4c444.. | grep -cvE '^[a-z][a-z-]*(-[0-9]+)?: '   0   (11 Commits, alle markiert)
```

**Mitteilungs-Pflicht (Auflage aus dem BEREIT-Votum):** Die Mitteilung an alle Rollen steht als
eigener Abschnitt direkt unter der Tafel (`docs/STATUS.md` Z.24 ff.) und nennt **Variable**
(`TICKET_ROLLE`), **Form** (`^[a-z][a-z-]*(-[0-9]+)?$`) und **Beispiel**
(`TICKET_ROLLE=evaluator bash scripts/commit-pruefen.sh "Botschaft" pfad`) — per grep bestaetigt.

**Realtest:** Die ehrliche Grenze des Evaluators gilt fort — rueckwirkend ist die Marke an
`b0f4c444` nicht beweisbar. Der lebende Beweis setzt sich fort: die Abschluss-Commits DIESER
Pruefung (dieses Blatt und der STATUS-Vermerk) gehen ohne Praefix mit `TICKET_ROLLE=release-pruefer`
ins Tor; die Marke `release-pruefer: ` am Betreff stammt vom Tor. Ergebnis im STATUS-Vermerk.

### Die zwei Evaluator-Randnotizen — ins Protokoll uebernommen (kein P0/P1)

1. **Form-echte Nicht-Rollen-Praefixe** (`docs: `, `fix: `, `test: ` — Zensus der Historie: 129x/1x/2x)
   fallen kuenftig bei anderer `TICKET_ROLLE` als **WIDERSPRUCH**. Richtung Blockade, sichtbar, durch
   Umformulieren behebbar — kein stilles Loch. **Offene Planner-Entscheidung:** ob solche Praefixe
   zulaessige Marken werden sollen oder verboten bleiben.
2. **Trimm-Unschaerfe des Entdeckungs-greps:** eine fuehrend-eingerueckte, korrekt markierte Zeile
   (`  evaluator: …`) wird byte-identisch verbucht und zaehlt im grep als unmarkiert —
   **Fehlalarm-Richtung** (grep > 0 ohne echte Umgehung), kein stilles Loch. Dokumentierte
   Unschaerfe der Dauerkontrolle, kein Handlungsbedarf vor Release.

**Urteil: RELEASE_FREI an `28760966`.** Kette geschlossen, Tore am HEAD selbst gruen gefahren,
Release-Diff enthaelt ausschliesslich den freigegebenen Bau, Rueckweg doppelt belegt, keine offenen
P0/P1-Befunde. Nach v1.2-Vertretung folgt der Sicherungs-Push (`git push fork
auto/hausplaner-integration`); das Ergebnis wird im STATUS-Datensatz verbucht. **Ball bei Yama:
main-Veroeffentlichung.**
