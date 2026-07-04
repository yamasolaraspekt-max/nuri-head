# Heizkörper-Modul — Bauplan (Phase B · Feinkonzept B0)

> **Grundlage (bindend):** `docs/heizkoerper-bestandsanalyse.md` (Commit 8e6a157), wberechnung
> `CLAUDE.md`/`ARCHITEKTUR.md`. Stack ticket: Blade + **Alpine (nur Heizkörper-Modul, W8)**, MySQL,
> Test-Isolation `ticket_testing`. **Produktions-CRM (~3.000 Kunden), Branch `private/app-code-backup`.**
> **B0 ist reines Feinkonzept.** Bau (Stufen i–v) startet erst nach Freigabe dieses Dokuments.
> Status-Spalten je Stufe werden während des Baus hier fortgeschrieben.

## Gefallene Weichen (nicht neu diskutieren)
W1 Bestandsaufnahme = `radiator_installations` additiv erweitern · `ticket.radiators` = Wechselrichter-Altlast, **nie andocken, nicht umbenennen**.
W2 `supplier_article_map` mit **neutralem Schlüssel** (`hersteller`+`herst_artikelnr`); Kanäle = austauschbare Mapper hinter Interface; erster Mapper **IDS/Sonepar**; OMD/DATANORM nur Stub.
W3 ETIM **nicht** portiert; Katalog Stufe 1 = parametrisches **EN-442-Modell** (wberechnung-Referenz).
W6 **B-TICKET jetzt**; B-WBERECHNUNG (B5/B6/B8) **nur Spezifikation**, Cut-over-gebunden.
W8 **Alpine** ins Ticket, Muster aus wberechnung; CLAUDE.md-Eintrag „Alpine nur Heizkörper-Modul".

## Protokoll offene Entscheidungen 4/5/7 (keine blockiert — empfohlene Option gefolgt)
- **E4** (Spec-Muster): eigene typisierte Tabelle `product_radiator_specs` (via `product_id`), konsistent mit `katalog-reconciliation-plan §6.2`. → in Stufe (i).
- **E5** (Bilder): nur ticket-IDS opportunistisch; `IdsMapper` schreibt `bild_url` wenn Payload sie liefert; kein Investment in externe Bildquelle. → in Stufe (iii).
- **E7** (Einrohr/Strang B6): durch W6 aufgelöst — Struktur `heating_circuits` jetzt, Kaskadenlogik nur spezifiziert. → Stufe (i) Struktur / B-WBERECHNUNG Spec.

---

## 1. Reconciliation-Tabelle (Bestand → Nutzung)
| A2-Baustein | Einstufung | Bestand (Fundstelle) | Nutzung im Bau |
|---|---|---|---|
| B1 EN-442-Katalog | VORHANDEN (wb) | `heizkoerper` + `HeizkoerperSeeder` (Kermi) | Muster für `product_radiator_specs` (E4); q_norm/exponent/normbedingung übernehmen |
| B4 PerformanceService | VORHANDEN (wb) | `HeizkoerperService.php:20-90` (qReal/minVorlauf/leistungstabelle/status), **DB-frei** | 1:1 nach ticket `App\Services\Heizkoerper\RadiatorPerformanceService` portieren (Cut-over/Referenz); UI-Endpunkt ruft es |
| B7 Deckung + Ampel | VORHANDEN (wb) | `HeizkoerperService::status:82-90`, Ampel grün/gelb≥90%/rot | Ergebnis-UI (Stufe v) |
| B12 Voreinstellung/Hydraulik | VORHANDEN/TEILW. (wb) | `HydraulikService.php:32-95` (Verfahren B), `radiator_installations.supply_valve_presettable` | `HydraulicService` portieren; Voreinstellung gegen echte kvs (Stufe iv) statt generisch DN15 |
| B14 Stücklisten-Export | VORHANDEN (ticket) | `master_sets`/`master_set_components`(`type='zubehoer'`)/`deal_measurement_items` | CSV-Export **erweitern**, nicht duplizieren (Stufe v) |
| B11 Zubehör | TEILW. (ticket) | `master_set_components.type='zubehoer'` | Zubehör-Positionen andocken; Kompatibilität aus `valve_insert_compatibility` |
| B3 Bestandsaufnahme | KONFLIKT→W1 | `radiator_installations` (an Kundenakte, `NewLeadsController:2197`) | additiv erweitern (Stufe i), Rechen-Input-Link |
| B13 supplier_article_map | KONFLIKT→W2 | `distributor_prices`, `resolveProduct()`/`upsertDistributorPrice()` | neutraler Map + `IdsMapper` auf Sonepar-Anbindung |

