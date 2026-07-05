# G-3b-T1 — HTTP-E2E-Test Anfrage-Konversion · BEFUND: Feature nicht vorhanden

**Datum:** 2026-07-05 · **Branch:** `private/app-code-backup` · **Ergebnis:** ⛔ **STOPP** — das zu testende Feature existiert im ticket-Repo (keinem Branch) nicht. **Keine Produktivdatei geändert, kein Feature erfunden.**

## Vorprüfung — `git status --short` (Anfang)

```
 M app/Http/Controllers/EconomicCalculationController.php   (Navi-Strang, fremd-uncommittet)
 M app/Http/Controllers/Heizkoerper/HeizkoerperController.php (M4-HK, parallele Instanz)
 M resources/views/admin/layouts/sidebar.blade.php          (Navi-Strang, fremd)
 M routes/web.php                                            (fremd, parallele Instanz)
 ?? diverse docs/*, .claude/, tests/Feature/Heizkoerper/…, Artefakte
```
Nichts davon ist konversions-bezogen. Alle Änderungen sind fremd/vorbestehend.

## Suche nach dem zu testenden Feature (read-only, belegt)

| gesucht | Beleg | Ergebnis |
|---|---|---|
| Route `konversion` / `/app/anfragen/{inquiry}/konversion` | `grep -r konversion routes/` | ❌ nicht vorhanden |
| `/app`-Route-Prefix | `grep prefix('app')/'app/' routes/` | ❌ nicht vorhanden |
| Ziel-Feld `converted_project_id` | `grep -r converted_project_id app/ database/ routes/` | ❌ nirgends |
| RBAC `crm.manage` / `crm.view` | `grep -r crm.manage\|crm.view` | ❌ nicht vorhanden |
| `InquiryConversion` / `ConversionService` | `grep -r … --include=*.php` (ohne vendor) | ❌ nicht vorhanden |
| `konversion`/`convert`/`vorschau` in `InquiryController` | `grep InquiryController.php` | ❌ keine solche Methode |
| Konversions-Commit in **allen** Branches | `git log --oneline --all \| grep -i konversion\|conversion` | ❌ leer — nie committet |
| `tests/Feature/Crm/` | `ls` | ❌ Verzeichnis fehlt |
| `php artisan test --filter=InquiryConversionTest` | Ausführung | ❌ **„No tests found"** |
| „finaler Kontrollbericht" / Konversions-Doku in `docs/` | `find/grep docs/` | ❌ nicht gefunden |

Vorhanden sind nur: `Inquiry`-Modelle + `InquiryController` (nur CRUD `inquiry_view`) **ohne Konversions-Funktion**. Anfragen/Leads laufen über `NewLeadsController` (`new_leads`), nicht über eine Konversions-Route.

## Was wurde getestet
**Nichts** — es gibt keine Route/Controller/Service. Ein HTTP-E2E-Test träfe eine nicht existierende Route (404 statt 201/403/409).

## Geänderte Dateien
**Keine** (nur dieser Bericht). `git status --short` (Ende) enthält **keine** Konversions-/Test-Datei von mir — identisch zum Anfang.

## Testergebnisse
`php artisan test --filter=InquiryConversionTest` → **No tests found**. Der Auftrag nahm diesen Service-Test als vorhanden an; er existiert nicht. (Die weiteren Verifikationsbefehle `--filter='InquiryConversionHttpTest|…'`, `tests/Feature/Crm`, `view:cache/clear` wurden nicht ausgeführt — ohne Feature gegenstandslos; `view:cache/clear` hätte den Cache-Zustand verändert, daher bewusst unterlassen.)

## Prämissen-Befund (der eigentliche Punkt)
Der Auftrag setzt voraus: *„Die Anfrage-Konversion ist service-getestet und Route-RBAC ist geprüft, es fehlt nur ein HTTP-E2E-Test."* **Alle drei Voraussetzungen fehlen** (kein Service, keine Route, kein RBAC, kein Service-Test, kein Kontrollbericht). Das Feature existiert in keinem Branch — vermutlich in einer anderen Instanz/Konversation gebaut oder geplant-aber-nicht-committet. **Ich habe das Feature nicht erfunden/gebaut** — das wäre eine massive Produktivänderung (Route + Controller + Service + RBAC + ggf. Migration), die der Auftrag („nur Tests") ausdrücklich ausschließt.

## Bestätigung
- ✅ Keine Produktivlogik geändert · ✅ keine Migration · ✅ keine Produktiv-DB berührt (nur read-only Suche + `--filter` gegen leere Testmenge) · ✅ keine Accounting-/Finanz-Gate-Dateien berührt · ✅ `git status` Ende = Anfang (nur dieser Bericht).

## Nötige Klärung
Wo ist das Anfrage-Konversion-Feature? **(a)** anderer Branch/Repo/Instanz → Pfad nennen, dann schreibe ich den E2E-Test dort; **(b)** geplant, noch nicht gebaut → dann fehlt erst das Feature selbst, bevor ein E2E-Test möglich ist. Ich rate/baue nichts, bis das geklärt ist.
