# INTELLIGENZ-AUDIT — Automatisierungsgrad & System-Intelligenz (ticket CRM)

> **Status:** READ-ONLY-Audit, Stand 2026-07-09. Repo `/Users/yamanuri/Documents/ticket`, Branch `private/app-code-backup`.
> **Frage:** Wie weit ist ticket ein *mitdenkendes* System (Kausalität, Plausibilität, Konsistenz, Routing, Redundanz) — und wo ist es eine *stumme Datenbank*, die Handarbeit verlangt oder Unsinn durchlässt?
> **Zieldatei:** Dies ist die einzige von diesem Auftrag geschriebene Datei. Parallele Audits (`00-index.md`, `01-fehler-inventur.md`, `02-architektur.md`, `03-swot.md`, `code-audit.md`, `p*-*.md`, `docs/agents/**`) wurden **nicht** angefasst.

## Leitprinzip (bindend) — „sinnvoll" ≠ „maximal" automatisiert

Jeder Fund trägt ein Urteil:
- **(a) SOLL automatisiert** — sicher ableitbar, spart Arbeit, keine Fach-/Rechtsentscheidung.
- **(b) DARF NICHT voll automatisiert** — Fach-/Rechtsentscheidung → nur **Vorschlag + Bestätigung** (Operanden-Gate: kein erfundener Wert; bei Unsicherheit fragen/markieren statt raten).
- **(c) IST automatisiert, aber FALSCH/RISKANT** — bestehende Automatik rechnet/handelt still falsch.

**Operanden-Gate (Referenz-Fall):** `FormulaEvaluationService` rechnet **nicht** mit fehlenden Operanden weiter (→ Status `unvollstaendig`, kein Wert) und markiert geschätzte Operanden als **nicht-verbindlich** (`enthaelt-ungepruefte-werte`). Das ist der Maßstab, an dem alle anderen „rechne einfach mit Default weiter"-Stellen gemessen werden.

## Beleg-Disziplin

Jeder Fund trägt Datei:Zeile oder eine SQL-Messung. Hochrechnungen und **NICHT-VERIFIZIERT**-Punkte sind markiert. Datenbasis-Warnung: die lokale Dev-Restore ist zu ~82 % leer (363/442 Tabellen 0 Zeilen); Konsistenz-SQL läuft nur auf den lebendigen Kern-Tabellen (`new_leads` 52, `lead_product_lists` 52, `offers` 29, `deals` 14, `invoices` 11). Aussagen zu „passiert in der Praxis" sind daher strukturell (Code) + Stichprobe (Daten), nicht volumengemessen.

**TABU (nur gelesen, nicht bewertet):** Nuriva-APIs, Video/Jitsi, Invoice-Zone (interne Rechnungserzeugungs-Qualität), Legacy Bitrix/NIBE/IMAP. Wo unten „Abnahme→Rechnung" steht, ist die **fehlende Brücke** bewertet, nicht die Qualität der Invoice-Zone.

---

# ACHSE 1 — KAUSALITÄT (löst Ereignis A automatisch Folge B aus?)

**Vorbefund (wichtig):** Keine Kern-Entität feuert eine *fachliche* Folge über Model-Hook/Observer/Event. Alle Hooks sind rein technisch: `Deal::creating` vergibt nur `order_number` (`app/Models/Deal.php:40-50`), `Offer::creating` nur `offer_no` (`app/Models/Offer.php:31-50`), `Invoice::saving` reserviert Nummer + Lösch-/Status-Guard (`app/Models/Invoice.php:21-51`), `LeadProductList::booted` leitet nur `lead_stage_id` ab (`app/Models/LeadProductList.php:112-138`). `EventServiceProvider` (`:17-48`) macht nur Login/Logout-Log + `LeadRecordChanged→StoreLeadActivity` (Aktivitäts-Log). **Kein `::observe()` im gesamten `app/`.** → **Jede Folge muss explizit im Controller stehen; es gibt keine stillen Automatik-Hooks.** Das ist die strukturelle Ursache, dass fast alle Kausalitäten unten „manuell" sind.

| # | Ereignis | Erwartete Folge | Passiert | Beleg | Klasse |
|---|---|---|---|---|---|
| K1 | Lead→Angebot | Aufgabe/Reminder „Angebot erstellen" | **NEIN** | `LeadOverviewController.php:5140-5142` schreibt nur status/stage/history; 0 Offer/Task-Creates im Stage-Move | (a) |
| K2 | Angebot→Auftrag | Kalkulation/Auslegung + Materialliste + Kanban-Tasks/Projekt anstoßen | **HALB** — nur `deals`-Zeile | Auto: `LeadOverviewController.php:5646→5837` (Raw-INSERT); manuell: `DealController.php:3686`. Danach nur `lead_product_lists`-Update `:3702-3709`. Keine Calc/Materialliste/Projekt/Task | (a)+(b) |
| K3 | Auftrag→Montage | Montageplan/Einsatzplanung | **NEIN — bewusst manuell** (Yama-Entscheid) | Manueller Einstieg `PlannerPlanController.php:7158` (`storeProjectFromLeadProduct`, verlangt Bestätigung bei nicht-Montage `:7173-7179`). Kein Auto-Trigger | korrekt (b-by-design) |
| K4 | Abnahme→Rechnung | Rechnungs-**Entwurf** (invoices) vorschlagen | **NEIN** | Invoice nur manuell: `InvoiceController.php:218`, `InvoiceCanvasController.php:56`. Kein Pfad aus Abnahme/Abschluss-Eintritt | (b) |
| K5 | Storno (Deal) | Rechnungs-Storno + Lead-Stufen-Reset | **JA (destroy) / HALB (junk)** | `DealController.php:3756-3757` (destroy klammert beides atomar); `:3849` offen→`storniert`, bezahlt→`storniert_bezahlt_pruefen` + Warnung `:3764`; **junk `:3727` setzt NUR Stufe zurück, storniert Rechnungen NICHT** | (c) für junk-Asymmetrie |
| K6 | Termin/Bericht→Follow-up | `FollowUpCreator::sync` feuert | **JA, 5 Aufrufer — aber Lücken** | s. Tabelle unten | (a) für Lücken |

