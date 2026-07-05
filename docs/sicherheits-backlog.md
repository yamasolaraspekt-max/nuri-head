# Sicherheits-Backlog (eskalierte Bestandslücken)

> Eigene, additiv gepflegte Liste. Einträge sind **Bestandsbefunde**, die bewusst **nicht** im
> auslösenden Feature-Ticket gefixt werden (Scope-Disziplin), sondern als eigener Strang.
> Priorität terminiert Yama.
>
> **Umbenannt 2026-07-05:** die früheren „S-1/S-2/S-3"-Punkte heißen jetzt **SEC-DM** (Deal-Measurement-Security)
> — behebt die Namens-Kollision mit dem **S1-Rechnungsschiene**-Strang (STRAENGE.md). „S1" bleibt exklusiv der Rechnungsschiene.

## SEC-DM · `deal_measurement_items`-Schreibfläche ohne Ownership — **HOCH**

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
- **SEC-DM-a ✅ gebaut** (2026-07-05): `DealMeasurementPolicy` (Deal-Zuständigkeit b+: `created_by` ∨ `responsible_employee_id` ∨ `deals.employee_id` ∨ Super-Admin; **Portal-Hart-Deny** ohne Employee-Kontext), registriert in `AuthServiceProvider`. **Enforcement (`Gate::authorize('write', $measurement)`) an allen Item-/Material-Schreibern:** `HeizkoerperController@uebernehmen`, `DealMeasurementMaterialController@saveMaterials`/`@saveDetails`, `DealMeasurementController@updateItem`, `DealMaterialListController@updateMaterialStatus`/`@moveMaterialAllocation`/`@updateOrderDetails`/`@applyFeinaufmassToOfferDetail`. **Auto-Gen (`@index`) bewusst NICHT gegated** (Write-on-read, Backlog). **Waisen:** weiches Deny (erlaubt+geloggt+gezählt via `Cache` `deal_measurement_orphan_write_count`) bis `config('features.deal_measurement_orphan_hard_deny')` (Default OFF). **Backfill:** `php artisan deal-measurements:backfill-owner` (created_by ← `deals.employee_id`), **gebaut+getestet, Prod-Lauf = M5/Deploy**.
  - **UMSCHALT-KRITERIUM (Yama gibt Zeitraum frei):** definierter Zeitraum mit `orphan_write_count == 0` → Mikro-Commit `DEAL_MEASUREMENT_ORPHAN_HARD_DENY=true`.
- **SEC-DM-b-1 ✅ gebaut** (2026-07-05): **W-0 Deal-Anker-Fallback** in `DealMaterialListController::authorizeMeasurementWrite` — kein Aufmaß → Deal via `deals.offer_id` → Gate `write-deal-measurement-offer` (`deal.employee_id` + Super-Admin; `deals` hat kein `created_by`); **kein Deal → deny + Log `offer_orphan_write` + Zähler `offer_orphan_write_count`**. Damit ist die **Offer-Ebene** der 4 Material-Writer IMMER gegated (Restlücke geschlossen). Zusätzlich `Gate::authorize('write')` auf `DealMeasurementController@storeNote` + `DealMeasurementImageController@upload`.
  - **⚑ Weiche (offen): `DealMeasurementImageController@destroy(Image $image)`** — `Image` trägt **keinen** Measurement-/Deal-Link (nur `customer_id`/`alternative_id`/`article_group`). Nicht measurement-gatebar; braucht eigene Ownership-Entscheidung (Bild-/Kunden-Scope) → **nicht in SEC-DM-b-1 gebaut**, eigener Punkt.
- **SEC-DM-b-2 ✅ gebaut** (2026-07-05): enge Abilities in `DealMeasurementPolicy` — `assign` (Deal-Zuständiger ∨ `created_by` ∨ Admin; **nicht** der zugewiesene Techniker) · `unlock` (engster Kreis: Deal-Zuständiger ∨ Admin) · `delete` (Ersteller ∨ Deal-Zuständiger ∨ Admin, **soft**-Delete unverändert). `complete` bleibt `write` (Asymmetrie gewollt). **Enforcement:** `assignWork`/`unlock`/`destroy`/`complete` + `DealMeasurementImageController@destroy` (`delete-measurement-image`, Kunden-Anker b+-Kette: Uploader `image.created_by` ∨ Deal-Zuständiger des Kunden ∨ write-Beteiligter auf einem Aufmaß des Kunden ∨ Admin). **`unlock` nicht-completed: 200-No-op → 409** (Verhaltensänderung). **Übergangs-Soft-Deny je Ability** (breiterer write-Kreis erlaubt+geloggt bis Hart-Flag, Log `deal_measurement_ability_soft_deny`) mit Zählern `assign_denied_count` · `unlock_denied_count` · `delete_denied_count` (Image hart: `image_delete_denied_count`, nur Observability). Flags `deal_measurement_{assign,unlock,delete}_hard_deny` (Default OFF).
  - **Image-Anker breiter (Backlog):** `Image` ohne `deal_measurement_id` — Kunden-Anker bewusst breiter als Measurement-Anker; präzisere Bindung nur via additive Spalte (offene Weiche bei Bedarf).
  - **Umschalt-Zeiträume** (Waisen + 3 Ability-Flags) → Teil des **M5-Deploy-Gates** (Yama terminiert).

