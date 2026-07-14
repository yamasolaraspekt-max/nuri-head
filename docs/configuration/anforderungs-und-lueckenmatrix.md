# Anforderungs- und Lückenmatrix

**Stand:** 2026-07-14 · **read-only** · **Git:** `a00bb0a` · **Grundlage:** Masterentscheidung §1–§20 + fünf codebelegte Bestandsaufnahmen.

**Statuswerte:** `besteht_belastbar` · `besteht_teilweise` · `fehlt` · `widersprüchlich` · `blockiert` · `umgesetzt_ungeprüft` · `umgesetzt_geprüft` · `nicht_anwendbar` (mit Begründung).
Jede Anforderung dieses Dokuments ist erfasst; ein nicht gelisteter Punkt gilt als **nicht umgesetzt**. Spalten: **Ist-Stand** (mit Codepfad/Datenmodell/UI/Tests-Belegen), **Ziel**, **Status**, **Restarbeit**. Belege sind Datei:Zeile; „Ø" = nicht vorhanden.

---

## ARCH — Architektur

| ID | Anforderung | Ist-Stand + Beleg (Code/Daten/UI/Test) | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| ARCH-01 | Bedarf führt, Produkt folgt (Standard bedarfsgeführt) | Energie-Tools produktzentriert (`EnergieAuslegungController.php:199` wp_index required); Offer-Pipeline bedarfsgeführt aber read-only | Einheitlicher bedarfsgeführter Einstieg | `besteht_teilweise` | Energie-Tools an Objekt/Anforderungsprofil binden |
| ARCH-02 | Produktvorgabe = Filter, kein Eignungsnachweis | Filter existieren nur rudimentär; WP-Match ohne Produktvorgabe-Logik | Produktvorgabe als Constraint + Eignungsprüfung parallel | `fehlt` | Filter-/Constraint-Schicht mit Eignungsklassen |
| ARCH-03 | Kunden-/Vertriebsvorgabe unterdrückt keine techn. Warnung | Keine Vorgabe-Logik → nicht anwendbar heute, aber Warnungen (Gate A) existieren `WpAuslegungsketteService.php:92-131` | Warnungen immer sichtbar trotz Filter | `besteht_teilweise` | Warn-Persistenz + UI-Sichtbarkeit |
| ARCH-04 | Kein Konfigurator mit eigener Kunde/Objekt-DB | Energie-Tools objektlos aber **erfinden** keine eigene DB (transient); Offer-Pipeline nutzt Kern-Tabellen | Alle Module auf `lead_*`-Kern | `besteht_teilweise` | Objektbindung der Tools |
| ARCH-05 | Dynamische Modulaktivierung aus `lead_product_lists` | `KonfigurationsprojektService.php:44-48` liest Gewerke; nur WP verdrahtet (`:83`) | Modulpfad je Gewerk-Set | `besteht_teilweise` | Non-WP-Gewerke verdrahten |
| ARCH-06 | Automatische Modul-Ergänzung bei Abhängigkeit | Ø (keine Abhängigkeits-Engine) | WP→Heizlast etc. automatisch aktivieren | `fehlt` | Abhängigkeits-Graph (s. Modul-Matrix) |
| ARCH-07 | Controller ohne Fachlogik; Fachlogik in Services | Kerne = Services; aber `wpErgebnis`/`baueWp` klammern Kosten/Förderung im Controller `EnergieAuslegungController.php:360` | Dünne Controller | `besteht_teilweise` | WP Stufe 3b P1a/P1b (läuft, geparkt) |
| ARCH-08 | Module lesen gemeinsame Daten, kopieren nicht | `OfferReadinessService.php:14-19` liest Quelltabellen; Energie-Tools kopieren Formularwerte transient | Kein Kopieren | `besteht_teilweise` | Tools auf gemeinsame Quelle |
| ARCH-09 | Variante/Ranking/Auswahl/Freigabe getrennt | Ranking vorhanden (`verbindlich=false`); Auswahl/Freigabe **nicht** modelliert | Vier getrennte Zustände persistent | `fehlt` | Auswahl-/Freigabe-Datenmodell |
| ARCH-10 | Abweichung von Empfehlung braucht Begründung | Ø | Begründungs-Enum bei Abweichung | `fehlt` | Begründungsfeld + UI |