### K5 — Storno: die *gute* Automatik (mit korrektem Operanden-Gate) + ein (c)-Bug
Der Storno-Pfad `destroy()` ist ein Vorbild: bezahlte Rechnungen werden **nicht** still gelöscht, sondern auf `storniert_bezahlt_pruefen` gesetzt + Warnung „bitte buchhalterisch prüfen (Rückzahlung/Gutschrift)" (`DealController.php:3823-3827,:3764-3766`). Das respektiert das Operanden-Gate exakt: die buchhalterische Folge ist Fach-/Rechtsentscheidung → **markieren statt raten**. **Aber:** `junk($id)` (`:3718-3729`) macht die Rechnungs-Rückabwicklung **nicht** — nur `restoreLeadStageForDeal`. Wird ein Auftrag „gejunkt" statt „gelöscht", bleiben offene Rechnungen aktiv, obwohl der Auftrag storniert ist. → **(c) inkonsistente Storno-Wege** (destroy vollständig, junk halb).

### K6 — FollowUpCreator: 5 Aufrufer, 2 tote Herkunfts-Slots, Planner-Lücke
Der Service (`app/Services/FollowUp/FollowUpCreator.php:49-92`) ist die *eine* Erzeugungsstelle (Upsert per `(source_type, source_id)`, `next_reminder_at=NULL` → dashboard-only). Feuert aus:

| Aufrufer | source_type | Bedingung |
|---|---|---|
| `MainAppointmentController.php:1259` | main_appointment | nur bei `reminder_date && next_step && report_responsible` |
| `MainAppointmentController.php:2587` | main_appointment | Report/Complete-Pfad |
| `CustomerReportController.php:61` | customer_report | nur Outcome ∈ {nachfass, weitere_aufgaben} |
| `CustomerNoteController.php:395` | customer_note | `createFollowUpFromNote` |
| `KanbanLeadTaskController.php:657` | kanban_lead_task | nur Büro-Direkt-`done` + Outcome |

**Feuert NICHT** aus Stage-Move und **NICHT aus dem Planner/Tagesbericht** (der Planner spiegelt Reports separat via `storePlannerItemReportMirror`, ruft `FollowUpCreator` aber nicht). **Tote Slots:** `SOURCE_TYPES` (`:23-31`) deklariert zusätzlich `lead_product_list` + `appointment_report` — **kein** `sync()`-Aufrufer dafür. → **(a) Follow-up-Erzeugung greift „nur an Notiz/Termin/Bericht", nicht durchgängig** (Feld-/Tagesbericht des Monteurs erzeugt kein Follow-up).

### Kausalitäts-Fazit
Der einzige *fachliche* Automatismus, der zuverlässig greift, ist **K5-destroy** (Storno-Rückabwicklung) und **K6** (Follow-up, teilabgedeckt). Alle Prozess-Vorwärts-Kausalitäten (Lead→Angebot, Angebot→Auftrag-Kickoff, Abnahme→Rechnung) sind **Medienbrüche**: der Nutzer muss selbst daran denken, den nächsten Schritt anzustoßen. K2 legt zwar die `deals`-Zeile an, stößt aber **keinen** der realen Folgeprozesse (Materialliste aus Auftragsbestätigung, Kalkulation, Kanban-Aufgaben) an — genau die Kette, die Yamas Prozessbeschreibung als „nach Auftragsbestätigung stehen Produkte fest → Materialliste" beschreibt.

---

# ACHSE 2 — PLAUSIBILITÄT (fängt das System Unsinn ab?)

**Gesamtbild:** Die *junge* Zone (Energie/Heizlast/Anforderungsprofil/Form) plausibilisiert echt (Bänder, Operanden-Gate, Registry-Ablehnung). Der *alte* Kern (Angebot/Auftrag/Heizkreis/Inventar/Kanban) macht fast nur Typprüfung. `app/Http/Requests` ist leer (0 FormRequests) → alle Validierung inline, lückenhaft.

