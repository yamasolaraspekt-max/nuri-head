# EXPERTEN-INVENTUR 03 — AUFTRAGSABWICKLUNG (Angebot→Auftrag→Storno)

> **Rolle:** AUFTRAGS-EXPERTE (Auftragsabwicklung). **Modus:** REIN LESEND. Stand 2026-07-10. Repo `/Users/yamanuri/Documents/ticket`, Branch `private/app-code-backup`.
> **Zieldatei:** Dies ist die **einzige** von diesem Auftrag geschriebene Datei. Parallele Audits (`code-audit.md`, `intelligenz-audit.md`, `automatisierungs-hebel.md`, `experten/**`) wurden **nicht** angefasst.
> **Baut auf (firsthand gelesen):** `docs/audit/code-audit.md`, `docs/audit/intelligenz-audit.md`, `docs/audit/automatisierungs-hebel.md`, `docs/glossar.md`, `docs/architektur-entscheidungen.md` (via Kontext). SQL read-only über CNF auf `ticket`.
> **Begriffe (Glossar-verbindlich):** **Auftrag = `deals`**, Angebot = `offers`(+`offer_folders`/`offer_details`), Gewerk = `lead_product_lists` (Kunde×Produkt×Objekt), Objekt = `lead_alternative_adds`/`alternative_id`, Kunde = `new_leads`. „Projekt (Bauphase)" = `projects` (31 Z., **ohne `deal_id`** — SQL bestätigt, s. F9).
> **TABU (nicht bewertet):** Nuriva, Video/Jitsi, **Invoice-Zone** (interne Rechnungsqualität — nur an der Naht Storno→`invoices` betrachtet), Legacy Bitrix/NIBE/IMAP.
> **Datenbasis-Warnung:** Dev-Restore ~82 % leer. Auftrags-Kern lebendig aber dünn: **`deals` 14 Z.**, `offers` 29, `lead_product_lists` 52, `projects` 31, `deal_measurements` 0 (Seed). Alle „passiert in der Praxis"-Aussagen sind Code-strukturell + Stichprobe, nicht volumengemessen.

---

## 0. Kern-Befund vorab (die drei Auftrags-Wahrheiten)

Der Auftrag `deals` wird über **zwei divergente Wege** erzeugt und trägt **fünf** Status-Felder — das ist der zentrale Auftrags-Befund:

