# Playground → ticket — Übernahme-Inventur (Vergleichs-Landkarte)

**Stand:** 2026-07-11 · **Rolle:** Koordinator + Experten (read-only) · **Modus:** nur vergleichen/inventarisieren/bewerten/listen — **kein Bau, kein Import, kein Kopieren, keine Migration, kein Refactor, kein Gesamtkonzept, keine Strategieentscheidung.**
**Quellen:** ticket-Systeminventur (3 Runden, firsthand) · `playground-ticket-vergleich-entscheidung.md` (2026-07-05) · `uebernahme/playground-wert-inventur.md` (417 Z., firsthand 2026-07-05) · **eigene aktuelle playground-Struktur-Prüfung (2026-07-11)** · `architektur-entscheidungen.md`, `STRAENGE.md`.
**playground-Pfad:** `/Users/yamanuri/Documents/Playground/backend-laravel` (read-only Quelle).

---

## 1. Was aus `playground-ticket-vergleich-entscheidung.md` weiter gilt

Die Grundentscheidung (2026-07-05) gilt **unverändert**:

1. **ticket bleibt führendes System.** playground wird **nicht** als zweites System importiert, **nicht** als Design-Vorlage. Der Weg ist: ticket-Bestand behalten (Live-Funktion) → playground-**Ideen** extrahieren, wo fachlich besser → in ticket neu integrieren mit **ticket-Auth, ticket-Layout, ticket-Datenmodell, ticket-Navigation, ticket-Begriffen**.
2. **Keine zweite Wahrheit:** kein zweiter Kundenkatalog, kein zweiter Artikelkatalog (**EIN-Katalog-Weiche**), keine zweite Angebots-/Rechnungs-Welt, kein zweites Auth (RBAC nur als **Konzept**).
3. **playground = „Bauteile-Lager"** (Fazit §7 der Entscheidung).
4. **Prioritäten-Reihenfolge:** (P1) Formular-Engine/Smartrouting-Synthese → (P2) Angebotsampel/Pflichtdaten-Gates → (P3) selektiv Energie-Dienste (Lastmanagement/Dachbelegung) → (P4) Reklamation/Serviceauftrag → (P5) Betriebsmittel/Fuhrpark (nur Felder vergleichen).
5. **Buchhaltung/DATEV = eigener Strang** (nicht im allgemeinen Playground-Transfer); **HR/Lohn = eigener Strang**.
6. **Nicht übernehmen:** React-SPA / 3D-Dachplaner (Three.js) / TS-Connectoren (stack-fremd) · playground-Navigation als Zielnavigation · playground-Design · playground-Artikelkatalog · CRM/Angebote/Aufträge/Disposition/Org (ticket-Doppelung).
7. **Weichen bindend:** W5 (keine eigene „Projekt"-Ebene; Kunde→Objekt→Gewerk) · EIN Katalog · playground-Daten sind **Sample** (customers=14, offers=4 …) → **keine Live-Daten** wandern.
8. **`foerderungen`** wurde bereits aus playground übernommen (erledigt, nur testen/härten).

---

## 2. Was durch diese neue Untersuchung ergänzt oder korrigiert wurde

**Zählungs-/Struktur-Delta (playground, 2026-07-11 vs. Wert-Inventur 2026-07-05):**
| | Wert-Inventur (05.07.) | jetzt (11.07.) |
|---|---|---|
| Models | 274 | **289** |
| Controller | 269 | 269 |
| Services | (nicht genannt) | **154** |
| Migrationen | 164 | 164 |
| Views (Blade) | ~180 | **165** |
| Tests | (nicht genannt) | **262** |
→ playground ist **gewachsen** (Models +15, sehr viele Services/Tests) — v. a. **Accounting** und **Energie**. Der genaue Zuwachs ist **nicht kartiert [zu-verifizieren]**.

**Wichtigste Korrekturen (weil ticket seit 2026-07-05 gebaut hat):**
- **P1 Formular-Synthese ist TEILWEISE AUSGEFÜHRT in ticket.** ticket hat jetzt `FormulaEvaluationService` (eval-frei), `VisibleIfService`, `SmartroutingService`, `FormSchemaValidator` (FS-01/02/04/05/07 „gebaut"), `ProductFormulaController` geroutet. **ABER:** `SmartroutingService` = **0 Regeln / 0 Aufrufer** (isoliert), Antwortspeicherung/Vorlagen offen. → Die playground-**Engine-Ideen** sind großteils schon in ticket **gebaut**, aber **nicht verdrahtet**. „Übernehmen" verschiebt sich zu „verdrahten + Vorlagen".
- **InverterSizingService existiert jetzt in ticket** (verdrahtet in `EnergieAuslegungController`). Die playground-WR-Sizing-Idee ist damit teilweise da.
- **ticket hat jetzt eine EIGENE, komplette FiBu-Suite — aber ISOLIERT.** `BuchungsEngine`/`Belegfluss`/`EingangsBelegfluss`/`Datev`/`Auswertung` + 10 modellose `accounting_*`-Tabellen, **0 Aufrufer / 0 Buchungen**. → Die alte Aussage „ticket hat keine Buchhaltung" ist **überholt**. Playground-Accounting ist damit **nicht** der einzige Pfad; **ticket-eigene FiBu zuerst verdrahten**, statt playground zu importieren.
- **playground-Accounting ist GRÖSSER als 2026-07-05 dargestellt.** Statt „45 Models" jetzt eine sehr große Suite (40+ `Accounting*`-Models sichtbar: Kasse, Bank, Mahnung, Gates, DATEV-Export+Log, BWA, AfA, Perioden, Zahlungszuordnung, Prüf-Cases…). Weiterhin **Prototyp / legally sensitiv / DATEV-Testpaket „nicht bestanden"** — Einordnung unverändert (eigener Strang, nicht importieren).
- **playground-Energie ist breiter:** `Energie/{Performance,StringBuilder,PvBelegungExtractor,Schutzkomponenten,RoofTemplateFeatureExtractor,Kabel,EpsBox,InverterSizing}` — über WR-Sizing hinaus (String-Bau, PV-Belegung, Schutzkomponenten, Kabel). [zu-verifizieren welche ticket fehlen]
- **Weiche 3 (FiBu „Kanzlei führt")** ist laut Wert-Inventur Anhang E **von Yama aufgehoben** worden — **im `architektur-entscheidungen.md` aber nicht nachgezogen**; Status **zu bestätigen** (offene Frage).
- **`deal_invoices`-Divergenz:** Glossar „BEHALTEN (schlafendes Feature)" ↔ Systemkarte „Drop ausstehend".

---

## 3. Vergleich entlang der ticket-Kapitel A–N

> Je Kapitel kompakt. **F/M** = Belegtiefe playground großteils aus Wert-Inventur (NICHT-VERIFIZIERT line-by-line).

### A — Systemlandkarte / Querschnitt
ticket: eigene Landkarte (3 Inventur-Runden). playground zusätzlich: modularere Struktur (`routes/modules/*`, RBAC). Besser playground: Modularität, RBAC-Konzept, `history_entries` (Append-only-Audit). Besser ticket: Live-Betrieb, Sicherheit gehärtet. Übernehmbar: **RBAC-/Audit-Konzept** (kein Port). Führende Wahrheit: ticket. Risiko: zwei Auth-Systeme.

### B — Eingang / Lead / Kunde / Objekt
ticket: `new_leads`(~3000)/`lead_alternative_adds`(Objekt)/`lead_product_lists`. playground: `customers=14`/`objects=13` (Sample), sauberer modelliert. Doppelt: gesamte Kundenwelt. **Nicht übernehmen** (EIN Kundenwelt). Objekt-Ebene existiert in ticket als FK-Kette. Führende Wahrheit: ticket. Risiko: zweite Kundenwelt.

### C — Vertrieb / CRM-Prozess
ticket: Kanban/Aufgaben/Arbeitsliste/FollowUpCreator. playground zusätzlich: **Angebotsampel** (Pflichtdaten-Gate vor Phasenwechsel — echtes Konzept, das ticket fehlt). Besser playground: Ampel-Gate. Übernehmbar: **Angebotsampel = Konzept (B)**, an ticket-`changeStage`-Guard/Weiche-1 mappen, kein Code-Port. Führende Wahrheit: ticket-`lead_stages`. Risiko: Ampel hängt an Weiche 1.

### D — Angebot / Auslegung / Konfiguration
ticket: Angebot (`offers`/`offer_details`), **normbasierte Auslegungs-Kette** (Heizlast/Bivalenz/Klima/WR — teils via wberechnung). playground zusätzlich: **Formular-Engine/Smartrouting** (Aufnahme), **Angebotsampel**, **PV-Dachbelegung** (`tile_solar_mount`=220/`mounting_roof_compat`=111 — geronnene Montage-Kompatibilität), **Lastmanagement**. Besser playground: Formular-Aufnahme + Dachbelegungs-Fachlogik. Besser ticket: die Auslegungs-Rechenkerne (führend), W5-native Formular-Grundlage. Doppelt: WP/PV/WR-Auslegung (Cut-over). **Übernehmbar (mit Adapter):** Formular-Engine-**Lücken** (Vorlagen, `visible_if` vervollständigen, erweitertes Smartrouting) · **Dachbelegungs-Datenbasis** (nur Laravel-Datenmodell, kein 3D). Führende Wahrheit: Auslegung=ticket-Services, Katalog=ticket. Risiko: zweite Rechenwahrheit; playground-Formular hängt an `Project`/`interests` → Re-Anchoring nötig.

### E — Auftrag / Deal
ticket: `deals`/`deal_measurements`. playground: `orders=1`/Angebotsbestätigungen (Sample). Doppelt. **Nicht übernehmen** (ticket führend). Mögliche Idee (D): Angebotsannahme-Pflicht vor Auftrag.

### F — Produkte / Katalog / Sets / Preise
ticket: EIN Katalog (`products`/`article_groups`/`master_sets`/`distributors`/OMD). playground: `articles=398` (Sample), Stückliste/tech-Daten. Doppelt + **EIN-Katalog-Weiche**. **Nicht übernehmen** (Katalog); höchstens Feld-Vergleich. Führende Wahrheit: ticket-Katalog. Risiko: zweiter Katalog.

### G — Beschaffung / Lager / Material
ticket: Bestelllisten/Distributor-Preise; **`GoodsReceipt` bucht keinen Bestand** (Lücke). playground: Lagerorte/Bestellungen/Wareneingänge/**Inventur** (Hüllen, leer). Übernehmbar: **Inventur-/Wareneingangs-Regeln als Konzept (B/F)** falls ticket sie nicht hat — leere Hüllen, niedrige Prio. Führende Wahrheit: ticket. Risiko: leer + Doppel-Schema.

### H — Planung / Disposition / Ressourcen
ticket: Planner (mobil, Nuriva). playground: Dispositionen/Termine/Kapazität (leere Hüllen). Doppelt + leer. **Nicht übernehmen**; Kapazitäts-/Produktivitäts-Sicht evtl. Konzept (B), nachrangig.

### I — Montage / Ausführung / Tagesberichte
ticket: Tagesberichte/Checklisten/**Montage-Progressbar** (gebaut)/Feld→Büro-Rückfluss. playground: Bautagesberichte/Feinaufmaß/Aufgabennachweise (dünn); **Projekt-Ebene = Weiche-5-Verstoß**. Übernehmbar: **Bautagesbericht-/Nachweis-Foto-Ketten als Konzept (B)** auf Gewerk-Ebene. Nicht: Projekt-Ebene.

### J — Dokumentation / Abnahme / Nachweise
ticket: **keine Abnahme-Kette** (leere Stufe), `MaintenanceProtocol`-Muster ungenutzt. playground: Nachweise/Dokumente (Sample); Append-only-Audit. Übernehmbar: **Nachweis-/Audit-Muster als Konzept (B)**. Führende Wahrheit: neu in ticket. Risiko: eigenes Konzept nötig.

### K — Rechnung / Zahlung / FiBu
ticket: `invoices` (führend, A3/A5) + **eigene FiBu-Suite ISOLIERT** (0 Buchungen). playground: **große FiBu/DATEV-Suite** (40+ Models, Kasse/Bank/Mahnung/GoBD-Gates/DATEV/BWA/AfA — **Prototyp, DATEV nicht bestanden, legally sensitiv**). Besser playground: Umfang (Kasse/Bank/Mahnung/GoBD-Gates). Besser ticket: an `invoices`-Wahrheit angebunden. Doppelt: die ganze FiBu (jetzt **auf beiden Seiten**). **Nicht importieren** (zweite Wahrheit + Risiko); **ticket-eigene FiBu zuerst verdrahten**; playground höchstens als **Fach-Referenz** (welche GoBD-Gates/Bank-Matching fehlen ticket). Führende Wahrheit: ticket-`invoices`/`accounting_*`. Risiko: **hoch** (rechtlich/GoBD), Weiche 3 offen.

### L — Controlling / Nachkalkulation
ticket: Vorkalkulation, keine persistierte Nachkalkulation; Kostenstellen-Konzept (kein Code). playground: Controlling-KPI/OKR/Abteilungs-GuV (leer). Übernehmbar: **Konzept (B)**, nach K. Nicht: OKR (leer, niedrige Prio); Abteilungs-GuV berührt FiBu-Weiche.

### M — Service / Betrieb / Reklamation / Wiederkehr
ticket: `Problem`(Ticket-Kern), keine Auto-Wiedervorlage; Serviceaufträge dormant. playground: **Kundendienst (Tickets/Reklamation/Serviceauftrag) — reifes Api-Backend (A-Kandidat #2 lt. Wert-Inventur: CRUD+Status+Threading+SLA+Audit), Daten leer.** Besser playground: Reklamation/SLA-Workflow. Übernehmbar: **Reklamation/Serviceauftrag als ticket-Erweiterung (B/A)** — an `new_leads`/`deals` re-anchoren, Standard-Blade-CRUD, **kein zweites Ticket-System**. Führende Wahrheit: ticket-`Problem`/`new_leads`. Risiko: zweites Ticket-System.

### N — Querschnitt Architektur / Sicherheit / Performance / UX
ticket: Sicherheit gehärtet (P0/P1); tote Zone. playground: **RBAC (85 Permissions)**, `history_entries` (Append-only-Audit) — sauberer als ticket-`user_rolls`/`is_admin`. Übernehmbar: **RBAC-/Audit-Konzept (B)** — großer eigener Strang, **kein** Direkt-Port ins Live-Auth. Risiko: zwei Auth-Systeme.

---

## 4. Übernahmekategorien A–F

**A — bereits in ticket übernommen/gebaut:** `foerderungen` · Formel-Engine-Bausteine (`FormulaEvaluationService`/`VisibleIfService`/`SmartroutingService`, FS-01/02/04/05/07) *(gebaut, aber isoliert)* · `InverterSizingService` · wberechnung-Energie-Kerne (separater Strang).
**B — fachlich wertvoll, nur mit Adapter:** Angebotsampel (Pflichtdaten-Gate → `changeStage`-Guard) · erweitertes Smartrouting (Service/Gewerk/Objekt/Phase, an ticket-Anker) · Formular-Vorlagen (21/358 als Marker-Seeder in `product_formulas`) · PV-Dachbelegungs-Datenbasis (nur Datenmodell) · Kundendienst/Reklamation (re-anchored) · Bautagesbericht-/Nachweis-Konzepte.
**C — Idee übernehmen, Code nicht:** RBAC-Modell · Append-only-Audit (`history_entries`) · Inventur-/Wareneingangs-Regeln · Kapazitäts-/Produktivitäts-Sicht · Controlling/OKR.
**D — bereits in ticket vorhanden (playground = Doppelung):** CRM/Kunde/Objekt · Angebote/Aufträge · Katalog/Artikel · Disposition/Planner · Org/Abteilungen/Teams · WP/PV/WR-Auslegung (Cut-over).
**E — nicht übernehmen (zweite Wahrheit / Risiko / schlechter):** playground-Buchhaltung 1:1 (Prototyp/legally sensitiv) · zweites Auth/RBAC ins Live · React-SPA/3D-Dachplaner/TS-Connectoren · playground-Navigation/-Design · playground-Live-Daten.
**F — unklar, Tiefenprüfung nötig:** Kundendienst-/Betriebsmittel-**Code-Reife** (Api reif, Frontend fehlt) · Lastmanagement/Dachbelegung-Verzahnung (in Energie-Architektur jetzt oder später?) · welche playground-Energie-Services (String/Schutz/Kabel) ticket wirklich fehlen · das genaue playground-Wachstum seit 05.07.

---

## 5. Zukunftsliste (Priorität · Baustein · Kapitel · Nutzen · Risiko · Übernahmeart · Voraussetzung)

| Prio | playground-Baustein | ticket-Kapitel | Nutzen | Risiko | Übernahmeart | Voraussetzung |
|---|---|---|---|---|---|---|
| 1 | Smartrouting **verdrahten** + Formular-Vorlagen | D/C | hoch (Aufnahme-Automatik) | mittel | verdrahten (B, Ideen da) | ticket-Formular-System-Befund |
| 2 | Angebotsampel / Pflichtdaten-Gate | C/D | hoch | mittel | Konzept+Adapter (B) | Weiche 1 (Phasen) |
| 3 | Kundendienst / Reklamation | M | mittel-hoch | mittel | Adapter (B/A) | Code-Reife-Befund + Re-Anchoring |
| 4 | PV-Dachbelegungs-Datenbasis | D/F | mittel | mittel | Datenmodell (B) | Energie-Verzahnungs-Weiche (Yama) |
| 5 | Lastmanagement/Lastprofil | D | mittel | mittel | Datenmodell+Logik (B) | Energie-Architektur-Weiche |
| 6 | Betriebsmittel/Fuhrpark | H/M | niedrig-mittel | niedrig | Quick-Win-Adapter (B) | Code-Reife (klein) |
| 7 | RBAC / Append-only-Audit | N | Härtung | hoch | Konzept (C), eigener Strang | Security-Instanz |
| — | Buchhaltung/DATEV | K | groß | **sehr hoch** | **nicht** (eigenes Accounting-Projekt) | ticket-FiBu zuerst verdrahten + DATEV-Zertifizierung |

---

## 6. Offene Fragen an Yama

1. **Weiche 3 (FiBu):** Gilt „Kanzlei führt FiBu / A1" — oder ist sie (wie Wert-Inventur Anhang E notiert) **aufgehoben**? (Bestimmt, ob K = verdrahten der ticket-FiBu oder eingefroren.)
2. **ticket-eigene FiBu (isoliert) vs. playground-FiBu:** Bestätigst du, dass die **ticket-eigene** Accounting-Suite verdrahtet wird und playground-FiBu **nicht** importiert wird (nur Fach-Referenz)?
3. **Formular-Synthese-Rest:** Sollen die playground-**Vorlagen** (21 Formulare/358 Felder) als Marker-Seeder in `product_formulas` überführt werden, und wird `SmartroutingService` verdrahtet?
4. **Energie-Verzahnung:** Dachbelegung (`tile_solar_mount`/`mounting_roof_compat`) + Lastmanagement **jetzt** additiv in die frische Energie-Architektur, oder später?
5. **`deal_invoices`:** behalten (Glossar) oder droppen (Systemkarte)?
6. **Kundendienst/Betriebsmittel:** eigener Code-Reife-Befund vor Übernahme-Entscheidung — freigegeben?
7. **RBAC-Ablösung:** eigener Security-Strang jetzt planen oder Parkplatz?

---

## 7. Evaluator-Notiz

- **Belegt (firsthand):** aktuelle playground-Zählungen/Struktur/Service-Existenz (2026-07-11); ticket-Ist (3 Inventur-Runden); die Grundentscheidung + Prioritäten (Entscheidungs-Doku).
- **[Grundlage: Wert-Inventur 2026-07-05, NICHT line-by-line neu verifiziert]:** Modul-**Reifegrade** (Formular-Engine-Lücken, Kundendienst-Api-Reife, Buchhaltung-Prototyp, Dachbelegungs-Zeilen), Live-Row-Counts (Sample-Daten).
- **Offene Suchlücken:** genaues playground-Wachstum seit 05.07. (Models +15, Services/Tests stark — nicht kartiert); welche Energie-Services ticket real fehlen; Reife der gewachsenen Accounting-Suite; ob ticket-Checklisten-System dem playground-Formular wirklich überlegen/gleichwertig ist (ticket-Formular-System-Befund steht noch aus).
- **Keine vorschnelle Empfehlung:** Kategorien sind Fund-basiert, **nicht** priorisierte Umsetzungsentscheidung. Die Zukunftsliste ist Vorschlag, **nicht** Freigabe.
- **Nicht gemacht (korrekt):** kein Bau, kein Import, kein Kopieren, keine Migration, kein Refactor, kein Gesamtkonzept, keine Strategieentscheidung, kein Commit.
