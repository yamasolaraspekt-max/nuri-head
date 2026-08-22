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
   **`pid` = PID des Sitzungsprozesses** (`claude`, über die ppid-Kette von `$$` aufwärts: Shell → claude → VS Code),
   **nicht** die flüchtige Shell-PID einer Werkzeugrunde (Befund 79285cf2: drei von vier Einträgen trugen eine tote Zahl).
   **Headless-Betrieb (Yama 22.08.):** bei `claude -p --resume` ist jeder Lauf ein neuer Prozess — eine PID ist dort
   **kein dauerhafter Lebensnachweis**. Stabile Identität = **Sitzungs-ID**; je Lauf `pid` + `prozess_start` (gilt nur für
   diesen Lauf); dazu aktuelle `generation` + `digest`; **atomarer Heartbeat** (`heartbeat_bis` in `active/lease.yaml`,
   tmp+mv) während Schreibarbeit; Schreibrecht **ausschließlich unter gültiger Lease**. Transkript-mtime = nur Aktivitätshinweis.
   **Der Pull-Betrieb ist bis zur Umsetzung und negativen Abnahme von A-37 `SOFT-AKTIV — organisatorisch wirksam,
   technisch noch umgehbar`.** Berichte nennen ACKs nie als Summe, nur je Rolle mit Generation.
3. **ACK** — die einzige gültige Bestätigung, **kein Git-Commit**: Datei
   `ereignisse/<auftrag_id>/<rolle>-ack.yaml` atomar anlegen mit
   `auftrag_id, generation, digest (aus rollen/<rolle>.yaml.sha256), rolle, sitzungs_id, pid, worktree, branch, head_sha, zeit, antwort: GELESEN`.
   Ein Auftrag gilt **erst** als zugestellt, wenn diese Datei mit passender `generation` und passendem `digest` existiert.
4. **Claim (Lease)** nur, wenn `aktion` Arbeit verlangt (nicht bei `parken`/`warten`): Verzeichnis `leases/<auftrag_id>/`
   nach V2 §8 — `counter` (dauerhaft), `counter.lock/` (mkdir-Sperre für den ganzen Vergabevorgang), `active/lease.yaml`
   (mkdir-atomar über tmp + `mv`), mit `fencing_token` aus `counter`, `heartbeat_bis`, `owner` (sitzungs_id, pid, rolle).
   Zwei Sitzungen derselben Rolle können denselben Auftrag nicht gleichzeitig halten. Existiert `active/` gültig → nicht arbeiten.
   **Zielregel Lease-Identität (Yama 22.08., wörtlich):** stabile Identität = Sitzungs-ID · pro Lauf aktuelle PID plus
   Startkennung · während Schreibarbeit regelmäßig erneuerter Heartbeat · Übernahme **nur** bei abgelaufenem Heartbeat
   **und** fehlendem aktuellen Lauf (kein Prozess mit `--resume <Sitzungs-ID>`) · Fencing-Token bleibt maßgeblich ·
   **eine alte PID allein darf niemals eine Lease für verwaist erklären.** (Beleg: Planner-Lease nannte PID 88928,
   die Sitzung arbeitete unter 97092 weiter.)
5. **Steuerungshandlungen vor Sacharbeit (Yama 21.08.):** Registrierung, Lease-Anforderung und ACK sind
   **erlaubte Steuerungshandlungen und keine Sacharbeit** — sie sind ohne Lease erlaubt und auch dann, wenn der
   Auftrag `parken`/`warten` lautet. **Sacharbeit** (Dateien im Worktree ändern, Tests/DB-Läufe, Commits) nur im
   genannten `worktree`/`branch`, nur in `erlaubte_pfade`, nie in `verboten`. Ohne gültigen Auftrag, ACK, passenden
   Worktree, Branch und **aktive Lease**: keine Sacharbeit, kein Commit. `TICKET_ROLLE` muss bei jedem Prüf- und
   Commitbefehl tatsächlich in der Prozessumgebung gesetzt sein.
   **Zustellnachweis ist ausschließlich die gültige ACK-Datei** (Rolle, Sitzungs-ID, Generation, Digest) — ein
   Dateizugriff oder eine Zugriffszeit ist kein Nachweis.