**SEC-DM gesamt (Deal-Measurement-Ownership) ✅ funktional komplett** (SEC-DM-a write + SEC-DM-b-1 W-0/Notizen/Bilder + SEC-DM-b-2 Abilities). Offen: Umschalt auf hartes Deny (siehe Beobachtungsfenster), Image-Präzisions-Weiche, `@index`-Write-on-read (SEC-DM-2).

### 🚀 M5-Deploy auf main — ausgeführt 2026-07-05 (Sonntag-Randzeit)
- **DB-Backup:** `~/ticket-backups/ticket-pre-M5-2026-07-05.sql` (412/412 Tabellen).
- **Migrationen [Batch 15]:** HK `140001–140009` + Katalog `150005/150006` + SEC-DM-3 `160000` — Spec `150007–150009` **ausgeschlossen** (Strang B, nicht abgenommen → M5.1). Rollback: `migrate:rollback --batch=15`.
- **Seeder:** `WberechnungImportSeeder` → `products.imported_from='wberechnung'=24` (19 WP + 5 PV). **Backfill:** 0 (0 Measurements auf main).
- **Flag:** `HEIZKOERPER_MODULE_ENABLED=true` (config verifiziert ON).
- **Smoke:** 663-Durchstich=662,8 · 7 HK-Tabellen · Policy registriert · SEC-DM-3 nullable=YES · PlannerApiContractTest 14/14.
- **⏳ 14-Tage-Beobachtungsfenster: 2026-07-05 → 2026-07-19.** Kriterium: alle 5 Zähler (`deal_measurement_orphan_write_count`, `offer_orphan_write_count`, `assign_denied_count`, `unlock_denied_count`, `delete_denied_count`) **durchgängig 0** → dann je Flag ein Hart-Deny-Mikro-Commit (`DEAL_MEASUREMENT_*_HARD_DENY=true`). **Zähler > 0 → STOPP + Befund (wer/warum), keine Härtung.**
- **Nicht-Posten / Folge:** HK-Katalog leer (`RadiatorSpecSeeder` = Katalog-Strang, nicht in diesem Fenster) → Konfigurator erreichbar, aber ohne Katalog-Specs bis dahin · **M4-b Sidebar-Menüpunkt** (HK sonst nur per URL) · Spec-Migrationen **M5.1** · `image_delete_denied_count` (hart, nur Observability).

## SEC-DM-2 · Write-on-read: `DealMeasurementController@index` (GET erzeugt DB-Zeilen) — mittel
`@index` (@256, via `createMeasurementItemFromRow@544`) **erzeugt `deal_measurement_items` beim Anzeigen** (GET mit DB-Write). Deshalb in SEC-DM-a **nicht** gegated (sonst bräche das Ansehen fremder Aufmaße). Eigener Konstruktions-Befund: expliziter Init-Schritt **oder** Idempotenz-Absicherung. **Nicht Teil von SEC-DM.**

## SEC-DM-3 · Preis-Spalten `deal_measurement_items` nullable — ✅ testing (main = M5)
**Gebaut** (2026-07-05, testing): Migration `2026_07_05_160000` macht `unit_price`/`purchase_price`/`total_price` nullable (up/down, down nur waisenfrei sicher). `HeizkoerperController@uebernehmen` schreibt HK-Preise ehrlich **NULL** statt 0,00. **Konsequenz-Check belegt:** einziger `deal_measurement_items`-Preis-Leser `DealMaterialListController::mapDealMeasurementItemToMaterialRow:658-659` ist **NULL-sicher** (`?? 0`), bricht nichts; `DealController@780` summiert andere Struktur (null-sicher). **main-Lauf = M5.**
- **⚑ Display-Weiche (offen):** der Mapper **koerziert `null → 0`** (`(float)($item->unit_price ?? … ?? 0)`) → die Material-Liste zeigt weiterhin **0,00** statt „—". Ehrliche **Speicherung** ist erreicht (NULL in DB), die **Anzeige** braucht einen separaten Mini-Fix (Mapper null-erhaltend + Frontend „—"). Bewusst NICHT still gefixt (betrifft alle Material-Zeilen, nicht nur HK). Kandidat für M5-Frontend-Mikroposten (neben unlock-409).
