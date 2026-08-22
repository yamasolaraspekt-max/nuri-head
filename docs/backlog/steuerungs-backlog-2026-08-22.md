# STEUERUNGS-BACKLOG — technisch offene Prozessbarrieren (12)

```yaml
basis: docs/fortschritt/inventur-bilanz-2026-08-22.md @ 06642e35 (Mess-SHA eb304cf5)
kern: "alles muendet in A-37 (31 Kriterien inkl. 22b/c/d/e), dann Z0-I1 (Test-DB), Z0-I2 (Claim-Sperre), Z0-I3 (Pull-Auftragsbarriere)"
stand_pull_betrieb: "SOFT-AKTIV — organisatorisch wirksam, technisch umgehbar, bis A-37 negativ abgenommen ist"
```

| # | Befund | Schwere | Auftrag/Kriterium | Bau-SHA | unabh. Votum | Abhängigkeit | Besitzer | nächste konkrete Handlung |
|---:|---|---|---|---|---|---|---|---|
| 1 | Generator committet direkt im Integrations-Checkout (nacktes `git commit` umgeht das Tor) | KRITISCH | A-37-25 `pre-commit` | — (Vorab `49972884`/`1155709d` nicht abnahmefähig) | — | DoR Nachschärfung | Plan-Prüfer → Generator | DoR über `99ea9183+fdc8d7d5+96b24ca3` → gen 8 bauen |
| 2 | Pull-Takt ist keine Schreibbarriere (Pause überlaufen) | KRITISCH | A-37-22e Generation/Digest im Commit-Gate | — | — | wie 1 | wie 1 | wie 1 |
| 3 | Transportwerkzeug vom Generator ausführbar (3 FFs) | HOCH | A-37-22b Preflight-Autorisierung | — | — | wie 1 | wie 1 | wie 1 |
| 4 | `rueckweg.py` kennt 5/7 Bäume, Name statt Pfad+Zweig | HOCH | A-37-22 | `49972884` (Vorab) | — | wie 1 | wie 1 | wie 1 |
| 5 | Doppelgänger-Bäume gleichen/ähnlichen Namens | M | A-37-22c | — | — | wie 1 | wie 1 | wie 1 |
| 6 | `BEFUNDNOTIZEN.md` ohne Schreibbarriere | M | A-37-24 | — | — | wie 1 | wie 1 | wie 1 |
| 7 | Dirigent im Tor unbekannt / Schreibbereich nicht begrenzt | M | A-37-23 | `1155709d` (Vorab) | — | wie 1 | wie 1 | wie 1 |
| 8 | Leerer/unlesbarer Zustandscommit (zwei Kennungen) | M | A-37-26 (Zustandscommit-Muster) | — | — | wie 1 | wie 1 | wie 1 |
| 9 | Doppelbau ohne Claim-Sperre (W0-5) | HOCH | Z0-I2 (spezifizieren nach A-37) | — | — | A-37 | Planner | Blatt nach V2 §8 (counter/counter.lock/active, neun Schritte, owner-Identität) |
| 10 | Headless-Identität: PID kein Lebensnachweis; Lease-Übernahme | M | Z0-I3 (Pull-Auftragsbarriere) + Z0-I2 | — | — | A-37 | Planner | Blatt nach Yamas Zielregel (Sitzungs-ID, Lauf-PID/Start, Heartbeat, Übernahme nur bei abgelaufenem Heartbeat **und** fehlendem Lauf) |
| 11 | Test-DB-Isolation fehlt (ENV_BLOCKED, keine Parallelläufe) | HOCH | Z0-I1 (Y-13 entschieden; GRANT root offen) | — | — | A-37 | Yama (GRANT) → Planner → Generator | `SHOW GRANTS` nachweisen, vier DBs, `TEST_ROLLE` Pflicht, `SELECT DATABASE()`, Kollisionsprobe |
| 12 | Statuswahrheit hinkt (W1-3/W1-4 ABGENOMMEN, STATUS `CODE_FERTIG`) | M | Integrator-Zustandscommits | — | Voten `b9fe55c0`, `36eb8b63` | A-37 | Integrator | nach A-37 je ein belegter Zustandscommit |

| 13 | **Dirigent ohne eigene Rollendatei** — `~/.ticket-steuerung/rollen/dirigent.yaml` fehlt; die Steuerungsrolle unterliegt nicht selbst dem Pull-/ACK-/Digest-Verfahren (Yama 22.08. 08:53) | M | nach A-37: `rollen/dirigent.yaml` (Auftrag = Steuerung, Schreibbereich, Generation, Digest; ACK durch den Dirigenten selbst, Lesbarkeit für alle Rollen) | — | — | A-37 | Dirigent | nach DoR/Bau A-37 anlegen; bis dahin kein Begleitverkehr |
| 14 | **Integrator-Graubereiche** (gemeldet `integrator-selbstpruefung-rolle.yaml`): Ereignisdateien außerhalb eigener Aufträge · Meldungen in fremden Auftragsordnern · Verhalten bei ungültigem Digest | S | README-Präzisierung durch den Dirigenten (Steuerungsregel, keine Kriterien) | — | — | — | Dirigent | nach DoR-Votum beantworten (eine Regel je Punkt: Ereignisse nur im eigenen Auftragsordner, Fremdmeldungen als `hinweis-*` an den Dirigenten, ungültiger Digest = nicht handeln + melden) |

| 15 | **Zentraler Dispatcher statt Wecker je Rolle** (Konzept der unlegitimierten Sitzung 6b369768, von Yama im Kern angenommen): ein `launchd`-überwachter Dispatcher beobachtet `rollen/*.yaml`, prüft Digest, entprellt, Single-Flight je Rolle, weckt nur die registrierte Sitzung; Wecker schreiben nie (kein ACK/Lease/Commit); Unterbrechungsregel ohne alten Commit; `vorrang: nach_abschluss|sofort_unterbrechen` nur vom Dirigenten | M | **Z0-I4** (neu), nach A-37; Planner spezifiziert | — | — | A-37 ABGENOMMEN | Planner | Blatt nach A-37; bis dahin `planner-takt.sh` (Scratchpad, nicht dauerhaft) als Provisorium |
| 16 | **Meldepflicht technisch prüfen** (Yama 22.08.): Start-/Abschlussmeldung je Rolle und Auftrag mit rollenspezifischem Abschlussbegriff; Monitor lehnt falsche Begriffe, fehlende Rolle, veraltete Generation, nicht existierenden SHA ab | M | Z0-I3/Z0-I4 | — | — | A-37 | Planner | Regel gilt ab sofort (README, `docs/regelwerk/MELDEPFLICHT-AUFTRAG.md`); Prüfung im Dispatcher spezifizieren |

**Bereits behoben / als Regel wirksam (nicht mehr offen):** Nachrichtenkanal → Pull-System (soft) · Y-13-Ablage · Digest-Fenster (atomar) · Belegform SHA+Pfad+Anker · Errata-Regel nach DoR · `BASE_BLOCKED`-Regel · Dirigent-Prozess-Stillstand 01:14–07:51 (Ursache offen, beobachten).
