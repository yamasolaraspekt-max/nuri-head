# EXPERTEN-INVENTUR 01 — VERTRIEB (CRM / Lead-Management)

> **Rolle:** Vertriebs-Experte (CRM/Lead-Management). **Modus:** rein lesend. **Stand:** 2026-07-10.
> **Repo:** `/Users/yamanuri/Documents/ticket`, Branch `private/app-code-backup`.
> **Bereich:** Lead-Eingang (Anfrage→Lead) · Qualifizierung · Pipeline/Kanban (`lead_stages`) · Objekt-/Bedarfserfassung (`lead_alternative_adds`, `lead_product_lists`) · Zuweisung/Routing (Zuständigkeit, `department_id`).
> **TABU (nicht bewertet):** Nuriva/Video/Invoice-Zone/Legacy (Bitrix/NIBE/IMAP).
> **Beleg-Disziplin:** Jeder Befund trägt Datei:Zeile, Tabelle oder SQL-Messung. Datenbasis-Warnung: Dev-Restore, Kern lebendig (`new_leads` 52, `lead_alternative_adds` 71, `lead_product_lists` 52, `inquiries` 40), aber Ein-Tages-Seed — Volumen/Raten nicht ableitbar. Aussagen zu „Praxis" sind strukturell (Code) + Stichprobe (Daten).
> **Baut auf (gelesen, nicht dupliziert):** `docs/audit/code-audit.md`, `docs/audit/intelligenz-audit.md`, `docs/audit/automatisierungs-hebel.md`, `docs/crm-inventur-*`, `docs/glossar.md`, `docs/architektur-entscheidungen.md`. Dieser Bericht ergänzt sie um die **firsthand-Vertriebsschicht** (Intake/Qualifizierung/Routing-Code), die dort nur teil-belegt war, und **korrigiert** eine Verallgemeinerung (s. Abweichung A).

---

## 0. Bereichs-Landkarte (gemessen)

| Baustein | Beleg | Zeilen/Zahlen |
|---|---|---|
| `NewLeadsController` | `app/Http/Controllers/Customer/NewLeadsController.php` | 14.054 Z., 121 public-Methoden, **123 Routen** |
| `LeadOverviewController` (Kanban) | `app/Http/Controllers/Customer/Kanban/LeadOverviewController.php` | 7.075 Z., **48 Routen** |
| `InquiryController` (Anfrage) | `app/Http/Controllers/Inquiry/InquiryController.php` | 2.952 Z., **43 Routen** |
| `CustomerStageController` | `app/Http/Controllers/CustomerStageController.php` | 8 Methoden, **6 Routen** |
| `LeadImportController` | `app/Http/Controllers/LeadImportController.php` | 4 Routen (Massen-Import CSV) |
| `public/js/kanban.js` | Frontend Kanban | 17.204 Z. Vanilla-JS-Funktionszoo (Code-Audit 1.6) |
| Pipeline-Definition | Tabelle `lead_stages` | **11 Stufen** (s.u.), 0 FK, gut gepflegt |
| Kunde / Objekt / Gewerk | `new_leads` 52 · `lead_alternative_adds` 71 (193 Sp.) · `lead_product_lists` 52 | Kern-Kette lebendig |

**Pipeline (`lead_stages`, SQL firsthand):**
`lead`(1) → `follow_up`(3, sort 20) → `accepted`(4, sort 30) → `offer`(2, sort 40) → `deal`(5, sort 50) → `project`(6, sort 60) → `abnahme`(11, sort 70) → `completed`(7, closed) → `archive`(8, closed) → `junk`(9, closed). `abnahme` ist die jüngste, nachträglich aktivierte Stufe (`created_at 2026-07-03`, `is_default=0`, `is_protected=0`). Alle Stufen `is_active=1`, `is_protected=1` außer `abnahme`. **Kanon-Reihenfolge** (Sortier-`sort_order`) weicht von der Key-Reihenfolge ab — `follow_up` (20) steht VOR `offer` (40), aber der Fold foldet `follow_up→offer` (s. Konsistenz). Das ist gewollt (Nachfassen ist Vor-Angebots-Phase).

