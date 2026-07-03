# Architekturbewertung — Buchhaltung / DATEV / Kostenstellen (kritischer Review)

**Stand:** 2026-07-02 · **Read-only Bewertung — KEIN Code, KEINE Migration, keine bestehende Datei geändert. Kein neuer Bauplan, nur Kritik.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage. Planner-/Kanban-Änderungen ignoriert.
**Bewertete Dokumente:** integrationsplan · arbeitspaket-phase-0-1 · steuerberater-briefing · arbeitspaket-phase-2 · konzept-phase-2-stammsatz · konzept-phase-2-rollen · rollen-zuordnung-initial.

---

## Wichtigster Befund vorweg (der rote Faden)
Die **Denkqualität ist hoch**, die Trennungen sind fachlich richtig, das Sicherheitsmodell ist vorbildlich. **Aber der Plan ist für ein anderes Unternehmen dimensioniert als ticket heute ist:** Er ist auf **mehrere Filialen, eine funktionierende Rechnungsschiene und echtes Buchungsvolumen** ausgelegt. Real hat ticket **1 Filiale, ~0 produktive Rechnungen** (die 11 vorhandenen sind großteils Testdaten wie `TST-OPEN`, Auftragsanlage laut Bestandsaufnahme defekt) und eine **operativ noch löchrige `invoices`-Schiene** (kein PDF, keine Teilzahlung, löschbar trotz bezahlt, Nummernkreis unsicher).

**Konsequenz:** Bevor man die elaborierte Kostenstellen-/FiBu-Struktur baut, muss (a) die **Rechnungsschiene operativ tragfähig** sein und (b) die **Grundsatzentscheidung A1** fallen (baut ticket selbst Journal+DATEV, oder liefert es nur Belege+OP+Buchungsvorschläge an die Kanzlei?). Sonst baut man aufwendige Struktur auf Sand.

---

## Ampel-Überblick

| Bereich | Ampel | Kurzurteil |
|---|---|---|
| Zielarchitektur (Trennschichten, mapping_key, Gate) | 🟢 | fachlich richtig, sauber |
| Reihenfolge Phase 0/1 → 2 → Stammdaten → Vorschlag → Journal → DATEV | 🟡 | grundsätzlich richtig, aber Rechnungsschiene-Härtung & A1-Entscheidung gehören **vor** die Kostenstellen-/Journal-Arbeit |
| Phase 0/1 Zuschnitt | 🟡 | teils zu groß (Audit-/Hash-Infra ohne Nutzer), teils genau richtig (Nummernkreis, Löschsperre, nullable Felder) |
| Kostenstellen-Grundidee (KST ≠ lebende Abteilung, revenue/overhead) | 🟢 | konzeptionell stark und korrekt |
| Kostenstellen-Ausbaugrad (Historie/replacement/allocation für 1 Filiale) | 🔴 | Overengineering für den aktuellen Reifegrad — YAGNI |
| SKR03/04 über chart_of_accounts + account_mappings | 🟢 | genau richtig; vor StB nur Struktur, keine Werte |
| Buchungsvorschlag vs. Journal-Trennung | 🟢 | sehr wichtig, richtig |
| In-house Journal + DATEV-Export als Ziel | 🟡 | offene Strategiefrage — evtl. gar nicht nötig (Kanzlei-Weg) |
| Backfill jetzt (auf Testdaten) | 🔴 | verfrüht, produziert Scheinzahlen |
| Steuerliche Absicherung (Werte deferred, PRÜFPFLICHTIG) | 🟢 | vorbildlich vorsichtig |
| UI-/Workflow-Last in Phase 2 | 🟡 | Rolle+Flags+allocation+Ableitung+Override zu viel auf einmal |

---

## 1. Gesamturteil
- **Fachlich sinnvoll?** Ja. Die Zielarchitektur (isolierte `accounting_*`-Schicht neben `invoices`, Einbahn-Brücke Rechnung→Vorschlag→[Gate]→Journal→DATEV, `mapping_key`-Entkopplung, Default-Deny-Gate) ist branchenüblich und korrekt. 🟢
- **Realistisch umsetzbar?** Als Fernziel ja — aber **nicht in der aktuellen Breite auf einmal**. Der Umfang (60+ Konzept-Tabellen im Zielbild) ist für ein Ein-Filialen-Unternehmen mit dünnem Rechnungsvolumen sehr groß. Realistisch ist ein **stark abgespeckter Erstausbau**. 🟡
- **Reihenfolge richtig?** Im Kern ja (additiv → Stammdaten → Vorschlag → Journal → Export). **Zwei Korrekturen:** (1) **Rechnungsschienen-Härtung** (Nummernkreis, Löschsperre, Teilzahlung) gehört nach ganz vorne — sie behebt akute GoBD-Mängel und ist Voraussetzung für alles. (2) **A1-Entscheidung** (in-house vs. Kanzlei) muss **vor** Phase 3+ fallen, sonst baut man evtl. Journal/DATEV, die nie gebraucht werden. 🟡

