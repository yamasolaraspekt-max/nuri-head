# RELEASE-MANIFEST — Stufe 3b P0 (Auflage B): Optimistische Nebenläufigkeit der Profilversionskette

**Stand:** 2026-07-14 · **lokal, kein Commit, kein Push, keine Migration.** Commit-Freigabe = echter Yama nach unabhängigem Evaluator.
**Git-Ausgangsstand:** `f2d9a33` (G0c-2). **Abschlussformel:** *Umgesetzt, unabhängig geprüft — bereit zur Commit-Freigabe.*
**Begriffs-/Scope-Vermerk:** E9/Teil 2 durch PS-Entscheidungen neu geschnitten; 3b-0 = P0 + Dienste + Verdrahtung. **Dies ist P0.**

---

## 1. Umgesetzter Scope (P0, [MUSS, code-only])
`AnforderungsprofilService::neueVersion` gegen Lost-Update gehärtet — **optimistische Nebenläufigkeits-Kontrolle**
der LINEAREN Versionskette gemäß Yama/Planner-Zielverhalten:
- Innerhalb der Transaktion werden die Zeilen der Verankerung `lockForUpdate()` gesperrt; der Ketten-Kopf
  (`max(version)`) wird **frisch unter Sperre** gelesen (nicht aus dem übergebenen `$basis`).
- **Ist `$basis->version < max(version)` → `StaleProfilVersionException`** (Konflikt-Ablehnung). Es wird
  **keine** Folgeversion aus veralteten Daten erzeugt; der erste erfolgreiche Save bleibt vollständig erhalten.
- Ist die Basis der Kopf → neue Version `= max+1`, Kopierquelle = frisch gesperrte Kopf-Zeile.

**Korrektur gegenüber erstem P0-Entwurf:** Der erste Entwurf hängte bei stale Basis automatisch eine v3 an
(max+1). Das ist per Yama/Planner-Entscheidung „ohne freigegebene Rebase-/Branching-Regel **nicht zulässig**"
→ umgestellt auf Konflikt-Ablehnung. Der zugehörige Test wurde von „erwartet v3" auf „erwartet Ablehnung" gedreht.

## 2. Geänderte/neue Dateien (path-scoped, getrennt von P1)
- **Geändert:** `app/Services/Anforderungsprofil/AnforderungsprofilService.php` (nur `neueVersion`; +42/−10).
- **Neu:** `app/Services/Anforderungsprofil/StaleProfilVersionException.php`.
- **Geändert (Test):** `tests/Feature/Anforderungsprofil/AnforderungsprofilTest.php`
  (`test_neue_version_auf_stale_basis_wird_als_konflikt_abgelehnt`, `test_neue_version_auf_aktuellem_kopf_bildet_lineare_kette`).
- **`GrundrissController.php` NICHT re-touchiert** (bleibt bei `f2d9a33`). Kein P1-Beifang.

## 3. Datenbank / Migration
**Keine.** Reine Code-Härtung. (Ein Unique-Index als robuster Backstop ist als Folgeposten vermerkt — s. §6 M-1.)

## 4. Tests — Befehle & Rohresultate
```
php artisan test tests/Feature/Anforderungsprofil/AnforderungsprofilTest.php  → 11 passed (32 assertions)
php artisan test tests/Feature/Geometrie/GrundrissProfilPersistenzTest.php    → 5 passed (22)  (G0c-2-Regression)
php artisan test tests/Feature/Heizlast/AnforderungsprofilHeizlastAdapterTest.php → 8 passed  (Adapter-Regression)
php artisan test  (volle Suite)                                                → 608 passed, 1 failed
```
Einziger Rotfall: `InvoiceDeletionGuardTest` (Reverb `localhost:6001`, BroadcastException) — E4-Vorbestand,
P0-unabhängig (Diff berührt nichts an Invoice/Broadcasting).

## 5. Unabhängiger Evaluator (getrennte Instanz, read-only, eigenes Nachmessen) — **FREIGABE-EMPFEHLUNG**
Alle 9 Pflicht-Prüfpunkte erfüllt. Kernbelege:
- **Zwei-Verbindungs-MySQL-Gegenprobe (Punkt 8):** eigenes PDO-Skript, zwei getrennte Verbindungen gegen
  `ticket_testing` → Verbindung B **blockiert real** am `FOR UPDATE` (Lock-Wait-Timeout ~10,5 s, solange A hält),
  liest nach A-Commit `max(version)=2` → `basis(1) < 2` → Stale erkannt. Empirisch widerlegt, dass eine sperrende
  Zweitlesung unter REPEATABLE READ einen alten Snapshot sähe. `ticket_testing` rückstandsfrei, Dev-DB `ticket` unberührt.
- **Punkt 9:** E4-Reverb am unveränderten Stand reproduzierbar, kausal P0-unabhängig (Diff nur Service/Exception/Test).
- Stale-Ablehnung greift **vor** jeder Kopie (nie alte Daten geschrieben); Ein-aktiv-Invariante hält im
  Controller-Pfad (`neueVersion` erzeugt entwurf → `aktivieren` atomar in derselben `speichern`-Transaktion).

## 6. Offene Punkte (Evaluator-Befund, außerhalb P0-Scope → Folgeposten)
- **M-1 (mittel):** `anlegen` (Version 1) ist ungehärtet und es fehlt ein Unique-Index auf
  `(verankerbar_type, verankerbar_id, version)`. Zwei gleichzeitige **Erst**-Saves auf ein frisches Objekt könnten
  zwei `version=1`-Zeilen erzeugen (kein DB-Backstop). **Pre-existing**, durch P0 nicht verschlechtert. Empfehlung:
  Folgeposten „partieller/echter Unique-Index + `anlegen`-Lock" — das ist der in 3b-Startblock Scope 5 vorgesehene
  **[KANN]-Index mit additiver Migration (Pflicht-Stopp, echte Yama-Freigabe)**; sichert zugleich jeden Rest-Race in `neueVersion`.
- **M-2 (niedrig):** `StaleProfilVersionException` wird im Controller vom generischen `catch (Throwable)` als **422**
  beantwortet, nicht als **409/Conflict** mit eigenem Code. Funktional sicher (kein stilles v3, definierte Antwort),
  aber der Client kann Stale-Konflikt nicht separat behandeln. UX/Contract-Feinschliff, eigener kleiner Posten.

## 7. Bestätigungen
- ✅ Zielverhalten erfüllt (stale → Ablehnung, kein Auto-v3), deterministisch **und** nebenläufig belegt.
- ✅ Kein Commit/Push/Migration; P0-Diff getrennt von P1; `GrundrissController` nicht re-touchiert.
- ✅ Genau ein Rotfall (E4-Reverb), P0-unabhängig.
- ✅ Kein `git add -A`, kein CLAUDE.md. HEAD `f2d9a33` unverändert.

## 8. Ballbesitz / nächster Schritt
- **Yama:** Commit-Freigabe P0 (path-scoped: Service + Exception + Test) + Entscheidung zu M-1/M-2 (eigener Folgeposten).
- **Danach:** P1a (Charakterisierungstests) — startet laut Entscheidung **erst nach grüner P0-Freigabe**.
- **STOPP vor Commit. Kein Push.**
