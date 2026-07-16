# G1b-1 — Technische Evaluation (unabhängig, read-only)

**Welle:** G1b-1 · **Basis:** `0112794` · **Stand:** 2026-07-16 · **Instanz:** getrennt vom Generator und von der CAD-Nachprüfung.
**Urteil: GRÜN_MIT_AUFLAGEN** (Auflagen A und B nach Prüfung **erledigt**).

## Selbst nachgemessen
- **Unit (A-3 + Bestand), SQLite:** 46/46 OK, 100 Assertions.
- **DB-Smoke (echte Klassen, SQLite):** 15/15 OK.
- **Gegenbeweis-Harness:** Baseline grün; 8 Mutationen alle ROT wie erwartet (lintel, finished≤rough, Validator übersprungen, Update, Delete, Unique weg, Konflikt überschrieben, projection_role≠derived_only); md5-Restore aller 4 Dateien OK.
- **Feature-Tests (MySQL/RefreshDatabase):** hier nicht lauffähig (Alt-Migrationskette nicht SQLite-portabel) → in Yamas MySQL-Umgebung nachzuholen.

## 15 Punkte (Startblock §17)
1. `gebaeude_geometrie` bleibt Writer — Store schreibt nur `BuildingModelVersion::create(...)`; `SourceGeometryRef` hasht nur. ✓
2. Neue Tabelle derived_only — Migration-Default + Store-fixiert; Gegenbeweis #9 rot. ✓
3. Kein Mapping/Backfill — nur leere Tabelle, kein Seeder, kein Mapper. ✓
4. Immutable — `updating`/`deleting` werfen; kein `updated_at`/`deleted_at`; Gegenbeweis #4/#5 rot. (App-Level, offengelegt; Query-Builder-`update()` umginge Events — bewusst, kein Trigger.) ✓
5. Idempotenzschlüssel unique — `bmv_source_schema_projection_unique`. ✓
6. Konflikt statt Überschreibung — `ProjectionConflictException`; Race über `lockForUpdate` + 1062/23000-Fang; Gegenbeweis #7 rot. ✓
7. Validator vor Speicherung — Gegenbeweis #3 rot. ✓
8. A-3 vollständig — lintel==sill+rough; profile→Warncode; finished≤rough; vertikal OKFF-konsistent. ✓ (Auflage B, s. u.)
9. Keine stillen Defaults — profile explizit „unresolved"; `is_int`-Guards. ✓
10. Keine zweite Geometriewahrheit — derived_only, `room.polygon_not_marked_cache`. ✓
11. Additiv + reversibel — `Schema::create`, `down()` dropt nur diese Tabelle. ✓
12. Keine Bestandstabelle geändert. ✓
13. Kein Controller/Route/View/Job/Listener — git status nur Models/Services/Migration/Tests. ✓
14. Haupt-Worktree unverändert. ✓
15. Gegenbeweise belastbar — 8/8 rot, md5-Restore OK. ✓

## Auflagen
- **A (MUSS) — erledigt:** `BuildingModelVersionStoreTest` prüfte `$version->id === $p['revision_id']`, der PK ist aber surrogat (Store setzt `Str::uuid()`); auf MySQL wäre der gelieferte Test rot. **Fix:** Assertion auf `$version->revision_id` (+ `assertNotSame` PK) umgestellt.
- **B (SOLLTE) — erledigt:** `lintel_height_mm`/`finished_width_mm`/`finished_height_mm` fehlten in der mm-Integer-Prüfliste; ein Nicht-Integer hätte die Sturz-/Fertig-Prüfung still übersprungen. **Fix:** die drei Felder mit Null-Guard in die `unit.float_length`-Prüfung aufgenommen.

## Keine weiteren Funde
Kein Idempotenz-Loch, kein Immutability-Umweg (`upsert`/`insertOrIgnore`/`forceFill`+save auf Bestand) im Store, kein `now()`/UUID-Nebenwirkungsproblem; der Store schreibt nachweislich nur `building_model_versions`.