## DATA — Datenwahrheit, Herkunft, Versionierung, Preis/Verfügbarkeit

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| DATA-01 | Kunde/Objekt führende CRM-Strukturen | `new_leads→lead_alternative_adds→lead_product_lists→offers` belegt (`LeadAlternativeAdd.php:288-348`) | unverändert führend | `besteht_belastbar` | — |
| DATA-02 | Anforderungsprofil führt Bedarf (versioniert) | `Anforderungsprofil.php:36-70`, append-only, ein-aktiv (App-seitig) | unverändert führend | `besteht_belastbar` | DB-Unique-Backstop (s. SEC-04) |
| DATA-03 | Strukturiertes Auslegungsergebnis versioniert am Profil | Ø Feld (nur `gebaeude_geometrie` + EAV-Skalare); Ergebnisse read-only on-the-fly | additive Spalte `auslegung_ergebnis` (Migration) | `fehlt` | WP Stufe 3b-1 (Pflicht-Stopp Migration) |
| DATA-04 | Angebot = Ausgabe/Snapshot, nicht führend | `offer_details.sections` = Positionswahrheit; Auslegung im Profil getrennt (`KonfigurationsprojektService.php:19-23`) | unverändert | `besteht_belastbar` | — |
| DATA-05 | Automatische Datenübernahme vor Neueingabe | Ø in Energie-Tools; Offer-Readiness liest Quellen | Übernahme-Vorstufe | `fehlt` | Datenübernahme-Service |
| DATA-06 | Übernommener Wert mit Quelle/Datum/Bearbeiter/Vertrauen/Version | `anforderungsprofil_werte`: `datenlage/quelle/erfassungsweg` (`AnforderungsprofilWert.php:24-27`) | vollständige Herkunft je Übernahme | `besteht_teilweise` | Datum/Bearbeiter/Quelldatensatz ergänzen |
| DATA-07 | Werte nicht still überschreiben | Append-only Versionierung (`neueVersion`) verhindert stilles Überschreiben am Profil | unverändert | `besteht_belastbar` | Übernahme-Diff-UI |
| DATA-08 | Datenprüfung vor Berechnung | Ø Vorstufe; `HeizlastBelastbarkeit` klassifiziert nachgelagert | Prüf-Gate vor Rechnung | `fehlt` | Datenprüf-Schritt |
| DATA-09 | Herkunfts-/Vertrauensstufen systemweit | Teil: `datenlage∈{gemessen,berechnet,geschaetzt}` (`SchluesselRegistry`); die volle Liste (CAD/Dokument/LiDAR/…) fehlt | erweiterte Herkunftsstufen | `besteht_teilweise` | Herkunfts-Enum erweitern |
| DATA-10 | Belastbarkeitsstatus je Auslegung | `HeizlastBelastbarkeit.php:26-36` (belastbar/eingeschr./vorläufig/unzureichend) read-only | Status je Modul, persistiert | `besteht_teilweise` | auf alle Module ausdehnen + persistieren |
| DATA-11 | Keine zweite Datenwahrheit | **Verletzt (präzisiert, Evaluator R3):** **3 schreibbare Geometrie-Stores** (`anforderungsprofile.gebaeude_geometrie` führend, `raum_geometrien` legacy, `p_v_roof_plans.roof_structures` PV-Insel) + **2 abgeleitete/Nicht-Geometrie-Quellen** (`sanierungs_varianten.massnahmen` = Maßnahmen-JSON, `RoofAreaEstimator` = nicht-persistenter Schätzer); dazu Doppel-Positionsstruktur (`offer_product_lists` vs `offer_details.sections`). Grundrisiko = ≥2 unabhängig schreibbare Geometrien fürs selbe Objekt | Konsolidierung | `widersprüchlich` | schreibbare Stores konsolidieren/andocken |
| DATA-12 | Preis/Verfügbarkeit getrennt von Technik | Getrennt: Eignung im Auslegungs-DTO, Preis `preis_status='katalog_anker_fehlt'` (`AuslegungVorschlagService.php:238`) | unverändert | `besteht_belastbar` | — |
| DATA-13 | Gerätepreis ≠ Gesamtinvestition | Invest heute = Formularwert `investition`; Gerätepreis separat (nicht als Gesamt) | klare Kennzeichnung „Geräte-Listenpreis" | `besteht_teilweise` | Preis-Klassen-DTO |
| DATA-14 | `inventories.quantity` nicht als Lieferverfügbarkeit | Wird **nicht** so interpretiert (belegt; Verfügbarkeit nur `distributor_prices.availability`-Freitext) | unverändert | `besteht_belastbar` | — |
| DATA-15 | Eingabeänderung → abhängige Ergebnisse `stale` | **Nur** Versionsketten-Stale (`StaleProfilVersionException`); **kein** Observer/Listener zur **Ergebnis-Invalidierung** (`app/Observers` existiert nicht; die 3 vorhandenen `app/Listeners` betreffen nur Login/Lead-Aktivität) | Recompute-/Stale-Signal | `fehlt` | Ergebnis-Stale-Mechanismus |

