# Kundenprofil-Struktur — VOLLSTÄNDIGE Bestandsaufnahme (Ist + Soll-Ist)

> **Reine Analyse (nur Lesen), kein Neu-Design.** Vollständige, nummerierte Inventur aller Bereiche/Nav-Punkte des Profils + Soll-Ist-Abgleich (Kunde→Objekt→Projekt→Gewerk · 6 Phasen · Phase→Aufgabe→Arbeitsschritt). Grundlage: `architektur-entscheidungen.md`, `glossar.md`. **Ersetzt die frühere, unvollständige Fassung.**
>
> **Systematik:** Das Profil besteht aus **zwei** Blades. Jede Zeile beider wurde per Grep segmentiert (unten „Abdeckungs-Nachweis"). Die eigentlichen **Bereiche** liegen in nur ~2.800 der ~31.700 Zeilen — der Rest ist **CSS (~5.000 Z.)** und **JS (~24.000 Z.)**.

## 0. Abdeckungs-Nachweis (jede Zeile zugeordnet)

**`customer_profile.blade.php` (19.336 Z.):**
| Zeilen | Typ | Inhalt |
|---|---|---|
| 1–11 | Kopf | `@section('title')` + Head-`<link>`s (select2/glightbox/dropzone/object-context-menu) |
| 12–24 | `@section('style')` | 5 CSS-`<link>`s (u. a. ausgelagertes `customer_profile.css`) |
| **25–1149** | **`@section('content')`** | `@include(layouts.profile)` (Z.32) **+ die Overlays #15–31** |
| 1154–19336 | `@section('script')` | **JS** (~18.180 Z.) — keine „Bereiche", nur Verhalten |

**`layouts/profile.blade.php` (12.352 Z.):**
| Zeilen | Typ | Inhalt |
|---|---|---|
| **1–4988** | **CSS** | 11 `<style>`-Blöcke (2–69, 71–356, 357–2863, 2864–2968, 2970–3318, 3319–3587, 3588–4502, 4503–4636, 4637–4689, 4691–4888, 4890–4988) |
| **4989–6651** | **HTML-Content** | die **Bereiche #1–14** (Nav, Galerie, main-content, right-panel, Phasen-Sidebar, Modals) |
| 6653–12352 | **JS** | `<script>`-Blöcke (6653–6774, 6776–7916, 7918–8048, 8049–8154, 8270–8475, … bis Dateiende) |

→ **Keine großen HTML-Blöcke außerhalb `layouts:4989–6651` + `customer_profile:25–1149`.** Alles andere ist CSS oder JS (belegt durch `<style>`/`<script>`-Grenzen oben).

---

## 1. Vollständige Bereichs-Inventur (nummeriert 1…31)

### Gruppe I — Navigation (3 getrennte Mechanismen)
1. **Top-Nav `customer-nav`** — `layouts:5033`. Drei Modi: **Info · Historie · Aktivität** (`:5033–5177`).
2. **Bereichs-Nav (12 Punkte, daten-getriebenes Array)** — `layouts:5920–6115`, **genau 12** `label`-Einträge: Bilder & Dokumente(5930) · Aufgaben(5946) · Termin(5961) · **Angebote(5975)** · **Auftrag(5989)** · **Montage(6003)** · Rechnungen(6017) · Produkt(6039) · Tickets(6054) · Bewertungen(6068) · Historie(6085) · Arbeitsprozess(6099). Je mit `count` + `data-customer-id/-alternative-id/-product-id/-product-list-id`.
3. **Notiz-/Feed-Typ-Switcher** — `layouts:6373` (`ma-note-type-switcher`). Sechs Feed-Typen: **Angebot · Aufgaben · Auftrag · Kundenberichte · Termine · Tickets** (`:6373–6516`).

### Gruppe II — Layout-Container / Inhaltsflächen
4. **`customer-wrapper`** (Wurzel) — `layouts:4993`.
5. **`layout`** (Spalten-Container) — `layouts:5515`; darin `customerSidebar` (`:5516`).
6. **`sidebar-gallery`** — `layouts:5721`: **Objekt-Kacheln** mit Google-Map (`mapContainer{{ $object->id }}`) + Street-View-Screenshot (`object-thumb-streetview`) + Ansichtsmodi (Satellit/Karte/Gelände/Hybrid); **je Objekt eine Gewerke-Liste** (`<div id="object{{ $key }}" class="product-list">` + `@foreach ($products->where('alternative_id', $object->id))`, ~`:5866`).
7. **`main-content` / `#mainContent`** — `layouts:6229`: zentrale Fläche, **JS-befüllt** (die 12-Punkte-Nav lädt Panels hierhin; kaum Blade-Inhalt).
8. **`right-panel` / `#customerNotesRightPanel`** — `layouts:6321`: Notizen-/Feed-Panel (`ma-notes-header:6324`, Typ-Switcher #3, `newNoteArea:6517`, `note-scroll-wrapper:6522`).
9. **`mobileSidebarOpenBtn`/`-Backdrop`** — `layouts:2364` + `:5508/5513`: Mobil-Umschalter.

### Gruppe III — Drawer / Modals im Layout
10. **`customerFeedModal`** (`modal fade feed-modal`) — `layouts:5433`: Aktivitäts-Feed-Modal.
11. **`priceHistoryBackdrop` / `ph-drawer`** — `layouts:6562`: Preis-Historie-Drawer.
12. **`phaseSidebar`** (`phase-sidebar`) — `layouts:6593`: **Phasen-Sidebar**, Inhalt `<p>Lade...</p>` → **JS/AJAX-geladen** (`data-service-id`).
13. **`purchaseModal`** („Kaufübersicht") — `layouts:6601`; `noteDeletedModal` (`:6360`) + `noteDeletedModalWrapper` („Gelöschte Notizen", `:6618`).
14. **`newNoteComposer` / `noteBackdrop`** — `layouts:6636/6648`: Notiz-Erfassung.

### Gruppe IV — Overlays aus `customer_profile.blade.php` (Content 25–1149)
15. **`newProductModal`** — `:40` (+ `@include(new_product_form)`).
16. **`reportSidebar`** („Kundenprozessbericht") — `:69` (`reportList`, `reportFormContainer`).
17. **`comment_sidebar`** (Partial) — `@include :134`.
18. **`suggest_employees_drawer`** (Partial) — `@include :159`.
19. **`editSuggestedEmployeeDrawer`** — `:162`.
20. **`half_done_modal`** (Partial) — `@include :227`.
21. **`done_history_modal`** (Partial) — `@include :229`.
22. **`customerEditDrawer`** (Partial) — `@include :232`.
23. **`customerContactPeopleModal`** („Kontaktpersonen") — `:238`.
24. **`addProductOverlay`** — `:353` (Tabs `addTabDetails`/`addTabGallery`).
25. **`editProductOverlay`** — `:580` (Tabs `editTabDetails`/`editTabGallery`).
26. **`serials_overlay`** (Partial) — `@include :804`.
27. **`addCustomerProductModal`** — `:808`.
28. **`editCustomerProduct`** — `:832`.
29. **`obj_drawer_root`** (Partial, „Produkte zu Objekten zuordnen") — `@include :926`.
30. **`maHoverPreviewOverlay` (2×!) / `maLightboxOverlay`** — `:1008/1028/1012` (Bild-Hover + Lightbox; **Doppel-ID** bekannt).
31. **`customerKanbanTaskDrawer`** („Aufgabenmanagement") — `:1033`.

**Reihenfolge (oben→unten, gerendert):** Top-Nav → Objekt-Galerie (links) → main-content (mitte, 12-Nav-Panels) → right-panel Notizen/Feed (rechts) → dann die floating Drawer/Modals (#10–31, per JS geöffnet).

---

## 2. Hierarchie-Abbildung (Kunde→Objekt→Projekt→Gewerk)

- **Kunde** (`new_leads`, `$customer`) = Profil-Rahmen (Top-Nav #1).
- **Objekt** (`lead_alternative_adds`) = **eigene Galerie-Kachel je Objekt** mit Karte/Street-View (#6; `alternative` **279×** im Layout, `data-alternative-id` durchgängig). **Hierarchie sichtbar.**
- **Gewerk** (`lead_product_lists`) = **verschachtelt unter jedem Objekt** (`@foreach $products->where('alternative_id', $object->id)`, #6). **Objekt→Gewerk verschachtelt.**
- **Projekt** = **keine** eigene Nav-Ebene → **konsistent mit Weiche 5** (Projekt = Bauphase des Auftrags). ✅

**Einschränkung:** Verschachtelung nur in der **Galerie** (#6). Die Bereichs-Nav (#2), der Feed-Switcher (#3) und main-content (#7) sind **flach** und auf **ein aktives `(Objekt, Gewerk)`-Tupel** gescoped.

## 3. Phasen-Abbildung (6 Phasen · Phase→Aufgabe→Arbeitsschritt)

- **6 Phasen (`lead_stages`) im Profil-Blade: 0 Vorkommen** (auch `task_phase`/`phase_activit`/`customer_phase` = 0). Die verbindliche 6-Phasen-Achse ist **nicht** als Navigation/Fortschritt abgebildet.
- **Phasen-Sidebar (#12)** existiert, ist aber **JS-geladen** (`<p>Lade...</p>`, `data-service-id`) → **NICHT VERIFIZIERT**, welches Phasen-System sie zeigt (im Blade kein `lead_stage`).
- **Phasen-Labels verstreut in ZWEI Navs:** Bereichs-Nav (#2: Angebote/Auftrag/Montage/Rechnungen) **und** Feed-Switcher (#3: Angebot/Auftrag) — **flach gemischt** mit Funktionen, **nicht** die 6er-Sequenz (Lead/Abnahme/Abschluss fehlen als Nav-Punkt).
- **Phase→Aufgabe→Arbeitsschritt verteilt auf 4+ Stellen:** Bereichs-Nav „Aufgaben"(#2) + „Arbeitsprozess"(#2) + Feed „Aufgaben"(#3) + `phaseSidebar`(#12) + `customerKanbanTaskDrawer`(#31). Keine eine Sicht.

## 4. Widersprüche zur Soll-Struktur

1. **6 Phasen fehlen im Profil** (0 `lead_stage`) — die verbindliche Phasen-Achse (Weiche 1) ist hier nicht sichtbar; Phasen-Navigation lebt im externen Lead-Kanban.
2. **Phasen + Funktionen gemischt in ZWEI flachen Navs** (#2 und #3) ohne Phasen-Ordnung.
3. **„Rechnungen" als Phase-Peer** (#2), obwohl Rechnung eine Aufgabe der Abschluss-Phase ist — Ebenen-Vermischung.
4. **Aufgaben an ≥4 Stellen** (#2 Aufgaben, #2 Arbeitsprozess, #3 Feed-Aufgaben, #12, #31) — spiegelt die „mehrere Aufgaben-Systeme"-Zersplitterung.
5. **Drei getrennte Nav-Mechanismen** (#1 Top, #2 Bereich, #3 Feed) mit teils **überlappenden** Labels (Angebot/Auftrag/Aufgaben/Tickets/Termine tauchen in #2 UND #3 auf) → Redundanz, unklare Zuständigkeit.
6. **`phaseSidebar` (#12) hängt vermutlich am alten Phasen-System** — UI nicht auf die entschiedene Achse migriert (NICHT VERIFIZIERT).

## 5. Stärken (ehrlich)

1. **Objekt-Zentrierung stark verankert** (`alternative` 279×, `data-alternative-id` durchgängig) — passt exakt zum objekt-zentrierten Zielbild.
2. **Kunde→Objekt→Gewerk verschachtelt sichtbar** (#6) — Soll-Hierarchie korrekt abgebildet; „Projekt" zurecht keine Ebene (Weiche 5).
3. **Starke Objekt-Visualisierung** (Google-Map + Street-View je Objekt, #6).
4. **Bereichs-Nav daten-getrieben** (Array `label/count_key/count`, #2) → spätere Neu-Ordnung **markup-arm**.
5. **Klare Rollen-Trennung** Top-Nav (Kunden-Ebene, #1) vs. Bereichs-/Feed-Nav (kontextbezogen, #2/#3).
6. **Reiche, konsolidierte Funktionsabdeckung** (31 Bereiche: Dokumente, Aufgaben, Termine, Angebote, Aufträge, Montage, Rechnungen, Tickets, Bewertungen, Kontaktpersonen, Objekt-Zuordnung …) — funktional vollständig, nur strukturell ungeordnet.

## 6. Fazit — Ist → Soll (kompakt)

| Aspekt | Ist | Soll | Urteil |
|---|---|---|---|
| Kunde→Objekt→Gewerk | verschachtelt (#6) | ebenso | ✅ konform (Stärke) |
| Projekt als Ebene | keine | Bauphase, keine Ebene | ✅ konform |
| 6 Phasen im Profil | **0** | sichtbar/navigierbar | ❌ Widerspruch |
| Nav-Anzahl | **3** (Top/Bereich/Feed), teils redundant | klare Achse+Inhalt | ❌ Widerspruch |
| Phasen-Labels | flach in #2 **und** #3 | Phase = Achse | ❌ Vermischung |
| „Rechnungen" | Phase-Peer | Abschluss-Aufgabe | ❌ Ebenen-Fehler |
| Aufgaben | ≥4 Stellen | eine Sicht | ⚠️ zersplittert |
| Nav-Technik | daten-getrieben | — | ✅ gut umbaubar |

**Kein Neu-Design hier.** Die **Hierarchie stimmt weitgehend**; der Umbau-Bedarf ist die **fehlende Phasen-Achse + drei gemischte/redundante Navs**. Weil #2 daten-getrieben ist, wäre eine Neu-Ordnung markup-arm — die **Entscheidung** (Phase als Achse vs. Funktion als Inhalt; Konsolidierung der 3 Navs) steht aus und hängt an Weiche 1 + 6.

---

## Gelesen / NICHT gelesen — mit Zeilen-Abdeckung

**Systematisch gescannt (grep über JEDE Zeile beider Dateien):** alle `<style>`/`<script>`-Grenzen (Segmentierung), alle Modal/Drawer/Sidebar/Overlay-`id=`/`class=`, das Bereichs-Nav-Array (12 `label`), der Feed-Switcher (6 Typen), die Top-Nav (3), alle `@include`, alle Top-Level-Container (Einzug 0–4). **Gelesen (Regionen):** `layouts:4989–6651` (der gesamte HTML-Content-Bereich, in Abschnitten), Objekt→Gewerk-`@foreach`, Feed-Switcher, main-content, right-panel, phaseSidebar; `customer_profile:25–1149` (Content/Overlays, aus hart-verifizierter Kartierung).

**Nachweis:** *„Durchgesehen wurde die Struktur beider Blades vollständig segmentiert (Z.1–Ende). Zu KEINEM gelisteten Bereich gehören: `customer_profile:1154–19336` (JS) und `layouts:1–4988` (CSS) + `layouts:6653–12352` (JS) — diese ~29.000 Z. sind belegt CSS/JS, keine Bereiche."*

**NICHT gelesen / NICHT VERIFIZIERT:**
- **Das JS** (beide Dateien, ~24.000 Z.) — was die JS-befüllten Panels (main-content #7) + `phaseSidebar` (#12) **zur Laufzeit** anzeigen (Phasen? welches System?). „6 Phasen fehlen" gilt **belegt fürs Blade**, nicht für die AJAX-Laufzeit.
- **CSS** (~5.000 Z.) — nicht inhaltlich gelesen (irrelevant für Bereiche).
- **Default-Inhalt von `main-content`** (welches Panel zuerst) — nicht im Detail.

## Schwächen dieser Analyse (Selbstkritik)
- Die **Bereichs-Zählung (31)** beruht auf `id=`/`class=`-Enumeration + Segmentierung — ein Bereich mit **völlig unkonventionellem** Marker (kein id/keine bekannte class) könnte entgehen; der Abdeckungs-Nachweis (CSS/JS-Segmentierung) macht das aber unwahrscheinlich, da große unkartierte HTML-Blöcke ausgeschlossen sind.
- **Laufzeit unbelegt:** ob JS-Panels/`phaseSidebar` doch Phasen zeigen, ist offen — Browser-Test nötig, um die stärkste Aussage („6 Phasen fehlen") von Blade auf Laufzeit zu erhärten.
- **Konnte ich NICHT sicher zuordnen:** was genau `main-content` (#7) je Nav-Klick lädt (JS); ob `customerFeedModal` (#10) und der Feed-Switcher (#3) dieselbe Datenquelle sind; ob `phaseSidebar` (#12) und `customerKanbanTaskDrawer` (#31) dieselben Aufgaben zeigen — alles JS-abhängig, hier **NICHT VERIFIZIERT**.

---

*Reine Analyse — nichts geändert, kein Neu-Design. Belege: Segmentierung beider Blades (`<style>`/`<script>`-Grenzen wörtlich), `layouts/profile.blade.php` (customer-nav:5033, Bereichs-Nav 12×:5920–6115, Feed-Switcher 6×:6373, sidebar-gallery:5721 + Objekt→Gewerk-`@foreach`, main-content:6229, right-panel:6321, phaseSidebar:6593, Modals:5433/6360/6562/6601/6618/6636), `customer_profile.blade.php` (@section-Grenzen 12/25/1149, Overlays 40–1033); grep-Zählungen (alternative 279, lead_product 13, lead_stage 0, task_phase/phase_activit/customer_phase 0). Querverweis: `architektur-entscheidungen.md` (Weiche 1/5/6), `glossar.md`, `kundenprofil-architektur-bestandsaufnahme.md`, `kundenprofil-kartierung-hart-verifiziert.md`, `kanban-ebenen-montage-planner-nuriva-befund.md`, `struktur-systeme-verhaeltnis-befund.md`.*
