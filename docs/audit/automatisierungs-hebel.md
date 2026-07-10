# AUTOMATISIERUNGS-HEBEL — konstruktive Wirkungsanalyse (ticket CRM)

> **Status:** READ-ONLY-Analyse, Stand 2026-07-09. Repo `/Users/yamanuri/Documents/ticket`, Branch `private/app-code-backup`.
> **Frage (gedreht):** Nicht „wo fehlt Automatik", sondern **„welcher Hebel hebt den Automatisierungsgrad am stärksten — priorisiert nach Wirkung ÷ Aufwand".**
> **Baut auf:** `docs/audit/intelligenz-audit.md` (5-Achsen-Lückenanalyse + Automatisierungsgrad je Kette, Fund-IDs I-1…I-18), `docs/audit/code-audit.md`, `docs/architektur-entscheidungen.md` (Weichen 1–6 + Glossar), `docs/workflow-sollkonzept.md` (Yamas Soll-Prozess: „automatische Ableitungen").
> **Zieldatei:** Dies ist die **einzige** von diesem Auftrag geschriebene Datei. Parallele Arbeiten (`docs/audit/**`, `docs/agents/**`, `~/wissensregister/**`) wurden **nicht** angefasst.

## Messprinzip (bindend)

**Automatisierungs-Wert = Häufigkeit × Zeitersparnis/Vorgang × Fehlervermeidung ÷ (Risiko × Aufwand).**
Ohne Frequenz keine Priorisierung → deshalb steht unten zuerst eine **Frequenz-Grundlage** (15–20 häufigste Betriebs-Tätigkeiten). Wo Frequenz aus Daten ableitbar ist, ist sie belegt; wo nicht, ist sie **SCHÄTZUNG** markiert und als **Yama-Frage** gesammelt (nicht erfunden).

**Urteil je Hebel (Operanden-Gate, aus dem Intelligenz-Audit übernommen):**
- **(a) SOLL automatisiert** — sicher ableitbar, spart Arbeit, keine Fach-/Rechtsentscheidung.
- **(b) NUR Vorschlag + Bestätigung** — Fach-/Rechtsentscheidung; kein erfundener Wert, bei Unsicherheit markieren/fragen.
- **(c) heikel / bestehende Automatik falsch** — vorhandene Automatik handelt still falsch oder inkonsistent.

**Datenbasis-Warnung:** Die lokale Dev-Restore ist ein **Ein-Tages-Seed** (fast alle Kern-Zeilen `created_at = 2026-06-29`), kein Produktions-Zeitverlauf. **Absolute Raten (Vorgänge/Monat) sind daraus NICHT ableitbar** — nur **Funnel-Verhältnisse** (Anteil Lead→Angebot→Auftrag→Rechnung) und **Relativ-Häufigkeit** der Tätigkeitsarten. Alle „X/Tag"- und „X/Monat"-Werte sind daher **SCHÄTZUNG** und mit Prod-Skala (~3000 Kunden, MEMORY) plausibilisiert, nicht gemessen.

**TABU (nur an den Nähten betrachtet, inhaltlich nicht bewertet):** Nuriva, Video/Jitsi, Invoice-Zone (interne Rechnungs­erzeugungs-Qualität), Legacy Bitrix/NIBE/IMAP. Wo „Abnahme→Rechnung" auftaucht, ist die **fehlende Brücke** bewertet, nicht die Invoice-Zone selbst.

---

# FREQUENZ-GRUNDLAGE — die 15–20 häufigsten Betriebs-Tätigkeiten

**Gemessene Funnel-Form (SQL auf lebendigem Kern):**
`new_leads` 52 → `lead_alternative_adds` (Objekte) 71 → `lead_product_lists` (Gewerke) 52 → `offers` 29 (**56 % der Leads**) → `deals` 14 (**48 % der Angebote**) → `invoices` 11 (**79 % der Aufträge**). Daneben `main_appointments` 80, `customer_notes` 76, `customer_histories` 95.

**Kernaussage der Frequenz:** Die **Berührungspunkte** (Termin, Notiz, Aktivität) sind **häufiger** als die Vorgangs-Übergänge — und die Kette **verjüngt sich stark** (auf 11 Rechnungen kommen 52 Leads, 71 Objekte, 80 Termine, 76 Notizen). Ein Hebel an einer **hochfrequenten Alltags-Tätigkeit** (Termin, Aufgabe, Notiz) multipliziert breiter als ein Hebel am seltenen Kettenende (Rechnung) — es sei denn, der Einzel-Vorgang ist teuer genug (Aufmaß neu tippen, Rechnung vergessen).

| # | Betriebs-Tätigkeit | Frequenz-Grundlage | Tier | Berührte Hebel |
|---|---|---|---|---|
| F1 | Aktivität/History-Eintrag | `customer_histories` 95 (überw. **auto** protokolliert) | **belegt** | — (meist schon auto) |
| F2 | Termin anlegen/planen | `main_appointments` 80 | **belegt** | H-B2, H-V1 |
| F3 | Notiz/Kontakt-Vermerk erfassen | `customer_notes` 76 | **belegt** | H-V4, H-A2 |
| F4 | Objekt erfassen | `lead_alternative_adds` 71 | **belegt** | H-D1 |
| F5 | Lead erfassen | `new_leads` 52 | **belegt** | H-D1, H-A1 |
| F6 | Gewerk anlegen | `lead_product_lists` 52 (department_id **52/52** gefüllt) | **belegt** | H-V1, H-V2, H-A5 |
| F7 | Angebot erstellen | `offers` 29 | **belegt** | H-V3, H-B5 |
| F8 | Auftrag anlegen | `deals` 14 | **belegt** | H-A4, H-A5 |
| F9 | Rechnung erstellen | `invoices` 11 (7 offen / 4 bezahlt) | **belegt** | H-B1, H-A6 |
| F10 | Aufgabe zuweisen | `personal_tasks` 0 im Seed → mehrere je Lead | **SCHÄTZUNG** | H-V1, H-A1 |
| F11 | Follow-up erzeugen | `personal_tasks type=follow_up` (0 im Seed) | **SCHÄTZUNG** | H-A1, H-A2 |
| F12 | Phasenwechsel (Stage-Move) | mehrere je Lead-Lebenszyklus | **SCHÄTZUNG** | H-A1, H-A4, H-A5 |
| F13 | Checkliste/Formular ausfüllen | `product_formulas` 0, `..._routing_rules` 0 im Seed | **SCHÄTZUNG** | H-V2, PlausibilityService |
| F14 | Aufmaß/Feinaufmaß erfassen | `deal_measurements` 0 im Seed → ~1 je Auftrag | **SCHÄTZUNG** | H-D2, H-B3 |
| F15 | Materialliste erstellen/abgleichen | 0 im Seed → ~1 je Auftrag | **SCHÄTZUNG** | H-A5, H-B3 |
| F16 | Heizlast/Auslegung rechnen | Teilmenge WP/Heizung-Gewerke | **SCHÄTZUNG** | H-D2, H-B4, H-B5 |
| F17 | Montageplan/Planner-Item | `planner_items` 0 im Seed | **SCHÄTZUNG** | (Weiche 6, bewusst manuell) |
| F18 | Feld-/Tagesbericht (Monteur) | `customer_reports` 1 im Seed → je Montage-Tag | **SCHÄTZUNG** | H-A2 |
| F19 | Zahlung erfassen/prüfen | `invoices.paid_amount`-Pflege | **SCHÄTZUNG** | H-A4 |
| F20 | Auftrags-Storno | selten (Ausnahmefall) | **SCHÄTZUNG** | H-A3 |

**→ Yama-Frequenz-Fragen** (zur Kalibrierung der Priorisierung, s. Sammlung am Ende): reale Vorgänge/Monat je F1–F20, insb. Termine/Tag (F2), Aufgaben je Lead (F10), Anteil Gewerke mit Heizlast-Rechnung (F16), Montage-Tage/Woche (F18).

---

# TEIL 1 — Vier Hebel-Klassen systematisch

## 1.1 DURCHREICHUNG statt Wiedereingabe

**Vorbefund (positiv, hart belegt):** Die **Geld-Kette ist bereits FK-sauber durchgereicht** — Kunde/Objekt werden nirgends abgetippt, sondern per FK referenziert (`offers.customer_id/alternative_id`, `deals.offer_id`, `invoices.deal_id/offer_detail_id`). Und die **teuerste Positions-Durchreichung ist gebaut**: `InvoiceCanvasController::storeDraftFromOfferDetail` (`app/Http/Controllers/Invoice/InvoiceCanvasController.php:56-87`, Items `:519-619`) kopiert die kompletten Angebots-Positionen (`offer_details.sections` → Rechnungs-Items inkl. `title/qty/unit/unit_price/product_id/distributor_*/source_payload`). Angebotspreise sind **katalog-vorbefüllt** aus `distributor_prices` (`OfferWizardController.php:917-938,:1189-1192`). **Diese Achse ist im Geld-Fluss also überwiegend sauber** — Durchreichungs-Hebel liegen an den *Rändern*, nicht im Zentrum.

| ID | Ort / Beleg | Heutige Handarbeit | Frequenz | Ersparnis/Vorgang | Risiko/Aufwand | Urteil |
|---|---|---|---|---|---|---|
| **H-D1** | Objekt-Adresse wird im Haupt-Lead-Pfad **neu getippt** (`NewLeadsController.php:678-683` liest separate Felder `street2/postcode2/city2/full_address2`); der Zweit-Pfad `:1203-1208` reicht die Kundenadresse durch — **inkonsistent**. | Adresse des Objekts (=Wohnadresse beim EFH-Regelfall) doppelt eintippen | **hoch** (F4/F5: ~jedes Objekt, 71 Objekte / 52 Leads) | ~30–60 s | S / niedrig | **(a)** Objekt-Adresse aus Kunde vorbefüllen, „abweichend"-Schalter. = I-17 |
| **H-D2** | **Heizlast tippt das Aufmaß neu.** `Energie/HeizlastController.php:8,:147-162` baut ein **transientes** Ein-Zonen-Projekt, importiert **kein** `DealMeasurement`; Raum-/Flächenmaße (`grundflaeche_m2/hoehe_m/bauteile.flaeche_m2`) frisch getippt. | Gebäude-/Raummaße, die schon im Feinaufmaß stehen, komplett neu erfassen — und weil transient, nicht mal zurückgespeichert | **mittel** (F16, Teilmenge WP/Heizung — SCHÄTZUNG) | **~10–20 min** (ganze Raumgeometrie) | M / mittel | **(a)** Prefill aus `deal_measurements` (gleiche physische Größe, gleiches Objekt = sicher). = I-7. **Größter Einzel-Zeitfresser der Durchreichung.** |
| H-D3 | Auftrag trägt **keine** Angebots-Positionen, nur Aggregat-Summen (Kanban-Pfad `LeadOverviewController.php:5702-5742`: `total_net/total_gross` aus Offer-Detail; Manual-Pfad `DealController.php:3686` kopiert **nichts** Monetäres). | — (Positionen leben bewusst in Angebot→Rechnung-Canvas) | mittel (F8, 14) | gering | — | **kein Hebel / by-design** — der Auftrag ist ein Kopf-Satz; die Positionen fließen über den Rechnungs-Canvas (s.o.). Nur dokumentiert, damit nicht fälschlich „gebaut" wird. |

**Fazit 1.1:** Nur **zwei** echte Durchreichungs-Hebel — H-D1 (billig, breit) und **H-D2 (teuer je Vorgang, der lohnendste Medienbruch der Erfassung)**. Das Zentrum der Geld-Kette braucht **nichts** — es ist bereits durchgereicht.

## 1.2 AUSLÖSUNG statt Erinnerung

**Vorbefund (aus Intelligenz-Audit bestätigt):** Es gibt **keinen** fachlichen Model-Observer im `app/` — jede Folge muss explizit im Controller stehen. Der **einzige** verlässliche fachliche Automatismus ist `FollowUpCreator` (5 Aufrufer) + Storno-`destroy`. Das ist die strukturelle Ursache, dass fast alle Vorwärts-Kausalitäten Handarbeit sind. **Der Phasenwechsel (Stage-Move) ist der saubere Ereignis-Punkt** — er wird heute für **keine** fachliche Folge genutzt.

| ID | Ereignis / Beleg | Heutige Handarbeit | Frequenz | Ersparnis + Fehlervermeidung | Risiko/Aufwand | Urteil |
|---|---|---|---|---|---|---|
| **H-A1** | **Lead→Angebot löst nichts aus.** `LeadOverviewController.php:5140-5142` schreibt nur status/stage/history, 0 Task/Reminder-Create. | Nutzer muss selbst dran denken, „Angebot erstellen" als Aufgabe anzulegen | **hoch** (F5/F12, jeder Lead) | verhindert **vergessene/liegengebliebene Angebote** (direkter Umsatz-Schutz) | S / niedrig | **(a)** Beim Eintritt in „Angebot" per `FollowUpCreator::sync` eine Aufgabe erzeugen (Baustein da). = I-1-nah |
| **H-A2** | **Follow-up greift nicht am Feld-/Tagesbericht.** `FollowUpCreator` feuert aus Notiz/Termin/Kanban, **nicht** aus Planner/Tagesbericht; die Slots `lead_product_list` + `appointment_report` sind **deklariert, aber tot** (`FollowUpCreator.php:24-31`; kein `sync()`-Aufrufer). | Monteur-Bericht erzeugt keinen automatischen Nachfass; Büro muss manuell | mittel (F18, je Montage-Tag — SCHÄTZUNG) | verhindert **verlorene Nachfässe** nach Feld-Rückmeldung | S / niedrig | **(a)** die 2 toten Slots durch `sync()` verdrahten. = I-9 |
| **H-A3** | **Storno-Asymmetrie:** `DealController::destroy` storniert Rechnungen mit (`:3756-3757`), aber **`junk($id)` (`:3718-3729`) storniert Rechnungen NICHT** — nur Lead-Stufe zurück. | Nach „junk" bleiben offene Rechnungen aktiv trotz storniertem Auftrag → stille Inkonsistenz | niedrig (F20, selten) | verhindert **widersprüchliche Auftrag(storniert)/Rechnung(aktiv)** | S / niedrig | **(c)** `cancelInvoicesForDeal` auch im junk-Pfad. = I-3 |
| **H-A4** | **Zahlung → Auftragsstatus fehlt.** SQL: Deals 17/18/24/28 stehen `active`, obwohl `invoices.paid_amount = total_amount`. Keine Rück-Kausalität. | Auftrag „weiß" nicht, dass er finanziell abgeschlossen ist; Mensch prüft/setzt | mittel (F8/F19, jeder Auftragsabschluss) | verhindert **„active"-Karteileichen**, ehrliches Cockpit | S / niedrig | **(a)** Ableitung/Flag „voll fakturiert & bezahlt" aus `invoices`. = I-8 (hängt Weiche 1) |
| **H-A5** | **Angebot→Auftrag stößt keine Folgeprozesse an.** `DealController.php:3702-3709` macht nur `lead_product_lists`-Update; keine Materialliste/Kalkulation/Kanban-Aufgaben. Yamas Soll: „nach Auftragsbestätigung → Materialliste" (`workflow-sollkonzept.md:273,315,335`). | Nach Auftrag alle Kickoff-Schritte (Materialliste, Kalkulation, Aufgaben) von Hand starten | mittel (F8, 14) | verhindert **Kickoff-Lücken**; spürt den größten Prozess-Medienbruch | M / mittel | **(a)+(b)** Kickoff-Aufgaben als **Vorschlag** (Materialliste-Anstoß (a), Fach-Feinheiten (b)). = I-11 |
| **H-A6** | **Abnahme→Rechnung ohne Vorschlag.** Kein Pfad aus Abnahme/Abschluss; Rechnung nur manuell (`InvoiceController.php:218`, `InvoiceCanvasController.php:56`). | Nach Abnahme muss Mensch dran denken, die Rechnung anzustoßen | mittel (F9, je fertiger Auftrag) | verhindert **vergessene Schlussrechnung** (direkter Cashflow) | M / mittel | **(b)** Rechnungs-**Entwurf** vorschlagen (nicht festschreiben). TABU-Naht Invoice-Zone. = I-10 |

**Fazit 1.2:** Die Auslösungs-Klasse ist **der ergiebigste Boden** — sechs Hebel, davon vier auf **Aufwand S** (H-A1/A2/A3/A4), die alle den vorhandenen `FollowUpCreator` bzw. eine einfache Ableitung nutzen. Der **Stage-Move ist der ideale, ungenutzte Auslöse-Punkt**.

## 1.3 VORSCHLAG statt Leereingabe

| ID | Ort / Beleg | Heutige Handarbeit | Frequenz | Ersparnis + Fehlervermeidung | Risiko/Aufwand | Urteil |
|---|---|---|---|---|---|---|
| **H-V1** | **Zuständiger immer manuell / Ersteller-Default.** `FollowUpCreator.php:51-54` (Fallback `[creatorEmployeeId]`), `PersonalTaskController.php:833-837`, `KanbanLeadTaskController.php:441`. Dabei ist **`lead_product_lists.department_id` zu 52/52 gefüllt** — der Zuständigkeits-Anker liegt bereit; Yamas Soll-Kette „Produkt→Abteilung→Innen/Außendienst→Kalender" (`workflow-sollkonzept.md:115-135`). | Bei jeder Aufgabe/jedem Termin den Zuständigen von Hand wählen | **hoch** (F2/F10, sehr häufig) | spart Zuweisungs-Klick, **verhindert Fehl-/Nicht-Zuweisung** | M / mittel | **(a)+(b)** Default-Zuweisung per Gewerk/Abteilungs-Owner **vorschlagen**, überschreibbar. = I-12 |
| **H-V2** | **Smart-Formular-Router ist tot.** `SmartroutingService.php:27` (Kontext-Match Gewerk/Objekt/Phase, Spezifitäts-Tiebreak, Fallback) hat **0 Aufrufer**; live naiver Produktfilter `LeadProductChecklistValueController.php:41`. **Doppelte Lücke:** `product_formula_routing_rules` = **0 Zeilen** — auch die Regel-Tabelle ist leer. | Nutzer sieht „alles je Produkt" statt kontextgenau; Formularwahl manuell | mittel (F13 — SCHÄTZUNG) | zielgenaue Formulare, weniger Rausch-Felder | S (Wiring) / niedrig — **aber Regeln müssen erst gepflegt werden** | **(a)** Router verdrahten **+** Regel-Tabelle befüllen. = I-1 (Regel-Leere ist neuer Zusatz-Befund) |
| **H-V3** | **Standardpositionen je Gewerk — Vorlagen-System ist gebaut.** `OfferTemplatePickerController.php:370-391` kopiert `sections` (Standard-Positionen) + `cover_text`; `OfferTemplateController::wizardSearch` (`:703-756`) liefert `match_score`/`match_reasons` (`Gewerk passt`), Vorlagen `department_id`-skaliert. | Nutzer sucht die Vorlage manuell; kein Auto-Vorschlag der Gewerk-Vorlage | mittel (F7, 29) | schneller Angebots-Start, konsistente Positionen | S–M / niedrig | **(b)** Vorlage **automatisch nach Gewerk vorschlagen** (ohne Such-Eingabe). Teil-gebaut → nur „letzte Meile" |
| H-V4 | **Textbaustein-Bibliothek für Notizen/Berichte fehlt.** Kanned-Text existiert **nur** angebots-intern (`cover_text_html`, AGB `OfferFolderController.php:931`); kein allgemeiner Snippet-Store (grep leer). | Wiederkehrende Notiz-/Bericht-Texte frei tippen | hoch (F3, 76 Notizen) | Tippzeit, Einheitlichkeit | M / mittel | **(b)** allgemeine Textbaustein-Bibliothek (Neubau, kein vorhandener Baustein) |

**Fazit 1.3:** H-V1 (Zuständiger nach Abteilung) sticht heraus — **hohe Frequenz + Anker (`department_id`) liegt gefüllt bereit**. H-V2/H-V3 sind „gebaut, nur zünden", H-V4 ist echter Neubau.

## 1.4 ABLEITUNG statt Handrechnung

**Vorbefund (positiv):** Die **Angebots-/Rechnungs-Summen sind bereits server-autoritativ abgeleitet** (`OfferController.php:2022-2028` netto/MwSt/brutto; `InvoiceController.php:1136` Zeilensumme; Front-End-Werte werden **neu gerechnet**, nicht vertraut, `:1674-1678`). **Nummernvergabe** (Rechnung/Angebot/Auftrag) ist voll automatisiert, race-safe (`InvoiceNumberService.php:35-84`, `Offer.php:33-95`, `Deal.php:42-76`). Die **Auslegungs-Kerne** (Heizkörper) sind **bereits an den Auftrag verdrahtet** (s. TEIL 2). → Ableitungs-Hebel liegen bei **Fälligkeit, Kapazität, Materialmenge** — und im **still-falschen** PLZ-Default.

| ID | Ort / Beleg | Heutige Handrechnung | Frequenz | Ersparnis + Fehlervermeidung | Risiko/Aufwand | Urteil |
|---|---|---|---|---|---|---|
| **H-B1** | **Rechnungs-Fälligkeit nicht abgeleitet.** `InvoiceController.php:1097` `due_date` frei/`nullable`; **keine** `payment_term`/`zahlungsziel`-Rechnung. Distributoren tragen bereits `payment_terms` (`OfferWizardController.php:846`) — Quelle da, ungenutzt. (Seed-Beleg: alle `due_date`=2026-07-13 unabhängig vom `issue_date` → nicht regel-abgeleitet.) | Fälligkeitsdatum je Rechnung von Hand setzen | mittel (F9, jede Rechnung) | spart Datum, **verhindert falsche/fehlende Fälligkeit → sauberes Mahnwesen** | S / niedrig | **(a)** `due_date = issue_date + Zahlungsziel` (Standard-Term pflegbar) |
| **H-B2** | **Kapazität/Verfügbarkeit ohne Vorschlag.** grep `capacity/kapazit/verfug/overlap/kollision` über `Appointment`+`Planner`: nur Zeitfenster-Filter zum **Laden**, **kein** Doppelbuchungs-/Auslastungs-Check (`PlannerPlanController.php:6472-6481`). Mitarbeiter manuell/Ersteller (`MainAppointmentController.php:469`). | Freien Slot/Kollisionen im Kopf/per Blick prüfen | **hoch** (F2, 80 Termine — häufigstes Ereignis) | **verhindert Doppelbuchung**; spart Slot-Suche | M / mittel | **(a)** Kollisions-/Doppelbuchungs-Warnung (sicher ableitbar); **(b)** Slot-/Kapazitäts-Vorschlag |
| H-B3 | **Materialmenge nicht aus Aufmaß abgeleitet.** Mengen von Hand in `materials_snapshot` (`DealMeasurementMaterialController.php:35-36`); Maschine rechnet **nur** Delta/Vergleich (`:259-283`, `DealMaterialListController.php:1204+`). | Materialmengen aus Maßen selbst hochrechnen | mittel (F15, je Auftrag) | spart Kalkulation, weniger Mengenfehler | L / mittel | **(b)** Mengen-**Vorschlag** aus Geometrie (fachlich je Gewerk, Meister bestätigt) |
| **H-B4** | **PLZ-Miss → still Default-Klima.** `Energie/HeizlastController.php:127` `?? -8.5`, `HeizlastRechner.php:30` still `-12.0`. Operanden-Gate-Bruch: rechnet mit **erfundenem** Norm-Klima weiter, unmarkiert. | (Automatik rechnet still falsch weiter) | mittel (F16) | **verhindert stille Falschrechnung** in der Auslegung | S / niedrig | **(c)** „PLZ unbekannt → Default angenommen (nicht verbindlich)" flaggen (wie `datenlage='geschaetzt'`). = I-4 |
| H-B5 | **Heizlast-Kerne nicht ans Angebot angedockt.** `app/Services/Heizlast/*` (BivalenzService, WaermepumpenMatchService, …) speisen **nur** die Energie-Views, **schreiben nicht** in `deal_measurement_items`/Angebot (grep in Offer/Deal-Pricing = 0). Brücke wäre der **unverdrahtete** `AnforderungsprofilHeizlastAdapter`. *(Kontrast: die **Heizkörper**-Kerne SIND verdrahtet — s. TEIL 2.)* | Auslegungs-Ergebnis manuell ins Angebot übertragen | mittel (F16) | keine Doppelerfassung Auslegung→Angebot | M–L / mittel | **(a)+(b)** über Anforderungsprofil-Brücke ins Angebot (Muster: Heizkörper-Pfad) |

**Fazit 1.4:** **H-B1 (Fälligkeit)** ist ein echter Quick-Win (S, sicher, Quelle da). **H-B2 (Kapazität)** trifft das **häufigste Ereignis** (Termin) mit hoher Fehlervermeidung. H-B4 ist ein **(c)-Fix** (still-falsch). H-B3/H-B5 sind fachlastig → (b).

---

# TEIL 2 — Vorhandene Infrastruktur als Multiplikator

Diese Bausteine machen Automatik **billig**, weil das Fundament steht und nur **verdrahtet** werden muss (beste Wirkung/Aufwand). Verifiziert per Signatur + Aufrufer-Zählung (grep über `app/`, `resources/`, `routes/`).

| Baustein | Zustand | Was er **ermöglicht** (billig, weil da) | Speist welchen Hebel |
|---|---|---|---|
| **FollowUpCreator** (`app/Services/FollowUp/FollowUpCreator.php:49`) | **1 Erzeugungsstelle**, Upsert-Key `(type='follow_up', source_type, source_id)` → dedup-sicher; 4 Slots verdrahtet, **2 tot** (`lead_product_list`, `appointment_report`). | Neue Auslöse-Quellen mit **einem `sync()`-Call**, ohne neue Plumbing — dedup-sicher. | **H-A1, H-A2** (und H-V1 als Zuweisungs-Default darin) |
| **Phasenwechsel-Punkt** (Stage-Move `LeadOverviewController.php:5140`) | sauberer Ereignis-Punkt, heute **0 fachliche Folge** | Der **ideale Auslöser-Hook** — jede Phasen-Kausalität (Lead→Angebot-Task, Auftrag-Kickoff, „bezahlt"-Flag) hängt hier an. | **H-A1, H-A4, H-A5** |
| **SmartroutingService** (`app/Services/Form/SmartroutingService.php:27`) | **0 Aufrufer**; Kontext-Router (Gewerk/Objekt/Phase, Spezifität+Priorität+Fallback) fertig — **aber** `product_formula_routing_rules` = **0 Zeilen** | Datengetriebenes Formular-Routing „PV→PV-Welt" (Yamas Soll §6). Ein Controller-Call — plus Regel-Pflege. | **H-V2** |
| **PlausibilityService** (`app/Services/Form/PlausibilityService.php:46`) | **0 Aufrufer**; reiche Warn-Heuristiken (negative Fläche/Menge, Einheiten-Mix, Raumhöhe-Band), **nie blockierend** | Automatische Sanity-Warnungen auf **jeder** Checklisten-/Aufmaß-Rechnung — **ein Call, kein Schema-Change**. | (Fehlervermeidungs-Multiplikator, = I-2) |
| **Anforderungsprofil + Registry** (`app/Services/Anforderungsprofil/*`) | **0 Wiring**; `SchluesselRegistry` (Whitelist ~18 Schlüssel), `saving`-Hook wirft bei Unsinn, versioniert, **`datenlage` gemessen/berechnet/geschätzt**-ehrlich | Audit-sicheres „angenommen vs. gemessen"-Register je Lead/Gewerk **als Brücke** Auslegung→Angebot (H-B5). | **H-B5, H-B4** |
| **Auslegungs-Kerne Heizkörper** (`app/Services/Heizkoerper/*`) | **BEREITS verdrahtet**: `Heizkoerper/HeizkoerperController.php:150-204` schreibt `DealMeasurementItem` (deal_id/offer_id/qty) aus `CompatibilityService`+`RadiatorPerformanceService` | **Referenz-Muster** für „Rechen-Ergebnis → Angebots-Position". Zeigt, wie H-B5 (Heizlast) andocken sollte. | Muster für **H-B5** |
| **Form-Engine** (`FormulaEvaluationService.php:100`) | verdrahtet (1 Aufrufer, `ProductFormulaController::evaluate`); Operanden-Gate vorbildlich (`unvollstaendig`/`ungeprueft`, `verbindlich`-Flag) | Ergebnis heute nur Anzeige — Routing des `verbindlich`-Verdikts in Angebots-Positionen würde Checklisten-Rechnungen Positionen treiben. | (Ausbau-Reserve) |
| **Vorlagen-System Angebot** (`OfferTemplatePickerController.php:370`, `wizardSearch:703`) | gebaut, `match_score`/`Gewerk passt`, `department_id`-skaliert | Auto-Vorschlag der Gewerk-Vorlage → Standard-Positionen vorbefüllt. | **H-V3** |
| **`department_id` auf Gewerk** (`lead_product_lists`, **52/52 gefüllt**) | zuverlässige Realdaten | Zuständigkeits-Ableitung „Gewerk→Abteilung→Owner" ist **sicher fahrbar**. | **H-V1** |

**Kernbotschaft TEIL 2:** Der billigste Automatisierungsgewinn ist **kein Neubau**, sondern **Verdrahten**. Fünf fertige Intelligenz-Schichten (FollowUpCreator-Slots, SmartroutingService, PlausibilityService, Anforderungsprofil, Vorlagen-Auto-Vorschlag) liegen ungenutzt — und der **Heizkörper-Pfad beweist**, dass das Muster „Rechen-Ergebnis→Position" im Repo schon funktioniert.

---

# TEIL 3 — HEBEL-RANGLISTE (nach Automatisierungs-Wert)

Sortiert nach **Wirkung ÷ Aufwand**. Zeitersparnis-Spalte: `X Min × Y Vorgänge = Z` — **Y ist SCHÄTZUNG** (Seed erlaubt keine Raten), Größenordnung mit Prod-Skala (~3000 Kunden) plausibilisiert. Aufwand S/M/L.

| ID | Hebel | Klasse | Heutige Handarbeit | Soll-Automatik | Frequenz | Zeit/Fehler (SCHÄTZUNG) | Aufw | Risiko | Nutzt Baustein | a/b/c | Abhängigkeit |
|---|---|---|---|---|---|---|---|---|---|---|---|
| H-B1 | Rechnungs-Fälligkeit ableiten | Ableitung | `due_date` manuell | `issue_date`+Zahlungsziel | mittel (F9) | 20 s × jede Rechnung; **Mahnwesen-Fehler weg** | **S** | niedrig | `payment_terms` (da) | **a** | — |
| H-A1 | Lead→Angebot-Aufgabe | Auslösung | Aufgabe von Hand | `FollowUpCreator` bei Stage-Move | hoch (F5/F12) | verhindert **liegengebliebene Angebote** | **S** | niedrig | FollowUpCreator + Stage-Move | **a** | — |
| H-A2 | Follow-up am Feld-/Tagesbericht | Auslösung | Nachfass manuell | tote Slots `sync()` | mittel (F18) | verhindert verlorene Nachfässe | **S** | niedrig | FollowUpCreator | **a** | — |
| H-V2 | Smart-Formular-Router zünden | Vorschlag/Routing | „alles je Produkt" | `SmartroutingService::route` | mittel (F13) | zielgenaue Formulare | **S** Wiring | niedrig | SmartroutingService | **a** | **Regel-Tabelle befüllen (0 Zeilen!)** |
| PLS | PlausibilityService verdrahten | Plausib. | keine Prüfung | Warn-Modus im Speicherpfad | mittel (F13/F14) | fängt Unsinn ab | **S** | niedrig | PlausibilityService | **a** | — (= I-2) |
| H-A4 | „voll fakturiert & bezahlt"-Flag | Auslösung/Ableitung | Mensch prüft | Ableitung aus `invoices` | mittel (F8/F19) | ehrliches Cockpit | **S** | niedrig | — | **a** | Weiche 1 |
| H-D1 | Objekt-Adresse aus Kunde | Durchreichung | Adresse doppelt tippen | Prefill + „abweichend" | hoch (F4) | 30–60 s × jedes Objekt | **S** | niedrig | — | **a** | — |
| H-A3 | junk() = destroy() Storno | Auslösung/Konsist. | Rechnung bleibt aktiv | `cancelInvoicesForDeal` in junk | niedrig (F20) | keine Widersprüche | **S** | niedrig | — | **c** | — |
| H-B4 | PLZ-Default flaggen | Ableitung/Plausib. | still falsch | „angenommen"-Kennzeichnung | mittel (F16) | **stille Falschrechnung weg** | **S** | niedrig | Anforderungsprofil-Muster | **c** | — |
| H-V1 | Zuständiger nach Abteilung | Vorschlag/Routing | jede Zuweisung manuell | Owner-Vorschlag per `department_id` | **hoch** (F2/F10) | Zuweisungs-Klick × sehr oft; Fehl-Zuweisung weg | **M** | mittel | `department_id` (52/52) | **a+b** | Owner-Definition je Gewerk |
| H-B2 | Kapazität/Doppelbuchung | Ableitung | Slot im Kopf prüfen | Kollisions-Warnung + Slot-Vorschlag | **hoch** (F2, 80) | **Doppelbuchung weg**; Slot-Suche | **M** | mittel | Planner-Zeitfenster | **a+b** | — |
| H-V3 | Gewerk-Vorlage auto-vorschlagen | Vorschlag | Vorlage manuell suchen | Auto-Match per Gewerk | mittel (F7) | schneller Angebots-Start | **S–M** | niedrig | Vorlagen-System | **b** | — |
| H-A5 | Angebot→Auftrag-Kickoff | Auslösung | Kickoff von Hand | Materialliste/Kalkul.-Aufgaben (Vorschlag) | mittel (F8) | Kickoff-Lücken weg | **M** | mittel | Stage-Move + FollowUpCreator | **a+b** | Materiallisten-Modell |
| H-A6 | Abnahme→Rechnungs-Entwurf | Auslösung | Rechnung manuell anstoßen | Entwurf vorschlagen | mittel (F9) | **vergessene Schlussrechnung weg** | **M** | mittel | Canvas-Bootstrap (da) | **b** | Invoice-Zone (TABU-Naht), Weiche 3 |
| H-D2 | Heizlast-Prefill aus Aufmaß | Durchreichung | Maße neu tippen | Prefill `deal_measurements` | mittel (F16) | **10–20 min × je Rechnung** | **M** | mittel | Aufmaß-Modell | **a** | Aufmaß-Datenmodell |
| H-V4 | Textbaustein-Bibliothek | Vorschlag | Texte frei tippen | Snippet-Store | hoch (F3) | Tippzeit, Einheitlichkeit | **M** | niedrig | — (Neubau) | **b** | — |
| H-B5 | Heizlast-Kern → Angebot | Ableitung | Auslegung manuell übertragen | Anforderungsprofil-Brücke | mittel (F16) | Doppelerfassung weg | **M–L** | mittel | Anforderungsprofil + Heizkörper-Muster | **a+b** | Anforderungsprofil-Wiring |
| H-B3 | Materialmenge aus Aufmaß | Ableitung | Mengen hochrechnen | Mengen-Vorschlag | mittel (F15) | Kalkulationszeit | **L** | mittel | — | **b** | Gewerk-Fachregeln |

*(Nicht neu bewertet, aber automatisierungs-relevant und im Intelligenz-Audit belegt: **I-5** Negativ-Mengen-Validierung, **I-6** Vorlauf/Rücklauf-Band, **I-13** Phasensprung-Markierung, **I-14** `deals` 5-Status-Feld-Konsolidierung, **I-15** `lead_stage_id`-Kanon, **I-16** Reminder-Härtung/toter Job. Diese sind **Fehlervermeidung/Konsistenz**, kein „Arbeit abnehmen" — deshalb hier nicht doppelt gerankt, sondern per Verweis mitgeführt.)*

## TOP-5 QUICK-WINS (hoher Wert / S-Aufwand / kleines Risiko — zuerst bauen)

1. **H-B1 — Rechnungs-Fälligkeit ableiten** (`due_date = issue_date + Zahlungsziel`). Sicher, Quelle (`payment_terms`) da, direkter Mahnwesen-Nutzen.
2. **H-A1 — Lead→Angebot-Aufgabe** über `FollowUpCreator` am Stage-Move. Schützt direkt Umsatz (kein Angebot vergessen), Baustein vorhanden.
3. **H-A2 — die 2 toten FollowUpCreator-Slots verdrahten** (Feld-/Tagesbericht → Nachfass). Ein `sync()`-Call, dedup-sicher.
4. **PlausibilityService verdrahten** (I-2) + **H-B4 PLZ-Default flaggen**. Beide „Intelligenz liegt fertig da" — Warn-Modus einhängen, stille Falschrechnung markieren.
5. **H-D1 — Objekt-Adresse aus Kunde vorbefüllen** + **H-A4 „bezahlt"-Flag** + **H-A3 junk-Storno**. Drei kleine, sichere, breit wirkende Fixe.

*(H-V2 Smart-Router wäre ebenfalls S, ist aber **kein reiner Quick-Win**: die Regel-Tabelle `product_formula_routing_rules` ist leer — Wiring **und** Regel-Pflege nötig. Als „Quick-Win mit Sternchen" führen.)*

## TOP-3 GROSSHEBEL (höchster Gesamtwert)

1. **H-V1 — Zuständigkeits-Vorschlag nach Abteilung (+ die Auslöse-Kette H-A1/H-A2).** Trifft die **höchstfrequenten** Tätigkeiten (Termin, Aufgabe, Follow-up), der Anker (`department_id` 52/52) und die Erzeugungsstelle (FollowUpCreator) liegen bereit. Macht den gesamten Aufgaben-Fluss von „wer legt an, kriegt's" zu „richtiger Owner vorgeschlagen".
2. **H-B2 — Kapazitäts-/Doppelbuchungs-Check am Termin.** Termin ist das **häufigste Ereignis** (80) und Doppelbuchung ist ein teurer, sicher vermeidbarer Fehler.
3. **H-A5 + H-A6 — die zwei großen Prozess-Medienbrüche schließen** (Angebot→Auftrag-Kickoff und Abnahme→Rechnungs-Entwurf). Höchster Einzel-Vorgang-Wert (Kickoff-Vollständigkeit, keine vergessene Schlussrechnung), beide als **Vorschlag** (Operanden-Gate). *(Alternativ als Großhebel: **H-D2 Heizlast-Prefill** — der größte Einzel-Zeitfresser (10–20 min/Vorgang), aber schmalere Frequenz.)*

## Gesamt-Einschätzung — wie viel Handarbeit realistisch abnehmbar

- **Alltags-Verwaltung (Aufgaben zuweisen, Follow-ups, Fälligkeiten, Adress-/Formular-Vorbefüllung):** **substanziell abnehmbar** — hier liegt die Masse der Quick-Wins, und die Bausteine sind gebaut. Größenordnung (SCHÄTZUNG, Yama kalibriert): der **Klick-/Erinnerungs-Overhead** je Vorgang (Zuweisung, Nachfass-anlegen, Fälligkeit, Adresse) ist der breiteste Gewinn, weil er **jeden** Vorgang trifft.
- **Kette/Prozess-Anstöße (Lead→Angebot, Auftrag-Kickoff, Abnahme→Rechnung):** **als Vorschlag abnehmbar**, nicht als Vollautomat — der Anstoß wird automatisch, die fachliche Ausgestaltung bleibt Mensch.
- **Rechen-Assistenz (Heizlast/Auslegung):** die junge Zone ist bereits Grad ~4; der Hebel ist **Andocken ans Angebot** (H-B5) + **Prefill** (H-D2), nicht mehr Rechenlogik.
- **Realistische Aussage ohne Prod-Zahlen:** Der **Automatisierungsgrad des Alt-Kerns lässt sich von ~2 auf ~3–4 heben, überwiegend durch Verdrahten des bereits Gebauten** — der teuerste Teil (Rechen-Intelligenz, FK-Kette, Erzeugungsstelle) existiert schon. Eine belastbare „X Stunden/Monat"-Zahl ist **erst mit Yamas Frequenzangaben** seriös nennbar (s.u.).

### Wo bewusst die GRENZE (darf NICHT voll automatisiert werden)

- **Fach-/Rechtsentscheidungen — nur Vorschlag + Bestätigung:** Rechnungs-**Festschreibung** (Abnahme→Rechnung bleibt Entwurf, H-A6), Materialmengen aus Geometrie (H-B3), Heizlast→Angebot (H-B5), Zuständigkeits-Zuweisung (final überschreibbar, H-V1) — alles **(b)**.
- **Operanden-Gate strikt:** kein Weiterrechnen mit erfundenem Wert (H-B4 PLZ-Default → markieren statt still annehmen). Referenz bleibt `FormulaEvaluationService`.
- **Bewusst manuell (Yama-Entscheidungen, nicht anrühren):** Montageplan-Erzeugung (kein Auto-Trigger, Kuratier-Prinzip, `architektur-entscheidungen.md` Weiche 6); Büro→Planner-Kuratierschritt; Feld-Rückfluss **mit PL-Prüfschritt** (Melde→Prüf→Bestätigt), nicht automatisches Durchreichen.
- **Storno bezahlter Rechnungen:** bleibt „markieren + Mensch prüfen" (`storniert_bezahlt_pruefen`), buchhalterische Folgeregel offen (Weiche 4).
- **Phasen-Reihenfolge:** nicht hart blocken (Weiche 2 = „flexibel mit Warnung") — Übersprung **sichtbar markieren**, nicht verhindern (I-13).

---

# STRENGE

## Offene Frequenz-Fragen an Yama (Priorisierung hängt daran)
1. Reale **Termine/Tag** (F2) und **Aufgaben je Lead** (F10)? — bestimmt Rang von H-V1/H-B2 (heute als Großhebel nur aus Seed-Relativhäufigkeit geschlossen).
2. **Rechnungen/Monat** und übliches **Zahlungsziel** (F9)? — H-B1-Wert + Term-Default.
3. Anteil Gewerke mit **Heizlast-Rechnung** (F16) und **Montage-Tage/Woche** (F18)? — H-D2/H-B5/H-A2.
4. **Storno-Häufigkeit** (F20)? — bestätigt niedrigen Rang von H-A3.
5. Funktioniert die **„Produkt→Abteilung→Kalender"-Auto-Ableitung** heute schon (Aktivposten) oder hakt sie (`workflow-sollkonzept.md:135`)? — direkt H-V1.

## Gelesen / gemessen (firsthand)
`docs/audit/intelligenz-audit.md` (vollständig), `docs/architektur-entscheidungen.md` (Weichen 1–6 + Nachträge), `docs/workflow-sollkonzept.md` (§5 Intake, §7 Ableitungen, §8 Gewerk-Ableitungen), `app/Services/FollowUp/FollowUpCreator.php` (Ziel-Tabelle/Slots); **SQL firsthand:** Row-Counts + Funnel + Verteilungen (`new_leads/offers/deals/invoices/main_appointments/customer_notes/customer_histories`), `deals.status/project_status`, `invoices.status/issue_date/due_date/Verknüpfungsspalten`, `lead_product_lists.department_id` (52/52), `product_formula_routing_rules`=0, `product_formulas`=0. **Via Explore-Agenten (belegt mit file:line):** Feld-Ebene Durchreichung (Lead→Angebot→Auftrag→Rechnung, `OfferWizardController`/`OfferController`/`DealController`/`LeadOverviewController`/`InvoiceCanvasController`/`InvoiceController`/`NewLeadsController`); Infrastruktur-Multiplikatoren (`SmartroutingService`, `PlausibilityService`, `Anforderungsprofil/*`, `Heizkoerper/*`, `Heizlast/*`, `FormulaEvaluationService`, Vorlagen-System); Ableitungs-/Vorschlags-Flächen (Offer-Summen, `due_date`, Material/Kapazität, Nummern, Zuständiger).

## NICHT-VERIFIZIERT / Selbstkritik
- **Frequenz ist der schwächste Punkt:** Seed = **ein Tag**, keine Raten ableitbar. Alle „X/Tag/Monat" sind **SCHÄTZUNG**; die Top-3-Großhebel (H-V1/H-B2 hohe Frequenz) stehen auf **Seed-Relativhäufigkeit** (Termine 80 > Notizen 76 > Leads 52), nicht auf Prod-Volumen. **Ändert Yamas Frequenzangabe die Reihenfolge, gilt Yama.**
- **„0 Aufrufer" = statischer grep** (SmartroutingService, PlausibilityService, Anforderungsprofil-Wiring): stark, aber kein Beweis gegen Reflection/String-Dispatch. **NICHT-VERIFIZIERT.**
- **Clientseitige Verkettung nicht geprüft:** Ob Blade/JS Heizlast-Prefill (H-D2), Angebots-Anstoß (H-A1) oder Zuständigen-Vorschlag (H-V1) **clientseitig** schon leistet, ist **serverseitig widerlegt, clientseitig NICHT VERIFIZIERT**. Vor Bau je Hebel eine Blade/JS-Prüfung.
- **`due_date`-Seed-Beleg** (alle 2026-07-13) ist ein **Seed-Artefakt** — es *stützt* „nicht regel-abgeleitet", der harte Beleg ist aber der Code (`InvoiceController.php:1097` + fehlende `payment_term`-Rechnung), nicht die Daten.
- **Zeitersparnis-Minuten** je Vorgang sind **Erfahrungs-Schätzung**, nicht gemessen.
- **Heizkörper-Kern „verdrahtet"** stützt sich auf den Explore-Befund (`HeizkoerperController.php:150-204`) — Endpoint nicht selbst ausgeführt (**NICHT-VERIFIZIERT** zur Laufzeit, Code-belegt).
- **TABU respektiert:** Invoice-Zone/Nuriva/Video/Legacy nur an Nähten (H-A6 = fehlende Brücke, nicht Invoice-Qualität).
