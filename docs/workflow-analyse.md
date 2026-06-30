# Workflow-Analyse — CRM-Kernprozess (read-only)

**Reine Lese-Analyse. Nichts geändert, nichts gebaut, nichts gelöst.** Stand: 2026-06-30 · Branch `private/app-code-backup`.
Zweck: den Kern-Geschäftsprozess so tief verstehen, dass die **Schwächen sichtbar** werden — als Grundlage für ein Gespräch zwischen Yama und dem Planer. Die Termin-/Kalender-Seite ist in [kalender-termine-bestandsaufnahme.md](kalender-termine-bestandsaufnahme.md) beschrieben und wird hier nur als Verzweigung referenziert.

> **Kernbefund in einem Satz:** Die Kette *Lead → Anfrage → Angebot → Auftrag → Aufmaß → Projekt → Rechnung*
> ist **kein durchgehender Daten-Strang mit erzwungener Reihenfolge**, sondern eine Menge **parallel
> geführter Datensätze**, die über dasselbe Tripel `(customer_id, product_id, alternative_id)` lose
> zusammenhängen. Der „Fortschritt" wird an **vielen Stellen gleichzeitig** geführt — ohne eine einzige
> verbindliche Quelle. Genau dort liegen die Risiken.

---

## 1. Verkettung — der reale Fluss, wie er im Code ist

### Die Stationen und ihre Tabellen
| Station | Tabelle(n) | Schlüssel |
|---|---|---|
| **Anfrage** | `inquiries` (+ `inquiry_product_lists`) | eigene ID |
| **Lead/Kunde** | `new_leads` + **`lead_product_lists`** (1 Zeile je Gewerk) | `(customer_id, product_id, alternative_id)` |
| **Angebot** | `offers` (+ `offer_folders`, `offer_details`) | `(customer_id, product_id, alternative_id)` |
| **Auftrag** | `deals` | dito + **`offer_id`** (seit P1-16 verknüpft) |
| **Aufmaß** | `deal_measurements` | `deal_id` |
| **Projekt** | `projects` | dito Tripel — **KEIN `deal_id`** |
| **Rechnung** | `deal_invoices` **und** `invoices` | siehe unten (zwei Systeme) |
| **Ticket/Reklamation** | `problems` | `(customer/product/...)` + `lead_stage_id` |

### Was löst was aus
- **Anfrage → Lead:** Wird eine Anfrage „verifiziert", entsteht ein `new_leads`-Eintrag **plus** eine `lead_product_lists`-Zeile. Das passiert an **zwei** Stellen: `InquiryVerificationController:513/554` **und** `InquiryController:2705/2752`. (→ zwei Wege, dasselbe zu tun.)
- **Lead → Angebot:** Angebote entstehen über den Offer-Wizard (`OfferWizardController`) und werden über `offer_folders`/`offer_details` ausgearbeitet. Die Verbindung zum Lead läuft über das Tripel + `lead_product_lists.accepted_offer_folder_id`.
- **Angebot → Auftrag:** `DealController@dealStore` (in P1-16 wiederhergestellt) legt den Auftrag aus der `lead_product_lists`-Zeile an, setzt `deals.status='deal'`, zieht — falls vorhanden — `offer_id`/`offer_folder_id`, und setzt `lead_product_lists.status='deal'`.
- **Auftrag → Aufmaß:** `DealMeasurementController` führt das Feinaufmaß und setzt `deals.measurement_status` / `deals.project_status`.
- **Auftrag → Projekt:** **kein direkter Auslöser.** `projects` ist eine eigene Tabelle ohne `deal_id`; Projekte werden über den Planer (`PlaningController`) geführt. Der Auftrag trägt zwar ein eigenes `project_status`-Feld, aber `deals` und `projects` sind **zwei getrennte Datensätze**, nur über das Tripel verbunden.
- **Auftrag → Rechnung:** `DealInvoiceController@store` verlangt **`deal_id` (Pflicht)** → sauberer Auftrag→Rechnung-Pfad (`deal_invoices`). **Daneben** gibt es die generische `invoices`-Tabelle (`InvoiceController`), die nur an `customer_id`/`object_id` hängt — **ohne Auftragsbezug**.
- **Verzweigungen:** Termine (7 Auto-Quellen, s. Kalender-Doc), Tickets (`problems`), Aufgaben (`general_tasks`) hängen jeweils am selben Tripel bzw. an `deal_id`/`customer_id`.

