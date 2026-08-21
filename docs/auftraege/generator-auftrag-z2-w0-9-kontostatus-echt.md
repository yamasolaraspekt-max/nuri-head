# Z2-W0-9 · „Deaktiviert" deaktiviert: echter Kontostatus statt Online-Flag

```yaml
zustand: ENTWURF
welle: 0 (Authentifizierung, LIVE) — gilt UNABHÄNGIG von der Rechte-Entscheidung vom 21.08.
basis_sha: 114b98f6
herkunft: Befund A-7 präzisiert (docs/backlog/inventur-2026-08-21-z2-folge.md, Messung 21.08.)
spur: A — Authentifizierung, Schema (additiv), LIVE-Nutzer
entscheidung_dirigent: die Oberfläche verspricht „Deactivated" — das Versprechen wird wahr gemacht; KEIN neues Verhalten erfunden, das alte eingelöst. Yama kann widersprechen (Vollmacht, Anhör-Pflicht) — dokumentiert hier.
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: security-reviewer (Meldung)
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
Ein deaktivierter Nutzer kann sich **nicht** anmelden (Web + Planner-API-Token), eine laufende
Session endet, bestehende Tokens werden widerrufen; der Admin-Knopf „Abmelden erzwingen" wirkt.
`is_active` bleibt unangetastet als Online-Flag (kein Bestandsverhalten kippt).

## Ist-Beleg (Messung 21.08.)
`is_active` wird von `LogUserLogin.php:26` (=1) / `LogUserLogout.php:16` (=0) gesetzt; geprüft nur in
`MobileAuthController:68-73` (Nebenwirkung: Web-Logout sperrt Mobile-Login). `LoginController` = Trait
ohne `credentials()`; web-Gruppe (`Kernel.php:35-43`) ohne Statusprüfung; `PlannerApiAuthController:50-59`
ohne Statusprüfung. `logOffUser` (`UserController:449-455`): falscher Session-Schlüssel + keine
`sessions`-Tabelle → wirkungslos. DB: alle 52 Nutzer `is_active=1`.
**Reproduktion:** Admin deaktiviert X (UI „Deactivated") → X loggt sich ein → 200, Flag wieder 1.

## Scope · Dateien
- **Schema additiv:** Migration `users.disabled_at` (nullable timestamp). Keine Änderung an `is_active`.
- `LoginController::credentials()` überschreiben: `['email','password','disabled_at' => null]` (Trait-Muster).
- Middleware `EnsureUserNotDisabled` in der `web`-Gruppe UND als Schutz für `auth:sanctum`-Routen:
  `disabled_at` gesetzt → Logout + 403/Redirect; Planner-Token-Ausgabe prüft `disabled_at`; beim
  Deaktivieren werden die `personal_access_tokens` des Nutzers gelöscht (Admin-Widerruf).
- Schreibpfade `deactive`/`adminUsersToggleActive`/`logOffUser` auf `disabled_at` umhängen; `logOffUser`
  repariert oder — wenn ohne DB-Sessions nicht leistbar — ehrlich als „setzt Deaktivierung, Session
  endet beim nächsten Request" beschriftet (Middleware macht genau das).
- UI-Texte „Active/Deactivated" lesen `disabled_at`, nicht `is_active`.
- `MobileAuthController:68` prüft `disabled_at` statt `is_active` (beseitigt die Fehlsperre nach Web-Logout).
- Tests `tests/Feature/Security/KontostatusTest.php`: deaktiviert → Web-Login 4xx, Token-Endpunkt 4xx,
  laufende Session beim nächsten Request beendet, Tokens weg; aktiv → alles wie bisher;
  Web-Logout sperrt Mobile-Login NICHT mehr. **Nur `ticket_testing`.**
**Nicht-Ziele:** keine Rechte-Logik (W0-7); keine Löschung von Nutzern; kein Umbau des
Session-Treibers; keine Änderung an `is_active`-Semantik.

## Kanten
- Bestandsdaten: `disabled_at` startet NULL für alle — niemand wird durch die Migration gesperrt
  (Kriterium D). — Rückweg: Middleware per Config abschaltbar? **Nein** — eine Auth-Sperre, die man
  abschaltet, ist keine; Rückweg ist der Commit. — Admin darf sich nicht selbst aussperren: Schreibpfad
  verweigert Deaktivierung des eigenen Kontos (messen, ob das schon so ist).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: deaktiviert → Web-Login abgelehnt | credentials | *n.U.* | Testname |
| B: deaktiviert → `POST api/planner/auth/token` abgelehnt; vorhandene Tokens gelöscht | Token | *n.U.* | Testname + DB-Probe |
| C: laufende Session endet beim nächsten Request nach Deaktivierung | Middleware | *n.U.* | Testname |
| D: Migration setzt niemanden auf deaktiviert (`SELECT count(*) WHERE disabled_at IS NOT NULL` = 0 nach Migration) | Schutz | *n.U.* | Rohausgabe |
| E: Web-Logout sperrt Mobile-Login nicht mehr | Korrektur | *n.U.* | Testname |
| F: UI zeigt „Deactivated" genau dann, wenn `disabled_at` gesetzt | UI | *n.U.* | Browserabnahme |

**P1-Kriterium A ist vor dem Bau wirksam rot** (Reproduktion 200).

## Rückweg
Migration additiv (Spalte bleibt, schadet nicht); Code-Commit zurückdrehbar; keine Datenänderung
außer gelöschten Tokens deaktivierter Nutzer (gewollt).