---

## 1. IST-FUNKTIONEN des Bereichs

### 1.1 Lead-Eingang (Anfrage→Lead)
- **Zwei getrennte Intake-Kanäle:**
  1. **Direkt-Lead:** `NewLeadsController::store()` `:523-…` — Formular-Intake mit **echter Server-Validierung** (`:528-600`: `customer_type` required, `branch_id required|exists:branches,id`, `contact_person nullable|exists:employees,id`, `email` validiert). Legt `new_leads` + `lead_alternative_adds` (Objekt) + optional `lead_product_lists` (Gewerke) + Dächer (`roofs.*`) in einem Rutsch an.
  2. **Anfrage-Trichter:** `InquiryController` — `inquiries`-Tabelle (40 Zeilen) als Vor-Lead-Stufe mit eigenem Lebenszyklus (`Draft`→`Unpublished`/`Published`→`Verified`/`Junk`). Anfrage kann Produkte + Mitarbeiter tragen (`inquiry_product_lists`, 39 Z.) und wird via **`verify()`** `:1548-1637` typisiert: `type ∈ {Kunde, Lead}` → Konversion zu Lead; sonst Distributor/Hersteller/Architekt/Bank/… → andere Zielobjekte. `verify()` blockt Konversion, wenn Anfrage **keine Produkte** (`:1560-1564`, 422) oder ein Produkt **ohne zugewiesenen Mitarbeiter** hat (`:1566-1574`, 422) — echtes Gate.
- **Anfrage→Lead-Builder:** private `lead()` `:2680-2767` — erzeugt `new_leads` (`status='Lead'`, `source='Anfrage'`, `customer_no` Jahr+Random `:2683`), das Haupt-Objekt `LeadAlternativeAdd` (`object_name='Privathaus'`, `main=1`, `stage='lead'` `:2729-2744`) und je Anfrage-Produkt eine `LeadProductList`-Zeile mit durchgereichtem `department_id`/`employee_id`/`field_employee` `:2752-2762`.
- **Quellen-Erfassung:** `new_leads.source` gepflegt (SQL: Flyer 11, Messe 10, Empfehlung 9, Google Ads 9, Website 8, Telefon 5) — **echte Lead-Source-Attribution vorhanden.**
- **Massen-Import:** `LeadImportController` (CSV `index/preview/confirm`, `:575-577`).

### 1.2 Qualifizierung
- **`NewLeadsController::qualified($id)`** `:1750-1798` — regelbasiertes Qualifizierungs-Gate mit gestufter Prüfung: (a) Produkt gewählt? (`lead_product_lists` vorhanden, sonst Status „KEIN PRODUKT AUSGEWÄHLT…"); (b) Adresse vollständig (Straße+PLZ+Ort, sonst „KEINE KONTAKTDATEN"); (c) Kontaktkanal vorhanden — differenziert nach fehlend Mail/Telefon → Status-Hinweis „bitte per Brief/telefonisch/per E-Mail Kontakt aufnehmen"; nur wenn alles erfüllt → `status='QUALIFIZIERT'` `:1795`.
- **Duplikat-Erkennung (zweistufig):**
  - Intake-Zeit: `lead()`-Builder prüft Namens-/Adress-Match case-insensitiv vor Neuanlage (`:2691-2701`) → aktualisiert statt dubliziert.
  - Nachträglich: `NewLeadsController::duplicates()` `:10275` (Übersicht) + `mergeDuplicate()` `:10524` (Merge) + `destroyDuplicate()` `:10516`. Zusätzlich ein Adress-basierter Duplikat-Report `:5159-5408` (Haupt-Adresse aus `new_leads` `:5162` + Objekt-Adresse aus `lead_alternative_adds` `:5221`, gemergt `:5277`, Status `duplicate` `:5404`).
