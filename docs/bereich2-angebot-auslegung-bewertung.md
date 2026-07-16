# Bereich 2 — Angebot, Auslegung & Kalkulation — Bewertung

**Stand:** 2026-07-11 · **read-only** · **keine Umsetzung/Refactor/Import/Löschen/Überschreiben/Archivieren/Commit/Lösung.**
**Zweck:** Inventur (`bereich2-angebot-auslegung-inventur.md`) + Verifikation (`bereich2-angebot-auslegung-verifikation.md`) fachlich/technisch **bewerten** und **Ziel-Wahrheiten vorbereiten** — noch **keine** Lösung.
**Grundlage:** ausschließlich die in den zwei Vorgänger-Dokumenten `datei:zeile`-belegten Funde (nicht neu erhoben).
**Rahmen:** CLAUDE.md „Eine Wahrheit je Sachverhalt", DAUERDIREKTIVE (Belegkette Angebot→Auftrag→Rechnung docht an, baut nicht um), Operanden-Gate (kein stiller Wert), Reihenfolge ①Konzept→②Workflow→③Verknüpfung→④Automatisierung, `rueckfall-archiv-regeln.md`.

> **Kern-Bewertung:** Bereich 2 ist ein **Rechen-Hochwert-Bereich auf ungeklärtem Fundament**. Die Fachlogik ist stark (Norm-Auslegung gebaut), aber **drei Wahrheiten sind ungesetzt** — Anker (woran hängt Auslegung), Positionen (welche Angebots-Wahrheit führt), Preis (Server oder Browser). Solange diese drei nicht entschieden sind, verstärkt jeder Bau die Doppelwahrheiten statt sie aufzulösen. **Ausnahme mit Sonderstatus: die Preis-Manipulierbarkeit ist ein Sicherheits-/Integritätsbefund (P1-nah).**

---

## 1. Anker-Wahrheit

**Befund:** Alle `/admin/energie/*`-Auslegungen laufen ankerlos (Formular-Freieingabe, Ergebnis flüchtig). Der als führend gebaute `Anforderungsprofil`-Anker (`ERLAUBTE_ANKER = [LeadAlternativeAdd, LeadProductList]`, Kunde bewusst ausgeschlossen) hat 0 Aufrufer. `grundriss.speichern` erzeugt verwaiste `HeizlastProjekt` ohne Owner-FK; Energiekonzept führt „Kunde" als Freitext.

**Risiko:** Ohne Anker keine objekt-/gewerk-bezogene Wiederauffindbarkeit, keine Versionierung des Bedarfs, kein Übergang Auslegung→Angebot; verwaiste Datensätze; Kunden-Doppelidentität. Falscher Anker (Kunde als Rechen-Basis) würde die Objekt-Klammer aushebeln.

**Fachliche Bewertung:** Auslegung ist eine **Objekt-Eigenschaft** (ein Kunde kann mehrere Objekte/Gewerke haben). Der Bedarf (Operanden) gehört versioniert ans Objekt, das Gewerk verfeinert. Der Kunde ist Kontext-Klammer, nicht Rechen-Basis. Das ist genau die im Code gebaute, aber tote Logik — sie ist fachlich richtig.

**Technische Bewertung:** Das Framework ist da (polymorphe Whitelist, `saving`-Hook, Adapter auf denselben `HeizlastRechner`). Es fehlt nur die Verdrahtung an eine Fläche. Kein Neubau nötig, sondern Anschluss — aber erst nach Entscheidung.

**Vorgeschlagene Ziel-Wahrheit:** Auslegungs-Eingang = **`anforderungsprofile`**, verankert am **Objekt (`lead_alternative_adds`) kanonisch**, **Gewerk (`lead_product_lists`) optional**. **Kunde (`new_leads`)** = Kontext-Klammer, **nicht** Anker. **Angebot (`offers`/`offer_details`)** = Ausgabe-Ziel der Auslegung, **nicht** Eingangs-Anker. **`OfferFolder`** = Angebots-Varianten-Ebene, **kein** Auslegungs-Anker.

**Offene Entscheidung an Yama:** (a) Bestätigst du Objekt-kanonisch/Gewerk-optional? (b) Soll ein **ankerloser Taschenrechner-Modus** (Schnellkalkulation vor Lead-Anlage, ohne Speicherung) bewusst erhalten bleiben — oder wird Auslegung künftig **nur verankert**? (c) Sollen verwaiste `HeizlastProjekt` künftig einen Pflicht-Owner bekommen?

