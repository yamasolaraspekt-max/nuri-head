# Phase 0 — FiBu-Transplantations-Befund (Accounting-Instanz, 2026-07-05, read-only)

> **Zeitpunkt-Bericht** (Reife/Kollision/Plan). Dauerregeln stehen woanders (s. §1). **Kein Bau** — der Stufenplan (§8) braucht Yamas Freigabe (Pflicht-Stopp am Ende). Quelle: playground-FiBu (18 Migr./45 Models), ticket-Belegkette.

## §1 Umsatzdefinition (Verweis, nicht Kopie)
Es gilt **`docs/accounting/umsatzdefinition.md`** (Dauerregel): `invoices` = einzige Umsatz-Wahrheit · Buchungssatz je **festgeschriebener** Rechnung · `status` = Festschreib-Gate · `deal_invoices` stillgelegt. Die FiBu **dockt nur an die festgeschriebene Rechnung an** (CLAUDE.md → Daten-/Ketten-Schutz).

## §2 Kollisions-Karte + Konzept-Vergleich je Kettenglied
> Grundsatz: **ticket-Schema bleibt Wahrheit der Kette; playground passt sich an.** playground ist nicht automatisch schlechter — wo es konzeptionell besser ist, wird das **additiv** ans ticket-Schema gehoben (nie zweite Struktur produktiv).

