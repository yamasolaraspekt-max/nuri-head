# Z0-I4 — Zentraler, dauerhafter Dispatcher (Vorgabe Yama 22.08.2026 ~09:50, Wortlaut; Kriterien: Planner nach A-37)

```yaml
status: "VORGABE — nicht vor A-37 ABGENOMMEN spezifizieren; bis dahin keine neuen Wecker/Cron-Konstruktionen, vorhandene nur als Provisorium, A-37-Kette nicht umbauen"
reihenfolge: "Planner spezifiziert -> Plan-Pruefer prueft -> Generator baut -> Evaluator prueft Positiv-/Negativ-/Absturz-/Neustartfaelle unabhaengig -> erst nach ABGENOMMEN werden die individuellen Rollenwecker entfernt"
```

## Entscheidung (Yama)
Die individuellen Rollenwecker werden nicht weiter ausgebaut. Ziel ist Z0-I4 als zentraler, dauerhafter Dispatcher.
Der Dispatcher ist **keine fachliche Rolle und kein weiterer Agent** — nur technische Zustell- und Überwachungsinfrastruktur.

```
Yama  →  Entscheidungen
Dirigent  →  veröffentlicht Rollenauftrag + Digest + Auslöser
Zentraler Dispatcher  →  validiert und weckt genau eine Rolle
Planner → Plan-Prüfer → Generator → Evaluator → Integrator
```

## Aufgaben des Dispatchers
1. `rollen/*.yaml` und relevante Abschlussereignisse beobachten.
2. Digest, Generation, Sitzung, Rolle, Worktree und Branch prüfen.
3. Prüfen, ob alle `ausloeser` erfüllt sind.
4. Pro Rolle höchstens einen Lauf starten — Single-Flight.
5. Ausschließlich die registrierte Sitzungs-ID wecken.
6. Niemals selbst ACK, Lease, Commit, Status oder Abschlussmeldung schreiben.
7. Abstürze über Sitzungs-ID, Lauf-PID, Startzeit, Heartbeat und Fencing-Token bewerten.
8. Nach Neustart offene Aufträge erneut aus den Dateien rekonstruieren.
9. Bei Fehlern begrenzt erneut versuchen; danach `DISPATCH_BLOCKED` an Dirigent/Yama melden.
10. Aus einem Auftrag niemals selbst neue Prioritäten oder Generationen ableiten.

**Zustellung ist mindestens-einmal**, nicht „genau einmal"; doppelte Sacharbeit verhindern Single-Flight, Lease,
Fencing und Commit-Gates. Neustart, Laufwechsel und veraltete PID dürfen keine Aufträge verlieren oder Leases allein
für verwaist erklären. Durch `launchd` dauerhaft gehalten, stabiler Pfad (nicht Scratchpad).

## Priorität und Unterbrechung (nur der Dirigent setzt)
`vorrang: nach_abschluss` (Regel) oder ausnahmsweise `vorrang: sofort_unterbrechen`. „Sofort" bedeutet **nicht**
Prozess-Kill: die laufende Rolle beendet den nächsten sicheren Schritt, schreibt keinen weiteren Commit, gibt die
Lease frei und meldet `UNTERBROCHEN`. Der Dispatcher entscheidet das niemals selbst. Rollen schreiben Startmeldung,
ACK, Lease und Abschlussmeldung selbst.

## Abnahmekriterien / Pflichtproben (unabhängig belegt, Evaluator)
- Digest falsch → kein Start · Auslöser fehlt → kein Start · doppelte Dateimeldung → nur ein Lauf · Rolle läuft bereits → kein zweiter Lauf
- falscher Worktree/Branch/Sitzungs-ID → Ablehnung · Neustart des Dispatchers → offene Aufträge bleiben auffindbar
- Prozessabsturz vor ACK → kontrollierte Wiederholung · Absturz nach Commit vor Abschlussmeldung → kein Doppelcommit
- neue Generation während der Arbeit → nächster Schreibschritt blockiert · ungültige Abschlussmeldung / nicht existierender SHA → keine Folgeaktivierung
- Start- und Abschlussmeldung tragen die richtige Rollenbezeichnung · Release-Prüfer wird nur geweckt, wenn er tatsächlich einen Auftrag erhält
- Evaluator besitzt danach genau **einen** Single-Flight-Takt (heute zwei versetzte 5-Minuten-Takte)
Nach erfolgreicher Abnahme werden sämtliche rollenbezogenen Cron-/Sitzungswecker entfernt.

