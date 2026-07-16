# Abnahme — Hausplaner-Transplantation Code-Layer (T-a/T-b/T-c)

**Wave:** Hausplaner nach ticket · **HEAD:** 6c4f6bd · **Datum:** 2026-07-16
**Rollentrennung gewahrt:** Generator = Instanz A (Transplantation), Evaluator = unabhaengige
Instanz. Diese Abnahme ist das Urteil des **Evaluators** (selbst gemessen, mit Gegen-Beweis),
nicht die Selbstauskunft des Generators.

**Messumgebung des Evaluators:** Wegwerf-DB `ticket_testing` (nur das Hausplaner-File migriert,
Live-`ticket` ausschliesslich lesend beruehrt). Laufzeit-Gegenbeweise transaktional (Rollback).

## Urteil je Kriterium — alle GRUEN

1. **Additiv, keine Regression** — Migration `Schema::create` fuer genau 3 Tabellen, idempotent
   (`if hasTable(...) return`), FK gekapselt; `git show --stat` der 3 Commits: 0× ALTER/DROP/RENAME
   an Bestand, kein UPDATE-Beifang, keine Beruehrung der Kette invoices/deals/offers.
2. **Anker ▲T1 alternative_id** — 0 Code-Treffer fuer `project_id`/`Project::` in
   app/Domain/Hausplaner + Controller (nur 1 Kommentar). `scene['projectId']=$alternativeId`
   (Wert wandert, Bundle-Feldname bleibt), DB-Spalte `alternative_id`.
3. **base_revision → 409, kanonische Checksum** — Laufzeit: Stale-Save (base=1 gegen rev2) →
   `ok:false`, DB-Zeile unveraendert, „HIJACK"-Nutzlast nicht uebernommen. Checksum: gleiche Daten/
   andere Schluesselreihenfolge → identische sha256; ohne ksort → verschieden (Kanonisierung wirkt).
4. **Route-Hygiene** — `route:list`: 6× hausplaner.objekt.* + 1 alte hausplaner.dachplaner, alle mit
   permission-MW, `{objekt}`/`{snapshotId}` whereNumber; fremde Snapshot-ID → 404 (Ownership-Filter).
5. **Sichtbarkeit == Route-Sperre** — `@can(` in akte.blade = 0; Button-Bedingung und Route-MW rufen
   beide exakt `hasPermission('hausplaner.view','read')` → koennen nicht divergieren; is_admin-Bypass
   oeffnet beide, ohne Recht schliessen beide (403 + kein Button).
6. **BearbeitungsSperre im Island** — `@include(...bearbeitungs-sperre, ['bereich'=>'hausplaner',
   'sperrId'=>$objekt->id])`; `system.sperre.ping/leave` existieren (web.php ~4966) → keine
   route()-Exception.
7. **Heredoc/Blade-Integritaet** — `grep \$` in beiden Views = 0/0; `php -l` Kompilat beider Views +
   Partial: „No syntax errors" (3/3).

**Waechter:** volle ticket-Testsuite **704 passed / 0 failed** (selbst ausgefuehrt); eine Wahrheit je
Objekt bewiesen (zweites Erstellen auf dieselbe alternative_id → 1062 Duplicate, unique-Index greift).

## Stehender FLAG + vereinbarter Folgeschritt (eigenes, additives Paket)

`hausplaner.view`/`.manage` sind (noch) nicht im item/action-Raster modelliert → heute oeffnet nur
`is_admin`. Evaluator-Bewertung: fuer „In Abnahme" **tragbar (fail-closed:** kein halb-offener Zustand;
Nicht-Admin bekommt weder Button noch Route). Sauberes Rechte-Mapping als spaeteres Paket (Planner-Go
noetig, weil Route-Konvention beruehrt):
- `hausplaner` als **ein** Item registrieren (Seeder/Migration, additiv), `is_read`=view,
  `is_update`=manage.
- Routen auf `permission:hausplaner,view` bzw. `permission:hausplaner,update` umstellen (der
  `hasPermission($item,$action)`-Vertrag traegt das bereits) — einziger Code-Touch; Button-Bedingung
  analog. Button↔Route-Symmetrie bleibt erhalten.
- UI im Rechte-Admin, um das Item je Mitarbeiter/Rolle zu schalten.

## Status / Ballbesitz

Code-Layer **grün, additiv, ankertreu** — T-d (die 5 Browser-Sichtproben,
`docs/abnahme-hausplaner-t-d.md`) ist **freigegeben**. Der Marker „In Abnahme" bleibt stehen, bis
alle fuenf Sichtproben grün sind (dann eigener additiver Commit zur Marker-Entfernung). Die
Zwei-Sitzungs-Amber-Probe (SP-5) und die visuellen 403/Button-Zustaende (SP-1/SP-2) sind headless
nicht renderbar — Code-Pfade aber bewiesen; sie gehoeren bewusst zu Yamas Browser-Abnahme.
