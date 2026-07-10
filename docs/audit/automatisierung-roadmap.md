# CRM-AUTOMATISIERUNG-MASTER — Stufe 3/4: Bewertung + abhängigkeits-sortierte Roadmap

> Baut auf Stufe 1 (`experten/`) + Stufe 2 (`automatisierung-stufe2.md`). Frequenzen von Yama (STOPP 2). **Endet am Schluss-Stopp:** Yama priorisiert → gewählte Posten laufen durch den Planner→Generator→Evaluator-Zyklus (`docs/agents/`). Reine Analyse, kein Bau hier.

## 0 — BUG-FIX-Liste (getrennt von den Hebeln — Datenintegrität, nicht Effizienz)
Diese verfälschen Daten und werden gefixt, **weil sie falsch sind**, nicht weil sie Arbeit sparen. Eigener kleiner Auftrag, je mit Pflicht-Stopp.
| ID | Bug | Schaden | Fix (klein) |
|---|---|---|---|
| **BUG-1** | `junk()` (DealController) setzt Gewerk-Stufe zurück, **storniert die Rechnungen NICHT** → Auftrag(Junk) + Rechnung(offen) = **Phantom-Forderung** | Finanziell (falsche offene Posten/Umsatz) | `cancelInvoicesForDeal()` (existiert für `destroy`) auch im `junk`-Pfad aufrufen; Verhaltens-Test |
| BUG-2 (Kandidat) | `qualified()` schreibt ganze Fehlersätze in `new_leads.status` → Status als Auswertungsgröße zerstört | Auswertung/KPI unbrauchbar | Fehlertext in eigenes Feld/Flash, `status` bleibt Werteliste (hängt an Weiche 1) |
| BUG-3 (Kandidat) | `invoices`: Live-Status `open` nicht im Model-Enum; `paid_at` < `issue_date` (Seed-Artefakt) | GoBD-fragwürdig, Konsistenz | Enum ergänzen/prüfen; Seed-Anomalie klären (Accounting-Zone → deren Instanz) |

## 3 — Informationsbedarf-Matrix (Phase × Projekt-Typ)
Gemeinsame Basis (immer) + typ-spezifische Zusätze. Typen (Yama): **T1 PV+Speicher EFH-Bestand ~40% · T2 WP EFH-Bestand+BEG ~25% · T3 PV+WP+Wallbox Neubau ~20% · T4 PV MFH ~15%.**

| Phase | Gemeinsame Basis (alle) | T1 PV+Speicher | T2 WP+BEG | T3 Kombi Neubau | T4 PV MFH |
|---|---|---|---|---|---|
| **Lead** | Kunde/Objekt/Adresse, Gewerk-Wahl, Zuständiger | Verbrauch kWh, Dach | Heizlast-Grobwert, Alt-Heizung | alle drei | Gebäude/WEG, Einheiten |
| **Angebot** | Positionen, Preis, Angebotsdok | **Dach→Montage, Modul+Ausrichtung→WR/MPPT, Speichergröße** | **Heizlast (DIN), Vorlauftemp, Heizflächen, WP-Matching, BEG-Antrag** | PV+WP+Wallbox + Lastmanagement | PV-String + **Mieterstrom-Abrechnungsmodell** |
| **Auftrag** | Auftragsbestätigung, Material | PV-Stückliste, Netzanmeldung | WP + hydraul. Abgleich (Danfoss B, Förder-Pflicht) | kombinierte Stückliste | + Zähler-/Messkonzept MFH |
| **Montage** | Einsatzplan, Checkliste, Bericht | PV-Montagechecklist (DC/AC) | WP-Aufstellung/Anschluss/Abgleichprotokoll | beide Gewerke-Checklisten | + Zählerschrank/Kaskade |
| **Abnahme** | Protokoll, Mängel, Übergabe | Inbetriebnahme PV, Einspeisezusage | WP-Inbetriebnahme, Abgleich-Nachweis | beide | + MFH-Abnahme je Einheit |
| **Rechnung** | Schlussrechnung, Förderabzug | EEG/Einspeisung | **BEG-Förderabzug** | kombiniert | Mieterstrom-Abrechnung |

