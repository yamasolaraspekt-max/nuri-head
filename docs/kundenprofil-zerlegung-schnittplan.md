# Kundenprofil-Blade — Zerlegungs-Schnittplan (read-only)

**Reine Analyse. KEIN Refactor, keine Zerlegung, kein Code verschoben.** Stand: 2026-07-01 · Branch `private/app-code-backup`.
Datei: `resources/views/admin/new_leads/customer_profile.blade.php` — **23.145 Zeilen**. Grundlage: `docs/kundenprofil-architektur-bestandsaufnahme.md`, Begriffe `docs/glossar.md`.
Zweck: ein **Schnittplan**, damit die Datei später **Scheibe für Scheibe** sicher zerlegt werden kann. Dieses Dokument ändert nichts.

> **Kernbefund:** Die Datei ist sauber in **drei Blade-Sektionen** geteilt — und sie ist zu **78 % Inline-JavaScript**:
> - **CSS** (`@section('style')`): Z.12–3615 ≈ **3.604 Z. (16 %)** — fast Blade-frei → **sicherste erste Scheibe**.
> - **HTML** (`@section('content')`): Z.3616–4964 ≈ **1.349 Z. (6 %)** — Profil + Modals + Drawer + 2 Asides.
> - **JS** (`@section('script')`): Z.4969–23145 ≈ **18.176 Z. (78 %)** — 82 `<script>`-Tags; **hier liegt die eigentliche Größe und das Risiko**.

---

## 1. Grobgliederung — die Landkarte der Datei

| Bereich | Zeilen | Inhalt |
|---|---|---|
| `@section('title')` | 8–11 | Seitentitel (3 Z.) |
| **`@section('style')` — CSS** | **12–3615** | 7 `<style>`-Blöcke (s. Abschnitt 2) |
| **`@section('content')` — HTML** | **3616–4964** | Profil-Include + Modals + Drawer + Asides (s. u.) |
| **`@section('script')` — JS** | **4969–23145** | 82 `<script>`-Tags (12 extern, ~70 inline) |

### HTML-Content (3616–4964) im Detail
| Block | ~Zeile | Art |
|---|---|---|
| `@include('…layouts.profile')` | 3623 | Kopf/Stammdaten (Partial — schon ausgelagert) |
| `@include('…layouts.new_product_form')` | 3646 | Gewerk-Formular (Partial) |
| `reportSidebar` (Drawer) | ~3781 | Kundenprozessbericht |
| `commentSidebar` (Drawer) | ~3846 | Kommentare |
| `@include('…partials.customerEditDrawer')` | 3936 | Stammdaten-Edit-Drawer (Partial) |
| Modal „Teilweise erledigt" | ~3883 | Status-Modal |
| Modal „Verlaufsdetails" | ~3921 | Historie |
| Karte „Übersicht" | ~3967 | Kunde |
| „Kontaktperson hinzufügen" | ~3992 | Kontakt |
| Modal „Neues Produkt hinzufügen" | ~4063 | Gewerk add (Variante 1) |
| Modal „Produkt ansehen/bearbeiten" | ~4291 | Gewerk edit |
| Modal „Seriennummern verwalten" | ~4511 | Seriennummern |
| Modal „Produkt hinzufügen" (cmodal) | ~4554 | Gewerk add (Variante 2 — Dublette?) |
| Modal „Produkt bearbeiten" (cmodal) | ~4597 | Gewerk edit (Variante 2) |
| **`<aside>` „Produkte zu Objekten zuordnen"** | ~4665 | **Objekt-Produkt-Baum** (Mehrfachheit) |
| **`<aside>` „Aufgabenmanagement"** | ~4772 | kb-enterprise Task-Karten |

### JS (4969–23145) — identifizierbare Module
- 12 externe `<script src=…>` (CDN/asset): **4970–5000** (Quill, html2canvas, Choices, d3, flatpickr …).
- Benannte Inline-Blöcke: `wizard-object-script` (**5436–6205**, ~770 Z.), `id="84723"` (6207–6213), `customer-profile-workflow-addon-script` (Workflow-Stages-Addon).
- Große unbenannte Blöcke u. a.: **5032–5408** (~376 Z.), **6324–7371** (~1.047 Z.), und viele weitere bis 23145.

