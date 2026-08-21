# Z0-I3 — Pull-Auftragssteuerung mit technischer Auftragsbarriere (Vorgabe Yama 21.08.2026, Wortlaut; Kriterien: Planner)

> Baue eine zentrale, worktree-externe Auftragssteuerung als Pull-System. Nur der Dirigent schreibt
> Aufträge. Jede Rolle registriert Sitzungs-ID, PID, Rolle, Worktree und Branch und liest vor jedem
> Schreibzugriff ihren eigenen Auftrag. Implementiere atomare ACKs, Lease mit monotonem Fencing-Token,
> Heartbeat und fail-closed Recovery. Ergänze eine technische Commit-Barriere für normale Commits und
> Merges, die Rolle, Sitzung, Worktree, Branch, aktiven Auftrag, erlaubte Pfade und Voraussetzungen
> kontrolliert. Fehlender oder widersprüchlicher Auftrag muss sperren. Bestätigungen dürfen keine
> Git-Commits erzeugen. Ein Auftrag gilt erst als zugestellt, wenn eine ACK-Datei mit passender
> Generation und passendem Auftrags-Digest existiert. Schlafende Sitzungen werden nicht als erreicht
> bezeichnet. Abnahme mit Negativproben für falsche Rolle, falschen Worktree, falschen Branch,
> abgelaufene Generation, doppelte Lease, unerlaubten Pfad und nackten `git commit`.

## Zielarchitektur (Yama)
```
/Users/yamanuri/.ticket-steuerung/
├── rollen/        planner.yaml evaluator.yaml generator.yaml plan-pruefer.yaml integrator.yaml release-pruefer.yaml
├── auftraege/
├── leases/
└── ereignisse/
```
Auftrag mindestens: `auftrag_id, generation, rolle, worktree, branch, basis_sha, prioritaet, aktion,
voraussetzungen, erlaubte_pfade, verboten, status`.

## Technische Regeln (Yama)
1. Sitzung registriert einmal Identität (Sitzungs-ID, PID, Rolle, Worktree, Branch gemeinsam).
2. Eine Rolle hat höchstens einen aktiven Auftrag.
3. Die Rolle zieht den Auftrag selbst — vor jeder Werkzeugrunde und jedem Schreibzugriff; Push-Nachrichten sind Hinweise, nie Autorität.
4. Bestätigung außerhalb von Git: atomare ACK-Datei `ereignisse/<auftrag>/<rolle>-ack.yaml`; keine Bestätigungscommits.
5. Lease verhindert Doppelarbeit (monotoner Fencing-Token; zwei Sitzungen derselben Rolle nie gleichzeitig).
6. Commit-Hook kontrolliert vor jedem normalen Commit und Merge: Rolle, Sitzung, Worktree, Branch, aktiver Auftrag, erlaubte Pfade, Voraussetzungen — ohne gültigen Auftrag kein Commit.
7. Eine schlafende Sitzung gilt nicht als informiert — bis ein Orchestrator existiert, stößt Yama sie einmal manuell an.
8. Fortschritt nur durch Belege: `ZUGETEILT → GELESEN → GECLAIMT → IN_ARBEIT → CODE_FERTIG → ABGENOMMEN`, jeder Übergang mit SHA, Zeit, Rolle, Beleg.

## Einordnung (Dirigent)
Heute als **Sofortlösung von Hand** betrieben (Dateien in `rollen/`, README). Die technische Durchsetzung (Hook, Lease-Werkzeug, ACK-Prüfung) ist
Bauauftrag in der Folge A-37-Erweiterung → Z0-I2 → Z0-I3, Kriterien durch den Planner, DoR durch den Plan-Prüfer, Bau durch den Generator
im eigenen Worktree, Abnahme mit den genannten Negativproben. Lease-Modell: `docs/konzept/agentenarchitektur-v2.md` §8 (rolle/dirigent `0d897b0e`).
