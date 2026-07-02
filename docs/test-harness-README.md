# Test-Harness: Montage-Rückfluss & Qualifikation

Reproduzierbares, **local-only** Seed-Set für die Rückfluss-/Qualifikations-Kette
(1b-Link → B1-Anforderung → B3-Schwellenlogik → PL-Prüfliste). Legt einen
**eigenen** Test-Kontext an (kein Anhängen an echte Daten), ist **idempotent** und
lässt sich **restlos** wieder abräumen.

> Alle Zeilen tragen den Marker `[TEST-HARNESS]` in einem Textfeld (kein
> Schema-Eingriff). Der Teardown löscht ausschließlich anhand dieses Markers.

## Ausführen

```bash
# Seeden (idempotent — mehrfach ausführbar, dupliziert nicht)
php artisan db:seed --class="Database\Seeders\Testing\HarnessSeeder"

# Restlos abräumen (prüft am Ende 0 Reste je Tabelle, wirft sonst)
php artisan db:seed --class="Database\Seeders\Testing\HarnessTeardownSeeder"
```

Einzelne Bausteine sind auch einzeln aufrufbar
(`HarnessContextSeeder`, `QualifikationTestSeeder`, `MontageTestSeeder`) —
Reihenfolge: Context → Qualifikation → Montage.

## Sicherheit

Jeder Seeder (inkl. Teardown) ruft als **erste Zeile** `guardLocal()`:
außerhalb von `app()->environment('local')` wird eine `RuntimeException`
geworfen. Damit können weder Seed noch Teardown in Prod/Staging laufen.

## Dateien (`database/seeders/Testing/`)

| Datei | Zweck |
|---|---|
| `HarnessSupport.php` | Trait: `TAG`-Konstante, `guardLocal()`, idempotentes `upsertId()`, `context()`/`employees()`-Lookups über den Marker |
| `HarnessContextSeeder.php` | Produkt, Gewerk, Kunde (Privat), Objekt, `lead_product_list`, `phase_section`, `task_phase`, **2 Tätigkeiten** (mit/ohne Anforderung) |
| `QualifikationTestSeeder.php` | 4 Mitarbeiter + supervisor-Kette + je 1 Marker-User; referenziert die **echten** `position_qualifications` |
| `MontageTestSeeder.php` | 6 Kanban-Karten-Varianten + Pläne/Items (**1b-Link**) + `pie` + `klte` |
| `HarnessTeardownSeeder.php` | FK-sicheres Hard-Delete aller Marker-Zeilen + 0-Rest-Beweis (`remnants()`) |
| `HarnessSeeder.php` | Orchestrator (Context → Qualifikation → Montage) |

## Test-Kontext

- **Produkt** = eigenes `article_groups` · **Gewerk** = eigenes `departments`
- **Kunde/Objekt/Gewerk-Zeile** = `new_leads`(Privat) → `lead_alternative_adds` → `lead_product_lists`
- **Phase** = `task_phase` (+ eigene `phase_section`, da `phase_activities.section_id` NOT NULL + FK)
- **Tätigkeiten**:
  - *Montage (Meister erforderlich)* → `required_qualification_id` = Qualifikation mit **sort_order 3**
  - *Aufmaß (keine Anforderung)* → `required_qualification_id = null`

## Mitarbeiter & Schwelle

Anforderung = `sort_order 3`. **Qualifiziert = eigener sort_order ≤ 3** (kleiner = höher).

| Rolle | Qual (sort) | supervisor | Rolle in B3 |
|---|---|---|---|
| `Pruefer` | 3 | – | qualifiziert → wird Prüfer |
| `MonteurQual` | 2 | Pruefer | qualifiziert → Karte **done** |
| `MonteurUnqual` | 5 | Pruefer | nicht qualifiziert → Karte **reported** + Prüfer |
| `Fremder` | 1 | – | außerhalb der Kette (sieht Fall C nicht) |

Je Mitarbeiter ein **Marker-User** (`name = employee.id` für `authEmployeeId`,
E-Mail-Domain `@test-harness.local`). Passwort: `test-harness`.

## Montage-Szenarien (Karten)

| Karte | Ausgang | Tätigkeit | Performer | Nach `completeItemWithReport` |
|---|---|---|---|---|
| A | offen, ohne MA | req | – | Fixture (unassigned) |
| B | offen | req | MonteurQual | **done** |
| C | offen | req | MonteurUnqual | **reported** + Prüfer = Pruefer |
| D | offen | frei (keine Anf.) | MonteurUnqual | **done** (Anforderungs-frei) |
| E | bereits done | req | MonteurUnqual | unverändert (Zustands-Guard) |
| F | soft-deleted | req | MonteurUnqual | unverändert (SoftDelete-Guard) |

**Pflicht für B3:** Jede `planner_items`-Zeile trägt den 1b-Link
`source_type='phase_activity'` + `source_id=<Tätigkeit>` +
`kanban_lead_task_id=<Karte>` **und** eine `pie`-Zuordnung des Performers —
ohne diese drei Bedingungen feuert der B3-Rückfluss nicht.
Item B trägt zusätzlich **befülltes `meta`** (Regressionswächter für den
`sync()`-meta-Bug).

## Verifikationslauf (bestanden)

- **Seed → 32 Marker-Zeilen**; alle 5 Items `LINK OK` (source_type/source_id/kanban_lead_task_id gesetzt).
- **Seed erneut → 32** (idempotent, keine Dubletten).
- **Kette:** B → `done` · C → `reported` + reviewer = Pruefer, `done_at` null · D → `done` (frei).
- **Prüfliste:** Fall C erscheint beim Prüfer, **nicht** beim Fremden.
- **Teardown → 0 Reste** in allen Tabellen (auch nach mutierter Kette; Hard-Delete erwischt die soft-deletete Karte).
- **Prod-Guard:** `guardLocal()` wirft in gefakter `production`-Umgebung, in `local` nicht.