6. Jede Meldung in der Berichtsform: Ausgangs-SHA · Ergebnis-SHA · geänderte Pfade · Votum · Browser · Abweichung · nächster Ball.
6b. **Antworten des Dirigenten** stehen unter `ereignisse/<auftrag_id>/dirigent-*.yaml` — bei jedem Pull mitlesen.
    Eine Antwort ändert den Auftrag nicht; ändert sich der Auftrag, steigt `generation` (dann neues ACK).
6c. **Nachziehen** des eigenen Worktrees gegen `auto/hausplaner-integration` nur als **Fast-Forward**
    (`git merge --ff-only`); ein Merge-Commit ist ein Commit und braucht Auftrag + Lease. Nicht-FF → melden.
7. Zustände: `ZUGETEILT → GELESEN → GECLAIMT → IN_ARBEIT → CODE_FERTIG → ABGENOMMEN` — jeder Übergang mit SHA, Zeit, Rolle, Beleg
   (Übergänge schreibt die Rolle als `ereignisse/<auftrag_id>/<rolle>-<zustand>.yaml`; der Dirigent spiegelt nach STATUS über den Integrator).

## Prozessregel nach DoR (Yama 22.08.)
Nach erteilter DoR wird der **Kriterienstand eingefroren**. Reine Erläuterungs-/Beleg-/Formulierungsberichtigungen
ohne Kriterienwirkung werden **nicht** einzeln committet, geprüft und transportiert, sondern als **ein Errata-Ereignis**
(`ereignisse/<auftrag>/<rolle>-errata.yaml`) gesammelt und mit **einem** Commit und **einem** Rückweg transportiert.
Kriterienänderung nach DoR = neue DoR-Runde. Grund: nach der DoR um 00:49 folgten mehrere Berichtigungs-Commits,
Prüfzyklen und Rückwege; der vollständige Transport lag erst 07:54 vor.
**Basis-Regel für Bauten:** ist der Rollenzweig nicht Fast-Forward-fähig auf die Integration (divergiert), meldet die
Rolle `BASE_BLOCKED`; kein Merge mit Altarbeit, kein Reset, kein Bau auf veraltetem Stand. Der Dirigent stellt einen
frischen, isolierten Arbeitsstand vom Integrations-HEAD bereit (alter Zweig unverändert als Beleg, umbenannt).

## Für den Dirigenten
- Änderung eines Auftrags = `generation` +1, Datei neu schreiben, Digest neu berechnen (`shasum -a 256`), alte ACKs verfallen.
- **Atomar veröffentlichen** (Befund Integrator 23:44 — er las gen 4 mit dem Digest von gen 3, weil Datei und
  `.sha256` in zwei Runden entstanden): erst `rollen/<rolle>.yaml.tmp` schreiben, Digest in `.sha256.tmp` rechnen,
  dann **beide** per `mv` umbenennen — nie eine Runde lang Datei und Digest auseinanderklaffen lassen.
- Rollenbesetzung: eine Sitzung je Rolle; `sitzung_erwartet`, Registrierung und ACK müssen **dieselbe** Sitzungs-ID
  nennen, sonst ist die Generation nicht quittierbar. Lebensnachweis einer Sitzung = laufender Sitzungsprozess, nie
  Transkript-mtime.
- Eine Rolle hat **höchstens einen** aktiven Auftrag. Kein „nebenbei".
- Eine schlafende Sitzung gilt **nicht** als informiert — bis ein Orchestrator existiert, stößt Yama sie einmal manuell an.
- Technische Commit-Barriere (Rolle, Sitzung, Worktree, Branch, aktiver Auftrag, erlaubte Pfade, Voraussetzungen; auch nackter `git commit` und Merge)
  ist **Bauauftrag** (A-37-Erweiterung / Z0-I2 / Z0-I3), spezifiziert vom Planner — siehe `auftraege/Z0-I3-pull-steuerung.vorgabe-yama.md`.