## BLDG — Gebäudemodell

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| BLDG-01 | Versioniertes kanonisches Gebäudemodell führend | `anforderungsprofile.gebaeude_geometrie` (versioniert, objektgebunden, `GrundrissController::schreibeGeometrieVersion`) | unverändert führend | `besteht_belastbar` | Mehrraum/Geschoss |
| BLDG-02 | Nicht-führend: SVG/3D/PDF/LiDAR/Screenshot/KI | Prinzip im Grundriss-Pfad erfüllt (SVG rein Editor); PV-Insel/RoofEstimator verletzen es | Darstellungen nie führend | `besteht_teilweise` | PV/Dach-Geometrie ans Modell |
| BLDG-03 | 2D primär, 3D abgeleitet | 2D-Editor real (`grundriss_editor.blade.php`); 3D nur Playground | 3D aus kanonischem Modell ableiten | `besteht_teilweise` | 3D-Ableitung ohne Zweitwahrheit |
| BLDG-04 | Bauteiländerung aktualisiert alle Auswertungen | Innerhalb Heizlast ja; modulübergreifend Ø (kein Stale) | Kaskadierte Invalidierung | `fehlt` | s. DATA-15 |
| BLDG-05 | Mehrere Eingangswege (leer/2D/PDF/Bild/DXF/DWG/IFC/LiDAR/Modell) | leer/2D/DXF/PDF/Bild real (`import-service/main.py`); DWG kontrolliert, IFC/LiDAR Ø | fehlende Wege ergänzen | `besteht_teilweise` | IFC/DWG/LiDAR (spätere Wellen) |
| BLDG-06 | Topologieprüfung | `TopologieGate` Pflicht-Gate (`GrundrissController:828/888`) | unverändert; auf neue Quellen ausdehnen | `besteht_belastbar` | Gate für PV/Dach |
| BLDG-07 | Geometrie-Versionierung | am Profil append-only; `raum_geometrien`/`p_v_roof_plans` unversioniert | einheitlich versioniert | `besteht_teilweise` | Legacy/PV versionieren |
| BLDG-08 | Datenherkunft an Geometrie | U-Wert-Herkunft ja (`heizlast_bauteile.quelle`); Polygon/Segmente tragen keine Herkunft/Import-Quelle | Herkunft an Geometrie | `besteht_teilweise` | Import-Herkunft in `gebaeude_geometrie` |
| BLDG-09 | Gemeinsame Geometrie für alle Gewerke (Dach+PV = dieselbe) | **Verletzt:** PV nutzt `p_v_roof_plans` getrennt | eine Geometrie | `widersprüchlich` | PV-Dach an kanonisches Modell |

