# MELDEPFLICHT — Start- und Abschlussmeldung je Rolle und Auftrag (Yama, 22.08.2026 — verbindlich)

```yaml
erteilt_von: "Yama 22.08.2026 ~09:25, Wortlaut uebernommen"
gilt_fuer: "jeden Rollenlauf in jedem Auftrag; menschenlesbar UND strukturiert in /Users/yamanuri/.ticket-steuerung/ereignisse/<auftrag_id>/"
technische_pruefung: "zentraler Monitor (Z0-I3/Z0-I4) erkennt Start/Ende nur bei Rollenname, TICKET_ROLLE-Uebereinstimmung, aktueller Auftrag-ID + Generation, stimmendem Digest, passender Sitzung/Worktree/Branch, zuordenbarer Start- und Abschlussmeldung derselben Rolle, existierendem Ergebnis-SHA, rollenpassendem Zustandsbegriff"
```

## Pflichtmeldung vor Arbeitsbeginn (nach Auftrag + Digest, vor jeder Sacharbeit, vor der Lease)
> Ich habe den Auftrag `<Auftrag-ID>` in Generation `<N>` über die zentrale Rollenquelle vom Dirigenten erhalten.
> Ich bearbeite diesen Auftrag als `<Rolle>` im Worktree `<Worktree>` auf dem Zweig `<Branch>`.

Start-Ereignis `ereignisse/<auftrag_id>/<rolle>-AUFTRAG_GESTARTET.yaml`:
```yaml
ereignis: AUFTRAG_GESTARTET
auftrag_id: BAU-generator-A-37
generation: 8
rolle: generator
quelle: dirigent
digest: "..."
sitzungs_id: "..."
worktree: /Users/yamanuri/Documents/ticket-rolle-generator
branch: rolle/generator
ausgangs_sha: "..."
fencing_token: 3
zeit: "..."
erklaerung: "Ich bearbeite diesen Auftrag als Generator."
```
Erst danach dürfen Lease und Sacharbeit beginnen.

## Pflichtmeldung beim Abschluss — genau der eigene Rollenanteil, nie „alles erledigt"
| Rolle | Wortlaut | Abschlussbegriff |
|---|---|---|
| Planner | Ich habe den Auftrag `<ID>` als Planner vollständig spezifiziert. Ergebnis-SHA `<SHA>`. Eine technische Umsetzung oder Abnahme behaupte ich nicht. | `SPEZIFIZIERT` |
| Plan-Prüfer | Ich habe den Auftrag `<ID>` als Plan-Prüfer geprüft. Mein Votum lautet `ERTEILT`/`NICHT ERTEILT`. Ich habe nichts gebaut. | `ERTEILT` / `NICHT ERTEILT` |
| Generator | Ich habe den Auftrag `<ID>` als Generator gebaut und als `CODE_FERTIG` gemeldet. Ergebnis-SHA `<SHA>`. Eine unabhängige Abnahme behaupte ich nicht. | `CODE_FERTIG` |
| Evaluator | Ich habe den Auftrag `<ID>` als Evaluator unabhängig geprüft. Mein Votum lautet `ABGENOMMEN`/`NACHBESSERN`. Ich habe nichts gebaut oder repariert. | `ABGENOMMEN` / `NACHBESSERN` |
| Integrator | Ich habe den belegten Stand des Auftrags `<ID>` als Integrator transportiert bzw. den Zustand nachgezogen. Integrations-SHA `<SHA>`. Ich habe keine fachliche Entscheidung getroffen. | `TRANSPORTIERT` / `ZUSTAND_NACHGEZOGEN` |
| Release-Prüfer | Ich habe den Auftrag `<ID>` als Release-Prüfer geprüft. Mein Votum lautet `RELEASE_FREI`/`NICHT RELEASE_FREI`. Ich habe nichts veröffentlicht. | `RELEASE_FREI` / `NICHT RELEASE_FREI` |
| Dirigent | Ich habe den Auftrag `<ID>` als Dirigent zugewiesen bzw. eine Steuerungsentscheidung getroffen. Ich habe weder Kriterien geschrieben noch Produktcode gebaut oder den Auftrag abgenommen. | `ZUGEWIESEN` / `ENTSCHIEDEN` |

Abschluss-Ereignis `ereignisse/<auftrag_id>/<rolle>-AUFTRAG_ABGESCHLOSSEN.yaml` mit denselben Feldern plus
`abschlussbegriff`, `ergebnis_sha`, `erklaerung: "Ich habe meinen Anteil an diesem Auftrag als <Rolle> abgeschlossen."`

## Technische Ablehnung (Beispiele)
Generator schreibt `ABGENOMMEN` · Planner schreibt `gebaut` · Evaluator schreibt `behoben` · Integrator schreibt `fachlich
entschieden` · Abschlussmeldung ohne Rolle · Abschlussmeldung zu veralteter Generation · Ergebnis-SHA existiert nicht.
Der Monitor akzeptiert keine allgemeine Aussage „Auftrag erledigt", aus der Planung, Bau, Prüfung und Abnahme nicht
eindeutig getrennt hervorgehen.

## Einführung
Gilt ab sofort für laufende und neue Aufträge (Hinweis-Ereignis je Auftragsordner); Generationen werden dafür nicht
erhöht. Technische Prüfung durch den zentralen Monitor ist Teil von Z0-I3/Z0-I4 (nach A-37).
