# RELEASE-MANIFEST — G0c-2: Objektgebundene, versionierte Geometrie-Persistenz

**Stand:** 2026-07-14 · **lokal, kein Push, keine Migration, kein Kern-Eingriff, kein Commit** (Commit-Freigabe = echter Yama nach unabhängiger Prüfung).
**Startblock:** „Bau frei G0c-2 v1.1 — FINAL (verbindliche Yama-Entscheidung, Rang 4)". **Grundlage:** `docs/g0c-migrationsplan.md` (G0c-1 read-only Plan, §10 Auflösung). **Git-Ausgangsstand:** `7a2b829`.
**Abschlussformel:** *Umgesetzt, bereit zur unabhängigen Prüfung.* Keine Selbst-Abnahme, keine Auto-Fortsetzung.

---

## 1. Umgesetzter Scope (G0c-2)
`GrundrissController::speichern` schreibt die gezeichnete Grundriss-Geometrie **nicht mehr destruktiv** in
`raum_geometrien` (`updateOrCreate`) + `heizlast_bauteile` (`bauteile()->delete()`), sondern als **neue,
objektgebundene, versionierte Anforderungsprofil-Version** in `anforderungsprofile.gebaeude_geometrie`.
**Verhalten-only, keine Migration; der Ziel-Hook `gebaeude_geometrie` existierte bereits.**

- **Objekt-Pflicht:** `speichern` verlangt `alternative_id` (→ `LeadAlternativeAdd`) **vor** dem Gate.
  Fehlt es → **422 `objekt_fehlt`** (nicht gefunden → 422 `objekt_nicht_gefunden`). **Objektlose
  Persistenz ist verboten** — kein Raten, kein Default-Objekt (Operanden-Gate).
- **Versionierung:** vorhandene aktive Profil-Version am Objekt → `AnforderungsprofilService::neueVersion`
  (append-only n→n+1); sonst `anlegen` (Version 1). Danach `aktivieren` → **genau eine `aktiv`**, die
  Vorversion wird `abgeloest` (nicht gelöscht).
- **Topologie-Gate (G0b) bleibt Pflichtdurchgang** vor jedem Schreiben (Bow-Tie → 422 `selbstschnitt`,
  keine Version).
- **Transform = Mirror `GeometrieAbleitungService::ausGeometrie`** (byte-unverändert, nur aufgerufen).
- **Alt-Pfad bleibt** (`baueProjekt`/`schreibeInProjekt` nur noch für transiente `vorschau`) — deprecated,
  **nicht gelöscht** (Rückfall-/Archiv-Regel).
- **Scope-Zuschnitt (Yama): „Backend jetzt, UI als Sub-Slice."** Server-422-Guard genügt; sichtbarer
  Objekt-Picker / „Grundriss erfassen"-Action (AP-3a) = browser-verifizierter Sub-Slice (verschoben, §2).

## 2. Nicht umgesetzt / bewusst verschoben
- **Sichtbarer Objekt-Picker + AP-3a „Grundriss erfassen"-Action** → browser-verifizierter Sub-Slice
  (Nicht-Scope dieser Welle; kein Editor-Redesign). `editor()` liest bereits `?alternative=<id>` und
  reicht `alternativeId` an die View — die serverseitige Bindung steht, die sichtbare Zuweisung folgt.
- **Backfill/Umzug des Alt-Bestands** → Deploy-Tag-Runbook (`docs/g0c-migrationsplan.md` §7/§7a), inkl.
  **Objekt-Zuordnungsstrategie (kein Raten, manuelle Review-Liste)**. Lokal 0 Bestandszeilen.
- **Controller-Umstellung der Leser** (Auslegung liest künftig aus dem Profil) → Folge-Slice.
- **D3/D4** (heizlast_bauteile stilllegen; Mehrraum) → spätere Slices.

## 3. D1–D5 — wörtlich (aus G0c-1 Plan §2) + Auflösung (Yama 2026-07-14)

