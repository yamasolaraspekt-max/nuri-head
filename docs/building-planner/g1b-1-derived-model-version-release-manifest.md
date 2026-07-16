# RELEASE-MANIFEST — G1b-1 (derived-only `building_model_versions` + A-3-Öffnungshöhen)

**Welle:** G1b-1 · **Heimat-App:** `ticket` · **Stand:** 2026-07-16
**Isolierter Branch:** `work/g1b-0-persistence-decision` · **Worktree:** `../ticket-g1b-0` · **Basiscommit:** `0112794` (G1b-0-Docs)
**Status:** UMGESETZT (Generator) — nicht „grün". Abnahme durch technischen Evaluator + CAD-Nachprüfung + Yama. **Kein Commit des G1b-1-Slices, kein Push.**

> **Verbindlich:** `anforderungsprofile.gebaeude_geometrie` bleibt der **einzige fachliche Writer**. `building_model_versions` ist eine **unveränderliche, abgeleitete Projektion (derived_only)** — kein Zweit-Writer, kein Mapper, kein Backfill, kein Reader-Cutover, kein Dual-Write.

## 1. Geänderte/erzeugte Dateien (Slice)
**Produktivcode (neu):**
- `database/migrations/2026_07_16_120000_create_building_model_versions_table.php` (additiv, nur diese Tabelle)
- `app/Models/BuildingModelVersion.php` (immutable)
- `app/Services/BuildingModel/DerivedBuildingModelVersionStore.php`
- `app/Services/BuildingModel/SourceGeometryRef.php` · `CanonicalHash.php` · `ProjectionConflictException.php` · `CanonicalModelInvalidException.php` · `BuildingModelVersionImmutableException.php`

**Produktivcode (geändert, nur A-3):**
- `app/Services/BuildingModel/CanonicalBuildingModelValidator.php` — Sturzkonsistenz `opening.lintel_conflict`, `height_mode=profile`-Behandlung (`opening.profile_height_unresolved`, harte Unterkante), erweiterte mm-Integer-Prüfung für `lintel_height_mm`/`finished_*`.

**Tests (neu):**
- `tests/Unit/BuildingModel/OpeningHeightContractTest.php` (A-3, 6 Tests)
- `tests/Feature/BuildingModel/BuildingModelVersionStoreTest.php` · `BuildingModelVersionImmutabilityTest.php` (RefreshDatabase/MySQL)

**Nicht Teil des Deliverables (Worktree-lokale Test-Infra, uncommittet, keine Secrets, gitignored/nicht gestaged):** `phpunit.g1b1.xml` (MySQL-Runner → `ticket_g1b1_testing`, Credentials NUR aus `.env`), `vendor/` (composer install), `.env` (aus Haupt-Projekt kopiert, gitignored), `public/build/` (aus Haupt-Worktree kopiert für `@vite`-Views). **`phpunit.g1b1.xml` bleibt untracked und wird nicht committet** — die gelieferten Tests laufen mit Projekt-`phpunit.xml` bzw. sicheren Env-Overrides; das lokale XML war nur nötig, um die Test-DB auf `ticket_g1b1_testing` zu isolieren (Projekt-`phpunit.xml` erzwingt `ticket_testing`).

## 2. Vertrag
- **Source-Identität:** `source_type='gebaeude_geometrie'` + `source_profile_id` (anforderungsprofile.id) + `source_version` (anforderungsprofile.version) + `source_hash` (schlüssel-normalisierter Hash der Quellgeometrie).
- **Idempotenzschlüssel (Unique, letzte Schutzlinie):** `(source_profile_id, source_version, schema_version, projection_version)` → `bmv_source_schema_projection_unique`. Gleicher Schlüssel + gleicher `payload_hash` → **dieselbe Zeile** (idempotent); gleicher Schlüssel + abweichender Hash → **`ProjectionConflictException`** (keine Überschreibung, kein Duplikat).
- **PK:** surrogate `id` (Store-generierte UUID), entkoppelt von Payload-Inhalt; `revision_id` als eigene Spalte (aus Payload).
- **Immutability (Applikationsebene, offengelegt):** `updating`/`deleting` werfen `BuildingModelVersionImmutableException`; kein `updated_at`, kein Soft-Delete. **Keine** DB-Trigger (Migration portabel). *Hinweis:* Query-Builder-`update()` würde die Model-Events umgehen — bewusst app-seitig, kein Trigger.
- **Rolle:** `projection_role = derived_only` (Default + Store-fixiert). Projektion ≠ Nutzerfreigabe; `scan_proposal`/`external_provider` werden nicht automatisch `confirmed`.

## 3. A-3 Öffnungshöhen (geschlossen)
- Datum OKFF; `rough_top = sill_height_mm + rough_height_mm`; `lintel_height_mm == rough_top` sonst `opening.lintel_conflict` (Fehler, keine stille Priorisierung).
- Rohbau führt; `finished_* ≤ rough_*` sonst `opening.finished_exceeds_rough`.
- Vertikal (uniform): `sill ≥ bottom_offset` und `sill+rough_height ≤ bottom_offset+height_mm`.
- `height_mode=profile`: **untere** Grenze (`sill < bottom_offset`) wird hart geprüft; **obere** Grenze ist ohne lokale Wandhöhe nicht entscheidbar → `opening.profile_height_unresolved` (Warnung), keine Annahme einer uniformen Höhe.
- **Konsumenten-Auflage (A-3-CAD-2):** `validation_status='valid'` bei Profilwänden bedeutet **nicht** vollständig geprüfte Vertikalgeometrie — die `warnings` müssen an der Oberfläche sichtbar bleiben.

