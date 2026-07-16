# CLAUDE.md — ticket CRM (Projekt-Governance)

> Modul-/Bereichs-Regeln für dieses Repository. Additiv gepflegt.

> **⛔ OBERSTES DOKUMENT: [`docs/BETRIEBSORDNUNG.md`](docs/BETRIEBSORDNUNG.md)** — autonomer Mehrstrang-Betrieb (Rollen BAUER/PRÜFER/KOORDINATOR, Gates G1–G9, Vollmacht + Restgrenze, FiBu-Sondergates). Bindet JEDE Instanz; bei Konflikt mit einem Auftragstext gilt die Betriebsordnung. Ändert nur Yama.

> **⛔ BAU-ORDNUNG (verbindlich): Vor jedem Produktiv-Commit: Selbstprüfung gegen [`docs/architektur/bauordnung.md`](docs/architektur/bauordnung.md) §5 (die 10 Fragen).** Die Bauordnung (Schichten-/Daten-/Sicherheits-/Bau-Prozess-Regeln, abgeleitet aus dem Code-Audit) ist verbindlich und steht **unter** BETRIEBSORDNUNG.md/CLAUDE.md — bei Konflikt gelten diese. Jeder Agent liest sie bei jedem Start.

> **📍 „LIVE"/„PRODUKTION" = LOKALE DEV-DB (Yama-Klarstellung 2026-07-08).** Im laufenden Programm meint „live" die **lokale** DB (`ticket`/`ticket_testing`) auf Yamas Rechner — dort ist freies Arbeiten erlaubt (Migrationen, Seeder, Daten-Bereinigung inkl. UPDATE/DELETE als beauftragter Posten). **Hetzner-Produktion (3000 Kunden) wird NICHT angefasst** — erst am separaten Deploy-Tag, den Yama auslöst. Die „Produktion off-limits"-Regel gilt weiterhin **NUR für Hetzner**. *(Die DAUERDIREKTIVE unten schützt die ticket-Datenintegrität auch lokal: destruktive Schritte bleiben eigene, beauftragte, belegte Posten — kein Beifang.)*

> **## Optimierungs-Arbeitsmodus: Drei-Rollen-Zyklus (Standard, ab 2026-07-09).** Für JEDEN Optimierungs-/Verbesserungs-Auftrag am CRM/ERP gilt der Drei-Rollen-Zyklus **PLANNER → GENERATOR → EVALUATOR** aus [`docs/agents/00-zyklus.md`](docs/agents/00-zyklus.md) als verbindlicher Standard-Arbeitsmodus. Bindend: getrennte Instanzen (**Generator ≠ Evaluator zwingend**), jede Übergabe schriftlich mit Belegen, Evaluator-Veto (kein Commit ohne Freigabe), **Yama ist finaler Freigeber vor jedem Produktiv-Commit**. Der Zyklus setzt BETRIEBSORDNUNG.md + bauordnung.md DURCH und hebt sie nicht auf; bei Konflikt gilt Betriebsordnung/CLAUDE.md. *(Dringende Sicherheits-P0/P1-Fixes dürfen Yama-direkt laufen; der Zyklus ist der Standard für reguläre Optimierungs-Posten.)*

> **⛔ CLAUDE-CODE-STARTPFLICHT: Agenten sofort aktivieren (dauerhaft, ab 2026-07-10).** Bei JEDER neuen Claude-Code-Arbeit in diesem Repository gilt automatisch [`docs/agents/04-claude-code-startanweisung.md`](docs/agents/04-claude-code-startanweisung.md). Claude Code muss vor fachlicher Arbeit die Agenten-Unterlagen `docs/agents/00-zyklus.md`, `01-planner.md`, `02-generator.md`, `03-evaluator.md` sowie die Fundament-Dokumente lesen/anwenden. Der Standardmodus ist **Planner zuerst**: keine Umsetzung ohne Bestandsaufnahme, Ist-Belege, Konzept und Arbeitspakete. Wenn Claude Code echte Subagent-/Task-Werkzeuge bereitstellt, werden Planner/Generator/Evaluator als getrennte Instanzen genutzt; wenn nicht, werden die Rollen streng sequenziell im Hauptthread simuliert und jede Rollenübergabe schriftlich protokolliert. Reserve-Skills werden nur bei Bedarf aktiviert.

