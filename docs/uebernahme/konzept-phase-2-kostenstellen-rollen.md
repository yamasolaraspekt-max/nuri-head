# Konzept-Ergänzung Phase 2 — Kostenstellen-Rollen (Umsatz vs. Overhead vs. interner Service)

**Stand:** 2026-07-02 · **Read-only Konzept — KEIN Code, KEINE Migration, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — spätere Views strikt im ticket-Design.
**Ergänzt:** `docs/uebernahme/konzept-phase-2-kostenstellen-stammsatz.md` (Stammsatz-Modell). Alle dortigen Regeln (Stammsatz-Stabilität, Historie, Ableitung, Pflichtfelder, SKR-Vorbereitung, Gates ROT) gelten weiter; hier kommt die **Rollen-Dimension** dazu. Planner-/Kanban-Dateien unberührt.

## Neue fachliche Information
Abteilungen haben unterschiedliche betriebswirtschaftliche Funktionen:
1. **Umsatz-/Gewinn-Abteilungen** (Profit-Center) — z. B. Elektro, SHK, Dachdecker, Maler, Bauelemente, Schreiner, Fliesenleger, Baudekoration.
2. **Kosten-/Overhead-Abteilungen** (Cost-Center) — z. B. Verwaltung, Marketing, IT, Controlling, Finanzen, Buchhaltung, Management, Geschäftsführung.
3. **Optional später: interne Service-Abteilungen** — leisten an andere Abteilungen (z. B. Fuhrpark, Werkstatt, Lager), werden intern verrechnet.

> Beleg aus den echten Daten (16 Abteilungen, 1 Filiale): umsatztragend u. a. Bauelemente, Schreiner, Dachdecker, Maler, Fliesenleger, Baudekoration; Overhead u. a. Controlling, Marketing, Finanzen, Buchhaltung, Verwaltung, Management, Geschäftsführung. Die Rollen-Zuordnung ist damit real und trennscharf.

---

## 1. Empfehlung `center_role` — und wie es zu „Kostenstelle = Abteilung je Filiale" passt

**Empfehlung:** `center_role` als **Pflicht-Enum** am Kostenstellen-**Stammsatz** (nicht an der lebenden Abteilung):
- `revenue` — umsatztragend (Profit-Center)
- `overhead` — kostenverursachend, kein Direktumsatz (Cost-Center)
- `internal_service` — interner Leistungserbringer (Phase-2 nur als Wert reserviert, Verrechnung später)

**Passung zum Stammsatz-Modell:** Die Rolle hängt an der **Kostenstelle je (Filiale, Abteilung)**, nicht an der Abteilung selbst. Das ist wichtig, weil dieselbe Abteilung theoretisch je Filiale unterschiedlich geführt werden könnte und — vor allem — weil die Rolle **historisch stabil** bleiben muss (siehe §8). In Fall A (aktuell: Abteilung ist filialspezifisch, 1 Filiale) ist das faktisch „eine Rolle je Abteilung", aber das Modell bleibt zukunftssicher für mehrere Filialen.

**Ableitung der Rolle:** wird beim Anlegen/Backfill je Kostenstelle **einmal gesetzt** (Vorschlag aus einer pflegbaren Zuordnungsliste Abteilung→Rolle, von Yama/Controlling bestätigt) — **nie** automatisch aus Buchungen abgeleitet und nie rückwirkend geändert.

---

## 2. Welche Buchungen dürfen auf welche Rolle

Gesteuert über drei Erlaubnis-Flags je Kostenstelle (Default aus `center_role`, aber überschreibbar für Sonderfälle):

| Rolle | `is_revenue_allowed` | `is_direct_cost_allowed` | `is_overhead_allowed` |
|---|---|---|---|
| `revenue` | **ja** | **ja** (Material, Lohn, Projektkosten) | nein (Standard) |
| `overhead` | **nein** | nein (Standard) | **ja** (Verwaltung, Marketing, IT, Miete) |
| `internal_service` | nein | ja (eigene Leistungskosten) | ja | 

- **Umsatz** (Ausgangsrechnung/Erlös) darf nur auf Kostenstellen mit `is_revenue_allowed = true` (i. d. R. nur `revenue`).
- **Direkte Kosten** (Material, Lohn, Projekt) nur auf `is_direct_cost_allowed = true` (`revenue`, `internal_service`).
- **Overhead-Kosten** (Miete, Marketing, IT, Verwaltung) nur auf `is_overhead_allowed = true` (`overhead`, `internal_service`).

