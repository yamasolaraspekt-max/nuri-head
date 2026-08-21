# Z2-W0-12 · Nuriva-Token: Laufzeit 8 h (konfigurierbar), Widerruf, Bereinigung (Y-10 entschieden)

```yaml
zustand: ENTWURF
welle: 0 / Phase 4 (Gesamtauftrag: „Token-Widerruf, Bereinigung und Ablaufzeit ergänzen")
basis_sha: 14dc15f3
herkunft: Messung Sanctum 21.08. (docs/backlog/inventur-2026-08-21-z2-folge.md): expiration null, keine Bereinigung, kein Admin-Widerruf
entscheidung: Y-10 ENTSCHIEDEN 21.08. (Yama): NURIVA_TOKEN_LAUFZEIT_STUNDEN = 8, konfigurierbar, jederzeit rückstellbar
spur: A — Authentifizierung, Bedienfolge Nuriva (erneutes Login nach Ablauf)
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
laeuft: NACH W0-9 (Kontostatus) — W0-9 löscht Tokens beim Deaktivieren; dieser Auftrag ergänzt Ablauf + Bereinigung + Admin-Widerruf
```

## Ziel
1. `config/sanctum.php` `expiration` = `env('NURIVA_TOKEN_LAUFZEIT_STUNDEN', 8) * 60` (Minuten);
   `.env.example` mit Kommentar (Default 8 h; `0`/leer = unbegrenzt wäre der Rückweg, ausdrücklich
   benannt).
2. Scheduler: `sanctum:prune-expired --hours=24` täglich (`app/Console/Kernel.php`).
3. Admin-Widerruf: ein benannter Weg, alle Tokens eines Nutzers serverseitig zu löschen (Artisan-
   Befehl oder Admin-Aktion neben „Deaktivieren" — **erst messen**, was W0-9 schon liefert, nicht doppeln).
4. Bestehender Dauer-Token (ID 8, seit 02.07., ungenutzt): läuft mit Ablauf nicht rückwirkend ab —
   `expires_at` ist NULL; im Baubericht benennen; Widerruf über Punkt 3 (Yama-Freigabe: Löschung eines
   ungenutzten Tokens — hier erteilt durch Y-10 „Widerruf ergänzen").

## Ist-Beleg
`config/sanctum.php:49` `'expiration' => null`; `grep prune-expired app/ routes/` → 0; `Kernel.php:19-35`
5 Kommandos ohne Sanctum; `personal_access_tokens`: 1 Token, `expires_at` NULL; `logout-all`
(`PlannerApiAuthController:137-149`) ist Selbstbedienung.

## Scope · Dateien
`config/sanctum.php`, `.env.example`, `app/Console/Kernel.php`, ggf. Artisan-Command/Admin-Aktion,
Tests: Token nach 8 h + 1 min → 401; innerhalb → 200; Prune entfernt abgelaufene; Widerruf löscht alle
Tokens des Nutzers. **Nur `ticket_testing`.**
**Nicht-Ziele:** keine Änderung am Login-Flow von Nuriva; keine Abilities (W0-6); kein Refresh-Token-
Mechanismus (Bedienentscheidung; falls 8 h in der Praxis stören, ist das Yamas Rückstell-Entscheid).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: `config('sanctum.expiration')` = 480 bei Default; env-Änderung wirkt (Test mit Env-Fake) | Laufzeit | *n.U.* | Testname |
| B: abgelaufener Token → 401; gültiger → 200 | Laufzeit | *n.U.* | Testname |
| C: Scheduler enthält `sanctum:prune-expired` (Rohausgabe `schedule:list`) | Bereinigung | *n.U.* | Rohausgabe |
| D: Widerruf löscht alle Tokens eines Nutzers (DB-Probe vorher/nachher) | Widerruf | *n.U.* | Rohausgabe |
| E: Rückweg dokumentiert (`0` = unbegrenzt) in `.env.example` | Doku | *n.U.* | Zitat |

**P1-Kriterium A ist vor dem Bau wirksam rot** (`expiration` null).

## Rückweg
`NURIVA_TOKEN_LAUFZEIT_STUNDEN=0` (unbegrenzt) in `.env`; Code-Commit zurückdrehbar.
