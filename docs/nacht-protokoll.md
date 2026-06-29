# Nacht-Protokoll — autonomer Stabilisierungslauf

**Start:** 2026-06-29, ~02:4x · **Modus:** Claude (Planer) ist alleiniger Ausführer via Sub-Agenten.
Nutzer schläft, ~6 h nicht erreichbar, volle Autonomie erteilt. Serielle Abarbeitung P1→P4.

## Leitplanken (Selbstbindung)
- Nur Stabilisierung (Crashes/Datenverlust/XSS/CSRF). KEIN Aufbau, KEIN Seeder-Umbau, keine Features.
- Pro Fix: Sub-Agent ausführen → ich verifiziere gegen fixierten HEAD + curl (A/B/C) → 1 Fix = 1 Commit.
- Keine Migration/Schema-Änderung, nichts Destruktives. Push nur `backup-private`.
- Bei Unsicherheit/Risiko: anhalten, hier dokumentieren, NICHT blind committen.

## Ausgangs-Ist-Aufnahme (fixiert)
- Branch `private/app-code-backup`, **HEAD `8c18761`** (letzter Fremd-Commit 02:35, Seeder).
- Working Tree sauber.
- **Test-Anker (Login per E-Mail, Passwort `demo1234`):**
  - A Admin: `markus.hoffmann@solar-aspekt-nord.test` (uid 126, is_admin=1)
  - B berechtigt: `claudia.neumann@solar-aspekt-nord.test` (uid 127, 14 rolls)
  - C ohne Rechte: `kevin.wagner@solar-aspekt-nord.test` (uid 128, 0 rolls)
- DB: 51 users / 51 employees (durch Fremd-Seeder-Umbau aufgebläht — Basis bleibt eingefroren).

## Status Pakete (Start)
- P0 (Sicherheit, 13 Befunde): ✅ komplett, verifiziert.
- P1-Block 1 (P1-20..25, Crash-Fixes): committet von Fremd-Instanz (01:56–02:09) — **noch von mir zu verifizieren**.
- P1-Block 2/3, P2, P3, P4: offen.

---

## Lauf-Log
(wird fortlaufend ergänzt)

### 1. Ist-Aufnahme — erledigt
HEAD `8c18761` fixiert, Anker A/B/C vorhanden (uid 126/127/128, Eigenschaften korrekt).

### 2. P1-Block 1 verifiziert (HEAD b9adbd9) — 5/6 grün
- P1-20 Übergaben ✅ (`/handover` live 200, war 500)
- P1-21 Rabattgruppen ✅ (Code korrekt; live nur 403 — Produkt-Verwaltung seit P0-08 admin-only, als B nicht erreichbar = gewollt)
- P1-22 Urlaubsanspruch ✅ (`/leave_day_view` live 200, war 500)
- P1-23 Qualifikation ⚠️ Crash behoben, aber Ownership/IDOR fehlt (Auftrag verlangte sie) → Nachbesserung offen
- P1-24 Lead-Objekt restore ✅ (Route+Methode da, GET 405 = nur POST)
- P1-25 Sprache löschen ✅ (DELETE-Route)
- BEFUND: HEAD wanderte 8c18761→b9adbd9 → Fremd-Instanz evtl. noch aktiv. Ab jetzt prüft jeder Ausführer-Agent ZUERST die HEAD-Stabilität.

### 3. Plan-Anpassung (Risiko nachts)
- Reihenfolge: **P2 Datenverlust** (klare Logik, kein Dependency-Risiko) → **P3 XSS** (nur wenn HTML-Purifier vorhanden, sonst für Nutzer dokumentieren) → **P1-23-Ownership-Nachbesserung**.
- BEWUSST für Nutzer liegen gelassen: **P1-Block 2** (DealController/OfferComment/Inquiry-Methoden = neue Geschäftslogik, unbeaufsichtigt zu riskant).