> **D1 — Schemabedarf:** Reicht der bestehende `gebaeude_geometrie`-JSON-Hook (dann **keine Migration**, nur Verhalten + Transform), oder soll eine **additive** Spalte (z. B. `raum_geometrien.anforderungsprofil_id` als Verweis / `geometry_hash`) ergänzt werden? *(Falls additive Migration gewünscht → G0c-2 mit Pflicht-Stopp + additivem Vorschlag.)*
>
> **D2 — Quell-Shape ↔ Ziel-Shape:** `raum_geometrien` (polygon+wand_segmente) ist die *editierbare* Geometrie; `gebaeude_geometrie` (raeume[]+bauteile[]) ist der *Rechen-Input*. Bleibt `raum_geometrien` als editierbarer Layer (read-only nach Umzug) erhalten, während `gebaeude_geometrie` die führende versionierte Wahrheit wird? Oder wandert auch die editierbare Roh-Geometrie ins Profil-JSON?
>
> **D3 — `heizlast_bauteile`:** bleibt als abgeleiteter Cache (re-derivierbar via `ausGeometrie`) oder wird ebenfalls stillgelegt?
>
> **D4 — Mehrraum/Geschoss:** heute `GrundrissController` = 1 Projekt : 1 Raum; `gebaeude_geometrie.raeume[]` erlaubt mehrere. Umzug = Modell-Erweiterung? (Scope-Frage für G0c-2.)
>
> **D5 — Verankerung:** Geometrie ans **Objekt** (`LeadAlternativeAdd`) oder ans **Gewerk** (`LeadProductList`)? (AP-3 Option E: Objekt kanonisch.)

**Auflösung (verbindlich):**
| # | Entscheidung | Umsetzung in G0c-2 |
|---|---|---|
| D1 | **Verhalten-only, keine Migration.** `gebaeude_geometrie` existiert. | Kein Migrationsfile, keine neue Spalte/Tabelle. |
| D2 | `raum_geometrien` bleibt **transienter Vorschau-Layer**; `gebaeude_geometrie` wird **führend**. | `vorschau` nutzt weiter den transienten Pfad; `speichern` schreibt nur noch ins Profil. |
| D3 | `heizlast_bauteile` **bleibt** re-derivierbarer Cache. | In G0c-2 **nicht** stillgelegt (späterer Slice). |
| D4 | **1 Raum je Save** (Grundriss = 1 Raum). | `raeume => [ ausGeometrie(...) ]`; Mehrraum späterer Slice. |
| D5 | Verankerung am **Objekt** (`LeadAlternativeAdd`, AP-3 Option E). | `verankerbar_type = LeadAlternativeAdd`, `verankerbar_id = alternative_id`. |

**Objekt-Kontext-Entscheidung (Sofort-Stopp aufgelöst):** **Option B + Auffang-„A-lite"** — Erfassung aus
Objekt-/AP-3a-Kontext (`?alternative=<id>`); fehlt das Objekt → serverseitig **422** (kein Raten). Kein
Option C. **Begriffe:** „Profil-Persistenz" der Geometrie **ist G0c-2**; eine spätere **Stufe 3b** (WP)
ist **Folgeintegration**, nicht erneut „Profil-Persistenz".

## 4. Geänderte/neue Dateien (path-scoped)
- **Geändert (1 Produktivdatei):** `app/Http/Controllers/Energie/GrundrissController.php`
  (use-Imports, Konstruktor `AnforderungsprofilService`, `editor()` liest `?alternative`, `speichern()`
  objektgebunden+versioniert umgeschrieben, neue Helfer `baueGebaeudeGeometrie` + `schreibeGeometrieVersion`).
- **Geändert (1 Testvertrag):** `tests/Feature/Geometrie/GrundrissGateHttpTest.php`
  (`test_speichern_lehnt_ab_und_persistiert_nichts` auf Objekt-Pflicht-vor-Gate umgestellt + `objekt()`-Helper;
  Original archiviert). **Grund:** G0c-2 ändert den `speichern`-Vertrag legitim (Objekt vor Gate) — kein
  Regressionsbruch, sondern erwartete Vertragsanpassung (wie G0a→G0b-Fixtures).
- **Neu:** `tests/Feature/Geometrie/GrundrissProfilPersistenzTest.php` (5 Tests),
  `docs/g0c2-release-manifest.md`, `_archiv/2026-07-14/g0c2-geometrie-profil-persistenz/`
  (`GrundrissController.php.original`, `GrundrissGateHttpTest.php.original`, `MANIFEST.md`).
- **Doku aktualisiert:** `docs/g0c-migrationsplan.md` (§7a Runbook-Objekt-Zuordnung, §10 Auflösung),
  `docs/wp-stufe3-release-manifest.md` §11 (Begriffsabgrenzung).

