# [TEST-HARNESS] — Durchklick-Testbestand (nur lokal)

Seeder zum **manuellen Durchklicken** von CRM-Flächen mit einem definierten, wieder entfernbaren Testbestand. **Kein Produktiv-Code.** Jede Testzeile trägt den Marker `[TEST-HARNESS]` in einem Textfeld → Teardown löscht rückstandsfrei. Läuft **nur** in `local` (`guardLocal()` wirft sonst).

## Befehle

```bash
# Alles seeden (Kontext + Qualifikation + Montage + Kanban)
php artisan db:seed --class="Database\Seeders\Testing\HarnessSeeder"

# Wieder entfernen (Hard-Delete über den Marker, 0 Reste)
php artisan db:seed --class="Database\Seeders\Testing\HarnessTeardownSeeder"
```

Idempotent: mehrfaches Seeden erzeugt **keine** Duplikate (upsert über den Marker).

## Kanban durchklicken — was du wo siehst

Nach dem Seed **`/lead/kanban` öffnen** (Board). Zusätzlich zu den Live-Karten erscheinen die Test-Karten (Kunde/Objekt heißen `[TEST-HARNESS] …`):

| Spalte | Test-Karte | Worauf achten |
|---|---|---|
| **Lead** | „Kanban 1 Lead" | Kontroll-Karte **ohne** Zustands-Pill |
| **Angebot** | „Kanban 2 Angebot" | **Wiedervorlage-Pill MIT Datum** (offenes Follow-up) |
| **Angebot** | „Kanban 3b Wiedervorlage-Alt" | **Wiedervorlage-Pill OHNE Datum** (Alt-Muster `status='follow_up'`, per FK-Fold hier gelandet) |
| **Angebot** | „Kanban 4a NULL-FK" / „4b FK-Zustands-Stage" | B1-Netz: erscheinen trotz fehlender/zeigt-auf-Zustands-FK (Fallback bzw. Fold) — **Regressionswächter** |
| **Auftrag** | „Kanban 3 Auftrag" | reine Phasen-Karte |
| **Montage** | „Kanban 4 Montage" | **alle Aufgaben-Pills**: Offen 3 · Erledigt 1 · **Prüfung 1** (mit Prüfer) · **Überfällig 1** · Aufgaben 1/4 |
| **Abnahme** | „Kanban 5 Abnahme" | **erste Abnahme-Karte überhaupt** — die Spalte war vor Stufe B leer |
| **Abschluss** | „Kanban 6 Abschluss" | abgeschlossene Karte (Spalte sonst leer) |

**Prüfliste** (`reported`-Karte des Prüfers) füllt sich über die Montage-Karte „Aufgabe Prüfung" (unqualifizierter Monteur → Rückfluss an den Prüfer, s. `MontageTestSeeder`/`QualifikationTestSeeder`).

## Seeder-Übersicht
- `HarnessContextSeeder` — Kunde/Objekt/Produkt/Gewerk + Phase/Tätigkeiten (Basis, alle docken hier an).
- `QualifikationTestSeeder` — Test-Mitarbeiter (qualifiziert/unqualifiziert/Prüfer) + Marker-User.
- `MontageTestSeeder` — Montage-Karten A–F (Qualifikations-Rückfluss: done vs. reported+Prüfer).
- `KanbanTestSeeder` — der Board-Durchklick-Bestand (obige Tabelle).
- `HarnessTeardownSeeder` — Hard-Delete über den Marker (inkl. `personal_tasks`-Follow-ups).
