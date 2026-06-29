# Auftrag P1 — Block 1 (für die Ausführer-Instanz)

**Rolle:** ticket, lokal, Branch `private/app-code-backup`, Push nach `backup-private`. Ein Planer
überwacht dich und nimmt jeden Fix ab. **P1-Block 1: sechs tote/kaputte Pfade**, die beim Klicken
500/404 werfen. **1 Fix = 1 Commit.**

**VORAB-REGEL (Lehre aus P0):** Vor jedem Fix prüfen, ob die Lücke im **aktuellen HEAD** noch
existiert (`git rev-parse HEAD`; den betroffenen Code frisch lesen). Manche Funde der Untersuchung
können zwischenzeitlich erledigt sein — solche als „bereits gefixt" melden, nicht doppelt anfassen.

**Verifikation je Fix REAL:** eingeloggt als **B = claudia.neumann@solar-aspekt-nord.test**
(Passwort `demo1234`, volle Rechte) den betroffenen Endpunkt aufrufen — vorher 500/404,
nachher 200/funktioniert. HTTP-Code vorher/nachher in den Bericht.

---

## FIX P1-20 — Übergaben tot (Class not found)
`app/Http/Controllers/HandoverController.php:8` importiert/nutzt `App\Models\Assets` (existiert nicht).
**Fix:** `use App\Models\Asset;` und alle `Assets::` → `Asset::` (Z.97,151,234,236).
**Verif:** `GET /handover` als B → vorher 500, nachher 200.

## FIX P1-21 — Rabattgruppen: RouteNotFound nach Speichern
`app/Http/Controllers/Product/DiscountGroupController.php:59,94` →
`redirect()->route('discount.group.info')` (existiert nicht).
**Fix:** auf `'discount_group.info'` (Unterstrich) korrigieren.
**Verif:** Rabattgruppe anlegen/ändern → vorher RouteNotFoundException, nachher Redirect ok.

## FIX P1-22 — Urlaubsanspruch: 500 beim Rendern
`resources/views/admin/employee/holiday/leave_day.blade.php:26,54,158` →
`action('App\Http\Controllers\LeaveDayController@…')` falscher Namespace.
**Fix:** korrekter FQCN `Employee\Profile\LeaveDayController` bzw. `route()`-Helper.
**Verif:** Urlaubsanspruch-Seite als B → vorher 500 (InvalidArgumentException), nachher 200.

## FIX P1-23 — Qualifikation löschen: Fatal + falsches Model
`app/Http/Controllers/Employee/Position/QualificationController.php:104` ruft
`FurtherEducation::find` (nicht importiert).
**Fix:** auf `Qualification::find($id)` ändern, `FurtherEducation`-Bezug entfernen,
Ownership-Prüfung ergänzen.
**Verif:** Qualifikation löschen → vorher Fatal, nachher korrektes Model gelöscht.

## FIX P1-24 — Lead-Objekt wiederherstellen: 404
`resources/views/admin/.../customer_view.blade.php:5878` postet auf
`/lead/objects/{id}/restore-deleted` — Route + Methode fehlen (`routes/web.php:780-784`,
nur junk/restore-junk/delete).
**Fix:** Route + `restoreDeletedObject()` ergänzen (analog zu restore-junk).
**Verif:** gelöschtes Objekt wiederherstellen → vorher 404, nachher ok.

## FIX P1-25 — Sprache löschen: 405
Lösch-Form (`resources/views/admin/.../emp_lang.blade.php:927`) sendet POST+`@method('DELETE')`,
Route ist GET `/language_destroy` (`routes/web.php:2828`).
**Fix:** Route auf `Route::delete` vereinheitlichen.
**Verif:** Sprache löschen → vorher 405, nachher gelöscht.

---

**BERICHT an den Planer:** je Fix HTTP-Code vorher/nachher (oder „war bereits gefixt"), aktueller
HEAD nach dem letzten Commit, und ob eine Ownership-/Folgefrage offenblieb.