In **Phase 2** sind das **Validierungs-/Warnregeln auf der Rechnungs-/Zuordnungsebene** — es gibt noch keine scharfe Buchung. Die Flags werden jetzt gepflegt und geprüft, damit spätere Phasen (5+) sie hart erzwingen können.

---

## 3. Umsätze

- **Umsatzrechnungen (Ausgangsrechnungen) nur auf `revenue`-Kostenstellen** (`is_revenue_allowed = true`).
- **Rechnung versehentlich auf Overhead:** die Ableitung schlägt nie eine Overhead-KST für eine Umsatzrechnung vor; wählt ein Nutzer sie manuell, greift bei `status='sent'` eine **Validierung** → Toastr-Warnung „Umsatz auf Overhead-Kostenstelle nicht zulässig", Speichern blockiert (harte Regel) bzw. in einer Übergangsphase Warn-Badge (weiche Regel, konfigurierbar). **Empfehlung: hart bei `sent`.**
- Ausnahmen (z. B. Weiterberechnung aus einer Overhead-Einheit) laufen nur über explizit gesetztes `is_revenue_allowed = true` an genau dieser KST — nie über stille Aufweichung der Rolle.

---

## 4. Direkte Kosten

- **Material, Lohn, Projektkosten → `revenue`-Kostenstelle** des leistenden Gewerks (`is_direct_cost_allowed = true`). So entstehen direkte Kosten dort, wo auch der Umsatz steht → Deckungsbeitrag je Gewerk wird bildbar.
- **Verwaltungskosten, Marketing, IT, Miete → `overhead`** (bzw. `internal_service`), `is_overhead_allowed = true`.
- **Interner Service** (später): erbringt Leistung an andere KST; seine Kosten sammeln sich auf der `internal_service`-KST und werden später per Umlage weiterverteilt (§5).
- Phase 2 erfasst nur die **Zuordnung/Dimension** (welche KST trägt die Kostenzeile) — **keine** Kostenverbuchung, keine Umlage.

---

## 5. Gemeinkosten-Umlage — nur VORBEREITET, keine Automatik in Phase 2

Zwei nullable Vorbereitungsfelder je Kostenstelle (siehe §6): `allocation_method` und `allocation_target_rule`. Erlaubte Methoden (als Enum reserviert, **ohne** Ausführungslogik):
- `revenue_share` — nach Umsatzanteil der Empfänger-KST
- `headcount` — nach Kopfzahl (`employee_departments`)
- `hours` — nach geleisteten Stunden
- `fixed_key` — fixer Schlüssel (`allocation_target_rule` als JSON, z. B. `{"cost_center_id": 42, "percent": 30}`)
- `none` / NULL — keine Umlage

**Phase-2-Regel:** Felder sind **pflegbar und validierbar**, aber es läuft **keine automatische Umlage**. Es wird nichts verteilt, nichts gebucht. Die Umlage-Ausführung ist ein späteres, GF-/Steuerberater-freigegebenes Modul (Phase 5+/Controlling). So ist die Struktur da, ohne unbestätigte Verteilzahlen zu erzeugen.

---

## 6. Zusätzliche Felder für `cost_centers`

Ergänzend zum Stammsatz-Modell (`konzept-phase-2-kostenstellen-stammsatz.md`, §2):

| Feld | Typ | Bemerkung |
|---|---|---|
| center_role | string(16) | `revenue` / `overhead` / `internal_service` — **Pflicht**, historisch stabil |
| is_revenue_allowed | boolean, default (aus role) | Umsatzbuchungen erlaubt |
| is_direct_cost_allowed | boolean, default (aus role) | direkte Kosten erlaubt |
| is_overhead_allowed | boolean, default (aus role) | Overhead-Kosten erlaubt |
| allocation_method | string(16), nullable | `revenue_share`/`headcount`/`hours`/`fixed_key`/`none` — nur Vorbereitung |
| allocation_target_rule | json, nullable | Parameter zur Methode (z. B. Ziel-KST/Prozent) — nur Vorbereitung |

**Defaults beim Anlegen** (überschreibbar): `revenue` → revenue+direct true, overhead false · `overhead` → nur overhead true · `internal_service` → direct+overhead true, revenue false. `allocation_*` bleibt NULL bis zur Umlage-Freigabe.

---

## 7. Ermöglichte Auswertungen (später)