## 4. Testergebnisse

### 4a. MySQL-Abnahme (isolierte DB `ticket_g1b1_testing`, MySQL 9.7.1, Port 3307, `ticket_user@localhost`)
Isolationsnachweis vor jeder Migration: `SELECT DATABASE() = ticket_g1b1_testing` erzwungen.
- **Migration:** `migrate:fresh` grün (volle Kette inkl. `2026_07_16_120000_create_building_model_versions_table`). Schema: Tabelle da, `payload` nativer `json`, **kein `updated_at`/`deleted_at`**, Unique `bmv_source_schema_projection_unique` (4 Spalten), Indizes `revision_id`/`model_id`/`object_id`. **up/down/up:** `down()` entfernt **nur** `building_model_versions` (bmv 1→0, `anforderungsprofile`/`raum_geometrien` bleiben), `up` stellt wieder her.
- **Fokussierte Tests (Unit+Feature BuildingModel):** **56/56, 124 Assertions.**
- **Gegenbeweis #8 (Source-Unveränderlichkeit):** Schutztest 6/6 grün (Source-Zeile hash-identisch nach gültiger/idempotenter/konflikt-/ungültiger Projektion); rote Mutation (Store fasst `gebaeude_geometrie` an) → Schutztest ROT (exit 1); Store md5-identisch restauriert; danach grün.
- **Concurrency:** Unique-Constraint als DB-Schutzlinie bewiesen (Duplikat-Insert → **MySQL errno 1062**, ROWCOUNT bleibt 1). Sequenzielle Idempotenz/Konflikt über die grünen Feature-Tests. **Grenze:** echter 2-Verbindungs-Paralleltest nicht ausgeführt → keine vollständige Race-Sicherheit über die Constraint hinaus behauptet.
- **Regression (Geometrie/Heizlast/G0c-2, Unit+Feature):** **90/90, 320 Assertions.**
- **Volle MySQL-Suite:** **692 Tests, 686 passed, 1 Error + 5 Failures.**
  - **Baseline-Vergleich (§10):** dieselbe Suite **auf Basis 0112794 ohne G1b-1** = 676 Tests, **1 Error + 5 Failures = identische Signatur**. G1b-1 fügt **+16 grüne Tests** hinzu und verursacht **0 neue Fehler**.
  - Die 6 vorbestehenden Issues sind **nicht G1b-1**: 1× `InvoiceDeletionGuardTest` (E4-Reverb-BroadcastException, Pusher cURL 7 → localhost:6001, dokumentierte Baseline) + 5× `OmdClientTest` (Lieferanten-OMD-Client). Die OMD-Failures sind **umgebungsbedingt** (Verschlüsselungsumgebung / App-Schlüssel: gespeicherte Username/Kundennummer entschlüsseln in dieser frischen isolierten Umgebung zu `''`), **nicht** G1b-1: `git diff 0112794` auf Invoice/Suppliers/Models ist **leer** — G1b-1 fasst diese Domänen nicht an. Die Haupt-Worktree-Baseline (675/1) weicht ab, weil dort die reale Verschlüsselungs-/Seed-Umgebung vorhanden ist. *(Korrektur ggü. einer früheren Fassung: Ursache ist die Verschlüsselungsumgebung, nicht die Test-Reihenfolge — vom Re-Evaluator nachgemessen.)*

### 4b. Voraus-Verifikation (SQLite-in-memory, Vorlauf)
- Unit BuildingModel 46/46; DB-Smoke (echte Klassen) 15/15; Gegenbeweise §14 8/8 ROT + md5-Restore (Validator/Store/Model/Migration). Diente der schnellen Vorprüfung ohne DB-Zugang.

## 5. Prüfung (getrennte Instanzen)
- **Technischer Evaluator: GRÜN_MIT_AUFLAGEN** — 15 Punkte belegt. Auflage A (MUSS, **erledigt**): Feature-Test-Assertion `id`→`revision_id`. Auflage B (**erledigt**): `lintel_height_mm`/`finished_*` in mm-Integer-Prüfung. Details `g1b-1-technical-evaluation.md`.
- **CAD-Nachprüfung: FACHLICH_GRÜN_MIT_AUFLAGEN** — 7 Fragen JA. A-3-CAD-1 (**erledigt**): Profil-Unterkante hart geprüft. A-3-CAD-2 (Konsumenten-Nutzungsregel, dokumentiert). Details `g1b-1-cad-evaluation.md`.

## 6. Git-Status
- **Isolierter Worktree:** Basis `0112794`; G1b-1-Artefakte **untracked** (kein Commit). `git status` zeigt nur BuildingModel-/Migrations-/Test-Dateien + Validator-Mod + `phpunit.g1b1.xml` (Infra).
- **Haupt-Worktree** (`/Users/yamanuri/Documents/ticket`): unabhängig, von G1b-1 **nicht berührt**.

## 7. Bestätigungen
Haupt-Worktree unverändert · `gebaeude_geometrie` unverändert · kein fachlicher Zweitwriter · kein Mapper · kein Backfill · kein Reader-Cutover · kein Controller/Route/View/Job/Listener/Observer/Scheduler · keine Bestandstabelle geändert · kein Commit des G1b-1-Slices · kein Push.