**Darf gebaut werden:** **NEIN.** Anker ist die Basis-Weiche; ohne (a)–(c) würde jede Verdrahtung (auch BivalenzService) auf unsicherem Eingang sitzen.

---

## 2. Angebotspositions-Wahrheit

**Befund:** `offer_details.sections` (JSON) = führend/live (Wizard + Ordner). `offer_product_lists` = Legacy: einziger Schreiber gegen **nicht existente Spalte `folder_id`**, referenziert **alten** Katalog `product_master_sets`, Route existiert aber Pfad tot/unerreichbar. `MasterSet`/`MasterSetComponent` = Quelle beim Zusammenstellen (Hydration der `sections`). `CostingSet` = Kalkulations-Vorgabe.

**Risiko:** Zwei Positions-Schienen = „zweite Wahrheit"; der `folder_id`-Pfad ist eine tickende Schema-Falle (Punkt 6 Verifikation). Alt-Katalog `product_master_sets` neben `master_sets` = Katalog-Doppelung.

**Fachliche Bewertung:** Ein Angebot hat **eine** Positionsstruktur. Der JSON-Baum `sections` trägt die reale Hierarchie (Sets/Positionen/Lohn) und ist bereits die gelebte Wahrheit. `offer_product_lists` bringt keinen fachlichen Mehrwert mehr.

**Technische Bewertung:** `sections` ist überall angebunden (Deal-Materialliste, Feinaufmaß-Snapshot). `offer_product_lists` ist schema-inkonsistent und unerreichbar — reiner Altlast-/Fallen-Kandidat. MasterSet ist die richtige **Quelle**, darf aber keine parallele Positions-Wahrheit im Angebot werden.

**Vorgeschlagene Ziel-Wahrheit:** **`offer_details.sections` = die EINE Angebots-Positions-Wahrheit.** `offer_product_lists` → **Legacy, belegt stilllegen** (später, Variante B, nicht jetzt). `MasterSet`/`MasterSetComponent` → **führende Quelle** beim Zusammenstellen (Hydration), kein zweiter Positions-Speicher. `CostingSet` → Regel-/Vorgabe-Träger (Kalkulation, Punkt 3).

**Offene Entscheidung an Yama:** (a) Bestätigst du `sections` als alleinige Wahrheit? (b) `offer_product_lists` + `folder_id`-Pfad: **deaktiviert lassen bis geklärt** und später als eigener Posten stilllegen — einverstanden? (c) Soll der Alt-Katalog `product_master_sets` in Bereich 3 (ERP) mit-adressiert werden?

**Darf gebaut werden:** **NEIN.** Stilllegen = eigener beauftragter Posten (DAUERDIREKTIVE + Archiv-Regel); die Wahrheit-Setzung ist Voraussetzung für Preis- und Übernahme-Arbeit.

---

## 3. Preis-Wahrheit  ⚠️ Sonderstatus (Sicherheit/Integrität)

**Befund:** JS rechnet Einzelpreise + Zeilensummen + Gesamtsummen und schickt sie mit. Server (Engine 1) **verwirft die Gesamtsumme und rechnet sie neu**, nimmt aber den **Einzelpreis (`price`/`ek`) ungeprüft aus dem Payload** — **kein** Katalog-/DB-Abgleich. Engine 2 vertraut sogar `node['total']`. `master_sets.global_gemeinkosten`/`global_wagnis` **existieren in keiner Migration** → still `0`. Marge je Komponente frei (Default 50 %). MwSt aus `tax_rate`. Kein dedizierter Pricing-Service.

**Risiko:** **(hoch)** Manipulierbarer Payload setzt beliebige Einzelpreise; der „Recompute" bestätigt sie nur → als autoritative `total_net` gespeichert und in `deals.price`/FiBu weitergereicht. GK/Wagnis still 0 → VK systematisch zu niedrig **ohne Warnung** (Operanden-Gate-Verstoß). Zwei Engines → divergierende Totals; Distributor-Flow setzt `node.total` EK-statt-VK-basiert.

**Fachliche Bewertung:** Der Verkaufspreis ist eine **kaufmännische Wahrheit**, die aus Katalog-EK + definierter Aufschlagsregel (GK/Wagnis/Marge) entstehen muss — nicht aus dem Browser. Die stille 0 bei GK/Wagnis ist ein echter kaufmännischer Fehler (zu billig anbieten).

