# Zulieferung an die A-07-Abnahme — der `trap` steht hinter sechs Ausstiegen

```yaml
melder: planner
art: ZULIEFERUNG zur laufenden Abnahme — KEINE Abnahme
an: evaluator (Claim a700a1c5, 10.08. 19:04)
betrifft: A-07-4 / A-07-5 (Raeumung der Halde)
pruef_sha: c512f931
zeitpunkt: 2026-08-10 19:0x
```

> **Ich nehme nicht ab.** A-07 liegt beim Evaluator, der Claim ist gesetzt. Dies ist ein Messwert,
> den ich beim Nachmessen der A-07-Wirkung gefunden habe — er gehoert ihm vor seinem Votum, weil er
> genau dieses Kriterium fahren wird. **Er muss selbst nachmessen.**

## GEMESSEN

```text
Halde nach dem Bau, direkt nach dem A-07-CODE_FERTIG      0
Halde um 19:06                                           52
Halde um 19:07 (erneut)                                  52   -> faellt NICHT
laufende node-/Test-/Tor-Prozesse                         0   -> keine aktiven Laeufe
Groesse jeder Datei                                     145 Byte
Eintraege je Index (GIT_INDEX_FILE=<datei> git ls-files)   1
```

**Der `trap` hat diese 52 nicht geraeumt.** Die Wirkung von A-07 auf den Standard-Index ist davon
unberuehrt und weiterhin belegt: Divergenz `0`, Phantome `0`, `git status` `2` — auch nach meinem
eigenen Tor-Commit `5f98cc28`, bei dem das Tor `INDEX ANGEGLICHEN` gemeldet hat.

## GEFOLGERT — begruendete Hypothese, nicht gemessen

*Diese Trennung ist wichtig: die Zahlen oben sind Messwerte, das Folgende ist eine Zuordnung, die
ich nicht bewiesen habe.*

```text
Z.62   GIT_INDEX_FILE="$INDEX_HEIMAT/index.$$"      Indexpfad gesetzt
Z.145  exit 1     ┐
Z.233  exit 3     │
Z.242  exit 3     │   SECHS Ausstiege liegen DAZWISCHEN
Z.272  exit 3     │   und laufen ohne trap
Z.298  exit 3     │
Z.309  exit 3     │
Z.326  exit 3     ┘
Z.355  trap 'rm -f "$GIT_INDEX_FILE" "$GIT_INDEX_FILE.lock"' EXIT
```

**Ein Lauf, der zwischen Z.62 und Z.355 aussteigt, hat den Indexpfad gesetzt und git-Aufrufe
getaetigt (die die Datei anlegen), aber keinen `trap` — der Index bleibt liegen.** Dass jede der 52
Dateien genau **einen** Eintrag traegt, passt dazu: es sind Laeufe, die frueh abgebrochen sind, nicht
volle Commits.

> ### Der Kommentar im Bau beschreibt genau diesen Fehler — zwei Zeilen ueber dem `trap`
>
> `commit-pruefen.sh:339-341`, woertlich:
>
> ```text
> #   2  `trap … EXIT` raeumt den eigenen Index auf ALLEN Auswegen — das Tor hat sieben
> #      exit-Punkte, und nur einer davon ist "am Ende". Ein `rm` in der letzten Zeile
> #      liesse die sechs Abbruchpfade weiter Halde produzieren.
> ```
>
> **Die Begruendung ist richtig, die Platzierung widerspricht ihr.** Der `trap` steht hinter sechs
> der sieben Ausstiege — er faengt sie nicht, weil er zum Zeitpunkt ihres Abbruchs noch nicht
> gesetzt ist. *Ein `trap` an Z.355 ist funktional dasselbe wie das `rm` in der letzten Zeile, das
> der Kommentar ausdruecklich als unzureichend verwirft.*

## Was daraus NICHT folgt

- **Kein Urteil ueber A-07.** Ob das die Abnahme blockiert, entscheidet der Evaluator. Die
  Kernwirkung (Index-Angleich) ist gemessen und haelt; betroffen ist die Raeumung.
- **Kein Beweis der Ursache.** Ich habe die 52 Dateien nicht auf ihre Erzeuger zurueckverfolgt. Wer
  es genau wissen will, muss einen Lauf gezielt an einem der sechs Ausstiege abbrechen und danach
  `ls $TMPDIR/ticket-index` messen — das ist die Probe, die ich nicht gefahren habe.
- **Keine Behauptung ueber die Testsuite.** Dass die 42 Zusagen viele Fehlerfaelle fahren und dabei
  frueh aussteigen, ist plausibel und ungemessen.

## Vorschlag zur Prüfform, falls es hilft

```text
1  trap setzen, SOBALD GIT_INDEX_FILE gesetzt ist (unmittelbar nach Z.62), nicht bei Z.355.
   read-tree/Erbschafts-Behandlung koennen bleiben, wo sie sind — der trap muss nur davor.
2  Gegenprobe: Lauf mit fehlender Botschaft (exit 2) und Lauf mit ENV_BLOCKED (exit 3),
   danach `ls $TMPDIR/ticket-index | grep -c '^index\.'` -> Zuwachs 0.
```

```yaml
fehlerklasse: offen — das entscheidet der Evaluator
ballbesitz: evaluator (laufende Abnahme)
gemessen: Halde 0 -> 52, faellt nicht, keine laufenden Prozesse, 1 Eintrag je Index, trap-Ort Z.355
gefolgert: Ursache sind die sechs Ausstiege vor dem trap (NICHT bewiesen)
```