---

## 2. Inline-Anteile (CSS / JS) — die risikoärmsten Kandidaten

| Anteil | Zeilen (ca.) | % | Blade drin? | Risiko beim Auslagern |
|---|--:|--:|---|---|
| **CSS** (`@section('style')`) | 3.604 | 16 % | **nur 4× `{{ }}`** (vermutl. `asset()`) | **sehr niedrig** |
| **HTML** (`@section('content')`) | 1.349 | 6 % | 37× `{{ }}`, 5× `@php`, 6× `@foreach/@if` | mittel (Daten-Bezug, bleibt Blade) |
| **JS** (`@section('script')`) | 18.176 | 78 % | **25× `{{ route() }}`**, 13× `@json/@if`, **0× `{{ $item }}`** | hoch (Blade-im-JS) |

**Die 7 `<style>`-Blöcke (CSS):** 23–1741 (**1.718 Z.!**) · 1742–1886 · 1888–2356 · 2360–2768 · 2771–3310 · 3312–3525 · 3527–3614.

**Bestätigung:** Das CSS ist **fast verhaltensneutral** auslagerbar — nur **4 Blade-Interpolationen** (wahrscheinlich `{{ asset(...) }}` für Bild-URLs). → Auslagern **als Blade-Partial** (`@include`, Blade wird weiter verarbeitet) = **100 % sicher**; als statische `.css` müsste man nur diese 4 `asset()` in feste Pfade umschreiben. **Das ist die sicherste erste Scheibe.**

---

## 3. Abhängigkeiten / Stolperfallen (Risiko-Zonen)

- **Daten ($item):** Der HTML-Content nutzt `$item`-Blöcke über `@foreach`/`@php`/`{{ }}` (37+5+6) — diese bleiben im Blade; Content-Partials müssen die Daten weiterbekommen. **Das JS nutzt `{{ $item }}` NICHT (0×)** → das JS koppelt **nicht** direkt an die Controller-Daten (großer Vorteil für die Auslagerung).
- **Blade-im-JS (der Haupt-Stolperstein):** **25× `{{ route('…') }}`** + 13× `@json/@if` im JS. Eine naive Verschiebung in eine statische `.js`-Datei **bricht** diese (Blade wird in `.js` nicht verarbeitet). Betroffene Routen: `customer.profile.workflow.stages` (2×), `suggest.employees.store/update`, `personal.task.customer.store`, `customer_card_notes.store`, `ajax.save.customer.history`, `ajax.object.delete`, `admin.new_leads.invoices.panel`, `ajax.customer.objectProductTree` u. a.
  → **Lösung (Standard-Muster):** ein kleiner Inline-„Bootstrap"-Block, der diese Werte einmal als JS-Konstanten setzt (`window.PROFILE_ROUTES = { … }`), dann den Logik-Rumpf in `.js` ziehen, der diese Konstanten liest. **Ohne diese Shim kein JS-Auslagern.**