### Verkettet vs. unabhängig nebeneinander
- **Erzwungen verkettet** ist wenig: nur `deal_measurements → deal_id` und `deal_invoices → deal_id` sind echte Pflicht-FKs.
- **Lose nebeneinander** (über das Tripel, nicht über FK) sind: `offers`, `deals`, `projects`, `lead_product_lists`. Sie *sollten* eine Reihenfolge bilden, sind aber technisch gleichrangige Datensätze. Es gibt **keinen erzwungenen** „erst Angebot, dann Auftrag, dann Projekt"-Strang.

---

## 2. Kausalität — passiert das Richtige in der richtigen Reihenfolge?

### Der „Fortschritt" wird an extrem vielen Stellen geführt
**`lead_product_lists`** trägt allein **~11 Status-/Stufen-Felder**: `status`, `stage`, `stage_mode`, `product_stage_id`, `product_task_phase_id`, `work_status`, `lead_stage_sub_stage_id`, `offer_acceptance_status`, `old_stage`, `stage_history`, `product_stage_history` (+ 4 `moved_without_offer_acceptance*`-Felder).
Geschrieben wird darauf aus **~12 Controllern**: Phase- (2), Inquiry- (2), Offer- (4: Wizard/Folder/Kanban/Gallery), NewLeads, Measurement und Kanban (3: LeadOverview, KanbanCustomerPanel, LeadStageSubStage). → **Es gibt keine einzige verbindliche Quelle**, wo ein Lead im Prozess steht.

**`deals`** trägt **5** Status-Felder: `status`, `deal_status`, `measurement_status`, `project_status`, `status_msg` — gesetzt aus `DealController` und `DealMeasurementController`. Vier davon sind parallele kleine Zustandsautomaten an einem Datensatz; welcher „der" Status ist, ist nicht definiert (die Auftragsliste filtert auf `status IN (order,deal)`, der Workflow nutzt `deal_status` = confirm/inconfirm, das Aufmaß `measurement_status`, der Bau `project_status`).

### Vier getrennte Kanban-/Stufen-Welten
1. **Anfrage-Kanban** → `inquiries.status`
2. **Lead-Kanban** → `lead_product_lists.stage`/`status` (12 Schreiber)
3. **Angebots-Kanban** → `offers.status` (`OfferKanbanStageController`)
4. **Auftrags-Kanban** → `deals.status` (`DealController@updateStatus`)

Sie laufen **getrennt**. Ein Fortschritt in einem zieht den anderen **nicht zwingend** nach. Beispiel-Bruch: `dealStore` setzt `lead_product_lists.status='deal'` — aber eine spätere manuelle Kanban-Verschiebung kann denselben Wert wieder überschreiben (12 Schreiber konkurrieren), und der **Anfrage**-Status bleibt davon ohnehin unberührt.

### Belegter kausaler Bruch (das gesuchte „Urlaubs-Muster")
In `LeadOverviewController` (~Z. 4983-5031) gibt es ein **„Offer-Gate"**, das **übersprungen** werden kann: Wird ein Lead in eine spätere Stufe geschoben, **ohne** dass ein Angebot angenommen wurde, setzt das System
`offer_acceptance_status = 'moved_without_offer_acceptance'` und `moved_without_offer_acceptance = true`.
→ **Das System erzwingt die Angebots-Annahme NICHT, es protokolliert nur, dass sie übersprungen wurde.** Damit ist „Angebot angenommen → Auftrag" eine **freiwillige**, keine erzwungene Kausalität. *(Ob das gewollt ist, ist eine Geschäftsregel — s. Teil 5.)*

