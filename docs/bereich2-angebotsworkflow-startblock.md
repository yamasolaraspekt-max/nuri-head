# Planner-Startblock — Bereich 2: Angebotsworkflow / Angebotsreife („Wie komme ich zum Angebot?")

**Stand:** 2026-07-12 · **read-only Planungs-Startblock** · **kein Bau/Import/Refactor/Migration/Automatisierung.**
**Zweck:** Die Frage *„Wie komme ich zum Angebot?"* **fachlich und technisch als durchgängigen Workflow** aufbauen — den End-to-End-Pfad Lead→Angebot kartieren, die **Angebotsreife-Lücken** benennen und eine **Arbeitspaket-Landkarte** ableiten. **Kein Bau; erst Analyse/Konzept.**
**Methode:** `systemoptimierung-fahrplan.md` Schritt ② **Workflow bestimmen** für Bereich 2 (Schritt ① Konzept liegt vor: `docs/bereich2-angebot-erstellung-konzept.md`). Danach ③ Verknüpfung, ④ Automatisierung — jeweils eigene, freigegebene Runden.
**Geltung:** CLAUDE.md (agents/05 Pflicht-Fachagenten für Workflow/Architektur/Frontend), Ziel-Wahrheiten Bereich 2 (Anker=Anforderungsprofil an Objekt/Gewerk · Positionen=`offer_details.sections` · Preis=P1-a), „eine Wahrheit je Sachverhalt", `rueckfall-archiv-regeln.md`.

---

## 1. Domäne / Kapitel
- **Bereich 2 — Angebot, Auslegung & Kalkulation.** Teilpaket: **Angebotsworkflow / Angebotsreife** (der durchgängige Nutzerpfad zum versendbaren Angebot).
- **Rolle:** Planner (read-only). Generator/Evaluator erst nach separater Bau-Freigabe.

## 2. Startpunkt (bereits Vorhandenes)
**Konzept-/Inventur-Basis:** `bereich2-angebot-erstellung-konzept.md` (3 Wege → eine Wahrheit), `bereich2-angebot-auslegung-inventur.md` + `-verifikation.md` + `-bewertung.md`, `bereich2-gewerke-dienstleistungen-formulare-playground-abgleich.md`, `bereich2-wp-pv-formular-uebernahme-konzept.md`, `bereich2-folgeposten.md`.
**Jetzt lebende Bausteine (verankert):** P1-a Preis-Wahrheit (`CatalogPriceGuard`, `1ad02f4`) · WP-Bedarfsformular als v2-`product_formula` (geseedet, live) · F2 v2-Render/Erfassung (`a412dad`) · `offer_details.sections` als Positions-Wahrheit · Vorlagen-Weg (`OfferTemplatePickerController::useTemplate`) · Auslegungs-Services WP/PV (teils isoliert).

## 3. Ziel (+ Nicht-Ziele) der geplanten Analyse-Runde
**Ziel:** den **Soll-Workflow Lead→Angebot** je der 3 Wege durchgängig beschreiben (fachlich = Nutzerpfad/Entscheidungen, technisch = Controller/Service/Tabelle/View), die **realen Brüche** und die **Angebotsreife-Kriterien** benennen, und eine **priorisierte Arbeitspaket-Landkarte** (mit prüfbarem Frontend-Pfad je Paket) ableiten. **Kein** Bau.
**Nicht-Ziele:** keine Umsetzung/kein Adapter-Bau · keine PV-Verdrahtung · keine Bivalenz-Verdrahtung · keine `SchluesselRegistry`-Erweiterung · keine Migration · keine zweite Angebots-/Formularwelt · kein Prod-Seed · keine Automatisierung.

## 4. Was gelesen/geprüft werden muss (Leseliste, firsthand)
- **Angebots-Kette:** `Customer/Offer/OfferController` (save-document/processOffer), `OfferWizardController` (Set-/Artikel-Auswahl), `OfferFolderController` (Ordner/Status, `changeDocumentStatus` = Angebot→Deal), `OfferDetailsController`, `DealMaterialListController`; Tabellen `offers`/`offer_folders`/`offer_details(sections)`/`offer_templates`/`offer_product_lists`.
- **Eingang/Anker:** `new_leads`/`lead_alternative_adds`/`lead_product_lists` (Kunde/Objekt/Gewerk) + `Anforderungsprofil`/`SchluesselRegistry` (Bedarf) + `LeadProductChecklistValue` (Formular-Antworten, jetzt v2-fähig).
- **Auslegung (Tool-Weg):** `Heizlast/*` (HeizlastProjektService, WaermepumpenMatchService, **BivalenzService 0 Aufrufer**), `Energie/*` (InverterSizing, PvgisErtrag, **PvProjekt 0 Aufrufer**), `HeizkoerperController::uebernehmen` (einziges Tool→Positionen-Muster, → `deal_measurement_items`).
- **Preis/Kalkulation:** `CatalogPriceGuard` (P1-a), `MasterSet`/`CostingSet`, die zwei Summier-Engines (Bewertung Punkt 3).
- **Frontend:** `resources/views/admin/offer/configuration/offer/config.blade.php` (Wizard), `folder-show.blade.php`, `checklists/checklist.blade.php` (F2).
- **Übergabe:** Angebot→Auftrag (`deals`), Angebot→Vorlage (`processTemplate`).

