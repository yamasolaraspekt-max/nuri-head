# 08 — FINANZ-EXPERTE (Rechnung / FiBu) · Experten-Inventur Stufe 1

> Rolle FINANZ-EXPERTE (CRM-AUTOMATISIERUNG-MASTER). **Rein lesend, rein beschreibend.**
> Bereich: Rechnungserstellung, Abschlags-/Schluss-/Stornorechnung, Förderabzug, in-house FiBu,
> offene Posten, Zahlungs-Matching, Mahnwesen.
> **⚠️ Zonen-Grenze:** Die **Invoice-/Accounting-Zone gehört einer anderen Instanz.** Sie ist hier
> **inventarisiert (Ist + Automatisierungs-Potenzial)**, NICHT zur Änderung bewertet. Keine
> Bau-/Umbau-Empfehlung an dieser Zone — nur Beschreibung, wo Automatik ansetzen *könnte*.
> Baut auf: `docs/audit/{code-audit,intelligenz-audit,automatisierungs-hebel}.md`, `docs/accounting/*`,
> `docs/backlog-accounting.md`. Stand-Verifikation per Read-only-SQL (`ticket`-DB).

---

## 0. Datenlage (verifiziert, `ticket`-DB, SQL firsthand)

| Sachverhalt | Wert |
|---|---|
| `invoices` gesamt | **11 echte** + 1 Test (`id 21`, `TST-OPEN-2337`) |
| Status-Verteilung **live** | **`open` = 7** (112.288,80 €) · **`paid` = 4** (92.905,68 €, voll bezahlt) |
| `invoice_items` | **0** (Positionen nicht materialisiert) |
| `deal_invoices` | **0** (stillgelegt, Drop offen) |
| `foerderungen` | **0** (Standalone-Modul, nicht mit Rechnung verdrahtet) |
| FiBu-Seed | `accounting_clients` **1** · `accounts` **26** · `account_mappings` **24** |
| FiBu-**Bewegung** | `accounting_documents` **0** · `accounting_journal_entries` **0** |

**Kernbefund der Datenlage:** Die FiBu-Buchungsschicht ist **gebaut + geseedet, aber nie auf die
Live-Rechnungen angewandt** (0 Belege, 0 Buchungssätze). Zwischen *„gebauter Fähigkeit"* und
*„produktivem Zustand"* klafft die ganze Betriebs-Lücke.

---

## 1. IST-FUNKTIONEN (Beleg-Ebene)

### 1.1 Rechnungserstellung
- **Ausgangs-Beleg = `invoices`** (führende Schiene, `docs/accounting/umsatzdefinition.md`), FiBu-nah gebaut:
  `account_id`(Mandant), `customer_id`, `object_id`, `deal_id`(Auftrag), `offer_detail_id`/`source_offer_detail_id`(Angebot→Rechnung),
  `subtotal/tax_rate/tax_amount/total_amount`, `service_from/to`(Leistungszeitraum, GoBD), `paid_amount/paid_at`,
  `deal_limit_amount/deal_remaining_before/after`(Auftrags-Budget). — `app/Models/Invoice.php`, Migrationen `2023_07_19…invoices` + Anbauten bis `2026_06_15`.
- **CRUD:** `InvoiceController@store/update/updateStatus/destroy/show` + `selectCustomers/Objects/Products/Deals` + `dealItems`
  (`app/Http/Controllers/Invoice/InvoiceController.php`).
- **Positions-Durchreichung Angebot→Rechnung** (Medienbruch bereits geschlossen):
  `InvoiceCanvasController::storeDraftFromOfferDetail` (`:56-87`, Items `:519-619`) kopiert
  `offer_details.sections`(JSON) → Rechnungs-Items inkl. `title/qty/unit/unit_price/product_id/distributor_*/source_payload`.
  Preise katalog-vorbefüllt aus `distributor_prices`. **Diese Achse ist sauber** (kein Abtippen).
- **Summen server-autoritativ:** `Invoice::recalcTotals()` (`:175`) rechnet `subtotal/tax_amount/total` aus Items neu; Front-End-Werte werden nicht vertraut.
- **Beleg-Typen** (`invoiceTypes()`, `:1533`): Rechnung · **Abschlagsrechnung** · **Stornorechnung** · Gutschrift u. a.
  → als *Typ-Etikett* vorhanden; **eigene Abschlags-/Schluss-Logik existiert NICHT** (kein Abschlags-Plan,
  keine Rest-/Schlussrechnungs-Verkettung, kein Abschlag-gegen-Schluss-Verrechnung).