- **Element-IDs (JS ↔ HTML):** Das JS hängt an konkreten IDs aus dem Content (`newProductModal`, `reportSidebar`, `commentSidebar`, `quill-editor`, `addTabGallery/Details`, `editTabGallery/Details`, `customerKanbanTaskTitle`, `data-object-product` …). Auslagern bricht **nicht**, solange diese IDs im Markup bleiben — aber **HTML und JS dürfen nicht im selben Zug umbenannt** werden.
- **Verflochtene Zonen (Finger weg / zuletzt):** Die benannten JS-Blöcke (`wizard-object-script`, `workflow-addon`) mischen Logik + `{{ route() }}` + DOM-Manipulation, die HTML per Template-Literal **baut** (die „Tabs" bei Z.7324/12433 sind JS-gebautes HTML, kein statisches Markup). Diese Blöcke sind die **Risiko-Zonen**.
- **~105 AJAX-Calls** sind über die ~70 Inline-Blöcke **verstreut** (nicht zentralisiert) — das erschwert ein „in einem Rutsch"-Auslagern und spricht für **blockweises** Vorgehen.

---

## 4. Natürliche Schnittlinien

| Gut (unabhängig) | Eng verwoben (zuletzt) |
|---|---|
| **CSS** (`@section('style')`) — eigener Block, fast Blade-frei | Die großen JS-Logik-Blöcke (5032–5408, 6324–7371) mit Blade + DOM-Bau |
| Externe `<script src>` (4970–5000) — reine CDN-Includes | `wizard-object-script` (Objekt-Wizard) — Logik + Routen + Template-Literale |
| Einzelne **Content-Modals** (Seriennummern ~4511, Verlaufsdetails ~3921, Kontaktperson ~3992) — eigenes Markup, kapselbar als `@include` | `customer-profile-workflow-addon` — Workflow-Stages, eng an IDs/Routen |
| Die **2 Asides** (Objekt-Produkt-Baum ~4665, Aufgaben ~4772) — abgegrenzte Panels | Alles, was HTML **im JS** erzeugt (kein sauberer Schnitt HTML/JS möglich) |

---

## 5. Vorgeschlagene Zerlegungs-Reihenfolge (sicher → riskant; nur Vorschlag, NICHTS ausführen)

> **Goldene Regel:** **eine** Scheibe pro Commit, danach Seite laden + durchklicken, dann nächste Scheibe.

1. **🟢 SICHERSTE — CSS auslagern** (`@section('style')` 12–3615 → `partials/_profile_styles.blade.php` via `@include`, oder als `.css` mit Umschreiben der **4** `asset()`). **Verhaltensneutral**, größter Zeilen-Gewinn (−3.604 Z.), null Logik-Risiko. **Hier anfangen.**
2. **🟢 Externe Script-Includes** (4970–5000) → in den Layout-`@push('scripts')`/`@section` verschieben. Reine CDN-Tags, kein Inline-Code.
3. **🟢 Gekapselte Content-Modals** je **einzeln** → `@include`-Partials: „Seriennummern", „Verlaufsdetails", „Teilweise erledigt", „Kontaktperson". Eigenes Markup, geringe Verflechtung; Daten via `@include(..., [...])` mitgeben.
4. **🟡 Die 2 Asides** (Objekt-Produkt-Baum, Aufgabenmanagement) → je ein Partial. Etwas mehr ID-/JS-Bezug — nach den Modals.
5. **🟡 JS-Bootstrap einführen** (noch nichts verschieben): einen Inline-Block anlegen, der die **25 `{{ route() }}`** + CSRF + benötigte IDs als `window.PROFILE_*`-Konstanten setzt. **Voraussetzung** für alle JS-Auslagerungen.
6. **🟠 Benannte JS-Module** je einzeln in `.js` ziehen (lesen die Bootstrap-Konstanten): `customer-profile-workflow-addon` → `wizard-object-script` → … Jeweils 1 Modul, testen.
7. **🔴 RISKANTEST — die großen unbenannten JS-Blöcke** (5032–5408, 6324–7371, …) blockweise, zuletzt. Hier sitzen Logik, Routen und JS-gebautes HTML eng beieinander → maximale Sorgfalt, kleine Schritte.

---

## 6. Die zweite Datei — `customer_view.blade.php`

- **Größe:** 9.860 Zeilen. `@section('content')` ab Z.4059 → davor ebenfalls ~4.000 Z. Inline-CSS/JS (dasselbe Muster).
- **Lebt sie? JA** — gerendert von **drei** NewLeadsController-Methoden: `index` (3683), `my_lead` (4792), `new_lead` (5068).
- **Was ist sie? KEINE Profil-Dublette**, sondern die **Lead-/KundenLISTE**: enthält `table-responsive`/`<table>` mit Sortierung (`sort_by=new_leads.customer_no/quelle`). Also die **Übersichts-/Listenansicht**, nicht das Einzelprofil.
- **Konsequenz für die Zerlegung:** Es sind **zwei getrennte Dateien mit unterschiedlichem Zweck** (Profil vs. Liste), **beide** mit dem Mega-Inline-Problem. Sie **erst getrennt** behandeln (eigener Schnittplan je Datei). Mögliche **gemeinsame CSS/JS-Bestandteile** zwischen beiden sind beim Auslagern auf Wiederverwendung zu prüfen (nicht zweimal kopieren) — aber das ist ein **späterer** Schritt.

---

## Zusammenfassung für die Freigabe der ersten Scheibe

- **Datei = 3 saubere Sektionen** (CSS 12–3615 · HTML 3616–4964 · JS 4969–23145), JS dominiert (78 %).
- **Erste, sicherste Scheibe: das CSS** (`@section('style')`) — fast Blade-frei (nur 4 `asset()`), verhaltensneutral, −3.604 Z.
- **JS ist der große Brocken** und braucht **zuerst einen Bootstrap-Konstanten-Block** (25 `{{ route() }}`), bevor irgendein JS-Modul nach `.js` wandert.
- **Zweite Datei** (`customer_view.blade.php`) ist die **lebende Lead-Liste**, kein toter Klon — separat behandeln.

---

*Reine Analyse — kein Code verschoben, keine Partials angelegt. Belege: Sektions-Grenzen (`@section`/`@endsection`), `<style>`/`<script>`-Zeilenbereiche, `{{ route() }}`/`{{ $item }}`/`@php`-Zählungen je Sektion, Content-Anker (Modal-/Aside-IDs, Karten-Überschriften), `NewLeadsController` (Methoden `view`/`index`/`my_lead`/`new_lead`). Querverweis: `kundenprofil-architektur-bestandsaufnahme.md`, `glossar.md`.*

---

# Scheibe 2: Content-Modals — Schnitt-Analyse

> **Stand:** nach Scheibe 1 (CSS ausgelagert, Commit `cbd92d8`). Die externe-Skripte-Scheibe wurde **verworfen** (Skripte sind bereits extern + mit Inline-JS verschachtelt → nicht verhaltensneutral schneidbar). Nächster Kandidat: der **`@section('content')`-Bereich, Z.25–1373** (1.348 Z.). **Reine Analyse — nichts geschnitten, keine Partials angelegt.**
>
> **Zeilenangaben** beziehen sich auf `customer_profile.blade.php` im **aktuellen** Zustand (19.554 Z., nach Scheibe 1).

## Grundlegendes zur Schnitt-Mechanik (gilt für ALLE Blöcke)

1. **Struktur:** Der Content-Bereich ist eine **flache Folge von Geschwister-`<div>`s** (Modals, Drawer, Overlays, Asides) — jeweils sauber balanciert (`<div id=…>…</div>`). Kein Block greift in offene Tags eines anderen. **Markup-seitig sind alle Top-Level-Blöcke sauber kapselbar.**
2. **Blade-Variablen-Vererbung:** `@include('…')` **erbt automatisch den kompletten Eltern-Scope**. Ein Block, der `$customer`/`$employees`/… nutzt, funktioniert nach dem Auslagern an **derselben Stelle** unverändert — die Variablen sind weiter im Scope. → **Verhaltensneutral.** Best Practice zusätzlich: Variablen **explizit** übergeben (`@include('…', ['customer' => $customer])`).
3. **JS-Anker:** Der große JS-Bereich (Z.1411–19.552, 63 Inline-Blöcke) spricht die Blöcke **ausschließlich über Element-IDs / `class` / `data-`-Attribute** an (`getElementById`, `querySelector`). Ein **wortgleicher** Umzug ins Partial (IDs unverändert, `@include` rendert inline an gleicher DOM-Position) lässt das JS die Elemente **exakt wie bisher** finden. → JS bleibt unangetastet, **IDs dürfen sich nicht ändern**.
4. **Die eigentliche Falle ist NICHT das Markup, sondern zwei lose `@php`-Datenblöcke** (Z.170–189, siehe unten) — die müssen **an Ort und Stelle bleiben**, weil ein nachfolgender `@include` sie konsumiert.

## Block-Landkarte (Z.25–1373)

| # | Block | Zeilen | Was / wofür | Blade-Daten | in sich geschl.? | JS-Anker (Beispiele) |
|---|---|---|---|---|---|---|
| A | Wrapper + **Profil-Include** | 27–36 | `app-content > content-body > @include(layouts.profile)` | (erbt alle) | **schon Partial** | — |
| B | **newProductModal** | 40–66 | BS-Modal „Neues Produkt hinzufügen"; enthält `@include(layouts.new_product_form)` | `route(lead_product_lists.bulk.store)` | ✅ | `#product_customer_id`, `#saveProductRows` |
| C | **reportSidebar** (+ reportFormContainer) | 69–130 | „Kundenprozessbericht"-Slider + Bericht-Formular (Quill) | `$stageLabels` (inline `@php`, self-contained), `now()` | ✅ | `#reportSidebar`, `#reportForm`, `#quill-editor` |
| D | **commentSidebar** (+ commentFormModal) | 134–168 | „Kommentare"-Slider + Kommentar-Formular | — | ✅ | `#commentSidebar`, `#newCommentForm`, `#report_id` |
| — | *glue: `@php $allEmployees`* | 170–173 | DB-Query, speist Block F **und** Include I | *definiert* `$allEmployees` | **kein Block — nicht schneiden** | — |
| — | *glue: `@php $docTypes`* | 175–189 | Array, speist Include I (customerEditDrawer) | *definiert* `$docTypes` | **kein Block — nicht schneiden** | — |
| E | **suggestEmployeesDrawer** | 193–220 | nx-Drawer „Mitarbeiter vorschlagen" | — (`@csrf`) | ✅ | `#suggestEmployeesDrawer`, `#employeeRows` |
| F | **editSuggestedEmployeeDrawer** | 223–286 | nx-Drawer „Mitarbeiter bearbeiten" | `@foreach($allEmployees)` (aus glue 170) | ✅ (aber Daten-gekoppelt) | `#editSuggestedEmployeeForm`, `#deleteSuggestedEmployee` |
| G | **halfDoneModal** | 288–324 | BS-Modal „Teilweise erledigt" | — | ✅ | `#halfDoneModal`, `#halfDoneForm` |
| H | **doneHistoryModal** | 326–342 | BS-Modal „Verlaufsdetails" | — | ✅ | `#doneHistoryModal`, `#doneHistoryContent` |
| I | **customerEditDrawer-Include** | 344–345 | `@include(partials.customerEditDrawer)` (konsumiert glue 170/175) | (erbt `$allEmployees`,`$docTypes`) | **schon Partial** | — |
| J | **customerContactPeopleModal** | 351–464 | Custom-Modal „Kontaktpersonen" (Tabelle + Formular) | `{{ $customer->id }}` (1×) | ✅ | `#customerContactPeopleModal`, `#contactPeopleTableBody`, `#contactPersonForm` |
| K | **addProductOverlay** | 466–691 | cp-Overlay „Neues Produkt" (Tabs Details/Galerie), groß | `@foreach($employees)` | ✅ (groß, 226 Z.) | `#addProductOverlay`, `#btnAddSave`, `#installed_by` |
| L | **editProductOverlay** | 693–912 | cp-Overlay „Produkt ansehen/bearbeiten" (Tabs), groß | `@foreach($employees)` | ✅ (groß, 220 Z.) | `#editProductOverlay`, `#btnEditSave`, `#btnToggleEditLock` |
| M | **serialsOverlay** | 917–949 | cp-Overlay „Seriennummern verwalten" (im Quelltext als *„already custom"* markiert) | — (**0 Blade**) | ✅ **isoliert** | `#serialsOverlay`, `#serialsModalBody`, `#btnSerialsModalSave` |
| N | **addCustomerProductModal** | 953–994 | cmodal „Produkt hinzufügen" (Zeilen-Tabelle) | `route(lead.products.save)` | ✅ | `#addCustomerProductModal`, `#modalNewRows`, `#modalAddRow` |
| O | **editCustomerProduct** | 997–1069 | cmodal „Produkt bearbeiten" | `@foreach($new_products)`, `@foreach($departments)`, `route(lead.products.update)` | ✅ | `#editCustomerProduct`, `#edit_product`, `#edit_department` |
| P | **objDrawerRoot** | 1071–1151 | Aside „Produkte zu Objekten zuordnen" + Objekt-Anlage + Google-Maps | — (**0 Blade**, aber Maps-JS) | ✅ (JS-schwer) | `#objDrawerPanel`, `#co_map`, `#drawerObjectsCols` |
| Q | **maHoverPreview / maLightbox** | 1153–1175 | Bild-Hover-Vorschau + Lightbox | — (CSS-Var `--ma-blue-soft`, Inline-`onclick`) | ⚠️ **Doppel-ID** (s. Backlog) | `#maLightboxImg`, `window.maCloseLightbox()` |
| R | **customerKanbanTaskDrawer** | 1178–1372 | Aside „Aufgabenmanagement" (Kanban-Tasks/Vorlagen), sehr groß | — (`@csrf`) | ✅ (sehr groß, 195 Z.) | `#customerKanbanTaskDrawer`, `.kb-enterprise-card`, `#customerKanbanManualTaskForm` |