- **Junk/Restore-Workflow:** Lead-Ebene `junk()` `:3319` / `unjunk()` `:3342`; Objekt-Ebene `junkObject()` `:13824` / `restoreJunkObject()` `:13896` / `restoreDeletedObject()` `:14019` (typisierte `JsonResponse`, jung). Sortier-Buckets `qualified_sort` `:3231`, `not_qualified_sort` `:3237`, `junk_sort` `:3249`. SQL: 19/71 `lead_alternative_adds` soft-gelöscht — Junk wird real genutzt.

### 1.3 Pipeline / Kanban
- **Board:** `LeadOverviewController::kanban()` `:222` + `kanbanFeed()` `:450` (Server-Daten), Frontend `public/js/kanban.js` (17k Z.). Filter-Presets pro Nutzer: `KanbanFilterSetting` CRUD (`kanbanFilterSettingsIndex/Store/Update/MakeDefault` `:6158-6421`), Kunden-Suche `kanbanCustomerSearch` `:6275`, Karten-Geo `kanbanBranchAddresses` `:6337`.
- **Stufen-Move:** `updateStage()` `:4625` (typisierte `: JsonResponse`, delegiert an Model-Hook) + `moveStageWorkflow()` `:4905`. Der eigentliche FK-`lead_stage_id` wird zentral im Model-Hook `LeadProductList::deriveLeadStageId()` (`app/Models/LeadProductList.php:144-175`) mit Synonym-Fold abgeleitet (`follow_up→offer`, `accepted→deal`). **Ein Ordnungs-Gate:** `requiresAcceptedOfferBeforeEnteringDeal` `:5330` beim Eintritt in `deal` (per Flag `skip_offer_gate_without_folder` umgehbar `:5108`).
- **Wahrheit:** Laut Weiche 1/6 (`docs/architektur-entscheidungen.md`) ist `lead_stages` die **eine Phasen-Wahrheit**; Kanban-Karte = `lead_product_lists` (Gewerk-Ebene), d.h. **ein Kunde kann mehrere Karten haben** (je Gewerk). Das ist eine bewusste, tragfähige Entscheidung.

### 1.4 Objekt- / Bedarfserfassung
- **Objekt = `lead_alternative_adds`** (71 Z., **193 Spalten**, God-Table — Code-Audit 1D): trägt Adresse, Geo, `stage`, `periority`, plus PV/WP/Dach-Detailfelder. Mehrere Objekte je Kunde möglich (`main`, `address_no`). Detail-Auslagerung `lead_alternative_pv_wp_details` (118 Sp.) bereits als Strangler-Muster vorhanden.
- **Bedarf = `lead_product_lists`** (52 Z., 43 Sp., 11 FK, 13 Idx — **gut modelliert**): je Zeile ein Gewerk mit `product_id`, `service_id`, `department_id`, `employee_id`, `field_employee`, `status`, `stage`, `lead_stage_id`, `accepted_offer_folder_id`. Verwaltung: `saveProduct()` `:6042`, `updateProduct`, `deleteProduct`, `getCustomerProduct` `:820`.
- **Roof/PV-Erfassung** im Intake (`roofs.*` Validierung `:587-600`) — PV-spezifische Bedarfserfassung integriert.
- **Nachbar-Akquise:** `neighbor()` `:9533` / `neighborData()` `:9617` / `neighborProducts()` `:9613` — Geo-basierte Nachbarschafts-Ansprache (Anlagen in der Nähe). Eigenständige Vertriebsfunktion.
- **Aktivitäts-/Historie-Feed je Kunde:** `customerProfileFeed()` `:2544`, `customerFeed()` `:4021`, `loadHistoryFeed()` `:10562`; gespeist über Event `LeadRecordChanged → StoreLeadActivity` (`EventServiceProvider.php:29-30`, Tabelle `lead_activity_logs`, 21 Z.). **Der einzige fachliche Auto-Trigger im Vertrieb, der zuverlässig feuert** (Aktivitäts-Log, keine Prozess-Folge).

