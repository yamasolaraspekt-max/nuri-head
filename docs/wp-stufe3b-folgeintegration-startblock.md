# Startblock + Konzept — WP Stufe 3b: Folgeintegration der Auslegungskette

**Git-Ausgangsstand:** `f2d9a33` (G0c-2/AP-4c committet 2026-07-14, path-scoped; 4 lokale Commits ahead, kein Push).
**Auftrag:** E9 Teil 2 (Planner in delegierter Yama-Rolle). **Begriffsdisziplin:** Stufe 3b = **Folgeintegration**
(Controller-Umstellung, Preisanbindung, Angebotsübergabe). Die Profil-Persistenz-**Mechanik** existiert (G0c-2)
und wird genutzt, nicht neu gebaut.

**Modus:** Planner zuerst (Bestandsaufnahme read-only abgeschlossen). **Dieses Dokument ist das Ergebnisdokument
der Planner-Runde** und meldet die aus den Ist-Belegen entstandenen **Pflicht-Stopps** vor dem Bau.

---

## 1. Ist-Belege (firsthand, read-only)

### 1.1 Orchestrator (Stufe 3a, vorhanden, noch nirgends verdrahtet)
- `app/Services/Auslegung/WpAuslegungsketteService::rankeKandidaten(WpAuslegungsEingabe): array`
  (Konstruktor: `WaermepumpenMatchService`, `BivalenzService`, `CatalogDeviceRepository`).
- Ergebnis = **Ranking** `{anwendbar, label, verbindlich=false, gewichte, gewichte_ausgesetzt, kandidaten[]{geeignet,
  ausschluss_grund, nibe, bivalenz{...}, kriterien.jaz, invest_quelle_fehlt, verfuegbarkeit_quelle_fehlt, score}, hinweise[]}`.
- Eingabe-DTO `WpAuslegungsEingabe`: `phiHlKw, qHeizKwh, qWwKwh, wwMitWp, vorlaufC, plz, wpTyp, heizsystem, belastbar, raumtempC, lat, lon, limit`.
- **Referenzen heute:** nur Service+DTO+Unit-Test. **Kein** Controller nutzt ihn.

### 1.2 Die drei fragmentierten Einstiege (Energie-Trias, teilen die Heizlast-Kerne)
| Controller | WP-Logik heute | Routen | Deckt Orchestrator das ab? |
|---|---|---|---|
| `Energie/HeizlastController` | `wpMatch->kandidaten(hl,'luft_wasser','heizkoerper',vorlauf)` hartkodiert; **kein** Bivalenz/JAZ/Gate/Ranking | `energie.heizlast[.berechnen]` | Teilweise (Match ja, Rest fehlt in Ctrl) |
| `Energie/EnergieAuslegungController` | Einzelgerät per `wp_index`; **JAZ + Warmwasser + Verbrauch + Kosten + Förderung**; **kein** Bivalenz/Ranking | `energie.wp-auslegung[.berechnen/.dokument]` | **Nein** — Orchestrator liefert Ranking, **nicht** Kosten/Förderung/Dokument |
| `Energie/EnergiekonzeptController` | `baueWp()` ~identisch zu B (Duplikat) + Sanierung + PV | `energie.energiekonzept[.berechnen/.dokument]` | **Nein** — s. B, eingebettet ins Gesamtkonzept |

Kern-Reuse-Ziele (Mirror, unverändert): `WaermepumpenMatchService::kandidaten`, `BivalenzService::berechne`,
`JazService::{jaz,vorlaufTemp,stromverbrauch}`, `CatalogDeviceRepository::heatPumps`.
**Weitere WP-Stränge (außerhalb Energie/, teilen die Kerne NICHT):** `Customer/Offer/AuslegungVorschlagController`
(+`AuslegungVorschlagService`, read-only Vorschlag), `WpKatalogMatchingController` — **nicht** Ziel der „drei".

### 1.3 E8-Preis (Invest / Verfügbarkeit)
- **Invest belegbar:** `products.retail_price` `decimal(12,2)` nullable (numerisch, Listenpreis/UVP je Gerät).
  Verknüpfung WP↔products: `product_heat_pump_specs.product_id → products.id`. **Aber:** die Kette transportiert
  heute **weder `product_id` noch Preis** (`CatalogDeviceRepository`-Select zieht nur `p.model`,`b.name`; DTO
  `HeatPumpKennlinie` ohne Preis; Kandidaten-Array ohne Preis). `retail_price` ist **nicht** im `Product::$fillable`
  → **Pflegegrad unbelegt** (Spalte da, Befüllung im Code nicht erzwungen).
