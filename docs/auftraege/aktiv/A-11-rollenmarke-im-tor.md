# A-11 — Die Rollenkennung entsteht im Tor, nicht in der Selbstdisziplin

```yaml
auftrag: A-11
titel: "Commit-Tor: die Rolle kommt aus der Umgebung und wird der Botschaft vorangestellt - fehlt sie, gibt es keinen Commit"
spur: A                            # Werkzeug am Commit-Weg
heimat_app: ticket
status: ENTWURF                    # der Plan-Pruefer entscheidet ueber BEREIT
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

**REIHENFOLGE — Planner-Entscheidung, wie schon fuer A-08/A-07:**

```text
1  A-07   P0-Rest, groesster Eingriff (trap + Angleich), Schaden waechst mit JEDEM Tor-Lauf
2  A-11   klein und zonenrein, stoppt einen LAUFENDEN Schaden: jeder unmarkierte Commit
          bleibt dauerhaft ununterscheidbar, das ist nicht nachholbar
3  A-09   P2, seltene Luecke, kleinste Wirkung
```

> **Nachrangig ist A-11 nur gegenueber A-07, nicht gegenueber A-09** — und zwar aus einem Grund, der
> beim Abwaegen leicht untergeht: **A-07s Schaden ist reparierbar, A-11s nicht.** Ein divergenter
> Index laesst sich angleichen, eine Halde raeumen. Ein Commit, der ohne Marke geschrieben wurde,
> ist fuer immer ununterscheidbar — das ist Befund 0. Jeder Tag ohne A-11 vergroessert eine Menge,
> die niemand mehr aufloesen kann.
>
> *Weil A-11 auf Z.46–52 begrenzt ist, waere ein **paralleler** Bau zu A-07 technisch moeglich. Ob
> parallel gebaut wird, entscheidet der Plan-Pruefer — ich lege die Reihenfolge fest, nicht die
> Gleichzeitigkeit.*

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
warteschlange: nach A-07, vor A-09
```
