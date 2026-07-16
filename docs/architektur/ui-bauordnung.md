# UI-Bauordnung — Styleguide-Pflicht, Echtdaten-Prüfung, visuelle Regression

**Stand:** 2026-07-16 · Erweiterung der Bauordnung (steht UNTER BETRIEBSORDNUNG.md/CLAUDE.md; bei Konflikt gelten diese).
**Gilt für:** jede Instanz, die in ticket UI baut oder ändert (Generator wie Evaluator).

---

## §1 · Styleguide-Pflicht (die lebende Komponentenbibliothek)

Unter **`/admin/styleguide`** (Route `styleguide.index`, View `resources/views/admin/styleguide/index.blade.php`) liegt jede UI-Grundform **genau einmal, mit allen Zuständen**, gebaut aus den sa-ui-Tokens: Farbwelt, Seitenkopf (`<x-page-head>`), Buttons, Status-Pills, Formularfelder, Tabelle, Filterleiste, Modal.

1. **Vor jedem neuen UI-Element wird der Styleguide geprüft.** Existiert die Komponente → sie wird verwendet, nicht nachgebaut.
2. **Existiert sie nicht → sie wird ZUERST im Styleguide angelegt** (mit allen Zuständen: normal, Hover, Fokus, Fehler, inaktiv, leer), dann in der Zielseite eingesetzt.
3. **Farbwerte nur über Tokens** (`var(--sa-…)` bzw. daran verdrahtete Scope-Tokens wie `--al-…`). Kein hartkodierter Hex in Views — Ausnahme: die Token-Dateien selbst und `@media print`.
4. **Ein-Schreiber-Regel:** Styleguide-View + Token-Dateien (`partials/sa-ui.blade.php`, `arbeitsliste/_tokens.blade.php`, `components/page-head.blade.php`) sind ein eigener Strang. Andere Stränge lesen, schreiben nicht.

**Verbote (Yamas Farbwelt, gemessen):** kein Schwarz als Schrift-/Flächenfarbe (Dunkelgrau `#1f2937`; reines `#000` nur im Druck), kein Navy/Dunkelblau, kein Fremdblau, kein Fremdrot (nur `#ef4444`-Familie), dunkle Buttons verboten.

## §2 · Echtdaten-Prüfung (nicht mit Demozeilen abnehmen)

CRM-Ansichten sehen mit 3 Demozeilen immer gut aus und brechen bei 3.000 Kunden. Deshalb gilt: **eine UI-Fläche ist erst fertig, wenn sie mit den hässlichen Realfällen hält.**

**Pflicht-Extremfälle** (als Seeder-Zustand `UiExtremfaelleSeeder` anzulegen — NUR lokale Dev-DB, additiv, nie Hetzner):

| Fall | prüft |
|---|---|
| Kunde mit sehr langem Namen (80+ Zeichen, & und Umlaute) | Umbruch, Abschneiden, Tabellen-Spalten |
| Auftrag mit 40+ Positionen | Scrollen, Summenzeile, Druck |
| Ticket ohne Kunde/Zuordnung | Null-Anzeige statt Fehler |
| Überfällige Rechnung (Mahnstufe, negativer Saldo) | Statusfarben, Vorzeichen |
| Lead ohne E-Mail/Telefon | leere Felder, Aktions-Buttons |
| Liste mit 0 Einträgen und mit 500+ Einträgen | Leerzustand, Performance, Paginierung |

**Drei Pflicht-Viewports:** 1440px (Büro) · 1024px (Tablet auf der Baustelle) · 375px (Telefon).

## §3 · Visuelle Regression (der Screenshot-Loop)

1. **Referenzfläche ist der Styleguide:** Nach jeder Welle wird `/admin/styleguide` in den drei Viewports geschossen und gegen den letzten Stand gedifft. Ein unbeauftragter Diff = ein Agent hat globales CSS angefasst → Stopp, Ursache, Korrektur (R7).
2. **Je geänderter Fläche:** Screenshot mit dem Extremfall-Seeder-Zustand, nicht mit Leer-DB. Der **Evaluator** prüft die Screenshots — der Generator nimmt nicht selbst ab (Drei-Rollen-Zyklus).
3. Werkzeug: Playwright lokal (läuft auf Yamas Rechner gegen die Dev-Umgebung); Screenshots als Belege in die Übergabe.
4. **Rate-nicht-Regel, visuell:** nicht schätzen, ob es passt — rendern und ansehen.

## §4 · Abnahme-Gate UI (ergänzt die 10 Fragen der Bauordnung)

Vor jedem Produktiv-Commit mit UI-Anteil zusätzlich: **(a)** Styleguide geprüft/ergänzt? **(b)** Tokens statt Hex? **(c)** Extremfälle gerendert? **(d)** 3 Viewports gesehen? **(e)** Styleguide-Diff sauber? — Eine Nein-Antwort ohne Begründung = kein Commit.