- **Verfügbarkeit: bleibt `quelle_fehlt`** — kein Verfügbarkeitsfeld am WP-Gerät/products. Nur `inventories.quantity`
  (Seriennummern-/Standortbestand) als indirekte Mengenquelle — semantisch Bestand ≠ Lieferverfügbarkeit → **Yama-Frage**.
- `distributor_prices`/OMD/IDS bestätigt **inert** (nicht angebunden; bindet 3b nicht an).

### 1.4 offer_details + Persistenz-Nutzung
- **offer_details = Angebots-Dokumentkopf**, Positionen liegen als **JSON-Blob `sections[]`** (frei) — **keine feste
  Positionsspalte**; daneben relationale Zeilen `offer_product_lists` (Feld **`notes`** für sichtbares Label geeignet).
  Angebot↔Objekt: `Offer.alternative_id → LeadAlternativeAdd`; Belegkette Angebot→Auftrag→Rechnung vorhanden
  (`OfferDetail.document_status`, `Invoice.offer_detail_id`) — **nicht umbauen**.
  **Übernahme-Schreibpfad Auslegung→Angebot existiert noch nicht** (Vorschlag heute strikt read-only).
- **Profil-Persistenz-Feld für ein Auslegungs-ERGEBNIS fehlt:** Am `Anforderungsprofil`-Kopf gibt es als JSON
  **ausschließlich `gebaeude_geometrie`** (semantisch **Input**, nicht Ergebnis). Werte liegen in EAV
  `anforderungsprofil_werte` (**skalar je Schlüssel, Registry-Whitelist**) — ein **strukturiertes Ranking**
  (Geräteliste + Gewichtung + Label) passt dort **nicht** hinein (nur Text `ergebnis_hinweis`). `neueVersion`
  kopiert Werte + `gebaeude_geometrie` vorwärts (append-only) — ein Ergebnisfeld gäbe es nur nach additiver Spalte.

---

## 2. PFLICHT-STOPPS (vor Bau, Startblock-eigene Regeln ausgelöst)

### PS-1 — Persistenz Scope [MUSS] 2 braucht ein Schemafeld (es existiert nicht)
Scope 2 verlangt „Auslegungsergebnis + Ranking am versionierten Anforderungsprofil ablegen (bestehender
G0c-2-Mechanismus)". Die **Mechanik** (neueVersion) existiert, das **Ablagefeld nicht**: `gebaeude_geometrie`
ist Input (Zweckentfremdung abzulehnen → bräche Reproduzierbarkeit), EAV ist skalar/Whitelist, `ergebnis_hinweis`
nur Text. → **Startblock-Pflicht-Stopp „Schemaänderung nötig".** Das entspricht exakt dem in Scope 5 vorgesehenen
Fall („additive Migrationsdatei zur Freigabe, erste Migration → echte Yama-Freigabe").
**Vorschlag:** additive, nullable JSON-Spalte `anforderungsprofile.auslegung_ergebnis` (Cast `array`), in die
`neueVersion`-Vorwärtskopie aufgenommen; additiv, kein Drop/Rename, Rollback belegt. **Alternativen:** (b) Ergebnis
nur am Angebot (`offer_details.sections`) statt am Profil — dann Scope 2 entfällt/wird zu Scope 4; (c) nur Textvermerk
`ergebnis_hinweis` — verliert Struktur/Gewichtung (abzulehnen). **Entscheidung nötig, welche Wahrheit das
Auslegungs-Ergebnis führt: Profil (neue Spalte) oder Angebot.**

### PS-2 — Controller-Umstellung Scope [MUSS] 1: Vertrags-/Funktionslücke
Der Orchestrator liefert **Ranking**, die Views B/C erwarten **Einzelgerät + Kosten/Förderung/Dokument**, die der
Orchestrator **nicht** berechnet. Ein reiner Controller-Swap **entfernt Funktionalität (Kosten/Förderung/PDF)** →
Regression. „Keine Auslegungslogik verbleibt in Controllern" ist ohne **Erweiterung des Orchestrators** (Kosten/
Förderung = Kern-Touch → Nicht-Scope) nicht voll erfüllbar. **Entscheidung nötig:**
- **Variante 1 (empfohlen, additiv):** Orchestrator wird die führende Schiene **für Match+Bivalenz+Ranking+Label**;
  Kosten/Förderung/Warmwasser/Dokument bleiben als **nachgelagerte, klar getrennte** Controller-Schritte (kein
  Auslegungs-*Rechnen* mehr im Controller, aber Kosten/Förderung als eigene Dienste bleiben). Views bekommen einen
  Ranking-Block zusätzlich; Alt-Einzelgerät-Pfad deprecated markiert.
