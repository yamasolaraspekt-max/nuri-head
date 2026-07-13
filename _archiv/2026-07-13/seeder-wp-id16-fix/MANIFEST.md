# Archiv-Manifest — id=16-Fix: WaermepumpeKomplettloesungSeeder WP per Name auflösen

**Datum:** 2026-07-13 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** P3-d0 (belegt: `article_groups.id=2` = Wärmepumpe, `id=16` = „Tapete").

## Warum archiviert
Eine **committete Bestandsdatei** (Seeder) wird geändert → Sicherungskopie (zusätzlich zu `git revert`).

## Originalpfad → Archivkopie
- `database/seeders/WaermepumpeKomplettloesungSeeder.php` → `WaermepumpeKomplettloesungSeeder.php.original`

## Was geändert ist (nur die WP-Bindung, Option B)
```
- // Fixed from your UI wrapper
- $productId = 16;
+ // id=16-Fix (Option B): WP-Artikelgruppe per Name auflösen statt hartkodierter id (id=16 = „Tapete").
+ $productId = DB::table('article_groups')->where('article_group', 'Wärmepumpe')->value('id');
+ if ($productId === null) { throw new RuntimeException("... 'Wärmepumpe' nicht gefunden ..."); }
```
Der Seeder legt **Phasen-/Task-Templates** (`task_phases`/`phase_activities`) an — **kein** Katalog/Preis.
Vorher band er die WP-Komplettlösung hart an `product_id=16` (= „Tapete" in dieser DB); jetzt an die
per Name aufgelöste WP-Artikelgruppe (id=2). **Kein stiller Fallback** — fehlt „Wärmepumpe", bricht er ab.

## Nicht geändert / Nicht-Ziele
`stageMap`/`section_id` (hartkodierte Snapshot-ids — separater „Seeder-Portabilität"-Posten) · Zieltabellen ·
Insert-Logik · Katalog/Preis · Migration. **Keine Datenbereinigung** (Seeder lief auf dieser DB nie; keine
falschen WP-Daten vorhanden). Kein Seeder-Lauf gegen Dev-DB. Kein Push, kein `git add -A`.

## Tests
`tests/Feature/WaermepumpeKomplettloesungSeederTest.php` (neu, Test-DB, kein Dev-DB-Lauf): WP=2/Tapete=16 +
Fixtures (phase_sections/stages) → Seeder erzeugt Phasen/Aktivitäten mit `product_id=2` (nicht 16); fehlende
„Wärmepumpe"-Gruppe → klarer `RuntimeException`.

## Rückweg (Notfall)
**Variante A:** `git revert`. Alternativ Datei aus `.original` zurückspielen. Reine Code-Änderung, kein
DB-/Schema-/Dateneingriff → verlustfrei.