> **🎯 PFLICHT-FACHAGENTEN für Produkt/Frontend/Workflow/Architektur (dauerhaft, ab 2026-07-10).** Bei Aufgaben zu Frontend, Design, Layout, UX, Wizard, App-Konzept, Workflow, Prozess oder Architektur gilt zusätzlich [`docs/agents/05-fachagenten-produkt-architektur-frontend.md`](docs/agents/05-fachagenten-produkt-architektur-frontend.md). Claude Code muss dann die Perspektiven **Konzeption-Agent**, **Workflow-Agent**, **Architektur-Agent** und **Frontend-Design-Agent** in Planner und Evaluator explizit abarbeiten. Für Wärmepumpen-Auslegung und andere Energie-/Auslegungs-Wizards sind alle vier Fachagenten Pflicht; UI-Änderungen brauchen Browser-/Screenshot-Prüfung, außer es handelt sich nur um Dokumentation. **Diese Regel gilt nicht rückwirkend als Auftrag, bereits abgeschlossene Slices erneut umzubauen** — sie bindet neue sichtbare Änderungen.

> **🎨 STYLEGUIDE-PFLICHT + VISUELLE REGRESSION (dauerhaft, ab 2026-07-16).** Für JEDES UI-Element gilt [`docs/architektur/ui-bauordnung.md`](docs/architektur/ui-bauordnung.md): **Vor jedem neuen UI-Element `/admin/styleguide` prüfen. Existiert die Komponente, wird sie verwendet. Existiert sie nicht, wird sie ZUERST dort angelegt, dann eingesetzt.** Farbwerte nur über sa-ui-Tokens (kein Hex in Views außer Token-Dateien + `@media print`). Abnahme nur mit Echtdaten-Extremfällen in 3 Viewports (1440/1024/375); der Styleguide ist die Referenzfläche der visuellen Regression (Screenshot-Diff je Welle). Styleguide + Token-Dateien sind ein eigener Strang (Ein-Schreiber-Regel).

> **🧭 ARBEITSKOMPASS / FAHRPLAN-PFLICHT (dauerhaft, ab 2026-07-10).** Bei jeder größeren Aufgabe zuerst [`docs/arbeitskompass-ticket.md`](docs/arbeitskompass-ticket.md) prüfen und die Aufgabe dort einordnen: aktueller Fokus, nächster Schritt, Blocker oder Parkplatz. Diese Datei hält fest, woran gerade gearbeitet wird, wann ein Fokus fertig ist und was als Nächstes kommt. Keine neue große Baustelle ohne Eintrag oder bewusste Yama-Entscheidung. **Rang/Funktion:** Der Arbeitskompass dient ausschließlich der **Navigation, Priorisierung und Statusübersicht** und verweist auf die führenden Fahrpläne, ADRs und Startblöcke. Er **ersetzt oder überstimmt weder** BETRIEBSORDNUNG, `CLAUDE.md`, `STRAENGE.md`, ratifizierte ADRs **noch den freigegebenen Startblock eines aktiven Slices**. Bei Widersprüchen gilt die festgelegte Quellenhierarchie; der **aktive Startblock bestimmt den konkreten Slice-Scope**. Der Arbeitskompass ist keine eigenständige fachliche Wahrheit.

> **📚 WISSENS-REGISTER (ab 2026-07-09).** Vor neuen Aufgaben zuerst `~/wissensregister/register.md` + `ideensammlung.md` nach Themen-Tags durchsuchen — dort liegt Yamas gesichtetes Material (CRM/ERP/Energie-Prototypen, Fachdaten, Norm-Referenzen, Ideen) als **Verweis-Index** (keine Kopien, außerhalb Git aus Datenschutz). Passende Einträge → Originaldatei bei Bedarf voll lesen; so fließt vorhandenes Wissen/Code in neue Arbeit ein statt neu erfunden zu werden. Der **Planner-Agent** nutzt `ideensammlung.md` als Input-Quelle. **SENSIBEL** markierte Einträge (Kundendaten/Förder-/Geschäfts-/Credential-Dateien) nur referenzieren, **nie Inhalt zitieren/kopieren**. Aktualisieren: „Wissens-Register aktualisieren" (inkrementell via `scan-log.md`).

