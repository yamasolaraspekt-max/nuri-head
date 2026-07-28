# `.ai-workflow/` — Prüfbefund vor der ersten Benutzung

*Planner, 27.07.2026, 21:0x. Yama hat den Auftrag „getrennte Entwicklungsumgebung einrichten“
gegeben. **Die Struktur existiert bereits** — 15 Dateien, angelegt 18:38 von einer anderen Instanz.
Ich habe deshalb nicht gebaut, sondern gemessen. Der Auftrag verlangt das ausdrücklich:
„Überschreibe keine vorhandenen Workflow-Dateien ohne vorherige Prüfung.“*

## 1. Was da ist, und wie es gebaut ist

Alle 15 geforderten Dateien liegen vor (README · 4 Rollen · 4 Templates · 5 Skripte · tasks/.gitkeep).

**Handwerklich sauber, gemessen:**

- `bash -n` auf allen fünf Skripten: **ok**
- alle mit `#!/usr/bin/env bash` und `set -euo pipefail`
- alle ausführbar
- **keine** absoluten Benutzerpfade (`/Users/`, `/home/`) — null Treffer
- **keine** destruktiven Befehle: kein `rm -rf`, kein `git reset --hard`, kein `branch -D`,
  kein `push --force`
- `git worktree prune` steht drin, aber **`--dry-run` ist Vorgabe**, `--prune` ist Opt-in
- das einzige `rm -r` (`validate-task-handover.sh:73`) räumt ein `mktemp -d` auf — unter `set -e`
  kann die Variable nicht leer sein. **Kein Befund.**
- der Validator akzeptiert `02-planner-spec.md` **und** `planner-spec.md` — die
  Nummerierungsfrage aus dem Auftrag ist bereits abgefangen

**Testläufe (gefahrlos, nichts angelegt):**

| Aufruf | Ergebnis |
|---|---|
| `create-task-worktrees.sh --dry-run TASK-999` | 4 Worktrees geplant, `DRY-RUN: Keine Worktrees … angelegt`, exit 0 |
| `create-task-worktrees.sh --dry-run "bad id~1"` | `ERROR: Ungültige Task-ID`, **exit 64** |
| `validate-task-handover.sh TASK-999 planner` | `ERROR: Pflichtdatei fehlt: …/02-planner-spec.md`, **exit 1** |
| `check-worktree-role.sh` | `ERROR: Rollenmarker fehlt`, **exit 3** |

Alle zehn geforderten Prüfungen in `create-task-worktrees.sh` sind vorhanden: Repo-Prüfung,
Task-ID-Validierung (`git check-ref-format`), sauberer Baum (exit 3), Basiserkennung, Erkennung
bestehender Branches **und** ob ein Branch schon in einem anderen Worktree hängt, kein
Überschreiben (exit 4), vier Worktrees, Branch-Anlage, Exit-Codes, VS-Code-Zeilen.

## 2. Drei Befunde vor der ersten echten Benutzung

### B-01 (P1) — `.ai-workflow/` ist nicht committed, und **die Worktrees hätten es nicht drin**

`git check-ignore` sagt: **nicht ignoriert, nur nicht hinzugefügt.** Der Ordner steht als `??` im
Status, kein Eintrag in `.gitignore` oder `info/exclude`.

**Die Folge ist die eigentliche Falle:** `git worktree add` checkt einen Branch aus. **Unverfolgte
Dateien wandern nicht mit.** In `ticket-planner-TASK-123` gäbe es also **kein `.ai-workflow/`** —
das Werkzeug, das die Trennung herstellt, wäre in den getrennten Verzeichnissen nicht vorhanden.

Zweitens: nicht committet heißt **nicht auf `fork`/`backup-private` gesichert**. Vor dem Deploy ist
der gepushte Stand die einzige Kopie außerhalb der Maschine. 76 KB Arbeit liegen ungesichert.

**→ Muss committet und gepusht werden, bevor der erste Worktree entsteht.** Nicht von mir: Cowork
schreibt nur `docs/`.

### B-02 (P1) — Die Basis wäre `main`, und `main` ist 37 Commits zurück

Gemessen: `origin/HEAD` → `origin/main`, `refs/heads/main` existiert ⇒ `detect_base` liefert
**`main` (`665dd70e`, Stand 26.07.)**. Die Arbeit lebt auf `auto/hausplaner-integration`
(`5aa042f2`), **Abstand 37 Commits**.

Der Dry-Run bestätigt es wörtlich: `Basis: main (665dd70e…)`.

**Das ist kein Skriptfehler** — das Skript tut genau, was beauftragt war. Es ist eine Kollision mit
unserem Zyklus: bei uns ist `main` **absichtlich** zurück, weil Tor 2 Yama gehört. Jeder Worktree
würde auf dem Stand von gestern starten und die Arbeit von 37 Commits nicht sehen.

**→ Zwei Wege:** entweder eine `--base`-Option ergänzen, oder im README festschreiben, dass die
Basis bei diesem Repo `auto/hausplaner-integration` heißt. **Die Option ist besser** — eine Regel im
README ist wieder nur Prosa, und dieselbe Lehre steht seit heute im Auftragsschema.

### B-03 (P2) — Die Worktree-Skripte gehören ins Mac-Terminal, nie über die Brücke

`git worktree list` zeigt von hier aus **sechs bestehende Worktrees als `prunable`** — weil ihre
Pfade `/Users/yamanuri/…` in der Linux-VM der Brücke nicht existieren. **Auf dem Mac sind sie
vermutlich intakt.**

`remove-task-worktrees.sh --prune`, **von der Brücke aus ausgeführt**, würde sechs echte Worktrees
deregistrieren. Das Skript ist nicht schuld; die Umgebung täuscht es.

**→ Regel: alle Worktree-Skripte laufen im Terminal auf dem Mac.** Von hier aus nur `--dry-run`.

## 3. Was ich nicht geprüft habe

Ob die Skripte **fachlich das Richtige** prüfen — ob also der Validator die Lücken findet, gegen die
er gebaut ist. **Das ist eine Abnahme und gehört dem Evaluator**, nicht mir: ich habe die
Anforderungen an dieses Werkzeug mitformuliert und wäre für deren Lücken betriebsblind.

**Der Vorschlag dafür steht schon im Auftragsschema (§7.4): die Rückwärts-Probe.** Das alte
Auftragsblatt von AUF-38 Scheibe 3 durch den Validator schicken. Findet er die Lücke nicht, die uns
das `NACHBESSERN` eingebracht hat, taugt er nichts — und das merken wir ohne Kosten.