### P1 — Negative Mengen/Beträge: KEINE globale Regel (c/a)
Enforcement hängt am Einzel-Controller. Vorhanden in der jungen Zone (`PlannerItemMaterialController.php:77` `min:0.001,max:999999`; `Heizkoerper/HeizkoerperController.php:51` `anzahl min:1`; `CostingSetController` dutzende `min:0`). **Fehlt** u.a.: `HandoverController.php:131` (`quantity` nur `required`), `PurchaseRequestController.php:195` (`nullable|numeric`, kein min → Negativ passiert), `DistributorPriceController.php:315` (`price required|numeric`, kein min:0 → Negativpreis passiert), `ExternalPersonalController.php:60` (`price required`, gar kein numeric). Im gesamten Angebots-/Auftrags-Kern nur **ein** `min:0` gefunden (`DealController.php:2218` `planned_hours`). → **(a)** für Menge≥0 (sicher ableitbar); **(b)** für Preis (Negativpreis = Gutschrift/Rabatt kann legitim sein → Regel je Kontext, nicht blind blocken).

### P2 — Heizlast-Band: existiert, ist aber nur Warnung (b, gut gebaut — aber nicht durchgesetzt)
`HeizlastNormwerte::plausi()` (`app/Services/Heizlast/HeizlastNormwerte.php:58-66`) liefert ein echtes Plausi-Band mit `warnung`-Flag (`<10` „sehr niedrig – Eingaben prüfen", `>120` „sehr hoch"), verdrahtet in `HeizlastRechner.php:60,:127`. **Korrekt (b):** ein Fachwert soll *nicht hart blockiert*, sondern markiert werden. **Limitierung:** rein advisory (nie Block), Band 10–120 statt der genannten 1–500 (alles 120–500 fällt in einen einzigen „zu hoch"-Kübel). Input-Seite gut gebändert (`Energie/HeizlastController.php:99-108`: `grundflaeche_m2 min:1`, `u_wert 0.01–10`, `baujahr 1800–2100`).

### P3 — PlausibilityService: vollständig gebaut, ZERO Aufrufer (a — dead code aktivieren)
`app/Services/Form/PlausibilityService.php` implementiert reiche Heuristiken (negative Fläche/Länge → Warnung `:68-74`, Menge 0 `:77-83`, Raumhöhe außer 1.5–4 m `:86-95`, Einheiten-Mix `:99-106`, per-Feld min/max `:142-182`). **Kein Aufrufer** (`grep PlausibilityService` in `app/` = nur die Klasse selbst). → **(a) Die Plausibilisierung ist da, aber dormant** — Wiring = geringer Aufwand, sofort Nutzen.

### P4 — Vorlauf/Rücklauf: Alt-Pfad ungeprüft (a)
`CustomerHeatingCircuitController.php:40-41,:115-116`: `flow_temperature` / `return_flow_temperature` nur `required|numeric` — **Rücklauf > Vorlauf, Negativ-Temperaturen, 9000 °C speicherbar**. Der **neue** Pfad ist strukturell sicher (`RadiatorPerformanceService.php:30` leitet Rücklauf = Vorlauf−Spreizung ab, Spreizung>0 erzwungen `Heizkoerper/HeizkoerperController.php:52-54`). → **(a)** Alt-Pfad um `flow>return` + Temperatur-Band ergänzen.

### P5 — PLZ-Fallback: still gerechnet mit Default-Norm-Temperatur (c — Operanden-Gate verletzt)
PLZ nur `nullable|string|max:10` (`Energie/HeizlastController.php:94`, `SanierungController.php:110`), keine 5-Stellen-Prüfung. Bei Miss liefert `KlimaPlzService` null und der Aufrufer **fällt still auf Default** zurück: `Energie/HeizlastController.php:127` `?? -8.5`, `HeizlastRechner.php:30` `norm_aussentemp_c` still `-12.0`. → **(c) Das System rechnet mit einem erfundenen Norm-Klima weiter, ohne es zu markieren** — genau der Operanden-Gate-Bruch. Soll: „PLZ unbekannt → Default −Xk verwendet (Annahme, nicht verbindlich)" flaggen (wie es die Anforderungsprofil-Zone mit `datenlage='geschaetzt'` bereits vormacht).

### P6 — Übergangs-Plausibilität: freie Phasensprünge, nur ein (umgehbares) Gate (b)
Kanon-Reihenfolge `LeadOverviewController.php:3038-3046` (lead→offer→follow_up→accepted→deal→project→completed). **`moveStageWorkflow` erzwingt KEINE Reihenfolge:** Validierung (`:4904-4917`) prüft nur, dass Ziel-Keys *existieren*; `stageExists` (`:2818`) lässt **jede** aktive Stufe als Ziel zu. Einziges Ordnungs-Gate: `requiresAcceptedOfferBeforeEnteringDeal` (`:5330`) beim Eintritt in `deal` — **und das ist per Request-Flag umgehbar** (`skip_offer_gate_without_folder` → `offer_acceptance_status='moved_without_offer_acceptance'` `:5108`). **„Abnahme ohne Montage" / Lead→completed-Sprung ist NICHT verhindert.** → **(b):** Reihenfolge ist Geschäftsregel (Weiche 2 = „flexibel mit Warnung"). Also nicht hart blocken, aber **Rückwärts-/Übersprung markieren** (heute unsichtbar außer beim Deal-Gate).

