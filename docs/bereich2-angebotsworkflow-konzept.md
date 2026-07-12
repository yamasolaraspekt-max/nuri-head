# Bereich 2 — Angebotsworkflow / Angebotsreife — Konzept

**Stand:** 2026-07-12 · **read-only · nur Analyse/Konzept** · kein Bau/Refactor/Import/Migration/Automatisierung/Commit/zweite Wahrheit/Angebotslogik-Umbau.
**Grundsatz (Yama, verbindlich):** *Ein Angebot darf nicht beliebig beginnen. Vor dem Angebot muss geklärt sein, ob ein Vorgang **angebotsfähig** ist.*
**Grundlage (firsthand, `datei:zeile`/Tabelle):** DB `ticket` (Live-Werte/Counts) + Code; baut auf `bereich2-angebot-erstellung-konzept.md`, `-inventur/-verifikation/-bewertung`, `-gewerke-…-playground-abgleich`, `-wp-pv-formular-uebernahme-konzept` und den jetzt lebenden Bausteinen (P1-a `CatalogPriceGuard`, WP-Formular geseedet, F2 v2-Render).
*(Dieses Dokument enthält auch die Angebotsreife-/Qualifizierungs-Analyse; ein separates `…-angebotsreife-qualifizierung-konzept.md` entfällt zugunsten des Kapitels 4 hier.)*

---

## 1. Fachliches Zielbild — die Angebotsreife-Kette
```
Kunde → Objekt → Bedarf/Anfrage → Qualifizierung → Abteilung/Standort → Gewerk → Dienstleistung
→ zuständiges Personal/Rolle → Formular/Tool/Vorlage → Material/Sets/Artikel/Lohn/Zeit
→ Kalkulation → Angebot → Angebotsprüfung → Versand / Vorlage speichern
```
**Regel:** Der Übergang „→ Angebot" ist ein **Gate**: nur ein **angebotsfähiger** Vorgang darf ein Angebot erzeugen. Angebotsfähigkeit = alle **blockierenden Pflichtkriterien** erfüllt (Kapitel 4).

---

## 2. Ist-Code-Befund je Domäne (firsthand)

Legende: 🟢 aktiv verdrahtet · 🟡 vorhanden, unverdrahtet/manuell/leer · 🔴 Legacy/Doppelwahrheit · ⬜ fehlt.