### 1.2 Nummernkreis
- **`InvoiceNumberService`** (`app/Services/Invoice/InvoiceNumberService.php`) = einzige Vergabestelle,
  Format **`RE-yy####`**, transaktional + `lockForUpdate` (race-safe), idempotent, Draft ohne Nummer,
  Vergabe erst bei ausgestelltem Status. Lazy-Seed aus Bestand.

### 1.3 Status-Lebenszyklus + GoBD-Schutz (Beleg)
- Model-Enum `STATUS = [draft, sent, paid, overdue, cancelled]`, `ISSUED_STATUSES = [sent, paid, overdue]`.
- **Guards:** ausgestellt→`draft` gesperrt (Storno-Pflicht, `updating`-Hook + `updateStatus`);
  Löschschutz `InvoiceDeletionGuard` (Nummer/Status≠draft/Zahlung ⇒ blockiert); Nummernvergabe im `saving`-Hook atomar (keine Lücke bei Save-Fehler).
- **History:** `recordInvoiceHistory` + `invoice_histories` (Event-Trail create/status_changed).

### 1.4 Zahlung / Zahlungseingang
- **Einzige Zahlungs-Erfassung = Statuswechsel auf `paid`.** `applyStatusAccounting` (`:1208`) setzt dann
  `paid_amount = total_amount` (Voll) und `paid_at = now()`. Wechsel weg von paid ⇒ Felder auf 0 zurück.