**Technische Bewertung:** Der „must never trust"-Kommentar ist **nur für die Summe** umgesetzt, nicht für den Einzelpreis — die Schutzabsicht ist im Code halb eingelöst. Fehlende Spalten + zwei Inline-Engines + kein Service = strukturell unsicher.

**Vorgeschlagene Ziel-Wahrheit:** **Server-Pricing als Wahrheit** — beim Persistieren Einzelpreise aus **Katalog/`MasterSetComponent` (EK)** + **einer** definierten Aufschlagsregel (führend: `CostingSet`) ableiten/validieren; **JS nur Vorschau**. **EINE** Summen-Engine. GK/Wagnis = **echte, existierende Felder mit Operanden-Gate** (fehlt → markieren, nie still 0). MwSt bleibt `tax_rate` je Detail.

**Offene Entscheidung an Yama:** (a) Soll die Einzelpreis-Wahrheit künftig **server-/katalog-geführt** sein (Browser-Preis nur Vorschau)? (b) Wo führt die Aufschlagsregel — **`CostingSet`** (ein Regel-Träger) statt freier Komponenten-Marge + fehlender MasterSet-Felder? (c) **Sonderfrage:** Die Preis-Manipulierbarkeit ist P1-nah — soll sie als **eigener Sicherheits-Posten** (Yama-direkt erlaubt lt. Betriebsordnung) **vorgezogen** werden, oder im Bereich-2-Zielkonzept gebündelt bleiben?

**Darf gebaut werden:** **NEIN (jetzt)** — außer du ziehst (c) als P1-Sicherheits-Posten bewusst vor. Selbst dann zuerst Bewertung durch dich; der reguläre Pricing-Umbau hängt an der CostingSet-Regel-Entscheidung.

---

## 4. Auslegungs-Wahrheit

**Befund:** Heizlast: `HeizlastProjektService` (raumweise, Norm-nah, verdrahtet) ↔ `HeizlastService` (Wohnflächen-Überschlag, 0 Aufrufer). WP: `WaermepumpenMatchService` (Kandidaten, nur im Heizlast-Rechner) + `BivalenzService` (Ranking/JAZ/E-Stab/Strom, 0 Aufrufer) ↔ `JazService` (Richtwert-JAZ, aktiv im WP-Flow). PV: `InverterSizingService` (Einzel-WR, aktiv) ↔ `PvProjektService` (Mehrdach-Bündler, 0 Aufrufer). Operanden heute frei getippt (`heizlast_kw`), nicht aus Anforderungsprofil.

**Risiko:** Zwei Rechen-Wahrheiten je Größe (JAZ Richtwert vs. gerätespezifisch; Heizlast raumweise vs. Überschlag). Aktives Frontend (`profit.blade.php`) baut Bivalenz im JS nach → dritte Wahrheit. Ohne Anker (Punkt 1) speisen sich die Rechnungen aus Hand-Eingaben.

**Fachliche Bewertung:** Die **norm-nahe, gerätespezifische** Rechnung muss führen (raumweise Heizlast; Bivalenz-Simulation aus Kennlinie). Richtwerte sind Schnell-Schätzung/Plausibilisierung, nicht Angebots-Wahrheit. Fachlich ist BivalenzService der wertvollste liegende Baustein.

**Technische Bewertung:** BivalenzService fast anschlussfertig (nur PLZ + DI + Aufruf + View), aber die JAZ/Strom-Doppelung mit JazService muss **vorher** per Fachentscheidung aufgelöst werden — sonst zwei Zahlen. HeizlastService/PvProjektService sind eigenständige Verfahren, kein blindes Einhängen.

**Vorgeschlagene Ziel-Wahrheit:** **Heizlast führend = `HeizlastProjektService`** (raumweise); `HeizlastService`-Überschlag = **nur Schnell-Schätzer/Plausibilisierung**, nicht Angebots-Wahrheit. **WP-JAZ/Strom führend = `BivalenzService`** (gerätespezifisch) sobald verankert verdrahtet; **`JazService`-Richtwert = Plausi/Fallback**. **PV Einzel-WR = `InverterSizingService`**; `PvProjektService` = Mehrdach-Wahrheit **nur wenn Mehrdach-Fall gebraucht** (Klärung). Alle Operanden aus **Anforderungsprofil** (Punkt 1).

