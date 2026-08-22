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

## Abgrenzung
| Instanz | darf | darf nicht |
|---|---|---|
| Yama | entscheiden, freigeben, Reihenfolge setzen | — |
| Dirigent | Aufträge veröffentlichen (Rollenquelle + Digest + Auslöser + `vorrang`), Generationen erhöhen, Lagebericht, Steuerungsentscheidungen unter Vollmacht | Kriterien, Produktcode, Voten, Status, Abnahme |
| Dispatcher | beobachten, validieren, wecken (genau eine registrierte Sitzung je Rolle, Single-Flight), Zustellmeldung, `DISPATCH_BLOCKED` | ACK, Lease, Commit, Status, Abschlussmeldung, Prioritäten/Generationen ableiten, Unterbrechungen entscheiden |
| Intendant (nicht eingeführt) | — (ggf. später nur Strategie) | Routing, Commits, Freigaben |