**Daten-Abhängigkeiten kompakt:** `$customer`→J · `$employees`→K,L · `$allEmployees`→F (def. in glue 170) · `$docTypes`→Include I (def. in glue 175) · `$new_products`,`$departments`→O · `$stageLabels`→C (block-intern) · Routen→B,N,O.

## Schnitt-Eignung — Rangliste (am saubersten → am riskantesten)

**Tier 1 — 0 Blade-Variablen, klein, isoliert (ideale erste Schnitte):**
1. **M — serialsOverlay** (917–949, 33 Z.) — 0 Blade, im Quelltext selbst als eigenständig markiert, kleiner Blast-Radius. ★
2. **H — doneHistoryModal** (326–342, 17 Z.) — 0 Blade, winzig.
3. **G — halfDoneModal** (288–324, 37 Z.) — 0 Blade.
4. **E — suggestEmployeesDrawer** (193–220) — 0 Blade (`@csrf`).
5. **D — commentSidebar** (134–168) — 0 Blade.

**Tier 2 — genau 1 einfache Blade-Abhängigkeit (Route oder 1 Variable):**
6. **N — addCustomerProductModal** (nur Route) · 7. **C — reportSidebar** (`$stageLabels` block-intern) · 8. **B — newProductModal** (Route + verschachtelter Include) · 9. **J — customerContactPeopleModal** (`$customer`).