**Offene Entscheidung an Yama:** (a) Bestätigst du `BivalenzService` als führende JAZ/Strom-Wahrheit (JazService → Plausi)? (b) Wird der Mehrdach-PV-Fall (`PvProjektService`) gebraucht oder ist Einzel-WR ausreichend? (c) Soll `heizlast_kw` in der WP-Auslegung künftig **aus der Heizlast-Rechnung** kommen (kein Hand-Abtippen)?

**Darf gebaut werden:** **NEIN.** Abhängig von Punkt 1 (Anker/Operanden) + JAZ-Wahrheit-Entscheidung. Danach ist Bivalenz-Verdrahtung der naheliegende erste Posten.

---

## 5. Angebot → Auftrag Wahrheit

**Befund:** `changeDocumentStatus` schreibt `deals` als flachen Kopf (`price = total_net` + FK), **keine Positionen**; Positionen bleiben nur in `offer_details.sections` + `angebot_snapshot_sections`. Match bestehender Deal über `customer_id + alternative_id + product_id` (nicht `offer_folder_id`). `total_net` kann durch Engine-Divergenz abweichen. Status `auftrag` unerreichbar. Deal-Kopf per Raw-SQL.

**Risiko:** Auftrag ohne persistierte Positions-Wahrheit (hängt am Angebots-JSON); `deals.price` friert ein und folgt Angebotsänderungen nicht; Match kann **denselben Deal überschreiben** bei mehreren Angeboten desselben Produkts; Raw-SQL umgeht Model-Guards.

**Fachliche Bewertung:** Der Auftrag ist ein **eigener Belegschritt** in der geschützten Kette (DAUERDIREKTIVE). Er braucht einen **eingefrorenen Positions-Snapshot** zum Zeitpunkt der Beauftragung — nicht nur einen Preis-Skalar. Ein Angebot ist änderbar, der Auftrag muss den beauftragten Stand festhalten.

**Technische Bewertung:** Der Snapshot-Mechanismus existiert bereits (`angebot_snapshot_sections`, `DealMeasurement.sections_snapshot`) — die Frage ist, ob der **Deal selbst** eine belastbare Positions-Referenz/-Kopie bekommt statt nur `price`. Match-Schlüssel und Raw-SQL sind eigene technische Mängel.

**Vorgeschlagene Ziel-Wahrheit:** Beim Auftrag muss ein **Positions-Snapshot persistiert** sein (belegfest), nicht nur `deals.price`. Führende Referenz = der **eingefrorene `sections`-Snapshot** am Auftrag; `deals.price` = abgeleiteter Kopf-Wert. Match/Verknüpfung über **`offer_folder_id`** (eindeutig), nicht `customer+product`. Deal-Anlage über das **`Deal`-Model** (Guards), nicht Raw-SQL.

**Offene Entscheidung an Yama:** (a) Reicht der bestehende Snapshot als Positions-Wahrheit des Auftrags, oder soll der Auftrag eine **eigene persistierte Positionsliste** bekommen? (b) Diese Frage **berührt Bereich 4 (Auftrag) + 5 (FiBu)** — soll sie dort entschieden werden, hier nur vormerken?

**Darf gebaut werden:** **NEIN.** Bereichsübergreifend (2↔4↔5); gehört ins Zielkonzept mit Bereich 4/5, nicht in einen isolierten Bereich-2-Bau.

---

## 6. Tote / Reserve-Bausteine

**Befund:** `SmartroutingService` = bewusste Reserve (5 grüne Tests, FS-05-Marker), aber 0 Regeln/0 Produktiv-Aufrufer. `HeizlastService`, `PvProjektService`, `PlausibilityService` = faktisch tot (kein Test/Flag/Reserve-Docblock). `BivalenzService` = faktisch tot, aber hochwertig (Punkt 4).

**Risiko:** Blindes Aktivieren erzeugt zweite Rechen-Wahrheiten (HeizlastService, BivalenzService vor JAZ-Entscheidung). Smartrouting reif, aber ungefüttert (0 Regeln) → aktiviert ohne Regeln wirkungslos.

