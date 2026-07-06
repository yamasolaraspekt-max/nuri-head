# Bestandsaufnahme: playground / wberechnung -> ticket

**Stand:** 2026-07-05  
**Scope:** read-only Bestandsaufnahme im aktuellen `ticket`-Arbeitsbaum. Keine Code-Aenderung, keine Migration, kein Testlauf.

## 1. Kurzurteil

Es wurde **nicht "das playground" breit nach ticket kopiert**. Im aktuellen Code ist aus dem allgemeinen
`playground` als klar markierter, echter Modultransfer vor allem **Förderungen** sichtbar. Der Rest ist bisher
überwiegend **Konzept, Inventur, Entscheidungsvorlage oder Backlog**.

Bei `wberechnung` ist deutlich mehr passiert: Heizkörper-/Heizlast-/Katalog-Teile sind bereits portiert oder
vorbereitet. Trotzdem ist auch `wberechnung` laut Abschlussbilanz **nicht abschaltbar**: dokumentiert sind
**14 von 215 fachlich wertvollen Positionen** übernommen, also grob **6,5 %**. Die Quote klingt klein, ist aber
ehrlich: Es wurden zuerst die tragfähigen Kerne und Kataloge übernommen, nicht die ganze App.

## 2. Begriffsklärung

| Quelle | Bedeutung in dieser Bestandsaufnahme | Status |
|---|---|---|
| `playground` | CRM/ERP-Prototyp mit Buchhaltung, HR, Formularen, Lager, Kundendienst, Energie-Ideen | Hauptsaechlich Konzeptquelle; nur einzelne Teile portiert |
| `wberechnung` | Fachrechner-App fuer Heizlast, Heizkoerper, WP/PV, PVGIS, Wirtschaftlichkeit, Klima | Teilweise technischer Cut-over; weiter Referenz fuer offene Rechner |
| `ticket` | Fuehrendes Live-System, Blade/Vuexy, echtes CRM und operative Daten | Zielsystem; Design/Layout ist verbindlich |

## 3. Echt aus playground uebertragen

| Bereich | Dateien / Orte im ticket | Bewertung |
|---|---|---|
| **Förderungen** | `database/migrations/2026_06_28_100000_create_foerderungen_table.php`, `app/Models/Foerderung.php`, `app/Http/Controllers/Admin/FoerderungController.php`, `resources/views/admin/foerderungen/index.blade.php`, `routes/web.php`, Sidebar unter **Energie** | Echter Transfer. Im Code steht ausdrücklich "übernommen aus playground", aber an ticket-Auth und Blade angepasst. |
| **Navigation / Strukturideen** | `docs/navigation-*`, `docs/uebernahme/*`, Sidebar-Bereich **Energie** | Teilweise umgesetzt als Informationsarchitektur, nicht als 1:1 Playground-Navi. |
| **Buchhaltung / DATEV** | `docs/uebernahme/buchhaltung-datev-integrationsplan.md`, Sprint-/Ticket-Dokumente | Noch kein fertiger Accounting-Code im aktuellen Arbeitsbaum. Es gibt Konzepte und Rechnungsschienen-Tickets, aber keine `accounting_*` Code-Schicht. |
| **Dynamische Formulare** | Ticket hat eigenes `ProductFormula`-System; kein `dynamic_forms`-/`FormRoutingService`-Import gefunden | Noch nicht portiert. Empfehlung bleibt: Synthese aus ticket-Formularen und playground-Engine, nicht danebenstellen. |
| **HR / Lohn / Kundendienst / Betriebsmittel / Lager / Controlling** | Keine klaren Playground-Transfers im aktuellen Code gefunden | Bisher Konzept-/Backlog-Status. |

**Pragmatische Zahl:** Aus dem allgemeinen `playground` ist aktuell eher **1 produktiver Modultransfer** sichtbar
(Förderungen). Alles andere ist noch nicht als fertiger Port im Code angekommen.

## 4. Echt aus wberechnung uebertragen

Laut `docs/cutover-wb-abschlussbilanz.md` sind diese Teile im ticket angekommen oder als lokaler Cut-over
verifiziert:

| Bereich | ticket-Orte | Status |
|---|---|---|
| Heizkoerper-Rechenkern EN-442 / Hydraulik | `app/Services/Heizkoerper/RadiatorPerformanceService.php`, `HydraulicService.php` | Portiert, Paritaets-Tests dokumentiert |
| Heizkoerper-Kompatibilitaet | `CompatibilityService.php`, `RadiatorSituation.php`, `CompatibilityResult.php` | Portiert |
| Heizkoerper-Katalogadapter / Katalogschema | `RadiatorCatalogAdapter.php`, `RadiatorSpecSeeder.php`, `product_radiator_specs`-Migrationen | Vorbereitet, teils testing/main-Unterschied laut Bilanz |
| Heizkoerper UI / Konfigurator | `app/Http/Controllers/Heizkoerper/HeizkoerperController.php`, `resources/views/admin/heizkoerper/konfigurator.blade.php` | Vorhanden, hinter Feature-/Auth-Schnitt |
| WP/PV/Geraete-Kataloge | `WberechnungImportSeeder`, `database/data/wberechnung_import.php`, `product_*_specs` | Teilweise importiert/vorbereitet |
| Heizlast-Referenzdaten und Kernteile | `app/Services/Heizlast/*`, `AnforderungsprofilHeizlastAdapter.php`, Referenz-Katalog-Seeder | Teilweise portiert; weitere Adapter offen |

