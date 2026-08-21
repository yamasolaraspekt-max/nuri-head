# DIRIGENT-TAFEL — die eine verbindliche Anweisungstafel für alle Rollen-Sitzungen

```yaml
stand: "21.08.2026 ~23:00 — Fassung 1; wird vom Dirigenten fortgeschrieben, Aenderungen nur hier"
lesepflicht: "JEDE Rollen-Sitzung liest diese Datei VOR jeder Arbeit neu ein: /Users/yamanuri/Documents/ticket-rolle-dirigent/docs/auftraege/DIRIGENT-TAFEL.md (lesen, nie schreiben)"
warum: "Sitzungsnachrichten des Dirigenten sind gemessen NIE in einer Rollen-Sitzung angekommen (0 cross-session-message in sechs Transkripten). Wirksam ist nur, was gelesen oder technisch erzwungen wird."
autoritaet: "Wortlaut Yama 21.08. (Weg a, acht Korrekturpunkte, fuenf CONT-Bedingungen); Dirigent stellt keine Kriterien"
reihenfolge: "A-42 -> A-37 -> Z0-I1 -> uebrige Abnahmen — nichts anderes"
```

## Gilt für ALLE — jetzt
- **WIP-Stopp.** Keine neuen Bauten, keine Nebenanalysen, keine „Kein Ball"-Commits, keine Altposten.
  A-38, A-39, Z1-W1-5-1, Symbol-/Zähl-/Maurer-Arbeiten und **sämtliche Produktarbeit bleiben geparkt**
  — nicht gelöscht, nicht zurückgesetzt, nicht abgenommen, nicht als Fortschritt gewertet.
- **Gestoppt:** Generator-Sitzung `7df19ed4` (PID 87659, arbeitete direkt im gemeinsamen Checkout
  `auto/hausplaner-integration`, sechs direkte Commits `824f8512` `8529c63b` `7eaab966` `ada3b645`
  `6f89d060` `4e02c273`) per `SIGSTOP`. **Stopp-SHA `4e02c273`.** Kein `CONT`, bis: (1) A-42
  unabhängig abgeschlossen, (2) A-37 mit wirksamem `pre-commit`-Hook fertig, (3) fünf Negativproben
  bestanden, (4) nacktes `git commit` umgeht das Tor nicht, (5) unabhängige Evaluator-Abnahme liegt vor.
- **Niemand committet im gemeinsamen Checkout `/Users/yamanuri/Documents/ticket`** außer dem
  Integrator mit seinem eng begrenzten Integrationscommit. Jede Rolle nur im eigenen Worktree.
- **Berichtsform (jede Meldung):** Ausgangs-SHA · Ergebnis-SHA · geänderte Pfade · unabhängiges Votum ·
  Browserstatus · offene Abweichung · nächster Ball. Ein Commit ohne Diff ist kein Fortschritt.
  Belege: `Commit-SHA + Dateipfad + stabiler Anker`, kein nackter Zeilenzeiger.
- **Bestätigung:** jede Rolle committet als Erstes eine Zeile in ihrem Worktree (Blatt
  `docs/auftraege/bestaetigung-<rolle>.md` oder in ihrem Befundblatt): „`<rolle>`: Tafel Fassung 1
  gelesen · HEAD `<sha>` · in Arbeit war: … · geparkt." — das ist die einzige Bestätigung, die zählt.

## EVALUATOR (Sitzung `303cefb6`, Worktree `ticket-rolle-evaluator`)
Ausschließlich **A-42** unabhängig abnehmen (Bau `26c46f31`), frische Instanz, nichts sonst:
`461 = 289 + 172` am damaligen Ausgangsstand · alle 172 Blöcke byte-identisch · keine Inhaltszeile
verloren/hinzu · gerade Zaunbilanz in beiden Dateien · 104 Auftragsdatensätze unverändert · Ballbesitz
je Rolle vorher = beide Dateien nachher · **zweiter Lauf = leerer Diff** · die zwei zurückgebliebenen
Überschriften bewertet · `scripts/yama-posten.py` + Wacheanweisungen durchsuchen beide Dateien ·
danach trägt STATUS Bau-SHA/Zustand/Ball. Zusätzlich **Zieldateischutz** prüfen: `docs/BEFUNDNOTIZEN.md`
hat heute nicht dieselbe Schreibbarriere wie `docs/STATUS.md` → als Befund melden (kein Abzug, wenn
A-42 das nicht verlangte). Votum mit Befehl + Rohausgabe + SHA, im eigenen Worktree committen.
Zweitens, danach: **W0-5-Doppelbau fachlich vergleichen** (`28ca0834` `app/Support/Planner` vs
`ef7a8c89` `app/Traits/PlannerZustaendigkeit`; Kriterium A drei Fälle; Test-Pfade; Konfliktlage) →
Vergleichsbericht, **kein** Votum auf einen der beiden, nichts überschreiben. Keine Z1-/W0-Voten.

