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
