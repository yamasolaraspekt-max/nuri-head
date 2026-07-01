# Kundenprofil-Kartierung — HART gegen den Code verifiziert

> **Reine Analyse (nur Lesen), kein Code geändert.** Auftrag: die Kartierung (`kundenprofil-architektur-bestandsaufnahme.md` + `kundenprofil-zerlegung-schnittplan.md`) kritisch gegen den echten Code prüfen — aktiv Fehler suchen, nicht bestätigen. Jede Aussage mit Zahl/Zitat.
>
> **Gesamturteil vorweg: Die Kartierung war GRÜNDLICH, nicht oberflächlich.** Die überprüfbaren Kernzahlen sind **exakt** (Original 23.145 Z., Sektionsgrenzen, %-Aufteilung, 25 `route()` im JS, `{{ $item }}`=0, `NewLeadsController@view`→`NewLeads`, 4/4 Block-Ranges, 5/5 Partials sauber). **Kleine Korrekturen** (keine Fehler im Kern): aktuelle Größe 19.409 (nicht 19.470 — Scheibe 2.3 kam danach); „~105 AJAX" = real 103 Call-Sites; „JS Blade-frei" ist nicht 100 % (3 `{{ $emp }}` im JS); die Map-Zeilennummern sind nach den Schnitten **verschoben** (vor dem nächsten Schnitt neu ableiten). Ein von der Map **nicht erwähnter** Punkt: das Profil-JS ruft `/customers/…`-URLs — aber die sind **URL-Namensfalle, keine Daten-Falle**.

---

## 1. Dateigröße + Struktur

| Behauptung (Map) | Tatsächlich | Urteil |
|---|---|---|
| Original ~23.145 Z. | `git show cbd92d8~1` = **23145** | ✅ **exakt** |
| aktuell ~19.470 Z. | `wc -l` = **19409** | ⚠️ **stale** (2.3 kam danach; korrekt 19.409) |
| Reduktion | 23.145 → 19.409 = **−3.736** | ✅ korrekt |
| Sektionen CSS 12–3615 · HTML 3616–4964 · JS 4969–23145 | im Original exakt: `@section('style')` 12 / `@endsection` 3615 · `content` 3616/4964 · `script` 4969/23145 | ✅ **exakt** |
| %-Aufteilung 16 / 6 / 78 | CSS 3604/23145=**15,6 %** · HTML 1349/23145=**5,8 %** · JS 18177/23145=**78,5 %** | ✅ korrekt (gerundet 16/6/78) |

→ **Punkt 1: bis auf die eine stale Zahl (19.470→19.409) exakt.** Die Reduktion und die Struktur-Behauptung stimmen zeichengenau.

## 2. AJAX / Routen / Blade-Variablen im JS