## 5. Kern-Services unverändert (Mirror-Analogie)
`git status app/Services/Heizlast app/Services/Klima app/Services/Geometrie app/Services/Anforderungsprofil app/Models`
→ **leer**. `GeometrieAbleitungService`/`RaumHuelleService`/`HeizlastRechner`/`AnforderungsprofilService`/
`TopologieGate` nur **aufgerufen**, nicht verändert.

## 6. Datenbank / Migration
**Keine.** D1 = Verhalten-only. Kein neues Schema, keine Spalte, keine Tabelle. Alt-Tabellen bleiben
(deprecated, nicht gedroppt). **DB-Dump-Pflicht gegenstandslos:** die Tests laufen gegen die
Per-Worktree-Test-DB (`ticket_testing`, RefreshDatabase); die lokale Arbeits-/Dev-DB `ticket` wird
**nicht** geschrieben.

## 7. Tests — Befehle & Rohresultate
```
php artisan test tests/Feature/Geometrie/GrundrissProfilPersistenzTest.php
→ 5 passed (22 assertions)
php artisan test tests/Feature/Geometrie/GrundrissGateHttpTest.php
→ 4 passed
php artisan test        (volle Suite / Wächter)
→ 606 passed, 1 failed
```
**Abdeckung (v1.1-Kriterien):**
- `test_objektgebundener_save_erzeugt_neue_aktive_version` — Save 1 → Version 1 aktiv; Save 2 → Version 2,
  **genau eine aktiv**, Version 1 **abgelöst** (nicht gelöscht); Wand-Fläche 15,0 m².
- `test_objektlose_persistenz_wird_abgelehnt` — ohne `alternative_id` → **422 `objekt_fehlt`**, 0 Profile.
- `test_topologie_gate_bleibt_pflicht` — Bow-Tie → **422 `selbstschnitt`**, 0 Profile (Gate bleibt Pflicht).
- `test_aktiver_pfad_schreibt_keine_raum_geometrien` — `raum_geometrien` + `heizlast_projekte` unverändert
  (kein destruktiver Schreibpfad mehr).
- `test_aequivalenz_abgeleitete_werte_stimmen` — die im Profil abgeleiteten Werte (grundflaeche 15,0;
  hoehe 3,0; Wand-Fläche 15,0; grenzflaeche aussen) entsprechen exakt der Mirror-`ausGeometrie`-Ableitung.
- Vertrags-Regression `GrundrissGateHttpTest::test_speichern_lehnt_ab_und_persistiert_nichts` — jetzt mit
  Objekt: Gate erreicht, Bow-Tie abgelehnt, weder `raum_geometrien` noch `anforderungsprofile` geschrieben.

**Einziger Rotfall (volle Suite):** `InvoiceDeletionGuardTest` (Reverb `localhost:6001`, BroadcastException)
= **E4-anerkannter Vorbestand**. Ein **zweiter** Rotfall trat während der Umsetzung auf (der o. g.
G0b-Vertragstest) und wurde als **erwartete Vertragsanpassung** aufgelöst (nicht als Regression) → Suite
zurück auf **genau einen** (Reverb) Rotfall.

## 8. Äquivalenz-Hinweis (ehrlich)
Der Äquivalenz-Nachweis vergleicht **stabile abgeleitete Zahlenwerte** (grundflaeche_m2, hoehe_m,
Wand-flaeche_m2, grenzflaeche) zwischen gespeichertem Profil und Mirror-`ausGeometrie` — **nicht** einen
rohen JSON-Byte-Hash. Grund: `speichern` normalisiert die Eingabe über `validieren()`/`normSegmente`
(Default-Felder, Schlüssel-Reihenfolge), sodass ein roher md5-Vergleich gegen eine handgebaute
Un-normalisierte Geometrie ein **Artefakt** der Feld-Reihenfolge misst, nicht die fachliche Äquivalenz.
Die abgeleiteten Zahlen sind die belastbare, reproduzierbare Wahrheit. Der Deploy-Tag-Backfill nutzt
zusätzlich den per-Zeile-Vergleich alt↔neu (§5 Migrationsplan).

