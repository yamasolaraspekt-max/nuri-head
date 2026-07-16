# G1b-0 — Persistenzvarianten und Entscheidungsvorlage

**Welle:** G1b-0 (Persistenz-/Revisions-/Mapping-/Cutover-Entscheidung) · **Basis:** `b31f451` (isolierter Worktree `work/g1b-0-persistence-decision`) · **Stand:** 2026-07-16
**Modus:** read-only Architektur-/Mappingwelle. **Kein Produktivcode, keine Migration, keine Tabelle, kein Model, kein Commit, kein Push.**
**Bindet:** 3D-0B Variante C (ratifiziert), 3D-0C-Mapping, G1a-Vertrag/ADR, G0c-2, „Eine Wahrheit je Sachverhalt", „Kein Dual-Write".

> **Leitregel (unverändert, nicht still aufgehoben):** `anforderungsprofile.gebaeude_geometrie` bleibt bis zu einem späteren, ausdrücklich von Yama beauftragten Cutover-Slice die **einzige kanonische fachliche Schreibwahrheit**. Neue relationale/Snapshot-Strukturen sind zunächst **nur abgeleitete Projektion** — nie ein zweiter Schreiber. **Kein Dual-Write.**

---

## 1. Ist-Persistenz (read-only inventarisiert)

### 1.1 Speicher
- **`anforderungsprofile.gebaeude_geometrie`** — JSON, **nullable**, kein Default (`2026_07_05_170007_add_gebaeude_geometrie_to_anforderungsprofile_table.php:18`); Cast `array` (`app/Models/Anforderungsprofil.php:33`).
- Trägertabelle `anforderungsprofile`: `version` (unsignedInteger, default 1), `status` (`entwurf|aktiv|abgeloest`), `abgeloest_durch_id` (self-FK, nullOnDelete), `morphs('verankerbar')` → **`LeadAlternativeAdd`/`LeadProductList`** (Whitelist `Anforderungsprofil.php:47-50`, `saving`-Hook erzwingt sie), `created_by`→employees, `timestamps`. **Kein `updated_by`, kein SoftDelete.** Index `[verankerbar_type, verankerbar_id, status]`.
- **Format der Spalte:** `{ raeume: [ { name, grundflaeche_m2, bauteile:[ { typ, u_wert_datenlage, … } ] } ] }` — das **Heizlast-Rechenformat**, **nicht** die G1a-Kanonik (`buildings → storeys → nodes/walls/openings/rooms/slabs`).
- **Verwandte Geometrie-Halter:** `raum_geometrien` (polygon/wand_segmente JSON, an `heizlast_raeume`) = **Legacy-/Transient-Editorpfad**, nach G0c-2 vom aktiven Save **nicht mehr** beschrieben (Wächter-Test). `sanierungs_varianten`, `p_v_roof_plans.roof_structures`, `RoofAreaEstimator` halten **keine** kanonische Gebäudegeometrie.
- **`building_model*`-Tabellen: existieren nicht** (Migrations-grep leer). Der G1a-Validator ist rein in-memory.

### 1.2 Writer (genau 2)
| ID | Datei:Methode | schreibt | Anlass | Validierung | Transaktion | Lock/Stale |
|----|---------------|----------|--------|-------------|-------------|------------|
| **W1** | `GrundrissController::schreibeGeometrieVersion` (Zuweisung `:328`) | volle `raeume[]`-Struktur (aus `ausGeometrie`/Mirror) | `POST energie.grundriss.speichern` | **TopologieGate** (Regeln 1–7) + Objekt-Pflicht (422 `objekt_fehlt`) | `DB::transaction` (`:268`) | erbt aus W2 |
| **W2** | `AnforderungsprofilService::neueVersion` (`:65-120`, Zuweisung `:97`) | kopiert Geometrie in Version n+1 (append-only) | jede neue Profilversion | Morph-Whitelist (Model `saving`) | `DB::transaction` (`:67`) | **P0:** `lockForUpdate` + `max(version)` + `StaleProfilVersionException` (`:78-85`) |
Append-only: Alt-Version → `status=abgeloest` + `abgeloest_durch_id`, **kein UPDATE/DELETE** auf Bestandszeilen. Keine weiteren produktiven Writer.

