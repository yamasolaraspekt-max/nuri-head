# Z2-W0-10 · `api/secure/master-sets*`: Secret nie aus der URL, config statt env, Debug nur lokal

```yaml
zustand: ENTWURF
welle: 0 / Phase 4 (Gesamtauftrag 21.08.: „vollständig untersuchen, ggf. P0/P1-Auftrag") — untersucht, P1
basis_sha: ae7cee9d
herkunft: Messung A-8 (docs/backlog/inventur-2026-08-21-z2-folge.md) — Auth vorhanden, Härtung fehlt
spur: A — Geheimnisse, EK-/Margen-/Personaldaten
entscheidung_offen: Y-11 — WER konsumiert diese Schnittstelle? Kein Konsument im Repo. Ohne Konsument: STILLLEGEN statt härten (billigster, sicherster Weg). Bis Yama antwortet: härten (Weg A).
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel (Weg A — härten)
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