**Fachliche Bewertung nach Wert:**
- **Hoch:** `BivalenzService` (Norm-Krone) — aber erst nach Anker + JAZ-Wahrheit.
- **Mittel-hoch:** `PlausibilityService` — dient direkt dem **Operanden-Gate** (Eingabe-Plausi: neg. Fläche/Menge, Einheiten-Mix); relativ gefahrlos als reine Warn-Schicht, weil er keine führende Zahl setzt.
- **Mittel:** `SmartroutingService` — wertvoll für Formular-Automatik, aber Regel-Basis fehlt (Bereich 1/Formulare).
- **Fall-abhängig:** `PvProjektService` — nur wenn Mehrdach-PV real gebraucht.
- **Gering:** `HeizlastService` — nur Schnell-Schätzer; als Plausi denkbar, sonst Archiv-Kandidat (Variante B, später).

**Technische Bewertung:** Alle unverdrahtet; „0 Aufrufer" ist statischer Grep (Reflection nicht 100 % ausgeschlossen, aber nicht gefunden). Aktivierung ist je ein eigener Posten mit Rückfallpfad.

**Vorgeschlagene Ziel-Wahrheit:** Reserve behalten (kein Löschen). Aktivierungs-Reihenfolge an die Wahrheiten gebunden: **nichts aktivieren, bevor Anker (1) + Preis (3) + JAZ (4) entschieden sind.** `PlausibilityService` als reine Warn-Schicht früh denkbar; `HeizlastService` Archiv-Prüfung (später, Variante B).

**Offene Entscheidung an Yama:** (a) `PvProjektService`: Mehrdach gebraucht — behalten oder als verzichtbar markieren? (b) `HeizlastService`: als Schnell-Schätzer vorhalten oder zur späteren Archivierung vormerken? (c) Darf `PlausibilityService` als **reine Plausibilisierung** (setzt keine führende Zahl) früher kommen?

**Darf gebaut werden:** **NEIN.** Kein Aktivieren vor Wahrheiten-Entscheidung; kein Archivieren in dieser Runde.

---

## 7. UI- / Workflow-Bewertung

**Befund:** Aktiv-falsch führende Views: `checklist/.../profit.blade.php` (rechnet Bivalenz/Heizlast/WP im JS statt Service) und `configuration/offer/config.blade.php` (Preise im JS). Gebrochene Nutzerpfade: **Auslegung→Angebot** (Werte manuell abtippen), **`heizlast_kw`** Heizlast-Rechner→WP-Auslegung (kein Datenfluss), **Angebot→Auftrag** (keine Positionen). Tote Legacy-View `wp/index.blade.php` (nicht erreichbar).

**Risiko:** Frontends als heimliche Rechen-Wahrheit → Ergebnisse driften vom (richtigen) Service ab; manuelle Übertragung = Fehlerquelle; Nutzer kann keinen durchgängigen Pfad prüfen.

**Fachliche Bewertung:** Der Nutzer erlebt heute **Insel-Rechner** statt einer Kette Objekt→Bedarf→Auslegung→Angebot→Auftrag. Die wertvollste Fachausgabe (Bivalenz: E-Stab/Laufstunden/Strom) ist im Browser **gar nicht sichtbar** — genau die Substanz, die Yama liegen sah.

**Technische Bewertung:** JS-Rechenlogik dupliziert PHP-Services (Wartungs-/Divergenz-Last); die richtige Ausgabe fehlt in den Views. Umbau berührt große Inline-JS-Views (Vorsicht, Variante B).

**Vorgeschlagene Ziel-Wahrheit:** Views = **Darstellung**, Rechnung = **Server-Service** (keine zweite Rechen-Wahrheit im JS). Durchgängiger Pfad: Objekt/Gewerk → Anforderungsprofil → Auslegung (Server) → Angebot (`sections`, Server-Pricing) → Auftrag (Snapshot).

**Was Yama aktuell im Browser NICHT sinnvoll prüfen kann:** (1) eine **verankerte** Auslegung (Objekt→Bedarf→Ergebnis wiederauffindbar); (2) den **Bivalenz-Output** (Bivalenzpunkt/E-Stab/Laufstunden/Strom) — nirgends gerendert; (3) ob ein **Angebotspreis katalog-korrekt** ist (Browser-Preis, kein Anker); (4) einen durchgängigen **Auslegung→Angebot→Auftrag**-Pfad.

**Offene Entscheidung an Yama:** (a) Sollen die JS-rechnenden aktiven Views (profit/config) mittelfristig auf Server-Services umgestellt werden? (b) Ist der **erste sichtbare Nutzerpfad**, den du prüfen können willst, die **verankerte WP-Auslegung mit Bivalenz-Ausgabe** (deckt Punkt 1+4)?