## 5. Expertenrollen (agents/05 — Pflicht bei Workflow/Architektur/Frontend)
- **Workflow-Agent:** der reale Nutzerpfad je Weg (Vorlage/manuell/Tool) von Lead bis versendbares Angebot; Entscheidungs-/Bestätigungspunkte (Operanden-Gate).
- **Konzeption-Agent:** Angebotsreife-Kriterien (was macht ein Angebot „fertig/versendbar": Pflichtdaten, Preis-Wahrheit, Positionen, Rechtstexte).
- **Architektur-Agent:** eine Wahrheit (`sections`), Anker (Objekt/Gewerk/Anforderungsprofil), keine zweite Angebotswelt; wo Adapter statt Neubau.
- **Frontend-Design-Agent:** ein Einstieg „Angebot erstellen" mit 3 Startpunkten → einheitlicher Entwurfs-Screen; Browser-Prüfbarkeit je Schritt.
- **Energie-Fach:** Tool→Angebot (WP/PV) — welche Auslegungswerte in Positionen/Mengen/Lohn münden.

## 6. Vorgehen Schritt für Schritt (der Analyse-Runde, nach Freigabe)
1. **Ist-Workflow je Weg** firsthand nachzeichnen (Route→Controller→Service→Tabelle→View), inkl. wo er heute endet/bricht.
2. **Soll-Workflow je Weg** definieren, alle mündend in `offer_details.sections` + P1-a.
3. **Angebotsreife-Kriterien** festlegen (Definition „versendbares Angebot") + Reife-Ampel je Weg.
4. **Bruch-/Lücken-Liste** mit Schweregrad (z. B. Auslegung→`sections`-Adapter fehlt; Anforderungsprofil-Anker unverdrahtet; duales Angebots-Modell; Angebot→Auftrag nur Preis).
5. **Arbeitspaket-Landkarte:** kleine, additive Pakete mit Backend+Frontend+Testpfad + prüfbarem Browser-Schritt, priorisiert; je Paket Rückfallpfad-Hinweis.
6. **Erster empfohlener Baustein** benennen (belegt) — voraussichtlich der schmalste Weg zu einem im Browser sichtbaren „durchgängigen" Angebot.

## 7. Kritische Fragen (in der Analyse zu beantworten)
- Woran hängt der Angebots-**Eingang** (Kunde/Objekt/Gewerk) und wie kommt der **Bedarf** (Formular/Anforderungsprofil) hinein?
- Wie wird aus **Tool-/Formular-Ergebnis** ein `sections`-Entwurf (Adapter-Muster wie `HeizkoerperController::uebernehmen`)?
- Wo entsteht **eine** Angebots-Wahrheit (`sections`) und wo drohen **zweite Wahrheiten** (`offer_product_lists`, zwei Summier-Engines, JS-Preise)?
- Was ist **Angebotsreife** (Pflichtdaten-Gate, Preis-Wahrheit P1-a, Rechtstexte) und wo steht jeder Weg?
- Welche vorhandenen, aber **isolierten** Bausteine (BivalenzService, PvProjekt, Anforderungsprofil-Adapter) sind Teil des Soll-Workflows (verdrahten statt neu bauen)?
- Welche Schritte dürfen **automatisch** laufen, welche nur **Vorschlag+Bestätigung**, welche **nie** automatisch (Versand)?

## 8. Risiken (vorab benannt)
- **Zweite Angebots-/Rechenwahrheit** (Legacy `offer_product_lists`, Engine-Doppelung, JS-Preise) → Workflow muss auf `sections`+P1-a konvergieren.
- **Anker-Losigkeit** der Auslegung (heute stand-alone) → Workflow muss an Objekt/Gewerk/Anforderungsprofil verankern.
- **Scope-Ausufern:** die Kette ist groß → strikt in kleine, browser-prüfbare Pakete schneiden, nicht „alles auf einmal".
- **Übergreifende Bereiche:** Angebot→Auftrag berührt Bereich 4/5; sauber als Naht markieren, nicht mitbauen.

## 9. Ergebnisdokument
`docs/bereich2-angebotsworkflow-konzept.md` (in der Analyse-Runde nach Freigabe) — mit: Ist/Soll-Workflow je Weg, Angebotsreife-Kriterien + Reife-Ampel, Bruch-/Lücken-Liste, priorisierte Arbeitspaket-Landkarte (Backend+Frontend+Testpfad je Paket), erster empfohlener Baustein, offene Entscheidungen an Yama, Risiken/Rückfall.

## 10. Stop-Kriterium + Yama-Abnahmepunkt
- **Dieser Startblock** liegt vor → **STOPP.**
- **Yama-Abnahmepunkt:** Yama gibt (a) den Start der **Analyse-/Workflow-Konzept-Runde** frei (read-only, Ergebnisdokument oben) und **danach separat** (b) ein etwaiges erstes **Arbeitspaket**. **Kein Bau ohne neue Freigabe.**

---

*Nächster Schritt laut Auftrag: **STOPP.** Kein Bau. Ich warte auf dein Go für die Angebotsworkflow-Analyse-/Konzept-Runde (Ergebnis: `docs/bereich2-angebotsworkflow-konzept.md`).*