## Meldepflicht je Rolle und Auftrag (Yama 22.08., verbindlich ab sofort — ohne Generationswechsel)
**Vor jeder Sacharbeit (nach Auftrag + Digest, vor der Lease):** menschenlesbar „Ich habe den Auftrag `<ID>` in Generation
`<N>` über die zentrale Rollenquelle vom Dirigenten erhalten. Ich bearbeite diesen Auftrag als `<Rolle>` im Worktree
`<Worktree>` auf dem Zweig `<Branch>`." + Ereignis `ereignisse/<auftrag_id>/<rolle>-AUFTRAG_GESTARTET.yaml`
(ereignis, auftrag_id, generation, rolle, quelle: dirigent, digest, sitzungs_id, worktree, branch, ausgangs_sha,
fencing_token, zeit, erklaerung).
**Beim Abschluss:** nur der eigene Rollenanteil, mit rollenspezifischem Begriff — Planner `SPEZIFIZIERT` · Plan-Prüfer
`ERTEILT/NICHT ERTEILT` · Generator `CODE_FERTIG` · Evaluator `ABGENOMMEN/NACHBESSERN` · Integrator
`TRANSPORTIERT/ZUSTAND_NACHGEZOGEN` · Release-Prüfer `RELEASE_FREI/NICHT RELEASE_FREI` · Dirigent `ZUGEWIESEN/ENTSCHIEDEN`;
Wortlaut „Ich habe meinen Anteil an diesem Auftrag als `<Rolle>` abgeschlossen." + Ereignis
`<rolle>-AUFTRAG_ABGESCHLOSSEN.yaml` (plus abschlussbegriff, ergebnis_sha). Nie „alles erledigt"; Generator behauptet keine
Abnahme, Planner keinen Bau, Evaluator keine Reparatur, Integrator keine Fachentscheidung. Vollständiger Wortlaut:
`docs/regelwerk/MELDEPFLICHT-AUFTRAG.md` (rolle/dirigent). Technische Prüfung: Z0-I3/Z0-I4.

## Wecker, Takte, Dispatcher (Yama 22.08.)
Monitore, Crons und ein künftiger Dispatcher dürfen **ausschließlich**: Änderung erkennen · Digest prüfen · die
registrierte Sitzung wecken · eine Zustellmeldung erzeugen. Sie dürfen **niemals**: ACK schreiben · Lease nehmen · Dateien
ändern · committen · einen zweiten Rollenprozess starten, wenn einer aktiv ist (Single-Flight je Rolle). Kein detached
Prozess je Rolle; Ziel ist **ein** zentraler, `launchd`-überwachter Dispatcher (Folgeauftrag **Z0-I4**, nach A-37).
**Unterbrechung bei neuer Generation derselben Kennung:** laufende atomare Dateioperation beenden → vor dem nächsten
Schreibzugriff stoppen → keinen alten Commit erzwingen → Dirty-State (Pfade, Diff-Stat, Hash) dokumentieren → Lease
freigeben → Unterbrechungsereignis schreiben → neuen Auftrag lesen und quittieren. Kein Reset, kein Verwerfen, kein Amend.
Bei anderer Kennung setzt ausschließlich der Dirigent `vorrang: nach_abschluss | sofort_unterbrechen`.

## Aktueller Stopp
Generator-Sitzung `7df19ed4` (PID 87659, committete direkt im gemeinsamen Checkout) ist per `SIGSTOP` angehalten. Stopp-SHA `4e02c273`.
Kein `CONT`, bis A-42 abgeschlossen, A-37 mit `pre-commit` fertig, fünf Negativproben bestanden, nacktes `git commit` gesperrt, Evaluator-Abnahme vorliegt.
Reihenfolge: **A-42 → A-37 → Z0-I1 → übrige Abnahmen.** A-38, A-39, Z1-W1-5-1 und alle Produktarbeit geparkt.
