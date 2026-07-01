# Kundenprofil-Struktur — Bestandsaufnahme (Ist + Soll-Ist-Abgleich)

> **Reine Analyse (nur Lesen), kein Neu-Design.** Ziel: die heutige Struktur/Navigation des Profils erfassen und gegen die **entschiedene** Struktur abgleichen (Kunde→Objekt→Projekt→Gewerk · 6 Phasen · Phase→Aufgabe→Arbeitsschritt). Grundlage: `architektur-entscheidungen.md` (Weiche 1/5), `glossar.md`. **Kein Vorschlag** — nur die Bestandsaufnahme, die eine spätere Design-Entscheidung ermöglicht.
>
> **Wo die Struktur steckt:** Die eigentliche Navigation liegt in **`resources/views/admin/new_leads/layouts/profile.blade.php`** (12.352 Z., via `@include` aus `customer_profile.blade.php:32`). Die Modal-Datei enthält nur die Overlays.
>
> **Kurzfazit:** Die **Hierarchie Kunde→Objekt→Gewerk ist verschachtelt sichtbar** (Objekt-Galerie mit Karte/Street-View + Gewerke je Objekt) — das ist eine **Stärke** und passt zum Soll (Projekt ist zurecht keine Nav-Ebene, Weiche 5). Der **große Widerspruch: die 6 entschiedenen Phasen erscheinen im Profil-Blade NICHT** (`lead_stage` = 0 Vorkommen); stattdessen mischt eine **flache 12-Punkte-Bereichs-Nav** Phasen-Labels (Angebote/Auftrag/Montage/Rechnungen) mit Funktionen (Aufgaben/Termin/Produkt/…) ohne die 6er-Sequenz.

---

## 1. Navigations-/Bereichs-Inventur (mit DOM-Verankerung)

**A) Top-Nav — `customer-nav`** (`profile.blade.php:5033`, `-inner:5178`): drei Modi — **Info · Historie · Aktivität**.

**B) Bereichs-Nav — flache 12-Punkte-Liste** (daten-getriebenes Array, `5930–6099`), je mit `icon`/`count_key`/`count` + `data-customer-id`/`data-alternative-id`/`data-product-id`/`data-product-list-id` (`:5939–5942`):

| # | Label | Zeile | Typ |
|---|---|---|---|
| 1 | Bilder & Dokumente | 5930 | Funktion |
| 2 | Aufgaben | 5946 | Funktion (Prozess) |
| 3 | Termin | 5961 | Funktion |
| 4 | **Angebote** | 5975 | Phase (2) |
| 5 | **Auftrag** | 5989 | Phase (3) |
| 6 | **Montage** | 6003 | Phase (4) |
| 7 | Rechnungen | 6017 | *Aufgabe der Abschluss-Phase, keine der 6* |
| 8 | Produkt | 6039 | Gewerk/Objekt |
| 9 | Tickets | 6054 | Funktion |
| 10 | Bewertungen | 6068 | Funktion |
| 11 | Historie | 6085 | Funktion |
| 12 | **Arbeitsprozess** | 6099 | Prozess (Phase→Aufgabe), `data-service-id`, `project-link project-card` |

**C) Objekt-Galerie — `sidebar-gallery`** (`:5721`): Objekt-Kacheln mit **Google-Map** (`mapContainer{{ $object->id }}`) + **Street-View-Screenshot** (`object-thumb-streetview`, `triggerScreenshot(customer, object)`), Ansichtsmodi Satellit/Karte/Gelände/Hybrid; **je Objekt eine Gewerke-Liste** (`<div id="object{{ $key }}" class="product-list ma-product-list">` + `@foreach ($products->where('alternative_id', $object->id) …)`, `~:5866`).

**D) Phasen-Sidebar — `phaseSidebar`** (`:6593`, `data-service-id`): Blade-Inhalt nur `<p>Lade...</p>` → **JS/AJAX-geladen** (Inhalt nicht im Blade).

**E) Container-Reihenfolge** (oben→unten): `customer-nav-wrap` (4994) → `customer-nav` (5033) → `sidebar-gallery` (5721) → Bereichs-Nav (5930) → `main-content` (6229) → `right-panel` (6321) → `phaseSidebar` (6593).