### 1.3 Reader
| ID | Datei:Methode | liest | Zweck | Cutover-Risiko |
|----|---------------|-------|-------|----------------|
| **R1** | `AnforderungsprofilHeizlastAdapter::berechneUndSchreibe` (`:35,47,61,85`) | `raeume[].bauteile[].u_wert_datenlage` | **DIN EN 12831 Heizlast** (byte-genauer Kern) | **HOCH** — direkte Rechen-Eingabe; Format-/Schlüsseländerung bricht die Rechnung |
| **R2** | Versions-Kopie (W2) | ganze Geometrie zum Reproduzieren | Versionskette | mittel (format-agnostische 1:1-Kopie) |
| — | Tests (`AnforderungsprofilHeizlastAdapterTest`, `GrundrissProfilPersistenzTest`) | `raeume[]` | Regression | Testbruch bei Formatwechsel |
**Kein** produktiver Reader in WP-Auslegung, PV, Dach, `RoofAreaEstimator`, Projektansicht oder Export greift auf `gebaeude_geometrie` zu. Der Grundriss-Editor liest `raum_geometrien` (Legacy), nicht die kanonische Spalte. **Der einzige fachliche Konsument der kanonischen Wahrheit ist R1 (Heizlast).**

**Folge für G1b:** Ein Cutover berührt genau **einen** produktiven Reader (Heizlast) und **einen** Schreibpfad (Grundriss→AnforderungsprofilService). Das hält die Umstellung klein und beherrschbar — aber R1 ist geschäftskritisch (Heizlast-Golden-Master).

---

## 2. Variantenbewertung

### P-A — `gebaeude_geometrie` dauerhaft zur Kanonik erweitern
Die JSON-Spalte wird um die G1a-Felder (buildings/storeys/nodes/walls/openings mit reference_line_type, station, Umlaufsinn) erweitert und bleibt langfristiger kanonischer Speicher.
- **Pro:** kein neuer Store; ein Schreiber bleibt; append-only-Versionierung existiert bereits; kein Reader-Umzug nötig, wenn das Alt-`raeume[]` als abgeleiteter Teil erhalten bleibt.
- **Contra:** die Spalte müsste **zwei Formate** tragen (Heizlast-`raeume[]` **und** G1a-Topologie) → faktisch zwei Wahrheiten in einer Zelle, oder ein Formatbruch für R1. JSON-Größe/Query-barkeit für 300 Wände schlecht; keine relationale Integrität/Indizes; Undo/Redo, Teilzustände, CAD-Funktionen (G2ff) schwer. **Verstößt tendenziell gegen „eine Wahrheit je Sachverhalt" innerhalb einer Zelle.**
- **Urteil:** als **Übergang** brauchbar (Spalte bleibt Writer), als **Ziel** ungeeignet.

### P-B — Immutable Snapshots als abgeleitete Projektion
`gebaeude_geometrie` bleibt Writer; eine neue, **unveränderliche** Struktur `building_model_versions` hält **abgeleitete** G1a-Snapshots (JSON im v1-Schema, `is_cache`/`derived`, `derived_from`-Bezug, Hash).
- **Pro:** saubere Trennung Writer↔Projektion; Reproduzierbarkeit + Parität ohne den Writer anzufassen; die Projektion ist jederzeit aus ihren Quellen neu aufbaubar (Rollback = Tabelle droppen, kein Datenverlust, `gebaeude_geometrie` unberührt); Grundlage für spätere Leserumstellung.
- **Contra:** Gefahr, dass die Projektion **faktisch** als Zweitwahrheit gelesen/geschrieben wird, wenn nicht streng „derived-only" + Wächter-Test; Projektionsfehler möglich (Transform G1a↔Heizlastformat).
- **⚠️ Eingangsmenge ehrlich (CAD-Auflage A2):** Die Projektion ist **nicht** allein aus `gebaeude_geometrie` (Q1) rekonstruierbar. Q1 trägt das Heizlast-`raeume[]`-Format (Räume/Flächen/U-Werte) — **keine** Knoten/Wandpolygone/Öffnungen/Stationen. Die G1a-Topologie (walls/openings/nodes) stammt objektgebunden aus **`raum_geometrien` (Q2)**, einem Legacy-/Transientpfad, der gegenüber Q1 **veralten** kann. Damit gilt: aus Q1 allein entsteht nur ein **Raum-Skelett** (ausreichend für die Heizlast-Parität, weil R1 genau die Q1-Operanden liest); die **echte** Wand-/Öffnungs-Topologie-Wahrheit schreibt erst der spätere Editor in den P-D-Writer. Bis dahin liefert P-B ein Teilmodell. (Lokal wird 0 Bestandsgeometrie erwartet, was das praktisch entschärft, den konzeptionellen Punkt aber nicht aufhebt.)
- **Urteil:** **geeigneter erster konkreter Baustein** — als *derived projection*, nie als Schreiber.

