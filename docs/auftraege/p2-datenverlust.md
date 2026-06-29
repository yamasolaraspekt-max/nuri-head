# Auftrag P2 — Datenverlust (für Ausführer-Agent)

**Rolle:** ticket, lokal, Branch `private/app-code-backup`, Push `backup-private`. Vier Fixes gegen
stillen Datenverlust in genutzten Kernbereichen. **1 Fix = 1 Commit.**

**STABILITÄTS-CHECK ZUERST (Pflicht):** `git rev-parse HEAD`, dann 90 s warten, nochmal prüfen.
Wenn der HEAD sich verändert hat → eine andere Instanz arbeitet parallel → **STOPP, nichts ändern,
melde es**. Nur wenn stabil, fortfahren. Notiere den HEAD, gegen den du arbeitest.

**VORAB je Fix:** betroffene Datei frisch lesen, prüfen ob die Lücke im aktuellen Code noch
existiert (Zeilennummern können abweichen). Bereits erledigt → "bereits gefixt" melden.

**Verifikation je Fix REAL per tinker (DB-Zustand vorher/nachher):** lege Testdaten an, führe die
Aktion aus, prüfe den Zustand, räume danach auf (forceDelete). Halte vorher/nachher fest.

---

## FIX P2-29 — Anfrage-Bearbeiten setzt Status bedingungslos zurück
`app/Http/Controllers/Inquiry/InquiryController.php` (~Z.1360): `update()` setzt
`$inquiry->status = 'Unpublished'` IMMER → eine bereits verifizierte (Published) Anfrage verliert
beim Bearbeiten ihren Status. (Gleiches Muster ggf. in store/finalizeDraft — dort ist Unpublished
als Initialzustand aber KORREKT; NUR update() ist der Bug.)
**Fix:** in `update()` den Status nur setzen, wenn er noch leer/Draft ist; sonst erhalten
(`$inquiry->status = $inquiry->status === 'Published' ? 'Published' : 'Unpublished';` o.ä.).
**Verif:** Inquiry mit status='Published' anlegen → update() (per Request-Simulation/tinker) →
Status MUSS 'Published' bleiben. Cleanup.

## FIX P2-30 — Lagerausgabe-Storno bucht Bestand nicht zurück
`app/Http/Controllers/Inventory/InventoryRequestOutController.php`: `store()` (~Z.256) zieht
Bestand ab, aber `destroy()`/`update()` (Storno) buchen NICHT zurück → Inventarschwund.
**Fix:** in `destroy()` (und Status-Storno in `update()`) den `quantity` des Postens wieder auf
`inventories.quantity` addieren, in DB::transaction. Doppel-Rückbuchung vermeiden (nur wenn vorher
abgebucht war).
**Verif:** Inventory-Stand notieren → RequestOut anlegen (Bestand sinkt) → destroy → Bestand MUSS
wieder auf Ausgangswert. Cleanup.

## FIX P2-31 — Urlaub genehmigen reduziert Resttage nicht
`app/Http/Controllers/Employee/Profile/LeaveController.php` `approve()` (~Z.230-258): setzt
approved/status, dekrementiert aber `employees.remaining_day` NICHT (nur der Dashboard-Pfad save()
tut das).
**Fix:** in `approve()` nach Erfolg `Employee::where('id',$leave->emp_id)->decrement('remaining_day',
$leave->duration)` in DB::transaction. Doppel-Dekrement bei erneutem approve vermeiden (nur wenn
Status vorher nicht schon 'accept' war).
**Verif:** Employee remaining_day notieren → Leave anlegen → approve → remaining_day MUSS um
duration gesunken sein; zweites approve darf NICHT nochmal abziehen. Cleanup.

## FIX P2-32 — Urlaubsanspruch-Bearbeiten entzieht Published-Status
`app/Http/Controllers/Employee/Profile/LeaveDayController.php` (~Z.71): `update()` setzt
`status='Not published'` hart → Bearbeiten eines aktiven Jahres entzieht still den Status.
**Fix:** Status beibehalten (nur ändern, wenn bewusst gewünscht).
**Verif:** LeaveDay mit status='published' → update() → Status MUSS erhalten bleiben. Cleanup.

---

**BERICHT:** je Fix — was geändert, Verif-Beleg (Zustand vorher→nachher), Commit-Hash, "bereits
gefixt" falls zutreffend. Am Ende: HEAD-Hash + offene Fragen. Wenn der Stabilitäts-Check anschlägt:
nur das melden, sonst nichts.