→ **Der typ-spezifische Bedarf konzentriert sich in Angebot/Auslegung** (deshalb ist die Auslegungs-Brücke + Smartrouting der größte 🅱-Hebel). Basis-Felder (Kunde/Objekt/Position) sind universell.

## 3 — Hebel-Bewertung (je Hebel: Klasse · a/b/c · Fehler-Folgen · Config · Baustein · Wert)
Wert = Frequenz × Ersparnis × Fehlervermeidung ÷ Aufwand (Frequenz aus Yama, Ersparnis SCHÄTZUNG-offen).

### 🅰 UNIVERSELL (jedes Projekt, sofort — kein Konfig-Fundament)
| ID | Hebel | a/b/c | Fehler-Folge | Baustein | Aufwand | Wert |
|---|---|:--:|---|---|:--:|:--:|
| A1 | Lead→Angebot-Task am Stage-Move | **a** VOLL | billig-reversibel (Task zu viel) | FollowUpCreator + FK-Hook | S | **hoch** (jeder Lead) |
| A2 | Feld-Abschluss→Nachfass (tote FollowUpCreator-Slots) | **b** Vorschlag | billig (Nachfass zu viel) | FollowUpCreator | S | hoch |
| A3 | Rechnungs-Fälligkeit ableiten (`issue_date`+`payment_terms`) | **a** VOLL | billig (Datum korrigierbar) | payment_terms (da) | S | mittel-hoch |
| A4 | Objekt-Adresse aus Kunde durchreichen | **a** VOLL | billig | FK-Kette | S | mittel |
| A5 | „voll fakturiert & bezahlt"-Flag | **a** VOLL | billig | invoices | S | mittel |
| A6 | GR→Bestand koppeln (Wareneingang bucht) | **b** Vorschlag | teuer wenn falsch (Bestand) → mit Bestätigung | goods_receipts/inventories | M | mittel |
| A7 | Angebot→Auftrag-Kickoff (Einsatzplanung auto-anstoßen) | **b** Vorschlag | teuer (Fehldisposition) → Vorschlag+Bestätigung | Einsatzplanung (gebaut!) | M | **hoch** |

### 🅱 KONTEXT-GESTEUERT (brauchen Smartrouting-Fundament ZUERST)
| ID | Hebel | a/b/c | Fehler-Folge | Baustein | Aufwand | Wert |
|---|---|:--:|---|---|:--:|:--:|
| B0 | **Smartrouting-Fundament** (Regeln pflegen + Aufrufer einhängen) | — | Enabler | SmartroutingService (gebaut, 0 Regeln) | M | **Schlüssel** |
| B1 | Auslegungs-Brücke je Gewerk (Konfigurator, s. u.) | **b** Vorschlag | **teuer-haftungsrelevant** (Fehlplanung) → immer Vorschlag+Fach-Freigabe | Auslegungs-Kerne + AnforderungsprofilAdapter | L | **höchster** |
| B2 | Checklisten-Routing je Gewerk/Phase/Objekt-Typ | **a** (Auswahl)/b | billig (falsche Checkliste sichtbar) | Formular-Engine + Smartrouting | M | hoch |
| B3 | Pflichtfelder/Informationsbedarf je Konstellation | **b** | mittel | Formular-Engine + Config-Felder | M | mittel-hoch |
| B4 | Materialbedarf je Gewerk aus Auslegung (Menge-aus-Geometrie) | **b** Vorschlag | teuer (Bestellfehler) → Vorschlag | DealMaterialList (abgeleitet) + Auslegung | M | hoch |