- **Variante 2:** Orchestrator um Kosten/Förderung erweitern → **Kern-/Scope-Ausweitung, Pflicht-Stopp**, größer.
- **Bestätigung Scope:** die „drei" = **Energie-Trias** (Heizlast / WP-Auslegung / Energiekonzept). Offer-Stränge NICHT.

### PS-3 (nachrangig) — E8-Preis-Verdrahtung berührt Repository + DTO
Invest ist aus `products.retail_price` ziehbar **ohne** die Rechen-Mirrors (Match/Bivalenz/Jaz) anzufassen, wenn
`CatalogDeviceRepository::heatPumps()`-Select additiv `p.retail_price`(+`p.id`) mitzieht und der Orchestrator den
Preis aus der ohnehin gekeyten Kennlinien-Collection liest (`HeatPumpKennlinie`-DTO +1 Property). **Frage:** gilt
`CatalogDeviceRepository`/`HeatPumpKennlinie` als „Kern diff-unverändert" (dann Pflicht-Stopp) oder als zulässig
additiv erweiterbarer Daten-/Transport-Layer? Zudem: **`retail_price` pro Kandidat leer → per-Gerät `quelle_fehlt`**
(kein alles-oder-nichts, kein erfundener Preis; Operanden-Gate). Verfügbarkeit bleibt `quelle_fehlt` (kein Feld).

---

## 3. Was OHNE Entscheidung sofort baubar ist (unblockiert)

- **Auflage B (Scope [MUSS] 5, code-only):** `AnforderungsprofilService::neueVersion` gegen Lost-Update härten —
  `lockForUpdate` beim Lesen der Basis-/Aktiv-Version innerhalb der bestehenden `DB::transaction`. Kein Schema, kein
  Kern-Mirror (ticket-eigene Infrastruktur, im Startblock ausdrücklich änderbar), härtet die gerade ausgelieferte
  G0c-2-Mechanik. **Sofort baubar + testbar (paralleler-Doppel-Save-Gegenprobe).** Der [KANN]-Unique-Index bräuchte
  Migration → an PS-1 koppeln (eine additive Migration für beides).

---

## 4. Arbeitspakete (nach Entscheidung PS-1/PS-2/PS-3)
1. **P0 (unblockiert):** Auflage B `lockForUpdate` + Lost-Update-Test.
2. **P1:** Controller-Umstellung Energie-Trias auf Orchestrator gemäß PS-2-Variante (führende Match/Ranking-Schiene;
   Alt-Pfade deprecated, nicht gelöscht; keine Auslegungs-Rechnung im Controller).
3. **P2:** Persistenz-Nutzung gemäß PS-1-Entscheidung (Profil-Spalte **oder** Angebot als führende Ergebnis-Wahrheit).
4. **P3:** E8-Preis (Invest aus `retail_price`, per-Gerät-`quelle_fehlt`-Fallback, Renormalisierungs-Tests;
   Verfügbarkeit `quelle_fehlt`) gemäß PS-3-Entscheidung.
5. **P4:** Übergabe → `offer_details.sections`/`offer_product_lists.notes` inkl. sichtbarem Label „informativ, nicht verbindlich".
6. **P5:** E2E-Integrationsreferenz mit Handrechnung (Objekt→Profil→Kette→Ranking→Persistenz→offer_details).
7. **P6 [KANN]:** Äquivalenz als A/B-Diff (Auflage C); [KANN] Unique-Index (mit PS-1-Migration).

## 5. Stop-Kriterium / Ballbesitz
**STOPP vor Bau** (außer P0, das ich auf Nicken sofort baue). Danach Bau der Pakete → Standardformel →
unabhängiger Evaluator → STOPP vor Commit. Kein Push.

---

## 6. Entscheidungen Yama/Planner (eingegangen — Scope-Teil aus E9/Teil 2 hierdurch **neu geschnitten**)

