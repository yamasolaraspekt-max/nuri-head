# Paket 4 — Angebotsworkflow End-to-End-MVP — Plan

> **Status:** Arbeitsdokument (autonomer 3h-Auftrag 2026-07-13). Backend + Frontend zusammen. Read-only-first; kein Push ohne separate Freigabe.
> **Ziel:** Aus dem vorhandenen Bereich-2-Fundament einen **browserprüfbaren, geführten** WP-Angebotsweg ableiten und den ersten sicheren Slice (4a — Cockpit) umsetzen. **Keine zweite Wahrheit, kein Auto-Preisanker, kein Schreiben in `offer_details.sections`.**
> **Führende Wahrheiten:** Auslegung → `Anforderungsprofil` · Positionen → `offer_details.sections` · Preis → `CatalogPriceGuard`/`component_id` (gespeist aus OMD/IDS→`distributor_prices`). Reife on-the-fly aus `OfferReadinessService`.

---

## A. Aktueller Stand der Bausteine

| Baustein | Status | Datei / Route / Service | browserprüfbar? | offen / blockiert |
|---|---|---|---|---|
| Bedarf / WP-Formular | **fertig** | `ProductFormula` (product_id=2, v2), `checklist.blade` | ja | — |
| Formularspeicherung | **fertig** | `LeadProductChecklistValue`, `LeadProductChecklistValueController` (F2) | ja | — |
| Formular-Calc (eval-frei) | **fertig** | `FormulaEvaluationService`, `ProductFormulaController::evaluate`, `VisibleIfService` | ja | — |
| Angebotsreife (on-the-fly) | **fertig** | `OfferReadinessService` · `offers.angebotsreife.panel/json/index` | ja | — |
| Reife-Gate (Erstellpfade) | **fertig** | `OfferReadinessGate` in Wizard/store/sync/processOffer/useTemplate | ja (422) | — |
| useTemplate-Absicherung | **fertig** | H1 (folder_name) · H2 (alternative_id exists+Zugehörigkeit) · H3 (Non-WP+null→422) | ja | — |
| Auslegungs-Vorschau (read-only) | **fertig** | `AuslegungVorschlagService` · `offers.auslegung.vorschau` | ja | Positionen **preislos** (Katalog-Anker fehlt) |
| WP-Katalog-Matching (read-only) | **fertig** | `WpKatalogMatchingService` · `offers.wp-katalog-matching` (liest alle WP-Sets) | ja | Schnittmenge **0** (Preis∩Specs disjunkt) |
| Objektprofil-UX (Tab-Block) | **fertig** | UX-2 `customer_object_profile.blade` (Reife/Auslegung/Preis) | ja | — |
| Kanban-UX (Badge/Filter) | **fertig** | `kanban.blade` `kb-reife-*` + Batch `offers.angebotsreife.index` | ja | — |
| OfferWizard / OfferController | **vorhanden (Bestand)** | `offers.wizard` (GET), `OfferWizardController`, `OfferController`, `OffersController` | ja | Erstellpfade gegated; **kein** geführter Einstieg |
| Angebotspositionen | **Wahrheit steht** | `offer_details.sections` (Schreiber `processOffer`) · `calculateOfferSections` (Totals) | ja (Wizard) | Positionen manuell/Set/Template; **kein** Auslegung→sections-Adapter |
| Vorlagen | **fertig (Bestand)** | `OfferTemplatePickerController::useTemplate` (gegated) | ja | — |
| Preisfähigkeit / P1-a | **fertig** | `CatalogPriceGuard` (component_id → EK/VK, Marker) | ja | greift nur bei bepreisten Set-Komponenten |
| OMD/IDS-Preisstrang | **gebaut, inert** | `SupplierConnection`/`OmdClient`/`distributor_prices`/`supplier_article_map` | teils | **blockiert:** 0 Connections, spec-Produkte ohne Artikelnr/Preis (P3-d2b) |
| **Cockpit / Workflow-Führung** | **FEHLT** (Paket 4a) | — | — | genau das baut 4a |

**Kernbefund:** Alle Einzel-Bausteine existieren und sind read-only prüfbar. Was fehlt, ist die **Führung** — ein zentraler Ort je WP-Gewerkzeile, der Stand/Fortschritt/nächste Aktion zeigt und die verstreuten Panels bündelt. **Technisch** ist der Weg weit; **kaufmännisch** (Preis) am OMD/IDS-Strang blockiert.

---

## B. End-to-End-Sollworkflow (WP)
```
1.  Lead/Kunde wählen                     (Bereich 1)
2.  Objekt/Gewerk wählen (lead_product_lists, WP=2)
3.  Bedarf erfassen        → WP-Formular (LeadProductChecklistValue)
4.  Angebotsreife prüfen   → OfferReadinessService (Ampel/Aufgaben/Blocker)
      └─ Gate: harte Blocker offen → Angebot gesperrt (422)
5.  Auslegung prüfen/erzeugen → Anforderungsprofil (Heizlast/Bivalenz)
6.  Vorschlag/Ranking sehen  → AuslegungVorschlagService (proposed sections, preislos)
7.  Preisfähigkeit prüfen    → WpKatalogMatchingService (Schnittmenge/Kandidaten)
8.  Angebot erstellen:
      Weg 1 Vorlage  (useTemplate, gegated)
      Weg 2 manuell aus Sets/Artikeln (Wizard config.blade → sections)
      Weg 3 Tool/Auslegung (später: Auslegung→sections-Adapter, P3-e)
9.  Angebot speichern        → offer_details.sections (processOffer, CatalogPriceGuard, Totals)
10. Optional als Vorlage speichern (processTemplate)
11. Versenden / Übergabe
```
**Trennung:** Schritte 3–6 = **technische Reife** (heute weit machbar). Schritt 7 + bepreiste 8/9 = **kaufmännische Preisfähigkeit** (heute OMD/IDS-blockiert → Positionen preislos/markiert).