### 4. P2 Datenverlust — ✅ 4/4 committet (be02156, ddedae5, 24fb5d3, 8d1c3bd)
Ausführer-Selbstverif (tinker, DB-State vorher→nachher):
- P2-29 Inquiry: Published bleibt erhalten ✅; Draft→Unpublished (Default intakt) ✅
- P2-30 Lager-Storno: 100→Ausgabe30→70→destroy→100 ✅; Doppel-destroy geschützt (Annahme: Rückbuchung nur bei destroy, nicht update — begründet, da store unbedingt abbucht)
- P2-31 Urlaub: remaining 16→approve→14 ✅; 2. approve bleibt 14 (Doppel-Schutz) ✅
- P2-32 LeaveDay: Published bleibt erhalten ✅

### ⚠️ KRITISCH — Fremd-Instanz arbeitet WEITER (Annahme „nur ich aktiv" gilt nicht)
Während P2 schoben sich Fremd-Commits ein (`8b8278b` Seeder-Partnertypen, `13d76b9` UI-Fix); uncommittete Fremd-Änderung im Working Tree (`sidebar.blade.php`). Meine Commits blieben sauber (je 1 Datei, explizit `git add <datei>`, nie `-A`), aber Konfliktrisiko ist real — v.a. bei `routes/web.php`.
**MASSNAHME (Nacht):** nur noch **isolierte Controller-/View-Fixes**, die die Fremd-Instanz nicht anfasst. **Keine** routes-lastigen Pakete (P4 CSRF/GET→POST) und **kein** Package-Install (P3 XSS nur, falls Purifier schon vorhanden). Jeder Agent: Stabilitäts-Check + explizites `git add <datei>`. Bei wiederholter Instabilität → anhalten.
**FÜR DICH (Nutzer):** Bitte die andere Instanz stoppen, sobald du wach bist — paralleles Schreiben am selben Branch ist die Hauptgefahr; danach kann ich auch die routes-lastigen Pakete sicher fahren.

### 5. Runde 3 — ✅ erledigt (HEAD a438f58)
- P1-23b Ownership Qualifikation-Löschen: committet (a438f58), curl C→403 / A→200 ✅. P1-Block 1 = **6/6**.
- P3 XSS: Purifier NICHT installiert → korrekt nicht umgesetzt (kein Package-Install nachts). 5 Sinks dokumentiert (appointments/show:1635; problem/profile:2090,2174,2328; problem_edit:1254) → brauchen Nutzer-Entscheidung (mews/purifier).
- Stabilitäts-Check: ruhig.

### 6. Runde 4 — ⛔ INSTABIL, gestoppt (kein Code geändert)
Stabilitäts-Check sprang an: HEAD a438f58→609418f in 90 s → Fremd-Instanz committet weiter. Korrekt nichts geändert. **Code-Fixes nachts blockiert, solange die andere Instanz läuft.**
Vorarbeit liegt bereit (für ruhigen Branch): P5-44 = 11× `(int)name` in GeneralTaskController (Z.24,130,207,267,373,536,803,819,828,913,998; Z.422 schon employeeId); P4-41 = UserController Z.145 + Z.310 (`?1:1`); je nullable→employeeId direkt, NOT-NULL→Null-Guard.

### 7. Controlling-Bestandsaufnahme — ✅ fertig (committet 5b37e55)
docs/controlling-bestandsaufnahme.md (Teil A/B/C, ~40 Belege). Kernbefunde: kein Kostenstellen-Feld (0 Treffer); Umsatz nur über Auftragsschiene an Abteilung knüpfbar (invoices ohne department_id → 2 Erlös-Schienen); Kosten je Filiale, nicht je Abteilung (keine AfA, keine km); keine Umlage-Logik; keine DATEV in ticket (FiBu/cost_centers im playground-Prototyp). Fachliche Klärung GF/Steuerberater im Dok markiert.

---

## MORGEN-ÜBERSICHT (Stand bei Nutzer-Rückkehr)

