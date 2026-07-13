# System-Bereichsstruktur — ticket (abgeleitet aus den Inventuren)

**Stand:** 2026-07-11 · **read-only** · **kein Bau/Import/Refactor/Automatisierung — nur Strukturierung + Begründung.**
**Zweck:** Aus den vorhandenen Fakten (nicht willkürlich) eine professionelle **Bereichsstruktur** ableiten, damit wir danach **Bereich für Bereich** sauber abarbeiten. Vorstufe zum Gesamtkonzept — **das Gesamtkonzept wird hier noch NICHT geschrieben.**
**Leitprinzip (Yama, 2026-07-11):** Zahl der Bereiche ist flexibel (darf steigen/sinken) — **entscheidend ist, dass die Themen sauber zusammengeführt sind** (eine Datenwahrheit / ein Workflow-Abschnitt je Bereich, keine künstlichen Nähte, keine Sammeltöpfe).

**Quellen (firsthand):** `docs/system-inventur.md` (führende Wahrheiten §10, Doppelwahrheiten §11, unverdrahtete Bausteine §12, Prozessbrüche §13) · `-nachsuche.md` + `-nachsuche-3.md` · `docs/playground-uebernahme-inventur.md` · `docs/wberechnung-uebernahme-inventur.md` · `docs/system-kapitelplan.md` (Kapitel A–N) · Live-Code 2026-07-11.
**Geltung:** `docs/rueckfall-archiv-regeln.md` verbindlich (kein Löschen/Überschreiben ohne Rückfallpfad).

---

## 0. Ableitungs-Logik + Änderung ggü. der 8-Fassung

**Drei Zusammenführungs-Kriterien:**
- **(K1) Gemeinsame Datenwahrheit** — was auf demselben führenden Model sitzt, gehört in **einen** Bereich (alles auf `invoices` → Finanzen; alles auf `offers`+Energie-Services → Angebot/Auslegung; alles auf `new_leads`/`lead_stages` → Front Office).
- **(K2) Gemeinsamer Workflow-Abschnitt** — was im Betrieb in einem Zug bearbeitet wird (signierter Auftrag → Disposition → Montage → Abnahme = **eine** Realisierungsschleife).
- **(K3) Trennschärfe von Sensibilität/Reife** — was eigene rechtliche/normative Prüftiefe braucht, bleibt getrennt (FiBu; Auslegung; technische Sicherheit).