## LIDAR — Scanaufnahme

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| LIDAR-01 | LiDAR = Aufnahmequelle, nicht Modellwahrheit | Ø (kein LiDAR) | Prinzip bei Einführung | `fehlt` | spätere Welle |
| LIDAR-02 | Scan-Bestätigungs-Workflow (Vorschlag→prüfen→Topologie→Bauteile) | Ø | Workflow | `fehlt` | spätere Welle |
| LIDAR-03 | LiDAR bestimmt nicht Material/Aufbau/U-Wert/Bestellmaß automatisch | Ø | Prinzip | `fehlt` | spätere Welle |

## HL — Heizlast

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| HL-01 | Bedarfsdaten (Geometrie/Volumen/Solltemp/Flächen/U-Werte/…) | `HeizlastProjektService`/`HeizlastRechner`, Geometrie→Bauteile (`GeometrieAbleitungService`) | unverändert | `besteht_belastbar` | — |
| HL-02 | Ausgabe (Raum-/Gebäudeheizlast/Qualität/Methode/Warnungen/Freigabe) | Rechner liefert; `HeizlastBelastbarkeit` klassifiziert; Freigabestatus Ø | Ausgabe + Freigabe | `besteht_teilweise` | Freigabestatus |
| HL-03 | Keine verbindliche WP-Größe ohne belastbare Heizlast | WP-Kette Operanden-Gate G1–G5 + Label „informativ"; Heizlast-Adapter nicht an Controller verdrahtet | durchgängige Kopplung | `besteht_teilweise` | Adapter verdrahten |

## HYD — Heizflächen / hydraulischer Abgleich

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| HYD-01 | Bedarfsdaten (Raumheizlast/Heizkörper/FBH/Temperaturen/Kreise/…) | `RadiatorPerformanceService`(EN442)/`HydraulicService`/`CompatibilityService`; Aufmaß `radiator_installations` | unverändert | `besteht_belastbar` | — |
| HYD-02 | Ausgabe (Eignung/Leistung/Anpassungen/Vorlauf/Volumenstrom/Einstellwerte) | `HydraulicService::abgleich` (`HeizlastProjektService.php:97`); Heizkörper→`deal_measurement_items` | unverändert | `besteht_belastbar` | Ownership-Gap (bekannt) |

## WP — Wärmepumpe

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| WP-01 | Bedarf zuerst (Heizlast/WW/Nutzung/Vorlauf/System/Standort/Aufstellung/Schall/Elektro/Netz) | Teil-Operanden im DTO (`WpAuslegungsEingabe`); Schall/Aufstellung/Elektro/Netz Ø | vollständige Bedarfsaufnahme | `besteht_teilweise` | Aufstellungs-/Schall-/Elektro-Operanden |
| WP-02 | Match/Bivalenz/JAZ/Heizstab/Ranking/Warnungen | `WpAuslegungsketteService` verdrahtet Match+Bivalenz+JAZ+Ranking; Gate A | unverändert | `besteht_belastbar` | nur luft_wasser (Katalog) |
| WP-03 | Hersteller/Modell/Technologie = Filter, Ranking ≠ Auswahl | Ranking `verbindlich=false`; NIBE gekennzeichnet nicht geboostet; Auswahl nicht modelliert | Auswahl getrennt | `besteht_teilweise` | s. ARCH-09 |
| WP-04 | Empfehlung/Auswahl/Freigabe/angebotene Variante unterscheiden | Nur Empfehlung existiert | vier Zustände | `fehlt` | Auswahl-/Freigabe-Modell |

