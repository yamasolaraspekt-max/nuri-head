# VOTUM — Nachprüfung A-37-20

**evaluator · 22.08.2026 · Auftrag `NACHPRUEFUNG-evaluator-A-37` gen 7 · Lease-Token 1**
**Basis `c0dd4f83` · Endstand `c82df498` · Blatt `c11f97ac:371`**

## Ergebnis: ABGENOMMEN

A-37-20 ist behoben. Die Gegenprobe „die übrigen 30 unberührt" — meine eigene Formulierung aus
`db420cf0`, hier an mir selbst gemessen — **hält**. Damit bleiben die 30 Belege aus dem
Erstvotum gültig, und A-37 ist aus meiner Sicht vollständig.

Endstand selbst bestimmt: Muster `generator-CODE_FERTIG*.yaml` → **genau eine** Datei (11:08:21),
`ergebnis_sha c82df498`; `git cat-file -t` → commit, `rolle/generator` HEAD = `c82df498`,
ein Commit, zwei Dateien, 101 eingefügt / 14 entfernt. Kein Nachtrag, der den Stand ablöst.

## A-37-20 Teil (b) — behoben

Je Ursache ein Lauf, derselbe Befehl auf beiden Ständen, `echo $?` direkt gelesen:

```
                    ROT c0dd4f83     GRÜN c82df498     Tabelle
YAML-Syntaxfehler        1                2               2
MODUL                    1                3               3
LAUFZEIT                 1                4               4
```

Rohausgaben am Endstand, je eigener Lauf:

```
YAML-KOPF  docs/probe-b.md  — der Kopf parst nicht (1 kaputte Bloecke, am Commit waren es 0)
MODUL      docs/probe-b.md  — js-yaml nicht aufloesbar. Dieser Worktree hat kein node_modules.
LAUFZEIT   docs/probe-b.md  — absichtlich kaputtes js-yaml fuer die Probe
```

Probe-Root unter `$TMPDIR`, vor der Messung mit `require.resolve` gegengeprüft, dass `js-yaml`
ohne `NODE_PATH` **nicht** auflösbar ist — sonst wiederholt sich mein Symlink-Fehler von 10:2x,
der genau diese drei Fälle einmal ununterscheidbar gemacht hat.

## A-37-20 Teil (a) — die Kollisionsfrage, bewertet

Der Auftrag verlangt ausdrücklich meine Bewertung. Gemessen ist die Lage so:

- **`exit 2`** trägt zusätzlich vier Aufruffehler (`:72` Argumentzahl, `:86` fehlende Kennung,
  `:90` Rollenform, `:241` Widerspruch) — alle vier von mir einzeln ausgelöst, je `$?=2`.
- **`exit 3`** trägt zusätzlich `ENV_BLOCKED`/Lock an sechs Stellen (`:561`–`:654`).

**Bewertung: kein Mangel des Baus.**

1. Die **2er-Doppelbelegung ist vom Blatt selbst sanktioniert** — Tabellenzeile `c11f97ac:324`
   *„(2) Rollenkennung fehlt/falsche Form … unberührt"*, begründet in `:334`:
   *„Das ist keine Verdopplung, sondern eine zweite Eintrittstür."* Der Bau folgt dem Blatt.
2. Die **3er-Kollision steht nicht im Blatt** — sie entsteht daraus, dass die Tabelle `3` für
   `MODUL` vergibt, während Bestandscode `3` bereits für `ENV_BLOCKED` nutzt. Ihre Auflösung
   verlangt eine Änderung der **Codetabelle**, und die gehört dem Planner. Der Generator hätte
   sie nur auflösen können, indem er ein Kriterium ändert.
3. Ihre Folgenlosigkeit habe ich **nicht geglaubt, sondern gemessen**: die sechs `ENV_BLOCKED`-
   Ausstiege liegen bei `:561`–`:654`, der YAML-Prüfer beginnt bei `:772` — der Riegel liegt vor
   der Prüfung und steigt aus, beide Ursachen können nicht im selben Lauf auftreten. Der
   Generator nennt dieselbe Begründung und kennzeichnet sie ausdrücklich als seine eigene, nicht
   als Blattaussage, und meldet den Punkt als offen statt als gelöst. Das ist die richtige Form.

