# Entscheidung: ticket mit playground vergleichen, dann uebernehmen

**Stand:** 2026-07-05  
**Scope:** Vergleich `ticket` (`/Users/yamanuri/Documents/ticket`) gegen `playground`
(`/Users/yamanuri/Documents/Playground/backend-laravel`). Quelle `playground` nur gelesen. Keine Code-Aenderung,
keine Migration, kein Testlauf.

## 1. Kurzentscheidung

`ticket` bleibt fuehrendes System. `playground` wird **nicht** als zweites System importiert und auch nicht als
Design-Vorlage verwendet. Der richtige Weg ist:

1. **ticket-Bestand behalten**, wo ticket bereits operative Live-Funktion hat.
2. **playground-Ideen extrahieren**, wo sie fachlich besser sind.
3. **in ticket neu integrieren**, mit ticket-Auth, ticket-Layout, ticket-Datenmodell und ticket-Navigation.

Die wichtigste Entscheidung: **Formulare/Smartrouting zuerst**, danach **Angebotsampel/Pflichtdaten-Gates** und
selektiv **Energie-/Dach-/Lastmanagement-Bausteine**. Buchhaltung/DATEV bleibt ein eigener Strang, nicht Teil eines
allgemeinen Playground-Imports.

## 2. Harte Vergleichsfakten

| Punkt | ticket | playground | Entscheidung |
|---|---|---|---|
| Dateiumfang `app/database/resources/routes/tests` | ca. 2609 Dateien | ca. 1330 Dateien | ticket ist groesser und operativer; playground ist modularer |
| Navigation | ticket-Sidebar + Quick-/Sonderwege, Energie bereits konsolidiert | eine zentrale `$nav`-Quelle mit 13 Bereichen und sehr vielen Punkten | Strukturidee ja, 122-Punkte-Navi nein |
| UI-Stack | Blade/Vuexy/Bootstrap/jQuery | Blade/Alpine + React-Reste | ticket-Design gewinnt |
| Live-Daten | ticket ist operatives CRM | playground hat viel Sample-/Seed-Daten | keine Live-Daten aus playground |
| Auth | ticket-eigenes Rechte-/Rollenmodell | RBAC mit Rollen/Permissions | RBAC-Konzept ja, kein zweites Auth-System |

## 3. Entscheidungen je Modul

