---
name: building-document
description: Datenmodell-Skill für das versionierte Gebäudedokument (SceneDocument) des Planners. Regelt Schema, mm-Ganzzahl-Konvention, Migration/Versionierung (schemaVersion), Revision/Checksum, 409-Konfliktschutz und additive DB-Änderungen. Nutzt die vorhandene Hausplaner-Domain als Basis (R1/R2), baut keine zweite Persistenz.
---

# building-document

## Ziel
Ein konsistentes, versioniertes Gebäudedokument als einzige Wahrheit der Szene.

## Basis (wiederverwenden, R1/R2)
- `app/Domain/Hausplaner/Models/HausplanerDocument.php` / `HausplanerSnapshot.php`
- Actions `ErstelleLeeresSzenenDokument`, `SpeichereHausplanerDokument` (Revision+Checksum,
  `lockForUpdate`, 409-Pfad), `StelleSnapshotWieder`
- TS-Schema/Validierung `resources/planner/hausplaner/domain/{scene.types.ts,validation.ts}`

## Konventionen
- Eine Szene, **mm als Ganzzahl** (`units:'mm'`).
- `schemaVersion` + Lade-Migration (z. B. v1→v2 additiv, alte Inhalte unverändert).
- Node-Union additiv erweitern (neue Sammlungen NEBEN der Union, wenn Konsumenten sonst brechen).
- Persistenz: Client sendet `base_revision` + `schema_version` + `scene`; Serverseite validiert
  `schema_version`/`scene.schemaVersion` (gültige Versionen als `in:...` pflegen), schreibt
  Revision+Checksum, Spalte folgt der Szene.
- **Additiv-only DB** (Ticket ist live): neue Tabellen/Spalten, keine destruktiven Migrationen.

## Nicht-Scope
Keine zweite Dokumenten-/Upload-/Versionslogik neben der bestehenden. Keine Renderer-Logik hier.

## Prüfungen
- Schema-Round-Trip (laden→migrieren→validieren→speichern→laden) konsistent.
- Revisions-/409-Verhalten getestet.
- Validierung lehnt unbekannte Version/Node-Typ ab (Kante), verliert nichts still.

## Pflicht-Stopp
Spec/Änderungsvorschlag ohne Selbstumsetzung, wenn nur Planung beauftragt. Kein Commit. Kein Push.
