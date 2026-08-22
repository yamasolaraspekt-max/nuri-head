# VOTUM — Wirksamkeitsprobe A-37-25 in den sieben realen Bäumen

**evaluator · 22.08.2026 · Auftrag `PROBE-evaluator-A-37-25-wirksamkeit` gen 8 · Lease-Token 1**
**Basis `0a18446a` → Hinweg auf `96643116` · Integrationsstand `f6792ec3`**

## Ergebnis: WIRKSAM 7 VON 7

Yamas Frage lautete: *„bestätige mir, dass der nackte `git commit` scheitert"* — **in jedem realen
Baum.** Er scheitert. Selbst ausgelöst, je Baum ein Lauf, HEAD vor und nach jedem Versuch gemessen.

| Baum | `pre-commit` | `core.hooksPath` | nackter `git commit --allow-empty` | HEAD |
|---|---|---|---|---|
| `ticket` | ausführbar, 3692 B | `.githooks` | **exit 1** | unverändert |
| `ticket-rolle-generator` | ausführbar, 3692 B | `.githooks` | **exit 1** | unverändert |
| `ticket-rolle-planner` | ausführbar, 3692 B | `.githooks` | **exit 1** | unverändert |
| `ticket-rolle-plan-pruefer` | ausführbar, 3692 B | `.githooks` | **exit 1** | unverändert |
| `ticket-rolle-evaluator` | ausführbar, 3692 B | `.githooks` | **exit 1** | unverändert |
| `ticket-release-pruefung` | ausführbar, 3692 B | `.githooks` | **exit 1** | unverändert |
| `ticket-rolle-dirigent` | ausführbar, 3692 B | `.githooks` | **exit 1** | unverändert |

Alle sieben Abweisungen tragen dieselbe Ursache im Klartext:

```
ROLLEN-TOR  TICKET_ROLLE ist nicht gesetzt — ohne Rolle ist keine Zuordnung pruefbar.
KEIN COMMIT. Ohne Rollenmarke ist keine Zuordnung pruefbar (Rueckgabe 5).
            TICKET_ROLLE=<rolle> setzen — oder ueber scripts/commit-pruefen.sh gehen.
```

**Positivfall (c):** dieser Commit selbst. Er entsteht mit `TICKET_ROLLE=evaluator`, ACK gen 8 und
`aktion: bauen` über `commit-pruefen.sh` — und er entsteht in einem Baum, der seit dem Hinweg
`96643116` denselben Haken trägt wie die sechs anderen.

## Der Zwischenstand war 6 von 7 — und warum das kein Widerspruch ist

Um 12:26 fehlte im Planner-Baum `.githooks/pre-commit`: der Hinweg war dort noch nicht gefahren,
`core.hooksPath` zeigte auf ein leeres Verzeichnis. **Ein Haken, den es im Baum nicht gibt, kann
nicht greifen** — mein nackter Probe-Commit lief mit exit 0 durch und erzeugte einen Commit auf
`rolle/planner`.

**Sofort zurückgesetzt** (`git reset --soft HEAD~1`, im selben Lauf) und nachgemessen statt
behauptet: HEAD wieder `352900f3`, `git diff --cached` 0 Dateien, der Commit in keinem Zweig mehr
referenziert. Kein Rückstand von mir — er war `--allow-empty`, und weder ein leerer Commit noch
`reset --soft` berührt den Arbeitsbaum. Dass die eine unstaged Datei dort dem Planner gehört, hat
er selbst belegt: sein `BASE_BLOCKED` (12:30:38) scheiterte genau an ihr.

Nach Auftrag ist das **AUSSTEHEND, nicht MANGEL** — die Ursache war der fehlende Hinweg, nicht ein
unwirksamer Haken. Um 12:55, nach seinem Hinweg auf `7333a341`, habe ich dieselbe Probe wiederholt:
**exit 1, HEAD unverändert.** Damit 7/7.

**Die Probe im zweiten Takt habe ich bewusst nicht wiederholt**, solange der Hook fehlte: das
Ergebnis war deterministisch und bereits belegt, ein zweiter Versuch hätte nur eine weitere
Nebenwirkung in einem fremden Baum erzeugt.

## Was der Fall zusätzlich zeigt — belegt, nicht vermutet

**Zwischen Abnahme und Transport steht jeder Baum ungeschützt.** A-37-25 war um 11:2x abgenommen
und um 12:26 im Planner-Baum wirkungslos, weil der Bau dort nicht lag. Die Barriere ist so alt wie
ihr Transport, nicht wie ihre Abnahme. Das ist **kein Mangel des Baus** — er kann nur wirken, wo er
liegt — aber es ist die Antwort auf die Frage, ab wann man sich auf ihn verlassen darf.

## Grenze, zitiert und nicht geprüft

`git commit --no-verify` und ein geänderter `core.hooksPath` übergehen den Haken. Das steht so im
Bau selbst und in meinem Votum `db420cf0`; der Auftrag nimmt es ausdrücklich aus. Ich zitiere es,
ich habe es nicht gemessen — und ich melde es nicht als gelöst.

## Ball

**Dirigent** — 7 von 7 wirksam. Yamas Frage ist beantwortet.

## Nachtrag: das Tor hat mich beim Schreiben dieses Votums selbst gesperrt

Mein erster Commit-Versuch für dieses Votum wurde mit **Rückgabe 6 — `MODULSTAND`** abgewiesen:

```
ROLLEN-TOR  MODULSTAND  die Module in ticket-rolle-evaluator gehoeren nicht zu diesem package-lock.json.
            Lockfile jetzt: f50008e1ae546d345df26d9387140bb5f0ad34b3
            Marke sagt:     d17b19a290d5961b5a658b9d2854f78612517638
            geschrieben:    2026-08-16T14:39:39+02:00
            Abhilfe: npm ci in diesem Baum — bash scripts/module-nachziehen.sh
```

**Der Fall ist echt und nicht gestellt:** meine Marke stammte vom 16.08., das Lockfile hat sich
seither durch A-37-27 (`js-yaml` als Wurzel-Abhängigkeit) geändert, und mein Hinweg auf `96643116`
hat mir den neuen Stand in den Baum gelegt — ohne die Module nachzuziehen. Genau die Lage, gegen
die A-37-12/13 geschrieben wurde: *„Ein Lauf auf fremden Modulen ist grün oder rot aus Gründen, die
nicht im Code stehen."*

**Ich habe die im Tor genannte Abhilfe gefahren, nicht das Tor umgangen:**
`bash scripts/module-nachziehen.sh` → Rückgabe 0, Marke neu geschrieben
(`hash f50008e1… zeit 2026-08-22T10:59:11Z`), Tor danach **0**. Kein `--no-verify`, kein
Umbiegen von `core.hooksPath`, keine Änderung am Bau.

Das ist ein Wirksamkeitsbeleg, den ich nicht geplant hatte: **A-37-13 hat einen realen
Modulstand-Konflikt gefangen, bevor er zu einem grünen Lauf aus fremden Gründen führen konnte** —
und zwar an mir, dem Prüfer.