### 🅲 FUNDAMENT-Posten (ermöglichen 🅱, kein direkter Hebel)
| ID | Posten | Warum |
|---|---|---|
| F1 | **Config-Felder strukturieren** (Neubau/Bestand + EFH/MFH + BEG/BAFA/KfW als Werteliste) | Voraussetzung für Smartrouting-Kontext + Konfigurator; heute leer/frei (`building_types`=0, `foerderungen`=0) |
| F2 | Abnahme-Datenmodell (Protokoll/Mängel/Unterschrift) | weißer Fleck; Voraussetzung für Abnahme→Rechnung-Trigger; Baustein `MaintenanceProtocol` wiederverwendbar |

## 4 — Abhängigkeits-sortierte Roadmap (Umsetzungs-Reihenfolge)
```
BUG-1 junk-Storno ─────────────────────────────► (sofort, eigener Fix, integritätskritisch)

WELLE 1 — 🅰 Universelle Quick-Wins (parallel, kein Fundament nötig)
  A1 Lead→Angebot-Task ─┐
  A3 Fälligkeit          ├─ hoher Wert/S-Aufwand, sofort verdrahtbar
  A4 Objekt-Adresse      │
  A5 Bezahlt-Flag        ┘
  A2 Feld→Nachfass · A7 Auftrag-Kickoff (Vorschlag) · A6 GR→Bestand

WELLE 2 — 🅲 Fundament
  F1 Config-Felder (Neubau/Bestand/EFH/MFH/Förderung) ──► ermöglicht B0/B1/B2/B3
  B0 Smartrouting-Fundament (Regeln + Aufrufer) ──► SCHLÜSSEL vor allen 🅱
  F2 Abnahme-Datenmodell ──► ermöglicht Abnahme→Rechnung

WELLE 3 — 🅱 Kontext-Hebel (nach B0/F1)
  B2 Checklisten-Routing · B3 Pflichtfelder je Typ
  B1 Auslegungs-Brücke/Konfigurator (größter Hebel, s. Ableitungs-Baum) · B4 Materialbedarf
```
**„blockiert durch"-Kanten:** B1/B2/B3/B4 ← B0 (Smartrouting) ← F1 (Config-Felder). A-Welle blockiert durch nichts.

## Konfigurator-Ableitungs-Baum (Ausformung von B1 — der größte Hebel)
Der Konfigurator ist die konkrete „Auslegungs-Brücke je Gewerk". Je Gewerk ein Ableitungs-Baum **mit Operanden-Gate** (kein erfundener Wert; bei Unsicherheit Vorschlag+Fach-Freigabe):

**PV:** `Dachtyp/Ziegel` → **Montagesystem** (Halter/Schiene) → `Modul + Ausrichtung/Neigung + Verschattung` → **String-Layout** → **WR + MPPT + (Optimierer)** → Stückliste + Ertrag (PVGIS).
**WP:** `Heizflächen + Baualter + Fläche` → **Heizlast (DIN)** → `Vorlauftemperatur` (aus Heizflächen, RadiatorPerformance) → **WP-Modell-Matching + Bivalenzpunkt** → Stückliste + JAZ/Wirtschaftlichkeit.
**Kombi:** PV + WP + **Lastmanagement/HEMS** + Wallbox-Lastpfad.

