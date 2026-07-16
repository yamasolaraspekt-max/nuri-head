# G1b-0 — Revisionen, Unveränderlichkeit, Locking und Cutover

**Welle:** G1b-0 · **Basis:** `b31f451` · **Stand:** 2026-07-16 · **read-only Vertrag, keine Tabelle/Model/Migration.**
**Bindet:** G0c-2 (append-only Geometrie-Hook), P0 (StaleProfilVersionException), 3D-0B/0C, „kein Dual-Write".

---

## 1. Modellidentität (Vertrag)

Jede persistierte kanonische Modellrevision führt:

| Feld | Bedeutung | Herkunft im Übergang |
|---|---|---|
| `model_id` (UUID) | stabile Modellidentität je Objekt | deterministisch aus `anforderungsprofile`-Objektbindung |
| `revision_id` (UUID) | eindeutige Revision | je `anforderungsprofile.version` |
| `parent_revision_id` (UUID/null) | Vorgänger­revision | Versionskette (n-1) |
| `schema_version` | JSON-Schema-Version (SemVer) | aus `v1.schema.json` |
| `geometry_hash` | Integritäts-/Änderungshash über die kanonische Geometrie | berechnet bei Projektion |
| `object_id` | Objektbindung | `verankerbar_id` |
| `source` / `data_quality` | Herkunft/Konfidenz | `migrated_existing_geometry` etc. |
| `created_by` / `created_at` | Ersteller/Zeit | `anforderungsprofile.created_by`/`timestamps` |
| `derived_from_profile_version` | Projektionsbezug (P-B) | `anforderungsprofile.version` |

**Trennung (verbindlich):** `schema_version` (technische Schema-Migration) und `revision_id`/`parent_revision_id` (fachliche Modellrevision) sind **getrennt** — eine Schema-Migration ändert **keine** fachliche Revision und umgekehrt.

## 2. Status-Lebenszyklus

```
draft ──(Validierung/Gate grün)──▶ validated ──(fachliche Freigabe)──▶ confirmed
                                                                          │
                                              (neue Bearbeitung erzeugt neue draft-Revision)
                                                                          ▼
confirmed ──(durch Nachfolgerevision abgelöst)──▶ superseded
```

Abbildung auf den Ist-Zustand (`anforderungsprofile.status`): `entwurf→draft`, (Gate-Durchlauf →`validated`), `aktiv→confirmed`, `abgeloest→superseded`. Der Zwischenstatus `validated` ist im Alt-Format nicht materialisiert und wird aus dem TopologieGate-Durchlauf abgeleitet.

## 3. Unveränderlichkeitsregeln (Vertrag)

1. **Bestätigte (`confirmed`) Revisionen werden niemals in-place verändert.** Bearbeitung erzeugt eine **neue `draft`-Revision** (append-only).
2. **Frühere Revisionen bleiben lesbar** (`superseded`, nicht gelöscht) — wie heute `abgeloest` + `abgeloest_durch_id`.
3. **Kein Soft-Delete, das Nachweise unsichtbar macht** — Revisions-/Freigabe-/Snapshot-Einträge bleiben unveränderliche Historie (analog Teil C: keine Soft-Deletes auf `building_model_versions`/Nachweistabellen).
4. **Bestätigung/Freigabe ist nachvollziehbar** (`created_by`, Zeitstempel, ggf. `confirmed_by`).
5. **Ein-aktiv-Invariante je Objekt** bleibt (heute App-Regel in `aktivieren`); DB-Durchsetzung ist ein späterer Slice-Punkt, kein Muss in G1b-0.

Diese Regeln sind bereits im Ist erfüllt (append-only Versionskette). G1b-0 hebt sie in den kanonischen Vertrag; es wird **kein** Code geändert.

## 4. Locking und Parallelbearbeitung (Wiederverwendung, kein Neubau)

