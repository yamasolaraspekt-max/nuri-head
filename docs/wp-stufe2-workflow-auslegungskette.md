# WP-Auslegung — Stufe 2: Workflow-Konzept der Auslegungskette

**Stand:** 2026-07-14 · **Workflow-Konzept (Vier-Stufen-Methode, Stufe 2), kein Bau, kein Commit, kein Push, keine Migration.** · **Heimat: ticket.**
**Beauftragung:** „Bau frei Stufe 2" (Planner in delegierter Yama-Rolle, 14.07.2026). **Grundlage:** `docs/wp-stufe1-konzeptpass-auslegungskette.md` (abgenommen, E-WP1–E-WP5 entschieden).
**Zweck:** den End-to-End-Soll-Ablauf der WP-Auslegung festlegen — je Schritt Verhalten, Eventualitäten, Gate-Punkte, Fehlerzustand. **Vernetzen, nicht neu rechnen.** Umsetzung (Orchestrator) ist Stufe 3.

---

## 1. Soll-Workflow (End-to-End)

Führende Schiene: **ein** `WpAuslegungsketteService` (Stufe 3, ticket), verankert am versionierten `Anforderungsprofil` (E-WP1). Backend führt; kein JS-Hauptrechnen.

| # | Schritt | Service (Reuse) | Eingang | Ausgang | Gate/Eventualität |
|---|---|---|---|---|---|
| 0 | **Objekt-Kontext** | `lead_alternative_adds` (Objekt) | Objekt-ID | PLZ, lat/lon, Gebäudedaten | Objekt fehlt → Blocker (kein Auslegung ohne Objekt) |
| 1 | **PLZ/Klima** | `KlimaPlzService` + `KlimaBinService` + `HoehenkorrekturService` | PLZ, Standorthöhe | NAT (höhenkorrigiert), θ_m, Heizgradtage, Bins | PLZ unbekannt → Operanden-Gate (PLZ erfragen), kein Default-Klima raten |
| 2 | **Verbrauch/Technik** | `VerbrauchsService::berechne(HeizlastEingabe)` | Verbrauchsdaten | Jahres-Heizarbeit/-WW (kWh), Verbrauchsheizlast | **E-WP2:** Verbrauch **vorrangig**; fehlt → dokumentierter Rückfall auf Schritt 3 |
| 3 | **Bedarf (Heizlast)** | `HeizlastProjektService::fuerProjekt()` + AP-1 `HeizlastBelastbarkeit` | Räume/Bauteile/Geometrie (durch Topologie-Gate G0b) | Φ_HL kW, Standard-/Auslegungsheizlast, **Belastbarkeitsstufe** | **G5:** unbelastbar → Ranking bleibt „informativ" (nie „verbindlich") |
| 4 | **Betriebsparameter** | `JazService::vorlaufTemp/klasse`, Objekt/Formular | Heizsystem, Emittent | Vorlauf °C, Systemgrenze, `wwMitWp` | **E-WP5/G1:** Gerätetyp/Heizsystem aus Feld, sonst Vorschlag+Bestätigung; **G3:** `wwMitWp` Fach-Entscheidung |
| 5 | **Kandidaten** | `WaermepumpenMatchService::kandidaten(benoetigtKw, wpTyp, heizsystem, vorlaufC)` | benötigte kW + Typ + Heizsystem + Vorlauf | Geräteliste (id, Leistung, COP/SCOP, Modulation, max_vorlauf, deckung_pct, status) | keine Kandidaten → Hinweis + Gate (Katalog/Kriterien prüfen), kein leeres Ranking als „fertig" |
| 6 | **Bivalenz je Kandidat** | `BivalenzService::berechne(WpKennlinie_k, Φ_HL, qHeiz, qWw, wwMitWp, vorlauf, plz, …)` **N× (je Kandidat)** | Kandidat-Kennlinie + Bedarf + Klima | Bivalenzpunkt, Deckung@NAT, E-Stab-Anteil, Laufstunden, JAZ, Wärme-/Strom-Split, Warnungen | Kennlinie unvollständig (W55 fehlt) → Warnung am Kandidat, nicht stiller Ausschluss |
| 7 | **Ranking** | Orchestrator (Stufe 3), Kriterien E-WP4 | Kandidaten + Bivalenz je Kandidat | gerankte, gelabelte Empfehlung | s. §2 |
| 8 | **Übergabe** | `AuslegungVorschlagService` → `offer_details.sections` | Ranking + Auslegung | Angebots-Vorschlag (read-only, unbepreist bis OMD/IDS) | Preisanker fehlt (OMD/IDS) → Position `katalog_anker_fehlt`, kein erfundener Preis |

---

## 2. Ranking-Workflow (E-WP4, zweistufig)

**Stufe A — hartes Eignungs-Gate (Muss, kein Gewicht).** Ein Kandidat ist nur „geeignet", wenn ALLE erfüllt:
- Heizlastdeckung am Bivalenzpunkt plausibel (gem. G1–G5-Operanden-Gate);
- Vorlauftemperatur ≤ Systemgrenze des Geräts (`max_vorlauf_c`);
- `wwMitWp` korrekt berücksichtigt.
Ungeeignete Kandidaten werden **ausgewiesen mit Grund** (nicht still entfernt).

**Stufe B — Sortierung der Geeigneten (Config-Gewichte):** **JAZ 50 % · Investitionskosten 30 % · Verfügbarkeit 20 %.** NIBE (Primärpartner) wird **gekennzeichnet, nicht versteckt geboostet**. Alle Gewichte in **einer** Config-Quelle (`config/wp_ranking.php` o. ä., Stufe 3) — Yama ändert sie per Config, kein Code.