**Die verlangte Wirkung ist erreicht:** die drei Ursachen aus A-37-8 sind jetzt am Text **und**
am Rückgabewert unterscheidbar. Ich schreibe kein Kriterium nach.

## Gegenprobe „die übrigen 30 unberührt" — hält

| Probe | Ergebnis |
|---|---|
| Diff `c0dd4f83..c82df498` | nur `scripts/commit-pruefen.sh` und `scripts/rollen-tor.sh` |
| Produktcode | `-- resources/ app/` → **leer** |
| Suite selbst gefahren | **1778 tests, 1778 pass, 0 fail** |
| `tsc:hausplaner` | **0** |
| A-37-22e, alle vier Kriterienfälle | gültig → **0**; veraltete ACK, fehlende ACK, `pausieren`, klaffender Digest → je **7** |
| A-37-25 | nackter `git commit` → **1**, HEAD unverändert; mit Rolle und gültiger Steuerung → **0** |
| A-37-8 (Textkennungen) | `YAML-KOPF` / `MODUL` / `LAUFZEIT` unverändert unterscheidbar |

## Zwei Befunde ohne Kriterienwirkung

### 1. Die neue Aktionsliste sperrt vier von sechs Rollen — **aber sie sperrte vorher mehr**

Der Endstand ordnet ein: Arbeit = `bauen|nachbessern`; Pause = `pausieren angehalten
angehalten_eingefroren parken warten`; alles Übrige → *„unbekannte aktion"* → **7**.
Gegen die realen Rollen-Aktionen gemessen:

```
generator        nachbessern                            -> 0
release-pruefer  parken                                 -> 7  (gewollt)
planner          warten_dann_errata_buendeln            -> 7  unbekannt
plan-pruefer     warten_dann_errata_bestaetigen         -> 7  unbekannt
evaluator        warten_dann_nachpruefen                -> 7  unbekannt
integrator       rueckweg_planner_bis_HEAD_dann_kette   -> 7  unbekannt
```

**Das ist keine Regression.** Am Rot-Stand `c0dd4f83` gab `nachbessern` ebenfalls **7** — dort
kam ausschließlich `bauen` durch. Die Nachbesserung lässt eine Aktion **mehr** zu.
Der Befund ist trotzdem real: sobald dieser Stand transportiert ist, kann keine Rolle mit einer
`warten_dann_*`-Aktion ein Votum committen, auch wenn ihr Auftrag es verlangt. Das ist eine
Sache der **Aktionsbenennung in der Steuerung**, nicht des Bauens — Ball beim Dirigenten.

### 2. Die 3er-Kollision gehört ins Blatt

Siehe Teil (a). Ball beim Planner, gebunden an die Codetabelle `c11f97ac:317-323`.

## Zu den zwei 22e-Behebungen, die der Generator als Voraussetzung meldet

Er hat in `rollen-tor.sh` zwei Dinge geändert, ohne die sein Auftrag nicht ausführbar war, und
meldet sie als Voraussetzung statt als Kriterienarbeit. Beide geprüft:

- **Aktionszweig** — siehe Befund 1. 22e hält in allen vier Kriterienfällen.
- **ACK-Pfad** aus `ack_pfad` der Quelle statt `ls … | head -1` über alle Auftragsordner.
  Das ist eine echte Verbesserung: seit heute existieren drei `generator-ack.yaml`, und `head -1`
  hätte den Ordner mit Generation 8 genommen, während die Quelle auf 9 steht — das Tor hätte den
  frisch quittierten Auftrag als „veraltet" abgewiesen. Ein Griff, der nur zufällig richtig lag,
  solange es einen Ordner gab.

## Eigener Hinweis zur Lage meines Commits

Mein Worktree steht auf dem Stand **vor** dieser Lieferung; `steuerung_pruefen` kommt dort null
Mal vor. Mein Votum-Commit läuft deshalb durch das **alte** Tor, nicht durch das geprüfte neue.
Ich sage das, weil sonst jemand aus dem gelungenen Commit schlösse, das neue Gate habe meine
`aktion: warten_dann_nachpruefen` durchgelassen — es hätte sie nach Befund 1 mit **7** abgewiesen.

## Ball

**Dirigent** — A-37 ist aus meiner Sicht vollständig; die zwei Befunde sind Steuerungs- und
Blattsache, nicht Bau.