**Sicher erledigt + verifiziert diese Nacht (durch mich):**
- P2 Datenverlust 4/4 (be02156, ddedae5, 24fb5d3, 8d1c3bd) — Inquiry-Status, Lager-Storno-Rückbuchung, Urlaubstage-Dekrement, LeaveDay-Status.
- P1-23b Ownership Qualifikation-Löschen (a438f58) — curl C→403/A→200. P1-Block 1 damit 6/6.
- Controlling-Bestandsaufnahme (5b37e55) — read-only.

**⛔ Code-Stabilisierung BLOCKIERT:** Die Fremd-Instanz committet die ganze Nacht weiter (Seeder-Ausbau, Partner-UI) → Stabilitäts-Check schlägt an → ich kann keine weiteren Code-Fixes nacht-sicher fahren. **Bitte die andere Instanz stoppen**, dann läuft die Stabilisierung sofort weiter.

**Runde 4 — ✅ erledigt (Branch war ruhig):**
- P5-44 GeneralTaskController (d7d0f93): 11× (int)name→employeeId(), Null-Guard für NOT-NULL-Spalten (Admin nicht aussperren).
- P4-41 UserController (a1471d2): is_active-Toggle korrigiert (Z.145/310).

**Tag-Modus ab hier:** Nutzer arbeitet mit, Fremd-Instanz gestoppt → Code-Pakete laufen weiter. Ich entscheide selbst, frage nicht.

**P3 XSS — ✅ erledigt:** mews/purifier installiert (24b4a7a), 5 Stellen mit clean() abgesichert (3941dc4) — appointments/show + problem/profile + problem_edit. Beleg: `<script>` raus, `<b>` bleibt.

### 8. Kaputte Funktionen — ✅ erledigt
- Externe Firma anlegen (6840519): Anlege-Formular postet jetzt auf external.store statt update.
- Angebots-Kommentare (e9d8195): 4 Routen unter eigenem Prefix /offer-comments (auth) registriert, JS-URLs angeglichen. Ausgelassen: count-Badge (Controller hat keine count-Methode, würde neue Logik brauchen).

### 9. Lösch-Funktionen absichern — ✅ erledigt (33a4299, ee0a309)
9 GET-Lösch-Routen → DELETE (Produkt 3, HR 6), zugehörige Views auf @csrf+@method('DELETE') umgebaut (inkl. JS-Sonderfall country). curl: alte GET-URLs → 405, Listenseiten 200.

### 10. Anfragen-Tafel (Kanban Drag&Drop) — ✅ updateStatus repariert (d851c79)
Drag&Drop setzt jetzt inquiries.status (Allow-Liste, Rechteprüfung P0-09). curl: gültig→200, ungültig→422 (kein 500 mehr).
**Für Nutzer festgehalten (Geschäftslogik nötig, NICHT geraten):**
- reverify: was passiert mit alter Lead/Brand-Entität bei Typwechsel? (verwaiste Daten?)
- publish: nur status='Published' oder volle Verifizier-Logik? (kein Frontend nutzt die Route)
- storeFromAI (KI-Mail-Import): Pflichtfelder/Defaults (branch_id, contact_person sind NOT NULL ohne Default!), Standard-Status, Mail-Verknüpfung.

### 11. Dashboard-Untersuchung — ✅ fertig (docs/dashboard-konzept.md, 73b31df)
11 Dashboards gefunden. Top-Schwächen: Absturz-Tabs (projects/offers, getTabCounts); Datenschutz (Umsatz/Forderungen/fremde Kalender ohne Rechte; Krankmeldungs-Docs öffentlich in public/); Riesen-View 13k Z + index/mobile-Duplikat; tote Links + Demo-Namen; falsche Zahlen + externe Wetter-Abhängigkeit. Konzept-Vorschlag: 3 rollenbasierte Dashboards (MA/Teamleitung/GF). GF-Entscheidungen (KPIs, Sichtbarkeit) im Dok markiert.