## PV — Photovoltaik

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| PV-01 | Bedarf zuerst (validierte Dachgeometrie/Ausrichtung/Verschattung/Verbrauch/Ziel) | Ø validierte Dachgeometrie; `PvProjektService` „in ticket gebrochen" (`KonfigurationsprojektService.php:20`) | bedarfsgeführte PV | `besteht_teilweise` | Dachgeometrie-Naht + PV-Service reparieren |
| PV-02 | Ausgabevarianten (max/wirtschaftlich/eigenverbrauch/…) | Ø Varianten; `InverterSizingService` nur String-Bewertung | Variantenrechnung | `fehlt` | PV-Sizing-Varianten |
| PV-03 | Keine verbindliche Modulzahl aus Bounding Box/Schätzung | `RoofAreaEstimator` (fehlerhaft) an KI-Chat; keine verbindliche Belegung | Validierte Geometrie-Pflicht | `besteht_teilweise` | Schätzer entwerten/gate |

## BAT — Speicher

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| BAT-01 | Bedarf zuerst (Verbrauch/Lastprofil/PV/Autarkie/WP/Wallbox) | Ø (nur WR-Kompatibilität, `InverterSizingService.php:403`) | bedarfsgeführte Speicher-Auslegung | `fehlt` | Speicher-Sizing-Service |
| BAT-02 | Ausgabe (Kapazitätsbereiche/Nutzungsgrad/Ersatzstrom/…) | Ø | Ausgabe | `fehlt` | s.o. |

## WB — Wallbox

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| WB-01 | Bedarf zuerst (Fahrzeug/Ladezeiten/Hausanschluss/PV/Lastmgmt/…) | Ø Auslegung; nur `ElectricVehicle`-Stammdaten | Wallbox-Modul | `fehlt` | neues Modul |
| WB-02 | Ausgabe (Ladeleistung/Phasen/Lastmgmt/PV-Überschuss/Elektroarbeiten) | Ø | Ausgabe | `fehlt` | neues Modul |

## GRID — Netzanschluss / Messkonzept

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| GRID-01 | Netzanschluss/Messkonzept prüfen | Ø Berechnung; nur `CustomerMeterCabinet`/`MeterCabinetCompany`-Stammdaten | Messkonzept-Modul | `fehlt` | neues Modul |
| GRID-02 | Messkonzept nicht automatisch aus Produktwahl | Ø | Prinzip | `fehlt` | mit Modul |

## ROOF — Dach

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| ROOF-01 | Führende Dachgeometrie (Kontur/Form/Neigung/First/Traufe/Gauben/…) | Ø echte Dachgeometrie; nur Hüllbauteil `dach` + Schätzer | kanonische Dachgeometrie | `fehlt` | Dach-Domäne am Modell |
| ROOF-02 | Dach+PV dieselbe Geometrie (kein paralleles PV-Dachmodell) | **Verletzt:** `p_v_roof_plans` getrennt | eine Geometrie | `widersprüchlich` | Konsolidierung (s. BLDG-09) |

## WIN / DOOR — Fenster / Türen

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| WIN-01 | Bedarf zuerst (Öffnung/Wandaufbau/Roh-/Fertigmaß/Zuordnung/Anforderung) | Nur als Heizlast-Bauteil `fenster` (Öffnung im Wandsegment); keine Aufmaß-Domäne | Fenster-Domäne | `fehlt` | neues Modul |
| WIN-02 | Produktfilter danach; Plan/LiDAR ohne Kontrollaufmaß nicht bestellfähig | Ø | Prinzip + Aufmaß-Gate | `fehlt` | mit Modul |
| DOOR-01 | Türen analog WIN | Nur Heizlast-Bauteil `tuer` | Tür-Domäne | `fehlt` | neues Modul |