### 1.5 Zuweisung / Routing
- **Zuständigkeits-Anker liegt gefüllt:** `lead_product_lists.department_id` **52/52 belegt**, 8 distinkte Abteilungen (SQL). Also je Gewerk eine Abteilung zugeordnet.
- **Routing-Helfer existiert und ist verdrahtet:** `NewLeadsController::getLeadEmployee()` `:5988-6039` — matcht `product_positions` auf `(article_group_id, department_id, service_id)` `:5995-5999` → abgeleitete Position-IDs → `department_positions`-Join → Mitarbeiter-Vorschlagsliste `:6010-6025` (mit Fallback auf alle Abteilungs-Mitarbeiter, wenn kein Positions-Match). **Das ist echtes kontextbasiertes Zuständigkeits-Routing** — als **Vorschlags-Endpoint** (Multi-Picker im Frontend), nicht als Auto-Zuweisung. Spiegel-Funktion in `InquiryController::departmentEmployees()` `:1701` / `getEmployee()` `:1768`.
- **Datenbasis dünn:** `product_positions` nur **13 Zeilen** — die Routing-Regeln sind erst rudimentär gepflegt.

---

## 2. STÄRKEN (gut / nutzbar)

1. **Sauberes, gut indiziertes Bedarfs-Modell.** `lead_product_lists` (11 FK, 13 Idx, `department_id` 52/52) ist die tragfähigste Vertriebs-Tabelle: Gewerk-granular, mit gefülltem Zuständigkeits-Anker. Der Kanban-Move läuft zentral über den Model-Hook mit deterministischem Fold — die Stufen-Kette ist in den Daten **konsistent** (SQL: alle 52 Zeilen korrekt abgeleitetes `lead_stage_id`, 0 NULL; `follow_up→2`, `accepted→5` = Fold greift).
2. **Pipeline sauber als Daten-Wahrheit.** `lead_stages` ist eine echte, pflegbare Stufen-Tabelle (11 Stufen, `key`/`name`/`color`/`icon`/`sort_order`/`is_closed`/`is_protected`), nicht hartkodiert. Neue Stufe `abnahme` wurde per Migration additiv aktiviert — belegt, dass die Struktur erweiterbar ist.
3. **Intake HAT Validierung — und ein echtes Anfrage-Gate.** `NewLeadsController::store()` validiert serverseitig inkl. FK-`exists`-Checks (`:550,:563`); `InquiryController::verify()` blockt Konversion ohne Produkt/ohne Mitarbeiter (422). Das widerlegt für DIESEN Bereich die pauschale „Validierung fehlt"-Kritik (s. Abweichung A).
4. **Duplikat-Abwehr auf zwei Ebenen.** Case-insensitiver Namens-/Adress-Match schon bei der Anfrage-Konversion (`InquiryController.php:2691-2701`, aktualisiert statt dupliziert) plus nachgelagerter Duplikat-Report + Merge (`NewLeadsController::duplicates/mergeDuplicate`). Für ein Vertriebs-CRM die richtige Grundfunktion — vorhanden.
5. **Lead-Source-Attribution real gepflegt.** `new_leads.source` breit gefüllt (6 Kanäle). Fundament für Kanal-Auswertung/ROI ist da (heute nur Rohdaten, kein Report).
6. **Kontext-Routing ist gebaut UND verdrahtet.** `getLeadEmployee()` liefert Positions-genaue Mitarbeiter-Vorschläge aus `product_positions`+`department_positions`. Anders als der tote `SmartroutingService` (Formular-Routing, 0 Aufrufer) ist DIESER Zuständigkeits-Router live — er braucht nur mehr Regel-Daten (`product_positions` 13 Z.) und einen Default-Übernahme-Schritt.
7. **Junk/Restore mit Grund + Aktivitäts-Log.** Junk trägt Grund (`junk_reason`/`junk_note`), Objekt-Junk als typisierte `JsonResponse` (junge Zone). `LeadRecordChanged`-Event speist einen echten Aktivitäts-Feed je Kunde.
8. **Nachbar-Akquise** als eigenständiger, oft fehlender Vertriebs-Baustein bereits vorhanden (Geo-basierte Nachbaransprache).