| Modul / Thema | ticket-Bestand | playground-Bestand | Entscheidung | Begruendung |
|---|---|---|---|---|
| **CRM / Kunden / Kontakte** | Stark, live, grosse Kundenbasis | Sauberer modelliert, aber Prototyp/Sample | **Nicht uebernehmen** | Keine zweite Kundenwelt; ticket ist Wahrheit |
| **Angebote / Auftraege** | Vorhanden und tief im Workflow | Gute Zusatzdienste, z. B. Versionen, Arbeitsraum, Ampel | **Konzept selektiv uebernehmen** | Angebotsampel/Pflichtdaten-Gates sind wertvoll, aber an ticket-Deals/Angebote anbinden |
| **Rechnungen** | S1-Haertung laeuft/steht in Tickets | FiBu-nahe Rechnungen | **Nicht importieren** | Rechnungsschiene in ticket haerten; keine zweite Invoice-Welt |
| **Formulare / Checklisten** | `ProductFormula`, `LeadProductChecklistValue`, Builder an Artikelgruppen/Leads | `DynamicForm`, `FormRoutingService`, sichere `FormulaEvaluationService` | **Prioritaet 1: verschmelzen** | ticket hat bessere Einbettung; playground hat bessere Engine |
| **Buchhaltung / DATEV** | Kein fertiger `accounting_*` Code im Arbeitsbaum; S1-Rechnungstickets | grosse Accounting-Suite mit Journal/DATEV/Gates, aber Risiko/Prototyp | **Separater Strang** | Nicht im allgemeinen Playground-Transfer; erst Rechnungsschiene, dann gezielter Accounting-Befund |
| **Energie / PV / WR / Dach / Lastmanagement** | Energie-Bereich, PVGIS, Wirtschaftlichkeit, wberechnung-Cut-over, Supplier/OMD | starker `InverterSizingService`, `LastmanagementService`, Dach-/Montage-Daten | **Selektiv in Energie-Strang pruefen** | Keine neue Energie-Navi; Dienste/Kataloge koennen vorhandene Energie-Architektur ergaenzen |
| **Heizlast / WP** | wberechnung-Cut-over ist fuehrend | playground hat auch Heizlast/WP-Services | **wberechnung fuehrt** | Nicht zwei Rechenwahrheiten; playground nur vergleichen, falls besserer Teil fehlt |
| **Artikel / Produktkatalog** | ticket `products`, `article_groups`, Supplier-Stack, OMD | eigener Artikel-/Produktkatalog | **Nicht uebernehmen** | Ein-Katalog-Regel; playground-Daten hoechstens als Vergleich |
| **Lager / Einkauf** | Inventar, Lieferscheine, Lagerausgaben, Kaufanfragen, Supplier-Schnittstellen | Lagerorte, Bestellungen, Wareneingaenge, Inventur | **Warten / gezielt pruefen** | ticket hat schon viel; nur fehlende Wareneingangs-/Inventurregeln vergleichen |
| **Betriebsmittel / Fuhrpark** | Assets, Betriebsmittel, Maschinen & Fahrzeuge bereits im Lager-Bereich | `BetriebsmittelController` ist vor allem Bridge zur API | **Nicht direkt portieren** | ticket hat Domäne schon; nur Wartung/Pruefplaene/Felder vergleichen |
| **Kundendienst / Tickets** | Ticket-System vorhanden | Tickets + Reklamationen + Serviceauftraege | **Ticket nicht portieren; Reklamation pruefen** | Keine zweite Ticketwelt; Reklamation als ticket-Erweiterung moeglich |
| **Disposition / Plantafel** | Einsatzplan/Planner vorhanden | Disposition/Plantafel | **Konzeptvergleich spaeter** | Erst Planner-Reife und echte Luecken bestimmen |
| **HR / Lohn** | Mitarbeiter/Abteilungen vorhanden, Lohn unklar/duenn | HR/Lohnvorbereitung/Lohnarten | **Eigener HR/Lohn-Strang** | Beruehrt DATEV, Rechte, Personaldaten; nicht schnell importieren |
| **Controlling / KPI / OKR** | Wenig/kein vollwertiges Controlling | Controlling/KPI/Abteilungs-GuV | **Spaeter** | Sinnvoll erst nach Accounting/Kostenstellen |
| **RBAC / Audit / History** | Ticket hat eigene Rechte und Historienansaetze | RBAC + `history_entries` sauberer | **Konzept uebernehmen** | Querschnittshaertung, kein Modulimport |
| **Foerderungen** | Bereits als `foerderungen` aus playground uebernommen | Quelle | **Erledigt, aber testen/haerten** | Echter Transfer ist vorhanden |

## 4. Prioritaeten nach Vergleich

### Prioritaet 1: Formular-Synthese

**Entscheidung:** bauen, aber nicht als `dynamic_forms`-Parallelwelt.

Behalten aus ticket:
- `ProductFormula`
- `product_formulas.fields` als vorhandene JSON-Struktur
- `LeadProductChecklistValue`
- vorhandene Blade-Builder-/Testansichten
- Artikelgruppen-/Lead-Anbindung

Uebernehmen aus playground:
- sichere Formel-Auswertung ohne `eval()`
- Operanden-Gate: unvollstaendig / ungeprueft / verbindlich
- `visible_if`-Logik
- Feldtyp-Semantik und Optionsvalidierung
- Smartrouting-Idee: Produkt + Service/Gewerk + Objekt/Phase
- Formular-Definitionen als fachliche Vorlagen, aber in ticket-Struktur ueberfuehren

Nicht uebernehmen:
- React-/API-UI
- playground-Tabellen 1:1
- zweite Navigation `Formulare` neben `Checklisten-Formulare`

### Prioritaet 2: Angebotsampel / Pflichtdaten-Gates

**Entscheidung:** Konzept uebernehmen, Code neu an ticket anbinden.

Playgrounds `OfferTrafficLightService` ist fachlich gut, haengt aber an `Project`, `formAnswers`, `openPoints`.
In ticket muss das auf Deals/Angebote/Lead-Produktlisten/Checklisten gemappt werden.

### Prioritaet 3: Energie-Ergaenzung

**Entscheidung:** selektiv pruefen, nur in vorhandenen Energie-Strang.

Kandidaten:
- `LastmanagementService`
- `InverterSizingService` / String-/WR-Regeln
- Dach-/Montage-Kompatibilitaetsdaten

Regel: wberechnung bleibt fuehrend fuer Heizlast/WP-Kern. Playground darf nur ergaenzen, nicht eine zweite
Energie-Wahrheit erzeugen.