| Behauptung | Tatsächlich | Urteil |
|---|---|---|
| ~105 AJAX-Calls | `fetch(` **65** + jQuery(`ajax/post/get/getJSON`) **38** = **103 Call-Sites** (`CP.api.` **9** sind Helfer INNERHALB dieser) | ✅ im Wesentlichen korrekt (±2). *NICHT VERIFIZIERT als „distinkte Routen" — ich zählte Call-Sites.* |
| „25 `{{ route() }}` im JS-Bootstrap" | im JS-Teil (≥1233): **25** (von 28 gesamt) | ✅ **exakt** |
| „0× `{{ $item }}`" | `grep -c "{{ $item"` = **0** | ✅ **exakt** |
| (implizit) JS Blade-frei | **3** `{{ $var }}` im JS-Teil: Z.10264 `@foreach($allEmployees)` → `{{ $emp->id/name/lastname }}` (ein in JS eingebettetes `<select>`) | ⚠️ **kleine Korrektur:** JS ist **nicht** 100 % Blade-frei — 3 Emp-Interpolationen. Die spezifische `{{ $item }}`-Aussage stimmt aber. |

→ **Punkt 2: die konkreten Behauptungen (25 route, 0 `$item`) sind exakt; „~105" ist real 103; „Blade-frei" ist mit 3 Emp-Vars minimal zu optimistisch.**

## 3. Die kritische Architektur-Behauptung — hängt das Profil an `new_leads`?

**JA, hart bestätigt.**
- Speisender Controller (wörtlich, `routes/web.php:795`): `Route::get('/new_lead_profile/{id}', [NewLeadsController::class, 'view'])`. ✅
- `NewLeadsController@view` (Z.1907), wörtlich Z.1936: `$customer = \App\Models\NewLeads::query()->find($id);` → **`$customer` ist ein `NewLeads`, KEIN `Customer`.** ✅
- Im Blade: `Customer::` = **0**, `$customer->` = 4 Zeilen (id/name/lastname/title). ✅

**Aktiv nach versteckter Falle gesucht — und einen NICHT-erwähnten Punkt gefunden:** Das Profil-JS ruft **`/customers/…`-URLs** (Z.9561/9670/9740/9860/11748/11753):
- `/customers/{customer}/price-history` → **`NewLeadsController@priceHistoryForCustomer`** ✅
- `/customers/{customer}/purchase-summary` → **`NewLeadsController@purchaseSummary`** ✅
- `/customers/{customer}/total-purchase` → **`NewLeadsController@updateTotalPurchase`** ✅
- `customers.contact-people.*` → **`CustomerContactPersonController`** (nutzt `CustomerContactPerson` + `NewLeads` + `employees` + `lead_activity_logs`; **`Customer::`=0, `table('customers')`=0**) ✅

→ **Die `/customers/`-URLs sind eine reine Namensfalle (URL sagt „customers", Controller ist NewLeads/new_leads).** **Keine** Daten-Customer-Falle. Die Map-Aussage „Profil hängt sauber an new_leads" **hält** — auch in der AJAX-Schicht. **Aber:** die Map hat diese `/customers/`-URLs **gar nicht erwähnt** — eine kleine **Vollständigkeits-Lücke** (kein Fehler, weil harmlos).

## 4. Block-/Modal-Landkarte + erledigte Partials

**Block-Ranges (Stichprobe gegen den Map-Referenzstand `cbd92d8` = 19.554 Z.):**
| Map sagt | echt in cbd92d8 (Z. X) | Urteil |
|---|---|---|
| addProductOverlay @466 | `id="addProductOverlay"` | ✅ exakt |
| editProductOverlay @693 | `id="editProductOverlay"` | ✅ exakt |
| objDrawerRoot @1071 | `id="objDrawerRoot"` | ✅ exakt |
| customerKanbanTaskDrawer @1178 | `id="customerKanbanTaskDrawer"` | ✅ exakt |

→ **4/4 stichprobenartig geprüfte Block-Ranges exakt.** ⚠️ **Caveat:** die Ranges gelten für `cbd92d8` (19.554); im **aktuellen** File (19.409) sind sie durch die 5 ausgelagerten Blöcke **verschoben** — die Map ist korrekt-zum-Zeitpunkt, aber die Zeilennummern müssen vor dem nächsten Schnitt neu abgeleitet werden.

**Erledigte Partials (alle 5):**
```
serials_overlay: Datei=JA @include=1x        | id=serialsOverlay im Blade=0
half_done_modal: Datei=JA @include=1x        | id=halfDoneModal=0
done_history_modal: Datei=JA @include=1x      | id=doneHistoryModal=0
comment_sidebar: Datei=JA @include=1x         | id=commentSidebar=0
suggest_employees_drawer: Datei=JA @include=1x| id=suggestEmployeesDrawer=0
```
→ **5/5 Partials existieren, je 1× eingebunden, Block-IDs 0× im Blade — die erledigten Scheiben sind sauber. ✅**

## 5. Was hat die Map übersehen?

- **`/customers/…`-URL-Namensfalle** (Punkt 3) — nicht erwähnt, aber harmlos.
- **Doppel-ID `maHoverPreviewOverlay`:** aktuell **2×** im Blade → die Backlog-Behauptung der Map ist **korrekt** ✅.
- **3 `{{ $emp }}` im JS** (Punkt 2) — die „Blade-frei"-Nuance.
- **Kein zusätzliches, übersehenes Modal gefunden** (die A–R-Abdeckung wirkt vollständig; `id="…Modal/Overlay/Drawer…"`-Scan zeigt genau die kartierten Blöcke).
- **Rangliste „sicher → riskant":** die 5 als „Tier-1 sicher" eingestuften 0-Blade-Blöcke wurden inzwischen **sauber ausgelagert** (Punkt 4) → die „sicher"-Einstufung war **korrekt**. *NICHT VERIFIZIERT:* die „riskanteren" Blöcke (K/L/P/R) habe ich **nicht** auf verstecktes Interleaving nachgeprüft — nur die Startzeilen stichprobenartig.

## 6. Gesamturteil

**GRÜNDLICH.** Begründung: jede hart überprüfbare Kernzahl war **exakt** (Original-Größe, Sektionsgrenzen, %-Split, 25 route im JS, `{{ $item }}`=0, `@view`→NewLeads, 4/4 Block-Ranges, Doppel-ID, 5/5 Partials). Das ist kein Muster oberflächlicher Arbeit.

**Was falsch/ungenau war (alles klein, nichts im Kern):**
- Größe „19.470" → real **19.409** (stale, weil Scheibe 2.3 nach dem Doc kam).
- „~105 AJAX" → real **103** Call-Sites (essentiell richtig).
- „JS Blade-frei" → **3** `{{ $emp }}` im JS (die `{{ $item }}`-Aussage selbst stimmt).
- Map-Zeilennummern jetzt **verschoben** (nach 5 Schnitten).
- `/customers/`-URLs **nicht erwähnt** (harmlos, Namensfalle).

**Was exakt richtig war:** Original 23.145; Sektionen 12/3616/4969; %-Split 16/6/78; 25 route im JS; `{{ $item }}`=0; `NewLeadsController@view`→`NewLeads`; alle 4 Block-Ranges; Doppel-ID `maHoverPreviewOverlay`; 5/5 Partials sauber.

**Kann man darauf weiter sicher aufbauen? JA — mit einer Regel:** Vor jedem weiteren Schnitt die **aktuellen** Zeilenbereiche des Ziel-Blocks neu ableiten (die Map-Nummern sind post-Schnitt verschoben) und die Datenabhängigkeiten am konkreten Block prüfen — **genau das Vorgehen der bisherigen Scheiben.** Die Kartierung ist eine verlässliche Landkarte, kein Foto mit aktuellen Koordinaten.

---

## Gelesen / NICHT gelesen (ehrlich)

**Vollständig gelesen/gezählt:** `wc -l`/`git show` (aktuell + cbd92d8~1 + cbd92d8); `@section`-Grenzen (Original + aktuell); AJAX-/route-/`{{ $ }}`-Zählungen (grep -c/-o, ganzes File); `NewLeadsController@view` (Methoden-Kern 1907–2537, die `$customer`-Ladezeile); die 6 `/customers/`-Routen in `routes/web.php` + deren Controller; `CustomerContactPersonController` (grep Customer::/customers/Modelle); 4 Block-Startzeilen in `cbd92d8`; 5 Partial-Dateien + `@include`-Zähler + Block-ID-Gegenprobe; Doppel-ID-Zähler.

**Nur gegrept / gestichprobt / NICHT VERIFIZIERT:**
- **Nur 4 von 18 Block-Ranges** stichprobenartig geprüft — die übrigen 14 (B, C, F, I, N, O, Q u. a.) **NICHT** einzeln gegen den Code.
- AJAX als **Call-Sites** gezählt, **nicht** als distinkte Routen — die „105 Routen"-Lesart bleibt offen.
- `NewLeadsController` **nicht** vollständig gelesen (nur `view`); `CustomerContactPersonController` **nur gegrept**, nicht ganz gelesen.
- Runtime der `/customers/`-AJAX **nicht** getestet (DB leer).
- „riskante" Blöcke K/L/P/R **nicht** auf Interleaving nachgeprüft.

## Schwächen meiner eigenen Prüfung
- Stichprobe (4/18 Blöcke) ist **kein** Vollbeweis der Block-Landkarte — nur ein starkes Indiz, dass sie stimmt.
- Ich habe die Map an ihren **überprüfbaren** Zahlen gemessen; qualitative Aussagen (z. B. „Datenabhängigkeit je Block") habe ich nur an den bereits geschnittenen Blöcken bestätigt, nicht an allen.
- „Kein übersehenes Modal" beruht auf einem ID-Muster-Grep — ein Modal mit unkonventioneller ID (ohne Modal/Overlay/Drawer im Namen) könnte entgehen.

---

*Reine Analyse — nichts geändert. Belege: `wc -l`/`git show cbd92d8~1|cbd92d8`; `@section`-grep (Original+aktuell); `routes/web.php:795` (`NewLeadsController@view`), `:796/1142/1143` (/customers/→NewLeadsController), `:1376–1386` (contact-people→CustomerContactPersonController); `NewLeadsController@view:1936` (`NewLeads::query()->find`); Blade-Zählungen (fetch 65 / jQuery 38 / CP.api 9 / route-JS 25 / `{{ $item }}` 0 / `{{ $emp }}`@10264); Partial-Existenz + `@include`-Zähler + Block-ID-Gegenprobe; `maHoverPreviewOverlay`=2. Querverweis: `kundenprofil-architektur-bestandsaufnahme.md`, `kundenprofil-zerlegung-schnittplan.md`, `customer-model-falle-befund.md`, `glossar.md`.*