---

## 3. SCHWÄCHEN (fehlt / hakt)

1. **[P1] Der Phasenwechsel löst KEINE Vertriebs-Folge aus.** `Lead→Angebot` (`LeadOverviewController.php:5140-5142`) schreibt nur status/stage/history — **0 Task/Reminder „Angebot erstellen"**. Der ideale Auslöse-Punkt ist ungenutzt (Intelligenz-Audit K1, Hebel H-A1). Konkrete Folge: **liegengebliebene Angebote werden nicht erinnert** — direkter Umsatz-Verlust-Vektor. Baustein `FollowUpCreator::sync()` liegt bereit.
2. **[P1] Status-Feld als Fehlermeldungs-Halter zweckentfremdet.** `qualified()` schreibt bei jedem Fehl-Check ganze deutsche Sätze in `new_leads.status` („KEIN PRODUKT AUSGEWÄHLT, BITTE PRODUKTAUSWAHL ERMITTELN", „um zu qualifizieren, bitte per Brief Kontakt aufnehmen") `:1763-1792`. Das **zerstört den Status als auswertbares Feld** (SQL zeigt bereits `QUALIFIZIERT`/`Lead`/`Active`/`Von Junk wiederhergestellt` gemischt) und macht jede Status-Segmentierung unmöglich. Der Qualifizierungs-Zustand gehört in ein eigenes Feld/Enum, die Fehlermeldung in die Response (nicht in die DB).
3. **[P1] Kein Reihenfolge-Schutz in der Pipeline.** `moveStageWorkflow` erzwingt keine Ordnung (`stageExists` lässt jede aktive Stufe zu, Intelligenz-Audit Achse 2); das einzige Gate (Angebot-vor-Auftrag) ist per Flag umgehbar. Lead→completed-Sprung / „Abnahme ohne Montage" nicht markiert. Weiche 2 (flexibel-mit-Warnung) noch nicht gebaut.
4. **[P1] Zuständigkeit bleibt manueller Klick, obwohl Anker + Router da sind.** `getLeadEmployee()` ist nur Vorschlags-Endpoint; die **finale Zuweisung ist überall Handarbeit / Ersteller-Default** (`FollowUpCreator.php:51-54` Fallback `[creatorEmployeeId]`, `PersonalTaskController.php:833`). Höchstfrequente Tätigkeit ohne Default-Übernahme (Hebel H-V1). „Wer anlegt, kriegt's."
5. **[P1] `lead_alternative_adds` = God-Table (193 Sp., 30 TEXT).** Das Objekt-/Bedarfs-Modell vermischt Stammdaten mit PV/WP/Dach-Detail; `SELECT *` in 25 Controllern (Code-Audit 1D-D). Bei Wachstum Lese-Flaschenhals; nur 1 FK. Zerlegungs-Kandidat #1 des Bereichs.
6. **[P2] Adress-Doppelerfassung Kunde↔Objekt.** `new_leads` und `lead_alternative_adds` tragen beide street/plz/city/full_address; Haupt-Intake-Pfad tippt Objekt-Adresse neu (`NewLeadsController.php:678-683` liest `street2/postcode2/city2`), Zweit-Pfad reicht durch — **inkonsistent** (Hebel H-D1). Beim EFH-Regelfall dieselbe Adresse zweimal.
7. **[P1] Untestbare Gott-Controller.** `NewLeadsController` 14k Z./121 Methoden/267 `DB::table` — **Test = NULL** (Code-Audit 1.7). Jede Änderung am Intake/Qualifizierungs-Pfad ist ungeschützt. Drei Speicher-Muster für `lead_product_lists` mit drei Default-Status (`Lead`/`open`/`archive`, Code-Audit 1.4) — Status-Zoo an der Quelle.
8. **[P2] `lead_stage_id`-Ableitung dreifach.** Kanon-Hook vs. `NewLeadsController::normalizeCompanyStage()` (ohne Fold, `:12977-13004`) vs. `match()` mit Tippfehler `archiv` (`:9791-9805`). Heute konsistent, **latent divergierbar** je Schreibweg.
9. **[Lücke] Kein Lead-Scoring / keine Priorisierung.** `periority` (sic) existiert als Freitext-Feld auf Objekt, aber es gibt **kein berechnetes Scoring** (Wert × Wahrscheinlichkeit × Alter). Kanban-Karten tragen keinen Wert/kein Alter-Signal (UX-Audit). Reine Zustandsverwaltung, keine Vertriebs-Intelligenz.
10. **[Lücke] Keine Funnel-/Kanal-Auswertung.** `source` ist gepflegt, aber es gibt keinen Conversion-Report je Kanal, keine Stage-Verweildauer, keine „Leads ohne Aktivität seit X"-Sicht als Wächter.