## 9. Bestätigungen
- ✅ Kern-Services **unverändert** (Mirror-Analogie; git leer).
- ✅ **Keine Migration**, keine neue Tabelle/Spalte; Alt-Tabellen bleiben (deprecated, nicht gedroppt).
- ✅ **Keine Bestandsdaten geändert/gelöscht** (additiv; lokal 0 Zeilen; Hetzner off-limits bis Deploy-Tag).
- ✅ **Objektlose Persistenz verboten** (422); kein geratenes Objekt (Operanden-Gate).
- ✅ **Topologie-Gate bleibt Pflicht**; kein Gate-Bypass.
- ✅ **Kein UI/Alpine-Umbau**, kein Editor-Redesign, kein Gebäudeplaner-Vorgriff, kein wberechnung-Eingriff.
- ✅ **Kein CLAUDE.md**, keine fremden Docs, kein `git add -A`, **kein Commit, kein Push**; HEAD `7a2b829`.
- ✅ **Rückfall-Archiv** angelegt (`_archiv/2026-07-14/g0c2-…` inkl. Original + MANIFEST).

## 10. Prüfbefehle für den Evaluator
1. `git status --short app/Services app/Models` → leer (Kern unverändert).
2. `git status --short` → G0c-2-Pfade: `GrundrissController.php` (M), Tests (M/neu), Docs, `_archiv/`.
   **⚠️ Wichtig (Evaluator-Auflage A):** `CLAUDE.md` ist im Working-Tree ebenfalls **modifiziert** —
   das ist **vorbestehende Rang-2-Governance-Drift** (nicht in dieser Welle erzeugt, datiert 07-10/07-11;
   Befund: `docs/claude-md-befund.md`) und gehört **NICHT** in den G0c-2-Commit. Der Yama-Commit ist
   **strikt path-scoped** auf die G0c-2-Dateien; **CLAUDE.md ausschließen** (kein `git add -A`). Ebenso
   entscheidet Yama, ob `docs/wp-stufe3-release-manifest.md` (§4, bewusst geändert) in denselben oder
   einen eigenen Commit gehört.
3. `php artisan test tests/Feature/Geometrie/GrundrissProfilPersistenzTest.php` → 5 passed.
4. `php artisan test tests/Feature/Geometrie/GrundrissGateHttpTest.php` → 4 passed.
5. `php artisan test` → 606 passed, 1 failed (nur Reverb-E4).
6. Selbst nachmessen: objektlos → 422 `objekt_fehlt`; Bow-Tie → 422 `selbstschnitt`; zweiter Save → Version 2, eine aktiv/eine abgelöst.
7. Kern-Gegenprobe: `git diff 7a2b829 -- app/Services/Heizlast/GeometrieAbleitungService.php` → leer.

## 11. Offene Punkte / nächster Schritt / Ballbesitz
- **Sub-Slice (browser-verifiziert):** Objekt-Picker + AP-3a „Grundriss erfassen"-Action (UI).
- **Deploy-Tag:** Backfill + Objekt-Zuordnungsstrategie (§7a Migrationsplan) — manuelle Review-Liste, kein Raten.
- **Folge-Slice:** Leser-Umstellung (Auslegung/HeizlastController lesen aus dem Profil); D3/D4.
- **Evaluator-Notizen (Folge-Slice, kein Veto):**
  - **B (Nebenläufigkeit, Vorbestand):** `AnforderungsprofilService::neueVersion` liest die aktive Version
    ohne `lockForUpdate`; kein Unique-Index auf `(verankerbar, version)`. Bei echtem Parallel-Doppel-Save
    theoretisch gleiche `version`/Lost-Update möglich (Endinvariante „eine aktiv" bleibt). Vorbestehende
    Service-Eigenschaft, lokal Einzelnutzer unkritisch → optimistisches Locking / Unique-Index als Härtung.
  - **C (Testtiefe, optional):** Äquivalenz-Test ist Known-Answer (hartkodierte Sollwerte) statt A/B-Diff
    gegen einen zweiten `ausGeometrie`-Aufruf; fachlich tragfähig (gleicher Mirror), A/B nur strenger.
- **Evaluator-Votum:** **FREIGABE MIT AUFLAGEN** (Auflage A = CLAUDE.md aus dem Commit ausschließen +
  §10.2 korrigiert → erledigt). Kriterien 1–8 durch eigenes Nachmessen bestätigt; volle Suite genau ein
  Rotfall (Reverb-E4).
- **Ballbesitz:** Evaluator-Prüfung abgeschlossen → **echter Yama** (Commit-Freigabe G0c-2, path-scoped). **Kein Push.**

---

*Umsetzung der beauftragten Welle G0c-2 (v1.1 FINAL) abgeschlossen. RELEASE-MANIFEST und Prüfbefehle liegen vor. Umgesetzt, bereit zur unabhängigen Prüfung.*
