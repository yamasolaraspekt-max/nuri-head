# Auftrag Runde 4 — ID-Mapping-Konsistenz + is_active-Toggle (isoliert, nacht-sicher)

**Rolle:** ticket, lokal, Branch `private/app-code-backup`, Push `backup-private`. Nur isolierte
Controller-Fixes (Fremd-Instanz arbeitet parallel im Seeder-/Partner-Bereich → den meiden). **1 Fix = 1 Commit.**

**STABILITÄTS-CHECK ZUERST:** `git rev-parse --short HEAD`, `sleep 90`, nochmal. Bei Veränderung → STOPP + "INSTABIL". Branch = private/app-code-backup.

**DISZIPLIN:** committe NUR deine Zieldatei explizit (`git add <pfad>`), NIE `-A`/`-a`. Vor jedem
Commit `git status` prüfen. Fremd-Änderungen (sidebar.blade.php, docs/) unangetastet lassen.

**VORAB je Fix:** Datei frisch lesen, prüfen ob die Lücke noch existiert (Stand bewegt sich). Falls
schon behoben → "bereits gefixt" melden.

---

## FIX P5-44 — GeneralTask: (int)name statt employeeId() → Fehlattribution
`app/Http/Controllers/Task/GeneralTaskController.php`: mehrere Stellen nutzen
`(int) auth()->user()->name` direkt (z.B. claimed_by/created_by/assignee), statt des Helpers
`User::employeeId()`. Bei nicht-numerischem Login bzw. Admin-Bypass wird so `0` geschrieben (falsche
Zuordnung). Helper existiert bereits (`app/Models/User.php`, `employeeId()`: numerisch→int, sonst null).
**Fix:** alle `(int) auth()->user()->name`-Vorkommen in diesem Controller durch
`auth()->user()->employeeId()` ersetzen; wo ein Integer zwingend ist, einen Null-Guard ergänzen
(`?? 0` nur, wo fachlich vertretbar — sonst Aktion ablehnen). NICHT die in P0-10 ergänzte
GeneralTaskPolicy/authorize-Logik verändern.
**Verif:** `php -l` sauber; kurz per tinker/Logik zeigen, dass employeeId() für einen Demo-User den
korrekten Wert liefert. (Kein DB-Schreibtest nötig, falls zu aufwändig — Code-Konsistenz reicht,
dann im Bericht so vermerken.)

## FIX P4-41 — UserController: is_active-Toggle ist No-Op
`app/Http/Controllers/User/UserController.php` (~Z.137 und ~Z.299): `is_active = $request->has('is_active') ? 1 : 1`
→ immer 1, ein inaktiver Benutzer lässt sich nicht anlegen/setzen.
**Fix:** korrekte Auswertung, z.B. `is_active = $request->boolean('is_active') ? 1 : 0;` (an beiden
Stellen). NICHT die in P0-06 ergänzte Permission-/Admin-Logik verändern.
**Verif:** `php -l` sauber. Optional curl als A: Benutzer mit/ohne is_active anlegen → Wert korrekt
gespeichert. Wenn curl zu aufwändig: Code-Beleg reicht, im Bericht vermerken.

---

**BERICHT:** je Fix — Änderung (Datei:Zeile), wie verifiziert, Commit-Hash (oder "bereits gefixt").
Am Ende: HEAD + ob Stabilitäts-Check ansprang + offene Fragen.