## 2. Was ist stark (unbedingt behalten)
- **Trennung `invoices` (operativ) ↔ `accounting_*` (FiBu):** verhindert, dass Buchhaltung die operative Schiene verunreinigt. 🟢
- **Buchungsvorschlag ↔ Journal:** die read-only Vorschlagsschicht ist der sicherste Einstieg und erlaubt fachliche Abnahme ohne Risiko. 🟢
- **Kostenstelle als stabiler Stammsatz ≠ lebende Abteilung:** genau richtig — schützt historische Belege vor operativen Umbenennungen. 🟢
- **`mapping_key` statt harter Kontonummern:** macht SKR03/04 zur Stammdaten-Zeile, kein Code-Fork. Vorbildlich. 🟢
- **Default-Deny-Gate + „Werte nur nach StB-Freigabe":** das beste Muster aus playground; hält unfertige Finanzlogik sicher. 🟢
- **revenue/overhead-Rollentrennung:** fachlich sauber, ermöglicht Deckungsbeitrag je Gewerk. 🟢
- **Konsequente Vorsicht bei Steuerwerten** (alles PRÜFPFLICHTIG, nichts aus playground blind). 🟢

## 3. Was ist riskant
- **Overengineering Kostenstellen 🔴:** `valid_from/valid_until`, `replacement_cost_center_id`, `allocation_method`, `allocation_target_rule`, drei Erlaubnis-Flags — das ist Enterprise-Konzern-Maschinerie für **eine Filiale mit 16 Abteilungen und ~0 echten Rechnungen**. Vieles davon löst Probleme, die ticket (noch) nicht hat.
- **Bau auf löchriger Rechnungsschiene 🔴:** Kostenstellen-Pflicht ab `sent` ist wertlos, solange kaum echte Rechnungen entstehen und die Auftrag→Rechnungskette defekt ist. Erst Schiene tragfähig, dann Pflichtfelder.
- **Falscher Backfill 🔴:** ein Backfill auf überwiegend Testdaten (`TST-OPEN`, verwaiste `deal_id`) erzeugt Schein-Kostenstellen und Schein-Zuordnungen. Backfill erst, wenn echte Daten existieren.
- **Falsche Strategieannahme 🟡:** der Plan tendiert implizit zu **voller in-house-FiBu** (Journal, Festschreibung, EXTF). Für ein Handwerksunternehmen mit Kanzlei ist oft **Belege+OP+Vorschläge an die Kanzlei** ausreichend — dann sind Phasen 5/6 teils überflüssig.
- **UI-/Workflow-Überforderung 🟡:** Rolle + 3 Flags + allocation-Felder + abgeleitete KST + Override + Warn-Badges gleichzeitig — für Nutzer, die heute nicht einmal ein Rechnungs-PDF haben. Zu viel Oberfläche auf einmal.
- **Steuerliche Fehlannahme 🟡 (gering):** Struktur ist neutral; Restrisiko nur, falls jemand die playground-„Vorschlag"-Stammdaten für echt hält. Guard existiert, muss aber diszipliniert bleiben.

## 4. Was fehlt noch
- **Technisch:** operative Härtung der Rechnungsschiene selbst (PDF, Teilzahlung/echte offene Posten, lückensicherer Nummernkreis, Löschsperre) — die eigentlichen **akuten GoBD-Mängel**. Diese fehlen als eigenständige, vorgezogene Arbeit.
- **Fachlich:** Klarheit, **ob ticket überhaupt buchen soll** (A1). Ohne diese Antwort ist die halbe Roadmap spekulativ.
- **Steuerberater:** B1–B4 (SKR, Kontenplan, Steuerschlüssel, Nummernsystematik) — bekannt, aber noch offen; zusätzlich Verfahrensdokumentation.
- **GF/Yama:** Umlage-Policy, Overhead-Umlage in DATEV oder nur intern, Betriebsmodell (**wer** bucht — gibt es überhaupt eine Buchhalter-Rolle/Nutzer in ticket?).
- **Datenqualität:** verwaiste `deal_id`, Testdaten-Bereinigung, Prüfung ob real Rechnungen entstehen; ohne diese Prüfung baut man Auswertungen auf Rauschen.