Quote aus der Abschlussbilanz: **14/215 fachlich wertvolle Positionen uebernommen**. Offen bleiben u. a.
WP-Auslegung, Bivalenz/JAZ, PV-/WR-Sizing, PVGIS-Kern, Wirtschaftlichkeit, Klima, Foerderlogik aus
`wberechnung`, Energiekonzept und Grundriss/Plan-Import.

## 5. Was ausdruecklich noch nicht uebertragen ist

| Nicht uebertragen | Warum |
|---|---|
| Playground-CRM, Kunden, Angebote, Auftraege | ticket ist hier fuehrend und hat Live-Daten. Playground-Daten sind Sample/Prototyp. |
| Playground-Rechnungen / `angebot_invoices` | Wird nicht importiert. Rechnungsschiene wird in ticket selbst gehaertet. |
| Playground-Artikelkatalog | Ein-Katalog-Regel: ticket-`products` bleibt Wahrheit. |
| Playground-Buchhaltung als Code | Zu riskant und im aktuellen Arbeitsbaum nicht vorhanden; bisher nur Planung/Tickets. |
| Playground-Design / React-SPA / 3D-Dachplaner | Stack-fremd. Views muessen im ticket-Design neu gebaut werden. |
| Playground-RBAC als Live-Auth | Nur Konzeptquelle; ticket-Auth nicht parallel durch ein zweites System ersetzen. |
| Live-Daten aus playground | Nicht noetig. Playground hat vor allem Test-/Seed-Daten. |

## 6. Navigation heute

Der Sidebar-Schnitt ist aktuell relativ sauber:

- **Energie** enthaelt `PVGIS`, `Heizkörper-Check`, `Wirtschaftlichkeit`, `Förderungen`.
- Kein eigener Menupunkt `wberechnung`.
- Kein Sammelbereich `Tools`.
- OMD ist nicht als eigene Hauptnavigation sichtbar.
- `Checklisten-Formulare` existiert im Artikel-/Produktkontext als ticket-eigenes System.

Wichtig fuer Yamas Wunsch gegen Doppelbegriffe: neue Playground-Ports sollten **nicht** als zweiter Begriff
daneben auftauchen. Beispiel: nicht `Formulare` und `Dynamische Formulare` parallel, sondern ein fuehrender
Begriff mit zusammengefuehrter Funktion.

## 7. Offene Luecken nach Wichtigkeit

1. **wberechnung-Cut-over fertig priorisieren**  
   Heizlast/WP/PV/PVGIS/Wirtschaftlichkeit sind fachlich wertvoller als weitere Playground-Spielereien.

2. **Rechnungsschiene stabilisieren**  
   Nummernkreis, Loeschsperre, Editiersperre, Storno, Teilzahlung, PDF, OP-Liste. Das ist Grundlage fuer
   Buchhaltung/DATEV, egal ob spaeter Kanzlei- oder In-house-Weg.

3. **Formular-System zusammenfuehren**  
   ticket-`ProductFormula` behalten, playground-Ideen wie Smartrouting, sichere Berechnung, `visible_if` und
   Pflichtdaten-Gates gezielt einbauen. Kein neues Parallel-Modul.

4. **Buchhaltung / DATEV nicht blind portieren**  
   Erst nach Rechnungsschienen-Haertung und klarer A1-Entscheidung. Im aktuellen Code ist keine fertige
   Accounting-Schicht vorhanden.

5. **Kundendienst / Betriebsmittel / Fuhrpark separat reifepruefen**  
   Das sind echte ticket-Luecken, aber playground hatte dort laut Inventur wenig oder keine Daten. Erst Code-Reife
   pruefen, dann entscheiden.

## 8. Naechste sinnvolle Bestandsaufnahme

Wenn weiter inventarisiert wird, dann nicht mehr allgemein "playground", sondern gezielt je Bereich:

1. **Formular-Synthese:** ticket `ProductFormula` vs. playground `dynamic_forms` fachlich zusammenlegen.
2. **Buchhaltung-Code-Realitaet:** welche S1-Tickets sind wirklich gebaut, getestet und migriert?
3. **wberechnung-Restliste:** fuer jeden offenen B-Slot sagen: portieren, verwerfen oder spaeter.
4. **Navigation-Deduplikation:** fuehrende Begriffe festlegen, doppelte/alte Begriffe entfernen.

## 9. Fazit

Aus `playground` ist bisher **sehr wenig als fertiger Code** uebernommen: im Wesentlichen **Förderungen** plus
viele gute Konzepte. Aus `wberechnung` ist **mehr Substanz** uebertragen, aber ebenfalls nur ein Teil:
Heizkoerper/Heizlast/Kataloge sind angefangen oder verifiziert, der grosse Rest bleibt Referenz.

Die richtige Linie bleibt: **nicht importieren, sondern sauber in ticket neu integrieren**. Nur das uebernehmen,
was fachlich stark ist, keine Playground-Optik, keine doppelten Begriffe, keine zweite Wahrheit neben bestehenden
ticket-Modulen.