### P-C — Gestufter Cutover auf neue kanonische Persistenz
Phasen (identisch zu 3D-0C G2): (1) `gebaeude_geometrie` bleibt Writer → (2) neue Struktur nur Dry-Run/derived → (3) Paritätsnachweis → (4) ausgewählte Leser umstellen → (5) Writer-Cutover in eigenem Slice → (6) alten Writer sperren → (7) kein Dual-Write zurück.
- **Pro:** setzt „eine Wahrheit / kein Dual-Write" strukturell durch; jeder Schritt einzeln prüf-/rückrollbar; nutzt P-B als Stufe 2; Heizlast-Golden-Master als Gate.
- **Contra:** mehrstufig, mehrere Slices; Zielspeicher (P-A-Spalte vs. P-D relational) wird erst zum Cutover final gewählt.
- **Urteil:** **empfohlener Gesamtweg.**

### P-D — Vollständig relationale Bauteilpersistenz
Relationale Tabellen `building_models/floors/nodes/walls/openings/rooms/slabs` gemäß Teil C.
- **Pro:** relationale Integrität, Indizes, Teilzustände, echtes Undo/Redo über Change-Sets, tragfähig für alle CAD-Funktionen ab G2; JSON-Export ableitbar.
- **Contra:** hohe Komplexität; nur sinnvoll **nach** bewiesener Parität; als **sofortiger** Zweit-Writer verboten (Dual-Write).
- **Urteil:** **langfristige Zielpersistenz** — Ziel des Writer-Cutovers (Phase 5 von P-C), nicht jetzt.

---

## 3. Planner-Empfehlung (eindeutig)

**Gesamtweg: P-C (gestufter Cutover).** Konkrete Ausprägung der Stufen:

| Frage (§7) | Entscheidung |
|---|---|
| **Übergangs-Writer** | `anforderungsprofile.gebaeude_geometrie` (unverändert, via W1 GrundrissController + W2 AnforderungsprofilService). |
| **Spätere Zielpersistenz** | **P-D** (relationale Bauteilpersistenz `building_*`) — erst nach Paritätsnachweis, eigener Yama-Slice. |
| **Snapshot-/Projektionsstatus** | Erster Baustein = **P-B**: `building_model_versions` als **abgeleitete, unveränderliche Projektion** im G1a-Format (`is_cache`/`derived`, `derived_from_profile_version`, `geometry_hash`). **Nie führend, nie zweiter Schreiber.** |
| **Zeitpunkt Writer-Cutover** | Erst wenn Struktur-**und** Geometrie-**und** fachliche Parität grün, Rollback getestet, Heizlast-Golden-Master grün — dann Writer-Cutover P-D in eigenem Slice (G1b-2+). |
| **Zeitpunkt Leserumstellung** | **Nach** Writer-Cutover: R1 (Heizlast-Adapter) über Adapter auf die kanonische Quelle umstellen; bis dahin liest R1 weiter `gebaeude_geometrie`. Kein Reader-Umzug vor bewiesener Parität. |
| **Rollback** | Solange nur P-B existiert: Projektion droppen = vollständiger Rollback (kein Datenverlust, `gebaeude_geometrie` unberührt). Nach Writer-Cutover: Umschalt-Flag zurück + alten Writer entsperren (in Cutover-Slice zu bauen/testen). |
| **Sperrung alten Writers** | Erst in der Cutover-Phase (Phase 6) per Wächter-Test (`GeometrieSchreibpfadWaechterTest`-Muster), nachdem P-D Writer grün. |
| **Dual-Write-Vermeidung** | P-B ist **derived-only** (immer aus `gebaeude_geometrie` neu aufbaubar), wird nie unabhängig geschrieben; Wächter-Test sichert den einen Schreibpfad. Zu jedem Zeitpunkt **genau ein fachlicher Writer**, beliebig viele Leser. |

**Ausdrücklich unzulässig** (§7): „beide Systeme vorerst parallel schreiben". Es gibt zu jedem Zeitpunkt genau einen fachlichen Writer.

**Formatbrücke (Kernrisiko):** Da `gebaeude_geometrie` das Heizlast-`raeume[]`-Format hält und G1a die Topologie-Kanonik, braucht die Projektion (P-B) einen **Transform-Adapter** `HeizlastGeometrie → CanonicalBuildingModel`. Dieser Adapter ist selbst Gegenstand des Dry-Run-Mappings (siehe `g1b-0-dry-run-mapping.md`) und muss die G1a-Pflichtfelder (reference_line_type, station_mm, Umlaufsinn, Höhen) aus dem ärmeren Alt-Format **ableiten oder als `nutzerentscheidung` markieren** — kein erfundener Wert.

---

## 4. Abgrenzung / Nicht-Ziele dieser Welle
Keine Migration, keine Tabelle/Model, kein Writer/Reader-Umbau, keine Änderung an `gebaeude_geometrie`, am G1a-Validator oder am v1-Schema, kein Controller/Route/View/Config/Editor/Three.js/Dach/PV/LiDAR. G1b-0 liefert nur Entscheidung + Verträge + Mapping + Parität-/Cutover-Plan.