Durch Rolle + Erlaubnis-Flags werden auf sauberer Datenbasis bildbar:
- **Umsatz je `revenue`-Kostenstelle** (Erlöse gefiltert auf `is_revenue_allowed`).
- **Direkte Kosten je Kostenstelle** (Material/Lohn/Projekt).
- **Overhead je `overhead`-Kostenstelle**.
- **Deckungsbeitrag vor Umlage** = Umsatz − direkte Kosten je `revenue`-KST.
- **Ergebnis nach Umlage** = Deckungsbeitrag − anteilig umgelegter Overhead (erst nach Umlage-Modul).

Phase 2 liefert die **Dimension**; die Auswertungen selbst folgen, sobald Buchungen (Phase 5) und Umlage (später) existieren. Wichtig: **Deckungsbeitrag vor Umlage** ist schon nach Phase 5 möglich (ohne Umlage-Entscheidung) — ein früher, sicherer Nutzen.

---

## 8. Regeln für dynamische Abteilungen (Rolle historisch stabil)

- **`center_role` ist am Stammsatz historisch stabil** — eine bestehende Kostenstelle ändert ihre Rolle **nicht**. Bereits gebuchte/zugeordnete Rechnungen behalten die Rolle, unter der sie liefen.
- **Rollenwechsel nur über bewusste Versionierung:** Soll eine Abteilung fachlich die Rolle wechseln (z. B. interne Einheit wird zum Profit-Center), wird die alte KST `archived` (`closed_at`, ggf. `replacement_cost_center_id`) und eine **neue** KST mit neuer Rolle + neuem `valid_from` angelegt. Kein In-Place-Update der Rolle.
- Umbenennung/Auflösung/Merge der operativen Abteilung berührt die Rolle der historischen KST nicht (rückwirkungsfrei, konsistent mit dem Stammsatz-Prinzip).

---

## 9. UI-Regeln (nur ticket-Design)