| Erzeugungsweg | Beleg | Setzt `status` | Kopiert Preis? | Auslöser |
|---|---|---|---|---|
| **A — Kanban-Auto** (Angebotsordner angenommen) | `LeadOverviewController::upsertDealFromAcceptedFolder` `:5654-5838` | **`'active'`** `:5745` | **Ja** (`total_net/gross` aus `offer_details`, `:5737-5740`) | automatisch beim Ordner-Accept in der Kanban-Move-Kette (`:5646`) |
| **B — Manuell** („Neues Projekt erstellen"-Modal) | `DealController::dealStore` `:3645-3715` | **`'deal'`** `:3696` | **Nein** (nur Kopf-Satz) | Nutzer-Klick, `route('deal.store')` |

→ **Derselbe Sachverhalt „Auftrag angelegt" produziert zwei verschiedene Default-Status** (`active` vs. `deal`) und zwei verschiedene Datenreichtümer (Weg A mit Summen, Weg B ohne). SQL bestätigt die Spreizung in den Live-Daten: 13× `active`, 1× `deal` (der einzige `dealStore`-Satz). Das ist die Auftrags-Seite von Weiche 1 (C1 im Intelligenz-Audit).

---

## 1. IST-FUNKTIONEN — Raster (Beleg · Stärken · Schwächen · Reife)

**Reife-Skala** (wie Intelligenz-Audit): **1** stumme Ablage … **3** solide CRUD mit Guards … **5** mitdenkend (löst Folgen aus, plausibilisiert, routet).

### F1 — Angebot→Auftrag-Überführung (Zusage), Weg A: Kanban-Auto-Upsert
- **Beleg:** `LeadOverviewController::upsertDealFromAcceptedFolder` `:5654-5838`; Aufruf `:5646` innerhalb der Ordner-Accept-Behandlung; **konkurrierende Angebotsordner werden auto-storniert** (`:5621-5644`: Status `cancel`/`offer_status=cancelled` + Biografie-Eintrag `auto_cancelled_by_kanban_acceptance`).
- **Stärken:** Idempotenter **Upsert** per `(customer_id, alternative_id, product_id, offer_folder_id)` `:5751-5768` → kein Doppel-Auftrag. Robustes **Legacy-NOT-NULL-Netz** (`information_schema`-Abfrage füllt pflicht-Spalten ohne Default `:5789-5835`). Preise/Summen aus `offer_details` durchgereicht (`:5702-5714,:5737-5740`). Angebotsnummer + Auto-`order_number` (`:5772-5774`). **Auto-Storno konkurrierender Angebote ist echte Kausalität** (K2 „konkurrierende Angebote auto-storniert").
- **Schwächen:** Schreibt via **`DB::table('deals')->insert()` `:5837` — umgeht den `Deal::creating`-Hook** (Ordner-Nummer wird deshalb separat manuell erzeugt `:5772`). Setzt `status='active'` (divergent zu Weg B). **Stößt keinen Folgeprozess an** (keine Materialliste-Materialisierung, keine Kalkulation, keine Einsatzplanung, keine Kickoff-Aufgabe) — s. §2.
- **Reife: 3** (robuster, idempotenter Insert + Auto-Storno der Konkurrenz; aber Raw-Insert am Hook vorbei + kein Kickoff).

### F2 — Angebot→Auftrag-Überführung, Weg B: manueller `dealStore`
- **Beleg:** `DealController::dealStore` `:3645-3715`.
- **Stärken:** **Autorisierungs-Gate** (`authorizeDealUpdate` `:3625` → `hasPermission('Customer','update')`). **Anti-Spoofing:** maßgeblich ist `lead_product_lists`, nicht die Hidden-Felder (`:3654-3662`, prüft `customer_id`-Konsistenz). `DB::transaction` klammert Deal-Create + Kanban-Stufen-Update (`:3685-3712`). **Merkt `old_stage`** für die Storno-Rückbuchung (`:3701-3706`). Nutzt den `Deal::creating`-Hook für `order_number` (`Deal.php:42-49`). Angebots-Auflösung: bevorzugt `accepted_offer_folder_id`, sonst neuestes passendes Angebot (`:3670-3683`).
- **Schwächen:** Kopiert **nichts Monetäres** (kein `price`/`total_*`) → Auftrag ohne Betrag, wenn nur über Weg B angelegt. Setzt `status='deal'` (divergent zu Weg A `active`). Auch hier **kein Folgeprozess-Anstoß**.
- **Reife: 3** (sicher, transaktional, anti-spoof; aber unvollständiger Datensatz + Divergenz zu Weg A).

### F3 — Angebots-Zusage-Gate (offer_acceptance)
- **Beleg:** `requiresAcceptedOfferBeforeEnteringDeal` `:5330-5360`; Auswertung in `moveStageWorkflow` `:4987-5140`; `offer_acceptance_status` gesetzt `:5076,:5104-5139`.
- **Stärken:** Beim Eintritt in `deal` wird ein **angenommenes Angebot verlangt** — echtes Ordnungs-Gate genau am richtigen Übergang (nicht früher, `:5343-5345`). Sauber protokolliert: `offer_acceptance_status='accepted_offer'` bzw. `'moved_without_offer_acceptance'` + `moved_without_offer_acceptance_{at,by,reason}` (`:5104-5139`) — der Umgehungs-Fall ist **markiert, nicht verschwiegen**.
- **Schwächen:** **Per Request-Flag umgehbar** (`skip_offer_gate_without_folder` / `skip_offer_acceptance_without_offer` `:4918,:4992`). Kein anderer Ordnungs-Zwang: `moveStageWorkflow` prüft nur, dass Ziel-Keys existieren (Intelligenz-Audit P6) — Rückwärts-/Übersprünge sind erlaubt und außer diesem Gate unsichtbar.
- **Reife: 3** (das *eine* fachliche Gate der Kette, mit ehrlicher Umgehungs-Markierung; aber weich).

### F4 — Auftragsakte / -Profil (Auftragsübersicht)
- **Beleg:** `DealController::profile` `:2751`, `index/all/loadKanbanColumn` `:780-918`, `history/buildDealHistoryItems` `:1003-1284`, Notizen (`DealNote`) `:1392-1503`, Dateien `:1609-1991`, Aufmaß-Report `:3255-3344`.
- **Stärken:** Reiche Akte: aggregierte Historie aus mehreren Quellen (`dealActivitySqlParts` `:51`), Notizen, Datei-Upload/Preview/Download mit Ordner-Auflösung (`resolveDealFolderId` `:1504`), Aufmaß-/Material-Zusammenfassung im Profil. Kanban-Spalten-Ansicht des Auftrags-Boards mit Filtern (`applyDealFilters` `:363`).
- **Schwächen:** Sehr breit (3.9k Z. Controller), keine Service-Schicht, keine Tests (Code-Audit 1.7: Deal = „PARTIELL", nur Measurement+Policy). Aufmaß-/Material-Daten teils aus Snapshots ohne dokumentierte Invalidierung (Rd3).
- **Reife: 3** (funktional vollständige Akte; wartungs-schwer, ungetestet).

### F5 — Auftrags-Statuswechsel (Unterphasen)
- **Beleg:** `updateStatus` `:919-1000`, `profileUpdateStatus` `:3040-3112`; `normalizeDealWorkflowStatus` `:617-658`, `syncDealWorkflowTargets` `:674-742`.
- **Stärken:** **`lockForUpdate` + `DB::transaction`** `:953-982` (race-sicher). **Begründungspflicht** (`reason required` `:923,:3044`) → jede Statusänderung erzeugt einen `DealNote`-Audit-Eintrag mit Alt→Neu-Label (`:970-980`). **Alias-Normalisierung** DE/EN (`:626-650`) fängt den Freitext-Status-Zoo ab. `syncDealWorkflowTargets` hält **Kanban-Gewerk + Angebotsordner konsistent** mit (`:687-741`).
- **Schwächen:** `syncDealWorkflowTargets` schreibt `lead_product_lists.status/stage` **hart auf `'deal'`** (`:698,:702`) unabhängig vom Auftrags-Substatus (auch bei `pause`/`cancel` bleibt das Gewerk auf „deal") — die Sub-Phase landet nur in Neben-Feldern (`sub_stage_key` `:717`). Fünf-Status-Feld-Problem bleibt (`status`, `deal_status`, `project_status`, `measurement_status`, `status_msg` — Schema bestätigt).
- **Reife: 4** (race-sicher, begründungspflichtig, mehr-Ziel-konsistent — die reifste Auftrags-Funktion; getrübt durch hart-`'deal'`-Rückschreibung).

### F6 — Auftragsbestätigung
- **Beleg:** `confirmed_at`-Feld nur via `updateDate`-Whitelist `:3901-3927`; Dokument-Kategorie `confirmed_order` „Auftragsbestätigung" nur als **Upload-Slot** (`resources/views/admin/deal/customer_view.blade.php:2761`, `partials/gallary.blade.php:6`).
- **Stärken:** Bestätigungsdatum erfassbar; Auftragsbestätigung als Dokument-Typ ablegbar.
- **Schwächen:** **Keine generierte Auftragsbestätigung** (kein PDF-/Dokument-Erzeuger gefunden — grep `auftragsbestätigung|order.?confirmation|confirmation.?pdf` = nur View-Optionen). `confirmed_at` ist ein freies Inline-Feld ohne Workflow (keine Kausalität „bestätigt → Kickoff"). Der Begriff „Auftragsbestätigung" existiert nur als **manueller Upload**, nicht als System-Ausgabe.
- **Reife: 1–2** (reine Ablage; keine Erzeugung, keine Folge).

### F7 — Änderungen / Nachträge
- **Beleg:** `updateDate` (Whitelist `sign_date, confirmed_at, status, price` `:3912`) `:3901-3927`; `updateReviewers` `:3883-3899`; `updateDate`-Preis-Cast `:3918-3919`.
- **Stärken:** Kontrollierte Inline-Edits per Feld-Whitelist (kein Mass-Assignment). Prüfer/„geprüft durch" setzbar.
- **Schwächen:** **Kein Nachtrags-/Change-Order-Konzept** (grep `nachtrag|change.?order|amendment|revision` in DealController = **0 Treffer**). Preisänderung am Auftrag ist ein stiller Feld-Overwrite **ohne Begründungspflicht** (anders als F5-Statuswechsel!) und **ohne Rückwirkung** auf Angebot/Rechnung/Positionen. Keine Positions-Ebene am Auftrag (H-D3: Auftrag ist Kopf-Satz; Positionen leben im Angebot/Rechnungs-Canvas) → ein „Nachtrag" (Mehrmenge) hat im Auftrag keinen Ort.
- **Reife: 1–2** (Feld-Edit ja, echtes Nachtragswesen nein).

### F8 — Storno / Rückabwicklung (junk vs. destroy)
- **Beleg:** `junk` `:3718-3730`, `unjunk` `:3733-3745`, `destroy` `:3748-3770`, `restore` `:3773-3781`; `cancelInvoicesForDeal` `:3829-3864`, `restoreLeadStageForDeal` `:3787-3808`, `markLeadStageDealForDeal` `:3811-3818`.
- **Stärken (destroy = Vorbild):** `destroy` klammert **atomar** `cancelInvoicesForDeal` + `restoreLeadStageForDeal` + SoftDelete (`:3755-3760`). **Operanden-Gate korrekt:** bezahlte Rechnungen werden **nicht** still storniert, sondern auf `storniert_bezahlt_pruefen` gesetzt + Warnung „bitte buchhalterisch prüfen (Rückzahlung/Gutschrift)" (`:3849,:3764-3766`); offene → `storniert`. **Idempotent** (überspringt schon-markierte `:3841`). Stufen-Rückbuchung nutzt das gemerkte `old_stage`, idempotent (`:3796-3806`). Nur die führende Schiene `invoices` wird berührt (`:3834`, Alt-Schiene stillgelegt — CLAUDE.md-konform).
- **Schwächen (die Asymmetrie — I-3/H-A3):** **`junk()` setzt NUR die Lead-Stufe zurück, storniert die Rechnungen NICHT** (`:3718-3730` ruft `restoreLeadStageForDeal`, **nicht** `cancelInvoicesForDeal`). Ein „gejunkter" Auftrag steht damit auf `status='Junk'` mit auf `accepted` zurückgesetztem Gewerk, während seine offenen Rechnungen **aktiv weiterlaufen** → widersprüchlicher Zustand Auftrag(geparkt)/Rechnung(offen). `junk` ist zwar reversibel (`unjunk`), aber das rechtfertigt die Rechnungs-Divergenz nur, wenn Junk als reine Parkbucht verstanden wird — der Stufen-Reset (der ja *doch* passiert) widerspricht dieser Lesart. **Inkonsistente Storno-Semantik: destroy vollständig, junk halb.**
- **Reife: destroy 5 · junk 2** (destroy ist die stärkste Kausalität des ganzen Auftrags-Bereichs; junk unterläuft sie).

### F9 — Auftrag→Montage / Einsatzplanung (deal-seitig)
- **Beleg:** `planningPreview` `:2112-2150`, `planningCheck` `:2152-2202`, `planningStore` `:2204-2271+`; Helfer `extractPlanningMaterialList` `:2475-2497`, `extractPlanningRequiredQualifications` `:2538`, `buildPlanningEmployeeSuggestions` `:2586-2654`, `planningEmployeeHasConflict` `:2656-2669`, `planningEmployeeScore` `:2671-2688`.
- **Stärken (deutlich reifer als die Basis-Audits andeuten):** Aus den **Angebots-Sektionen** werden **on-demand** (a) eine **Materialliste** (gruppiert/summiert je `master_set/product/component`, `:2485-2496`) und (b) die **erforderlichen Qualifikationen** extrahiert. `buildPlanningEmployeeSuggestions` **matcht Mitarbeiter nach Qualifikation + Abteilung, prüft Terminkonflikte** gegen `main_appointments` (`planningEmployeeHasConflict` `:2656-2668` — echte Zeitüberschneidungs-Prüfung!), **scort/rankt** (verfügbar +1000, Haupt-MA +100, montage_percent…) und liefert `best_available` (`:2638-2649`). `planningStore` legt einen `PlannerPlan` (draft) an mit `material_summary`, `qualification_summary`, `selected_employees` und `meta.deal_id` (`:2248-2270`).
- **Schwächen:** **Manuell/nutzer-initiiert** — dieser ganze Apparat feuert **nicht** automatisch aus der Angebot→Auftrag-Überführung, sondern erst, wenn ein Nutzer das Planungs-Modal am Auftrag öffnet. Materialliste wird **nicht materialisiert** (nur transient aus Angebots-Sektionen berechnet → nicht wiederverwendbar/nachtragsfähig). Konflikt-Check nur gegen `main_appointments`, **nicht** gegen andere `PlannerPlan`-Buchungen. Erzeugt `PlannerPlan`, nicht die `projects`-Zeile.
- **Reife: 4** (Material-Ableitung + Qualifikations-Matching + Verfügbarkeits-/Konflikt-Check + Scoring = echte Assistenz; nur eben manuell angestoßen und nicht materialisiert).

### F10 — Aufmaß-Akte am Auftrag (DealMeasurement)
- **Beleg:** `DealMeasurementController` (`storeFromDeal` `:342`, `assignWork` `:682`, `complete` `:2111`, `unlock` `:2259`, `updateItem` `:1595`, `saveDetail` `:1987`, Kanban `:1235-1493`).
- **Stärken:** Eigene Measurement-Akte je Auftrag mit Lock nach `complete` (`:2111`) + `unlock`-Gegenstück, Positions-Items (`DealMeasurementItem`), **Policy-geschützt** (Code-Audit: `DealMeasurementPolicyTest`, die einzige getestete Auftrags-Teilzone). Der Heizkörper-Auslegungs-Kern schreibt hier hinein (`HeizkoerperController` → `DealMeasurementItem`, Automatisierungs-Hebel TEIL 2).
- **Schwächen:** In Dev leer (0 Z.) → Verhalten nur Code-belegt. Redundanz-Naht zur Heizlast-Erfassung (Rd1/H-D2: Heizlast tippt Maße neu statt aus `deal_measurements` zu prefillen).
- **Reife: 3** (solide, gelockt, policy-getestet; als Datenquelle für andere Zonen unterausgeschöpft).

---

## 2. PRÜF-AUFTRAG: Was löst der Angebot→Auftrag-Übergang automatisch aus?

**Antwort: fachlich fast nichts — mit einer wichtigen Präzisierung gegenüber dem Intelligenz-Audit.**

Bei der Überführung (beide Wege) passiert **automatisch**:
1. `deals`-Zeile angelegt/geupserted (F1/F2).
2. Kanban-Gewerk `lead_product_lists.status/stage → 'deal'` + `old_stage` gemerkt (`:3701-3709` / `:5654ff` via Folder).
3. **Nur Weg A:** konkurrierende Angebotsordner auto-storniert (`:5621-5644`), Preis/Summen aus `offer_details` kopiert.
4. Realtime-Broadcast (`broadcastOfferFolderRealtime` `:5840`).

**NICHT ausgelöst:** Auslegung/Kalkulation, **Materialliste-Materialisierung**, Kickoff-Aufgaben, Einsatzplanung — bestätigt den Intelligenz-Audit-Befund K2/H-A5.

**Präzisierung (mein Zusatz-Befund):** Die Fähigkeiten **existieren im Auftrag**, sie sind nur **nicht an den Übergang gekoppelt**:
- **Einsatzplanung ist gebaut** (F9: `planningCheck/Store` mit Material-Ableitung, Qualifikations-Matching, Verfügbarkeits-/Konflikt-Check, Scoring) — aber als **manuelles Modal**, nicht als Auto-Folge der Zusage.
- **Materialliste ist ableitbar** (`extractPlanningMaterialList` aus Angebots-Sektionen) — aber **transient**, wird bei der Überführung nicht materialisiert.

→ Der Medienbruch ist also **nicht** „Fähigkeit fehlt", sondern „**Auslöser fehlt**": zwischen `dealStore`/`upsertDealFromAcceptedFolder` und dem vorhandenen `planning*`-Apparat gibt es keine Naht. Das ist ein **billiger Hebel** (H-A5-nah): den Übergang einen `PlannerPlan`-Entwurf **vorschlagen** lassen (Operanden-Gate: Vorschlag, nicht festschreiben). *(Yama-Entscheid: Montageplan bewusst manuell/kuratiert — Weiche 6. Also korrekt **Vorschlag**, kein Auto-Commit.)*

---

## 3. PRÜF-AUFTRAG: junk() vs. destroy() — Storno-Asymmetrie

**Bestätigt und präzisiert (I-3/H-A3).** Gegenüberstellung:

| Aktion | Rechnungs-Rückabwicklung | Lead-Stufen-Reset | SoftDelete | Reversibel | Atomar |
|---|---|---|---|---|---|
| `destroy` `:3748` | **JA** — `cancelInvoicesForDeal` (offen→`storniert`, bezahlt→`storniert_bezahlt_pruefen`+Warnung) | JA | JA (`delete()`) | über `restore` | **JA** (`DB::transaction` `:3755`) |
| `junk` `:3718` | **NEIN** | JA (`restoreLeadStageForDeal`) | Nein (`status='Junk'`) | über `unjunk` | Nein (2 Einzel-Saves) |

**Bewertung:** Die Asymmetrie ist real und ein **(c)-Inkonsistenz-Bug**: `junk` setzt die Gewerk-Stufe auf `accepted` zurück (tut also *so*, als sei der Auftrag rückabgewickelt), lässt die Rechnungen aber aktiv. Zustand nach `junk`: Auftrag `Junk` + Gewerk `accepted` + Rechnung `open` — drei Entitäten in drei widersprüchlichen Wahrheiten. **Fix (klein, sicher):** `cancelInvoicesForDeal($deal)` auch im `junk`-Pfad aufrufen (idempotent, existiert bereits), gespiegelt von `unjunk` (Storno-Rücknahme). Alternativ, falls Junk bewusst „parken ohne Rechnungs-Folge" sein soll: dann müsste `junk` **auch** den Stufen-Reset unterlassen — heute ist es genau der unglückliche Mittelweg.

---

## 4. PRÜF-AUFTRAG: Mehr-Gewerke-Auftrag am selben Objekt

- **Strukturell unterstützt:** Ein `deal` ist geschlüsselt auf **`(customer_id, product_id, alternative_id)`** — die Upsert-/Dedup-Logik unterscheidet **nach `product_id`** (`:5751-5754`, `restoreLeadStageForDeal` filtert alle drei `:3789-3792`). Zwei Gewerke (z. B. PV + WP) am selben Objekt ⇒ **zwei getrennte `deals`**, jeweils eigene `order_number`, eigenes Angebot/Ordner, eigene Einsatzplanung (F9 pro Deal). Das ist **konsistent** mit dem Glossar (Gewerk = `lead_product_lists`-Zeile; mehrere Gewerke je Objekt erlaubt).
- **Daten (SQL, dünn):** Nur 1 Objekt (`alternative_id`) mit >1 Auftrag — und dort **gleiches `product_id`** (2 Deals, 1 Produkt), also eher Re-Anlage desselben Gewerks als echter Mehr-Gewerke-Fall. In `lead_product_lists` hat im Dev-Seed **kein** Objekt >1 Gewerk. → **Mehr-Gewerke-am-Objekt ist code-seitig sauber getragen, aber in den Dev-Daten praktisch nicht exerziert** (NICHT-VERIFIZIERT an Prod-Volumen).
- **Schwäche/Naht:** Es gibt **keine objekt-übergreifende Klammer** über die mehreren Aufträge eines Objekts (Weiche 5 „Objekt klammert" — dormant). `projects` (Bauphase) hätte diese Rolle, ist aber **ohne `deal_id`** (SQL bestätigt: 0 `deal_id`-Spalten) → die Montage-/Bauphase eines Objekts ist von den Auftrags-Zeilen **entkoppelt**. Zwei Gewerke am selben Haus, die eine gemeinsame Baustelle sind, haben im System keinen gemeinsamen Anker (außer `alternative_id`).

---

## 5. AUTOMATISIERUNGS-REIFE gesamt (Auftragsabwicklung)

| Teil-Kette | Reife | Begründung (Beleg) |
|---|---|---|
| Angebot→Auftrag (Zusage) | **3** | Auto-Upsert + Angebots-Gate + Konkurrenz-Auto-Storno (F1/F3); aber zwei divergente Wege, Raw-Insert am Hook vorbei, kein Kickoff |
| Auftragsakte/Profil | **3** | vollständige Akte, aber ungetestet/Service-los (F4) |
| Statuswechsel | **4** | race-sicher, begründungspflichtig, mehr-Ziel-konsistent (F5) |
| Auftragsbestätigung | **1–2** | nur Upload-Slot + Inline-Datum, keine Erzeugung/Folge (F6) |
| Änderungen/Nachträge | **1–2** | Feld-Whitelist-Edit; kein Nachtragswesen, Preis-Overwrite ohne Begründung (F7) |
| Storno destroy | **5** | atomar, Operanden-Gate für bezahlte Rechnungen — Vorbild (F8) |
| Storno junk | **2** | Rechnungs-Rückabwicklung fehlt → Inkonsistenz (F8/§3) |
| Einsatzplanung (deal-seitig) | **4** | Material-Ableitung + Qualifikations-Matching + Konflikt-Check + Scoring; nur manuell, nicht materialisiert (F9) |
| Aufmaß-Akte | **3** | gelockt, policy-getestet, als Quelle unterausgeschöpft (F10) |

**Gesamturteil:** Die Auftragsabwicklung ist **überwiegend solides CRUD (Reife ~3)** mit **zwei überraschend reifen Inseln** — der begründungspflichtige, race-sichere **Statuswechsel (4)** und der **destroy-Storno (5)** — und einer **verborgenen Assistenz-Insel** (die deal-seitige **Einsatzplanung, 4**), die nur nicht an den Zusage-Übergang gekoppelt ist. Die Schwächen sind konzentriert: **Divergenz der zwei Anlage-Wege** (status/Preis), die **junk-Storno-Asymmetrie**, das **fehlende Nachtragswesen** und der **fehlende Auslöser** vom Übergang in die bereits gebaute Planung. Der Bereich ist damit näher am „mitdenkenden" Ende, als der breite Intelligenz-Audit nahelegt — er muss vor allem **konsolidiert (ein Weg, ein Status) und verdrahtet (Übergang → Planungs-Vorschlag)** werden, nicht neu gebaut.

---

## 6. Gelesen / Nicht-gelesen · NICHT-VERIFIZIERT · Selbstkritik

**Firsthand gelesen (Datei:Zeile):** `DealController.php` — Storno-Block `:3610-3864` (junk/destroy/restore/cancelInvoices/restoreLeadStage vollständig), `dealStore` `:3645-3715`, `updateStatus` `:919-1000`, `profileUpdateStatus` `:3040-3112`, Workflow-Status-Helfer `:609-742`, Planung `:2112-2271,:2475-2497,:2586-2695`, `updateDate/updateReviewers` `:3883-3927`, Methoden-Landkarte (alle Signaturen). `Deal.php` vollständig (Hook, Fillable, `generateOrderNo`, Relationen). `LeadOverviewController.php` — `upsertDealFromAcceptedFolder` + Konkurrenz-Auto-Storno `:5620-5867`, Angebots-Gate `:5330-5360`, `offer_acceptance_status`-Setzung `:4918-5180`. **SQL firsthand:** `deals` Status-Verteilung (5 Felder), `deals`/`projects`-Schema, `projects`-Rowcount (31) + **0 `deal_id`**, Mehr-Gewerke-Gruppierung (`deals`/`lead_product_lists` je `alternative_id`), `offer_acceptance`-Tabelle (existiert **nicht** — nur Spalte). **Fundament firsthand:** `code-audit.md`, `intelligenz-audit.md`, `automatisierungs-hebel.md`, `glossar.md` vollständig.

**Nicht/nur oberflächlich gelesen:** `DealMeasurementController` (nur Signaturen + Rollen, kein Methodenkörper — Aufmaß-Akte nur strukturell bewertet); `PlannerPlanController::storeProjectFromLeadProduct` `:7158` (der andere Montage-Einstieg, nur Existenz bestätigt); die Blade-Profile/Views (nur Auftragsbestätigungs-Slot verifiziert); `profile*`-Hilfsmethoden `:3114-3609` (nur Zweck). Invoice-Zone (TABU) nur an der Storno-Naht.

**NICHT-VERIFIZIERT:**
- Der **Auslöse-Kontext** von `upsertDealFromAcceptedFolder` (wird `:5646` in der Ordner-Accept-Behandlung gerufen; die exakte umschließende Methode/Route nicht bis zum Endpunkt zurückverfolgt).
- **Mehr-Gewerke-am-Objekt** an Prod-Volumen (Dev-Seed exerziert es praktisch nicht — 1 Objekt/1 Gewerk).
- Ob ein **Frontend/JS** die Divergenzen (Preis-Nachtrag von Weg B, Planungs-Anstoß) clientseitig kompensiert — serverseitig belegt, clientseitig **nicht** geprüft.
- Ob `junk` in der Praxis überhaupt genutzt wird (Storno-Häufigkeit F20 offen) — die Asymmetrie ist Code-real, ihr Schaden volumen-abhängig.
- „0 Nachtrag-Treffer" ist statischer grep über `DealController` — kein Beweis, dass Nachträge nicht anderswo (Angebots-Revision) abgebildet sind.

**Selbstkritik:** (1) Datenbasis dünn (`deals` 14 Z.) — alle Verteilungs-Aussagen sind Stichprobe. (2) Ich habe die **Reife von F9 (Einsatzplanung) bewusst höher** angesetzt als der breite Intelligenz-Audit (der sie unter „Auftrag→Montage bewusst manuell" subsumierte) — Begründung: der `planning*`-Apparat *rechnet und matcht* real; meine 4 bezieht sich auf die **Fähigkeit**, nicht auf die (fehlende) Kopplung. (3) Die Storno-Bewertung stützt sich auf die Code-Kommentare „Schwaeche 5/6", die belegen, dass destroy bereits gehärtet wurde — junk aber nachweislich nicht mitgezogen wurde. (4) TABU-Naht Invoice: ich bewerte nur, *dass* Storno an `invoices` andockt (korrekt, führende Schiene), nicht die Rechnungsqualität selbst.