## Intendantenklärung (Yama)
Im aktuellen Repository existiert keine Rolle Intendant (kein Rollenordner, keine Rollenquelle, kein Branch, kein Worktree,
kein Commit); der Architekturentwurf schließt ihre Einführung ausdrücklich aus („Dirigent wird einmal definiert; Intendant wird
nicht eingeführt") — **historischer Architekturentscheid**, keine technische Panne. Keine neue Intendantenrolle ohne separate
Entscheidung Yamas. Falls später gewünscht: ausschließlich strategische Portfolio-/Priorisierungsaufgaben; weder
Dirigentenrouting noch Yamas Freigaberecht duplizieren.

## Risiken beim Bau — Vergleich mit dem heutigen Zustand (Dirigent, 22.08. 09:58; gemessen, nicht vermutet)
| # | Gefahr | woran gemessen | Gegenmaßnahme (Pflicht für Spezifikation/Abnahme) |
|---:|---|---|---|
| R1 | **Doppelläufe derselben Sitzung** in der Übergangsphase: Dispatcher weckt per `claude -p --resume`, während die alten Sitzungs-Crons UND der interaktive VS-Code-Lauf derselben Sitzung weiterlaufen (heute: `aa0cddd3` lief als 88088 **und** 91834) | Befund Generator 09:00, Plan-Prüfer „fünf falsche PIDs" | Single-Flight prüft **alle** Läufe einer Sitzungs-ID (`ps`: `--resume=<id>` **und** interaktive Läufe), nicht nur eigene Starts; alte Wecker erst **nach** ABGENOMMEN entfernen, bis dahin weckt der Dispatcher **nicht** (Schattenbetrieb: nur beobachten/melden) → eigene Pflichtprobe „Rolle aktiv durch fremden Lauf → kein Start" |
| R2 | **Headless-Lauf mit `bypassPermissions`, gestartet von einem Daemon** — die Steuerungsdateien in `~/.ticket-steuerung/rollen/` sind für jeden lokalen Prozess schreibbar; ein manipulierter Auftrag würde vollautomatisch ausgeführt | heute: Dateirechte 644, keine Signatur | Dispatcher startet nur Aufträge, deren `.sha256` **und** ein zweites, nur dem Dirigenten/Yama zugängliches Siegel (z. B. Signatur mit Schlüssel in `~/.ticket-steuerung/.schluessel/`, 0600) stimmen; Rollenläufe ohne `bypassPermissions` wo möglich; Entscheidung Yama |
| R3 | **Neue Commit-Gates (A-37-22e/25) brechen legitime Commits aller Rollen**, sobald sie transportiert sind (`core.hooksPath=.githooks` ist je Worktree wirksam; nach Nachziehen überall) | A-37-Bau läuft im Generator-Baum; Integration noch ohne `pre-commit` | Evaluator prüft **Positivfälle je Rolle** (jede Rolle committet im eigenen Baum mit gültiger ACK/Lease; Integrator-Integrationscommit) **vor** dem Transport; Rollback-Pfad: Hook-Datei ist Text, ein Revert-Commit stellt den alten Stand her |
| R4 | **Dispatcher als neuer Single Point of Failure** (Daemon stirbt, launchd startet neu, offene Aufträge „vergessen") | heute gar kein Dispatcher; Planner-Takt im Scratchpad | Neustart-Rekonstruktion aus Dateien (Pflichtprobe), `DISPATCH_BLOCKED`-Meldung, Heartbeat-Datei des Dispatchers; bis zur Abnahme bleibt der manuelle Dirigenten-Weg gültig |
| R5 | **Transport durch den Dispatcher** (er „weckt" den Integrator — aber er darf nie selbst `rueckweg.py` fahren) | Vorfall Generator 08:06 | A-37-22b Preflight gilt auch für Dispatcher-gestartete Läufe: nur die Integrator-Sitzung im kanonischen Checkout transportiert |
| R6 | **Wecken des falschen Baums/der falschen Sitzung** nach Umbenennungen (Beleg-Baum `ticket-rolle-generator-beleg-2026-08-21`) | A-37-22c | Dispatcher liest Worktree/Branch ausschließlich aus der Rollendatei und prüft `git rev-parse --abbrev-ref HEAD` im Ziel vor dem Wecken |
**Schlussfolgerung:** nichts davon betrifft den heutigen Betrieb (Dispatcher existiert nicht); alles davon muss in die Planner-Spezifikation und die Evaluator-Pflichtproben von Z0-I4. Schattenbetrieb (beobachten/melden, nicht wecken) ist die sichere erste Stufe.

## Abgrenzung
| Instanz | darf | darf nicht |
|---|---|---|
| Yama | entscheiden, freigeben, Reihenfolge setzen | — |
| Dirigent | Aufträge veröffentlichen (Rollenquelle + Digest + Auslöser + `vorrang`), Generationen erhöhen, Lagebericht, Steuerungsentscheidungen unter Vollmacht | Kriterien, Produktcode, Voten, Status, Abnahme |
| Dispatcher | beobachten, validieren, wecken (genau eine registrierte Sitzung je Rolle, Single-Flight), Zustellmeldung, `DISPATCH_BLOCKED` | ACK, Lease, Commit, Status, Abschlussmeldung, Prioritäten/Generationen ableiten, Unterbrechungen entscheiden |
| Intendant (nicht eingeführt) | — (ggf. später nur Strategie) | Routing, Commits, Freigaben |