**Änderung ggü. der ersten 8-Bereichs-Fassung (auf Yamas „sauber zusammenführen"):**
- **Zusammengelegt:** vormals *Bereich 1 (Stammdaten/Kunde/Objekt)* **+** *Bereich 2 (CRM/Vertrieb/Kommunikation)* → **ein Bereich „Kunde, Objekt & Vertrieb (Front Office)"**. Begründung (K1+K2): identische Datenwahrheit (`new_leads`/`lead_alternative_adds`/`lead_product_lists`/`lead_stages`) — die Kundenakte lässt sich nicht getrennt vom Pipeline-/Aufgaben-Prozess säubern; die Trennung war eine künstliche Naht.
- **Bewusst NICHT zusammengelegt** (verschiedene Datenwahrheit/Sensibilität): Produkte/Lager ↔ Personal/Org (beides „Ressourcen", aber `products` ≠ `employees`); Service ↔ Ausführung (nach Übergabe ≠ während Bau); Personal/Org-**Rechte** ↔ technische **Sicherheit** (Rollen-*Definition* = Business → Bereich 7; Rollen-*Durchsetzung/IDOR/Gates* = Querschnitt N).

**Ergebnis: 7 Hauptbereiche + 1 Querschnitts-Prüfbrille (N, nicht sequenziell).**

| # | Bereich | deckt Kapitel (A–N) |
|---|---|---|
| 1 | **Kunde, Objekt & Vertrieb (Front Office)** | B + C (+ Kommunikation) |
| 2 | **Angebot, Auslegung & Kalkulation** | D |
| 3 | **Produkte, Einkauf & Lager (ERP-Material)** | F + G |
| 4 | **Auftrag, Disposition, Ausführung & Abnahme** | E + H + I + J |
| 5 | **Finanzen, Rechnung & Controlling** | K + L |
| 6 | **Service, Wartung & Nachbetreuung** | M |
| 7 | **Organisation, Personal, Standorte & Rechte** | Org-Querschnitt (aus H/N) |
| N | **Prüfbrille: Architektur/Sicherheit/Performance/UX/Tests** | N (begleitet alle) |

---

## Bereich 1 — Kunde, Objekt & Vertrieb (Front Office)

### Zweck
Die **führende Identität** von Kunde/Kontakt/Objekt/Gewerk sauber halten **und** den Lead durch die **Vertriebs-Pipeline** führen (Phasen, Aufgaben, Termine, Arbeitsliste). Ein Bereich, weil beides auf derselben Datenwahrheit sitzt.

### Enthaltene Funktionen
Anfrage-Intake, Lead-/Kundenanlage, Kundenakte, Objekt-/Adressdaten, Gewerk-Zuordnung, Kontakte, Zuständigkeit · Kanban/Stages, Aufgaben & Follow-ups & Wiedervorlagen, Termine/Kalender, Arbeitsliste/Inbox, Kommunikations-Historie.

### Nicht enthalten
- **Angebots-/Auslegungs-Inhalte** (→ Bereich 2).
- **Artikel-/Produkt-Stammdaten** (→ Bereich 3), **Personal-Stammdaten** (→ Bereich 7).
- **Legacy-Kommunikation** Bitrix/NIBE/IMAP — bewusst ignoriert (Projekt-Gedächtnis).

### Ticket-Bestand
`new_leads` (**Kunde**) · `lead_alternative_adds` (**Objekt**, God-Table 193 Sp./1 FK) · `lead_product_lists` (**Gewerk**) · `inquiries` · `contacts` · `lead_stages` (**Phase**, 6) · `lead_stage_sub_stages` · `personal_tasks` (**Follow-up-Träger**) · `main_appointments` · `kanban_lead_tasks`. Controller: `NewLeadsController` (**14.054 LOC**, Gott-Klasse, 267 `DB::table`), `InquiryController` (2.952), `LeadOverviewController` (7.075, neu-Stil), `KanbanLeadTaskController`, `LeadStageController`, `PersonalTaskController` (6.570), `PersonalTaskBoardController`, `MainAppointmentController`, `ArbeitslisteController` (neu), `Contacts/*`, `CustomerResponsibleController`. Service: `FollowUp/FollowUpCreator`.

### Playground-Bestand
CRM/Kunde/Objekt = **Kategorie D (Doppelung)**, nicht importieren; Warnung: parallele `Customer*`/`Lead*`-Model-Familien. **Smartrouting** (in ticket gebaut, 0 Regeln/0 Aufrufer) + **Angebotsampel/Pflichtdaten-Gate** = Kategorie B („verdrahten").

### Wberechnung-Bestand
Keiner (nutzt intern `heizlast_projekte`; in ticket durch **Anforderungsprofil-Framework** ersetzt).

### Datenwahrheiten (führend)
`new_leads` (Kunde) · `lead_alternative_adds` (Objekt) · `lead_product_lists` (Gewerk) · `lead_stages` (Phase) · `personal_tasks` (Follow-up) · `main_appointments` (Termin).

### Mögliche Doppelwahrheiten
- **`customers`-Zombie** neben `new_leads` (§11.3); **`Customer*` vs. `Lead*`** (Nachsuche-3).
- **Stage-Ableitung in 3 Varianten** mit Fold-Unterschied (§11.1); **3 Speichern-Muster/3 Default-Status** für `lead_product_lists` (§11.2).
- **`offer_kanban_stages` ↔ `lead_stages`** (§11.6); **`Appointment` vs. `MainAppointment`** (Nachsuche-3).

### Backend-Sicht
Models `NewLeads`/`LeadAlternativeAdd`/`LeadProductList`(+`booted` Stage-Kanon)/`Inquiry`/`Contact`/`LeadStage`(+`booted`)/`PersonalTask`/`MainAppointment`. Commands `ProcessPersonalTaskScheduler` (+Dublette), `DispatchMainAppointmentReminders`. Job `ScheduleTaskReminder`. **Toter Zweig:** Listener `StoreLeadActivity` (`LeadRecordChanged` nie dispatched). Risiko: `DB::table` umgeht Hooks.

### Frontend-Sicht
„Neue Anfrage / Meine Anfragen / Website-Leads / Kundenakte / Alle Kontakte", „Lead-Kanban" (`kanban.js` 17.204 Z.), „Meine Aufgaben", „Was jetzt?" (Arbeitsliste), „Mein Kalender". Sehr JS-lastig (`customer_profile` 91,5 % JS).

### Workflow-Sicht
Intake (Website/Fusion) → Lead → Kundenakte → Objekt(e) + Gewerk(e) → Zuständigkeit → Phase/Kanban → Aufgaben/Follow-ups (A1: Lead→Angebot-Task am Stage-Move) → Termine → Arbeitsliste. Übergibt an Bereich 2 beim Angebots-Trigger.

### Automatisierung
- **(a) automatisch:** Website-/Fusion-Intake (existiert), Follow-up am Stage-Move (race-safe A1b), Termin-Reminder, Arbeitslisten-Bündelung, Dubletten-**Vorschlag**.
- **(b) Vorschlag+Bestätigung:** Kunden-Merge, Objekt-Klammerung, Zuständigkeit, Smartrouting-Zuweisung, Phasen-Automatik.
- **(c) nicht:** endgültiges Löschen/Zusammenlegen von Kundendaten; Phasensprung über Pflichtdaten-Gate hinweg; Kunden-Mailversand ohne Freigabe.

### Abhängigkeiten
Fundament — hängt von niemandem ab; **Bereich 7** (Zuständigkeit/Rechte) parallel klären.

### Risiken
God-Table + Gott-Klasse `NewLeadsController` (14k) → breite Reichweite; Stage-Zoo; falls Phasen-Doppelquelle ungeklärt, driftet der Vertriebsstatus. Falsch (zu spät) → alle Bereiche referenzieren unsaubere Anker.

### Erster sinnvoller Testpfad
Neuen Lead anlegen → Objekt + Gewerk hinzufügen → über Kanban eine Phase weiterziehen → prüfen: Follow-up-Task entsteht, taucht in „Was jetzt?" auf, `lead_stage_id` konsistent, keine Zombie-`customers`-Doppelung.

### Erste Umsetzungsidee (noch NICHT bauen)
Später: (1) `customers`-Zombie belegt stilllegen (Trail, Drop = eigener Posten); (2) Stage-Ableitung auf **einen** Kanon; (3) `SmartroutingService` verdrahten; (4) toten `LeadRecordChanged`-Zweig dispatchen o. archivieren (Variante B).

---

## Bereich 2 — Angebot, Auslegung & Kalkulation

### Zweck
Aus Kunde/Objekt/Gewerk ein **technisch korrektes, kalkuliertes Angebot** erzeugen — inkl. **normbasierter Energie-Auslegung** (Heizlast, WP, PV, Wirtschaftlichkeit). Fachlich wertvollste, substanzreichste Domäne.

### Enthaltene Funktionen
Angebotsordner/OfferDetails/Vorlagen, Produkt-/Set-Auswahl, Kalkulation/Costing-Sets, **Auslegungen** (Heizlast DIN EN 12831, WP-Kennlinie/Bivalenz/JAZ, PV/WR-Sizing, PVGIS, Sanierungs-Wirtschaftlichkeit, Förderung, Fußboden, Heizkörper), Formular-/Checklisten-Engine, Angebots-/PDF-Ausgabe.

### Nicht enthalten
- **Artikel-Stammdaten/Einkaufspreise** (→ Bereich 3; hier nur **Auswahl aus** dem Katalog).
- **Auftragsannahme/Deal** (→ Bereich 4). **Rechnung** (→ Bereich 5).

### Ticket-Bestand
`offers` (**Angebot**) · `offer_details` · `offer_folders` · `master_sets` · `klima_plz` · `radiator_specs`/`radiator_installations` · `anforderungsprofil*`. **Kompletter Energie-Rechenkern** `app/Services/{Heizlast,Energie,Klima}/*` (20+ Services) + `/admin/energie/*` (8 Controller, 15 Views, PDF). Heizkörper-Modul live-testgrün. Riesen-Konfig-View `offer/configuration/offer/config.blade.php` (25.064 Z.). **3 isolierte Kron-Services:** `BivalenzService`, `HeizlastService`, `PvProjektService` (0 Aufrufer).

### Playground-Bestand
**Formel-Engine** (`FormulaEvaluationService` eval-frei, `VisibleIfService`, `SmartroutingService`) — in ticket gebaut, isoliert; **Formular-Vorlagen** 21/358; `InverterSizingService`; PV-Dachbelegungs-Datenbasis (`tile_solar_mount`=220, `mounting_roof_compat`=111 — Kategorie B).

### Wberechnung-Bestand
**Der gesamte Energie-Rechenkern (Herkunft)** — transplantiert + verdrahtet (`wberechnung-uebernahme-inventur.md`). Rest-Referenz in wb: `OpenMeteoKlimaService`, `StringBuilderService`, `InverterSuggestionService`, `AuslegungService`/`*HandoffService`, `MassstabVorschlagService`, `wp_material_sets.json`.

### Datenwahrheiten (führend)
`offers`/`offer_details` · `master_sets` · `CatalogDeviceRepository` (Geräte) · `klima_plz`/`KlimaPlzService` · `anforderungsprofile` (Bedarfs-Operanden).

### Mögliche Doppelwahrheiten
- **Duales Angebots-Modell** `offer_details.sections` vs. `offer_product_lists` (Nachsuche-3).
- **WP-Tripel** `Heatpump`/`ProductWP`/`product_heat_pump_specs`.
- `HeizlastService` (0) vs. `HeizlastProjektService` (4) — vermutlich Alt-Wrapper.

### Backend-Sicht
Controller `OfferController`/`OfferFolderController`/`DealMaterialListController`/`Energie/*`/`Heizkoerper/*`/`CostingSetController`/`EconomicAssumptionController`. Services: ganzer `Heizlast/Energie/Klima`-Stack + `Anforderungsprofil/*` + `Form/*` + `Spec/*`. Hooks `Offer::booted`, `Anforderungsprofil(+Wert)::booted`.

### Frontend-Sicht
„Angebots-Assistent / Übersicht / Vorlagen", „Checklisten-Formulare", `/admin/energie/*`-Wizards (WP/WR/Sanierung/Energiekonzept/Heizlast/Fußboden/Grundriss) + `*_dokument`-PDF-Views. Alpine erlaubt (Scope heizkoerper + formulare).

### Workflow-Sicht
Objekt/Gewerk → Bedarf (Anforderungsprofil) → Auslegung (Klima→Verbrauch→Heizlast→WP-Match→Kennlinie→**Bivalenz-Ranking**→JAZ/Strom/Förderung) → Geräte/Sets aus Katalog → Kalkulation → Angebot/PDF. **Bruch:** Kette endet bei „Kandidaten"; **`BivalenzService`-Ranking (E-Stab/Laufstunden/Strom) gebaut, aber nicht erreichbar** (§12).

### Automatisierung
- **(a) automatisch:** Rechenkern bei vollständigen Operanden, Geräte-Vorschlag, PDF.
- **(b) Vorschlag+Bestätigung (Operanden-Gate!):** Geräte-Auswahl, Bivalenzpunkt, Förder-Ansatz — **kein erfundener Wert; bei fehlenden Operanden fragen/markieren**.
- **(c) nicht:** verbindliche Angebotsfreigabe an den Kunden.

### Abhängigkeiten
Braucht **Bereich 1** (Objekt/Gewerk-Anker als Auslegungs-Eingang) + **Bereich 3** (Katalog/Geräte). Liefert an **Bereich 4** (Auftrag).

### Risiken
Größte/komplexeste Domäne (25k-Views, WP-Tripel, duales Angebots-Modell). Falsch (zu spät) → teuerste liegende Substanz bleibt ungenutzt (Yamas Sorge).

### Erster sinnvoller Testpfad
`/admin/energie/wp-auslegung` → berechnen → **prüfen, ob Bivalenzpunkt + E-Stab + Laufstunden + Strom erscheinen** (heute nur bis JAZ). Sichtbarster „gebaut-aber-nicht-verdrahtet"-Beweis.

### Erste Umsetzungsidee (noch NICHT bauen)
Später (Variante B zwingend — Rechenkern/Wizard): (1) `BivalenzService` an `EnergieAuslegungController::wpBerechnen` hängen; (2) `HeizlastService` klären (archivieren o. einhängen); (3) duales Angebots-Modell auf eine Wahrheit.

---

## Bereich 3 — Produkte, Einkauf & Lager (ERP-Material)

### Zweck
**Ein** Produktkatalog als Wahrheit + die materialwirtschaftliche Kette Einkauf → Lager → Materialbedarf für die Montage.

### Enthaltene Funktionen
Artikel/Article-Groups, Master-Sets/Komponenten, Lieferanten/Einkaufs-/Verkaufspreise, DATANORM/GC-Import, Bestelllisten/Materialbedarf, Wareneingang, Inventar/Lager.

### Nicht enthalten
- **Geräte-Auslegung/technische Auswahl** (→ Bereich 2, nutzt Katalog). **Rechnung** (→ Bereich 5). **Fuhrpark/Werkzeug als Ressource** (→ Bereich 7, sofern Betriebsmittel).

### Ticket-Bestand
`products`/`article_groups` (**EIN Katalog**) · `master_sets`(+components/cart) · `distributors`/`distributor_prices`/`distributor_product` · `customer_product*`. Controller `MasterSetController` (2.464)/`ArticleGroup/*`/`DistributorController`/`Inventory/*`/`DatanormController`/`DealMaterialListController` (Bestelllisten). Services `Suppliers/*`/`Import/*`/`Spec/*`. **Audit:** Beschaffungs-Prozessschicht weitgehend leer; **Wareneingang bucht keinen Bestand** (§13, zu verifizieren).

### Playground-Bestand
**Inventur-/Wareneingangs-Regeln** (Idee, Kategorie C). OMD/OpenMasterdata-Connector (playground reifer).

### Wberechnung-Bestand
`batterie_wr_kompatibilitaet` (W-C4-Rest, offen). wb-Procurement **verzichtet** (A4 → OMD-Stack). Geräte-Katalog (WP/PV) **eingefroren**, bereits in `products`/Specs.

### Datenwahrheiten (führend)
`products`/`article_groups` (EIN Katalog) · `master_sets` · `distributor_product`/`distributor_prices`.

### Mögliche Doppelwahrheiten
Mehrere Preis-Schienen (`distributor_prices` vs. `distributor_product` vs. `customer_product*`) → Preis-Wahrheit zu klären. Geräte-Specs additiv, kein Duplikat.

### Backend-Sicht
Models `Product`/`ArticleGroup`(+`booted`)/`MasterSet`/`Distributor`/`DistributorProduct`. Commands `SpecImportCommand`/`SpecRollbackCommand`. Services `Suppliers/*`, DATANORM-Import.

### Frontend-Sicht
„Katalog / Favoriten / Stamm-Listen / Preisvergleich", „Master-Sets", „Lieferanten-Schnittstellen / GC Online IDS", „Einheiten / Rabattgruppen / Artikel-Gruppen".

### Workflow-Sicht
Artikel pflegen (o. DATANORM-Import) → Sets → Preise (EK/VK) → Angebot zieht Sets (Bereich 2) → Auftrag erzeugt Bestellliste (Bereich 4) → Bestellung → Wareneingang → Lagerbestand → Materialverfügbarkeit. **Bruch:** Wareneingang→Bestand nicht gebucht.

### Automatisierung
- **(a) automatisch:** DATANORM/GC-Import, Bestelllisten-Erzeugung aus Auftrag, Bestandsfortschreibung (wenn gebaut).
- **(b) Vorschlag+Bestätigung:** Lieferanten-/Bestellmengen-Wahl, Preis-Übernahme in Kalkulation.
- **(c) nicht:** automatische Bestellauslösung ohne Freigabe.

### Abhängigkeiten
Zuliefer-Bereich für **2** (Auswahl) + **4** (Materialbedarf). Relativ eigenständig aufbaubar.

### Risiken
Zweite Preis-/Katalog-Wahrheit verfälscht Angebote/Rechnungen. Leere Beschaffungs-Prozessschicht → Montage ohne Materialverfügbarkeits-Sicht.

### Erster sinnvoller Testpfad
Artikel anlegen → in Master-Set → im Angebot (Bereich 2) auswählen → prüfen, dass genau **ein** Katalog-Datensatz referenziert wird.

### Erste Umsetzungsidee (noch NICHT bauen)
Später: (1) Preis-Wahrheit vereinheitlichen; (2) Wareneingang→Bestandsbuchung; (3) `batterie_wr_kompatibilitaet` als Adapter.

---

## Bereich 4 — Auftrag, Disposition, Ausführung & Abnahme

### Zweck
Vom angenommenen Angebot zur **realisierten, abgenommenen Baustelle**: Auftrag/Deal, Disposition (Personal/Termine), Montage/Tagesberichte, Abnahme/Nachweise. Die operative Realisierungsschleife.

### Enthaltene Funktionen
Angebotsannahme→Deal, Feinaufmaß, Einsatzplanung (Planner), Montage-Checklisten, Bau-Tagesberichte, Fotos/Rückmeldungen, Abnahmeprotokoll/Mängelliste, technische Nachweise, Grundriss/Plan.

### Nicht enthalten
- **Personal-/Qualifikations-Stammdaten** (Planner **nutzt** sie → Bereich 7). **Rechnung nach Abnahme** (→ Bereich 5). **Wartung/Reklamation nach Übergabe** (→ Bereich 6).

### Ticket-Bestand
`deals` (**Auftrag**) · `deal_measurements`(+details/items/histories) · `deal_notes` · `planner_*` · Checklisten (`Checklist*Controller`) · Reports (`DailyReportController` 4.001, `OverdueCenterController` 4.618) · `project*` (Objekt-Klammer, `deal_id` teils dormant) · `maintenance_protocols` (Muster) · **`handovers` = Lager-Asset-Transfer (Namensfalle, NICHT Kunden-Abnahme)**. Controller `DealController` (3.952)/`DealMeasurementController`/`PlannerPlanController` (**11.097 LOC**)/`PlannerEmployeeApiController`. **Abnahme (J) = niedrigste Reife (0–1):** Protokoll/Mängelliste/Unterschrift fehlen, Abnahme = leere Kanban-Spalte.

### Playground-Bestand
Bautagesbericht-/Nachweis-**Konzepte**, Kapazitäts-/Produktivitäts-Sicht (Idee, Kategorie B/C).

### Wberechnung-Bestand
**Grundriss/Plan-Import teilweise transplantiert** (`GrundrissController` + Views + Routen); Rest-Referenz `MassstabVorschlagService`.

### Datenwahrheiten (führend)
`deals` (Auftrag) · `deal_measurements` (Aufmaß) · `planner_*` (Disposition) · Reports/Checklisten (Ausführung). Abnahme: **noch keine** führende Wahrheit (Lücke).

### Mögliche Doppelwahrheiten
`deal_measurements` vs. Angebots-Positionen; `Appointment` vs. `MainAppointment`; `handovers` (Asset) vs. gewünschte Kunden-Abnahme (Namensfalle).

### Backend-Sicht
Models `Deal`(+`booted`)/`DealMeasurement`(+`booted`)/`PlannerItemEmployee`/`AppointmentEmployee`/`Project`. Command `BackfillDealMeasurementOwner`. `employees` mega-gekoppelt (90 Controller).

### Frontend-Sicht
„Feinaufmaß-Kanban", „Einsatzplan" (Planner), „Allgemeine Aufgaben", „Tagesberichte / Berichts-Übersicht / Überfällige Berichte", Grundriss-Editor.

### Workflow-Sicht
Angebot angenommen → Deal → Feinaufmaß → Einsatzplan (Personal+Termin) → Montage (Checklisten/Tagesberichte/Fotos) → offene Punkte → **Abnahme (Protokoll+Mängel+Unterschrift)** → Übergabe. **Brüche (§13):** Angebot→Auftrag 🔴, Auftrag→Montage 🔴 (keine Verfügbarkeitsprüfung), **Montage→Abnahme ⬜ (fehlt)**, Abnahme→Rechnung 🔴.

### Automatisierung
- **(a) automatisch:** Bestellliste aus Auftrag, Reminder überfällige Berichte, Planungs-Konfliktanzeige.
- **(b) Vorschlag+Bestätigung:** Ressourcen-Zuweisung (Personal/Termin), Rückwärtsterminierung, Abnahme-Freigabe.
- **(c) nicht:** Kunden-Abnahme/Unterschrift; Auftragsstatus „fertig" ohne Beleg.

### Abhängigkeiten
Braucht **2** (Angebot→Auftrag), **3** (Material), **7** (Personal/Qualifikation für Planner). Liefert an **5** (Abnahme→Rechnung) + **6** (Übergabe→Service).

### Risiken
Planner = 11k-Gott-Klasse; Abnahme = schwächste Stelle (Weißfleck). Falsch → Auftrag→Rechnung ohne belegten Abschluss.

### Erster sinnvoller Testpfad
Deal aus Angebot erzeugen → im Einsatzplan Mitarbeiter+Termin → Tagesbericht → prüfen, ob der Deal belegten Ausführungs-Status erhält (und wo die Abnahme-Lücke sichtbar wird).

### Erste Umsetzungsidee (noch NICHT bauen)
Später: (1) Abnahmeprotokoll auf `maintenance_protocols`-Muster; (2) Angebot→Auftrag-Trigger; (3) Verfügbarkeitsprüfung Auftrag→Montage.

---

## Bereich 5 — Finanzen, Rechnung & Controlling

### Zweck
Aus abgeschlossener Leistung **Umsatz** machen (Rechnung), an die **FiBu** andocken (Belege/OP/Buchungsvorschläge), betriebswirtschaftlich **auswerten** (Controlling/Nachkalkulation, Standort-Kosten).

### Enthaltene Funktionen
Rechnungen/Teilrechnungen/Gutschriften/Storno, Zahlungsstatus/OP, Buchungssätze/DATEV-Anbindung, Umsatzdefinition, Mahnung, Soll/Ist-Kosten, Projektmargen, Standort-/Abteilungs-Kosten (Kostenstelle), KPIs.

### Nicht enthalten
- **Vorkalkulation im Angebot** (→ Bereich 2; hier die **Nach**kalkulation).
- **Sanierungs-Wirtschaftlichkeit** (Auslegungs-Fachrechnung → Bereich 2, nicht FiBu — Grenzfall, bewusst getrennt).
- **eigene DATEV-Buchführung** (Kanzlei führt FiBu, A1 — sofern Weiche 3 gilt).

### Ticket-Bestand
`invoices` (**Umsatz-Wahrheit**) · `invoice_items` · `invoice_files` · `accounting_documents`/`accounting_journal`/`accounting_foundation`. Controller `InvoiceController`/`InvoiceCanvasController`/`Old/NewLeadsInvoiceController` (Alt). Services `Accounting/*` (BuchungsEngine, Auswertungs, DatevExtfExport, EingangsBelegfluss, Belegfluss) — **gebaut, 0 Buchungen** · `Invoice/*` (InvoiceNumberService race-safe, InvoiceDeletionGuard). Controlling `DashboardCompanyController`/`DashboardDepartmentController`/`EmployeeDashboardController`. Standort-Kosten `Branch*Expense/Rent/Insurance` (Kostenstelle=Abteilung je Filiale). **Keine persistierte Nachkalkulation.**

### Playground-Bestand
Accounting-Suite **größer/reifer**, aber **Prototyp/legally sensitiv**, DATEV-Testpaket „nicht bestanden" → **Kategorie E (nicht übernehmen)**, nur Fach-Referenz.

### Wberechnung-Bestand
`SanierungsWirtschaftlichkeitService` — fachlich **Bereich 2** (Auslegung), nicht FiBu.

### Datenwahrheiten (führend)
`invoices` (Umsatz — `deal_invoices` stillgelegt) · `accounting_*` · `branch_expenses` (Kostenstelle). `docs/accounting/umsatzdefinition.md`.

### Mögliche Doppelwahrheiten
- **`deal_invoices`** dormant neben `invoices` (§11.4).
- **„bezahlt" doppelt:** amountbasiert (`Invoice::isFullyPaid`, A5) vs. `status='paid'`-Dashboardfilter (A5b offen, §11.5).
- ticket-eigene vs. playground-Accounting (nur ticket-eigene verdrahten).

### Backend-Sicht
Models `Invoice`(+`booted`: Nummernkreis, due_date A3, Storno-Wächter)/`InvoiceFile`(+`booted`). Services `Accounting/*`/`Invoice/*`. **FiBu-Sondergates** (Betriebsordnung).

### Frontend-Sicht
„Rechnungen", Rechnungs-Canvas, Firmen-/Abteilungs-Dashboards. **Offen:** eigener Finanzen-Bereich in der Navi geplant (Memory navi-ia2-finanzen).

### Workflow-Sicht
Abnahme (Bereich 4) → Rechnung (ggf. Teilrechnungen) → Festschreibung (Nummernkreis) → **Buchungssatz an FiBu** → Zahlung/OP → Mahnung; parallel Nachkalkulation + Standort-Controlling. **Brüche:** Abnahme→Rechnung 🔴, **Rechnung→Buchung ⬜ (0 Buchungen)**, Zahlung/Mahnung ⬜, Nachkalkulation ⬜.

### Automatisierung
- **(a) automatisch:** Rechnungsnummer/Fälligkeit (existiert), Buchungssatz-Vorschlag nach Festschreibung, OP-Liste.
- **(b) Vorschlag+Bestätigung:** Konten-Mapping (`mapping_key`), Teilrechnungs-Split, Mahnstufe.
- **(c) nicht:** UStVA/DATEV-Abgabe (Kanzlei), Storno, endgültige Buchung ohne Beleg — **FiBu-Sondergates**.

### Abhängigkeiten
Braucht **4** (Abschluss→Rechnungs-Trigger), **7** (Kostenstelle), **1** (Rechnungsempfänger). **Weiche 3 (Kanzlei führt FiBu)** klären.

### Risiken
GoBD/rechtlich sensibel; FiBu unverdrahtet (0 Buchungen); „bezahlt"-Doppelquelle. Falsch (zu früh, ohne Bereich 4-Abschluss) → Rechnung ohne belegte Leistung.

### Erster sinnvoller Testpfad
Rechnung zu einem Deal → festschreiben → prüfen: Nummernkreis vergeben, Umsatz in `invoices`, Buchungsvorschlag entsteht (bzw. sichtbar, dass er **nicht** entsteht — 0-Buchungen-Lücke).

### Erste Umsetzungsidee (noch NICHT bauen)
Später (eng abgegrenzt): (1) Festschreib→Buchungsvorschlag-Trigger; (2) Dashboards `status='paid'` → amountbasiert (A5b); (3) `deal_invoices` belegt stilllegen.

---

## Bereich 6 — Service, Wartung & Nachbetreuung

### Zweck
Nach der Übergabe: **Servicefälle, Reklamationen, Wartung/Wiederkehr, Bestandskunden-Folgegeschäft.** Schließt den Lebenszyklus und speist neue Leads zurück (Bereich 1).

### Enthaltene Funktionen
Servicetickets/Problemmeldungen, Reklamationen, Wartungsverträge + Wiedervorlage, Bestandskunden, Folgeangebote.

### Nicht enthalten
- **Abnahme-/Übergabe-Doku** (→ Bereich 4). **Norm-/Förder-Nachweise des Angebots** (→ Bereich 2). **Auswertung/Controlling** (→ Bereich 5). **Qualität/Tests** (→ Querschnitt N).

### Ticket-Bestand
`problems`/`error_problem` (Reklamation) · `customer_maintenance_contracts` · `next_service_date` (**keine automatische Wiedervorlage**) · `BrandMaintenanceChecklist`. Controller `Ticket/ProblemController` (2.864)/`Customer/Maintenance/*`/`BrandMaintenanceChecklistController`. `maintenance_protocols`-Muster.

### Playground-Bestand
**Kundendienst/Reklamation** — re-anchored, **API reif, Frontend fehlt** (Kategorie B/F).

### Wberechnung-Bestand
Keiner.

### Datenwahrheiten (führend)
`problems` (Servicefall) · `customer_maintenance_contracts` (Wartung) · `next_service_date` (Wiederkehr).

### Mögliche Doppelwahrheiten
Servicefall (`problems`) vs. Bau-Mängel (Bereich 4 Abnahme) — Abgrenzung „Mangel bei Abnahme" ↔ „Reklamation nach Übergabe".

### Backend-Sicht
Models `Problem`/`ErrorProblem`/`CustomerMaintenanceContract`. Kanban-Position kürzlich ergänzt.

### Frontend-Sicht
Service-/Problem-Board, Wartungsvertrags-Kanban, Reklamations-Liste.

### Workflow-Sicht
Übergabe (Bereich 4) → Wartungsvertrag → `next_service_date` → **Wiedervorlage** → Servicetermin (Bereich 4-Disposition) → Abschluss; parallel Reklamation → Bearbeitung → ggf. Folgeangebot (zurück Bereich 2). **Bruch:** Wartung 🔴 (keine Auto-Wiedervorlage).

### Automatisierung
- **(a) automatisch:** Wiedervorlage-**Vorschlag** aus `next_service_date`, Eskalation überfälliger Fälle.
- **(b) Vorschlag+Bestätigung:** Folgeangebots-Anstoß, Wartungstermin-Planung.
- **(c) nicht:** Kunden-Kontakt/Angebot ohne Freigabe.

### Abhängigkeiten
Braucht **4** (Übergabe) + **1** (Bestandskunde). Speist zurück in **1/2**.

### Risiken
Niedrige Reife; `next_service_date` ohne Automatik → Wartungsgeschäft verfällt. Falsch (zu früh) → baut auf noch fehlender Abnahme (Bereich 4) auf.

### Erster sinnvoller Testpfad
Wartungsvertrag mit `next_service_date` anlegen → prüfen, ob Wiedervorlage/Hinweis entsteht (heute: nein — Lücke sichtbar).

### Erste Umsetzungsidee (noch NICHT bauen)
Später: (1) Wiedervorlage-Vorschlag aus `next_service_date`; (2) Kundendienst-Frontend auf reife API (re-anchored).

---

## Bereich 7 — Organisation, Personal, Standorte & Rechte (Quer-Ressource)

### Zweck
Die **Stammdaten der Leistungserbringer**: Mitarbeiter, Qualifikationen/Ränge, Abteilungen, Standorte/Filialen, Teams, Rollen/Rechte. Fundament für Disposition (Bereich 4), Zuständigkeit (Bereich 1) und Kostenstellen (Bereich 5).

### Enthaltene Funktionen
Mitarbeiter-Akte (Adresse, Dokumente, Lizenzen, Kleidung, Zeitbudget, Urlaub/Krank/Kurzabsenz, Zeitplan), Qualifikationen + Positions-/Rang-Hierarchie, Abteilungen, Standorte/Filialen (inkl. Kostenstruktur), Teams, Benutzer-Rollen/Rechte (Definition), Betriebsmittel/Fuhrpark.

### Nicht enthalten
- **Einsatz-Planung selbst** (Planner-Engine → Bereich 4; hier nur Ressourcen-Stammdaten).
- **Rechte-Durchsetzung/Gates/IDOR** (technisch → Querschnitt N).
- **Branch-Kosten als Auswertung** (→ Bereich 5; hier nur Erfassung).

### Ticket-Bestand
`Employee` (**mega-gekoppelt, 90 Controller**) + ~25 Sub-Models (`EmployeeAddress/Document/License/Cloth/MonthlyTimeBudget/RecurringLeave/ShortLeave/Sick/TimeSchedule/ProjectCoin/Set/Problem/PostcodeList`). **Org:** `Department`/`DepartmentPosition`/`EmployeeDepartment`/`BrandDepartment`/`DistributorDepartment`/`ExternalDepartments`/`ActivityDepartment`. **Rang:** `Position`/`PositionQualification`(+`Hierarchy`)/`Qualification`/`CostingSetRole`/`ActivityPosition`/`ProductPosition`. **Standort:** `Branch`(+`Address`/`ContractDetails`/`Expense`(+`OtherCost`)/`Insurance`/`Rent`/`RentInfo`, company_profile). **Team:** `Team`/`TeamMember`. **Rechte:** `User`/`UserRoll`/`UserRollItem` (`user_rolls`), `User::hasPermission()`. **7 überlappende Rollen-/Rang-Systeme**; `position_qualifications` = Rang-Fundament; Autoritäts-Logik teils **dormant** (Memory hierarchie-berechtigungen-landkarte).

### Playground-Bestand
**RBAC-Modell** (Idee, Kategorie C — eigener Security-Strang, **kein** zweites Auth ins Live). Betriebsmittel/Fuhrpark (Kategorie B). Append-only-Audit (`history_entries`, Idee C).

### Wberechnung-Bestand
`users` (1) — **verzichtet** (ticket-Auth gilt).

### Datenwahrheiten (führend)
`employees` (Person) · `departments` (Abteilung=Kostenstelle) · `branches` (Standort) · `positions`/`position_qualifications` (Rang) · `user_rolls` (Rechte) · `teams`.

### Mögliche Doppelwahrheiten
**7 überlappende Rollen-/Rang-Systeme** (Position, Qualification, PositionQualificationHierarchy, UserRoll, CostingSetRole, Department-Position, Team) → **zentrale Doppelwahrheit dieses Bereichs**. `ExternalDepartments` vs. `Department`.

### Backend-Sicht
Models s. o. (Hook `Employee::booted`). Commands `UpdateLeaveStatus`/`UpdateJobRepresentativeStatus`. Middleware `permission` → `CheckUserPermission` (Definition hier, Durchsetzung = N).

### Frontend-Sicht
Mitarbeiter-Verwaltung, Abteilungen/Positionen, Standort-/Filial-Stammdaten (+ Kosten), Teams, Rollen-/Rechte-Verwaltung, Qualifikations-Matrix.

### Workflow-Sicht
Mitarbeiter → Abteilung/Standort/Position → Qualifikationen → Rolle/Rechte → Team → **steht Disposition (Bereich 4) als Ressource bereit**, trägt Zuständigkeit (Bereich 1) + Kostenstelle (Bereich 5).

### Automatisierung
- **(a) automatisch:** Urlaubs-/Abwesenheits-Status (`UpdateLeaveStatus`), Vertreter-Status.
- **(b) Vorschlag+Bestätigung:** Rechte-Vergabe, Qualifikations-basierte Einsatz-Eignung.
- **(c) nicht:** Rollen-/Rechte-Änderung ohne Freigabe (Sicherheits-/Betriebsthema).

### Abhängigkeiten
Quer-Fundament für **4** (Disposition), Zuständigkeit in **1**, Kostenstellen in **5**. Eng begleitet von **N** (Sicherheit).

### Risiken
7 konkurrierende Rollen-Systeme → Rechte-Divergenz/Sicherheitslücken. `employees` 90-fach gekoppelt → jede Änderung breit. Falsch → Disposition/Zuständigkeit auf unklarem Rang-Fundament.

### Erster sinnvoller Testpfad
Mitarbeiter mit Qualifikation + Abteilung + Standort + Rolle anlegen → in Bereich 4 (Planner) als disponierbare Ressource prüfen → Rechte-Wirkung gegen `user_rolls`.

### Erste Umsetzungsidee (noch NICHT bauen)
Später (eigener Security-Strang): (1) die 7 Rollen-Systeme kartieren → ein führendes Rang-Fundament (`position_qualifications`) belegen; (2) Betriebsmittel/Fuhrpark-Quick-Win.

---

## Querschnitt N — Architektur, Sicherheit, Performance, UX, Tests (Prüfbrille, NICHT sequenziell)

**Kein abzuarbeitender Bereich, sondern die Brille über allen sieben.** **Sicherheit** (`user_rolls`/`hasPermission`-**Durchsetzung**, Middleware `permission`, P0/P1-IDOR über 98 Controller gegatet, `tests/Feature/Security`) · **Architektur** (zwei Welten Alt-Fett-Controller ↔ junge Services, Strangler-Weg, `bauordnung.md`) · **Performance** (706k View-LOC, Inline-JS `kanban.js` 17k) · **UX** (kein Design-System: 253 Farben/152 Buttons, `ux-frontend-audit.md`) · **Tests/QA** (71 Dateien) · **toter Ballast** (`Old/` 37 Dateien tot, ~110 copy/backup, ~28 „Old Code"-View-Ordner — Rückfall-/Archiv-Regel beachten). **Jeder Bereich wird durch N mitgeprüft.**

---

## 8. Priorisierung (fund-basiert, KEINE Umsetzungs-Freigabe)

| Prio | Bereich | Warum | Abhängigkeiten | Erster prüfbarer Frontend-Pfad | Risiko |
|---|---|---|---|---|---|
| 1 | **2 Angebot/Auslegung** | größte fertige, aber **unverdrahtete** Substanz (`BivalenzService` 0 Aufrufer); Yamas Sorge | 1 (Objekt-Anker), 3 (Katalog) | `/admin/energie/wp-auslegung` → Bivalenz-Ranking sichtbar | hoch (größte Domäne) |
| 2 | **1 Kunde/Objekt/Vertrieb** | Fundament aller Anker; Zombie/Stage-Zoo/God-Table | — | Lead+Objekt+Gewerk konsistent + Stage-Move→Follow-up | hoch (God-Table/Gott-Klasse) |
| 3 | **5 Finanzen/FiBu** | `invoices` sauber, FiBu gebaut aber 0 Buchungen; geld-kritisch, eng | 4 (Abschluss), 7 (Kostenstelle) | Rechnung festschreiben → Buchungsvorschlag | hoch (GoBD) |
| 4 | **4 Auftrag/Ausführung/Abnahme** | Realisierungsschleife; **Abnahme = Weißfleck** | 2, 3, 7 | Deal→Einsatzplan→Tagesbericht | hoch (Planner-Gott-Klasse) |
| 5 | **3 Produkte/Einkauf/Lager** | eine Katalog-Wahrheit; Beschaffungs-Prozessschicht leer | — (Zulieferer 2/4) | Artikel→Set→Angebot: eine Wahrheit | mittel |
| 6 | **7 Organisation/Personal/Rechte** | 7 Rollen-Systeme; Rang-Fundament dormant | N (Sicherheit) | Mitarbeiter+Qualifikation als Planner-Ressource | mittel-hoch |
| 7 | **6 Service/Wartung** | niedrige Reife; baut auf Abnahme (4) auf | 4, 1 | Wartungsvertrag → Wiedervorlage | niedrig-mittel |
| — | **N Querschnitt** | begleitet **alle** (Sicherheit/Reife/Doppelwahrheit) | — | pro Bereich mitgeprüft | — |

---

## 9. Zwei Reihenfolge-Varianten

### Variante A — fachlich sauberste Reihenfolge (Datenwahrheit vor Prozess)
**7 Organisation/Rechte → 1 Kunde/Objekt/Vertrieb → 3 Produkte/Lager → 2 Angebot/Auslegung → 4 Auftrag/Ausführung → 5 Finanzen → 6 Service** *(N begleitend)*.
**Logik:** Erst die **Fundament-Datenwahrheiten** (Personal/Rechte, Kunde/Objekt, Katalog), dann die Prozesse in realer Kettenreihenfolge. Minimiert das Risiko, auf unsauberen Ankern zu bauen. **Nachteil:** die teure Substanz (Auslegung) kommt erst an 4. Stelle → langsamer sichtbarer Nutzen.

### Variante B — schnellster sichtbarer Nutzen für Yama
**2 Angebot/Auslegung → 1 Kunde/Vertrieb → 5 Finanzen → 4 Auftrag/Ausführung → 3 Produkte/Lager → 7 Organisation → 6 Service** *(N begleitend)*.
**Logik:** Zuerst die Bereiche mit **fertiger, nur unverdrahteter Substanz** → schnell im Browser zeigbar (WP-Auslegung mit Bivalenz-Ranking; Arbeitsliste; Rechnung). **Nachteil:** arbeitet anfangs auf noch unsauberen Fundament-Ankern.

---

## 10. Verworfene Alternativ-Strukturen (und warum)

- **8 getrennte Bereiche (Stammdaten ≠ CRM als eigene Bereiche).** Verworfen zugunsten der Zusammenführung 1+2: identische Datenwahrheit, nicht getrennt abarbeitbar (K1+K2). Yamas Leitprinzip „sauber zusammenführen".
- **Die 14 Kapitel A–N 1:1 als Bereiche.** Verworfen: B…M sind Prozessschritte, keine abarbeitbaren Bereiche. A–N bleiben **Feinraster innerhalb** der 7 Bereiche.
- **Weitere Reduktion auf 6 (Service in Ausführung, oder Personal in N).** Verworfen: Service (nach Übergabe, Daten `problems`/`maintenance`) ≠ Ausführung (während Bau, Daten `deals`/`planner`) — Zusammenlegung wäre ein Sammeltopf (K3). Personal/Org (Business-Stammdaten `employees`) ≠ technische Prüfbrille N — nur die Rollen-*Durchsetzung* gehört zu N, nicht die Personal-Stammdaten.
- **„CRM vs. ERP" (2 Bereiche).** Verworfen: zu grob, versteckt Auslegungs-Substanz (2), FiBu-Sensibilität (5), Rollen-Problem (7).
- **Nach Code-Ordnern/Controller-Gruppen.** Verworfen: folgt der Alt-Fett-Controller-Struktur (Gott-Klassen) und zementiert Doppelwahrheiten statt fachlich zu ordnen.

---

## 11. Empfehlung

**Struktur:** die **7 Bereiche + Querschnitt N** oben (Themen sauber zusammengeführt: eine Datenwahrheit/ein Workflow-Abschnitt je Bereich).

**Reihenfolge:** **Hybrid aus A und B** — *„sichtbare Substanz zuerst, mit Fundament-Vorbehalt"*:
1. **Start mit Bereich 2 (Angebot/Auslegung)** — höchster Nutzen pro Aufwand (`BivalenzService` verdrahten, nicht neu bauen), sofort im Browser prüfbar, deckt Yamas Sorge (`system-inventur.md` §15 Option 1). **Vorbehalt:** der Eingangs-Kontrakt aus **Bereich 1** (Objekt `lead_alternative_adds` / Gewerk `lead_product_list` als Auslegungs-Anker) wird **vorab read-only geklärt** — sonst wird auf unsicherem Anker verdrahtet.
2. Dann **1** (Fundament nachziehen), **5** (Rechnung/FiBu, eng), **4** (Realisierung inkl. Abnahme).
3. Danach **3 → 7 → 6** in realer Kettenreihenfolge.
4. **Querschnitt N** in **jedem** Bereich mitprüfen — nie als „später"-Posten.

**Begründung:** Bereich 2 hat den höchsten Nutzen-pro-Aufwand (viel gebaut, nur verdrahten), liefert sofort einen prüfbaren Browser-Pfad und ist abgegrenzt genug, um ihn ohne Umbau des Alt-Kerns zu bearbeiten — solange der Objekt-/Gewerk-Anker (Bereich 1) als Eingang steht. Variante A wäre fachlich sauberer, verzögert aber den sichtbaren Nutzen zu stark; der Hybrid nimmt nur das Fundament-Risiko heraus.

---

## Evaluator-Notiz (Selbstprüfung)

- **Belegt (firsthand, 2026-07-11):** führende Wahrheiten/Doppelwahrheiten/Prozessbrüche (`system-inventur.md`); Energie-Rechenkern + 3 isolierte Services (`wberechnung-uebernahme-inventur.md`, live-grep); Org-/Standort-/Rollen-Models + Migrationen (eigener Lauf: `Branch`+Kosten-Subtree, `Department`/`Position`/`Qualification`, `user_rolls`, `Team`); Kategorien A–F (`playground-uebernahme-inventur.md`).
- **[Grundlage: Audit 2026-07-08 / Memory, nicht neu vermessen]:** LOC-/Reife-Zahlen, „Wareneingang bucht keinen Bestand", playground-Modul-Reifegrade, 7-Rollen-System-These, „Abnahme = leere Spalte".
- **Bewusst offen (Yama-Entscheidung):** genaue Naht Angebot→Auftrag (Bereich 2↔4); ob Sanierungs-Wirtschaftlichkeit bei 2 oder 5; Weiche 3 (FiBu). Bereichszahl bewusst auf 7 gesetzt (Zusammenführung 1+2), nicht künstlich gehalten.
- **Keine vorschnelle Empfehlung:** Priorisierung + Varianten sind **Vorschlag**, keine Freigabe.
- **Nicht gemacht (korrekt):** keine Umsetzung/Import/Refactor/Codeänderung/Automatisierung, **kein Gesamtkonzept**. `rueckfall-archiv-regeln.md` beachtet.

---

*Nächster Schritt laut Auftrag: **STOPP.** Yama prüft die Bereichsstruktur und entscheidet Bereiche + Reihenfolge. Erst danach (auf Freigabe) folgt die Tiefen-Inventur des ersten Bereichs bzw. das Gesamtkonzept.*
