# ticket-steuerung — zentrale Auftragssteuerung als PULL-System (Yama 21.08.2026, Sofortlösung)

**Warum:** „gesendet" ist nachweislich nicht „zugestellt" (0 cross-session-message in allen Rollen-Transkripten).
Deshalb **holt jede Rolle ihren Auftrag selbst** — Chat-Nachrichten sind keine Auftragsquelle.
**Nur der Dirigent schreibt hier** (`rollen/`, `auftraege/`). Rollen schreiben nur `ereignisse/`, `sitzungen/`, `leases/`.
Diese Stelle ist **Betriebszustand**, keine zweite Statuswahrheit — `docs/STATUS.md` bleibt der Statusträger.
**Autorität:** `/Users/yamanuri/.ticket-steuerung/` ist die operative Wahrheit. Der Spiegel im Repo
(`docs/auftraege/steuerung-spiegel/**` auf `rolle/dirigent`) ist **nur ein belegter Snapshot** mit Generation und
Digest — nie Autorität, nie Quelle eines Auftrags; er wird als eigener, pfadbegrenzter Commit je vollständiger
Momentaufnahme abgelegt, nie mit anderen Ereignissen vermischt.
**Bisher gilt:** solange kein Commit-Hook Auftrag, Generation, Digest, Worktree, Branch und Lease technisch prüft,
ist dies eine Sofortlösung, keine unübergehbare Barriere — die Durchsetzung gehört in A-37/Z0-I3.

## Für jede Rolle — vor JEDER Werkzeugrunde und vor JEDEM Schreibzugriff
1. `cat /Users/yamanuri/.ticket-steuerung/rollen/<rolle>.yaml` und `cat rollen/<rolle>.yaml.sha256` (Digest).
2. Einmalig **registrieren** (atomar: erst `.tmp` schreiben, dann `mv`):
   `sitzungen/<rolle>.yaml` mit `sitzungs_id`, `pid`, `rolle`, `worktree`, `branch`, `zeit`.
3. **ACK** — die einzige gültige Bestätigung, **kein Git-Commit**: Datei
   `ereignisse/<auftrag_id>/<rolle>-ack.yaml` atomar anlegen mit
   `auftrag_id, generation, digest (aus rollen/<rolle>.yaml.sha256), rolle, sitzungs_id, pid, worktree, branch, head_sha, zeit, antwort: GELESEN`.
   Ein Auftrag gilt **erst** als zugestellt, wenn diese Datei mit passender `generation` und passendem `digest` existiert.
4. **Claim (Lease)** nur, wenn `aktion` Arbeit verlangt (nicht bei `parken`/`warten`): Verzeichnis `leases/<auftrag_id>/`
   nach V2 §8 — `counter` (dauerhaft), `counter.lock/` (mkdir-Sperre für den ganzen Vergabevorgang), `active/lease.yaml`
   (mkdir-atomar über tmp + `mv`), mit `fencing_token` aus `counter`, `heartbeat_bis`, `owner` (sitzungs_id, pid, rolle).
   Zwei Sitzungen derselben Rolle können denselben Auftrag nicht gleichzeitig halten. Existiert `active/` gültig → nicht arbeiten.
5. **Steuerungshandlungen vor Sacharbeit (Yama 21.08.):** Registrierung, Lease-Anforderung und ACK sind
   **erlaubte Steuerungshandlungen und keine Sacharbeit** — sie sind ohne Lease erlaubt und auch dann, wenn der
   Auftrag `parken`/`warten` lautet. **Sacharbeit** (Dateien im Worktree ändern, Tests/DB-Läufe, Commits) nur im
   genannten `worktree`/`branch`, nur in `erlaubte_pfade`, nie in `verboten`. Ohne gültigen Auftrag, ACK, passenden
   Worktree, Branch und **aktive Lease**: keine Sacharbeit, kein Commit. `TICKET_ROLLE` muss bei jedem Prüf- und
   Commitbefehl tatsächlich in der Prozessumgebung gesetzt sein.
   **Zustellnachweis ist ausschließlich die gültige ACK-Datei** (Rolle, Sitzungs-ID, Generation, Digest) — ein
   Dateizugriff oder eine Zugriffszeit ist kein Nachweis.
6. Jede Meldung in der Berichtsform: Ausgangs-SHA · Ergebnis-SHA · geänderte Pfade · Votum · Browser · Abweichung · nächster Ball.
7. Zustände: `ZUGETEILT → GELESEN → GECLAIMT → IN_ARBEIT → CODE_FERTIG → ABGENOMMEN` — jeder Übergang mit SHA, Zeit, Rolle, Beleg
   (Übergänge schreibt die Rolle als `ereignisse/<auftrag_id>/<rolle>-<zustand>.yaml`; der Dirigent spiegelt nach STATUS über den Integrator).

## Für den Dirigenten
- Änderung eines Auftrags = `generation` +1, Datei neu schreiben, Digest neu berechnen (`shasum -a 256`), alte ACKs verfallen.
- Eine Rolle hat **höchstens einen** aktiven Auftrag. Kein „nebenbei".
- Eine schlafende Sitzung gilt **nicht** als informiert — bis ein Orchestrator existiert, stößt Yama sie einmal manuell an.
- Technische Commit-Barriere (Rolle, Sitzung, Worktree, Branch, aktiver Auftrag, erlaubte Pfade, Voraussetzungen; auch nackter `git commit` und Merge)
  ist **Bauauftrag** (A-37-Erweiterung / Z0-I2 / Z0-I3), spezifiziert vom Planner — siehe `auftraege/Z0-I3-pull-steuerung.vorgabe-yama.md`.

## Aktueller Stopp
Generator-Sitzung `7df19ed4` (PID 87659, committete direkt im gemeinsamen Checkout) ist per `SIGSTOP` angehalten. Stopp-SHA `4e02c273`.
Kein `CONT`, bis A-42 abgeschlossen, A-37 mit `pre-commit` fertig, fünf Negativproben bestanden, nacktes `git commit` gesperrt, Evaluator-Abnahme vorliegt.
Reihenfolge: **A-42 → A-37 → Z0-I1 → übrige Abnahmen.** A-38, A-39, Z1-W1-5-1 und alle Produktarbeit geparkt.