- **Rollen-Badge** je Kostenstelle: `revenue` (grün „Umsatz"), `overhead` (grau „Overhead"), `internal_service` (blau „Interner Service") — ticket-Badge-Stil.
- **Filter nach Rolle** in der Kostenstellen-Liste (Select2/Buttons), zusätzlich zu Status-Filter.
- **Warnung bei unpassender Buchung/Zuordnung** im Rechnungsformular: wählt der Nutzer eine KST, deren Rolle/Flags nicht zur Belegart passt (z. B. Umsatz auf Overhead), erscheint Toastr-Warnung + rotes Feld; bei `sent` blockiert (harte Regel).
- **Anlege-/Generator-Assistent** zeigt Rollen-Vorschlag je Abteilung (aus Zuordnungsliste), Nutzer bestätigt/ändert bewusst.
- **Umlage-Felder** (`allocation_*`) im KST-Formular als **read-only/„vorbereitet, inaktiv"** markiert, solange Umlage-Modul nicht freigegeben — damit klar ist: gepflegt, aber nicht wirksam.
- Keine Konten-/Buchungs-UI (unverändert Phase-2-Grenze).

---

## 10. Was steuerberater-/GF-pflichtig bleibt

- **Umlageschlüssel** (welche Methode, welche Prozente/Ziele): **GF/Controlling** definiert die Geschäfts-Policy; sobald Umlage in DATEV wirkt, mit **Steuerberater** abstimmen.
- **DATEV-Kostenstellenlogik**: ob und wie `center_role`/KOST1 in DATEV abgebildet wird (KOST1/KOST2), Kostenstellen-Nummernsystematik — **Steuerberater** (Teil B4/DATEV-Zielbild).
- **Ob Overhead in DATEV oder nur intern in ticket umgelegt** wird: Grundsatzentscheidung **GF + Steuerberater**. Empfehlung: interne Umlage/Auswertung in ticket zuerst; DATEV-seitige Umlage nur, wenn der Steuerberater sie führt.
- **Rollen-Grenzfälle** (z. B. eine Abteilung mit Misch-Charakter): fachliche Einordnung durch GF/Controlling, nicht durch das System.

**Nicht** blockiert (jetzt baubar): das Rollen-Feld, die Erlaubnis-Flags, die Validierungs-/Warnregeln, die Auswertungs-Dimension und die Umlage-**Vorbereitungsfelder**.

---

## 11. Anpassung an die bestehende Phase-2-Kostenstellenlogik

- **Ergänzt** das Stammsatz-`cost_centers` um `center_role` + drei Flags + zwei Umlage-Felder (§6) — keine Änderung der bestehenden Stammsatz-/Historien-/Ableitungsregeln.
- **Ableitung** (Kopf > deal > project > offer > creator): unverändert; zusätzlich prüft die Zuordnung jetzt die **Rollen-Erlaubnis** und warnt/blockiert bei Nichtpassung.
- **Backfill:** beim Erzeugen der KST je (Filiale, Abteilung) wird `center_role` aus der Zuordnungsliste vorgeschlagen (Bauelemente/Schreiner/Dachdecker/Maler/Fliesenleger/Baudekoration → `revenue`; Controlling/Marketing/Finanzen/Buchhaltung/Verwaltung/Management/Geschäftsführung → `overhead`) — Yama/Controlling bestätigt final. Kein Auto-Scharfschalten.
- **Pflichtregel** erweitert: eine `revenue`-KST auf einer Ausgangsrechnung ist ab `sent` Pflicht **und** rollen-konform.
- **Gates** bleiben ROT; keine Buchung, keine Umlage, keine Steuerwerte.

---

## 12. Risiken & Guards

| Risiko | Guard |
|---|---|
| Umsatz landet auf Overhead-KST | Ableitung schlägt nie Overhead für Umsatz vor; `is_revenue_allowed`-Prüfung + Blockade bei `sent` |
| Rolle wird nachträglich „umgebogen" und verfälscht Historie | Rolle am Stammsatz **immutable**; Wechsel nur über neue KST/Versionierung (§8) |
| Automatische Umlage erzeugt unbestätigte Zahlen | in Phase 2 **keine** Umlage-Ausführung; `allocation_*` nur Vorbereitung, im UI als inaktiv markiert |
| Falsche Rollen-Zuordnung beim Backfill | Vorschlag aus Zuordnungsliste, **manuelle Bestätigung**; Report der zugeordneten Rollen |
| Flags widersprechen der Rolle (Fehlkonfiguration) | Default-Flags aus Rolle; Abweichung nur bewusst, mit Hinweis; Validierung verhindert unmögliche Kombis |
| Overhead-Deckungsbeitrag-Verwirrung | Auswertung trennt strikt „vor Umlage" (ohne Overhead-Verteilung) und „nach Umlage" (erst nach Modul) |
| Scope-Creep in Buchung/Steuer | Rolle betrifft nur Dimension/Validierung; Gates ROT, keine Konten/Steuerwerte |

---

## 13. Definition of Done

1. `cost_centers` trägt `center_role` (Pflicht) + `is_revenue_allowed`/`is_direct_cost_allowed`/`is_overhead_allowed` + `allocation_method`/`allocation_target_rule` (nullable, nur Vorbereitung) — additiv zum Stammsatz-Modell.
2. Rollen-Defaults + überschreibbare Flags implementiert; Zuordnungsliste Abteilung→Rolle pflegbar; Backfill schlägt Rollen vor, Yama/Controlling bestätigt.
3. Validierung: Umsatzrechnung nur auf `revenue`/`is_revenue_allowed`; unpassende Zuordnung wird bei `sent` blockiert, im Draft nur gewarnt.
4. Rolle historisch stabil (immutable); Rollenwechsel nur über neue KST/Versionierung; dynamische Abteilungsänderungen rückwirkungsfrei.
5. Umlage ausschließlich **vorbereitet** — keine automatische Verteilung, keine Buchung; Felder im UI als inaktiv markiert.
6. UI im ticket-Design: Rollen-Badges, Rollen-Filter, Warnung bei unpassender Buchung.
7. Auswertungs-Dimension steht (Umsatz/direkte Kosten/Overhead je KST, Deckungsbeitrag vor Umlage später bildbar); Gates ROT; Live-Daten unverändert.

---

## 14. Klare Empfehlung

Führe `center_role` als **immutable Pflichtfeld am Kostenstellen-Stammsatz** ein (`revenue`/`overhead`/`internal_service`), gesteuert durch drei Erlaubnis-Flags mit rollenbasierten Defaults. In Phase 2 sind das **Dimension + Validierung/Warnung**, keine Buchung. Bereite die Umlage über `allocation_method`/`allocation_target_rule` **nur strukturell** vor — ohne jede automatische Verteilung. Der frühe, sichere Nutzen ist der **Deckungsbeitrag vor Umlage je Gewerk** (nach Phase 5, ohne Steuerberater-Umlageentscheidung). Umlageschlüssel und die Frage „Overhead-Umlage in DATEV oder nur intern in ticket" bleiben GF-/Steuerberater-Sache und werden erst im späteren Umlage-Modul scharf.