## FAC — Fassade / Dämmung

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| FAC-01 | Bedarf zuerst (Außenwandflächen/Öffnungen/Aufbau/Bestand/Ziel/Brandschutz) | Sanierung transient (`SanierungController`), keine Fassaden-Flächendomäne | Fassaden-Modul am Modell | `besteht_teilweise` | Fassadenflächen aus Geometrie |
| FAC-02 | Ausgabe (Varianten/Flächen/Mengen/Wärmebrücken/Vorarbeiten) | `SanierungsWirtschaftlichkeitService` (Wirtschaftlichkeit), keine Mengen/Wärmebrücken | erweitern | `besteht_teilweise` | Mengen/Wärmebrücken |

## UX — Bedienung

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| UX-01 | Kopfbereich (Kunde/Objekt/Projekt/Gewerke/Qualität/Version/Blocker/Filter) | Ø einheitlicher Kopf; Konfigurationsprojekt-Sicht als Ansatz | gemeinsamer Kopf | `fehlt` | UI-Rahmen |
| UX-02 | Dynamische Prozessnavigation (nur relevante Module) | Ø; statische Sidebar „Energie"-Tools | dynamische Navigation je Gewerk | `fehlt` | Navigations-Engine |
| UX-03 | Arbeitsbereich (Bedarf/Daten/fehlt/Randbedingungen/Berechnungen/Warnungen) | Ø einheitlich | gemeinsamer Arbeitsbereich | `fehlt` | UI |
| UX-04 | Ergebnisbereich je Variante (Eignung/Belastbarkeit/Quellen/Status) | Teil im WP-Offer-Panel (`auslegung_vorschlag_panel.blade.php`) | einheitlicher Ergebnisbereich | `besteht_teilweise` | verallgemeinern |
| UX-05 | Light/Dark-Mode geprüft | ungeprüft (Vuexy unterstützt beides) | Browser-Nachweis | `umgesetzt_ungeprüft` | Browser-Prüfung je Slice |
| UX-06 | Filter zeigt ausgeblendete bessere Lösungen an | Ø | Hinweis bei Filterausschluss | `fehlt` | Filterlogik + UI |

## SEC — Rechte / Sicherheit

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| SEC-01 | Autorisierung/Ownership in Controllern | Konfigurator/Angebot-Routen nur `auth`, keine Policy (`web.php:3279-3312`); einzige Policy `DealMeasurementPolicy` | Ownership-Gates | `besteht_teilweise` | Policies für Konfigurator/Angebot |
| SEC-02 | RBAC-Gate für Konfiguratoren/Angebote | Produkt-CRUD teils `permission:` (`CheckUserPermission`); Konfigurator Ø | RBAC-Gates | `besteht_teilweise` | Permission-Gates ergänzen |
| SEC-03 | Kein öffentlicher Schreibpfad ohne Auth | **KORRIGIERT (Evaluator B1):** `/ids/callback` ist **auth-gated** — Konstruktor `IdsController.php:24` `$this->middleware('auth')` **ohne** `->except(['callback'])`; `php artisan route:list -v --path=ids/callback` zeigt `App\Http\Middleware\Authenticate`. **Kein** Public-Write-Leck. **Nebenbefund (gegenläufig):** Route liegt außerhalb der auth-Gruppe (`web.php:497`) + Kommentar „must remain PUBLIC" → das Konstruktor-`auth` **konterkariert** die Absicht; der externe GC-Online-Callback ist für seinen Zweck vermutlich **funktional gebrochen** (302/401 statt Import). Kein HMAC/Secret/IP-Schutz, falls bewusst zu öffnen | Entweder auth-pflichtig belassen ODER `->except(['callback'])` **plus** Signatur/Secret/IP-Allowlist | `besteht_teilweise` | Callback-Auth-Widerspruch klären (Funktions-/Design-Befund, **kein** P0-Sicherheitsleck) |
| SEC-04 | Invarianten DB-seitig absichern | Ein-aktiv-Invariante nur App-seitig; kein Unique auf `(verankerbar,version)` | DB-Backstop | `besteht_teilweise` | M-1 (Unique-Index, Migration) |

