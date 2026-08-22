# A-37-25 WIRKSAMKEIT — „der nackte git commit scheitert" in JEDEM realen Baum (Vorgabe Dirigent, 2026-08-22T10:56:53+0200; Yama: „bestätige mir, dass es erledigt ist")

```yaml
status: "VORGABE — Generationen folgen ERST nach Evaluator-ABGENOMMEN (NACHPRUEFUNG-evaluator-A-37) und Integrator-TRANSPORTIERT (rolle/generator -> Integration). Bis dahin keine Aenderung an den laufenden Generationen."
gemessen_10_55: "core.hooksPath=.githooks in 7/7 Baeumen; .githooks/pre-commit NUR in ticket-rolle-generator (66 Z., ausfuehrbar); Integration + 6 Rollenbaeume: FEHLT -> nackter Commit geht dort heute durch"
erledigt_wenn: "7/7 reale Baeume: .githooks/pre-commit vorhanden+ausfuehrbar UND Probe 'nackter git commit' scheitert (exit != 0, kein Commit) — vom Evaluator je Baum ausgeloest, nebenwirkungsfrei; Dirigent misst die Dateilage unabhaengig nach und meldet Yama"
```

## Reihenfolge (Ball wandert, keine Stufe wird uebersprungen)
1. **Generator gen 9** — A-37-20 (laeuft). → CODE_FERTIG mit endstand_sha/ergebnis_sha.
2. **Evaluator gen 7** — Nachpruefung A-37-20 + Gegenprobe. → ABGENOMMEN (oder erneut NACHBESSERN → zurueck zu 1).
3. **Integrator gen 7** — Transport rolle/generator (Endstand) in die Integration; Zustandscommit A-37 (EINE Kennung, Beleg = Votum-SHA). Damit liegt der Haken im Integrations-Checkout — dem Ort der sechs nackten Commits.
4. **Alle Rollen, je eine Generation `aktion: basis_nachziehen`** (Planner, Plan-Pruefer, Evaluator, Integrator, Release-Pruefer, Dirigent; Generator hat ihn bereits): im EIGENEN Baum `git merge --no-edit auto/hausplaner-integration` (kein Rebase, kein Force); danach `ls -l .githooks/pre-commit` melden. Keine Sacharbeit in dieser Generation. Konflikte -> BASE_BLOCKED melden, nichts erzwingen.
5. **Evaluator gen 8** — in JEDEM der sieben realen Baeume die Probe: nackter `git commit` scheitert. Nebenwirkungsfrei heisst: die Probe darf KEINEN Commit hinterlassen; Verfahren vom Evaluator festgelegt (z. B. staged Probe-Datei, Commit-Versuch, Erwartung exit != 0; faellt das Tor wider Erwarten durch, sofort `git reset --soft HEAD~1` + Meldung als Mangel). Ergebnis je Baum: Pfad, Befehl, exit, Rohausgabe. Votum WIRKSAM / NICHT WIRKSAM je Baum.
6. **Dirigent** misst unabhaengig die Dateilage 7/7 und meldet Yama: „erledigt" nur bei 7/7 WIRKSAM.

## Grenze (benannt, nicht geloest — aus Bau und Votum)
`--no-verify` und umgebogener `core.hooksPath` uebergehen jeden Haken. Das Tor wirkt gegen Gewohnheit und Versehen, nicht gegen Absicht. Naechste Schicht: 22e (gebaut, bestaetigt) + Z0-I2/I3 (Identitaet an Lease/Fencing; Backlog 17).
