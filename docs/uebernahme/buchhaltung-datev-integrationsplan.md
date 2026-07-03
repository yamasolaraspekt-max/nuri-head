# Integrationsplan: Buchhaltung / FiBu / DATEV — `playground` auswerten → in `ticket` neu bauen

**Stand:** 2026-07-02 · **Reine Analyse — kein Code geändert, nichts aus playground importiert.**
**Grundsatz:** `ticket` ist das produktive, führende System. `playground` ist Prototyp und **Konzeptquelle** (Fachlogik, Tabellenideen, Workflows) — **nicht** Design-Vorlage und **nicht** zum Kopieren.
**Methodik:** 6 parallele Lese-Agenten (Ist ticket-Rechnung · Ist ticket-Kostenstellen · playground-Accounting · Mapping · DATEV/GoBD-Risiko · Fahrplan).

---

## 1. Kurzurteil — Soll man Buchhaltung/DATEV aus playground in ticket integrieren?

**Ja — aber als fachliche Neubau-Integration, nicht als Import.** Bewertung nach Achse:

- **Konzept playground: sehr wertvoll.** Der Accounting-Teil ist die ausgereifteste Domäne des Prototyps (~65 Tabellen, ~71 Services): echte doppelte Buchführung, §146-Festschreibung mit Vier-Augen + Audit, Storno-als-Umkehrbuchung, `mapping_key`-Entkopplung, EXTF-v700-Generator mit Selbstvalidierung, **Safety-Gate-Registry (Default-Deny)** und eine maschinenlesbare **Release-Blocker-Matrix**. Das ist eine erstklassige Blaupause.
- **Code playground: pilotreif, NICHT abnahmereif.** Rohes SQL-Dump-Schema statt echter Migrationen, ein **zweites totes Journal** (`accounting_buchungen`), ein `origin`-ENUM-Bug, unvalidierte Steuer-Stammdaten (nur „Vorschlag"), DATEV-Testpaket **nicht bestanden**, Unveränderbarkeit nur per App-Logik (keine DB-Grants), Mandantenfähigkeit (irrelevant für ticket = Einzelmandant).
- **ticket-Ist: großes Loch.** Nur die `invoices`-Schiene ist real nutzbar — ohne Debitor/Steuerschlüssel/Konto, ohne Teilzahlung/offene Posten, ohne Storno-Bezug, mit **lückenhaftem Nummernkreis** und **löschbaren (auch bezahlten) Rechnungen** = akuter GoBD-Verstoß. Keine Kostenstellen-Dimension, keine Kostenumlage.

**Fazit:** Datenmodell + Fach-Muster aus playground **übernehmen als Konzept**, den Code **sauber neu** bauen (echte Migrationen, ein Journal, StB-validierte Stammdaten, im ticket-Design). Der Engpass ist nicht Entwicklung, sondern **Steuerberater-Entscheidungen + zwei, drei Yama-Architektur-Weichen** — playground beweist das: 600+ grüne Tests und ein voller EXTF-Generator, Gesamtampel trotzdem ROT.

---

## 2. Was genau aus playground übernehmen

### Konzepte / Fach-Muster (übernehmen)
1. **`mapping_key`-Entkopplung** (`account_mappings`): Buchungslogik referenziert semantische Schlüssel (`revenue.pv.0`, `debtor.default`, `tax.13b`), nie harte Kontonummern. Macht SKR03/04 zur Stammdaten-Zeile statt Code-Fork.
2. **GoBD-Kette:** append-only Audit-Log + `entity_changes` + `entry_hash` (SHA-256-Verkettung) + Storno-statt-Löschen + §146-Festschreibung (atomar, Vier-Augen, idempotent).
3. **Safety-Gate-Registry (Default-Deny):** `finanz_safety.php` + `datev_release_checklist.php` — scharfe Aktionen sind gesperrt, bis die zugehörige Blocker-Entscheidung freigegeben ist. **Das ist das Sicherheitsmodell für unfertige Finanzlogik** und sollte als Erstes portiert werden.
4. **Buchungsvorschlag-Zwischenschicht** (`posting_suggestions`): Rechnung → Vorschlag (read-only) → *[Gate]* → scharfe Buchung. „Kein geratener Betrag" (bewusst `null` statt Fallback).
5. **Durchgängige Auswertungsdimensionen** (Kostenstelle/Kostenträger/Projekt/Auftrag) bis in die Journalzeile, gespiegelt im Storno.
6. **Steuerschlüssel-Struktur** (`tax_codes` mit `tax_type`/`bu_schluessel`/`tax_direction`, historisierte `tax_rates`).

### Tabellen (Struktur als Pate, neu als echte Migrationen)
**Neu (Muss):** `accounting_settings` (Singleton statt Mandant), `chart_of_accounts`+`accounts`, `account_mappings`, `tax_codes`+`tax_rates`, `debtors`, `cost_centers`, `cost_objects`, `open_items`, `accounting_journal_entries`+`_lines` (das **eine** kanonische Journal), `accounting_number_ranges`, `accounting_datev_exports`, `accounting_audit_log`+`entity_changes`, `accounting_gate_releases`.
**Neu (Kann/Phase 2):** `creditors`, `accounting_incoming_invoices`, `posting_suggestions` (im Plan als Sicherheitsschicht vorgezogen), Mahnwesen/Fristen.

### Services (Logik portieren, auf ticket-Auth verdrahten)
`JournalService` (bookFromSuggestion/storno/festschreiben), `FestschreibungsService`, `DatevPreparationService` (EXTF-v700 + validateExtf), `PostingLockService`, `NumberRangeService`, `AccountResolutionService` (mapping_key), `GateReadinessService`/`AccountingGateReleaseService` + `FinanzGateMiddleware`.

### Views
**Keine** — siehe Punkt 3. Jede Buchhaltungs-/DATEV-Seite wird im **ticket-Design neu** gebaut. playground liefert höchstens die **Blade-Struktur/Workflow-Idee**, nie die Optik.

---

## 3. Was NICHT übernehmen

- **Kein Design / keine Optik.** playground ist **keine** Design-Vorlage. Keine Tailwind-/Alpine-UI nach ticket. Neue Seiten strikt im bestehenden ticket-Stil (Vuexy/Bootstrap-Blade, ticket-Sidebar, vorhandene Cards/Tabellen/Buttons/Modals/Badges, Toastr/Select2). Fachlich nützliche playground-Views werden im ticket-Layout **neu umgesetzt**.
- **Kein rohes SQL-Dump-Schema** (`DB::unprepared(file_get_contents(...sql))`) → in ticket als echte, reversible Laravel-Migrationen neu schreiben.
- **Kein zweites Journal:** die tote Alt-Schiene `accounting_buchungen`/`_buchung_lines` nicht portieren — nur `accounting_journal_entries`/`_lines`.
- **Keine Steuer-/Konten-*Werte* aus playground** (Seeder = „Vorschlag", PRÜFPFLICHTIG). Nur Struktur, Werte kommen vom Steuerberater.
- **Keine Mandantenfähigkeit:** `accounting_client_id`-FKs entfallen ersatzlos (Einzelmandant → `accounting_settings`-Singleton).
- **Kein EXTF-Blindvertrauen:** DATEV-Test war nicht bestanden; Encoding/Header/`origin`-ENUM-Bug erst fixen, dann Kanzlei-Importtest.
- **Keine UStVA-Eigenbau-Schiene:** die Voranmeldung macht die Kanzlei in DATEV. ticket liefert Buchungen + Belege + OP.
- **`deal_invoices`** (tote ticket-Alt-Schiene) wird **verworfen**, nicht erweitert.

---

## 4. ticket-Grundsatzentscheidungen, die den Bau blockieren

**Harte Blocker vor Baubeginn der Stammdaten-/Buchungsschicht (Phase 3+):**

**Yama (Scope/Architektur):**
- **A1 — DATEV-Zielbild:** Eigen-EXTF-Export **vs.** DATEV-Unternehmen-online / nur Belege+OP an Kanzlei. Bestimmt die gesamte Ausbaustufe.
- **A4 — Unveränderbarkeits-Ebene:** reicht App-Logik, oder zusätzlich DB-Grants/Trigger (Infra-Invest)? GoBD-Substanzfrage.
- **A6 — Löschsperre-Stichtag + Altbestand:** ab wann revisionssicher, und wie mit bereits soft-deleted-aber-bezahlten Rechnungen umgehen?
- (A2 UStVA-Scope bestätigen · A3 OP-Buchhaltung in ticket ja/nein · A5 Checker-Rolle intern vs. StB · A7 Mahn-Policy — teils parallel klärbar.)
- **P0 (Agent 2) — Kostenstellen-Definition:** Filiale vs. Abteilung vs. Abteilung-je-Filiale. Business-Entscheidung, blockiert Phase 2 (nicht die DATEV-Blocker).

**Steuerberater (regulatorisch/Stammdaten — ohne die ist jeder FiBu-Code wertlos):**
- **B1 SKR03 oder SKR04** · **B2 verbindlicher Kontenplan** (inkl. Spezialkonten §13b/0%-PV/Anzahlung/Skonto) · **B3 Steuerschlüssel/BU-Schlüssel** je Fall (19/7/**0 % PV**/**§13b Reverse-Charge**/ig/steuerfrei) · **B4 Sachkontenlänge + Debitoren-/Kreditoren-Nummernsystematik**.

> **Kernaussage:** Vor dem ersten Buchhaltungs-Sprint gehört ein **Steuerberater-Termin**, der B1–B4 verbindlich liefert, plus eine Yama-Sitzung für A1/A4/A6. Alles davor produziert plausibel aussehende Falschzahlen.

---

## 5. Empfohlene Zielarchitektur

Eine **isolierte, additive `accounting_*`-Schicht** liegt **neben** — nicht in — der operativen `invoices`-Schiene:

- `invoices` bleibt **operative Wahrheit** (was der Kunde sieht/zahlt), wird nur um **nullable** Felder erweitert. Die FiBu ist nachgelagert und schreibt **nie** in operative Tabellen zurück.
- **Entkopplung über `mapping_key`** — kein Service kennt Kontonummern direkt; der Steuerberater ändert Mappings, nicht Code.
- **Genau ein kanonisches Journal**, doppelte Buchführung mit erzwungenem Soll/Haben-Ausgleich.
- **Einbahn-Brücke:** `invoices` → `posting_suggestions` (read-only, verwerfbar) → *[Safety-Gate + StB-Freigabe]* → `journal` (scharf, festschreibbar) → `accounting_datev_exports` (EXTF).
- **Gate-Registry (Default-Deny)** vor jedem scharfen Schritt (Festschreibung §146, Export, Löschung): gesperrt, solange die Blocker-Entscheidung offen ist.
- **Einzelmandant:** `accounting_settings`-Singleton; keine `accounting_client_id`-FKs.
- **UI:** alle Seiten im bestehenden ticket-Design neu.

---

## 6. Phasenplan mit Reihenfolge

```
Phase 0  Fundament: reversible Struktur-Migrationen + Gate-Registry (ROT)
         + Nummernkreis-Härtung + Löschsperre-Infrastruktur          ← KEIN StB
Phase 1  invoices/invoice_items additiv erweitern (alles nullable)   ← KEIN StB
Phase 2  Operative Vorbedingungen: cost_centers/cost_objects,
         department_id-Pflicht, Auftrag→Rechnungskette + Backfill     ← Yama P0 (kein StB)
Phase 3  Steuer-/Konto-Stammdaten: CoA, tax_codes, debtors,
         account_mappings, open_items(Struktur)                      ← StB B1–B4 (harter Blocker)
Phase 4  Buchungsvorschlags-Engine (read-only, KEINE scharfe Buchung)← nur B1–B4
── GRENZE: ab hier scharfe Buchung, hinter Safety-Gate + StB ──
Phase 5  Scharfe Buchung + §146-Festschreibung + offene Posten       ← A4, A6, Festschreibpolitik
Phase 6  DATEV-EXTF-Export + Kanzlei-Importtest                      ← A1 + bestandener Importtest
Phase 7  Ausbau: Skonto/Abschlag-USt, §13b/0%-PV-Feinschliff,
         Kreditoren/Eingangsrechnungen, Mahnwesen, Verfahrensdoku    ← parallel klärbar
```

Abhängigkeiten: 0→1→{2 ‖ 3}; 3→4→5→6→7. **Phase 2 läuft parallel zu Phase 3** (Business- statt Steuer-Blocker). Buchungsvorschläge (Phase 4) sind baubar/testbar, sobald Stammdaten stehen — scharf wird nichts vor Phase 5 + Freigabe.

**Migrationssicherheit (Querschnitt):** jede Phase nur **neue Tabellen** oder **nullable Spalten**; keine Änderung/Löschung bestehender `invoices`/`deals`-Spalten; nur idempotenter Backfill (Phase 2). Phasen 0–4 voll zurückrollbar. Ab Phase 5 kein technischer Rollback festgeschriebener Buchungen (Storno statt Delete = gewollt, GoBD-konform).

---

## 7. Erste sichere Bau-Etappe (ohne Steuerberater startbar)

**Phase 0 + Phase 1 (+ Portierung der Gate-Registry), parallel dazu Phase 2 sobald Yama die Kostenstellen-Definition liefert.**

Warum sicher — trifft **keine** Steuer-/Konto-Aussage und bucht **nichts** scharf:
- Nur **strukturelle, leere** Tabellen als echte reversible Migrationen (kein playground-SQL-Dump).
- `invoices` nur um **nullable** Spalten erweitert → kein bestehender Schreibpfad bricht, Live-Daten unangetastet.
- **Nummernkreis** technisch gehärtet (`SELECT … FOR UPDATE`/atomar, lückensicher) und **Löschsperre-Infrastruktur** gebaut — reine GoBD-Vorbereitung.
- **Safety-Gate-Registry** portiert, Gesamtampel **ROT** → scharfe Aktionen technisch gesperrt, solange StB-Blocker offen.
- Einzige UI: eine read-only „Gate-Status/Ampel"-Seite im ticket-Design.

Die playground-Steuer-Stammdaten werden hier **bewusst nicht** übernommen (unvalidiert → warten auf Phase 3 + StB-Freigabe).

**Prüfpunkte Phase 0/1:** Migration `up`/`down` sauber reversibel auf Live-DB-Kopie · Nummernkreis-Concurrency-Test (parallele Requests → keine Doppel-/Lückennummer) · Gate liefert bei fehlender Freigabe hart `deny` · alle bestehenden Rechnungs-Flows unverändert grün.

---

## 8. Zwingend mit Steuerberater zu klären (vor „scharf")

| # | Punkt | Blockiert |
|---|---|---|
| B1 | **SKR03 oder SKR04** je Mandant | Phase 3 |
| B2 | **Verbindlicher Kontenplan** inkl. Spezialkonten (§13b, 0 % PV, Anzahlung, Skonto) | Phase 3 |
| B3 | **BU-/Steuerschlüssel je Steuerfall** (19/7/**0 % PV**/**§13b Reverse-Charge**/ig/steuerfrei) — schriftlich bestätigt | Phase 3/4 |
| B4 | **Sachkontenlänge + Debitoren-/Kreditoren-Nummernsystematik** (vor erster Nummernvergabe!) | Phase 3 |
| B5/B6 | **Berater-/Mandantennummer + EXTF-Encoding/Header + Kanzlei-Importtest** | Phase 6 (nur bei A1=EXTF) |
| B7 | **Anzahlungs-/Abschlags-USt (§14 V)** + Konten — branchenkritisch (Solar/Montage) | Phase 5/7 |
| B8 | **Skonto-/Differenz-/Verzugs-USt (§17)** + Rundungstoleranz + Zinssatz (kein Fallback!) | Phase 5/7 |
| B9 | **Festschreibungspolitik** (Zeitpunkt) + Freigaberollen (Vier-Augen) | Phase 5 |
| B10 | **GoBD-Verfahrensdokumentation** abnehmen | vor Produktivgang |
| B11 | **Wirtschaftsjahr** (ggf. abweichend) | früh |

**Top-Risiken bei Bau ohne diese Klärung:** §13b/0%-PV falsch verbucht → materielle Steuerfehler/Haftung · Unveränderbarkeit nur App-seitig → GoBD-Verwerfung bei Betriebsprüfung · löschbare bezahlte Rechnungen (Ist-Zustand!) → sofortiger GoBD-Verstoß · hart verdrahtete Konten statt `mapping_key` → Massen-Refactoring bei SKR-/Kontenplan-Wechsel · EXTF ohne Kanzlei-Importtest → verbrannter Aufwand (playground-Beweis).

---

### Referenz-Fundstellen (playground = nur Fachlogik, NICHT Design)
`config/finanz_safety.php` (Default-Deny) · `config/datev_release_checklist.php` (Blocker-/Rollen-Matrix) · `config/datev_konten_freigabe.php` + `datev_steuerfall_freigabe.php` (leere StB-Freigabe-Container) · `app/Http/Middleware/FinanzGateMiddleware.php` · `app/Services/Accounting/{JournalService,FestschreibungsService,DatevPreparationService,GateReadinessService,AccountResolutionService}.php` · `database/sql/crm_erp_accounting_*.sql` (Schema-Paten).
`ticket` Ist: `app/Http/Controllers/Invoice/{InvoiceController,InvoiceCanvasController}.php` · `app/Models/{Invoice,InvoiceItem,DealInvoice}.php` · Migrationen `invoices`/`deal_invoices`/`deals`/`departments`/`branches`/`employee_departments`.