### 12. Dashboard — akute Fixes
- ✅ Abstürze behoben (973c9a8): getTabCounts nutzt PersonalTask/MainAppointment statt nicht-existenter Task/Appointment; tote projects/offers-Tabs entfernt; try/catch-Auffangnetz → load-tab/tab-counts liefern 200 statt 500 (curl belegt). Hinweis: loadTabContent ist totes Feature (Partials nach Old/ verschoben).
- ⚠️ Krankmeldungs-Docs öffentlich: ERFASST, NICHT geändert (zu riskant). Dateien liegen direkt unter public/images/employees/sick_documents/, ausgeliefert via asset() OHNE Controller/Auth dazwischen → curl ohne Login → 200 (Gesundheitsdaten frei abrufbar). Lokal nicht akut (App nicht deployed), aber **Go-Live-Blocker**. Sauberer Fix = größerer Umbau (Upload nach storage/private + geschützter Download-Controller + Anzeige umstellen + bestehende Dateien/DB-Pfade migrieren) — betrifft ECHTE Daten, braucht Sorgfalt + Nutzer-Freigabe. Details im Agent-Befund.

---

### 13. Spur Personal/HR/Lager — P1-Crash-Runde ✅ abgeschlossen (HEAD ba63c7c)
GEFIXT (500→200): Übergaben-Mehrfachansicht (47c8136, Formular zeigte auf nicht-existenten AssetsController); Mitarbeiter-CV (ba63c7c, INNER→leftJoin, crashte bei fehlendem Branch/Vertragstyp/Land).
ZURÜCKGESTELLT (verwaiste Routen ohne UI-Link, Designfrage Yama): teams.create/show, employee.info, employees.show, employee.sick.index. Lager-Detailseiten nicht testbar (keine Daten).
OFFENE ECHTE NUTZER-CRASHES: keine mehr in der Spur. Bitrix/NIBE/IMAP ignoriert.

---

### 14. Parallel-Runde (4 Spuren gleichzeitig, konfliktfrei) — ✅ 10 Crash-Fixes
Konfliktvermeidung: jede Spur nur eigene Dateien, routes/web.php nur vom CRM-Agenten. Alle 10 Commits sauber, kein Verlust (git-verifiziert).
- Anfragen/Leads (2): Lead-Details-Edit Blade-Syntaxfehler (994a4ae); Leadliste Status-Filter $productInfo (646bbf0).
- Vertrieb (1): OfferController@show ergänzt (2745614).
- Partner (3): global-restore SoftDeletes-Guard (6d0f8e6); all-contacts Suche falsche Spalten (210a5c6); brand departments address-Spalte (0012272).
- Finanzen (4): AssetInstallment Asset-Modell+Null-Guards (17726dd), Edit-Action (0f01003), Create-Action (1d17ac6), update Null-Deref (3f8297d).
ECHTE OFFENE CRASHES (zurückgestellt = Geschäftslogik): Aufträge Löschen/Junk/Unjunk/Restore (DealController-Methoden fehlen); Anfragen reverify/publish/storeFromAI. Tote/unverlinkte Routen (new_lead_product, lead_new_sort, lead/references) = kein Nutzer-Crash.

---

## OFFEN — braucht Nutzer-Vorgaben (nicht von mir geraten)
- **3 rollenbasierte Dashboards** (MA/Teamleitung/GF) + GF-Cockpit-KPIs → welche Zahlen, wer sieht was.
- **Krankmeldungs-Docs-Schutz** (Umbau wie oben) → Freigabe wegen echter Daten.
- **Anfragen-Knöpfe** reverify/publish/storeFromAI → fachlicher Ablauf.
- **P3 XSS** war erledigt; **P1-Block 2 DealController** (Auftrag-Anlegen) offen → neue Geschäftslogik.
- **Umsatz/Forderungen im Dashboard** ohne Rechteprüfung → wer darf das sehen (GF-Entscheidung).

**Braucht DEINE Entscheidung (nicht nachts raten):**
- P3 Stored XSS (5 Stellen): mews/purifier installieren? (sonst nicht sauber lösbar)
- P1-Block 2 (Auftrag-Anlegen/DealController, OfferComment, Inquiry-Kanban-Methoden): neue Geschäftslogik → tagsüber mit dir.
- P4 (CSRF/GET→POST, ~30 Routen): routes-lastig → braucht ruhigen Branch.