### Lücken-Liste — welche Fachregeln noch hinterlegt werden müssen
| Regel | Status heute | Quelle/Baustein |
|---|---|---|
| Dach/Ziegel → Montagesystem-Kompatibilität | **fehlt** (nur `dachziegel_db_schema.sql` + `claude_code_prompt_montagesysteme/solarhalter` als Referenz im Wissens-Register) | Wissens-Register CODE-014/016 |
| Modul + Ausrichtung → WR/MPPT-Matching + Optimierer | **teilweise** (Inverter-Sizing in Playground; ticket-seitig offen) | Playground-Transplant |
| Verschattung/Ertrag | **da** (PVGIS via `pvgis-proxy.php`, Wissens-Register CODE-011) | einhängen |
| Heizflächen → Vorlauftemperatur | **da** (`RadiatorPerformanceService`, Heizkörper-Modul) | einhängen (Muster: `HeizkoerperController::uebernehmen`) |
| Heizlast (DIN) | **da** aber isoliert (0 Aufrufer) | `HeizlastService` + `AnforderungsprofilHeizlastAdapter` verdrahten |
| WP-Modell-Matching + Bivalenz | **da** (`BivalenzService` VDI 4645, `JazService`) — dormant | einhängen |
| BEG/BAFA-Förderregeln je Typ | **fehlt** (`foerderungen`=0) | F1 + neue Fachregeln |
| Mieterstrom-Abrechnungsmodell (T4 MFH) | **nur Prototyp** (Wissens-Register) | eigener Posten, HV-Naht |

→ **Kernaussage:** Der Konfigurator ist zu **~60% „verdrahten"** (Heizlast/Vorlauftemp/PVGIS/Bivalenz gebaut, isoliert) und **~40% „Fachregeln neu hinterlegen"** (Dach→Montage, WR/MPPT-Matching, Förderung). Das Heizkörper-Modul (`uebernehmen`→`DealMeasurementItem`) ist das bewiesene Andock-Muster.

## Zwei Hebel-Klassen getrennt (deine Vorgabe)
- **UNIVERSELL** (Welle 1) = die billigen Sofort-Gewinne, konfig-unabhängig.
- **KONTEXT-GESTEUERT** (Welle 3) = der große Wert, aber erst nach dem Smartrouting-Fundament (B0/F1).

## Produkt-Varianz-Konsolidierung
Die PV/WP/Wallbox-Logik existiert heute teils in **drei Kopien** (Auslegungs-Kerne + Prototypen + Config-Blade 25k Z.). Ziel: **eine parametrisierte Engine je Gewerk hinter dem SmartroutingService** (Gewerk = Parameter, nicht getrennte Spur) — statt driftender Pfade. Das ist der Architektur-Kern von B0+B1.

## Verzahnung mit laufenden Strängen
- **Cut-over/wberechnung → Auslegung:** die Auslegungs-Kerne (Heizlast/PV) stammen daher — B1 hängt sie ein.
- **Accounting → Finanz:** FiBu-Buchungs-Trigger (0 Buchungen) ist die Finanz-Naht (Accounting-Instanz-Zone, TABU für Bewertung — nur Naht benennen).
- **Formular-Engine → Konfig-Steuerung:** FS-02/03/04/05 + SmartroutingService sind das Fundament für B2/B3.

## Grenzen-Kapitel (ehrlich — was dieser Befund NICHT sicher weiß)
- **Frequenzen** sind Yamas Schätzung (Dev-DB dünn) — die Rangfolge ist damit belastbar, aber die absoluten Zeitersparnisse bleiben SCHÄTZUNG-offen (Zeit/Handgriff nicht gemessen).
- **Konstellations-Grenzfälle** (Sonderförderung, Denkmal, gemischte MFH) sind nicht erschöpfend — die Matrix deckt die 4 Haupttypen.
- **Fachregel-Tiefe** (WR/MPPT-Matching, Förder-Details) braucht Yamas/Fachplaner-Wissen — der Ableitungs-Baum nennt die Lücken, füllt sie nicht.
- **Client-JS-Verkettung** (dynamische Aufrufe) serverseitig nicht prüfbar — als NICHT-VERIFIZIERT markiert wo relevant.

## ⛔ Schluss-Stopp
Yama priorisiert: welche Welle-1-Quick-Wins zuerst · ob F1/B0 (Smartrouting-Fundament) als nächster großer Posten · ob der Konfigurator-Ableitungs-Baum so stimmt (+ Fachregel-Lücken füllen). Gewählte Posten → Planner→Generator→Evaluator-Zyklus.