### P7 — Rechen-Plausibilität in der Form-Engine: GOLD (b, vorbildlich)
`FormulaEvaluationService` (`app/Services/Form/FormulaEvaluationService.php`): fehlender Operand → `unvollstaendig`, kein Wert (`:129-132`); geschätzter/ungeprüfter Operand → rechnet, aber `verbindlich=false` (`:157-164`); Division durch 0 → kein Wert (`:144-147`); unbekannte Bezeichner (`alert`, `require`, `__proto__`) als fehlender Operand behandelt, nie ausgeführt (`:330-341`). `AnforderungsprofilWert::saving` (`:33-51`) wirft bei unbekanntem `schluessel`/fehlendem `wert_num`/ungültiger `datenlage`. **Das ist der Referenz-Standard** — der Rest des Systems (P5, P1) sollte dahin.

---

# ACHSE 3 — KONSISTENZ (widersprechen sich Daten/Zustände nie?)

### C1 — `deals`: fünf parallele Status-Felder, Freitext-Werte (c)
Schema-Messung: `deals` trägt `status`, `deal_status`, `measurement_status`, `project_status`, `status_msg`. Daten-Stichprobe:
```
status  project_status   c
active  Montage          5
active  offen            4
active  in Bearbeitung   3
active  abgeschlossen    1
deal    NULL             1
```
→ 13× `active`, 1× `deal` (der von `DealController::dealStore` gesetzte Wert). `project_status` trägt Freitext-Deutsch (Montage/offen/in Bearbeitung/abgeschlossen). **Keine führende Wahrheit auf Auftrags-Ebene** — der Fortschritt eines Auftrags lebt gleichzeitig in `deals.status`, `deals.project_status` und `lead_product_lists.stage`. (Weiche 1 adressiert genau das, noch nicht gebaut.)

### C2 — Auftrag „active" trotz voll bezahlter Rechnung (a — keine Rück-Ableitung)
SQL: 4 Aufträge stehen auf `status='active'`, obwohl ihre `invoices` voll bezahlt sind (`paid_amount=total_amount`):
```
deal 17 active | inv paid 20.785,73
deal 18 active | inv paid 31.838,45
deal 24 active | inv paid 21.390,25
deal 28 active | inv paid 18.891,25
```
Es gibt **keine Rück-Kausalität** Zahlungseingang→Auftragsstatus. Der Auftrag „weiß" nicht, dass er finanziell abgeschlossen ist. → **(a)** Ableitung/Flag „vollständig fakturiert & bezahlt" (sicher aus `invoices` ableitbar).

### C3 — `lead_stage_id`-Ableitung dreifach, Divergenz latent (c)
Aus dem Code-Audit bestätigt: Kanon `LeadProductList::deriveLeadStageId()` (`:144-175`, mit Fold `follow_up→offer`, `accepted→deal`) vs. `NewLeadsController::normalizeCompanyStage()` (`:12977-13004`, **ohne** Fold) vs. dritte `match()`-Variante mit Tippfehler-Key `archiv` (`:9791-9805`). **Aktuell in den Daten konsistent** (SQL: alle 52 `lead_product_lists` haben korrekt abgeleitetes `lead_stage_id`, 0 NULL; `follow_up→2`, `accepted→5` = Fold greift) — weil hier über den Hook geschrieben wurde. **Divergenz-Risiko real**, sobald ein Schreibweg `normalizeCompanyStage` nutzt: derselbe Status liefert dann einen anderen `lead_stage_id`. → **(c) still divergierbare Zweit-Wahrheit** an der Quelle.