> **⛔ AUTOMATISIERUNGS-PRINZIP (dauerhaft, ab 2026-07-09).** „Sinnvoll automatisiert" ≠ „maximal automatisiert". Jede Automatisierung, die eine **Fach-/Rechtsentscheidung** dem Menschen abnimmt, folgt dem **Operanden-Gate**: **kein erfundener Wert — bei Unsicherheit/fehlenden Operanden wird gefragt oder markiert (Vorschlag + Bestätigung), nie stillschweigend weitergerechnet** (wie in der Formular-Calc-Engine + Anforderungsprofilen). Drei Klassen bei jedem Automatisierungs-Vorschlag benennen: **(a)** soll automatisiert sein (sicher ableitbar, spart Arbeit) · **(b)** darf NICHT voll automatisiert sein (Fach-/Rechtsentscheidung → nur Vorschlag+Bestätigung) · **(c)** ist automatisiert, aber falsch/riskant → korrigieren. Bindet Planner/Generator/Evaluator + jeden Optimierungs-Posten.

> **⛔ SYSTEMWEITE OPTIMIERUNGS-REIHENFOLGE (dauerhaft, ab 2026-07-11).** Für JEDE größere Domäne gilt die Reihenfolge: **① konzeptionell optimieren → ② Workflow bestimmen → ③ vorhandene Bausteine verknüpfen → ④ erst dann automatisieren**. Keine Automatisierung vor Konzept + Workflow + Verknüpfungsplan. Keine Parallelberechnung, keine zweite Wahrheit, kein UI-Flicken über falscher Fachlogik. Vor dem Bauen muss Claude Code belegen: Welche vorhandenen Services/Daten/Controller gibt es? Was wird wiederverwendet? Wo ist die führende Wahrheit? Welche Operanden fehlen? Was darf automatisch laufen und was braucht Vorschlag + Bestätigung? Diese Regel gilt systemweit, besonders für Angebot/Auslegung/WP, CRM, Auftrag, Beschaffung, Disposition, Montage, Abnahme, FiBu und Controlling.

> **📘 KAPITEL-FAHRPLAN FÜR OPTIMIERUNGEN:** Die konkrete Schrittfolge steht in [`docs/systemoptimierung-fahrplan.md`](docs/systemoptimierung-fahrplan.md): Inventur → kritische Bewertung → Optimierungsvorschlag → Ziel-Fahrplan → Verknüpfung → Workflow → Automatisierung → Umsetzungspakete → Prüfung/Abnahme. Claude Code muss bei jeder größeren Domäne sagen, bei welchem Kapitel er startet, wo die Runde endet und welches Ergebnisdokument entsteht.

> **⛔ KAPITEL-STARTBLOCK-PFLICHT:** Vor JEDEM Kapitel aus `docs/systemoptimierung-fahrplan.md` muss Claude Code zuerst einen Startblock liefern: Domäne, Kapitel, Startpunkt, Ziel, Analyseumfang, konkret zu lesende Dateien/Services/Datenquellen, Nicht-Ziele, Vorgehensweise Schritt für Schritt, Ergebnisdokument, Stop-Kriterium und ob Yama-Abnahme erforderlich ist. Ohne diesen Startblock keine Analyse, keine Umsetzung und keine Automatisierung.

## ⛔ DAUERDIREKTIVE: DATEN- UND KETTEN-SCHUTZ (ab 2026-07-05, strang-übergreifend & dauerhaft)

**Bindet JEDE Instanz und JEDEN playground→ticket-Schritt (Migration · Seeder · Import · Cut-over · FiBu).**

1. **TICKET-DATEN SIND UNANTASTBAR.** Kein Transplantations-Schritt darf **bestehende ticket-Zeilen ändern oder löschen**. Erlaubt sind **nur additive** Operationen: neue Tabellen · neue Spalten (**nullable oder mit Default**) · neue Zeilen. **Jeder UPDATE/DELETE auf Bestandsdaten ist ein eigener, explizit von Yama zu beauftragender Posten — niemals Beifang** eines Transplantats.