## 5. Was gehört NICHT in Phase 0/1 (harte Abgrenzung)
Ausdrücklich **zu früh** für Phase 0/1:
- **Journal (`accounting_journal_entries/_lines`)** — braucht Konten/Steuerschlüssel, gehört Phase 3/5. (Im aktuellen Phase-0/1-Paket korrekt **bereits ausgeschlossen** — gut.)
- **Echte Konten / Kontenplan-Werte** — Phase 3, nach B1/B2.
- **Steuerschlüssel-Werte (`tax_codes`)** — Phase 3, nach B3.
- **DATEV-Export / EXTF** — Phase 6, nach A1 + Kanzlei-Importtest.
- **UStVA** — gar nicht in ticket (Kanzlei).
- **Automatische Umlage** — Phase 5+/Controlling, nach GF/StB.
- **Zusätzliche Kritik:** **Audit-Log + entity_changes + Hash-Kette** stehen aktuell in Phase 0 — sie haben aber **keinen Schreiber**, bis das Journal existiert. Empfehlung: **nach Phase 5 verschieben** (sonst tote Infrastruktur). Ebenso die **vollständige Kostenstellen-Historisierung** (valid_from/until, replacement, allocation) → nicht Phase 0/1/2, sondern erst wenn Mehr-Filialen/Reorg real werden.

## 6. Was gehört in Phase 0/1 wirklich rein
Nur **additiv, reversibel, steuerneutral** — und mit direktem Nutzen **heute**:
- **Nullable Ankerfelder** an `invoices`/`invoice_items` (unstrittig, bricht nichts). 🟢
- **Nummernkreis-Härtung** (lückensicher, transaktional) — behebt akuten GoBD-Mangel. 🟢
- **Löschsperre-Infrastruktur** (Löschen bezahlter Rechnungen unterbinden) — behebt akuten GoBD-Mangel. 🟢
- **Gate-Registry (Default-Deny), schlank** — billig, erzwingt Disziplin. 🟢
- **`accounting_settings`-Singleton + leere `number_ranges`** — ok.

**Urteil zum Zuschnitt:** Phase 0/1 ist **an einer Stelle zu groß** (Audit/Hash-Infra ohne Nutzer → verschieben) und ansonsten **richtig dimensioniert**. Der eigentliche Fehler ist nicht Phase 0/1, sondern dass **Phase 2 (Kostenstellen) in voller Tiefe zu früh** kommt.

## 7. Bewertung der Kostenstellen-Logik
- **KST = Abteilung je Filiale, stabiler Stammsatz, revenue/overhead:** 🟢 stimmig und richtig.
- **Was ich ändern würde (YAGNI-Schnitt):** Für den Erstausbau reicht `id, code, name, branch_id, department_id (nullable), center_role, status (active/inactive), created/updated_by`. **Verschieben**, bis real gebraucht: `valid_from/valid_until`, `replacement_cost_center_id`, `is_*_allowed`-Flags (Rolle genügt zunächst), `allocation_method/allocation_target_rule`. Bei **1 Filiale** ist „Abteilung je Filiale" ohnehin faktisch „Abteilung" — die Filial-Achse behalten, aber die Historien-/Umlage-Maschinerie später nachrüsten (additiv problemlos möglich).
- **Pflicht ab `sent`:** konzeptionell richtig, aber praktisch **erst aktivieren, wenn echte Rechnungen entstehen** — sonst blockiert man einen kaum genutzten Flow ohne Nutzen. 🟡
- **Fazit:** Grundmodell grün, Ausbaugrad rot — **abspecken, nicht umbauen**.

## 8. Bewertung SKR03/SKR04
- **chart_of_accounts + accounts + account_mappings mit `mapping_key`:** 🟢 der richtige Weg, branchenüblich, entkoppelt sauber.
- **Vor StB-Freigabe baubar:** die **leere Struktur** + `AccountResolutionService`-Skelett (liefert ohne Mapping `unmapped`, nie ein Konto). 🟢
- **Vor StB-Freigabe NICHT:** irgendwelche **Konten-, Steuersatz- oder Mapping-Werte**; `active_chart_of_account_id` bleibt NULL. 🟢
- Kleiner Hinweis: `account_mappings.valid_from/valid_until` ist temporale Zusatzkomplexität — als Struktur harmlos, aber erst mit echten Mappings befüllen.