---

## 2. B-TICKET — Migrationsliste (Stufe i · ausschließlich ADD COLUMN / CREATE TABLE, umkehrbar)

> Typen MySQL/Laravel. Alle neuen Spalten **nullable** oder mit Default. Alle FKs `nullOnDelete`.
> Jede Migration mit realem `down()`. **Kein** Eingriff an `ticket.radiators`.

**M1 `product_radiator_specs`** (E4, EN-442-Katalog)
`id · product_id (FK products, nullable) · hersteller (string) · typ (string, z.B. '22') · bauart (enum: kompakt/glieder/roehren/konvektor/geblaesekonvektor/bad) · bauhoehe_mm (uint) · bautiefe_mm (uint) · q_norm_w_pro_m (decimal 8,2) · norm_bedingung (string, default '75/65/20') · exponent_n (decimal 4,2, default 1.30) · quelle (string, nullable) · aktiv (bool, default 1) · timestamps`

**M2 `radiator_installations`-Erweiterung** (W1, ADD COLUMN)
`radiator_spec_id (FK product_radiator_specs, nullable) · anzahl (uint, default 1) · baulaenge_mm (uint, nullable) · bautiefe_mm (uint, nullable) · anschluss_position (enum: seitlich/unten/mittel/wechselseitig, nullable) · anschluss_fuehrung (enum: zweirohr/einrohr, nullable) · ventil_einsatz_bestand (string, nullable) · kopf_norm_bestand (enum: M30x1_5/RA/RAV/RAVL/sonstige, nullable) · heating_circuit_id (FK heating_circuits, nullable) · q_norm_w_pro_m_override (decimal 8,2, nullable) · exponent_n_override (decimal 4,2, nullable) · typ_konfidenz (enum: sicher/geschaetzt, nullable)`
*(vorhanden, nicht neu: room/room_size/width/height/depth/niche_*/supply_valve(_presettable)/return_valve(_present)/renew_thermostat_head/limbs/radiator_type/design/image)*

**M3 `accessory_categories`**: `id · code (string, unique) · name (string) · sort_order (uint, default 0) · timestamps`

**M4 `accessories`**: `id · accessory_category_id (FK) · hersteller (string) · herst_artikelnr (string) · name (string) · typ (string, nullable) · dn (uint, nullable) · kvs_werte (json, nullable) · kopf_anschluss_norm (enum wie M2.kopf_norm_bestand, nullable) · einrohr_tauglich (bool, default 0) · voreinstellbar (bool, default 0) · product_id (FK products, nullable) · quelle (string, nullable) · aktiv (bool, default 1) · timestamps` · **unique(hersteller, herst_artikelnr)**

**M5 `valve_insert_compatibility`**: `id · hk_hersteller (string) · hk_serie (string, nullable) · baujahr_von (year, nullable) · baujahr_bis (year, nullable) · einsatz_accessory_id (FK accessories, nullable) · kopf_anschluss_norm (enum, nullable) · adapter_accessory_id (FK accessories, nullable) · quelle (string) · note (string, nullable) · timestamps`

**M6 `supplier_article_map`** (W2 neutral): `id · hersteller (string) · herst_artikelnr (string) · supplier_channel (enum: ids/omd/datanorm) · distributor_id (FK distributors, nullable) · lieferanten_artikelnr (string, nullable) · ek_preis (decimal 12,2, nullable) · vk_preis (decimal 12,2, nullable) · bild_url (string, nullable) · product_id (FK products, nullable) · accessory_id (FK accessories, nullable) · last_synced_at (timestamp, nullable) · timestamps` · **unique(hersteller, herst_artikelnr, supplier_channel)**

**M7 `radiator_connection_factors`** (B5-Konfig, Daten später): `id · anschluss_position (enum) · anschluss_fuehrung (enum) · bauart (string, nullable) · faktor (decimal 4,3) · quelle (string) · note (string, nullable) · timestamps`