| Domäne | Bestand (Tabelle/Klasse) | Status | Beleg |
|---|---|---|---|
| **Kunde** | `new_leads` (title/name/lastname/firma, full_address/street/postcode/city, phone/email, `customer_no`, `contact_person`) | 🟡 Felder da, **kaum Pflicht** (nur `customer_type`+`branch_id` required) | `NewLeadsController.php:528-550` |
| **Objekt** | `lead_alternative_adds` (Objektadresse, `object_type/building_type/building_condition`, PLZ/lat/lon, Dach-/Heizungs-/Dämmblöcke, `ready_for_offer`, `site_visit_needed`) | 🟡 reich, **keine Pflicht** (bool→nullable-string gelockert) | Migr. `2023_06_13_100802`, `2026_04_08_085421` |
| **Bedarf/Anfrage** | `inquiries` (type: Wartung/Komplettlösung/Montage/Verkauf/Planung/Reklamation/Reparatur), `lead_product_lists.interest`+`realization_time`, `lead_alternative_adds.objective`+`periority` | 🟡 **verteilt**, keine einheitliche Bedarfs-Struktur | `inquiries` Migr.; `NewLeadsController.php:581-584` |
| **Qualifizierung/Phase** | **`lead_stages`** (10: lead/follow_up/accepted/**offer**/**deal**/project/abnahme/completed/archive/junk), gespiegelt auf `lead_product_lists.status`/`stage`/`lead_stage_id` | 🟢 führende Phasen-Wahrheit | `LeadProductList.php:144`; `LeadOverviewController.php:5132` |
| **Abteilung/Standort** | `departments`(16, `branch_id`→branches), `branches`(1), `department_positions`(50); Vorgang: `lead_product_lists.department_id`(52/53), `offers.department_id`(29/29), `offer_details.branch_id` (nur Footer) | 🟢 Abteilung verdrahtet · 🟡 Standort nur indirekt (1 Branch) | `lead_product_lists.php:44`; `offers`:34 |
| **Gewerk** | `article_groups` (15 Gewerke), `lead_product_lists.product_id`→article_groups | 🟢 | `ArticleGroup`; `lead_product_lists.php` |
| **Dienstleistung** | **keine `services`-Tabelle**; = `phase_sections`(13)→`task_phases`(13)→`phase_activities`(49); `lead_product_lists.service_id`→**phase_sections** | 🟢 (Achse = phase_sections) | `InquiryVerificationController.php:206-212`; `LeadProductList.php:222` |
| **Personal/Rolle** | `employees`(51), `positions`(24, `qualification_id`→position_qualifications 24/24), `position_qualifications`(26); Vorgang: `lead_product_lists.employee_id`+`field_employee`(52/53), `teams`(JSON, 0 genutzt) | 🟢 **manuell** je Zeile · 🟡 qualifikationsbasierte Auto-Zuweisung dormant (`customer_suggest_employees`=0, `customer_responsibles`=Stub, `position_qualification_hierarchies`=0) | `InquiryController.php:1085-1087`; `CustomerResponsibleController` (Stub) |
| **Formular/Tool** | `product_formulas` (WP jetzt live, schema_v2), F2-Render/Erfassung, `LeadProductChecklistValue`, Auslegung `Heizlast/Energie/Heizkoerper/*` | 🟢 Engine+WP live · 🟡 Auslegung teils isoliert (BivalenzService/PvProjekt 0 Aufrufer), Tool→`sections` fehlt | (Bereich-2-Inventur) |
| **Sets/Artikel** | `master_sets`(aktiv), `products`/`article_groups`; `product_master_sets` | 🟢 master_sets · 🔴 `product_master_sets` Legacy-Zweitwahrheit | (Abgleich-Doku) |
| **Angebot** | `offers`(status draft/sent/accepted), `offer_folders`(document_status offer/deal, offer_status draft/accepted), `offer_details.sections`(führend)+document_status, `offer_templates`, `offer_product_lists` | 🟢 sections führend · 🔴 `offer_product_lists` Legacy · 🟡 zwei Summier-Engines | (Verifikation) |
| **Kalkulation/Pricing** | `CostingSet`/`MasterSet`, **P1-a `CatalogPriceGuard`** (Katalog-Preis erzwungen) | 🟢 P1-a live · 🟡 zwei Kalk-Welten (CostingSet-Labor ↔ Komponenten-Marge) | `1ad02f4` |
| **Vorlage/Matching** | `offer_templates`(+`processTemplate`/`useTemplate`), `SmartroutingService`(FS-05) | 🟢 Vorlagen-Weg · 🟡 Smartrouting dormant (0 Regeln); **keine Keywords/Tags** | (Abgleich-Doku) |

