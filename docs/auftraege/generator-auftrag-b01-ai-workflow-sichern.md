# B-01 — `.ai-workflow/` versionieren und den Rückstand sichern

*Planner, 29.07.2026, 00:45 CEST. Zweimal gemeldet, nie beauftragt — das ändert sich hier.*

```yaml
auftrag:
  id: B-01
  status: aktiv
  spur: B
  heimat: ticket
  ziel: "Das Werkzeug .ai-workflow/ ist versioniert, und der lokale Rueckstand liegt auf Yamas eigenen Remotes."
  nicht_ziel: >
    Keine inhaltliche Aenderung an den Skripten. Kein Bau. Kein public/*.
    Kein Push auf upstream. Kein --force. Kein Merge nach main.

scope:
  population_command: "git status --porcelain .ai-workflow | wc -l"
  population_at_writing: "15 Dateien, 76 KB, alle unverfolgt — Messung des Planners, KEINE Bedingung"
  pfade:
    - .ai-workflow/
  ausschluesse:
    - stelle: ".rm_probe_tmp"
      grund: "Fremde Sonde aus einem Loeschversuch am 27.07., 0 Byte, gehoert nicht zu diesem Auftrag."
      entschieden_von: planner
    - stelle: "detect_base liefert main statt der Integrationsbasis (Befund B-02)"
      grund: "Eigener Posten. Hier wird nur versioniert, nicht geaendert — sonst waere es Spur A."
      entschieden_von: planner

kriterien:
  - id: K-01
    aussage: "Alle 15 Dateien aus .ai-workflow/ sind im Commit, und nichts sonst."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD | sort"
      erwartet: "genau die Dateien unter .ai-workflow/, kein weiterer Pfad"
    beleg: dateiliste

  - id: K-02
    aussage: "Kein absoluter Benutzerpfad ist mitversioniert."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git grep -n '/Users/' -- .ai-workflow"
      erwartet: "leer (exit 1)"
    beleg: grepausgabe
    partner: >
      presence-Partner nach R2: derselbe Befehl ohne Pfadfilter muss Treffer liefern
      (sonst prueft der Befehl nichts). Beide Ausgaben in den Bericht.

  - id: K-03
    aussage: "Alle fuenf Skripte sind syntaktisch gueltig und ausfuehrbar."
    typ: presence
    kritikalitaet: P2
    pruefung:
      befehl: "for f in .ai-workflow/scripts/*.sh; do bash -n \"$f\" || echo \"FEHLER $f\"; done; ls -l .ai-workflow/scripts/"
      erwartet: "keine FEHLER-Zeile; Ausfuehrbit gesetzt und im Index (git ls-files -s zeigt 100755)"
    beleg: rohausgabe

  - id: K-04
    aussage: "Kein Produktionscode und kein Buendel ist beruehrt."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD -- resources scripts public app database"
      erwartet: "leer"
    beleg: rohausgabe

  - id: K-05
    aussage: "Der lokale Stand liegt auf fork UND backup-private."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "./push-integration-sicher.command && cat push-result.log"
      erwartet: >
        Fuer auto/hausplaner-integration steht bei BEIDEN Remotes 'OK'.
        Danach ist 'git branch -r --contains HEAD' NICHT leer.
    beleg: push-result.log + branch-r-ausgabe
    grenze: >
      Das Skript ist Yamas eigenes und der einzige zugelassene Weg. NIE upstream
      (raminsadid2021 = fremdes Konto), NIE --force. Schlaegt es fehl, wird der
      Fehler gemeldet und NICHTS von Hand nachgeholt.

selbstnachweis:
  gegenprobe: >
    K-02 ohne Pfadfilter fahren und zeigen, dass der Befehl ueberhaupt Treffer
    finden KANN. Eine absence-Zusage ohne presence-Partner ist nach R2 kein Beleg.
```

---

## Warum dieser Auftrag jetzt kommt

**Er berührt das Bündel nicht.** Der Evaluator hat um 00:2x gemeldet, dass er die aufgeschobene
headful-K7 an einem stillstehenden Baum fahren will; `public/*` steht unbewegt auf `a2a83e72`.
Dieser Auftrag fasst `public/` nicht an — er kann parallel zur Sichtprobe laufen.

**Präzisierung der Sichtproben-Regel, gilt ab sofort.** Am 29.07. um 00:06 habe ich geschrieben:
*„während einer laufenden Sichtprobe bewegt niemand das Bündel"*, und daraus fälschlich
*„Scheibe 5 wird nicht gezogen"* gemacht. Das ist zu weit. **Die Regel lautet: während einer
gemeldeten Sichtprobe wird `public/*` nicht bewegt.** Bauen, messen, committen an allem anderen
ist erlaubt. Der Engpass ist die Abnahme, nicht das Ziehen — das habe ich am 27.07. um 18:15 selbst
festgestellt und heute wieder gegen mich arbeiten lassen. **Zweiter Beleg derselben Fehlerklasse
bei mir: eine Sperre, die mehr sperrt, als ihr Grund trägt.**

## Der Grund, der schwerer wiegt als das Werkzeug

Gemessen am 29.07., 00:40:

```
git rev-list --count origin/auto/hausplaner-integration..HEAD   ->  223
git branch -r --contains HEAD                                   ->  (leer)
letzter gesicherter Stand: 432c179b, 26.07. 13:20
```

**223 Commits — zwei volle Arbeitstage — liegen auf genau einer Platte.** Vor dem Deploy ist der
Remote die einzige Kopie außerhalb der Maschine; „nicht gepusht" heißt hier nicht unordentlich,
sondern **kein Backup**. Die 76 KB `.ai-workflow/` sind der kleinere Teil des Problems.

Deshalb steht K-05 in diesem Auftrag und nicht in einem eigenen: **der Auftrag, der die kleinste
ungesicherte Sache versioniert, ist der richtige Ort, um alles Ungesicherte mitzunehmen.**

## Was B-01 ausdrücklich **nicht** löst

`detect_base` in `create-task-worktrees.sh` liefert über `origin/HEAD` den Branch `main` — und
`main` steht 46 Commits zurück. Wer heute Worktrees erzeugt, bekommt einen veralteten Baum.
Das ist **B-02** und wird ein eigener Posten mit `--base`-Option: es ändert Verhalten, also
Spur A, also nicht nebenbei.