**F) Overlays** (aus `customer_profile.blade.php` + Layout): newProductModal, reportSidebar, editSuggestedEmployeeDrawer, customerContactPeopleModal, addProductOverlay, editProductOverlay, addCustomerProductModal, objDrawerRoot („Produkte zu Objekten zuordnen"), maHover/maLightbox, customerKanbanTaskDrawer; + Layout: purchaseModal („Kaufübersicht"), noteDeletedModalWrapper („Gelöschte Notizen"), priceHistory, newNoteComposer.

---

## 2. Hierarchie-Abbildung (Kunde→Objekt→Projekt→Gewerk)

**Verschachtelt sichtbar — überwiegend Soll-konform:**
- **Kunde** (`new_leads`) = Rahmen des Profils (`$customer->id`, Top-Nav).
- **Objekt** (`lead_alternative_adds`) = **eigene Galerie-Kachel je Objekt** mit Karte/Street-View (`alternative` 279× im Layout, `data-alternative-id` durchgängig). **Hierarchie sichtbar.**
- **Gewerk** (`lead_product_lists`) = **unter jedem Objekt** als Produktliste (`@foreach $products->where('alternative_id', $object->id)`). **Objekt→Gewerk verschachtelt.**
- **Projekt** als Ebene: **nicht** als eigene Nav-Ebene — **konsistent mit Weiche 5** (Projekt = Bauphase des Auftrags, keine Ebene über Aufträgen). Gewerk hängt direkt unter Objekt. ✅

**Einschränkung:** Die Verschachtelung ist in der **Galerie** sichtbar; die **Bereichs-Nav (12 Punkte)** ist dagegen **flach** und auf **ein** aktives `(Objekt, Gewerk)`-Tupel gescoped (`data-alternative-id`/`data-product-list-id`) — d. h. man wählt Objekt/Gewerk in der Galerie, die Nav zeigt dann flach die Daten dieses einen Tupels. Kein Hierarchie-Baum in der Nav selbst.

---

## 3. Phasen-Abbildung (6 Phasen · Phase→Aufgabe→Arbeitsschritt)

- **Die 6 entschiedenen Phasen (`lead_stages`) erscheinen im Profil-Blade NICHT:** `lead_stage` = **0 Vorkommen**; `task_phase`/`phase_activit`/`customer_phase` = **0**. Die 6-Phasen-Achse (Lead→Angebot→Auftrag→Montage→Abnahme→Abschluss) ist im Profil **nicht als Navigation/Fortschritt abgebildet.** *(Die Phasen-Navigation lebt im separaten Lead-Kanban — siehe `kanban-ebenen-montage-planner-nuriva-befund.md`.)*
- **Stattdessen 4 phasen-ähnliche Nav-Labels** (Angebote/Auftrag/Montage/Rechnungen), **flach gemischt** mit Funktionen — **nicht** die 6er-Sequenz: **Lead**, **Abnahme**, **Abschluss** fehlen als Nav-Punkt; **Rechnungen** ist keine der 6 Phasen (Aufgabe der Abschluss-Phase).
- **Phase→Aufgabe→Arbeitsschritt** ist **verteilt**, nicht als eine Achse: Nav-Punkt **Aufgaben** + Nav-Punkt **Arbeitsprozess** (`data-service-id`, `project-card`) + **phaseSidebar** (JS-geladen) + Overlay **customerKanbanTaskDrawer**. → die „mehrere Aufgaben-Systeme"-Zersplitterung (siehe `struktur-systeme-verhaeltnis-befund.md`) spiegelt sich in der UI.
- **`phaseSidebar` lädt per JS** (`data-service-id`) — **NICHT VERIFIZIERT**, ob es zur Laufzeit die alten `task_phases`/`phase_sections` oder etwas anderes zeigt; im **Blade** gibt es **keinen** `lead_stage`-Bezug.

---

## 4. Widersprüche zur entschiedenen Struktur

1. **6 Phasen fehlen im Profil.** Die verbindliche Phasen-Achse (Weiche 1) ist im Profil **nicht sichtbar/navigierbar** (0 `lead_stage`). Der Nutzer sieht den Vorgangs-Fortschritt entlang der 6 Phasen **nicht** hier, sondern nur im externen Kanban.
2. **Flache Nav mischt Phasen + Funktionen.** Angebote/Auftrag/Montage/Rechnungen stehen ohne Phasen-Ordnung neben Aufgaben/Termin/Produkt/Tickets/Bewertungen — die entschiedene Trennung „Phase (Achse) vs. Funktion (Inhalt)" ist nicht abgebildet.
3. **„Rechnungen" als Phase-Peer**, obwohl Rechnung laut Weiche 1 eine **Aufgabe der Abschluss-Phase** ist — Ebenen-Vermischung.
4. **Aufgaben an mehreren Nav-/Overlay-Stellen** (Aufgaben · Arbeitsprozess · phaseSidebar · customerKanbanTaskDrawer) — keine eine Aufgaben-Sicht.
5. **phaseSidebar hängt vermutlich am alten Phasen-System** (nicht `lead_stages`) — die UI ist noch nicht auf die entschiedene Achse migriert (NICHT VERIFIZIERT, JS-geladen).

## 5. Stärken (ehrlich — was gut ist und bleiben sollte)

1. **Objekt-Zentrierung ist stark verankert** (`alternative` 279×, `data-alternative-id` durchgängig) — passt exakt zum objekt-zentrierten Zielbild.
2. **Kunde→Objekt→Gewerk ist verschachtelt sichtbar** (Galerie: Objekt mit Karte/Street-View → Gewerke je Objekt) — bildet die Soll-Hierarchie **korrekt** ab; „Projekt" zurecht keine Ebene (Weiche 5).
3. **Visuelle Objekt-Verankerung** (Google-Map + Street-View je Objekt) — starke, intuitive Objekt-Repräsentation.
4. **Bereichs-Nav ist daten-getrieben** (Array `label/count_key/count`, keine hartcodierten Tabs) → **leicht umsortierbar/erweiterbar** ohne Markup-Umbau. Das ist die beste Voraussetzung für eine spätere Neu-Ordnung.
5. **Klare Trennung** Top-Nav (Info/Historie/Aktivität = Kunden-Ebene) vs. Bereichs-Nav (kontextbezogen je Objekt/Gewerk).

## 6. Fazit — Ist → Soll-Abgleich (kompakt)

| Aspekt | Ist heute | Soll (entschieden) | Bewertung |
|---|---|---|---|
| Kunde→Objekt→Gewerk | verschachtelt in Galerie (Objekt→Gewerke) | Kunde→Objekt→Gewerk | ✅ **konform** (Stärke) |
| Projekt als Ebene | keine eigene Ebene | Bauphase des Auftrags, keine Ebene | ✅ konform |
| 6 Phasen im Profil | **0** (`lead_stage`) — nur im externen Kanban | 6 Phasen sichtbar/navigierbar | ❌ **Widerspruch** |
| Nav-Ordnung | flach, Phasen+Funktionen gemischt | Phase = Achse, Funktion = Inhalt | ❌ Widerspruch |
| „Rechnungen" | eigener Nav-Peer | Aufgabe der Abschluss-Phase | ❌ Ebenen-Vermischung |
| Phase→Aufgabe→Arbeitsschritt | verteilt (4 Stellen) | eine kohärente Achse | ⚠️ zersplittert |
| Nav-Technik | daten-getrieben (Array) | — | ✅ gut umsortierbar |
| Objekt-Visualisierung | Map/Street-View je Objekt | objekt-zentriert | ✅ Stärke |

**Kein Neu-Design hier.** Die Bestandsaufnahme zeigt: die **Hierarchie stimmt schon weitgehend**, der Hauptumbau-Bedarf ist die **Phasen-Achse (fehlt im Profil) + die flache, gemischte Bereichs-Nav**. Weil die Nav **daten-getrieben** ist, wäre eine Neu-Ordnung markup-arm — aber die **Entscheidung**, wie Phase (Achse) und Funktion (Inhalt) im Profil getrennt werden, steht noch aus und hängt an Weiche 1 (Phasen-Führung) und Weiche 6 (Aufgaben-Systeme).

---

## Gelesen / NICHT gelesen (ehrlich)

**Gelesen:** `profile.blade.php` — Nav-Region (4994–5180), Objekt-Galerie + Objekt→Gewerk-Verschachtelung (5700–5925), Bereichs-Nav-Array (5920–6118), Container-Anker (main-content 6229, right-panel 6321, phaseSidebar 6593), phaseSidebar-Inhalt; grep-Zählungen (alternative 279, lead_product 13, lead_stage 0, task_phase/phase_activit 0); Overlay-Liste aus `customer_profile.blade.php` (frühere Kartierung, hart verifiziert). **Nur gegrept / NICHT VERIFIZIERT:**
- **Das JS** hinter Bereichs-Nav-Panels + `phaseSidebar` (`data-service-id`) — **was zur Laufzeit** geladen/gezeigt wird (Phasen? welches System?) ist **nicht** gelesen; „Phasen nicht im Profil" gilt **belegt für das Blade**, nicht für die AJAX-Laufzeit.
- Nur ~1.500 der 12.352 Layout-Zeilen gelesen (Struktur-Anker), **nicht** die vollständige Datei — einzelne Bereiche/Karten könnten übersehen sein.
- Der **Default-Inhalt** von `main-content` (welche Ansicht zuerst erscheint) ist nicht im Detail gelesen.

## Schwächen dieser Analyse (Selbstkritik)
- **Struktur per Grep + Stichprobe**, kein Vollstudium des 12k-Layouts → die Nav-Inventur ist stark belegt, aber „vollständig" ist **nicht** garantiert.
- **Laufzeit-Verhalten unbelegt:** Ob die JS-geladenen Panels doch Phasen zeigen, ist offen — die stärkste Aussage („6 Phasen fehlen") ist **Blade-basiert**. Ein Browser-Test würde sie erhärten oder widerlegen.
- „Stärke/Schwäche"-Bewertung ist **meine Einordnung** gegen das Soll, keine reine Code-Aussage.

---

*Reine Analyse — nichts geändert, kein Neu-Design. Belege: `layouts/profile.blade.php` (customer-nav:5033, Bereichs-Nav-Items:5930–6099, sidebar-gallery:5721, Objekt→Gewerk `@foreach $products->where('alternative_id',$object->id)`:~5866, mapContainer/Street-View je Objekt, main-content:6229, right-panel:6321, phaseSidebar:6593 „Lade..."); grep-Zählungen (alternative 279, lead_product 13, lead_stage 0, task_phase/phase_activit/customer_phase 0). Querverweis: `architektur-entscheidungen.md` (Weiche 1/5/6), `glossar.md`, `kundenprofil-architektur-bestandsaufnahme.md`, `kanban-ebenen-montage-planner-nuriva-befund.md`, `struktur-systeme-verhaeltnis-befund.md`.*