2. **DIE BELEGKETTE IST GESETZT — FiBu DOCKT AN, BAUT NICHT UM.** Bestehende Kette in ticket:
   **Angebot** (aus Sets, Sets aus Artikeln) → **Auftrag** → **Rechnung(en)** [`invoices` = führende Schiene, Yama-Entscheidung 2026-07-05] · daneben **Bestelllisten** (aus Auftrag/Angebot).
   Die playground-FiBu hängt sich **ausschließlich an die festgeschriebene Rechnung** (Buchungssatz-Erzeugung). Angebots-, Set-, Artikel-, Auftrags- und Bestelllisten-Strukturen werden von der FiBu **NICHT verändert, NICHT dupliziert, NICHT durch playground-Äquivalente ersetzt.**

3. **KONFLIKT-REGEL:** Bei Struktur-Konflikt passt sich **immer der playground-Code dem ticket-Schema an — nie umgekehrt.** Kollision löst sich per **Adapter (bevorzugt)** oder **additiver Spalte** (nie destruktiv). Auch für die Kette zu prüfen: Teilrechnungen je Auftrag · Positions-Ebene für Erlöskonten-Split · Leistungszeitraum-Herkunft aus der Kette.

*(Verankert auch in `docs/STRAENGE.md`. Bezug: `invoices`-Schienen-Entscheidung + `docs/accounting/`.)*

## Eine Wahrheit je Sachverhalt

Für jeden zentralen Sachverhalt gibt es **genau eine führende Datenquelle** — keine zweite, auch nicht übergangsweise. Parallel-Strukturen werden **additiv zusammengeführt**, nie doppelt produktiv gehalten. Neue Quellen dürfen eine bestehende nicht duplizieren; bei Bedarf wird die alte belegt stillgelegt (Trail erhalten, Drop als eigener Posten).

- **Umsatz → `invoices`** (einzige Wahrheit): **`docs/accounting/umsatzdefinition.md`** (Dauerregel). `deal_invoices` stillgelegt, Drop ausstehend.
- **Status/Phasen → `lead_stages`** · **Katalog → ticket-Artikel-DB** (EIN Katalog). *(weitere Beispiele additiv ergänzen)*

## Heizkörper-Modul (Bereich „Energie & Auslegung")

- **Alpine.js ist erlaubt AUSSCHLIESSLICH in zwei Scopes** (Yama-Entscheidung 2026-07-06): **(1)** die
  `heizkoerper.*`-Views unter `resources/views/admin/heizkoerper/**`; **(2)** das **Formular-Rendering**
  des Strangs `formulare` (dynamische Sichtbarkeit `visible_if`, Feld-Renderer/Preview der
  `ProductFormula`-Checklisten-Formulare). **Nirgends sonst** im CRM. Begründung Scope 2: reaktive
  Sichtbarkeit ist genau Alpines Fall; ein jQuery-Nachbau wäre mehr Code bei geringerer Wartbarkeit.
  **Grenze:** kein Alpine-Wildwuchs außerhalb dieser zwei Scopes. Der übrige Bestand nutzt jQuery +
  Bootstrap/Vuexy. **Die bestehende Aufnahme-CRUD `radiator.config.*` (`RadiatorInstallationController`)
  bleibt unangetastet** (jQuery, kein Alpine-Umbau) — M4-a nutzt sie per Reuse für die Heizkörper-Aufnahme.

- **DO NOT DOCK `radiators`:** Das Alt-Model `app/Models/Radiator.php` (Tabelle `radiators`,
  Route `product.inveter.store`) ist eine **Wechselrichter-Altlast** — **NICHT** für Heizkörper
  verwenden, **nicht** umbenennen. Die Heizkörper-Domäne läuft ausschließlich über
  `App\Models\RadiatorSpec` / `App\Models\RadiatorInstallation` und
  `App\Services\Heizkoerper\*` (`RadiatorPerformanceService`, `HydraulicService`,
  `RadiatorCatalogAdapter`, `CompatibilityService`).