**Kern-Befund Angebotsfähigkeit:** Es existiert **kein Reife-/Pflichtdaten-Gate vor der Angebotserstellung** — alle drei Offer-Pfade validieren **nur FK-Existenz** (`OffersController::store:82`, `OfferWizardController::createOffer:336`, OfferFolder). Grep `angebotsfaehig|angebotsreif|pflichtdaten|vollstaendig` = kein Backend-Treffer. **Aber zwei wiederverwendbare Fragmente:**
- **`InquiryVerificationController.php:216-238`** — prüft je `lead_product_list`-Zeile die **Existenz** von Produkt/`service_id`(Dienstleistung)/`department_id`(Abteilung)/`employee_id`(Innendienst) → sammelt `$missing`. **Das ist bereits eine (inquiry-seitige) Angebotsreife-Prüfung** und der beste Wiederverwendungs-Kern.
- **`lead_alternative_adds.ready_for_offer`** (Flag „Angebotsbereit", gesetzt beim Vor-Ort-Termin) — **nur Anzeige, keine Sperre**; `chk_angebotsreif`/„Planungsbereit" existiert nur als **UI-Chip in `layouts/test.blade.php`** (Mockup, nicht verdrahtet).
- Das **einzige echte Gate** ist downstream: `LeadOverviewController::requiresAcceptedOfferBeforeEnteringDeal():5374` (Angebot muss angenommen sein, bevor „Auftrag") — bewusst überspringbar (`moved_without_offer_acceptance`).

---

## 3. Soll-Workflow der 3 Wege (alle → `offer_details.sections` + P1-a)

Gemeinsamer Kopf: **Vorgang muss angebotsfähig sein (Kapitel 4)** — sonst ist „Angebot erstellen" gesperrt.

- **Weg 1 — Vorlage:** angebotsfähig → Vorlage-Picker (`OfferTemplatePickerController::useTemplate`, vorhanden) → `template.sections` → Wizard → prüfen → speichern. Preise via P1-a.
- **Weg 2 — Manuell:** angebotsfähig → Wizard (`OfferWizardController`) → Sets/Artikel aus `master_sets`/`products` → `sections` → Kalkulation → speichern.
- **Weg 3 — Tool/Formular:** angebotsfähig → Formular/Auslegung (WP live, PV folgt) → **Tool→`sections`-Adapter (fehlt, Vorbild `HeizkoerperController::uebernehmen`)** → Wizard → prüfen → speichern.
Alle: optional „als Vorlage speichern" (`processTemplate`, + Scrub §14 Angebots-Konzept).

---

## 4. KAPITEL — Angebotsreife, Fortschritt und Aufgabenstatus

### 4.1 Reifegrad-Modell — gemeinsamer Kriterien-Katalog, gewerk-variabel
**Ein Kriterien-Katalog, eine Struktur; welche Kriterien gelten + ihr Gewicht ist je Gewerk/Dienstleistung parametrierbar.** Ein Kriterium (konzeptionell):
```
kriterium = { key, label, phase, gewicht, blocker(bool), quelle(prüfbar aus welchem Feld/Service), gilt_fuer_gewerk[] }
```
Vorschlag Basis-Katalog (Quelle = **vorhandene** Felder/Services):

| key | Kriterium | blocker | Quelle (Ist-Bestand) |
|---|---|---|---|
| `kunde_vorhanden` | Kunde mit Name+Kontakt | ja | `new_leads.name/lastname` + (`phone` ∨ `email`) |
| `objekt_adresse` | Objektadresse vorhanden | ja | `lead_alternative_adds.full_address`/`street`+`postcode`+`city` |
| `objekt_technik_basis` | techn. Basis je Gewerk | gewerk-abh. | `lead_alternative_adds.*` (WP: `heating_type`,PLZ,`living_space`; PV: `roof_type`,`roof_direction`) |
| `bedarf_erfasst` | Bedarf/Ziel erfasst | ja | `inquiries.type` ∨ `lead_product_lists.interest` ∨ `lead_alternative_adds.objective` |
| `gewerk_zugeordnet` | Gewerk gesetzt | ja | `lead_product_lists.product_id`→article_groups |
| `dienstleistung_zugeordnet` | Dienstleistung gesetzt | ja | `lead_product_lists.service_id`→phase_sections *(InquiryVerification prüft es schon)* |
| `verantwortung_zugeordnet` | Abteilung + Innendienst | ja | `lead_product_lists.department_id`+`employee_id` *(InquiryVerification)* |
| `pflichtformular_ausgefuellt` | Pflicht-`product_formula` ausgefüllt | gewerk-abh. | `LeadProductChecklistValue.filled_values` vs. Pflichtfelder (F2-Fortschritt) |
| `technische_pruefung` | Auslegung/Vorwert vorhanden | gewerk-abh. | `Anforderungsprofil`-Werte ∨ Auslegungs-Ergebnis (WP: Heizlast; PV: kWp) |
| `preisgrundlage` | Katalog-Preisgrundlage | ja | Katalog-Referenz (`component_id`) + P1-a fähig |
| `angebotsentwurf` | `sections`-Entwurf existiert | (Folge) | `offer_details.sections` |
| `angebotspruefung` | Prüfung/Freigabe erfolgt | (Folge) | Freigabe-Status (⬜ heute nicht vorhanden) |

### 4.2 Prozentberechnung (real, nicht kosmetisch)
```
reife_% = 100 × Σ(gewicht der ERFÜLLTEN, anwendbaren Kriterien) / Σ(gewicht ALLER anwendbaren Kriterien)
angebotsfähig  ⇔  ALLE blocker=true-Kriterien erfüllt   (unabhängig von %; % kann <100 bei offenen optionalen sein)
```
- **Anwendbar** = `gilt_fuer_gewerk` matcht das Gewerk des Vorgangs (WP≠PV≠Service). → gleiche Struktur, andere Kriterienmenge/Gewichte.
- **Jede Prozentzahl ist aus einem echten Feld-/Service-Check abgeleitet** (Spalte „Quelle"), nie ein Anzeige-Konstante.

### 4.3 Aufgabenstatus je Vorgang
Je Vorgang (= `lead_product_list`-Zeile) ableitbar aus dem Katalog:
- **Pflichtaufgaben gesamt** = Anzahl anwendbarer Kriterien mit `blocker=true` ∪ Pflichtformular-Pflichtfelder.
- **erledigt / offen** = erfüllt / nicht erfüllt.
- **blockierend** = offene `blocker=true`-Kriterien (sperren „Angebot erstellen").
- **optional** = anwendbare Kriterien mit `blocker=false`.
- **nächster empfohlener Schritt** = das offene Kriterium mit höchster Priorität (Blocker vor optional; Phase-Reihenfolge).
- **Status:** `unvollständig` (Pflicht offen, nicht blockiert) · `blockiert` (harter Blocker offen) · `in Prüfung` (technische Prüfung/Auslegung offen) · `angebotsfähig` (alle Blocker erfüllt) · `Angebot erstellt` (`offer_details.sections` da) · `versendet` (`offers.status=sent`).

### 4.4 Blockerlogik (Gate)
„Angebot erstellen/versenden" **gesperrt**, solange ein blockierender Pflichtpunkt fehlt: Kundendaten · Objektadresse · Gewerk · Dienstleistung · Pflichtformular · technische Prüfung · **Preisgrundlage** · verantwortliche Person/Abteilung · offene Plausibilitätswarnung (`PlausibilityService`, heute dormant → optional). Reuse: die `$missing`-Logik aus `InquiryVerificationController:216-238` ist der Kern; erweitert um Kunde/Objekt/Bedarf/Formular/Technik/Preis.

### 4.5 Unterschiedliche Kriterien je Gewerk — Abbildung im Bestand
| Mechanismus | trägt gewerk-spezifische Kriterien | Ist-Bestand |
|---|---|---|
| **`product_formulas`** (je `article_group`) | welche Pflichtfelder das Gewerk braucht | WP live; F2-Fortschritt liefert Fill-% |
| **`product_formula_routing_rules`** (Smartrouting) | welches Formular je Gewerk/Objekt Pflicht | 🟡 0 Regeln (dormant) |
| **`phase_sections`** (Dienstleistung je Gewerk) | welche Leistung/Phasen | 🟢 13 |
| **`Anforderungsprofil`/`SchluesselRegistry`** | welche techn. Operanden Pflicht | 🟡 nur Heizlast-Kern |
| **Gewerk-Auslegung** (WP Heizlast, PV kWp) | technische-Prüfung-Kriterium | 🟡 Services da, teils isoliert |
→ **Gemeinsame Struktur** (Katalog) + **gewerk-variable Menge** (aus product_formulas/routing/phase_sections/Anforderungsprofil). Keine Gewerk-Sonderwelten.

### 4.6 Backend-Datenmodell
- **On-the-fly berechnen** (read-only `AngebotsreifeService::fuer(LeadProductList): Reifebericht`), NICHT als neue persistierte Status-Spalte → **keine zweite Statuswahrheit** (führend bleiben `new_leads`/`lead_alternative_adds`/`lead_product_lists`/`product_formulas`/`offer_details`).
- **Optionaler Cache** nur als abgeleiteter Snapshot (für Filter/Listen-Performance), stets aus den Quellen neu ableitbar, nie eigenständige Wahrheit.
- **Schnittstelle fürs Frontend:** ein read-only Endpoint `GET …/angebotsreife/{lead_product_list}` → `{ percent, status, kriterien[], blocker[], aufgaben{…}, naechster_schritt }`.
- Reuse: `InquiryVerificationController`-Missing-Logik in den Service extrahieren (nicht duplizieren); `lead_stages` als Phasen-Achse; F2-Formular-Fortschritt für `pflichtformular_ausgefuellt`.

### 4.7 Frontend-Darstellung (Konzept)
- **Fortschrittsbalken %** (aus 4.2) + **Checkliste erledigt/offen** je Kriterium (Reuse der F2-Checklisten-Optik).
- **Blocker-Hinweise** prominent; **„Nächster Schritt"** sichtbar.
- **Button „Angebot erstellen" erst aktiv, wenn `status=angebotsfähig`** (sonst disabled + Blocker-Tooltip).
- **Filter** in der Lead-/Vorgangsliste: angebotsfähig / blockiert / in Prüfung / unvollständig.
- **Ansicht je Lead → je Gewerk/Dienstleistung** (Reife ist pro `lead_product_list`-Zeile, nicht pro Lead global).

### 4.8 Vorhandener wiederverwendbarer Code
`InquiryVerificationController:216-238` (Missing-Gate) · `lead_stages`+`LeadProductList::deriveLeadStageId` (Phase) · **F2** `checklist.blade.php` (Fortschritts-%-Muster) + `LeadProductChecklistValue` (Formular-Antworten) · `product_formulas`/`FormSchemaValidator` (Pflichtfelder) · `Anforderungsprofil` (Bedarf/Technik) · `SmartroutingService` (Formular-je-Gewerk, dormant) · `CatalogPriceGuard` (Preisgrundlage) · `FollowUpCreator`/`personal_tasks` (Aufgaben/Follow-ups) · `ready_for_offer`/`site_visit_needed` (Objekt-Flags).

### 4.9 Risiken bei falscher Prozentberechnung
- **Falsches „angebotsfähig"** → Angebot auf unvollständigen Daten (der eigentliche Schaden). Mitigation: Blocker = harte Feld-/Service-Checks, konservativ.
- **Kosmetische % ohne echten Bezug** → Fehlvertrauen. Mitigation: jedes % aus „Quelle"-Feld ableiten, testbar.
- **Zweite Statuswahrheit** (persistierter Reife-Status driftet von Daten) → on-the-fly + nur abgeleiteter Cache.
- **Gewerk-Kriterien-Drift** (WP-Regeln ≠ PV-Regeln unkontrolliert) → ein Katalog, Kriterien datengetrieben aus product_formulas/phase_sections.

---

## 5. Angebotsfähigkeit — Mindestdaten & Blocker (konkret)
**Mindestdaten (blockierend, dürfen NICHT fehlen):** Kunde (Name+Kontakt) · Objektadresse · Bedarf/Ziel · Gewerk · Dienstleistung · Abteilung+verantwortliche Person · Preisgrundlage (Katalog) · gewerk-spezifisches Pflichtformular + technische Prüfung (WP: Heizlast/PLZ; PV: Dach/Verbrauch).
**Später nachreichbar (optional, nicht blockierend):** Feindaten (exakte U-Werte), Fotos, zweite Ansprechpartner, Vorlage-Metadaten, Feinaufmaß.
**Blockiert Angebotserstellung:** jeder offene Blocker oben.

---

## 6. Konkrete Brüche (Ist)
- **Kein Reife-Gate vor Angebot** (nur FK-Existenz-Validierung) — der zentrale Bruch.
- **Kunde/Objekt ohne Pflichtdaten** (God-Table, alles nullable) → Reife-Kriterien haben unsaubere Quelle (Bereich-1-Thema).
- **Auslegung→`sections`-Adapter fehlt** (nur Heizkörper→Measurement) → `technische_pruefung`+`angebotsentwurf` nicht durchgängig.
- **`angebotspruefung`/Freigabe-Status fehlt** (⬜) → Reife-Endstufe nicht abbildbar.
- **Doppelwahrheiten** `offer_product_lists`/zwei Summier-Engines/`product_master_sets` (Preis-/Positions-Wahrheit) — Reife „Preisgrundlage" muss auf `sections`+P1-a zeigen.
- **Personal-Auto-Zuweisung dormant** → „verantwortliche Person" bleibt manuell (akzeptabel als Pflichtfeld).

---

## 7. Zeitpunkt & Abhängigkeiten — Prüfung deiner Vermutung (am Code)
Deine Vermutung: *P1-a zuerst → Angebotsreife (Konzept+kleines Paket) → WP/PV-Formular → Vorlagen/Matching.*

| These | Befund | Verdikt |
|---|---|---|
| „P1-a zuerst fertigstellen" | **P1-a ist bereits fertig + committet (`1ad02f4`)**; unabhängig von der Reife-Kette (Guard beim Speichern) | **erfüllt** (kein „davor" nötig) |
| „Danach Angebotsreife als Konzept + kleines Workflow-Paket" | Konzept = dieses Doc; kleines Paket = read-only `AngebotsreifeService` + WP-Reife-Panel (on-the-fly, keine 2. Wahrheit) | **bestätigt** |
| „Danach WP/PV-Formularübernahme" | WP-Formular ist **schon live** → Reife-Panel kann WP sofort nutzen; PV folgt und liefert PV-Pflichtkriterien | **bestätigt, teils vorgezogen** |
| „Danach Vorlagen/Matching" | braucht Bibliothek+Metadaten; nach Reife/Formularen | **bestätigt** |

**Wichtige Präzisierung (bereichsübergreifend):** Das Reife-**Modell** ist Bereich 2, aber seine **Datenquellen** sind teils **Bereich 1** (Kunde/Objekt/Qualifizierung = `new_leads`/`lead_alternative_adds`, God-Table, ohne Pflicht) und **Bereich 7** (Personal/Org). **Risiko bei zu frühem Vollbau:** eine harte Reife-Kette auf unsauberen Bereich-1-Ankern zementiert die God-Table. **Empfehlung:** Reife **on-the-fly** auf den vorhandenen Feldern rechnen (kein Bereich-1-Umbau, kein Pflichtfeld-Erzwingen im Alt-Kern jetzt) — die Pflichtfeld-Härtung in `new_leads`/`lead_alternative_adds` kommt später **mit Bereich 1**.

---

## 8. Priorisierte Arbeitspaket-Landkarte (jedes: Backend + Frontend + Testpfad)
| Prio | Paket | Backend | Frontend | Testpfad (browser-prüfbar) |
|---|---|---|---|---|
| 1 | **Reife-Service + WP-Reife-Panel (read-only)** | `AngebotsreifeService::fuer(lead_product_list)` (on-the-fly, Katalog aus 4.1, Reuse InquiryVerification+F2), read-only Endpoint | Panel je Gewerkzeile: %-Balken, Checkliste, Blocker, „nächster Schritt", Button-Gate (disabled bis angebotsfähig) | WP-Lead: Panel zeigt korrekte %/Blocker; Button erst aktiv, wenn alle Blocker erfüllt |
| 2 | **Filter angebotsfähig/blockiert/in Prüfung/unvollständig** | abgeleiteter Cache-/Query-Layer (aus Reife-Service) | Listenfilter | Liste filtert korrekt |
| 3 | **Tool→`sections`-Adapter (WP)** | Adapter Auslegung→`sections` (Vorbild Heizkörper), P1-a-Preise | „als Entwurf übernehmen" | WP-Auslegung → prüfbarer Angebotsentwurf |
| 4 | **PV-Formular + PV-Reifekriterien** | PV `product_formula` (wie WP-Pilot) + PV-Kriterien | PV-Formular/-Panel | PV-Lead: Reife/Formular |
| 5 | **`angebotspruefung`/Freigabe-Status** | Freigabe-Feld/Guard am Angebot | Prüf-/Freigabe-UI | Angebot „geprüft" vor Versand |
| 6 | **Legacy-Konsolidierung** (`offer_product_lists`/Engine-Doppelung/`product_master_sets`) | Stilllegen/Adapter (eigene Pakete, Variante B) | — | eine Positions-/Preis-Wahrheit |
| — | **Bereich-1-Pflichtdaten-Härtung** (Kunde/Objekt) | mit Bereich 1 | — | (bereichsübergreifend, später) |

---

## 9. Erstes empfohlenes Umsetzungspaket (Vorschlag, kein Bau)
**Paket 1 — WP-Angebotsreife-Panel (read-only, on-the-fly).** Kleinster Schnitt mit sofort sichtbarem Nutzen, auf den jetzt lebenden Bausteinen (WP-Formular, F2, P1-a, InquiryVerification-Missing-Logik):
- **Backend:** neuer read-only `AngebotsreifeService` (Kriterien-Katalog 4.1, on-the-fly, keine Persistenz/keine 2. Wahrheit) + ein read-only Endpoint; Reuse statt Neubau.
- **Frontend:** ein Reife-Panel je WP-Gewerkzeile (%-Balken + Checkliste + Blocker + „nächster Schritt") und **Button „Angebot erstellen" disabled bis angebotsfähig**.
- **Testpfad:** WP-Lead mit Teil-Daten → Panel zeigt Blocker + korrektes %; Daten vervollständigen → angebotsfähig → Button aktiv.
- **Rückfall:** rein additiv/read-only (kein Bestandseingriff); Feature-Flag; Variante A.
*(WP zuerst, weil WP-Formular + WP-Auslegung schon da sind; PV analog in Paket 4.)*

---

## 10. Offene Entscheidungen an Yama
1. **Reife on-the-fly** (empfohlen, keine 2. Wahrheit) vs. persistierter Reife-Status — bestätigst du on-the-fly + nur abgeleiteten Cache?
2. **Blocker-Set bestätigen** (Kapitel 5): sind Kunde/Objektadresse/Bedarf/Gewerk/Dienstleistung/Verantwortung/Preisgrundlage/Pflichtformular/Technik die harten Blocker?
3. **Pflichtdaten NICHT im Alt-Kern erzwingen** (Reife rechnet nur, härtet Bereich-1-Felder später) — einverstanden?
4. **Gewerk-Kriterien-Quelle:** Katalog datengetrieben aus `product_formulas`+`phase_sections`+`SmartroutingService` (Regeln füttern) — jetzt konzeptionell, Bau später?
5. **`angebotspruefung`/Freigabe** als eigener Status vor Versand — jetzt vorsehen oder später?
6. **Startpaket:** Paket 1 (WP-Reife-Panel) als erster Bau nach Freigabe?

---

## 11. Risiken & Rückfall (Zusammenfassung)
Zweite Status-/Positions-/Preiswahrheit (→ on-the-fly, `sections`+P1-a) · Bau auf God-Table-Ankern (→ nicht härten, nur rechnen) · Scope-Ausufern (→ kleine browser-prüfbare Pakete) · falsche % (→ echte Feld-Checks) · bereichsübergreifende Nähte (Bereich 1/4/5/7 markieren, nicht mitbauen). Jedes Bau-Paket: Variante-B-Archiv wo Bestandsdatei berührt, Testpfad, getrennter Evaluator, Yama-Freigabe.

---

## Evaluator-Notiz
- **Belegt (firsthand, `datei:zeile`/Live-DB):** Status-/Phasen-Landkarte; **kein** Reife-Gate vor Angebot (nur FK-Validierung); Reuse-Fragment `InquiryVerificationController:216-238`; `ready_for_offer`=Anzeige; Org/Personal/Dienstleistung-Zuordnung (manuell, qualifikationsbasiert dormant); Kunde/Objekt-Felder ohne Pflicht; P1-a fertig.
- **Konzept, nicht Umsetzung:** Kriterien-Katalog, %-Formel, Reife-Service, Panel, Adapter — **Vorschläge**; jede Umsetzung = eigenes freigegebenes Paket (Backend+Frontend+Testpfad+Rückfall).
- **Vermutung geprüft:** bestätigt mit Präzisierung (P1-a bereits fertig; Reife-Datenquellen bereichsübergreifend → on-the-fly, kein Bereich-1-Umbau jetzt).
- **Nicht gemacht (korrekt):** kein Bau/Refactor/Import/Migration/Automatisierung/Commit; keine zweite Wahrheit; keine Angebotslogik umgebaut.

---

*Nächster Schritt laut Auftrag: **STOPP.** Yama prüft Konzept + Kapitel 4 + offene Fragen (§10). Erst danach — auf separate Freigabe — Paket 1 (WP-Angebotsreife-Panel), read-only/additiv.*
