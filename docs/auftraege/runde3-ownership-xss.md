# Auftrag Runde 3 — P1-23 Ownership + P3 XSS-Bewertung (isoliert, nacht-sicher)

**Rolle:** ticket, lokal, Branch `private/app-code-backup`, Push `backup-private`. Nur isolierte
Controller-/View-Fixes (eine andere Instanz arbeitet parallel — Konflikte vermeiden!). **1 Fix = 1 Commit.**

**STABILITÄTS-CHECK ZUERST:** `git rev-parse --short HEAD`, `sleep 90`, nochmal prüfen. Bei
Veränderung → STOPP + melde "INSTABIL". Branch muss `private/app-code-backup` sein.

**DISZIPLIN GEGEN FREMD-ÄNDERUNGEN (Pflicht):** committe IMMER nur deine eigene Zieldatei explizit
(`git add <pfad>` dann `git commit`), NIEMALS `git add -A`/`git commit -a`. Lass uncommittete
Fremd-Änderungen (z.B. `sidebar.blade.php`) unangetastet. Vor jedem Commit `git status` prüfen,
dass nur deine Datei staged ist.

---

## FIX P1-23b — Ownership beim Qualifikation-Löschen (Nachbesserung)
`app/Http/Controllers/Employee/Position/QualificationController.php` `destroy($id)`: löscht aktuell
nur per `$id`, ohne zu prüfen, wem die Qualifikation gehört → IDOR (jeder kann fremde löschen).
**Fix:** Vor dem Löschen Ownership/Recht prüfen — Muster wie in den P0-Fixes: Admin-Bypass
(`auth()->user()->is_admin`) ODER der eingeloggte Mitarbeiter ist Eigentümer (Qualifikation gehört
zu seinem employee-Datensatz; prüfe das tatsächliche Feld, z.B. `emp_id`/`employee_id` der
Qualifikation gegen `User::employeeId()`). Wenn unklar, welche Spalte die Zuordnung trägt: das
sichere Minimum = Admin-only (`abort_unless(auth()->user()->is_admin, 403)`), und die offene Frage
melden.
**Verif (curl):** Login als C = kevin.wagner@solar-aspekt-nord.test (demo1234, keine Rechte) und
versuche eine Qualifikation zu löschen → muss **403**. Login als A = markus.hoffmann@… (Admin) →
darf. (Login: GET /login → _token, POST /login mit Cookie-Jar. Lösch-Route in routes/web.php suchen.)

## BEWERTUNG P3 — Stored XSS (NICHT blind fixen)
Betroffen: `resources/views/admin/appointments/show.blade.php:~1635` ({!! $r->report !!}) und
`resources/views/admin/problem/profile.blade.php` / `problem_edit.blade.php` (Quill-HTML via {!! !!}).
**Aufgabe = nur BEWERTEN, nicht riskant umsetzen:**
1. Prüfe, ob ein HTML-Purifier bereits installiert ist (`composer.json` / `vendor/` nach
   `mews/purifier` o.ä.).
2. Wenn JA: setze den Fix um (Purifier vor Ausgabe/Speichern) + verifiziere, dass harmloses HTML
   noch gerendert wird, `<script>` aber entfernt ist. 1 Commit.
3. Wenn NEIN: **NICHTS installieren, NICHTS blind auf {{ }} umstellen** (würde Rich-Text-Darstellung
   zerstören). Stattdessen nur im Bericht festhalten: Purifier fehlt, Fix braucht Nutzer-Entscheidung
   (Package installieren). Kein Commit.

---

**BERICHT:** P1-23b — Änderung + curl-Codes (C=403, A=ok) + Commit-Hash (oder Annahme/offene Frage).
P3 — Purifier vorhanden? umgesetzt/Commit ODER für Nutzer dokumentiert. Am Ende HEAD + ob der
Stabilitäts-Check unterwegs ansprang.