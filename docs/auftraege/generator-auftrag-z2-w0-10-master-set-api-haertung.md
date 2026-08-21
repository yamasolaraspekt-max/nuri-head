# Z2-W0-10 · `api/secure/master-sets*`: reversible Stilllegung per Schalter (Y-11 entschieden)

```yaml
zustand: ENTWURF
welle: 0 / Phase 4 (Gesamtauftrag 21.08.: „vollständig untersuchen, ggf. P0/P1-Auftrag") — untersucht, P1
basis_sha: 14dc15f3
herkunft: Messung A-8 (docs/backlog/inventur-2026-08-21-z2-folge.md) — Auth vorhanden, Konsument unbekannt
spur: A — Geheimnisse, EK-/Margen-/Personaldaten
entscheidung: Y-11 ENTSCHIEDEN 21.08. (Yama): kein externer Konsument bekannt → REVERSIBLE STILLLEGUNG, keine Löschung; erst deaktivieren und durch Tests absichern, endgültige Entfernung später gesondert.
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel (Weg B — reversibel stilllegen, ENTSCHIEDEN)
Die drei Routen `api/secure/master-sets`, `/{id}`, `/master-sets-debug` sind hinter einem Schalter
`config('services.master_set_api.aktiv')` (env `MASTER_SET_API_AKTIV`, **Default false**) —
inaktiv → **404** für alle drei (keine Existenzpreisgabe), unabhängig von Credentials. Code und
Controller bleiben erhalten (Rückweg: Schalter true). **Keine Löschung** (Rückfall-Regel; endgültige
Entfernung ist ein eigener, späterer Posten nach Yamas gesonderter Freigabe).
**Falls jemals reaktiviert (Schalter true), gelten die Härtungspunkte aus Weg A als Pflicht** — sie
stehen unten erhalten und sind in diesem Auftrag **Nicht-Ziel**.

## Nachvollzugs-Matrix Weg B (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Schalter false (Default): alle drei Routen → 404, auch mit gültigen Header-Credentials | Stilllegung | *n.U.* | Testnamen |
| B: Schalter true: Verhalten wie heute (anonym 401, Header 200) — Rückweg belegt | Rückweg | *n.U.* | Testnamen |
| C: `config/services.php` trägt `master_set_api.aktiv` mit Kommentar + Verweis auf Y-11; `.env.example` `MASTER_SET_API_AKTIV=false` | Doku | *n.U.* | Zitat |
| D: Kein Code gelöscht (`git diff --numstat`: Controller nur additiv/Guard) | Grenze | *n.U.* | Rohausgabe |
| E: Fehlbefund #116 in `docs/software-audit/fix-ledger.md:122` und `product-data/01-repository-inventory.md:430,558` als falscher Alarm berichtigt (Auth war vorhanden) | Doku | *n.U.* | Zitat |

**P1-Kriterium A ist vor dem Bau wirksam rot** (heute: anonym 401, mit Credentials 200 — erreichbar).

## Weg A — Härtung (erhalten für den Fall der Reaktivierung, hier NICHT-ZIEL)
1. Credentials nur aus Header/HTTP-Basic — die `?: $request->input('api_user'/'api_password')`-Zweige
   (`MasterSetApiController:28-34`) entfallen (Geheimnis nie in URL/Log).
2. `config/services.php` `master_set_api` (user/password aus env) nach Muster `fusion_forms` (`:34-36`);
   Controller liest `config()` (`config:cache`-Falle behoben).
3. Debug-Endpunkt nur `local`/`testing` (`abort_unless(app()->environment(['local','testing']), 404)`
   oder Route geklammert); Exception-Text (`:391-392`) auf `config('app.debug')` gaten wie `index/show`.
4. `Log::warning` im 401-Zweig; `throttle:10,1` auf der Auth-Fläche.
**Weg B (nach Y-11 „kein Konsument"):** die drei Routen entfernen bzw. Controller stilllegen — eigener
Mini-Auftrag, Rückfall-Regel (kein Löschen ohne Freigabe → Yamas Y-11-Antwort ist die Freigabe).

## Ist-Beleg
`MasterSetApiController.php:26-79` `authApi()`, `hash_equals` (:64-65), `env()` (:36-37), Query-Zweige
(:28-34); Debug `:303-395`, Exception ungegated `:388-393`; Payload `loadComponents:649ff`
(`purchase_price`, `margin`, `skonto`, `distributor_prices`), `loadLabor:932-1041` (`hourly_rate`,
Klarnamen, Foto-URL). Live: anonym 401, Query-Pfad ausgewertet. `grep -rn "secure/master-sets\|X-API-USER"`
→ nur Controller/Route/Doku; `nuriva-sync-anbindung-befund.md:44-55` ordnet keinen Konsumenten zu.

## Scope · Dateien
`MasterSetApiController.php`, `config/services.php`, `routes/api.php:197-220`, `.env.example`;
Test `tests/Feature/Api/MasterSetApiAuthTest.php`: Query-Credentials → 401; Header → 200; anonym 401;
Debug in `testing` 200 / Produktionssimulation 404 (Env-Fake). **Nur `ticket_testing`.**
**Nicht-Ziele:** keine Änderung der Payload-Struktur (bis Y-11 entschieden ist, ob die Schnittstelle
lebt); kein neues Secret-Format; `with_deleted` bleibt (post-auth, Rechtebezug = Folgefrage).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Query-String-Credentials → 401 (Test); Header-Credentials → 200 | Secret | *n.U.* | Testnamen |
| B: `grep -n "env('MASTER_SET_API" app/` → 0; `config('services.master_set_api.*)` vorhanden | config | *n.U.* | grep |
| C: Debug-Route außerhalb local/testing → 404 (Env-Fake im Test) | Debug | *n.U.* | Testname |
| D: Exception-Text nur bei `app.debug` (Zitat) | Gate | *n.U.* | Zitat |
| E: 401-Versuche erscheinen im Log (Test mit Log-Fake) | Protokoll | *n.U.* | Testname |

**P1-Kriterium A ist vor dem Bau wirksam rot** (live: Query-Pfad wird ausgewertet).

## Rückweg
Ein Commit, zurückdrehbar; keine Daten. Entdeckung: ein unbekannter Konsument, der Query-Credentials
nutzt, bekäme 401 — das wäre zugleich die Antwort auf Y-11.