### Fehlende kausale Verbindungen
- **Angebot angenommen ↛ Auftrag/Lead-Stufe automatisch.** Es gibt `accepted_offer_folder_id`, aber kein erzwungenes „Annahme erzeugt Auftrag". `dealStore` ist ein **manueller** Schritt.
- **Auftrag ↛ Projekt.** Kein Auslöser; Projekt ist separat (Planer).
- **Rechnung ↛ Auftragsstatus.** Eine Rechnung ändert den Auftragsstatus nicht; „bezahlt" lebt in `deal_invoices` getrennt.

---

## 3. Plausibilität — können unplausible Zustände entstehen?

| Möglicher Zustand | Warum möglich | Bewertung |
|---|---|---|
| **Rechnung ohne Auftrag** | Generische `invoices` hängen nur an `customer_id`/`object_id`, kein `deal_id` | erlaubt — **plausibel nur, wenn bewusst „freie" Rechnungen gewollt** (Geschäftsregel) |
| **Auftrag ohne (angenommenes) Angebot** | `dealStore` verknüpft `offer_id` nur *falls vorhanden*; Offer-Gate überspringbar | erlaubt — Geschäftsregel |
| **Lead in Stufe „Auftrag" ohne Angebots-Annahme** | `moved_without_offer_acceptance` | erlaubt + protokolliert — Geschäftsregel |
| **Projekt ohne Auftrag** | `projects` ohne `deal_id`, eigener Anlagepfad | technisch möglich |
| **Auftrag ohne Gewerk-/Abteilungsbezug** | `deals` Pflichtfelder sind customer/product/alternative/service/employee — `department_id` ist **nullable** | Auftrag ohne Abteilung möglich (relevant fürs Controlling) |
| **Zwei Rechnungs-Wahrheiten zum selben Vorgang** | `deal_invoices` (auftragsbezogen) **und** `invoices` (kundenbezogen) nebeneinander | Umsatz-Doppelzählung/Lücke möglich |

---

## 4. Datenkonsistenz-Risiken (aus Paket 2 gelernt)

1. **Mehrere Schreibpfade auf dieselbe wichtige Größe — verschärft.** Beim Urlaub waren es 4 Pfade auf `remaining_day`; hier sind es **~12 Schreiber auf `lead_product_lists.stage/status`**. Dieselbe Klasse von Risiko, deutlich größer. Welche Reihenfolge/welcher Schreiber „gewinnt", ist nicht definiert.
2. **Mit-Überschreiben bei scheinbar unzusammenhängender Aktion.** `dealStore` schreibt `lead_product_lists.status` mit; andere Kanban-/Offer-/Phase-Aktionen schreiben dasselbe Feld — eine harmlose Aktion an einer Stelle kann den Pipeline-Stand an anderer Stelle verändern. (Analog zu `remaining_day` beim Profil-Update.)
3. **Fehlende Gegenbuchung bei Storno/Löschung.**
   - Auftrag löschen = **SoftDelete** (P1-16). `deal_invoices` haben zwar `onDelete cascade`, das greift aber **nur beim Hard-Delete** → ein soft-gelöschter Auftrag lässt seine Rechnungen/Umsätze **stehen**; es gibt **keine Storno-/Gegenbuchung** des Umsatzes.
   - `lead_product_lists.status='deal'` wird beim Auftrags-Storno **nicht** zurückgesetzt → der Lead bleibt in „Auftrag", obwohl der Auftrag weg ist (verwaister Pipeline-Stand).