---

## C. Fortschrittslogik (Prozessreife, nicht Codemenge)

**Gesamt = 85 % technische Prozessreife + 15 % kaufmännische Preisfähigkeit** (getrennt ausgewiesen, keine Vermischung):
- **technik_prozent** = `OfferReadinessService.percent` (0–100 aus echten Pflicht-Kriterien: Kunde, Objekt, Bedarf, Zuständigkeit, WP-Formular, Auslegung, Preisgrundlage-Vorhandensein).
- **preis_prozent** = Preisfähigkeit: `WpKatalogMatchingService.schnittmenge > 0 ? 100 : 0`.
- **gesamt_prozent** = `round(0.85·technik + 0.15·preis)`.
  → Ohne Preisfähigkeit **max. 85 %** (Technik voll, Preis rot). Mit Preis 100 %.

**Etappen-Bänder (Anzeige):** 0–20 Datenanker/Formular · 20–40 Reife/Gate · 40–60 Auslegung sichtbar · 60–75 Vorschlag/Ranking · 75–90 Angebotserstellung geführt · 90–100 speicherbar + Vorlage + Preisfähigkeit. Die Prozentzahl kommt aus der obigen Formel; die Bänder ordnen sie visuell ein.

**Heutiger Ist-Wert (typisch, WP mit Profil + Formular, ohne Preis):** technik ~ hoch (je nach Vorgang), preis 0 → **gesamt ≤ 85 %**, Preis-Etappe rot mit „OMD/IDS offen".

---

## D. Offene Aufgabenliste

| ID | Aufgabe | Backend | Frontend | Risiko | Blocker? | Aufwand | Testpfad | Status |
|---|---|---|---|---|---|---|---|---|
| 4a | Workflow-Cockpit (read-only Führung) | Aggregations-Service | Cockpit-View | niedrig | nein | S | Route auth/404/Non-WP/leer/voll, kein Write | **in Umsetzung** |
| 4b | Auslegungs-Vorschlag „würde übernommen"-Ansicht | — (reuse) | UI-Vorschau + Diff | niedrig | nein | S–M | Vorschau read-only, kein Write | offen |
| 4c | Erster Schreibpfad Auslegung→sections | Serializer + Übernahme | Wizard-Aktion | **hoch** | **ja** (Katalog-Anker) | M–L | nur mit belegtem Anker ODER preislos markiert | **blockiert** (P3-e; Preis fehlt) |
| P3-e-Preis | OMD/IDS realer Import → distributor_prices | Import/Spiegel | Pflege-UI | hoch | **ja** | L | reale Anbindung nötig | **blockiert** (operative OMD-Seite) |
| K1 | Katalog-Kuratierung WP-Set (Preis+Specs je product_id) | Daten | — | mittel | **ja** | M | Test-DB-Fixture | **blockiert** (Preisquelle) |

---

## E. Nächste 3 MVP-Slices

**Slice 4a — WP-Angebots-Cockpit / Workflow-Statusseite (read-only)** ← *dieser Auftrag*
- **Backend:** `WpAngebotsWorkflowService` (aggregiert Reife + Auslegung + Matching on-the-fly; Prozentlogik C; 5-Schritt-Modell; nächste Aktion). `WpAngebotsWorkflowController::show`. Route `offers.workflow` GET `/offers/workflow/{leadProductList}`.
- **Frontend:** `cockpit.blade` — Kopf (Vorgang/Gewerk, konservativ PII-arm), Prozessleiste (Bedarf/Reife/Auslegung/Preisfähigkeit/Angebot), Fortschritt %, Aufgabenzähler, Technik/Preis getrennt, eingebettete Panels (lazy, wie UX-2), nächste-Aktion-CTA, „Angebot erstellen" als Link (`offers.wizard`) nur wenn angebotsfähig, sonst disabled.
- **Testpfad:** auth/404/Non-WP/leer/voll, kein Write, keine PII, View kompiliert.

**Slice 4b — Auslegungs-Vorschlag „würde übernommen werden"** (read-only)
- Reuse `AuslegungVorschlagService`; UI zeigt die proposed Positionen klar als „Vorschau — noch nicht im Angebot", mit Datenlage/Preisstatus. Kein Write. Bereitet 4c visuell vor.

**Slice 4c — erster sicherer Schreibpfad Auslegung→`offer_details.sections`** *(heute blockiert)*
- Nur zulässig, wenn ein **belegter Katalog-Anker** existiert (P3-e/K1) ODER Positionen explizit **preislos/ungesichert markiert** übernommen werden. Da der Preisstrang (OMD/IDS) inert ist und Auslegung→Positionen ohne Anker bepreist würde: **nicht bauen, sondern blockiert markieren**, bis Katalog-Anbindung steht.

---

## Empfehlung / Reihenfolge
1. **4a jetzt** (read-only Führung, sicher, sofort browserprüfbar).
2. **4b** danach (Vorschau der Übernahme, read-only).
3. **4c** erst nach OMD/IDS-Preisanbindung + Katalog-Kuratierung (bleibt bis dahin blockiert markiert).

*(Dieses Dokument darf committet werden; Preis-/OMD-Blocker sind belegt in `docs/bereich2-p3d2-ids-openmaster-preisfluss.md` / `-p3d2b-…`.)*