- **Kein Teilzahlungs-Eingabefeld** („The current form has no partial-payment input", `:1227`).
- **Keine Zahlungseingangs-Quelle / kein Bank-Matching.** `open_amount = total − paid` (`Invoice::getOpenAmountAttribute`) ist die einzige OP-Kennzahl.
- **`invoice_payments`-Tabelle referenziert, existiert aber NICHT** (Guard prüft `Schema::hasTable`,
  Migration fehlt) → defensiver, aber quellenloser Zweig.
- `installment_payments` (Migr. `2026_06_15_173818`) ist **Asset-/Maschinen-Finanzierung** (Branch/Asset),
  **nicht** Kunden-Abschlagszahlung — nicht mit `invoices` verbunden.

### 1.5 Auftrags-Budget-Gate
- `guardDealLimit`/`dealInvoiceBalance`/`syncDealLimitSnapshot` (`:1257-1342`): summiert Rechnungen je Deal,
  hält Restbudget-Snapshot, verhindert Überfakturierung über das Auftragslimit. Storno/Gutschrift zählen mit Vorzeichen (`signedInvoiceAmount`).

### 1.6 In-house FiBu (**fremde Zone** — nur inventarisiert)
FiBu i–viii gebaut + getestet (`docs/backlog-accounting.md`), Schicht dockt read-only an festgeschriebene `invoices` an:
- **`BelegflussService`** (iii/vii): Rechnung → Kopf-Buchung *„Debitor an Erlös + USt"* über `mapping_key`
  (keine harten Kontonummern); Positions-Split je `invoice_items`-Kategorie/Steuersatz mit Cent-Reconcile;
  Fallback auf Kopf-Buchung; Soll=Haben-Gate; idempotent (`source_invoice_id`).
- **`BuchungsEngine`** (iv): Festschreibung (append-only), lückenloser Nummernkreis `JJJJ-NNNNNN` je Mandant/Jahr
  (`lockForUpdate`), **Maker-Checker** (Erfasser≠Festschreiber), **Storno statt Edit** (Gegenbuchung, Original unberührt). 4 GoBD-Beweis-Tests grün.
- **`AuswertungsService`** (v): SuSa · UStVA (KZ81/86/66/83) · BWA — read-only, nur `is_finalized`.
- **`DatevExtfExportService`** (vi): EXTF-Buchungsstapel v13 (CP1252, Semikolon, Dezimal-Komma), Stern-Zerlegung, integrierter Konformitäts-Prüfer. **Versand bleibt Yama** (2.3), Status wird nicht automatisch gesetzt.
- **`EingangsBelegflussService`** (viii-Kern): „Wareneingang + Vorsteuer an Kreditor". **Keine reale
  Eingangsrechnungs-Quelltabelle** — Belegdaten werden übergeben; Bestelllisten-/`DealMaterialList`-Anbindung offen.

### 1.7 Förderabzug
- **`Foerderung`-Model** (`app/Models/Foerderung.php`, Tabelle `foerderungen`, **0 Zeilen**): Standalone
  Förderprogramm-Liste (KfW/BAFA, Antragsstatus/Frist/Betrag). **Nicht** an die Rechnung gekoppelt — kein
  Förder-Abzugsposten auf `invoices`, keine Netto-Rechnung-nach-Förderung.
- Förder-*Berechnung* existiert nur in der **Energie-Auslegung** (`FoerderungService`, `EnergiekonzeptController`) —
  Angebots-/Wirtschaftlichkeits-Anzeige, **keine** Faktura-Verdrahtung.

### 1.8 Mahnwesen
- **Existiert nicht.** Kein Dunning-Modul, keine Mahnstufen-Tabelle, keine Überfälligkeits-Automatik.
  `status='overdue'` ist ein manueller Etikett-Wert; keine Ableitung aus `due_date`, kein Mahnlauf.

---

## 2. STÄRKEN

1. **Eine Umsatz-Wahrheit** (`invoices`) sauber verankert; `deal_invoices` stillgelegt (0/0). Kette FK-durchgereicht (kein Abtippen von Kunde/Objekt/Positionen).
2. **Beleg-Modell FiBu-reif:** Netto/USt/Brutto, Leistungszeitraum, Mandant, Auftrag/Angebot-Link — direkt buchungssatzfähig.
3. **Nummernkreis race-safe + GoBD-Löschschutz + Storno-Pflicht** bereits am Beleg (nicht erst in der FiBu).
4. **Positions-Durchreichung Angebot→Rechnung ist gebaut** (der teuerste Medienbruch dieser Achse ist geschlossen).
5. **FiBu-Buchungsschicht (fremde Zone) architektonisch stark:** SKR-neutral via `mapping_key`, append-only, Maker-Checker, DATEV-Konformitäts-Prüfer, je Eigenschaft ein Beweis-Test.
6. **Auftrags-Budget-Gate** verhindert Überfakturierung strukturell.

---

## 3. SCHWÄCHEN / MEDIENBRÜCHE / KONSISTENZ-LÖCHER

| # | Befund | Beleg | Wirkung |
|---|---|---|---|
| S1 | **Abnahme→Rechnung ohne Vorschlag** (Medienbruch H-A6). Kein Pfad aus Auftragsabschluss; Rechnung nur manuell. | `InvoiceController:213`, `InvoiceCanvasController:56` | Vergessene Schlussrechnung → direkter Cashflow-Verlust |
| S2 | **Fälligkeit nicht abgeleitet** (H-B1). `due_date` frei/nullable; `payment_terms` (bei Distributoren vorhanden) **ungenutzt**. Live: **alle `due_date`=2026-07-13** unabhängig vom `issue_date` (Seed-Artefakt, stützt „nicht regel-abgeleitet"). `id 21` sogar `due_date=NULL`. | `InvoiceController:1097`; SQL | Falsche/fehlende Fälligkeit → kein sauberes Mahnwesen möglich |
| S3 | **„Bezahlt" ohne Zahlungseingang.** `status=paid` setzt `paid_amount=total` blind — kein Bankabgleich, kein Matching, keine Teilzahlung. | `applyStatusAccounting:1215` | „paid" = Behauptung, nicht Nachweis; OP nur `total−paid` |
| S4 | **Daten-Inkonsistenz `paid_at` < `issue_date`** (id 13: issue 24.06 / paid 31.05; id 20: issue 26.06 / paid 20.06). | SQL | Zahlung vor Ausstellung — Seed-/Prozess-Artefakt, GoBD-fragwürdig |
| S5 | **Status-Vokabular-Divergenz Code↔Daten.** Live-Status = **`open`** (7 Zeilen); Model-Enum kennt `open` **nicht** (`draft/sent/paid/overdue/cancelled`), `ISSUED_STATUSES` auch nicht. Diese 7 tragen dennoch Nummern. | `Invoice:16-19` vs. SQL | Logik (Nummernvergabe-Gate, Status-Guards) und Bestand reden zwei Vokabulare |
| S6 | **Nummern-Format-Divergenz + Lücken.** Live `RE-2026-#####` (00001,00006,00012,…, mit Sprüngen) — genau das **Fremdformat, das `InvoiceNumberService` explizit ignoriert**; der Service produziert `RE-yy####`. Bestand ist **nicht lückenlos**. | `InvoiceNumberService:88-100`; SQL | Zwei Nummernwelten; GoBD-Lückenlosigkeit im Bestand nicht gegeben |
| S7 | **Kein Mahnwesen** (keine Stufen, keine `overdue`-Ableitung, kein Lauf). | Codebase-Grep: 0 dunning | Überfällige laufen manuell / gar nicht |
| S8 | **FiBu nie auf Live angewandt** (0 Belege / 0 Buchungssätze trotz Seed). | SQL | Gebaute Fähigkeit ≠ produktiver Zustand; kein Festschreib→Buchung-Trigger |
| S9 | **Positions-Split faktisch inaktiv:** `invoice_items=0` für alle Bestandsrechnungen → nur Kopf-Buchung möglich (by design, aber Erlöskonten-Split trägt live nichts). | SQL; Belegfluss `:143` Fallback | Erlöskonten-Aufteilung erst für künftige Rechnungen |
| S10 | **Förderabzug nicht faktura-verdrahtet** (`foerderungen`=0, kein Rechnungs-Abzugsposten). | `Foerderung.php`; SQL | Förder-Netto-Rechnung nicht abgebildet |
| S11 | **`invoice_payments` referenziert, Tabelle fehlt.** | `InvoiceDeletionGuard:65` | Toter Schema-Verweis; Zahlungs-Historie strukturell nicht vorgesehen |

---

## 4. REIFE (je Teilbereich)

| Teilbereich | Reife | Begründung |
|---|---|---|
| Rechnungs-Beleg (CRUD, Nummer, Guards, Kette) | **Hoch** | vollständig, race-safe, GoBD-Löschschutz, chain-verankert |
| Positions-Durchreichung Angebot→Rechnung | **Hoch** | gebaut, katalog-vorbefüllt, server-autoritative Summen |
| FiBu-Buchungsschicht (fremde Zone) | **Gebaut/getestet, aber unangewandt** | i–viii grün im Labor, 0 Live-Buchungen — „produktions-latent" |
| Zahlung / Offene Posten | **Niedrig** | nur Voll-Zahlung-Flag, kein Matching, kein Teilzahlungs-Modell |
| Fälligkeit / Zahlungsziel | **Niedrig** | Quelle da (`payment_terms`), ungenutzt; Bestand nicht regel-abgeleitet |
| Abschlag / Schluss / Förderabzug | **Sehr niedrig** | nur Typ-Etiketten, keine Verrechnungslogik |
| Mahnwesen | **Nicht vorhanden** | kein Modul |

---

## 5. AUTOMATISIERUNGS-REIFE (gesamt)

**Gesamturteil: gespalten — „Ableitung/Beleg hoch, Prozess-Auslösung + Geldeingang niedrig".**

- **Hoch automatisiert (fertig):** Summen-Ableitung, Nummernvergabe, Positions-Kopie, Budget-Gate,
  FiBu-Buchungslogik + DATEV (Service-Ebene). Hier ist Automatik *gebaut*.
- **Automatisierbar, Quelle da, ungenutzt (Quick-Wins):**
  - **Fälligkeit (H-B1):** `due_date = issue_date + Zahlungsziel` — `payment_terms` vorhanden. Direkter Mahnwesen-Vorbau. *(S, sicher.)*
  - **„Voll fakturiert & bezahlt"-Flag (H-A4):** aus `invoices` an den Auftrag rück-ableiten (Deals stehen `active` trotz `paid_amount=total`). *(S.)*
- **Automatisierbar als Vorschlag (Medienbruch, TABU-Naht zur Invoice-Zone):**
  - **Abnahme→Rechnungs-Entwurf (H-A6):** Entwurf vorschlagen (nicht festschreiben). *(M.)*
- **Nicht gebaut / Automatik-Lücke:**
  - **Zahlungs-Matching / Zahlungseingang:** keine Quelle, keine Teilzahlung → OP nur rechnerisch. Voraussetzung für echtes Mahnwesen.
  - **Mahnwesen:** komplett offen (setzt S2 Fälligkeit + Zahlungseingang voraus).
  - **Festschreib→Buchung-Trigger:** FiBu-Service da, aber kein automatischer Auslöser vom Beleg (0 Live-Buchungen).
  - **Förderabzug-Faktura:** kein Modell.

**Wirkungs-Einordnung (aus `automatisierungs-hebel.md`):** Rechnung ist das **seltene Kettenende**
(11 Rechnungen vs. 52 Leads) — Frequenz-Multiplikator klein, aber **Einzel-Vorgang-Wert hoch**
(vergessene Schlussrechnung, falsche Fälligkeit = Cashflow). Reihenfolge des Hebel-Audits: **H-B1 zuerst**
(sicher, Quelle da), dann H-A4, dann H-A6 als Vorschlag.

---

## 6. GELESEN / NICHT-GELESEN

**Gelesen (vollständig):** `docs/accounting/{umsatzdefinition,phase-0-fibu-transplant-befund,schritt-1-rechnungsschienen-befund}.md`;
`docs/backlog-accounting.md`; `app/Services/Accounting/{BelegflussService,BuchungsEngine,AuswertungsService,DatevExtfExportService,EingangsBelegflussService}.php`;
`app/Services/Invoice/{InvoiceNumberService,InvoiceDeletionGuard}.php`; `app/Models/{Invoice,Foerderung}.php`;
`InvoiceController` (Methoden-Index + store/updateStatus/applyStatusAccounting/recalcTotals/Deal-Limit); Migration `installment_payments`; SQL-Stand (invoices, accounting_*, foerderungen).

**Gelesen (partiell / Struktur-Scan, nicht Zeile-für-Zeile):** `InvoiceController` Voll-Fluss (1600+ Zeilen — Kernmethoden gelesen, Rest per Index);
`InvoiceCanvasController` (nur über Zitat aus `automatisierungs-hebel.md`, **nicht** Quelltext); die 9 FiBu-Migrationen (nur `installment`, `invoice_number_ranges`-Logik via Service);
`code-audit.md`/`intelligenz-audit.md` (nur Finanz-relevante Zeilen via Grep, nicht ganz).

**Nicht gelesen:** FiBu-Test-Suiten (die „grün"-Aussagen sind aus dem Backlog übernommen, nicht selbst ausgeführt);
DATEV-Testpaket-Ergebnis; `KontenrahmenSeeder`-Zeilen (nur Row-Counts); `InvoiceCanvasController`-Quelltext; playground-Herkunft.

---

## 7. NICHT-VERIFIZIERT (explizit)

- **FiBu-Tests „grün"**: nicht selbst ausgeführt — aus `backlog-accounting.md` übernommen. GoBD-Vollständigkeit / EXTF-Korrektheit **nicht** zeilengeprüft (Phase-0 §9 bestätigt das ebenfalls).
- **`InvoiceCanvasController::storeDraftFromOfferDetail`**-Zeilenangaben: aus `automatisierungs-hebel.md` zitiert, Quelltext nicht selbst geöffnet.
- **Ursache Status-`open` (S5)**: ob `open` ein Alt-/Import-Status oder aktive Zweit-Semantik ist — **nicht** geklärt (Migrations-Historie nicht durchsucht).
- **Nummern-Lücken (S6)**: ob Sprünge Storno/Test/Import sind — nicht rückverfolgt.
- **`paid_at`<`issue_date` (S4)**: als Datenbefund gesehen, Ursache (Seed vs. Prozess) nicht bewiesen.
- Absolute Frequenzen (Rechnungen/Monat, übliches Zahlungsziel) — **Seed ist Ein-Tages-Snapshot**, nicht messbar (Yama-Kalibrierung offen).

---

## 8. SELBSTKRITIK

- Ich habe die **Zonen-Grenze respektiert**: FiBu/Invoice nur beschrieben, keine Umbau-Bewertung. Risiko: einige S-Punkte (S3/S5/S6) *klingen* wie Mängel-Urteile — sie sind als **Ist-Befund**, nicht als Änderungs-Auftrag zu lesen; die Behebung entscheidet die Eigentümer-Instanz.
- Die Trennung **Beleg-Zone (mein Kern) vs. FiBu-Zone (fremd)** verläuft im selben Verzeichnisbaum; einige Aussagen greifen zwangsläufig in die Nachbar-Zone (Buchungs-Trigger) — dort **nur Ist**, kein Soll.
- Der stärkste, eigenständig verifizierte Befund ist die **Anwendungs-Lücke** (S8: 0 Live-Buchungen trotz Seed) und die **Vokabular/Nummern-Divergenz** (S5/S6) — beide firsthand per SQL, nicht aus Vor-Dokumenten übernommen.
- **Nicht abgedeckt aus Zeit-/Scope-Gründen:** Kreditoren/Eingangsrechnungs-Realquelle (existiert nicht), `DealMaterialList*`-Kartierung (Backlog viii offen), UStVA-Zahlen gegen echte Live-Rechnungen (FiBu nie gelaufen → nichts zu prüfen).
</content>