### C4 — `new_leads.status`-Zoo (c, mild)
```
Lead 26 | Active 24 | QUALIFIZIERT 1 | Von Junk wiederhergestellt 1
```
Gemischte Sprache/Groß-Kleinschreibung, ein Status-Wert ist faktisch ein Log-Satz („Von Junk wiederhergestellt"). Kein Werte-Kontrakt → jede Auswertung, die `status` gruppiert, zerfällt.

### C5 — Storno-Asymmetrie destroy↔junk (c) → identisch mit K5, hier als Konsistenz-Sicht: nach `junk` können Auftrag (storniert) und Rechnung (aktiv) widersprüchlich auseinanderlaufen.

### Konsistenz-Fazit
Die **Kanban-Kette** (`lead_product_lists` status/stage/lead_stage_id) ist dank Hook heute konsistent (Stichprobe sauber). Die **Auftrags-/Finanz-Kopplung** ist es nicht: fünf Status-Felder auf `deals`, kein Rücklauf von Zahlung/Rechnung auf Auftragsstatus, kein Werte-Kontrakt. Quer-Konsistenz-Prüfungen (SQL-Widerspruchs-Checks) existieren im System **nicht** als Wächter — sie müssten erst gebaut werden.

---

# ACHSE 4 — ROUTING (kommt Richtiges automatisch zum Richtigen?)

### R1 — Aufgaben-Routing: manuell / Ersteller-Default, trotz vorhandener Anker (a/b)
- `FollowUpCreator.php:51-54`: Verantwortliche = `$data['responsible']`, sonst **Fallback `[creatorEmployeeId]`** („Default = Ersteller"); `assigned_by`=Ersteller (`:67`).
- `PersonalTaskController::store` (`:799-838`): Zuständige/Controller direkt aus dem Request (manueller Multi-Picker), `controller_id` fällt auf die Zugewiesenen zurück, nie auf eine Rollen-/Abteilungsregel; `assigned_by=auth()->user()->name` (`:833`).
- `KanbanLeadTaskController.php:442`: `performer_employee_id ?? $employeeId` — manuell, sonst aktueller Nutzer.

**Schema *hätte* die Anker** (`kanban_lead_tasks` trägt `lead_stage_id`, `task_phase_id`, `phase_activity_id`, `product_id`), aber **keiner wird zur Bearbeiter-Wahl genutzt**. → Arbeit landet bei dem, der sie anlegt (oder handverlesen). **(a)** wo ein Gewerk/eine Abteilung einen definierten Owner hat (sicher ableitbar) → Default-Zuweisung; **(b)** die finale Zuweisung darf überschreibbar bleiben (Vorschlag + Bestätigung).

### R2 — Formular-/Aktions-Routing (Smartrouting): der intelligente Router ist TOT, live läuft ein naiver Produktfilter (a)
`SmartroutingService` (`app/Services/Form/SmartroutingService.php:19`) implementiert **genau** das gewünschte Kontext-Routing: Match auf `article_group_id` (Gewerk) + `lead_product_list_id` + `object_type` gegen `product_formula_routing_rules`, mit Spezifitäts-/Prioritäts-Tiebreak + Fallback. **Null Aufrufer** (grep über `app/`, `resources/`, `routes/` = nur die Definition). Der **live**-Pfad ist naiv: `LeadProductChecklistValueController.php:41,:116` macht `ProductFormula::where('product_id', …)->get()` — Filter nur nach Produkt, **kein** `object_type`, keine Phase, keine Priorität, keine Mehrdeutigkeits-Auflösung, kein Fallback. → **(a) Die Smart-Routing-Fähigkeit ist gebaut (inkl. Regel-Tabelle), aber nicht verdrahtet** — Wiring = geringer Aufwand, hoher Nutzen; heute „sieht jeder alles" pro Produkt statt kontextgenau.

### R3 — Benachrichtigungs-Routing: kein Push für Follow-ups, nur In-App, fragile Kopplung (b/c)
- `ProcessPersonalTaskScheduler.php:31-71` **eskaliert** Tasks mit `reminder_date` **und** `reminder_time` (`notifyAssignedEmployees`). **Follow-ups qualifizieren nie:** `FollowUpCreator` lässt `reminder_time`/`next_reminder_at` NULL (`:65-66`), Scheduler filtert `whereNotNull('reminder_time')` (`:37`) → **Follow-ups sind dashboard-only** (bestätigt die MEMORY-Entscheidung Stufe-1).
- Kanal **nur In-App**: `PersonalTaskReminderNotification::via()` = `['database']` (`:20-22`) + Broadcast. Kein Mail. → Auch geplante Tasks erreichen nur eingeloggte, hinschauende Nutzer.
- **Fragile Kopplung:** `notifyAssignedEmployees` matcht Mitarbeiter↔User über `User::whereIn('name', employeeIds)` (`:210-214`) — Mitarbeiter-ID im `name`-Feld; bricht still, wenn die Zuordnung kippt → **(c)** Benachrichtigungen können lautlos verloren gehen.
- `OverdueCenterController.php:26-38` ist ein **Pull**-Report (Nutzer öffnet die Seite; merged Überfälligkeiten >48h in-memory), eskaliert nicht von selbst.
- **Toter Zweit-Mechanismus:** `app/Jobs/ScheduleTaskReminder.php` importiert (`PersonalTaskBoardController.php:14`), aber **nie `dispatch()`** → zwei konkurrierende Reminder-Wege, einer aufgegeben.

→ **(b)** für den Push selbst (Yama hat Stufe-1 dashboard-only bewusst gewählt — kein Fund, sondern Entscheidung; die Eskalations-Stufe kommt später). **(c)** für die fragile Name-Kopplung + den toten Job (Aufräum-/Härtungs-Sache).

### Routing-Fazit
Zwei fertig gebaute Intelligenz-Schichten (`SmartroutingService`, Task-Anker im Kanban-Schema) liegen **ungenutzt** brach, während die Live-Pfade manuell/naiv sind. Der billigste Intelligenzgewinn des ganzen Audits liegt hier: **Vorhandenes verdrahten**, nicht neu bauen.

---

# ACHSE 5 — REDUNDANZ (mehrfach getan/gespeichert/erfasst?)

### Rd1 — Erfassungs-Redundanz Heizlast ↔ Aufmaß: teuerster Schmerz (a)
`Energie/HeizlastController.php` baut ein **transientes Ein-Zonen-Projekt** (`:23`, `:170` „TRANSIENT: Rechner-Tool"). Eingaben werden **frisch getippt** und roh validiert (`raum.grundflaeche_m2`, `raum.hoehe_m`, `bauteile.*.flaeche_m2` `:99-107`), dann in `HeizlastRaum::create([...])` (`:147-162`) geschrieben. Der Controller importiert **nur** `HeizlastRaum` (`:8`) — **kein** Bezug auf `DealMeasurement`/`deal_measurements`. **Kein Prefill, keine Durchreichung.** Dieselben Gebäude-/Raummaße stehen bereits im Aufmaß (`deal_measurements` + `sections_snapshot`). → **Maße müssen zweimal getippt werden**, und weil Heizlast transient ist, wird das Getippte nicht mal zurückgespeichert. **(a)** Prefill aus dem Aufmaß (sicher: gleiche physische Größe, gleiches Objekt). **NICHT-VERIFIZIERT:** ob ein Blade/JS die Heizlast-Form clientseitig vorbefüllt (serverseitig definitiv nicht).

### Rd2 — Adress-Doppelablage Kunde ↔ Objekt (a, mild)
`new_leads` trägt `street/postcode/city/full_address`; `lead_alternative_adds` (Objekt) trägt **ebenfalls** `street/postcode/city/full_address`. Wenn das Objekt = Wohnadresse des Kunden ist (Regelfall Einfamilienhaus), wird dieselbe Adresse zweimal erfasst, ohne Sync. **(a)** Objekt-Adresse aus Kunde vorbefüllen (mit „abweichend"-Option). *Positiv:* die Beleg-Kette (`offers`/`deals`/`invoices`) speichert **keine** denormalisierten Namen/Adressen — sie referenziert per FK (`offers.customer_id→new_leads`, `alternative_id→lead_alternative_adds`). Nur `deals.location` (Ort-String) + `order_number`/`offer_number` sind Bequemlichkeits-Kopien. → Diese Achse ist im Beleg-Fluss **sauber**.

### Rd3 — Snapshot-Redundanz ohne dokumentierte Invalidierung (b/c-Risiko)
`deal_measurements.sections_snapshot` + `material_summary`, `offer_details.biography_data` (JSON): Point-in-time-Kopien von Angebots-/Material-Daten. Bei Belegen legitim (eingefrorener Stand), aber ohne dokumentierte Invalidierung droht Divergenz Snapshot↔Quelle (**NICHT-VERIFIZIERT** je Feld, ob gewollt eingefroren oder stale).

### Rd4 — Prozess-Redundanz: mehrere Angebots-Wizards (a — Aufräumen)
Mehrere sehr große, nahezu duplizierte Konfiguratoren koexistieren: live `offer/configuration/offer/config.blade.php` (~1,16 MB, geroutet `OfferWizardController.php:63`) **+** `config.blade copy.php` (~841 KB) **+** `offer/old/config.blade.php` (~974 KB) **+** `wizard-smart.blade.php`, `configuration/offer/Old/`, `offer/set/old code/`. → 3–4 parallele Multi-hundert-KB-Wizards = Wartungs-/Divergenz-Gefahr. (Überschneidet Code-Audit „toter Ballast"; hier als Prozess-Doppelung relevant.)

### Rd5 — Prozess-Redundanz: parallele Kanban/Phasen-Systeme (Weiche 1/6, offen)
Eine „Phase" existiert gleichzeitig in `lead_stages`(+`_sub_stages`), `kanban_lead_tasks`, `planner_items` und historisch `customer_phase_lists` (bereits gedroppt `2026_07_02_100000`). Cross-Sync-Brücken (`add_kanban_lead_task_id_to_planner_items`) sind je ein Divergenzpunkt. Das ist die Wurzel der `lead_stage_id`-Dreifachheit (C3). *Bereits durch Weiche 1/6 adressiert* (lead_stages = Phasen-Wahrheit, planner_items = Feld-Wahrheit) — hier nur als Redundanz-Beleg gelistet.

### Rd6 — Zwei Rechnungs-Schienen `invoices` ↔ `deal_invoices` (bereits entschieden)
Zwei Modelle/Nummern-/Mahn-Schemata (`deal_invoices` mit `reminder_level`/`is_storno`). Per CLAUDE.md/Weiche 3 ist `invoices` führend, `deal_invoices` stillgelegt (Drop ausstehend). Kein neuer Fund — nur Bestätigung der Redundanz.

---

# SYNTHESE — priorisierte Schwächen-Tabelle

Sortiert nach **Nutzen-pro-Aufwand** (oben = am meisten Intelligenz je Euro). Aufwand S/M/L.

| ID | Achse | Schwäche | Heutiger Zustand (Beleg) | Soll-Automatisierung | Kat | Nutzen | Aufw | Risiko | Abhängigkeit |
|---|---|---|---|---|---|---|---|---|---|
| I-1 | Routing | Smart-Formular-Router ungenutzt | `SmartroutingService.php:19` 0 Aufrufer; live naiver Produktfilter `LeadProductChecklistValueController.php:41` | Router verdrahten: Kontext-Formular je Gewerk/Objekt/Phase | (a) | hoch | S | niedrig | — (gebaut) |
| I-2 | Plausib. | PlausibilityService dormant | `PlausibilityService.php` 0 Aufrufer | In Aufmaß/Form-Speicherpfade einhängen (Warn-Modus) | (a) | hoch | S | niedrig | — (gebaut) |
| I-3 | Konsist. | junk() ≠ destroy() Storno-Asymmetrie | `DealController.php:3727` (junk ohne Rechnungs-Storno) vs `:3756-3757` | `cancelInvoicesForDeal` auch in junk-Pfad | (c) | mittel | S | niedrig | — |
| I-4 | Plausib. | PLZ-Miss → still Default-Klima | `Energie/HeizlastController.php:127` `?? -8.5`; `HeizlastRechner.php:30` | Flag „Annahme/geschätzt" statt still (Operanden-Gate) | (c) | mittel | S | niedrig | — |
| I-5 | Plausib. | Negativ-Menge nicht validiert | nur `DealController.php:2218` hat min:0; `HandoverController.php:131` etc. ohne | `min:0`/`gt:0` je Mengenfeld (Preis separat prüfen) | (a) | mittel | S | niedrig | — |
| I-6 | Plausib. | Vorlauf/Rücklauf Alt-Pfad ungeprüft | `CustomerHeatingCircuitController.php:40-41` nur numeric | `flow>return` + Temp-Band im Alt-Pfad | (a) | mittel | S | niedrig | — |
| I-7 | Redund. | Heizlast tippt Aufmaß neu | `Energie/HeizlastController.php:8,:147` kein DealMeasurement-Bezug | Prefill der Raum-/Flächenmaße aus `deal_measurements` | (a) | hoch | M | mittel | Aufmaß-Datenmodell |
| I-8 | Konsist. | Auftrag „active" trotz bezahlt | SQL: deals 17/18/24/28 active + invoice paid | Ableitung/Flag „voll fakturiert & bezahlt" | (a) | mittel | S | niedrig | Weiche 1 |
| I-9 | Kausal. | Follow-up greift nicht am Feld-/Tagesbericht | `FollowUpCreator` 5 Aufrufer, Planner ruft nicht; tote Slots `:23-31` | `sync()` aus Tagesbericht/Planner-Abschluss | (a) | mittel | S | niedrig | — |
| I-10 | Kausal. | Abnahme→Rechnung ohne Vorschlag | kein Pfad; nur manuell `InvoiceController.php:218` | Rechnungs-**Entwurf** vorschlagen (nicht festschreiben) | (b) | hoch | M | mittel | Invoice-Zone (TABU-Naht), Weiche 3 |
| I-11 | Kausal. | Angebot→Auftrag stößt keine Folgeprozesse an | `DealController.php:3702-3709` nur lpl-Update | Kickoff-Aufgaben (Materialliste/Kalkulation) als Vorschlag | (a)+(b) | hoch | M | mittel | Materiallisten-Modell |
| I-12 | Routing | Aufgaben-Routing manuell/Ersteller | `FollowUpCreator.php:52`, `PersonalTaskController.php:838` | Default-Zuweisung per Gewerk/Abteilungs-Owner (überschreibbar) | (a)+(b) | mittel | M | mittel | Owner-Definition je Gewerk |
| I-13 | Plausib. | Freie Phasensprünge, nur umgehbares Gate | `moveStageWorkflow` keine Ordnung; `:5108` skip-Flag | Übersprung/Rückwärts sichtbar markieren (nicht blocken) | (b) | mittel | M | mittel | Weiche 2 |
| I-14 | Konsist. | `deals` 5 Status-Felder + Freitext | Schema + SQL (active/deal; project_status DE-Freitext) | Eine führende Auftrags-Phase, Rest ableiten | (c)→(a) | hoch | L | mittel | Weiche 1 |
| I-15 | Konsist. | `lead_stage_id`-Ableitung dreifach | `deriveLeadStageId` vs `normalizeCompanyStage` (kein Fold) | `normalizeCompanyStage` löschen, Kanon nutzen | (c) | mittel | S | niedrig | — |
| I-16 | Routing | Reminder-Kopplung fragil + toter Job | `ProcessPersonalTaskScheduler.php:210` name-Join; `ScheduleTaskReminder` nie dispatch | Härten (echter User-FK) / toten Job entfernen | (c) | niedrig | S | niedrig | — |
| I-17 | Redund. | Adresse Kunde+Objekt doppelt erfasst | `new_leads` + `lead_alternative_adds` je street/plz/city | Objekt-Adresse aus Kunde vorbefüllen | (a) | niedrig | S | niedrig | — |
| I-18 | Redund. | 3–4 parallele Angebots-Wizards | `config.blade.php` 1,16 MB + copy/old-Varianten | Toten Wizard belegt stilllegen (nach Referenz-Beweis) | (a) | mittel | M | mittel | Code-Audit-Aufräumung |

**Quick-Wins (S-Aufwand, sofort):** I-1, I-2 (beide „gebaut, nur verdrahten"), I-3, I-4, I-5, I-6, I-9, I-15, I-16. Das sind die Stellen, an denen ticket am billigsten vom „stummen" zum „mitdenkenden" System wird — mehrheitlich, weil die Intelligenz bereits existiert und nur eingehängt oder konsistent gemacht werden muss.

---

# AUTOMATISIERUNGSGRAD je Kern-Kette

Skala: **1 stumme Datenbank** (speichert, denkt nicht) … **5 mitdenkendes Assistenzsystem** (löst Folgen aus, plausibilisiert, routet).

| Kern-Kette | Grad | Begründung (Beleg) |
|---|---|---|
| **Anfrage→Lead** | 2 | Erfassung + Duplikat-Check vorhanden, aber Lead löst keine Folge aus; Status-Zoo `new_leads.status` (SQL) |
| **Lead→Angebot** | 1–2 | Kein Task/Reminder/Draft beim Übergang (K1); Angebot rein manuell |
| **Angebot→Auftrag** | 2–3 | Deal wird auto-erzeugt + Angebots-Gate (umgehbar) + konkurrierende Angebote auto-storniert; aber **kein** Kickoff der Folgeprozesse (K2) |
| **Auftrag→Montage** | 2 | Bewusst manuell (Yama) — korrekt, aber damit per Definition kein Automatismus; Planer plant von Hand (K3) |
| **Montage→Abnahme** | 2 | Feld-Rückfluss teilweise (Progressbar aus planner_items gebaut), Melde→Prüf-Kette noch nicht; Rückfluss-Link fehlt (Weiche 6) |
| **Abnahme→Rechnung** | 1 | Kein Draft, kein Anstoß (K4); reiner Medienbruch |
| **Rechnung→Zahlung→Abschluss** | 2 | Zahlungsstatus in `invoices` sauber, aber kein Rücklauf auf Auftragsstatus (C2); Storno-Rückabwicklung stark (K5-destroy) |
| **Wartung/Follow-up** | 3 | `FollowUpCreator` = echte *eine* Erzeugungsstelle mit Upsert (Vorbild), aber dashboard-only + Lücken (K6, R3) |
| **Rechen-Assistenz (Heizlast/Form/Anforderungsprofil)** | **4** | Operanden-Gate, Plausi-Bänder, Registry-Ablehnung, geschätzt-Kennzeichnung (P2/P7) — die einzige Zone, die echt „mitdenkt" |

**Gesamturteil:** ticket ist im **umsatztragenden Alt-Kern eine überwiegend stumme Datenbank (Grad ~2)**: sie speichert Zustände sauber (Kanban-Hook, FK-Kette), stößt aber die realen Prozess-Folgen nicht an und plausibilisiert Eingaben kaum. Die **junge Rechen-Zone ist ein echtes Assistenzsystem (Grad ~4)** — und, entscheidend, **zwei ihrer Intelligenz-Bausteine (SmartroutingService, PlausibilityService) liegen ungenutzt brach.** Der schnellste Hebel ist nicht Neubau, sondern **Verdrahten des bereits Gebauten** und **Schließen der teuersten Medienbrüche** (Heizlast-Doppel-Erfassung, Abnahme→Rechnungs-Vorschlag, junk-Storno).

---

# Selbstkritik

- **Datenbasis dünn:** Dev-Restore zu ~82 % leer; die Konsistenz-SQL (C1/C2/C4) beruht auf 11–52 lebendigen Zeilen. An Prod-Daten (~3000 Kunden) könnten Divergenzen (Status-Zoo, Storno-Asymmetrie) zahlreicher oder das Gegenteil (sauber, weil Hook greift) sein. Alle „passiert in der Praxis"-Aussagen sind Code-strukturell + Stichprobe, nicht volumenbelegt.
- **Serverseitig:** Alle Kausalitäts-/Plausibilitäts-/Routing-Belege sind PHP-serverseitig. Ob das Frontend (Blade/JS) fehlende Automatik clientseitig verkettet (z.B. Heizlast-Prefill, Angebots-Anstoß nach Stage-Move) ist **NICHT VERIFIZIERT** — für I-7, I-1, I-11 wäre eine Blade/JS-Prüfung nötig, bevor „fehlt" endgültig gilt.
- **Kausalität nicht erschöpfend:** Queues/Scheduler wurden nur punktuell geprüft (`ProcessPersonalTaskScheduler` gelesen, aber keine Vollinventur aller `dispatch()`/`Schedule::`-Trigger auf zeitversetzte Folge-Erzeugung).
- **Dynamische Aufrufe:** „0 Aufrufer" für `SmartroutingService`/`ScheduleTaskReminder` ist statischer grep — stark, aber kein Beweis gegen Reflection/String-Dispatch (**NICHT-VERIFIZIERT**).
- **TABU respektiert:** Invoice-Zone, Nuriva, Video, Legacy nur an ihren *Nähten* betrachtet (fehlende Brücke Abnahme→Rechnung), nicht inhaltlich bewertet.
- **Snapshot-Invalidierung (Rd3)** je Feld nicht geprüft — gewollt-eingefroren vs. stale bleibt offen.

# Gelesen / Nicht-gelesen

**Gelesen/gemessen (firsthand):** `docs/architektur-entscheidungen.md`, `docs/glossar.md`, `docs/audit/code-audit.md` (Kontext); `moveStageWorkflow` + Offer-Gate + Deal-Erzeugung (`LeadOverviewController.php:4900-5360`, firsthand); Storno-Pfad `DealController.php:3660-3864` (firsthand); `FollowUpCreator.php` (firsthand); Konsistenz-SQL auf `lead_product_lists`/`deals`/`invoices`/`new_leads`/`offers` (firsthand); Schema-Messung Status-Felder + Adress-Spalten (information_schema, firsthand); Task-Routing (`PersonalTaskController.php`, firsthand). **Via Explore-Agenten (belegt mit file:line):** vollständige Kausalitäts-Kette der 6 Ereignisse; Plausibilitäts-Landkarte (Heizlast/Form/Anforderungsprofil/Alt-Kern); Routing (Smartrouting/Scheduler/OverdueCenter) + Redundanz (Heizlast↔Aufmaß, Wizards, Kanban-Systeme).

**Nicht gelesen / nur oberflächlich:** die Methodenkörper der meisten 387 Controller; die Multi-hundert-KB-Angebots-Blades (nur Größe/Route); Frontend-JS zur clientseitigen Verkettung; TABU-Zonen inhaltlich; vollständige Job/Scheduler-Inventur; jede Snapshot-Invalidierung.
</content>
</invoke>