## TEST — Tests

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| TEST-01 | Positive/negative/Grenztests grün | Rechenkerne dicht getestet (`tests/Unit/**`, `tests/Feature/**`); Suite 608/1 (Reverb E4) | Coverage je Modul | `besteht_teilweise` | fehlende Module + Charakterisierung (P1a geparkt) |
| TEST-02 | Modulübergaben getestet | Grundriss→Heizlast (`GrundrissProfilPersistenzTest`), Heizkörper→Angebot getestet | alle Übergaben | `besteht_teilweise` | Auslegung→Angebot (fehlt Pfad) |
| TEST-03 | Browser-/E2E + Light/Dark | Ø E2E/Browser-Nachweis | Browser-Prüfung je UI-Slice | `fehlt` | E2E-Harness |
| TEST-04 | Fachliche Gegenbeweise/adversarial | Praktiziert (Evaluator + Zwei-Verbindungs-Gegenprobe P0) | fortführen | `besteht_belastbar` | je Slice |

## OFFER — Angebotsübergabe

| ID | Anforderung | Ist-Stand + Beleg | Ziel | Status | Restarbeit |
|---|---|---|---|---|---|
| OFFER-01 | Angebot = Snapshot gewählter+freigegebener Variante | `offer_details.angebot_snapshot_sections` vorhanden; Auswahl/Freigabe Ø | Snapshot bei Freigabe | `besteht_teilweise` | Auswahl/Freigabe → Snapshot |
| OFFER-02 | Übergabe Ergebnis→Position mit Ergebnis-/Produkt-/Preisversion | **Kein Write-Pfad** Auslegung→Angebot (Vorschläge read-only); Heizkörper einziger Write | Übergabe-Adapter | `fehlt` | Auslegung→`offer_details.sections` (WP „Paket 3") |
| OFFER-03 | Auswahl-/Freigabestatus persistiert (nicht nur JSON-Blob) | Auslegungs-Marker `bestaetigung/datenlage/ampel` **nicht** persistiert (`AuslegungVorschlagService.php:239`) | persistente Status | `fehlt` | Status-Datenmodell |
| OFFER-04 | Montage-/Checklistenübergabe | Vorhanden (`HandoverController`, Checklisten, `deal_measurements`) aber nicht an Konfigurator gekoppelt | Kopplung | `besteht_teilweise` | Konfigurator→Montage |

---

## Positionsstruktur-Doppelwahrheit (Querbezug DATA-11)

`offer_product_lists` (2025-08, flache Zeilen) vs. `offer_details.sections` (2026-03, JSON) — zwei Positionsstrukturen; Konfigurator-Services referenzieren nur `offer_details.sections`. **Führung ist zu entscheiden + Alt-Struktur belegt stillzulegen** (eigener Slice, nicht in dieser Analyse gebaut).

## Zusammenfassung (Zählung siehe Vollständigkeitsbericht)

Muss-Anforderungen erfasst: **80** in **18 Tabellenabschnitten / 19 ID-Präfixen** (WIN + DOOR sind getrennte Präfixe, hier in einem Abschnitt geführt): ARCH 10, DATA 15, BLDG 9, LIDAR 3, HL 3, HYD 2, WP 4, PV 3, BAT 2, WB 2, GRID 2, ROOF 2, WIN 2 + DOOR 1, FAC 2, UX 6, SEC 4, TEST 4, OFFER 4. Keine Anforderung der Masterentscheidung ohne Zeile.

**Statusverschiebung nach Evaluator-Korrektur B1 (SEC-03):** `widersprüchlich` 4→**3**, `besteht_teilweise` 31→**32**. Aktualisierte Zählung: belastbar 13 · teilweise 32 · fehlt 31 · widersprüchlich 3 · umgesetzt_ungeprüft 1 · blockiert 0 · umgesetzt_geprüft 0 · nicht_anwendbar 0 = **80**.
