# ABNAHMERÜCKSTAND — gebaute, aber unbewiesene Produkt-/Sicherheitskorrekturen (12)

```yaml
basis: docs/fortschritt/inventur-bilanz-2026-08-22.md @ 06642e35 (Mess-SHA eb304cf5) — unveraenderliche Messbasis
fortschreibung: "diese Liste wird fortgeschrieben; Zustandswahrheit bleibt docs/STATUS.md; je Posten genau eine naechste Handlung"
reihenfolge_yama: "nach Risiko — W0-5 -> W0-7/W0-3 -> W0-1 -> W0-8 -> W0-10 -> W0-11 -> W0-12 -> Z1-Bauten inkl. Browser. Keine Sammelabnahme. Scheitert ein Votum: genau dieser Auftrag zurueck an Planner -> Generator -> Evaluator."
voraussetzung: "A-37 vollstaendig ABGENOMMEN (technische Barriere) und Z0-I1 (Test-DB-Isolation) VOR den Abnahmen; Browserfaelle erst danach"
freigabeurteil_yama: "bis A-37, Z0-I1 und die hochkritischen Sicherheitsvoten abgeschlossen sind: nicht releasebereit; keine Sicherheitswirkung als bewiesen melden; Browserfaelle offen kennzeichnen; betroffene unbestaetigte Routen nicht als sicher betrachten"
```

| # | Kennung | Befund(e) | Schwere | Bau-SHA | unabh. Votum | Browserpflicht | Abhängigkeit | Besitzer | nächste konkrete Handlung |
|---:|---|---|---|---|---|---|---|---|---|
| 1 | Z2-W0-5 | A-1, A-2, A-3, A-4 (IDOR GPS/Fotos/Master-Sets/Material) | HOCH | `28ca0834` (Checkout) / `ef7a8c89` (Worktree, Belegzweig) | — ; Evaluator-Vergleich liegt (`evaluator-w0-5-vergleich.yaml`, Empfehlung A = `28ca0834` bleibt) | nein (API-Tests, Test-DB) | A-37, Z0-I1 (DB) | Dirigent → Entscheidung, dann Evaluator | Entscheidung „28ca0834 bleibt, `ef7a8c89`-Vorgesetztenkette als Vorrat" festhalten → Evaluator-Votum auf `28ca0834` (drei Fälle fremd 403/eigen 200/Vorgesetzter) nach Z0-I1 |
| 2 | Z2-W0-7 | S-1 (`/planner/*` 61 Routen) + Rechte-Schalter + Item Planner | HOCH | `5831c06a` | — | nein | A-37, Z0-I1 | Evaluator | Votum: beide Schalterstellungen, Fremder 403 bei `false` |
| 3 | Z2-W0-3 | S-1 Verschärfung (Attendance `employee_id` aus Sitzung) | HOCH | `69c85d01` | — | nein | A-37, Z0-I1 | Evaluator | Votum inkl. Rot-Probe (Generator meldete eigenen wertlosen Ersttest) |
| 4 | Z2-W0-1 | S-5 Gebäudeakte `/objekte/*` | HOCH | Generator 21.08. 20:18 (SHA aus STATUS ziehen) | — | nein | A-37, Z0-I1 | Evaluator | Votum: ohne Customer-Recht 403; mit Schalter true 200 |
| 5 | Z2-W0-8 | A-6 `secure.image` Recht + Bindung | M/H | `29eb791c` | — | ja (Bildauslieferung) | A-37, Z0-I1, Browser-DB | Evaluator | Votum + Browserfall nach Z0-I1 |
| 6 | Z2-W0-10 | A-8 Master-Sets-API Stilllegung (Y-11) | GELB/ROT | `cb771cbf` | — | nein | A-37 | Evaluator | Votum: Schalter aus → 404/410, Debug-Endpunkt tot, Query-Secret weg |
| 7 | Z2-W0-11 | A-10 CSRF `ids/callback` Teil A (ehrlich begrenzt) | ROT | `fd94dea5` | — | nein | A-37; Teil B Y-12; Teil C W0-11c | Evaluator | Votum Teil A (Ablehnung, keine Persistenz-Zuschreibung); Restabgrenzung B/C ausdrücklich |
| 8 | Z2-W0-12 | A-7 Token-Laufzeit 8 h (Y-10) | M | `976f7d6b` | — | nein | A-37 | Evaluator | Votum: Ablauf, Widerruf, Bereinigung |
| 9 | Z1-W1-5 | K-1 `insulationType` Ausweis | S | `da86c59d` + Nachbesserung `7eaab966` (direkt im Checkout, **nicht abnahmefähig**) | `a4144ff4` NACHBESSERN (eine Zahl) | nein | A-37 (Nachbesserung muss regelkonform neu entstehen) | Planner → Generator (Worktree) → Evaluator | Nachbesserung Z1-W1-5-1 im Generator-Worktree neu bauen, dann Votum |
| 10 | Z1-W1-1 | K-4 DIN-Badge Vorbehalt | S/M | `b3c6ac29` | `d40adbf5` Kriterium C ENV_BLOCKED | **ja** | Z0-I1 (Browser-DB) | Evaluator | Browserlauf nach Z0-I1, dann Endvotum |
| 11 | Z1-W1-2 | P-1 Walmdach Ablehnung | S | `60c04eef` | `27143f96` 4/5, E ENV_BLOCKED | **ja** | Z0-I1 | Evaluator | Browser: Fehlermeldung sichtbar; Widerspruch `dachformVorlagen.ts` auflösen |
| 12 | A-38 | Merges laufen am Tor vorbei (Governance, kein Inventurbefund, geparkt) | Prozess | `0f731c22` | — | nein | A-37 | Evaluator (nach A-37) | Votum nach A-37; bis dahin geparkt |