**Tier 3 — Daten-gekoppelt oder groß:**
10. **F — editSuggestedEmployeeDrawer** (braucht `$allEmployees` aus loser glue-`@php`) · 11. **O — editCustomerProduct** (2 Var + Route) · 12. **K — addProductOverlay** (226 Z., `$employees`) · 13. **L — editProductOverlay** (220 Z., `$employees`).

**Tier 4 — groß/JS-schwer (später, einzeln, mit Extra-Vorsicht):**
14. **P — objDrawerRoot** (Google-Maps-Init an IDs gebunden) · 15. **R — customerKanbanTaskDrawer** (195 Z., viele Kanban-Anker).

**Nicht schneiden:** A + I (schon Partials) · glue-`@php` 170–189 (Daten-Prep, muss vor Include I stehen bleiben) · **Q** (Doppel-ID zuerst bereinigen → Backlog).

## Empfehlung: Scheibe 2a = **Block M (serialsOverlay, Z.917–949)**

**Warum genau dieser Block zuerst:**
- **0 Blade-Variablen** → das Partial ist ein reiner statischer `@include` ohne Argumente; trivial verhaltensneutral (kein Datenfluss zu prüfen).
- **Vollständig in sich geschlossen:** ein Top-Level-`<div id="serialsOverlay" class="cp-overlay">…</div>`, balanciert, klarer Anfang/Ende.
- **Klar abgegrenzte Semantik** („Seriennummern verwalten") — im Quelltext bereits als eigenständiger, „custom" Baustein gekennzeichnet.
- **Klein (33 Z.)** → Byte-Gleichheit des verschobenen Markups leicht verifizierbar, minimaler Blast-Radius — perfekt, um das Partial-Muster **einmal sauber zu etablieren**.
- **JS-Anker** (`#serialsModalProductName`, `#serialsModalBody`, `#serialsModalCount`, `#btnSerialsModalSave`, `#snCloseBtn`, `#snCancelBtn`) bleiben **wortgleich**; das JS im großen Block findet sie unverändert per ID.

**Schnitt-Plan 2a (nach separatem OK, NICHT jetzt):** neues Partial `resources/views/admin/new_leads/partials/modals/serials_overlay.blade.php` (Markup Z.917–949 **wortgleich**), im Blade durch **eine** `@include('admin.new_leads.partials.modals.serials_overlay')`-Zeile ersetzen. **Verifikation:** Profil `/new_lead_profile/105` → 200; Seriennummern-Overlay öffnet/funktioniert (JS findet IDs); Byte-Vergleich des verschobenen Markups; vorher/nachher-Zeilen. → Danach 2b/2c = **H** und **G** (weitere 0-Blade-Modals) als natürliche Folgeschnitte.

> **Hinweis Namenskonvention:** Es existiert bereits `resources/views/admin/new_leads/partials/` mit ~18 Partials (u. a. `customerEditDrawer`, `contact_info`, `report`, `single_comment`) und ein `partials/edit/`-Unterordner. Ein neuer `partials/modals/`-Unterordner fügt sich sauber ein. Muster ist im Repo also etabliert.

## Backlog (getrennt festgehalten — NICHT Teil einer Schnitt-Scheibe, jetzt nicht anfassen)

1. **Skript-Dubletten:** `Sortable.min.js` wird 2× geladen (Z.1382 `sortablejs@1.15.0` **und** Z.1408 cdnjs `Sortable/1.15.0`); `chart.js` wird 2× geladen (Z.1389 **und** Z.1409). → Doppelte Netzwerk-Requests, harmlos, aber unsauber. Entfernen ist eine **Verhaltensänderung** (Tag weg) → eigener Bugfix mit eigenem Pflicht-Stopp.
2. **Kaputte Referenzen (vorbestehend, älter als Scheibe 1) → 404:** Z.21 `object-context-menu-final.css` **und** Z.19553 `object-context-menu-final.js` verweisen auf Dateien mit Suffix `-final`, die **nicht existieren** (live geprüft: HTTP 404). Auf der Platte liegen `public/css/object-context-menu.css` / `public/js/object-context-menu.js` (**ohne `-final`**). → Das Objekt-Kontextmenü lädt aktuell **gar nicht**. Fix = Referenz korrigieren **oder** Datei umbenennen; eigener Pflicht-Stopp.
3. **Doppelte DOM-ID `maHoverPreviewOverlay`** (Block Q): identisch bei **Z.1153 und Z.1173** (inkl. Kind-`<img id="maHoverPreviewImg">`). Ungültiges HTML (ID-Duplikat); der zweite Block ist toter/verschatteter Code. → Vor dem Auslagern von Block Q **zuerst deduplizieren**; eigener Pflicht-Stopp.

---

*Reine Analyse für Scheibe 2 — kein Code verschoben, keine Partials angelegt. Belege: vollständige Lektüre `@section('content')` Z.25–1373; Block-Grenzen per `<div id=…>`/Schließ-Tags; Daten per `@php`/`@foreach`/`{{ $… }}`-Scan; JS-Anker per ID-Referenzen; Live-404-Check `object-context-menu-final.*`; Doppel-ID-Grep. Querverweis: `kundenprofil-architektur-bestandsaufnahme.md`, `glossar.md`.*