## 9. Bewertung Buchhaltung/DATEV — die zentrale Strategiefrage
**Empfehlung: NICHT sofort auf volles in-house-Journal + DATEV zielen.** Für ein Handwerksunternehmen mit Steuerberater ist der risikoärmere, günstigere Zielkorridor:
- **ticket baut:** saubere Rechnungen (mit Nummernkreis/Löschsperre/PDF), **offene Posten**, Belegablage, **Buchungsvorschläge** (read-only) inkl. Kostenstelle.
- **Kanzlei macht:** Festschreibung, Journal-Verantwortung, UStVA, DATEV — in ihrem System (DATEV Unternehmen online / Belege+OP).
- **Grenze zur Kanzlei:** alles **ab scharfer Buchung/Festschreibung** bleibt zunächst bei der Kanzlei. In-house Journal + EXTF ist eine **optionale spätere Ausbaustufe**, nur wenn ein echter Bedarf (Volumen, Prozess) sie rechtfertigt und der Kanzlei-Importtest besteht.
- **Ampel:** in-house-FiBu als Sofortziel 🔴 (zu viel Risiko/Aufwand), Belege+OP+Vorschläge 🟢.

## 10. Empfehlung — drei Fünferlisten

**A) 5 Entscheidungen, die VOR dem ersten Code-Sprint final sein müssen**
1. **A1 – DATEV-Zielbild:** Belege+OP+Vorschläge an Kanzlei **oder** volles in-house-Journal+DATEV? (bestimmt die halbe Roadmap)
2. **Operatives Betriebsmodell:** Wer bucht/pflegt? Gibt es eine Buchhalter-Rolle in ticket? Entsteht real Rechnungsvolumen?
3. **Rechnungsschiene zuerst härten (ja/nein):** Nummernkreis + Löschsperre + Teilzahlung vor der Kostenstellen-/FiBu-Arbeit.
4. **B1 SKR03/04** (Steuerberater) — Voraussetzung für jede Stammdaten-Arbeit.
5. **Kostenstellen-Ausbaugrad:** schlanker Erstausbau (Rolle+Status) vs. volle Historie/Umlage — **jetzt festlegen**, um Overengineering zu vermeiden.

**B) 5 Arbeiten, die man sicher vorbereiten kann (ohne StB, ohne Risiko)**
1. Nullable Ankerfelder an `invoices`/`invoice_items` (additiv).
2. Nummernkreis-Härtung (steuerneutral).
3. Löschsperre-Infrastruktur (behebt GoBD-Mangel).
4. Gate-Registry (Default-Deny), schlank.
5. Leere `chart_of_accounts`/`accounts`/`account_mappings` + `AccountResolutionService`-Skelett (nur Struktur).

**C) 5 Dinge, die man ausdrücklich verschieben sollte**
1. Journal + Festschreibung + Audit-Hash-Kette (erst Phase 5).
2. DATEV-EXTF-Export + UStVA (Phase 6 / gar nicht).
3. Automatische Umlage + allocation-Felder.
4. Kostenstellen-Historisierung (`valid_from/until`, `replacement_cost_center_id`).
5. Backfill/Pflichtfeld-Enforcement, bis echte Rechnungen existieren.

---

## Kurz-Entscheidungsvorlage: „Bauen / Warten / Erst klären"

| Baustein | Empfehlung |
|---|---|
| Nullable Anker an invoices/invoice_items | **Bauen** |
| Nummernkreis-Härtung | **Bauen** |
| Löschsperre (bezahlte Rechnungen) | **Bauen** |
| Gate-Registry (schlank, Default-Deny) | **Bauen** |
| Leere SKR-Struktur + Resolver-Skelett | **Bauen** |
| Kostenstellen — schlank (code/name/branch/department/role/status) | **Erst klären** (Ausbaugrad festlegen), dann bauen |
| Kostenstellen — Historie/replacement/allocation/flags | **Warten** |
| Kostenstellen-Pflicht ab `sent` + Backfill | **Warten** (bis echtes Rechnungsvolumen) |
| Stammdaten mit echten Konten/Steuerschlüsseln | **Erst klären** (B1–B4 Steuerberater) |
| Journal / Festschreibung / Audit-Hash | **Warten** (Phase 5) |
| DATEV-Export / EXTF | **Erst klären** (A1), sonst **Warten** |
| UStVA in ticket | **Nicht bauen** (Kanzlei) |
| Rechnungs-PDF + Teilzahlung/echte OP | **Erst klären** (Priorität vs. FiBu — vermutlich vorziehen) |

**Ein-Satz-Fazit:** Die Architektur ist richtig gedacht, aber überdimensioniert für ticks heutigen Reifegrad — **erst A1 klären und die Rechnungsschiene tragfähig machen, dann eine schlanke Kostenstellen-/Stammdaten-Schicht bauen; Journal/DATEV/Umlage bewusst verschieben, bis Bedarf und Steuerberater-Freigaben real sind.**