### PS-1 — ENTSCHIEDEN: Ergebnis-Heimat = versioniertes Anforderungsprofil (nicht Angebot)
- Führende Wahrheit des Auslegungsergebnisses = Profil. `offer_details` = nur späterer **Übergabesnapshot +
  Herkunftsreferenz**, nicht führend. Keine Zweckentfremdung von `gebaeude_geometrie`; nicht EAV-`werte`; nicht `ergebnis_hinweis`.
- Ablagefeld = additive nullable JSON-Spalte `anforderungsprofile.auslegung_ergebnis` — **grundsätzlich bestätigt,
  aber eigener Slice 3b-1** (eigene Migration, eigener Startblock, eigener Evaluator, **echte Yama-Freigabe** als
  erste Migration). **NICHT Teil von 3b-0.**
- **Stale-Regel (verbindlich):** neue Profilversion kopiert **Eingaben** vorwärts, **Ergebnisse NICHT** → auf `null`
  oder `stale`; kein stilles Übernehmen eines alten Rankings als scheinbar aktuelles Ergebnis. Mindestvertrag
  `auslegung_ergebnis`: `schema_version, status(current|stale), profile_version_id, input_hash, generated_at,
  orchestrator_version, catalog_snapshot_at, weights, weights_suspended, candidates, warnings, source_completeness`.

### PS-2 — ENTSCHIEDEN: Variante 1, Zerlegung in vier getrennte Dienste (keine Auslegungslogik im Controller)
Ziel-Architektur — Controller werden dünn, jede Fachverantwortung ein eigener Dienst:
| Dienst | Verantwortung | Status |
|---|---|---|
| `WpAuslegungsketteService` | Match → Bivalenz → Ranking → Label (`verbindlich=false`) | **existiert** (3a), wird verdrahtet |
| `WpCostingService` | Kosten → Betriebskosten → Vergleich | **neu** (Extraktion aus Controllern; wrappt bestehende `KostenService`/`VerbrauchsService`) |
| `WpFundingAssessmentService` | Förderhinweise → Förderfähigkeit → fehlende Voraussetzungen | **neu** (wrappt `FoerderungService`) |
| `WpDocumentService` | Dokumentdaten → PDF-Aufbereitung | **neu** (Dokument-/PDF-Aufbereitung der Trias) |
Der Orchestrator wird **nicht** um Kosten/Förderung/Dokument erweitert (kein Kern-Ausweitung); diese bleiben
getrennte, nachgelagerte Dienste. Alt-Fragmentpfade in den Controllern deprecated markiert, **nicht gelöscht**.

### PS-3 — OFFEN
Repo/DTO-Kern-Status für die Preis-Durchleitung (`CatalogDeviceRepository`-Select + `HeatPumpKennlinie`-DTO) +
Verfügbarkeitsquelle (`inventories.quantity` widmen vs. `quelle_fehlt`).

### 3b-0-Scope-Grenze — ENTSCHIEDEN
**3b-0 = P0 + Dienst-Zerlegung + Orchestrator-Verdrahtung.** Preis-Anbindung (PS-3: Transport-Layer additiv
erlaubt; Verfügbarkeit bleibt `quelle_fehlt`) und `offer_details`-Übergabe = **eigene Folge-Sub-Slices**.
**3b-1** = Persistenz-Spalte `auslegung_ergebnis` (Migration, echte Yama-Freigabe).

**P0 umgesetzt** (2026-07-14): `AnforderungsprofilService::neueVersion` lockForUpdate + max(version)-Ableitung;
Test `AnforderungsprofilTest::test_neue_version_haengt_ueber_max_an_nicht_ueber_stale_basis`; Suite 607/1 (Reverb).

---

## 7. Dienste + Verdrahtung — Extraktionskonzept (Planner, belegt) + Design-Gabelungen

### 7.1 Ist-Belege der drei Controller (firsthand gelesen)
| Controller | Operanden vorhanden | WP-Rechnung heute | Ausgabe |
|---|---|---|---|
| `HeizlastController::heizlastErgebnis` (`:120-184`) | nur `heizlast_kw` + `ziel_vorlauf_c` + `plz`; **kein** `qHeizKwh`/`qWwKwh`/Personen/Heizsystem | `wpMatch->kandidaten(hl,'luft_wasser','heizkoerper',vorlauf)` (Typ/System **hartkodiert**) | Vorschlags-Block `$wp` (Kandidatenliste) |
| `EnergieAuslegungController::wpErgebnis` (`:360-450+`) | **alle** (Formular: heizlast, heizsystem, wp_typ, Personen, ww, Verbrauch, Investition, Förder-Flags, Strompreis) | Einzelgerät per `wp_index` → `JazService`+`WarmwasserService`+`VerbrauchsService`+`KostenService`+`FoerderungService`; **kein** Bivalenz/Ranking | Kenndaten + Wirtschaftlichkeit + Förderung (Blade + Dokument) |
| `EnergiekonzeptController::baueWp` (`:256-326`) | wie B (eingebettet) | ~Duplikat von B + Sanierung/PV | Gesamtkonzept-Block |