| Kettenglied | ticket-Konzept | playground-Konzept | wo playground ehrlich besser | Ziel-Schema (additiv) |
|---|---|---|---|---|
| **Angebot** (`offers`=29 · `offer_details`=29) | Angebot mit Positionen in **`offer_details.sections` (JSON)** + `total_net/tax_rate/total_gross`, Snapshot | (kein direktes Äquivalent — FiBu beginnt erst an der Rechnung) | — | **unverändert.** FiBu fasst Angebot nicht an. |
| **Sets** (`group_sets`/`costing_sets`/`master_sets`) | Sets bündeln Artikel; Kalkulation über `costing_sets` | — | — | **unverändert.** |
| **Artikel** (`article_groups`=15) | ticket-Artikel-DB = **EIN Katalog** (Weiche) | eigene Produkt-/Artikel-Modelle | nein (Katalog-Weiche gilt) | **unverändert.** Erlöskonto-Mapping später **additiv** je Artikel/Kategorie. |
| **Auftrag** (`deals`=14) | Auftrag = `deals`, verknüpft Angebot→Rechnung (`deals.offer_detail_id`) | Projekt-zentriert (Weiche 5: kein „Projekt") | nein | **unverändert.** |
| **Bestellliste** (Material-/Bestell-Listen aus Deal/Angebot, `DealMaterialList*`) | Einkaufs-/Materialseite | Kreditoren/Wareneingang | Kreditoren-Buchung (Wareneinsatz) — **später**, nicht Umsatzpfad | **später additiv** (Kreditoren-Seite, eigene Stufe). |
| **Rechnung** (`invoices`=11 · `invoice_items`=0) | **Beleg-Modell stark:** `subtotal/tax_rate/tax_amount/total_amount`, `service_from/to`, `account_id`, `deal_id`, `invoice_items` mit `source_item_type/id/payload`; `status` varchar; `invoice_no` (nullable) | **volle FiBu:** Journal (append-only/Festschreibung), Konten, GoBD-Maker-Checker, Nummernkreis, DATEV-EXTF | **Festschreibung/Unveränderlichkeit · lückenloser Nummernkreis · Maker-Checker · Positions-Erlöskonten-Split · Journal/Buchungssatz** — all das fehlt ticket | **ticket-`invoices` bleibt der Beleg; playground-Buchungs-/Journal-/GoBD-Schicht dockt additiv an** (neue FiBu-Tabellen, kein Umbau der invoices). |

**Kern-Synthese:** ticket liefert das **Beleg-/Positions-Modell** (invoices + invoice_items, chain-verankert). playground liefert die **Buchungs-/Journal-/GoBD-/DATEV-Schicht**, die ticket komplett fehlt. Sie werden **nicht** vermischt: die FiBu **liest** die festgeschriebene Rechnung und **erzeugt** den Buchungssatz in **eigenen** Tabellen (journal/accounts/documents) — kein Feld der Kette wird verändert.

## §3 invoice_items = **ÜBERTRAGUNGS-Gap** (belegt), kein Daten-Gap
- **Ableitbar aus der Kette:** `offer_details.sections` (JSON-Positionen) + `deals.offer_detail_id` + **`InvoiceController@dealItems`** (`decodeOfferSections(...)`, `:801/831`) + `invoice_items.source_item_type/source_item_id/source_payload` (Herkunfts-Link). Die Positionen **existieren** in der Kette; sie sind nur für die 11 Bestandsrechnungen nicht materialisiert.
- **Einordnung:** **Übertragungs-/Verdrahtungs-Gap** (künftige Rechnungen können Positionen tragen), **nicht** fehlende Daten.
- **Direktive:** die **11 Bestandsrechnungen werden NICHT nachbefüllt** → **Kopf-Buchung als dokumentierter Startzustand** (ein Erlöskonto je Rechnung aus `subtotal`/`tax_amount`). **Positions-Erlöskonten-Split = eigene spätere Stufe** (§8), keine Vorbedingung der ersten Buchung.

## §4 SKR03/04 — externe Klärung (Steuerberater), **keine Empfehlung**
Die **Wahl** des Kontenrahmens (SKR03 vs. SKR04) ist eine **fiskalische Entscheidung → Klärung extern mit dem Steuerberater** (die In-house-Buchung ändert das nicht). **Technisch keine Blockade:** playground ist SKR-flexibel — `accounts` trägt `account_number` **+ `datev_account_number` + `chart_of_account_id`** (eigene `chart_of_accounts`-Tabelle), `normal_balance` (Soll/Haben), `is_tax_relevant`, `default_tax_code_id`; `accounting_clients.default_chart_of_account_id` wählt den Rahmen je Mandant. → Der Rahmen ist ein **Seed/Config**, keine Struktur; die Wahl kann spät fallen, ohne Schema-Umbau.

## §5 Kollisions-Karte playground-FiBu ↔ ticket (Tabellen/Routen/Auth)
- **FiBu-eigene Tabellen** (`accounting_journal_*`, `accounts`, `account_mappings`, `accounting_documents`, `accounting_datev_exports`, `dunning*`, …) = **kollidieren mit nichts** in ticket → nach Phase-0-Freigabe **vorziehbar** (Bau-Takt-Ausnahme), Marker `imported_from='playground'`.
- **Einziger Berührungspunkt zur Kette = `invoices`** (read-only Anker für die Buchungssatz-Erzeugung) — keine Struktur-Kollision, additive FK-Referenz (`invoice_id`) von der FiBu-Seite.
- **Auth/RBAC:** playground-FiBu nutzt `permission:*`-Gating (eigenes RBAC). ticket hat `is_admin`/`user_rolls`. → **Adapter** (FiBu-Gates auf ticket-Auth abbilden), RBAC-Ablösung bleibt eigener Strang (nicht hier).
- **Geteilte Datei:** nur `RELEASE-MANIFEST.md` (additive Zeilen).

## §6 Gap-Liste „wirklich buchen können" (Arbeitspakete)
1. **Kontenrahmen-Seed** (SKR nach Steuerberater-Klärung) + `chart_of_accounts`/`accounts`/`account_mappings` — additiv.
2. **Debitoren-Mapping** Kunde→Konto (`customer_id` → Debitorenkonto; Sammel- oder Einzeldebitor) — additiv.
3. **USt-/Steuerschlüssel** (`tax_codes`, Zuordnung `tax_rate`→Steuerschlüssel→USt-Konto) — additiv.
4. **Nummernkreis** lückenlos/GoBD (ticket `invoice_no` heute nullable → Festschreib-Nummernkreis-Service) — additiv.
5. **Buchungs-Engine + Journal** (append-only, Storno statt Edit) — FiBu-eigen.
6. **GoBD-Maker-Checker-Gate** vor Festschreibung — FiBu-eigen (playground-Service, ungeprüft → Beweis-Tests).
7. **DATEV-EXTF-Export** — muss playgrounds eigenen Konformitäts-Prüfer **UND** DATEV-Testpaket **grün** bestehen (Startzustand lt. Audit: **rot**).
8. **Positions-Erlöskonten-Split** (invoice_items→Erlöskonten je Artikel/Kategorie) — **eigene spätere Stufe**, nicht Vorbedingung.
9. **Kein gelebter Belegfluss** in playground (Seeds dünn) → Zahlen-Wahrheit gegen Referenz-Fälle absichern (§7-Gate).

## §7 Qualitäts-Gates (Engineering-Pflicht, unabhängig vom Steuerberater)
- **GoBD als harte Systemeigenschaften:** Unveränderlichkeit gebuchter Sätze (append-only, Storno statt Edit) · lückenlose Journal-Nummerierung · Maker-Checker vor Festschreibung · Audit-Trail. **Je Eigenschaft ein Beweis-Test.**
- **DATEV-EXTF:** Export besteht playgrounds Konformitäts-Prüfer **+ DATEV-Testpaket grün VOR dem ersten echten Export.** „Rot" = dokumentierter Start, „grün" = Gate.
- **Zahlen-Wahrheit:** Bilanz/BWA gegen **handgerechnete Referenz-Fälle** (Test-Geschäftsjahr, bekannte Soll-Werte; Harness-Muster: Marker, idempotent, Teardown 0).
- **Produktions-Disziplin:** alles gegen `ticket_testing` bis expliziter Release-Freigabe · RELEASE-MANIFEST-Pflege · Live-DB (3000 Kunden) erst nach bestandenem Testpaket + Yama-Abnahme.

## §8 Stufenplan (i)–(n) — je Stufe Pflicht-Stopp + Verifikation
- **(i) Schema additiv + Marker** — FiBu-Tabellen (journal/accounts/documents) mit `imported_from`, `down()`-rollback-bewiesen. *Gate: Migration grün, Teardown 0.*
- **(ii) Kontenrahmen-Seed** (SKR nach externer Klärung) + Debitoren-/USt-Mapping. *Gate: Seed idempotent, marker-basiert.*
- **(iii) Belegfluss-Anker** — festgeschriebene `invoices` → **Kopf-Buchungssatz** (Forderung an Erlöse+USt). *Gate: Referenz-Fall handgerechnet = gebucht.*
- **(iv) Buchungs-Engine + GoBD-Gates** (append-only, Storno, Maker-Checker, Nummernkreis). *Gate: 4 GoBD-Beweis-Tests grün.*
- **(v) Auswertungen** (SuSa/BWA/UStVA) gegen Referenz-Geschäftsjahr. *Gate: Zahlen-Wahrheit.*
- **(vi) DATEV-EXTF-Export.** *Gate: Konformitäts-Prüfer + Testpaket grün.*
- **(vii) Positions-Erlöskonten-Split** (invoice_items→Erlöskonten) — **eigene spätere Stufe.** *Gate: Split summiert = Kopf-Betrag.*
- **(viii) Kreditoren-/Bestelllisten-Seite** (Wareneingang) — später, eigener Block.
- **Timing:** FiBu-eigene Tabellen (i) nach Phase-0-Freigabe vorziehbar (kollisionsfrei); der Rest nach wberechnung-Cut-over-Abschluss. Bau im Worktree `strang/accounting`.

## §9 Selbstkritik / NICHT-VERIFIZIERT
- Die **45 playground-Models + EXTF-Export-Korrektheit + GoBD-Festschreibungs-Vollständigkeit** sind **nicht einzeln geprüft** (Struktur-/Reife-Scan, kein Zeilen-Audit) — das ist Arbeit der Bau-Stufen (iv/vi), nicht dieses Befunds.
- **Bestellliste**-Tabellen (Kreditorenseite) nur grob verortet (`DealMaterialList*`) — Feinkartierung erst bei Stufe (viii).
- Der **Adapter playground-RBAC→ticket-Auth** ist als Weiche benannt, nicht entworfen.

---

**⛔ PFLICHT-STOPP.** Phase-0-Befund komplett: Kollisions-Karte + Konzept-Vergleich je Kettenglied (Synthese: ticket=Beleg, playground=Buchungs-/GoBD-Schicht, additiv angedockt), invoice_items = **Übertragungs-Gap** (Kopf-Buchung Start, Positions-Split eigene Stufe vii), SKR = **externe Steuerberater-Klärung** (keine Empfehlung, technisch flexibel), Gap-Liste + Stufenplan (i)–(viii) mit Qualitäts-Gates. **Keine Bau-Stufe vor Yamas Freigabe des Stufenplans.**