**Label-Pflicht:** Jedes Ranking trägt **„informativ, nicht verbindlich"**, solange (a) die finale Yama-Geschäftsentscheidung aussteht **und/oder** (b) die Heizlast-Belastbarkeit (G5, AP-1) nicht „belastbar" ist. Erst beides erfüllt → „verbindlich" möglich.

---

## 3. Datenfluss & Persistenz

- **Führende Wahrheit:** Bedarf/Auslegung/Ranking-Snapshot am **versionierten `Anforderungsprofil`** (Objekt-verankert, AP-3 Option E). Keine neue Tabelle, kein Controller-Transient als Wahrheit.
- **Energie-Herkunft (E-WP2):** Feld mit `quelle` + `vertrauensstatus` (`measured_on_site`/`from_document`/…); Verbrauch vorrangig, Rückfall dokumentiert.
- **Belastbarkeit (G5):** `HeizlastBelastbarkeit` (AP-1) steuert das „verbindlich/informativ"-Label.
- **Übergabe:** `offer_details.sections` (einzige Positions-Wahrheit) via `AuslegungVorschlagService`; Preis blockiert bis OMD/IDS.
- **Klima/Geräte:** eine Quelle (`KlimaPlzService`/`KlimaBinService`; `CatalogDeviceRepository`).

---

## 4. Gate-Punkte & Operanden-Gate (Übersicht)

| Gate | Auslöser | Verhalten (nie raten) |
|---|---|---|
| G1/E-WP5 | Gerätetyp/Heizsystem-Feld fehlt | Vorschlag+Bestätigung; kein hartkodierter `luft_wasser`/`heizkoerper`-Default |
| G2/E-WP2 | Jahres-Energie unklar | Verbrauch vorrangig; Rückfall Heizlastmethode **dokumentiert** |
| G3 | `wwMitWp` unklar | Fach-Entscheidung erfragen |
| G4 | Vorlauf-Grenze/Emittent | aus Auslegung; sonst markieren |
| G5 | Heizlast nicht belastbar (AP-1) | Ranking bleibt „informativ, nicht verbindlich" |

---

## 5. Eventualitäten / Fehlerzustände (kein stiller Datenverlust)

- **Kein Verbrauch** → Heizlastmethode, sichtbar dokumentiert (E-WP2).
- **PLZ unbekannt** → Klima nicht raten, erfragen.
- **Keine Kandidaten** → Hinweis + Gate (Typ/Heizsystem/Katalog prüfen), leeres Ranking ist kein „fertig".
- **Kennlinie unvollständig** (z. B. W55 fehlt) → Warnung am Kandidat, nicht stiller Ausschluss.
- **Unbelastbare Heizlast** → gesamte Empfehlung „informativ".
- **Preisanker fehlt** → Position unbepreist (`katalog_anker_fehlt`), kein Schätzpreis.

---

## 6. Orchestrator-Design (Konzept für Stufe 3, kein Code hier)

Ein `WpAuslegungsketteService` (ticket) führt Schritte 1–7 in Reihenfolge, liest/schreibt am `Anforderungsprofil`, hängt `BivalenzService` **je Kandidat** ein (die brachliegende Krone), baut das Ranking (E-WP4) und übergibt an `AuslegungVorschlagService`. Die drei Controller (`HeizlastController`, `EnergieAuslegungController`, `EnergiekonzeptController`) werden **Konsumenten** dieser Schiene (Auslegungslogik wandert aus den Controllern heraus); wo sie heute selbst rechnen, werden sie in Stufe 3 read-only/delegierend gestellt. **Mirror-Analogie:** berührt Stufe 3 gespiegelte Dateien (`GeometrieAbleitungService`/`RaumHuelleService`/`HeizlastRechner`/`BivalenzService` selbst) verändernd → **Stopp** (nur verdrahten, nicht umschreiben).

---

## 7. Markierte Default-Annahmen (E-WP5-Regel)

- **A1 (Gerätetyp/Heizsystem):** Default = aus Objekt/Formular-Feld ableiten; Feld-Namen/-Herkunft wird im Stufe-3-Startblock am Code belegt (heute hartkodiert am `HeizlastController`-Aufrufer). Falls kein geeignetes Feld existiert → reines Operanden-Gate.
- **A2 (Config-Ort Ranking):** Default `config/wp_ranking.php` (Stufe 3); endgültiger Name nach ticket-Konvention.
Beide Annahmen sind Architektur-/Umsetzungsfragen (kein Geschäfts-/Preis-/Haftungsstopp) und laufen per E-WP5 mit Markierung weiter.

---

## 8. Nicht-Ziele & nächster Schritt

**Nicht-Ziele:** kein zweiter WP-Rechner, keine Parallelberechnung, kein JS-Hauptrechnen, keine neue Auslegungs-Tabelle, keine Automatisierung, kein Mirror-Eingriff, keine Preis-/Katalogerfindung.

**Nächster Schritt (nach Prüf-Stopp):** **Stufe 3 (Verknüpfung)** als eigener Generator-/Evaluator-Zyklus mit eigenem Startblock: `WpAuslegungsketteService` bauen, `BivalenzService` je Kandidat einhängen, Ranking nach E-WP4 (Config + Label-Pflicht), Übergabe an `offer_details`-Vorschlag. Regeln: kein `git add -A`, Dateiplan vor Commit-Freigabe, Mirror-Analogie-Stopp, Ranking-Label Pflicht, unabhängiger Evaluator.

*Ende Stufe 2 (Workflow). Prüf-Stopp — Abnahme durch Yama/Evaluator vor Stufe 3. Kein Bau, kein Commit, kein Push.*
