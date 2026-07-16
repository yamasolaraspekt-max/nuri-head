# RELEASE-MANIFEST — G1b-0 (Persistenzentscheidung, isolierter Worktree)

**Welle:** G1b-0 (Persistenz-/Revisions-/Mapping-/Cutover-Entscheidung) · **Heimat-App:** `ticket` · **Stand:** 2026-07-16
**Isolierter Branch:** `work/g1b-0-persistence-decision` · **Worktree:** `../ticket-g1b-0` · **Basiscommit:** `b31f451`
**Haupt-Worktree bei Auftrag:** `2e5db0a` (9 fremde Navi-/CI-Commits über G1a — **unberührt**)
**Status:** UMGESETZT (Analyse/Entscheidungsvorlage) — nicht „grün". Abnahme durch CAD-Fachagent + technischen Evaluator + Yama ausstehend. **Kein Produktivcode, keine Migration, kein Commit, kein Push.**

## 1. Scope
Ausschließlich Architektur-/Mappingentscheidung für die Persistenz des kanonischen Gebäudemodells, ohne eine zweite Geometriewahrheit zu erzeugen. **Nicht** enthalten: Migration, Tabelle, Model, Writer/Reader-Umbau, Änderung an `gebaeude_geometrie`/G1a-Validator/v1-Schema, Controller/Route/View/Config/Editor/Three.js/Dach/PV/LiDAR, Commit, Push, Berührung des Haupt-Worktrees oder der 9 fremden Commits.

## 2. Erzeugte Dokumente (genau 5, im isolierten Worktree)
- `docs/building-planner/g1b-0-persistenzvarianten-und-entscheidungsvorlage.md`
- `docs/building-planner/g1b-0-dry-run-mapping.md`
- `docs/building-planner/g1b-0-revision-locking-und-cutover.md`
- `docs/building-planner/g1b-0-oeffnungshoehen-vertrag.md`
- `docs/building-planner/g1b-0-release-manifest.md` (dieses Dokument)

## 3. Inventur-Kennzahlen (read-only)
- **Geometrie-Schreibwahrheit:** 1 — `anforderungsprofile.gebaeude_geometrie` (JSON, versioniert, objektgebunden).
- **Writer:** 2 — `GrundrissController::schreibeGeometrieVersion` (TopologieGate), `AnforderungsprofilService::neueVersion` (P0-Stale-Lock).
- **Reader (produktiv):** 1 — `AnforderungsprofilHeizlastAdapter` (DIN EN 12831, hohes Cutover-Risiko); + Versions-Kopie.
- **`building_model*`-Tabellen:** 0 (existieren nicht).

## 4. Kern-Entscheidungen
- **Gesamtweg:** P-C (gestufter Cutover). Übergangs-Writer `gebaeude_geometrie`; Zielpersistenz P-D (relational) erst nach Parität + eigenem Yama-Slice.
- **Erster Baustein:** P-B `building_model_versions` als **abgeleitete, unveränderliche Projektion** (derived-only, nie Zweit-Writer).
- **Kein Dual-Write:** zu jedem Zeitpunkt genau ein fachlicher Writer; Wächter-Test sichert den Schreibpfad.
- **Revision/Locking:** Wiederverwendung der bestehenden append-only Versionskette + P0-Stale-Mechanik; Status `draft/validated/confirmed/superseded`; `schema_version` ≠ `revision_id` getrennt; keine neue Locking-Implementierung.
- **A-3 Öffnungshöhen:** vollständig als Vertrag entschieden (OKFF-Datum, `rough_top = sill + rough_height`, `lintel == rough_top` sonst gekennzeichneter Messkonflikt, Rohbau führt, `profile` nutzt lokale Wandhöhe an der Station).
- **Höhenprofil/DG:** Persistenzentscheidung schließt Kniestock/Abseite/geneigte Oberkante/unterschiedliche Raumhöhen nicht aus (`height_mode=profile` reserviert); kein Dach-/Profil-Algorithmus gebaut.

## 5. G0c-2 / „eine Wahrheit" — Bestätigung
Der append-only, objektgebundene Geometrie-Hook (G0c-2) bleibt erhalten und wird nicht verändert. Es entsteht kein zweiter Store; die Projektion ist derived-only.

## 6. Prüfung (getrennte Instanzen) — Ergebnis
- **CAD-Fachagent: FACHLICH_GRÜN_MIT_AUFLAGEN.** Alle 10 Fachfragen JA. Bindende Auflagen vor P-B-Bau: **A1** Reihenfolge Leserumstellung mit/nach Writer-Cutover (in Doc 3 §5 korrigiert), **A2** Projektions-Eingangsmenge Q1+Q2 ehrlich deklariert (in Doc 1 §2 korrigiert), **A3** `reference_line_type` nicht still `axis` (in Doc 2 M11 korrigiert). Umsetzungs-Vormerkungen: **A4** Sturzkonsistenz `opening.lintel_conflict` im Validator nachrüsten (P-B), **A5** Vertikalprüfung unter `height_mode=profile` auf lokale Wandhöhe umstellen (Profil-Slice).
- **Technischer Evaluator: GRÜN_MIT_AUFLAGEN.** 14 Punkte code-belegt (alle Datei:Zeile verifiziert: Writer `GrundrissController:328`/`AnforderungsprofilService:97`, Stale `:78-85`, Reader `AnforderungsprofilHeizlastAdapter:35`, Migration `…170007:18`; keine `building_model`-Migration; 0 eigene Commits; kein Remote-Ref). Nicht-blockierende Auflage: Git-Snapshot als „Stand bei Auftrag" kennzeichnen (§7).

## 7. Git-Status
- **Haupt-Worktree** (`/Users/yamanuri/Documents/ticket`): **Stand bei Auftrag** HEAD `2e5db0a`. Der Branch `private/app-code-backup` wird durch den **fremden Navi-/CI-Strang laufend weiterbewegt** (nach Auftragsbeginn weitere `feat(nav)`-Commits) — **von G1b-0 nicht berührt**. `b31f451` bleibt reiner Ancestor (kein Rebase/Alter). Fremde Commits, untracked Dateien und `_to_delete/` unangetastet. (Diese Zeile ist bewusst als Auftragsstand dokumentiert, nicht als aktuelle Live-Behauptung.)
- **Isolierter Worktree** (`../ticket-g1b-0`): Branch `work/g1b-0-persistence-decision`, Basis `b31f451`, nur die 5 neuen Docs untracked, 0 Commits, kein Push.

## 8. Bestätigungen
Haupt-Worktree unverändert · 9 fremde Commits unverändert · fremde uncommittete Dateien unverändert · kein Produktivcode · keine Migration · kein neuer Store · kein Dual-Write · kein Commit · kein Push · G1b-1 nicht begonnen.
