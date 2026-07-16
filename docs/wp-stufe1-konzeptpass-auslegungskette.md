# WP-Auslegung — Stufe-1-Konzept-Pass (Vier-Stufen-Methode, read-only)

**Stand:** 2026-07-14 · **read-only, kein Bau, kein Commit, kein Push, keine Migration.** · **Heimat: ticket** (Yama-Entscheidung 2026-07-14).
**Beauftragungsquelle:** Direkte Yama-Freigabe, 14.07.2026. **Status: Stufe 1 abgenommen** (Planner in delegierter Yama-Rolle, 14.07.2026); §7-Entscheidungen E-WP1–E-WP5 unten eingetragen.
**Methode:** Vier-Stufen-Fahrplan (`fahrplan_methode_domaenen.md`) — Stufe 1 „Konzeptionell optimieren" (Ist-Beleg + eine Wahrheit + Ziel-Datenmodell + Naht-/Gate-Punkte). Aufgabe ist **Vernetzen**, nicht ein zweiter WP-Rechner.
**Grundlage/Reuse:** `docs/bereich2-wp-auslegungswizard-gap-analyse.md`, `docs/wberechnung-uebernahme-inventur.md`, `docs/ap2-pv-inventur.md`, `docs/ap3-plattform-klammer-weiche.md` (Option E) — hier konsolidiert + firsthand am ticket-Code belegt (2026-07-14).

---

## 0. Korrektur einer Fahrplan-Annahme (belegt)

Der Fahrplan nennt wberechnung als „Heimat der Auslegung" und `BivalenzService` als brachliegende Krone. **Firsthand geprüft (2026-07-14): der gesamte Auslegungs-Rechenkern liegt bereits in ticket** und `BivalenzService` hat **0 Aufrufer in ticket**:

| Service | Ort in ticket |
|---|---|
| `BivalenzService`, `JazService`, `WaermepumpenMatchService`, `HeizlastProjektService`, `VerbrauchsService`, `HoehenkorrekturService`, `WpKennlinieService`, `KlimaBinService`, `HeizlastRechner`, `WarmwasserService` | `app/Services/Heizlast/*` |
| `KlimaPlzService` | `app/Services/Klima/KlimaPlzService.php` |
| Geräte-Katalog | `CatalogDeviceRepository->heatPumps()` → `WpKennlinie`-Instanzen |

→ **Die WP-Verknüpfung ist ein ticket-Posten.** (wberechnung bleibt nur Herkunfts-/Referenzbeleg; keine zweite Wahrheit.)

---

## 1. Ist-Bestandsaufnahme je Service (I/O, verdrahtet vs. isoliert)

| Schritt | Service (Reuse) | Eingang (I) | Ausgang (O) | Status |
|---|---|---|---|---|
| PLZ/Klima | `KlimaPlzService::getNormAussentempForPlz(plz)` / `findByPlz` | PLZ | NAT, θ_m, Heizgradtage, Höhe, Bins (via `KlimaBinService`) | 🟢 verdrahtet (mehrere Controller) |
| Höhe | `HoehenkorrekturService::korrigiere(θe_ref, standorthoehe, bezugshoehe)` | Referenz-NAT + Höhen | korrigierte NAT | 🟢 verdrahtet |
| Verbrauch/Technik | `VerbrauchsService::berechne(HeizlastEingabe)` | HeizlastEingabe | Verbrauchs-Heizlast/-Energie (`?array`) | 🟢 verdrahtet |
| Bedarf (Heizlast) | `HeizlastProjektService::fuerProjekt()` | Projekt (Räume/Bauteile/Geometrie) | Φ_HL kW, Standard-/Auslegungsheizlast, Hüllbilanz | 🟢 4 Aufrufer |
| Kandidaten | `WaermepumpenMatchService::kandidaten(benoetigtKw, wpTyp, heizsystem, vorlaufC, limit)` | benötigte kW + Typ + Heizsystem + Vorlauf | Geräteliste {id, hersteller, modell, leistung_kw, cop, scop, max_vorlauf_c, modulation_min/max_kw, deckung_pct, status} | 🟡 verdrahtet, aber **hartkodiert `luft_wasser`/`heizkoerper`** am Aufrufer |
| **Bivalenz je Kandidat** | **`BivalenzService::berechne(WpKennlinie, phiHlKw, qHeizKwh, qWwKwh, wwMitWp, vorlaufC, plz, raumtempC, lat, lon)`** | **eine** Geräte-Kennlinie + Gebäudebedarf + Klima | Bivalenzpunkt (VDI 4645), Deckung@NAT, E-Stab-Anteil, Laufstunden, Wärme-/Strom-Split, Saison-Verteilung, JAZ, Warnungen | 🔴 **0 Aufrufer — die Krone liegt brach** |
| JAZ | `JazService::jaz(HeizlastEingabe)` / `vorlaufTemp` / `klasse` / `stromverbrauch(e, qHeiz, qWw)` | HeizlastEingabe (+ Energie) | JAZ, Auslegungs-Vorlauf, Stromverbrauch | 🟢 2 Aufrufer |
| Ranking → Übergabe | — (fehlt) | Kandidaten + Bivalenz je Kandidat | gerankte Empfehlung → `offer_details.sections` | 🔴 **keine führende Schiene** |