4. **Fünf Status-Felder am Auftrag** ohne definierte Hoheit → können auseinanderlaufen (z. B. `status='deal'` aber `project_status` schon „fertig").

---

## 5. Schwächen (das Ergebnis) — vorgelegt, NICHT gelöst

> Schweregrad: **kritisch** = Datenfehler/Geld/rechtlich · **mittel** = Inkonsistenz/Verwirrung · **kosmetisch**.
> „GR" = in Wahrheit eine **Geschäftsregel-Frage** (hängt davon ab, wie Yama arbeitet), kein Bug.

| # | Schwäche | Beleg | Auswirkung | Bewertung |
|---|---|---|---|---|
| 1 | **Kein Single-Source-of-Truth für den Pipeline-Stand.** ~12 Controller schreiben `lead_product_lists.stage/status`; 5 Status-Felder am Auftrag. | Schema + Grep (Teil 2) | Stand kann je nach letztem Schreiber kippen; schwer nachvollziehbar | **kritisch** |
| 2 | **Vier getrennte Kanbans** (Anfrage/Lead/Angebot/Auftrag), nur lose gekoppelt. | 4 Controller (Teil 2) | Fortschritt in einem zieht andere nicht nach; widersprüchliche Anzeigen | **mittel** |
| 3 | **Offer-Gate überspringbar** — Lead/Auftrag ohne Angebots-Annahme. | `LeadOverviewController:4983-5031` | „Auftrag ohne Angebot" möglich; nur protokolliert | **mittel (GR)** |
| 4 | **Zwei Rechnungs-Systeme** (`deal_invoices` mit Auftrag vs. `invoices` ohne). | `DealInvoiceController:136` vs. `invoices`-Schema | Umsatz-Doppelzählung/-Lücke; uneinheitliche Wahrheit | **kritisch (GR)** |
| 5 | **Kein Umsatz-Storno bei Auftrags-Löschung** (SoftDelete, Cascade greift nicht). | Deal SoftDelete + FK-Cascade | Umsatz/Rechnung bleibt trotz gelöschtem Auftrag stehen | **kritisch** |
| 6 | **Lead-Stufe wird bei Auftrags-Storno nicht zurückgesetzt.** | kein Rückbuch-Pfad | Lead bleibt fälschlich in „Auftrag" | **mittel** |
| 7 | **Auftrag→Projekt nicht verkettet** (`projects` ohne `deal_id`). | `projects`-Schema | Projekt und Auftrag driften auseinander; Doppelpflege | **mittel (GR)** |
| 8 | **`deals.department_id` nullable** → Auftrag ohne Abteilung. | `deals`-Schema | Controlling/Umsatz-je-Abteilung lückenhaft | **mittel** |
| 9 | **Zwei Anfrage→Lead-Pfade** (Verification + Inquiry). | `InquiryVerificationController:513` / `InquiryController:2705` | Doppelpflege, Divergenz-Risiko | **mittel** |
| 10 | **Status-Felder als Frei-Text** ohne Enum (Auftrag/Lead/Anfrage). | Schemata | Tippfehler-/Inkonsistenz-Risiko (z. B. „deal" vs „Deal") | **kosmetisch–mittel** |

---

## Wichtigste Gesprächspunkte für Yama + Planer
1. **Was ist die verbindliche Quelle für „wo steht dieser Vorgang"?** Heute: keine. (Schwäche 1/2)
2. **Soll „Angebot angenommen" Pflicht vor „Auftrag" sein?** Heute: optional + protokolliert. (Schwäche 3 — GR)
3. **Welche Rechnung gilt — `deal_invoices` oder `invoices`?** Doppelsystem auflösen. (Schwäche 4/5 — GR + kritisch)
4. **Was passiert beim Auftrags-Storno mit Umsatz und Lead-Stufe?** Heute: nichts. (Schwäche 5/6)
5. **Ist „Projekt" ein eigener Vorgang oder eine Auftrags-Phase?** Heute: getrennt. (Schwäche 7 — GR)

*Ende der read-only Analyse. Keine Code-, Schema- oder Datenänderung. Nächster Schritt: Besprechung — dann wird über einzelne Anpassungen entschieden.*
