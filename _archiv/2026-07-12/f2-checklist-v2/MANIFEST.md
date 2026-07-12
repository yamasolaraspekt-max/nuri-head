# Archiv-Manifest — F2: product_formulas v2 Render/Erfassung lauffähig machen

**Datum:** 2026-07-12 · **Freigeber:** Yama („Bau frei für F2") · **Rolle:** Generator
**Bezug:** `docs/bereich2-folgeposten.md` (F2) · Startblock F2 (2026-07-12)

## Warum archiviert
Zwei **Bestandsdateien** werden geändert (Render/Erfassungspfad) → `rueckfall-archiv-regeln.md` **Variante B**
(Original zusätzlich sichern, nicht nur Git).

## Originalpfade → Archivkopien
- `resources/views/admin/new_leads/checklists/checklist.blade.php`
  → `_archiv/2026-07-12/f2-checklist-v2/resources/views/admin/new_leads/checklists/checklist.blade.php`
- `app/Http/Controllers/LeadProductChecklistValueController.php`
  → `_archiv/2026-07-12/f2-checklist-v2/app/Http/Controllers/LeadProductChecklistValueController.php`

## Was geändert wird (aktiver Pfad)
- **Blade:** v1/v2-robuste Normalisierung in EINEM gebündelten `@php`-Block (präsentationsnah, keine Fachlogik):
  robuster Array/JSON-Zugriff (kein `json_decode(array)`-TypeError), Feld-Identität `key ?? name`,
  Options aus CSV **oder** `{value,label}`-Array, Render aller v2-Typen (select/multiselect/number/integer/
  decimal/area/length/volume/power/plz/text/textarea/checkbox/boolean/consent/date/email/file/image),
  Unbekannt → Text/readonly (kein Crash). Robust gegen fehlendes `filled_values` via `$filled_values ?? []`.
  v1-Legacy-Hooks bleiben erhalten: `data-min`/`data-max`/`data-pattern`, `formula-field`/`data-formula`, `multi-group`.
- **Controller:** kleine private Helfer `asArray()` (Array/JSON/null → Array) + `fieldIdentity()` (`key ?? name`);
  `initChecklistRender`/`saveChecklist`/`save` json_decode-robust + identity-basiert; multiselect als Array
  gespeichert/gelesen.

## Bewusst NICHT geändert
`ProductFormulaController` (nicht im Scope; die Blade wird robust, sodass sein Preview unberührt funktioniert),
Models (`ProductFormula`/`LeadProductChecklistValue`), keine Migration, keine Berechnung/sections/PV.

## Geprüfte Nutzung
Blade gerendert von `LeadProductChecklistValueController::initChecklistRender` (`/lead-product-checklist/init`).
2. Aufrufer `ProductFormulaController::getProduct` (`:244`) ist **unrouted/Dead Code und bereits vor F2 defekt**
(nutzt eine undefinierte `$formula`) — F2 verschlechtert ihn nicht (neue Blade ist `$filled_values ?? []`-robust),
`ProductFormulaController` selbst wird NICHT geändert. Save-Route `/lead-product-checklist/save` → `saveChecklist`.
`save()` ist unrouted (tot), wird nur mitgehärtet. `saveChecklist`-Create-Zweig setzt `formula_snapshot`/
`formula_version` (NOT-NULL) via `firstOrNew`, damit Save auch ohne vorherigen Init nicht 1364-fehlschlägt.

## Tests
`tests/Feature/Form/ChecklistRenderV1V2Test.php` (v1 rendert, v2 rendert, v2 Save/Reload, multiselect, v1-CSV) +
Regression `WpProduktFormularPilotTest`.

## Rückweg (Notfall)
1. Beide Dateien aus diesem Archiv zurückspielen (bzw. `git revert` des path-scoped Commits).
2. Kein Datenbank-/Schemaeingriff → kein Datenverlust; additive Verzweigung, alte v1-Snapshots bleiben lesbar.