---

## 4. REIFE je Funktion

| Funktion | Reife | Beleg / Begründung |
|---|---|---|
| Direkt-Lead-Intake (`store`) | **produktiv** | validiert, legt Kette an, live genutzt (52 Leads) |
| Anfrage-Trichter (`inquiries`) | **produktiv** | eigener Lebenszyklus, 40 Z., 4 Status; `verify()` mit Gate |
| Anfrage→Lead-Konversion (`lead()`) | **produktiv** | firsthand geprüft, mit Dedup + Objekt + Gewerk-Anlage |
| Qualifizierung (`qualified`) | **produktiv, aber roh** | funktioniert, aber Status-Feld-Missbrauch (Schwäche 2) |
| Duplikat-Erkennung/Merge | **produktiv** | zweistufig, live (`mergeDuplicate` gegated) |
| Junk/Restore (Lead+Objekt) | **produktiv** | mit Grund + Log; 19/71 Objekte gejunkt |
| Kanban-Board + Filter-Presets | **produktiv** | `kanban()`/`kanbanFeed()`, Nutzer-Presets, 17k JS |
| Stufen-Move + Fold-Hook | **produktiv** | Daten konsistent, zentraler Hook |
| Objekt-Erfassung (`lead_alternative_adds`) | **produktiv, überladen** | God-Table, funktioniert aber Flaschenhals |
| Bedarf/Gewerk (`lead_product_lists`) | **produktiv, sauber** | gut modelliert, 52/52 department_id |
| Lead-Source-Attribution | **produktiv (Rohdaten)** | gepflegt, aber kein Report |
| Zuständigkeits-Routing (`getLeadEmployee`) | **prototyp/verdrahtet** | Router live, aber nur Vorschlag; Regel-DB dünn (13 Z.) |
| Nachbar-Akquise | **produktiv** | eigener Geo-Pfad |
| Aktivitäts-Feed (`LeadRecordChanged`) | **produktiv (dünn)** | einziger Auto-Trigger, nur Log (21 Z.) |
| Massen-Import (CSV) | **produktiv** | preview/confirm-Flow |
| Phasen-Move → Prozess-Folge | **tot / nie gebaut** | 0 Task/Reminder bei Lead→Angebot (K1) |
| Lead-Scoring / Priorisierung | **tot / nie gebaut** | kein berechnetes Scoring, `periority` nur Freitext |
| Funnel-/Kanal-Report | **tot / nie gebaut** | keine Conversion-/Verweildauer-Auswertung |
| Default-Zuweisung (Auto-Owner) | **tot / nie gebaut** | Anker da, Übernahme fehlt (H-V1) |
| Reihenfolge-/Übersprung-Wächter | **tot / nie gebaut** | Weiche 2 offen |

---

## 5. AUTOMATISIERUNGS-REIFE des Bereichs gesamt