## PLANNER (neue Sitzung von Yama, Worktree `ticket-rolle-planner`, `TICKET_ROLLE=planner`)
Erst **nach** dem A-42-Votum: A-37-Blatt erweitern — Kriterien gehören dir:
A-37-22 `scripts/rueckweg.py` kennt **alle** Rollenbäume inkl. `ticket-rolle-release` und
`ticket-rolle-dirigent` · A-37-23 Rollen-Tor kennt `dirigent` (Worktree `ticket-rolle-dirigent`, Zweig
`rolle/dirigent`) mit technisch begrenztem Schreibbereich `docs/konzept/`, `docs/regelwerk/`,
Steuerungsblätter in `docs/auftraege/`; kein Produktcode, kein `docs/STATUS.md`, kein
`docs/BEFUNDNOTIZEN.md` · A-37-24 `docs/BEFUNDNOTIZEN.md` unter dieselbe Schreibbarriere wie STATUS (nur
Integrator) · **A-37-25 echtes `pre-commit`-Tor** (`.githooks/pre-commit`, `core.hooksPath` steht
schon auf `.githooks`; `commit-msg` prüft heute nur Merges): nackter Commit im falschen Worktree
abgewiesen · fehlende Rolle abgewiesen (Exit 5) · falscher Zweig abgewiesen · Integrator darf den
begrenzten Integrationscommit · Merge-Hook bleibt wirksam · `--no-verify` als nicht technisch
verhinderbare Grenze **dokumentiert** · jede Negativprobe tatsächlich ausgelöst · `js-yaml` direkt +
Lockfile · Tor in allen Bäumen · 21/21 plus neue Kriterien. Mein Vorgriff `5c9afbc7` (dirigent-Zeile
im Tor auf `rolle/dirigent`) ist zu übernehmen **oder** zu ersetzen, nicht still zu behalten.
Danach **Z0-I1 vervollständigen** (Yama-Messung: Trennung heute freiwillig): `TEST_ROLLE`
verpflichtend, kein Rückfall auf gemeinsames `ticket_testing` · Rollen `evaluator`, `generator`,
`security`, `browser` · `SELECT DATABASE()` vor Migration/Seed/Truncate · vier dauerhafte
Testdatenbanken auf der normalen MySQL-Instanz (Y-13) · parallele Positiv- und Kollisionsprobe.
Danach **Z0-I2 Claim-Sperre spezifizieren, nicht bauen**: Vorlage `docs/konzept/agentenarchitektur-v2.md`
§8 (`counter` / `counter.lock/` / `active/`, neun Schritte, owner.yaml-Lebendigkeit, fail closed).

## PLAN-PRÜFER (Sitzung `3870df7a`, Worktree `ticket-rolle-plan-pruefer`)
**Stopp** für A-39 und alle Nebenanalysen/Altposten, keine „Kein Ball"-Commits. Nächste Arbeit
ausschließlich: DoR für das erweiterte A-37-Blatt, sobald der Planner es vorlegt; danach Z0-I1/Z0-I2;
danach Konzeptprüfung `docs/konzept/agentenarchitektur-v2.md` (Fassung `0d897b0e`, rolle/dirigent).

## GENERATOR im eigenen Worktree (Sitzung `aa0cddd3`, PID 88088, `ticket-rolle-generator`)
Verfügbar, aber **nichts bauen**, bis das erweiterte A-37-Blatt die DoR trägt; dann **ausschließlich
A-37**, im eigenen Worktree, über `commit-pruefen.sh`. W0-5: keinen Stand überschreiben oder löschen.

## INTEGRATOR (Sitzung `03737d75`, PID 80335, gemeinsamer Checkout)
Transportiert **ausschließlich** Ergebnisse der Reihenfolge A-42 → A-37 → Z0-I1. Keine Rückwege von
Altposten-/Nebenanalyse-Commits mehr; keine Zustands-/Abnahmenachträge für A-38/A-39/Z1-W1-5-1. A-42:
**erst nach** Evaluator-Votum Datensatz + Tafel in **einem echten, nicht leeren** Zustandscommit,
Gegenprobe `git show --stat`. Leere Zustandscommits abweisen. Uncommittete
`docs/FACHPRUEFUNG-DREI-LINSEN.md` im Checkout nicht anfassen. Rückweg von `rolle/dirigent` erst nach
A-37-22 (Werkzeug kennt den Baum).

## RELEASE-PRÜFER (Sitzung `4a2203cb`, `ticket-rolle-release`)
Keine Einfrierungen lockern, keine Transporte; beobachten, Befunde melden; kommt mit A-37-22 in den Rückweg.