### Prioritaet 4: Reklamation / Serviceauftrag

**Entscheidung:** als Erweiterung des bestehenden ticket-Ticket-/Kundenkontexts pruefen.

Nicht uebernehmen:
- Playground-Tickets als neues Ticket-System

Moeglich:
- Reklamation als eigener Typ/Workflow im bestehenden ticket-Kontext
- Serviceauftrag als Ableitung aus Ticket/Deal/Kunde

### Prioritaet 5: Betriebsmittel / Fuhrpark

**Entscheidung:** nicht portieren, sondern Felder/Workflows vergleichen.

ticket hat bereits:
- Assets/Betriebsmittel
- Inventar
- Maschinen & Fahrzeuge
- Wartungs-/Machine-Service-Strukturen

Playground kann nur helfen, wenn dort konkrete bessere Felder oder Pruefplan-Logik vorhanden sind.

## 5. Was ich explizit nicht entscheiden wuerde

| Thema | Warum nicht jetzt |
|---|---|
| Vollstaendige Buchhaltung aus playground | Zu gross, haftungsnah, braucht eigenen Accounting-Strang |
| HR/Lohn aus playground | Personaldaten + DATEV/Lohnbezug, braucht eigenen HR/Lohn-Strang |
| Playground-Navigation als Zielnavigation | Zu viele Punkte; ticket-Begriffe sollen fuehrend bleiben |
| Playground-Design | Widerspricht Yamas Vorgabe: ticket-Design bleibt verbindlich |
| Playground-Artikelkatalog | Kollision mit ticket-Katalog und Supplier/OMD |

## 6. Konkreter naechster Prompt fuer eine ausfuehrende Instanz

```text
AUFTRAG: ticket mit playground vergleichen und nur die beste Loesung entscheiden

Zielsystem: /Users/yamanuri/Documents/ticket
Quelle: /Users/yamanuri/Documents/Playground/backend-laravel nur read-only.

Fuehrend:
- ticket-Datenmodell
- ticket-Auth
- ticket-Layout
- ticket-Navigation
- ticket-Begriffe

Zuerst lesen:
- docs/playground-ticket-vergleich-entscheidung.md
- docs/playground-uebertragung-bestandsaufnahme.md
- docs/uebernahme/playground-wert-inventur.md
- docs/navigation-konsolidierung-befund-schnitt.md
- docs/architektur-entscheidungen.md

Arbeitsauftrag:
Starte mit Prioritaet 1: Formular-Synthese.

Vergleiche konkret:
ticket:
- app/Models/ProductFormula.php
- app/Http/Controllers/Product/ProductFormulaController.php
- database/migrations/2025_06_02_083017_create_product_formulas_table.php
- database/migrations/2025_06_03_205826_create_lead_product_checklist_values_table.php
- resources/views/admin/formula/*

playground:
- app/Models/DynamicForm.php
- app/Models/FormField.php
- app/Models/FormFieldOption.php
- app/Models/FormAnswer.php
- app/Models/FormRoutingRule.php
- app/Services/FormRoutingService.php
- app/Services/Form/FormulaEvaluationService.php
- app/Services/Form/PlausibilityService.php
- app/Services/Form/UnitConversionService.php
- database/sql/crm_erp_form_seed.sql

Erstelle zuerst nur ein Umsetzungspaket:
docs/formular-synthese-ticket-playground-arbeitspaket.md

Das Paket muss entscheiden:
1. JSON-Struktur von ticket behalten oder normalisieren?
2. Welche playground-Felder/Logik werden in ticket uebernommen?
3. Wie werden die 21 playground-Formulare/358 Felder in ticket-Vorlagen ueberfuehrt?
4. Welche Tests beweisen, dass keine zweite Formularwelt entsteht?
5. Welche Navi-Bezeichnung bleibt fuehrend?

Nicht bauen:
- keine Migration
- kein Code
- keine UI
- keine playground-Views
- keine React/Alpine-Uebernahme

Nach dem Dokument stoppen.
```

## 7. Fazit

Nach dem Vergleich ist die beste Entscheidung **nicht** "playground schneller uebertragen", sondern:

**ticket bleibt Basis, playground wird als Bauteile-Lager genutzt.**

Sofort wertvoll sind Formular-Engine/Smartrouting, Angebotsampel und einzelne Energie-Dienste. Alles andere ist
entweder bereits in ticket vorhanden, zu riskant fuer einen Schnelltransfer oder gehoert in einen eigenen Strang.