- **Bestehend:** `AnforderungsprofilService::neueVersion` hält den Schreibpfad mit `lockForUpdate()` + `max(version)` + **`StaleProfilVersionException`** (P0) — eine stale Basis wird als **Konflikt abgelehnt**, kein Auto-Rebase.
- **Vertrag G1b:** die kanonische Persistenz nutzt **denselben** Revisions-/Stale-Mechanismus (Revisions-Lock), **keine** neue Locking-Implementierung. Ein optionaler pessimistischer **Modell-Lock** (`locked_by/locked_at/heartbeat/expires`) für den späteren Editor (B5) ist **Zielarchitektur ab G2a**, nicht G1b-0 — hier nur als Nahtstelle benannt.
- Read-only-Viewer während Bearbeitung: zulässig (lesen der `confirmed`/`superseded`-Revision), schreibt nie.
- Konflikt bei Bestätigung: stale → Ablehnung (P0-Verhalten), Nutzer arbeitet auf frischer Basis weiter.

## 5. Cutover- und Rollback-Gates

**Reihenfolge (P-C, jede Stufe eigener Slice + Yama-Freigabe):**

| Stufe | Inhalt | Gate zum Weitergehen | Rollback |
|---|---|---|---|
| 1 | `gebaeude_geometrie` bleibt Writer | — (Ist) | — |
| 2 | **P-B** derived Projektion `building_model_versions` (nur Lesen/Parität) | additive Migration mit Up/Down + Yama-Freigabe | Tabelle droppen (kein Datenverlust) |
| 3 | **Paritätsnachweis** (siehe unten) | alle Parität-Gates grün | — |
| 4 | **Writer-Cutover** auf P-D relational (eigener Slice) | Parität + Rollback getestet + Golden-Master grün | Umschalt-Flag zurück, alter Writer entsperrt |
| 5 | Leser (R1 Heizlast via Adapter) umstellen — **mit/nach** Writer-Cutover, nie auf eine derived-only-Projektion als führende Wahrheit | Heizlast-Golden-Master grün auf kanonischer Quelle | Reader-Flag zurück auf `gebaeude_geometrie` |
| 6 | alten Writer sperren (Wächter-Test) | kein Dual-Write nachweisbar | Wächter deaktivieren, alter Writer aktiv |
| 7 | Integritätsprüfung (1 aktiv/Objekt, Versionskette) | — | — |

> **Reihenfolge-Klarstellung (CAD-Auflage A1):** Der einzige geschäftskritische Leser (R1 Heizlast, DIN EN 12831) wird **erst mit/nach dem Writer-Cutover** umgestellt, damit er nie eine derived-only-Projektion als eigenständige Wahrheit liest. Bis dahin liest R1 unverändert `gebaeude_geometrie`. Dies ist die verbindliche, mit `g1b-0-persistenzvarianten-und-entscheidungsvorlage.md §3` deckungsgleiche Lesart.

**Writer-Cutover erst wenn** (§13): Referenzbestände vollständig verglichen · Differenzen erklärt · Rollback getestet · alte Writer anschließend gesperrt · kein Dual-Write möglich · Heizlast-Golden-Master grün.

## 6. Paritäts-Gates (messbar, für Stufe 3)

**Struktur:** Anzahl Gebäude / Geschosse / Knoten / Wände / Öffnungen / Räume identisch (alt-abgeleitet vs. kanonisch).
**Geometrie:** Koordinaten (mm-Integer) · Wandlängen · Wanddicken · Öffnungsstationen · Höhen (OKFF-bezogen) · Nordbezug · Umlaufsinn · Topologieurteil (TopologieGate) identisch.
**Fachlich (der harte Gate):** Außenwandflächen · Fensterflächen · Raumflächen · Raumvolumen · **Heizlast-Operanden** · Objektbindung · Revisionsbezug — **per-Zeile-Hash identisch** zum Golden-Master (`AnforderungsprofilHeizlastAdapterTest`, DIN EN 12831 Kern byte-genau).
**Cutover-Freigabe:** nur wenn alle drei Ebenen grün **und** Rollback getestet **und** alter Writer danach gesperrt.

## 7. Dual-Write-Ausschluss
Zu jedem Zeitpunkt **genau ein fachlicher Writer**. In Stufe 2–4 ist das `gebaeude_geometrie`; die Projektion ist **derived-only** (aus `gebaeude_geometrie` neu aufbaubar), nie unabhängig geschrieben. Ein Wächter-Test (Muster `GeometrieSchreibpfadWaechterTest`) sichert den einen Schreibpfad. Der 3D-Viewer/Editor-Renderer schreibt nie.

*G1b-0 legt keine Tabelle/Model an, ändert keinen Writer/Reader und keinen Validator. Alles hier ist Vertrag für spätere, einzeln freizugebende Slices.*