**M8 `heating_circuits`** (Struktur jetzt, Kaskade am Cut-over): `id · customer_id (FK new_leads, nullable) · alternative_id (FK lead_alternative_adds, nullable) · name (string) · typ (enum: zweirohr/einrohr, default zweirohr) · ziel_vorlauf_c (decimal 4,1, nullable) · spreizung_k (decimal 3,1, nullable) · reihenfolge (uint, nullable) · meta (json, nullable) · timestamps`

**Stufe-(i)-Testplan:** neue `RadiatorSchemaMigrationTest` (Feature): `migrate:fresh` → alle 8 Objekte existieren mit erwarteten Spalten (`Schema::hasColumn`); **`migrate → migrate:rollback → migrate`** grün gegen `ticket_testing`; bestehende ticket-Tests unverändert grün. Rollback-Durchlauf ins Ticket-Pendant `scripts/ticket-mysql-check.sh` aufnehmen (falls fehlt, anlegen — analog `wberechnung-mysql-check.sh`).

---

## 3. B-TICKET — betroffene Models / Services / Routes / Views + Stufen

| Stufe | Neu/erweitert | Testplan |
|---|---|---|
| **(i) Migrationen** | 8 Migrationen (M1–M8) | Schema+Rollback (oben) |
| **(ii) Stammdaten** | Models `RadiatorSpec`, `Accessory`, `AccessoryCategory`, `ValveInsertCompatibility`, `SupplierArticleMap`, `RadiatorConnectionFactor`, `HeatingCircuit`; erweitertes `RadiatorInstallation` (fillable+casts+Relationen); Seeder Ventiltechnik (§4) | Seeder idempotent, Zeilen-Soll, **Quelle je Zeile als Kommentar**; unsichere Nummern weggelassen + im Report |
| **(iii) IDS-Mapper** | Interface `App\Services\Suppliers\SupplierArticleMapper` + `IdsMapper` (auf bestehender Sonepar/`SupplierConnectorService`-Anbindung) + leere `OmdMapper`/`DatanormMapper`-Stubs | Unit: IDS-Payload → `supplier_article_map`-Zeile (Hersteller/ArtNr/EK/VK/bild_url opportunistisch); Stubs werfen `NotImplemented`/no-op |
| **(iv) Kompatibilitäts-Service** | `App\Services\Heizkoerper\CompatibilityService` (Regeln D3, §5); Port `RadiatorPerformanceService` + `HydraulicService` (DB-frei, aus wberechnung) | Unit je Regel (§5): Ventil-HK/Kompakt-HK/Einrohr-Sperre/voreinstellbar/Voreinstellstufe/Altnorm |
| **(v) UI** | Alpine einführen (**CLAUDE.md-Eintrag zuerst**); Routen `heizkoerper.*` (Bereich 8 „Energie & Auslegung", ticket-Web-Guard); Views: Aufnahme-Formular (auf `radiator_installations`), SVG-Schema (Front+Seitenprofil), Stücklisten-Ansicht je Raum; CSV-Export über B14 erweitern | Feature: Aufnahme-CRUD; Rechen-Endpunkt liefert EN-442-Tabelle+Ampel; Export erzeugt Positionen |

**Routen (Vorschlag, Bereich 8):** `heizkoerper.aufnahme.{index,store,update,destroy}` · `heizkoerper.berechnen` (POST, Rechen-Endpunkt) · `heizkoerper.schema` (SVG) · `heizkoerper.stueckliste.{show,export}`. Auto-Typerkennung aus `bautiefe_mm` mit Unsicherheits-Auswahl (`typ_konfidenz`).

---

## 4. Stammdaten-Recherche (Stufe ii) — Umfang
Ventiltechnik mit **Hersteller-Artikelnummern aus öffentlichen Preislisten/Katalogen** (Quelle als Seeder-Kommentar):
Heimeier **V-exakt II**, Oventrop **AV 9**, Danfoss **RA-N**; Köpfe **M30×1,5 / RA-Klemm / RAV-RAVL-Altnorm**;
Adapter, Hahnblöcke (einrohr-umschaltbar), Rücklaufverschraubungen, Austausch-Einsätze.
`valve_insert_compatibility`: HK-Hersteller→Serie→Baujahr→Einsatz→Kopf-Anschluss für **Kermi/Purmo/Buderus/Vogel&Noot/Stelrad/Zehnder**, Spannen dokumentiert, als Stammdaten editierbar.
**Regel: keine erfundenen Nummern — unsicher = weglassen + im Report listen.** (Web-Recherche in Stufe ii, mit Quellenangabe.)

---

## 5. Kompatibilitäts-Regeln (Stufe iv, aus Ergänzung D3)
1. **Ventil-Heizkörper** (integriertes Ventilunterteil) → nur **Einsatz** wählen → **Kopf/Adapter** nach `kopf_anschluss_norm`.
2. **Kompakt-/Standard-HK** (ohne integr. Ventil) → **Ventil + Kopf + Rücklaufverschraubung**.
3. **Einrohr** (`anschluss_fuehrung=einrohr`) → **nur** `einrohr_tauglich=true`-Armaturen. **Zweirohr-Armatur an Einrohr = harte Sperre** (Validierungsfehler).
4. **Voreinstellbare Ventile immer bevorzugen** (`voreinstellbar=true`).
5. **Voreinstellstufe** aus `ṁ = Q/(c·ΔT)` gegen `kvs_werte` (nur wenn Heizlast+Spreizung vorhanden; sonst „am Objekt einregulieren").
6. **Altnorm-Kopf** (RAV/RAVL) → **Austauschempfehlung** (Adapter oder Einsatztausch).
Jede Regel = eigener Unit-Test.

---

## 6. B-WBERECHNUNG — Spezifikation (NUR Spec, Cut-over-gebunden, NICHT implementieren)

> Andockpunkt-Basis: `RadiatorPerformanceService` (Port von `HeizkoerperService`) und die Ampel/Deckung (B7).
> Alle drei setzen **auf** `qReal()/minVorlauf()/leistungstabelle()` auf, ohne diese zu ändern.

### B5 — Anschlussart-Korrekturfaktoren
Effektive Leistung `Q_eff = Q_real · f_anschluss`. `f_anschluss` aus `radiator_connection_factors`
(Schlüssel `anschluss_position × anschluss_fuehrung × bauart`). Referenzfall **seitlich oben/unten (zweirohr) = 1,000**;
Untenanschluss/Mittelanschluss/wechselseitig < 1,0 (Werte aus Norm/Herstellerangabe in Stufe ii/Cut-over füllen —
**nicht erfinden**). Andock: Multiplikator **nach** `qReal()` (neuer Parameter `?float $anschlussFaktor=null`, Default 1,0 = verhalten unverändert).

### B6 — Einrohr-Strangkaskade (iterativ)
Für einen Einrohr-Strang mit Gesamt-Volumenstrom `V̇_strang` (l/h), Zumischrate `a ∈ (0,1]` (Anteil, der durch den HK statt Bypass fließt) und Reihenfolge der HK 1..N:
- Strang-Eintritt HK n: `t_ein(n) = t_strang(n)` mit `t_strang(1) = t_vor_strang`.
- HK-Vorlauf durch Beimischung: `t_vor(n) = t_raum(n) + a · (t_ein(n) − t_raum(n))` (Zumischrate senkt die wirksame Übertemperatur).
- Leistung: `Q_n = qReal([hk_n], t_vor(n), t_raum(n), spreizung_lokal)` (bestehende Methode, je HK).
- Strang-Abkühlung: `t_strang(n+1) = t_strang(n) − Q_n / (1,163 · V̇_strang)`.
- **Iteration** über 1..N; Abbruch, wenn `t_strang(n) − t_raum(n)` unter Mindest-Übertemperatur fällt (→ Rest-HK unterversorgt, Ampel rot).
Ausgabe je HK: `t_vor(n)`, `Q_n`, Deckung, Ampel; je Strang: letzter versorgter HK, Grenz-Vorlauf.
Andock: neuer `HeatingCircuitCascadeService::rechne(HeatingCircuit $c, array $hks)` nutzt `qReal()` unverändert; persistiert nach `heating_circuits.meta`.

### B8 — Empfehlungs-Engine
Reihenfolge (erste passende Empfehlung je HK, alle gegen `leistungstabelle()`/`minVorlauf()`/`status()` geprüft):
1. **Typ-Upgrade** (z.B. 22→33) **nur** wenn `bautiefe_neu ≤ nische_tiefe` **und** `baulaenge/bauhoehe` passen (Nischen-/Tiefenprüfung aus `radiator_installations`); Gewinn aus neuer `q_norm`.
2. **Länge +** (nächste Katalog-Baulänge) wenn Wandbreite/`niche_left/right` es zulässt.
3. **Lüfter-Nachrüstung** (Aktiv-Konvektions-Kit) mit **szenarioabhängigen %-Gewinnen** je Systemtemperatur (Faktor je Szenario aus Katalog/Herstellerangabe — Stufe ii/Cut-over füllen).
4. **Gebläsekonvektor** (Austausch) inkl. `kuehlung_flag` (kühlfähig ja/nein) **und** `kondensat_flag` (Kondensatablauf nötig) als Ausgabe-Attribute.
5. **2. Heizkörper** im Raum (zusätzliche Fläche), wenn Wandfläche vorhanden.
6. **Szenario-Fallback**: höheres Systemtemperatur-Szenario akzeptieren **mit JAZ-Hinweis** (höherer Vorlauf → schlechtere JAZ; Kopplung an WP-Auslegung `WpKennlinieService`).
Ausgabe: geordnete Empfehlungsliste je HK mit erwartetem Deckungsgrad je Szenario nach Maßnahme.
Andock: `RadiatorRecommendationService::fuerRaum(...)` liest `leistungstabelle()`; ändert keinen Rechenkern.

---

## 7. Governance-Pflichten (während Bau)
- **Neue `ticket/CLAUDE.md`** (ticket hat keine): Einträge (a) „**Alpine im Ticket ausschließlich für das Heizkörper-Modul zugelassen**" (vor Stufe v), (b) „**DO NOT DOCK: `ticket.radiators` = Wechselrichter-Altlast** (`product.inveter.store`) — nie andocken, nicht umbenennen", (c) Modulgrenze: **Physik = wberechnung** (Port), **Katalog/Zubehör/Stückliste/Aufnahme = ticket**, Schnittstelle = `RadiatorPerformanceService`/`HydraulicService`-Signaturen. *(wberechnung `ARCHITEKTUR.md` bleibt unangetastet — read-only.)*
- Nach **jeder** Stufe: Commit (sprechende Message), Tests grün, Status hier fortschreiben.
- **Bestehende Tests dürfen nicht brechen** (ticket UND wberechnung — aktueller Stand: 281 grün laut ABSCHLUSSBERICHT, lokal 271 gemessen; als Regressions-Baseline behandeln).
- **Verboten:** nuriva-/Planner-API-Verhalten ändern, ETIM-Portierung, OMD-Implementierung, Rename `ticket.radiators`, Framework-Zusätze außer Alpine, erfundene Artikelnummern, Implementierung B5/B6/B8.

## 8. Status (wird während Bau fortgeschrieben)
| Stufe | Status | Commit | Test |
|---|---|---|---|
| (i) Migrationen | offen | — | — |
| (ii) Stammdaten | offen | — | — |
| (iii) IDS-Mapper | offen | — | — |
| (iv) Kompatibilität | offen | — | — |
| (v) UI | offen | — | — |

## 9. Offene Fragen an Yama (nummeriert)
1. **Katalog-Datenquelle B1:** Die EN-442-Kennwerte (`q_norm_w_pro_m`, `exponent_n`) für `product_radiator_specs` — aus wberechnungs `HeizkoerperSeeder` (Kermi-kalibriert) **übernehmen** (im Feinkonzept begründete Code-/Datenübernahme, W3-konform) oder in Stufe ii neu recherchieren? *(Empfehlung: übernehmen — bereits kalibriert.)*
2. **`ticket-mysql-check.sh`:** Es existiert kein Ticket-Pendant zum wberechnung-Re-Check. Stufe (i) soll den Rollback-Durchlauf dort aufnehmen — **neu anlegen** (empfohlen) bestätigt?
3. **Bau-Freigabe:** B0 ist fertig. Stufe (i) (Migrationen, nur gegen `ticket_testing`) starten — jetzt oder nach deiner Durchsicht?

> **B0 Ende. Kein Bau ohne Freigabe von B0 (Auftragsregel „erst nach fertigem B0").**
