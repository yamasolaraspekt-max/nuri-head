# Sicherheits-Backlog (eskalierte Bestandslücken)

> Eigene, additiv gepflegte Liste. Einträge sind **Bestandsbefunde**, die bewusst **nicht** im
> auslösenden Feature-Ticket gefixt werden (Scope-Disziplin), sondern als eigener Strang.
> Priorität terminiert Yama.

## S-1 · `deal_measurement_items`-Schreibfläche ohne Ownership — **HOCH**

**Befund (M4-a v-c-2, 2026-07-05):** Die gesamte Deal-Measurement-Schreibfläche ist nur durch
`auth` geschützt — **kein Ownership-/Policy-Check**. Jeder authentifizierte Nutzer kann in **jedes
fremde Aufmaß** schreiben.

- `DealMeasurementMaterialController@saveMaterials` (`:15`): Guard = `auth` (Route in
  `Route::middleware(['auth'])`, `routes/web.php:4162`) + `status==='completed'`→423. Kein Ownership.
- `DealMeasurementController@256`/`@551` (echter `deal_measurement_items`-Schreiber): kein
  `__construct`/`middleware`/`authorize`/`Gate`/`Policy` — nur `auth()->user()` für Audit-Spalten.
- Route-Model-Binding (`{measurement}`) akzeptiert jede ID.

**Neu eingeführt (gleiche Bestandslage, bewusst NICHT abweichend):**
`HeizkoerperController@uebernehmen` (`heizkoerper.stueckliste.uebernehmen`) folgt exakt dem
Bestandsmuster (Flag+`auth` + `completed`→423), damit v-c-2 keine Sonderrolle spielt — **erbt
denselben Ownership-Gap**.

**Fix (eigener Strang):** modulweit eine Policy nach **`PlanUploadPolicy`-Muster** (bzw. bestehende
Ownership-/Zuständigkeits-Logik) an **allen drei** Stellen: `saveMaterials` +
`DealMeasurementController`-Schreibpfade + `heizkoerper.stueckliste.uebernehmen`. Prüfen: darf der
Nutzer den zugehörigen Deal bearbeiten (Zuweisung/Rolle/Filiale)?

**Priorität: HOCH** (Yama terminiert). Verwandt: die WP-/Heizlast-Auslegungstabellen
(`heizlast_projekte` u. a.) sind bei ihrer Produktiv-Anbindung auf dieselbe Ownership-Frage zu prüfen.

### Status
- **S-1a ✅ gebaut** (2026-07-05): `DealMeasurementPolicy` (Deal-Zuständigkeit b+: `created_by` ∨ `responsible_employee_id` ∨ `deals.employee_id` ∨ Super-Admin; **Portal-Hart-Deny** ohne Employee-Kontext), registriert in `AuthServiceProvider`. **Enforcement (`Gate::authorize('write', $measurement)`) an allen Item-/Material-Schreibern:** `HeizkoerperController@uebernehmen`, `DealMeasurementMaterialController@saveMaterials`/`@saveDetails`, `DealMeasurementController@updateItem`, `DealMaterialListController@updateMaterialStatus`/`@moveMaterialAllocation`/`@updateOrderDetails`/`@applyFeinaufmassToOfferDetail`. **Auto-Gen (`@index`) bewusst NICHT gegated** (Write-on-read, Backlog). **Waisen:** weiches Deny (erlaubt+geloggt+gezählt via `Cache` `deal_measurement_orphan_write_count`) bis `config('features.deal_measurement_orphan_hard_deny')` (Default OFF). **Backfill:** `php artisan deal-measurements:backfill-owner` (created_by ← `deals.employee_id`), **gebaut+getestet, Prod-Lauf = M5/Deploy**.
  - **UMSCHALT-KRITERIUM (Yama gibt Zeitraum frei):** definierter Zeitraum mit `orphan_write_count == 0` → Mikro-Commit `DEAL_MEASUREMENT_ORPHAN_HARD_DENY=true`.
- **S-1b offen** (eigener Befund+Stopp): `assignWork`, `complete`/`unlock`/`destroy`, `storeNote`, Images — Frage: reicht `write` oder eigene Abilities (`assign`/`complete`/`delete`)?

## S-2 · Write-on-read: `DealMeasurementController@index` (GET erzeugt DB-Zeilen) — mittel
`@index` (@256, via `createMeasurementItemFromRow@544`) **erzeugt `deal_measurement_items` beim Anzeigen** (GET mit DB-Write). Deshalb in S-1a **nicht** gegated (sonst bräche das Ansehen fremder Aufmaße). Eigener Konstruktions-Befund: expliziter Init-Schritt **oder** Idempotenz-Absicherung. **Nicht Teil von S-1.**

## S-3 / M5 · Preis-Spalten `deal_measurement_items` nullable — mit M5
`unit_price`/`purchase_price`/`total_price` sind `decimal default(0)` **non-nullable** → HK-Regel-Kandidaten (kein Preis) landen als **0,00** in der preisführenden Material-Liste = **falsche Zahl, nicht Platzhalter**. **Fix = additive `nullable`-Migration, Bestandteil von M5** (zusammen mit den HK-Produktiv-Migrationen + Flag-Freischaltung). Bis dahin lebt „kein Preis" in `raw_snapshot.preis_bekannt=false`+`note`.