**Belege:** Signaturen firsthand aus `app/Services/Heizlast/*` + `app/Services/Klima/KlimaPlzService.php` (2026-07-14); `BivalenzService`-0-Aufrufer per `grep` bestätigt.

---

## 2. Konsolidierungs-Befund (Fragmentierung)

Die Kette ist heute über **drei Controller** fragmentiert (bestätigt):
- `HeizlastController` — Heizlast + `WaermepumpenMatchService::kandidaten` (hartkodiert `luft_wasser`/`heizkoerper`), **ohne Bivalenz**.
- `EnergieAuslegungController::wpBerechnen` — JAZ, **ohne Bivalenz-Ranking**.
- `EnergiekonzeptController` — JAZ (weitere Nutzung).

→ **Keine durchgezogene Auslegung.** Ziel (Stufe 3): **eine führende Auslegungs-Schiene** (Orchestrator), die die Services in Soll-Reihenfolge fährt und die Krone einhängt. Was Reuse wird / was bleibt, entscheidet der Bau-Pass — **keine** Parallelberechnung, Backend führend.

---

## 3. Die Naht: `BivalenzService` je Kandidat (exakt, belegt)

`BivalenzService::berechne` nimmt **genau eine** `WpKennlinie` + den Gebäudebedarf. Ranking entsteht durch **N Aufrufe (je Kandidat)**:

```
für jeden Kandidat k aus WaermepumpenMatchService::kandidaten(...):
    wpKennlinie_k = CatalogDeviceRepository->heatPumps()[k.id]   // liefert WpKennlinie
    bivalenz_k = BivalenzService::berechne(
        wpKennlinie_k,
        phiHlKw      = <aus HeizlastProjektService::fuerProjekt>,
        qHeizKwh     = <Jahres-Heizarbeit>,        // Operand, s. Gate-Punkt G2
        qWwKwh       = <Jahres-WW-Energie>,        // Operand, s. Gate-Punkt G2
        wwMitWp      = <Fach-Entscheidung>,        // Operanden-Gate G3
        vorlaufC     = <JazService::vorlaufTemp / Nutzer>,
        plz          = <Objekt lead_alternative_adds.postcode>,
        raumtempC=20, lat, lon
    )
Ranking = sortiere Kandidaten nach {Deckung@NAT, E-Stab-Anteil, JAZ, Laufstunden, Warnungen}
```

**Konstruktor-Abhängigkeiten** (bereits in ticket): `BivalenzService(KlimaBinService, WpKennlinieService)` → per Container auflösbar, **keine neue Infrastruktur nötig**.

---

## 4. Fachregel-Lücken → Operanden-Gate (nicht raten, s. Fahrplan §6)

| # | Lücke | Heute | Soll (Stufe 1 Entscheidung) |
|---|---|---|---|
| G1 | Gerätetyp/Heizsystem **hartkodiert** `luft_wasser`/`heizkoerper` (Aufrufer von `kandidaten`) | fix | aus Objekt/Formular ableiten (FBH vs. HK, Sole/Luft) → sonst Vorschlag+Bestätigung |
| G2 | **Jahres-Heizarbeit/WW-Energie (kWh)** für Bivalenz | nicht durchgereicht | Herkunft festlegen: `VerbrauchsService` vs. aus Bedarf abgeleitet — **eine Wahrheit** |
| G3 | `wwMitWp` (WW über WP ja/nein) | nicht gesetzt | Fach-Entscheidung → Operanden-Gate |
| G4 | Vorlauf-Grenze/Emittentenart | teils fix | aus Auslegung/Heizsystem, sonst markieren |
| G5 | **Belastbarkeit der Heizlast** (AP-1) muss vor „verbindlichem Ranking" grün sein | vorhanden (AP-1-Gate) | Ranking nur „verbindlich" bei belastbarer Heizlast (schon gebaut: `HeizlastBelastbarkeit`) |

---

## 5. Ziel-Datenmodell (führende Wahrheit)

Konsistent mit **AP-3 Option E** (bereits entschieden): keine neue Auslegungs-Tabelle.
- **Bedarf/Auslegung/Ranking-Ergebnis → `Anforderungsprofil`** (versioniert, am **Objekt** verankert), PV/WP über `SchluesselRegistry` + je-Gewerk-Adapter. Das Bivalenz-Ranking wird als Auslegungs-Snapshot am aktiven Profil geführt (nicht in einem Controller-Transient).
- **Belastbarkeit** über AP-1 (`HeizlastBelastbarkeit`) — Ranking „verbindlich" nur bei belastbarer Heizlast.
- **Übergabe → `offer_details.sections`** (einzige Positions-Wahrheit) via `AuslegungVorschlagService` (read-only Vorschau existiert); Preis blockiert bis OMD/IDS.
- **Klima** = `KlimaPlzService`/`KlimaBinService` (eine Quelle); **Geräte** = `CatalogDeviceRepository` (ein Katalog).

