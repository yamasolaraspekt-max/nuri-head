# EVALUATOR-AUFTRAG (Gesamt) — 2026-07-18

Rolle: **Evaluator** (unabhängig). Traue KEINER „umgesetzt"-Behauptung des Generators — **selbst
nachmessen**, je Kriterium **Gegen-Beweis**, Urteil **grün/rot mit Beleg**. Vorher `references/pruefrahmen.md`
lesen. Alles gegen **`ticket_testing`** (phpunit.xml) bzw. lokal, **nie** Live-DB. Rot blockiert Weiterrücken.

Umgebung: ticket (Laravel 11, MySQL). Terminal + PHP vorhanden (Cloud-Instanz hat das nicht → daher dieser Auftrag).

---

## BLOCK A — FiBu / Accounting (Stufe ii + Bestand iv–vii)

### A1. Volle Accounting-Testsuite
```
php artisan test tests/Feature/Accounting
```
Erwartung: **alle grün** — `KontenrahmenSeederTest`, `GobdBuchungsEngineTest` (iv),
`AuswertungenTest` (v, SuSa/BWA/UStVA), `DatevExtfExportTest` (vi), `PositionsSplitTest` (vii),
`EingangsBelegflussTest`, `JunkStornoTest`. Bei rot: Testname + Assertion + Ursache benennen.

### A2. Stufe (ii) — Seeder-Eigenschaften (NEU, Gegen-Beweis je Kriterium)
Datei: `tests/Feature/Accounting/KontenrahmenSeederTest.php` (5 Tests).
```
php artisan test --filter=KontenrahmenSeederTest
```
Gegen-Beweis (nicht dem grünen Balken glauben):
1. **Idempotenz:** Seeder von Hand zweimal laufen lassen, Marker-Zeilen zählen —
   `DB::table('accounts')->where('imported_from','skr_seed')->count()` vor/nach 2. Lauf identisch?
2. **Mapping-FK:** bewusst ein `account_mappings.account_id` auf 0 setzen → Test `…verweisen_auf_existierende_konten` MUSS rot werden (roter Gegentest), danach zurücksetzen.
3. **Kette unberührt:** `invoices`/`deals`/`customers` Row-Count vor/nach Seed selbst vergleichen.
4. **Marker-Rückbau:** nach `delete where imported_from='skr_seed'` sind die 5 FiBu-Tabellen marker-frei.
Urteil grün nur, wenn Selbst-Messung + Gegentest bestehen.

### A3. Offener FiBu-Punkt (nicht bauen, nur bestätigen als Lücke)
`KontenrahmenSeeder` hat **kein** §13b-Reverse-Charge und **kein** innergemeinschaftlich-Steuercode.
→ als bekannte Lücke vermerken (fiskalisch = Steuerberater-Gate). Kein Rot deswegen.

---

## BLOCK B — Hausplaner Save-Fix (frühere Abnahme war T2 ROT / HTTP 422)

Fix eingespielt: `HausplanerController` Z.39/41 `in:1` → `in:1,2`; `SpeichereHausplanerDokument`
schreibt jetzt die `schema_version`-Spalte aus `scene.schemaVersion`.

### B1. Migration vorhanden/gelaufen
```
php artisan migrate:status | grep -i hausplaner
```
`2026_07_16_211128_create_hausplaner_foundation_tables` muss **Ran** sein (sonst gegen ticket_testing migrieren).

### B2. T2 Persistenz (Kern, Gegen-Beweis)
Objekt-Planer per URL: `/admin/hausplaner/objekt/{TEST_OBJEKT_ID}` (als Admin, Test-Objekt).
Wände + Satteldach zeichnen → Speichern (Strg+S).
- Erwartung: **HTTP 200**; `hausplaner_documents.scene_json` enthält `"roofs":[…]` + `"schemaVersion":2`;
  **Spalte `schema_version` = 2**; `revision` steigt (1→2); Reload zeigt das Dach.
- Beleg: DB-Ausschnitt + WebGL-Screenshot nach Reload.

### B3. v1-Regression
Reines Wand-Szenario (ohne Dach) speichern → weiterhin 200, kein Bruch.

### B4. SP-3 (409-Konflikt)
Zweite Sitzung mit veralteter `base_revision` speichern → **409**, nichts überschrieben.

### B5. SP-4 (Snapshot append-only)
Nach erfolgreichem Speichern Snapshot anlegen → enthält v2-Stand, append-only.

### B6. SP-5 (PAGEERROR)
Konsolen-`addEventListener`-null-Fehler beim Mount: **exakten Stack + Quelldatei/Zeile** mitschneiden.
(Unsere zwei addEventListener-Ziele — `renderer.domElement` szene.ts:~85, `window` App:~254 — sind
nachweislich nicht null; welche Datei wirft wirklich?) Nach Klärung als grün/rot einstufen.

### B7. Hausplaner-Tests
```
php artisan test tests/Feature/Hausplaner        # UebernehmeSzeneInAuslegungTest
npm run test:hausplaner                            # JS: erwartet 76+ grün
```

---

## BLOCK C — Hausplaner Build + UI (heller CI-Umbau)

### C1. Bundle baut aus Quelle (bestätigt meine TS-Änderungen)
```
npm run build:hausplaner   # tsc --noEmit UND vite build müssen grün sein
```
Erwartung: tsc 0 Fehler, Build ok → `public/hausplaner/hausplaner.js` neu.

### C2. Studio-Route + Navi + Optik (Browser)
- Navi „Planung & 3D" → **„Hausplaner"** (Route `hausplaner.studio`, nur auth) öffnet die Standalone-Testfläche.
- Erwartung: **helle CI** (weiß/CRM-Grün `--sa-accent`, Font Inter, `sa-ui` eingebunden), 2D-Raster;
  Wand ziehen + Taste `d` → **3D zeigt ein sauberes Satteldach** (beide Flächen lesbar), Schatten/Boden.
- Beleg: Screenshot 2D + 3D.

---

## BLOCK D — A3 Rechte-Mapping

```
php artisan route:list | grep -i hausplaner
```
- Objekt-Routen hängen an `permission:Hausplaner,read|update`; `studio` nur `auth`.
- **Gegen-Beweis:** Nicht-Admin OHNE Recht „Hausplaner" → `/admin/hausplaner/objekt/{id}` = **403**, Akte-Button fehlt.
  Admin (`is_admin`) → Zugriff. Recht „Hausplaner" in `UserRollController::permissionModules()` vorhanden.

---

## BLOCK E — Reuse-Konfig (nur Existenz, kein Lauf)
`.claude/skills/ticket-code-reuse/` (+SKILL.md, references), `.claude/agents/`, `docs/planner/*`,
CLAUDE.md-Abschnitt „Existing-Code-First". Nur bestätigen, dass vorhanden.

---

## Wächter (bei allem prüfen)
- Keine Regression an Bestandsdaten; keine stillen `null`-Felder; keine zweite Wahrheit.
- Testsuite gesamt selbst ausgeführt (nicht nur die neuen), Referenzfall-Tests grün.
- Nichts gegen Live-DB; alles `ticket_testing`.

## Rückmeldung
Je Block A–E: **grün/rot mit Beleg** (Befehl, Ausgabe/Screenshot/DB-Ausschnitt, bei rot konkreter
Reproduktionsfall). Rot blockiert. Am Ende: Gesamturteil + offene Punkte an Yama.