**Darf gebaut werden:** **NEIN.** Erst Anker/Preis/JAZ-Wahrheit; UI-Umbau über großen Inline-JS-Views nur mit Variante-B-Rückfallpfad.

---

## Zusammenfassende Entscheidungstabelle

| Thema | Risiko | Ziel-Wahrheit (Vorschlag) | Offene Entscheidung | Blockiert welche Umsetzung |
|---|---|---|---|---|
| **1 Anker** | hoch | Auslegung an **Objekt (kanon.)/Gewerk (opt.)** via `Anforderungsprofil`; Kunde=Klammer; Angebot=Ziel, nicht Anker | Objekt-kanonisch bestätigen? Taschenrechner-Modus behalten? Owner-Pflicht? | Bivalenz-Verdrahtung, jede Auslegungs-Persistenz, Auslegung→Angebot |
| **2 Positionen** | hoch | **`offer_details.sections`** = einzige Wahrheit; `offer_product_lists`=Legacy stilllegen; MasterSet=Quelle | `sections` bestätigen? `folder_id`-Pfad deaktiviert lassen? Alt-Katalog in Bereich 3? | Stilllegen offer_product_lists, Preis-/Übernahme-Arbeit |
| **3 Preis** ⚠️ | **hoch (P1-nah)** | **Server-Pricing** aus Katalog-EK + `CostingSet`-Regel; JS=Vorschau; GK/Wagnis echte Felder mit Operanden-Gate; 1 Engine | Server-/katalog-geführt? CostingSet als Regel-Träger? P1-Sicherheits-Posten vorziehen? | Angebots-Speichern, Kalkulation, Angebot→Auftrag-Preis |
| **4 Auslegung** | hoch | Heizlast=`HeizlastProjektService`; WP-JAZ/Strom=`BivalenzService` (JazService=Plausi); PV=InverterSizing (+Projekt nur b. Bedarf) | Bivalenz als JAZ-Wahrheit? Mehrdach-PV nötig? `heizlast_kw` aus Rechnung? | BivalenzService-Verdrahtung, WP-Auslegungs-View |
| **5 Angebot→Auftrag** | hoch | Auftrag persistiert **Positions-Snapshot** (nicht nur `price`); Match über `offer_folder_id`; via Model | Eigene Auftrags-Positionsliste o. Snapshot? in Bereich 4/5 entscheiden? | Übernahme-Umbau (bereichsübergreifend 2/4/5) |
| **6 Tote/Reserve** | mittel | Reserve behalten; Aktivierung erst nach 1/3/4; Plausi früh denkbar | PvProjekt behalten? HeizlastService archivieren? Plausi vorziehen? | Aktivierung toter Services |
| **7 UI/Workflow** | mittel-hoch | Views=Darstellung, Rechnung=Server; durchgängiger Pfad Objekt→…→Auftrag | JS-Views auf Server umstellen? erster Prüf-Pfad = verankerte WP-Auslegung? | UI-Umbau, sichtbarer Nutzerpfad |

---

## Evaluator-Notiz
- **Basis:** ausschließlich `datei:zeile`-belegte Funde aus Inventur + Verifikation; keine neue Code-Erhebung, keine Umsetzung.
- **Priorisierungs-Kern (Vorschlag, nicht Freigabe):** die drei Fundament-Wahrheiten **Anker (1) → Positionen (2) → Preis (3)** sind Voraussetzung für alles andere; **Auslegung (4)** ist der höchste sichtbare Nutzen, hängt aber an (1). **Preis (3)** hat wegen Manipulierbarkeit **Sonderstatus** (P1-nah, ggf. vorziehbar).
- **Keine Lösung gebaut:** Ziel-Wahrheiten sind **Vorschläge**; jede Umsetzung braucht getrennte Generator/Evaluator-Runde + Yama-Freigabe + Rückfallpfad (Variante B bei Rechenkern/Wizard/Views).
- **Nicht gemacht (korrekt):** keine Umsetzung/Refactor/Import/Löschen/Überschreiben/Archivieren/Commit.

---

*Nächster Schritt laut Auftrag: **STOPP.** Yama entscheidet die Ziel-Wahrheiten (Tabelle). Erst danach folgt — auf Freigabe — ein Zielkonzept bzw. das erste Umsetzungspaket.*