---

## 6. Soll-Kette (Stufe-2-Vorschau, belegt)

`Objekt (lead_alternative_adds) → PLZ/Klima (KlimaPlzService/KlimaBinService + Höhenkorrektur) → Verbrauch/Technik (VerbrauchsService) → Bedarf (HeizlastProjektService, Belastbarkeit AP-1) → Kandidaten (WaermepumpenMatchService) → Bivalenz je Kandidat (BivalenzService) → Ranking → Angebotsübergabe (Anforderungsprofil → offer_details)`

Gate-Punkte G1–G5 (s. §4) sind Vorschlag+Bestätigung, kein stilles Weiterrechnen.

---

## 7. Entscheidungen E-WP1–E-WP5 (abgenommen 2026-07-14, per Config änderbar)

- **E-WP1 Orchestrator-Ort:** **entschieden** — ein Auslegungs-Orchestrator-Service in ticket, verankert am versionierten `Anforderungsprofil` (AP-3 Option E). Keine Auslegungslogik in Controllern; die 3 fragmentierten Controller werden **Konsumenten** der einen führenden Schiene. Übergabe → `offer_details`. Keine neue Tabelle, keine zweite Wahrheit.
- **E-WP2 Energie-Operand:** **entschieden** — **gemessener Verbrauch (Verbrauchsmethode) hat Vorrang** vor bedarfsgerechneter Ableitung (Heizlastmethode). Herkunft als Feld mit Quelle + Vertrauensstatus am `Anforderungsprofil`; fehlt Verbrauch → **dokumentierter** Rückfall auf Heizlastmethode, nie stillschweigend.
- **E-WP3 wberechnung-Doppelkopie:** **entschieden** — ticket-Kopie ist führend; wberechnung-Kopie **deprecated** (Wissensregister-Eintrag in ticket). Schreibende Markierung in wberechnung = eigener Mini-Auftrag über deren Heimat-Instanz (Mehr-App-Regel), im Arbeitskompass vorgemerkt — blockiert ticket-Arbeit nicht.
- **E-WP4 Ranking (Default, Config-änderbar):** **entschieden, zweistufig.** **Stufe A = hartes Eignungs-Gate** (Muss, kein Gewicht): Heizlastdeckung am Bivalenzpunkt gem. G1–G5, Vorlauf ≤ Systemgrenze, `wwMitWp` korrekt. **Stufe B = Sortierung der Geeigneten:** JAZ 50 % · Investitionskosten 30 % · Verfügbarkeit 20 %. NIBE als Primärpartner **gekennzeichnet, nicht versteckt geboostet**. Alle Gewichte in **einer** Config-Quelle. Jedes Ranking trägt bis zur finalen Yama-Geschäftsentscheidung UND bis AP-1-Belastbarkeit (G5) das Label **„informativ, nicht verbindlich"**.
- **E-WP5 Gerätetyp/Heizsystem-Ableitung (G1):** **Architektur-/Umsetzungsfrage → Default + Markierung** (E-WP5-Regel): Ableitung aus Objekt/Formular-Feldern (Sole/Luft, FBH/HK); fehlt das Feld → Operanden-Gate (Vorschlag+Bestätigung), nie der hartkodierte `luft_wasser`/`heizkoerper`-Fall stillschweigend. Wird im Stufe-2-Dokument als markierte Default-Annahme geführt.

---

## 8. Reuse-Nachweis & Nicht-Ziele

- **Reuse:** alle 8 Services + Katalog + Klima existieren in ticket; die Krone `BivalenzService` ist fertig, nur unverdrahtet. Kein Service wird neu gebaut.
- **Nicht-Ziel:** kein zweiter WP-Rechner, keine Parallelberechnung, kein JS-Hauptrechnen, keine neue Auslegungs-Tabelle, keine Automatisierung vor Stufe 2/3-Abnahme.

---

## 9. Nächster Schritt (STOPP)

Stufe 1 (Konzept) liegt vor. **Stufe 2 (Workflow)** = den Soll-Ablauf §6 mit Eventualitäten/Gate-Punkten ausformulieren; **Stufe 3 (Verknüpfung)** = Orchestrator-Schiene bauen, `BivalenzService` je Kandidat einhängen, Ranking → Anforderungsprofil → Angebotsübergabe. Beides erst nach Yama-Abnahme dieses Konzepts + Klärung der Entscheidungspunkte §7. **Kein Bau vor Abnahme.**

*Ende Stufe-1-Konzept-Pass. Read-only, kein Bau, kein Commit, kein Push.*
