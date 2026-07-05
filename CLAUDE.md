# CLAUDE.md — ticket CRM (Projekt-Governance)

> Modul-/Bereichs-Regeln für dieses Repository. Additiv gepflegt.

## ⛔ DAUERDIREKTIVE: DATEN- UND KETTEN-SCHUTZ (ab 2026-07-05, strang-übergreifend & dauerhaft)

**Bindet JEDE Instanz und JEDEN playground→ticket-Schritt (Migration · Seeder · Import · Cut-over · FiBu).**

1. **TICKET-DATEN SIND UNANTASTBAR.** Kein Transplantations-Schritt darf **bestehende ticket-Zeilen ändern oder löschen**. Erlaubt sind **nur additive** Operationen: neue Tabellen · neue Spalten (**nullable oder mit Default**) · neue Zeilen. **Jeder UPDATE/DELETE auf Bestandsdaten ist ein eigener, explizit von Yama zu beauftragender Posten — niemals Beifang** eines Transplantats.

2. **DIE BELEGKETTE IST GESETZT — FiBu DOCKT AN, BAUT NICHT UM.** Bestehende Kette in ticket:
   **Angebot** (aus Sets, Sets aus Artikeln) → **Auftrag** → **Rechnung(en)** [`invoices` = führende Schiene, Yama-Entscheidung 2026-07-05] · daneben **Bestelllisten** (aus Auftrag/Angebot).
   Die playground-FiBu hängt sich **ausschließlich an die festgeschriebene Rechnung** (Buchungssatz-Erzeugung). Angebots-, Set-, Artikel-, Auftrags- und Bestelllisten-Strukturen werden von der FiBu **NICHT verändert, NICHT dupliziert, NICHT durch playground-Äquivalente ersetzt.**

3. **KONFLIKT-REGEL:** Bei Struktur-Konflikt passt sich **immer der playground-Code dem ticket-Schema an — nie umgekehrt.** Kollision löst sich per **Adapter (bevorzugt)** oder **additiver Spalte** (nie destruktiv). Auch für die Kette zu prüfen: Teilrechnungen je Auftrag · Positions-Ebene für Erlöskonten-Split · Leistungszeitraum-Herkunft aus der Kette.

*(Verankert auch in `docs/STRAENGE.md`. Bezug: `invoices`-Schienen-Entscheidung + `docs/accounting/`.)*

## Heizkörper-Modul (Bereich „Energie & Auslegung")

- **Alpine.js ist erlaubt AUSSCHLIESSLICH in den neuen `heizkoerper.*`-Views** — unter
  `resources/views/admin/heizkoerper/**`. **Nirgends sonst** im CRM. Der übrige Bestand
  nutzt jQuery + Bootstrap/Vuexy; Alpine ist ausschließlich für die Heizkörper-Konfigurator-/
  Ergebnis-Flächen (M4) zugelassen. **Die bestehende Aufnahme-CRUD `radiator.config.*`
  (`RadiatorInstallationController`) bleibt unangetastet** (jQuery, kein Alpine-Umbau) — M4-a
  nutzt sie per Reuse für die Heizkörper-Aufnahme.

- **DO NOT DOCK `radiators`:** Das Alt-Model `app/Models/Radiator.php` (Tabelle `radiators`,
  Route `product.inveter.store`) ist eine **Wechselrichter-Altlast** — **NICHT** für Heizkörper
  verwenden, **nicht** umbenennen. Die Heizkörper-Domäne läuft ausschließlich über
  `App\Models\RadiatorSpec` / `App\Models\RadiatorInstallation` und
  `App\Services\Heizkoerper\*` (`RadiatorPerformanceService`, `HydraulicService`,
  `RadiatorCatalogAdapter`, `CompatibilityService`).