### **MITTEL** (mit klarer Zweiteilung: Datenhaltung reif, Prozess-Automatik unreif)

**Begründung:**
Der Vertriebsbereich ist als **Erfassungs- und Zustandssystem produktiv und teils sauber** (gut modelliertes `lead_product_lists`, konsistenter Kanban-Fold-Hook, echte Duplikat-Abwehr, validierter Intake, gepflegte Source-Attribution, gefüllter `department_id`-Anker) — aber als **mitdenkendes Vertriebssystem schwach**: der Phasenwechsel — der ideale Auslöse-Punkt — stößt **keine einzige** Vertriebs-Folge an (kein „Angebot erstellen"-Task, kein Nachfass-Trigger, keine Kickoff-Aufgabe), die Zuständigkeits-Zuweisung bleibt manueller Klick trotz vorhandenem Router + Anker, und es fehlt jede Vertriebs-Intelligenz (Scoring, Funnel-Report, Verweildauer-Wächter, Reihenfolge-Markierung).

Das Besondere und Ermutigende: **Der Bereich liegt näher an „HOCH" als die reine Prozess-Automatik vermuten lässt, weil die teuren Bausteine schon gebaut sind** — `FollowUpCreator` (dedup-sichere EINE Erzeugungsstelle), der `department_id`-Anker (52/52), der `getLeadEmployee`-Router und der saubere Stage-Move-Punkt. Der billigste Sprung von MITTEL→HOCH ist **Verdrahten, nicht Neubau**: (1) `FollowUpCreator::sync()` am Stage-Move für Lead→Angebot-Task (H-A1, Aufwand S), (2) Default-Owner-Vorschlag aus `department_id` in die Zuweisung übernehmen (H-V1), (3) Qualifizierungs-Status in eigenes Feld ziehen. Ohne diese drei bleibt der umsatztragende Alt-Kern eine **überwiegend stumme Vertriebs-Datenbank (Intelligenz-Grad ~2)** — sauber erfasst, aber ohne Schub.

**Nicht HOCH**, weil jede Vorwärts-Kausalität ein Medienbruch ist und Scoring/Funnel/Wächter komplett fehlen. **Nicht NIEDRIG**, weil Erfassung/Dedup/Routing-Router/Kanban-Konsistenz real und tragfähig sind und die Automatik-Bausteine bereitliegen.

---

## Abweichungen / Korrekturen zu den Basis-Audits

- **Abweichung A (Korrektur):** Die pauschale Aussage „Write ohne Server-Validierung" (Code-Audit 2.2a) gilt für den **Haupt-Lead-Intake NICHT**: `NewLeadsController::store()` validiert breit inkl. FK-`exists` (`:528-600`), und `InquiryController::verify()` hat ein echtes fachliches Gate. Die Validierung ist inline (kein FormRequest) und im Bereich lückenhaft (`CustomerMeasureController` etc.), aber der Kern-Intake ist abgesichert.
- **Ergänzung B:** Der `getLeadEmployee()`-Zuständigkeits-Router (`:5988`) war in den Basis-Audits nicht als **verdrahtete** Routing-Fähigkeit belegt (dort lag der Fokus auf dem TOTEN `SmartroutingService` für Formular-Routing). Zuständigkeits-Routing ist live vorhanden — nur ohne Default-Übernahme und mit dünner Regel-DB (`product_positions` 13 Z.). Das verkleinert den H-V1-Aufwand.

---

## Gelesen / Nicht-gelesen

**Firsthand gelesen (Datei:Zeile):** `NewLeadsController` (`store` `:523`, `qualified` `:1750`, `getLeadEmployee` `:5988`, Duplikat-Report `:5159`, Methoden-Index über alle 121 public); `InquiryController` (`verify` `:1548`, `lead()`-Builder `:2680`, `junk/unjunk`, Methoden-Index); `LeadOverviewController` (Methoden-Index Kanban/Move firsthand, Move-Logik aus Intelligenz-Audit übernommen); `CustomerStageController` (Methoden-Index); Routen `web.php` (123/48/43/6/4 gezählt); `EventServiceProvider` (LeadRecordChanged-Wiring). **SQL firsthand:** `lead_stages` (11 Stufen voll), `new_leads.status`+`.source`-Verteilung, `lead_product_lists` status/stage/lead_stage_id + `department_id` 52/52/8-distinct, `inquiries.status`, `inquiry_product_lists` 39, `product_positions` 13, `lead_alternative_adds` 71/19-softdel, `lead_activity_logs` 21, `department_positions` 50, `departments` 16. **Als Kontext gelesen (nicht dupliziert):** `code-audit.md`, `intelligenz-audit.md`, `automatisierungs-hebel.md` (vollständig für Vertriebs-Bezüge).

**Nicht / nur oberflächlich gelesen:** die Methodenkörper der meisten 121 `NewLeadsController`-Methoden (nur ~8 im Detail); `LeadOverviewController` Move-Interna (aus Intelligenz-Audit übernommen, nicht re-verifiziert); `kanban.js` (17k Z., nur Struktur aus Code-Audit); Blade-Views des Bereichs (`new_leads/customer_profile.blade.php` 19k Z., nur Kennzahl); `CustomerStageController`-Körper; `LeadImportController`-Interna; `MassManagerController` (Pattern C nur aus Code-Audit).

## NICHT-VERIFIZIERT (offene Punkte)

- **Clientseitige Verkettung:** Ob Blade/JS beim Stage-Move clientseitig doch einen Angebots-Anstoß oder Zuständigen-Vorschlag auslöst, ist **serverseitig widerlegt, clientseitig NICHT-VERIFIZIERT** (17k Z. `kanban.js` + 17k Z. Inline-JS im customer_profile nicht durchgelesen).
- **Reale Frequenzen:** Alle „hochfrequent/selten"-Einordnungen stützen sich auf Ein-Tages-Seed-Relativhäufigkeit, nicht auf Prod-Volumen (~3000 Kunden). Ändert Yamas Frequenzangabe die Priorität, gilt Yama.
- **`getLeadEmployee`-Nutzungsgrad:** dass der Router im Frontend tatsächlich als Vorschlag angezeigt und wie oft er übernommen wird, ist statisch belegt (Endpoint existiert + Route), aber **Nutzungs-/UX-Fluss NICHT-VERIFIZIERT**.
- **`normalizeCompanyStage`-Live-Nutzung:** dass ein realer Schreibweg den nicht-foldenden Zweitpfad nutzt (Divergenz aktiviert), ist **latent, nicht in Daten belegt** (heute konsistent).

## Selbstkritik

- **Bereichs-Grenzen unscharf:** Angebot/Auftrag (`offers`/`deals`) und Task/Follow-up (`personal_tasks`) greifen tief in meinen Pipeline-Bereich, gehören aber anderen Experten. Ich habe die Vertriebs-Sicht auf die Übergänge belegt (Lead→Angebot-Auslöser, Zuweisungs-Router), aber die Angebots-/Task-Interna bewusst nicht bewertet — mögliche Doppelung/Lücke an der Naht.
- **Gott-Controller-Tiefe:** 121 Methoden in `NewLeadsController`, davon ~8 im Detail gelesen. Die Verallgemeinerung „Erfassung produktiv, Prozess-Automatik unreif" stützt sich auf die Schlüsselmethoden + die 14:1-LOC-Ratio des Code-Audits, nicht auf Vollzählung — einzelne versteckte Automatismen könnten übersehen sein.
- **Kanban-Move aus Zweitquelle:** Die Move-Reihenfolge-/Gate-Befunde habe ich aus dem Intelligenz-Audit übernommen (dort firsthand belegt), nicht selbst am Code re-verifiziert.
- **Datenbasis dünn:** Ein-Tages-Dev-Seed; alle Reife-/Frequenz-Urteile sind strukturell + Stichprobe, nicht volumengemessen.
