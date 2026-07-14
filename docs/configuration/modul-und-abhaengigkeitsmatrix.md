# Modul- und Abhängigkeitsmatrix

**Stand:** 2026-07-14 · **read-only** · **Git:** `a00bb0a`. Belegt aus den fünf Bestandsaufnahmen. „führende Quelle" = wo die Wahrheit liegen SOLL (Ziel) bzw. HEUTE liegt (kursiv, falls abweichend/Insel).

| Modul | benötigt (Bedarf zuerst) | erzeugt | invalidiert (bei Änderung) | führende Quelle | nachgelagerte Module | Ist-Status |
|---|---|---|---|---|---|---|
| **Objekt/Gebäudemodell** | Kunde, Objekt (`lead_alternative_adds`) | Geometrie (Räume, Flächen, Volumen, Öffnungen, Dachkontur) | Heizlast, PV, Dach, Fassade, Fenster, Heizflächen | `anforderungsprofile.gebaeude_geometrie` (versioniert) | Heizlast, Dach, PV, Fassade, Fenster/Türen | `besteht_belastbar` (1 Raum; Parallel-Geometrien offen) |
| **Anforderungsprofil (Bedarf)** | Kunde, Objekt, Formulare | bestätigte Eingaben, Randbedingungen, Datenlage/Quelle | alle Auslegungen | `Anforderungsprofil` (append-only) | alle Module | `besteht_belastbar` |
| **Heizlast** | Raumgeometrie, U-Werte, Solltemp, Standort/Norm-Außentemp, Lüftung | Raum-/Gebäudeheizlast, Datenqualität, Methode | Wärmepumpe, Heizflächen, Fassade | `Anforderungsprofil` (Werte) + Geometrie | Wärmepumpe, Heizflächen | `besteht_belastbar` (Adapter nicht verdrahtet) |
| **Heizflächen / hydraul. Abgleich** | Raumheizlast, Heizkörper/FBH, Systemtemperaturen, Kreise | Eignung, Vorlauf, Volumenstrom, Einstellwerte | Wärmepumpe (Vorlauf) | `radiator_installations` / `deal_measurements` | Wärmepumpe, Montage | `besteht_belastbar` (einziger echter Angebots-Write) |
| **Wärmepumpe** | Gebäudeheizlast, WW, Vorlauf, System, Standort, Aufstellung, Schall, Elektro, Netz | Match, Bivalenz, JAZ, Heizstab, Ranking, Warnungen | Angebot, Elektro, Netz/Messkonzept | `Anforderungsprofil` (Ziel: `auslegung_ergebnis`) | Angebot, Elektro, Montage | `besteht_belastbar` (read-only, nur luft_wasser) |
| **PV** | validierte Dachgeometrie, Ausrichtung, Verschattung, Verbrauch, Ziele | Belegungs-/Ertragsvarianten | Speicher, Netz/Messkonzept, Angebot | *heute `p_v_roof_plans` (Insel)* → Ziel: kanonisches Dach | Speicher, Wallbox, Netz | `besteht_teilweise` (PvProjektService „gebrochen") |
| **Speicher** | Verbrauch, Lastprofil, PV-Erzeugung, Autarkieziel, WP, Wallbox | Kapazitätsbereiche, Nutzungsgrad, Ersatzstrom | Netz/Messkonzept, Angebot | *Ø* → `Anforderungsprofil` | Netz, Angebot | `fehlt` (nur WR-Kompatibilität) |
| **Wallbox** | Fahrzeug, Ladezeiten, Hausanschluss, PV, Speicher, Lastmgmt | Ladeleistung, Phasen, Lastmgmt, Elektroarbeiten | Netz/Messkonzept, Angebot | *Ø* | Netz, Montage | `fehlt` |
| **Netz / Messkonzept** | Hausanschluss, Zähler, Netzbetreiber, WP, PV, Speicher, Wallbox, SteuVE | Messkonzeptvariante, Netzbetreiberklärung | Angebot, Montage | *Ø (nur Stammdaten `CustomerMeterCabinet`)* | Angebot, Montage | `fehlt` |
| **Dach** | obere Gebäudekontur, Dachform, Neigung, First/Traufe, Gauben, Hindernisse | Dachflächen, Neigung, Ausrichtung | PV | *heute Schätzer/`p_v_roof_plans`* → kanonisches Modell | PV, Fassade | `fehlt` (echte Domäne) |
| **Fenster / Türen** | Öffnung, Wandaufbau, Roh-/Fertigmaß, Zuordnung, Anforderungen | Aufmaß, Produktvarianten | Heizlast, Fassade | *heute nur Heizlast-Bauteil* → Öffnungs-Domäne | Angebot, Montage | `fehlt` (Konfigurator) |
| **Fassade / Dämmung** | Außenwandflächen, Öffnungen, Aufbau, Bestand, Ziel, Brandschutz | Varianten, Flächen, Mengen, Wärmebrücken | Heizlast | Geometrie + Sanierung | Angebot, Montage | `besteht_teilweise` (Sanierung transient) |
| **Energiekonzept** | WP-/Sanierungs-/PV-Teilergebnisse | Gesamtkonzept-Dokument | — | Assembler (kein eigener Speicher) | Angebot | `besteht_teilweise` (transient) |
| **Produktfilter** | berechneter Kandidatenraum | eingeschränkte Kandidatenliste | — | Query-/View-State | Ranking/Auswahl | `fehlt` (Filter-Schicht) |
| **Preis/Kosten** | Auswahl, Katalog (`master_set_components`), Investition | Investition, Betriebskosten, Vergleich | Angebot | `master_set_components` (Auto-Anker blockiert) | Angebot | `besteht_teilweise` (Auto-Anker unmöglich: Spec-`product_id` nullable) |
| **Förderung** | förderfähige Kosten, WE, Boni, Heizungsart/-alter | Zuschuss, effektiver Satz, Netto | Angebot | `FoerderungService` | Angebot | `besteht_belastbar` (BEG) |
| **Angebot** | gewählte + freigegebene Variante | Snapshot, Positionen, Totals | Auftrag, Rechnung | `offer_details.sections` | Auftrag, Montage, Rechnung | `besteht_belastbar` (Auslegung→Angebot fehlt) |
| **Montage/Ausführung** | freigegebenes Angebot, Aufmaß | Checklisten, Handover | — | `deal_measurements` + Checklisten | — | `besteht_teilweise` (nicht an Konfigurator gekoppelt) |

## Abhängigkeits-Kaskaden (Stale-Regeln, Ziel)

| Auslöser (Eingabeänderung) | setzt `stale` |
|---|---|
| Heizlast geändert | Wärmepumpe, Heizflächen |
| Dachgeometrie geändert | PV |
| Verbrauch geändert | PV-Wirtschaftlichkeit, Speicher |
| Hausanschluss geändert | Wallbox, Netz/Messkonzept |
| Fenster geändert | Heizlast, Fassade |
| Raumhöhe geändert | Heizlast (Volumen), Fassade |
| Nordwinkel geändert | PV, Fassadenausrichtung |
| Geometrie (Wand/Fläche) geändert | Heizlast, Fassade, Fenster-Nettofläche |

**Ist:** Diese Kaskaden existieren **nicht** aktiv (kein Observer/Listener; `DATA-15` = `fehlt`). Read-only-Verbraucher (OfferReadiness) umgehen das, indem sie immer frisch lesen — persistierte Ergebnisse (künftig `auslegung_ergebnis`) brauchen dagegen echten Stale-Trigger.

## Kritische Konsolidierungspunkte (Parallelwahrheiten)

1. **Geometrie — 3 schreibbare Stores + 2 abgeleitete Quellen:** schreibbar = `anforderungsprofile.gebaeude_geometrie` (führend, versioniert), `raum_geometrien` (legacy, transient), `p_v_roof_plans.roof_structures` (PV-Insel); abgeleitet/kein Geometrie-Store = `sanierungs_varianten.massnahmen` (Maßnahmen-JSON) + `RoofAreaEstimator` (nicht-persistenter Schätzer). Ziel: **eine** schreibbare Wahrheit; Legacy/PV-Insel andocken/stilllegen, abgeleitete Quellen nie als Zweitwahrheit schreiben.
2. **Positionen:** `offer_product_lists` vs. `offer_details.sections` → Führung entscheiden, Alt stilllegen.
3. **Preisanker:** Spec (`product_heat_pump_specs.product_id` nullable; Deklaration **ohne** `->foreign()`-Constraint trotz „FK"-Kommentar) ↔ Preis (`master_set_components`) über gemeinsame `product_id` konsolidieren, sonst Auto-Anker unmöglich.