**Kernbefund:** Der Orchestrator braucht `qHeizKwh, qWwKwh, plz, vorlaufC, wpTyp, heizsystem, phiHlKw`. **Nur B/C** haben
diese Operanden; **A (Heizlast) nicht** → dort würde der Orchestrator-Gate (`energie_fehlt`/`ww_entscheidung_fehlt`)
feuern. Und B/C wählen heute **ein Gerät per Index** (Nutzerauswahl), der Orchestrator liefert ein **Ranking** —
das ist ein **UX-Wechsel**, kein Feld-Swap.

### 7.2 Ziel-Zerlegung (PS-2 Variante 1)
- `WpAuslegungsketteService` (existiert): Match→Bivalenz→Ranking→Label.
- `WpCostingService` (neu): fasst `KostenService`+`VerbrauchsService`+Strom/Betriebskosten zu „Kosten→Betriebskosten→Vergleich".
- `WpFundingAssessmentService` (neu): fasst `FoerderungService` zu „Förderhinweise→Förderfähigkeit→fehlende Voraussetzungen".
- `WpDocumentService` (neu): Dokumentdaten→PDF-Aufbereitung (heute `wpErgebnis`-Array + `wp_auslegung_dokument.blade`).
- Controller werden dünn: Request→DTO→(Kette, Costing, Funding, Document)→View. Alt-Fragmentpfade deprecated, nicht gelöscht.

### 7.3 DESIGN-GABELUNGEN (brauchen Entscheidung/Fachagenten — Pflicht-Fachagenten-Regel greift)
- **G-A — Ranking vs. Einzelauswahl (Konzeption/Workflow/Frontend):** Ersetzt das Orchestrator-Ranking die manuelle
  `wp_index`-Wahl in B/C, oder ergänzt es sie (Ranking schlägt vor, Nutzer wählt weiter)? Das ändert View + Bedienfluss.
- **G-B — Heizlast-Controller (A):** Ohne Energie-/WW-Operanden kann der Orchestrator dort nicht voll laufen.
  (i) A bleibt beim schlanken `kandidaten()`-Vorschlag (nur Match, kein Ranking) — dann ist A **nicht** Teil der
  Orchestrator-Verdrahtung; oder (ii) A bekommt die fehlenden Felder (UX-Erweiterung). Empfehlung: **(i)** — A liefert
  weiter nur den Match-Hinweis; volle Kette bleibt B/C.
- **G-C — View-/Browser-Prüfung:** B/C-Views (`wp_auslegung*.blade`, `energiekonzept*.blade`) bekommen einen
  Ranking-Block → **Browser-/Screenshot-Prüfung Pflicht** (UI-Änderung, nicht nur Doku).
- **G-D — Kosten/Förderung im Ranking:** Werden Kosten/Förderung je Kandidat (N×) oder nur fürs gewählte Gerät
  gerechnet? (Performance + Bedeutung; Empfehlung: Ranking = Match/Bivalenz/JAZ; Kosten/Förderung erst fürs gewählte Gerät.)

### 7.4 Vorgeschlagene 3b-0-Paketfolge (nach G-A…G-D)
1. **P1a** `WpCostingService` + `WpFundingAssessmentService` + `WpDocumentService` als **reine Extraktion** aus B
   (Verhalten-identisch, Charakterisierungstests gegen `wpErgebnis`-Ist) — **kein** UX-Wechsel, kein Ranking. Risikoarm.
2. **P1b** Controller B/C rufen die Dienste statt Inline-Logik (Alt-Pfad deprecated) — Regressionsnachweis identische Ausgabe.
3. **P1c** Orchestrator-Verdrahtung + Ranking-Block in B/C gemäß G-A + Browser-Prüfung (G-C).
**P1a/P1b sind ohne Design-Entscheidung baubar** (reine, verhaltensgleiche Extraktion); **P1c braucht G-A…G-D.**
