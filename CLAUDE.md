# CLAUDE.md — ticket CRM (Projekt-Governance)

> Modul-/Bereichs-Regeln für dieses Repository. Additiv gepflegt.

> **⛔ OBERSTES DOKUMENT: [`docs/BETRIEBSORDNUNG.md`](docs/BETRIEBSORDNUNG.md)** — autonomer Mehrstrang-Betrieb (Rollen BAUER/PRÜFER/KOORDINATOR, Gates G1–G9, Vollmacht + Restgrenze, FiBu-Sondergates). Bindet JEDE Instanz; bei Konflikt mit einem Auftragstext gilt die Betriebsordnung. Ändert nur Yama.

> **⛔ BAU-ORDNUNG (verbindlich): Vor jedem Produktiv-Commit: Selbstprüfung gegen [`docs/architektur/bauordnung.md`](docs/architektur/bauordnung.md) §5 (die 10 Fragen).** Die Bauordnung (Schichten-/Daten-/Sicherheits-/Bau-Prozess-Regeln, abgeleitet aus dem Code-Audit) ist verbindlich und steht **unter** BETRIEBSORDNUNG.md/CLAUDE.md — bei Konflikt gelten diese. Jeder Agent liest sie bei jedem Start.

> **📍 „LIVE"/„PRODUKTION" = LOKALE DEV-DB (Yama-Klarstellung 2026-07-08).** Im laufenden Programm meint „live" die **lokale** DB (`ticket`/`ticket_testing`) auf Yamas Rechner — dort ist freies Arbeiten erlaubt (Migrationen, Seeder, Daten-Bereinigung inkl. UPDATE/DELETE als beauftragter Posten). **Hetzner-Produktion (3000 Kunden) wird NICHT angefasst** — erst am separaten Deploy-Tag, den Yama auslöst. Die „Produktion off-limits"-Regel gilt weiterhin **NUR für Hetzner**. *(Die DAUERDIREKTIVE unten schützt die ticket-Datenintegrität auch lokal: destruktive Schritte bleiben eigene, beauftragte, belegte Posten — kein Beifang.)*

> **⛔ AUTOMATISIERUNGS-PRINZIP (dauerhaft, ab 2026-07-09).** „Sinnvoll automatisiert" ≠ „maximal automatisiert". Jede Automatisierung, die eine **Fach-/Rechtsentscheidung** dem Menschen abnimmt, folgt dem **Operanden-Gate**: **kein erfundener Wert — bei Unsicherheit/fehlenden Operanden wird gefragt oder markiert (Vorschlag + Bestätigung), nie stillschweigend weitergerechnet** (wie in der Formular-Calc-Engine + Anforderungsprofilen). Drei Klassen bei jedem Automatisierungs-Vorschlag benennen: **(a)** soll automatisiert sein (sicher ableitbar, spart Arbeit) · **(b)** darf NICHT voll automatisiert sein (Fach-/Rechtsentscheidung → nur Vorschlag+Bestätigung) · **(c)** ist automatisiert, aber falsch/riskant → korrigieren. Bindet Planner/Generator/Evaluator + jeden Optimierungs-Posten.

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
