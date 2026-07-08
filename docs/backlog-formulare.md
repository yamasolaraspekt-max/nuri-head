# Backlog — Strang `formulare`

> Betriebsordnung 3.4. Quelle: `docs/formular-synthese-ticket-playground-arbeitspaket.md` §12 + Yama-§13-Antworten (2026-07-06). Schreiben: Yama (immer) · Bauer (nur zwingende Folge-Posten mit Beleg). Prüfer nie.

## Geltende Entscheidungen (Yama §13, 2026-07-06)
1. **„Checklisten-Formulare"** = führender Navi-Begriff. Kein neuer Bereich „Formulare".
2. **`ProductFormula`** = führendes Modell.
3. **JSON in Phase 1** — mit `schema_version` + Server-Validierung; Blast-Radius klein.
4. **playground-Vorlagen als Seeder: NUR Dry-Run (FS-06)** — echter Seed erst nach Sichtung des Dry-Run-Berichts, mit `imported_from`-Marker + Rückbau-Beweis.
5. **Alt-Checklisten ablösen: JA** (0 Zeilen = reiner Code-Rückbau, Muster `deal_invoices`) — **spätes** FS-Ticket, nicht vorziehen.
6. **`visible_if` = Alpine** (CLAUDE.md-Scope erweitert, Direktiven-Commit `05d830c`). Grenze: kein Alpine-Wildwuchs außerhalb der zwei Scopes.
7. **JSON dauerhaft = OFFEN** — Normalisierung (Phase 3) vertagt bis nach FS-08; kein Vorratsbeschluss.
> **Sicherheit (Zusatz A, `docs/formular-sicherheitsbefund.md`):** `new Function()` auf auth-only-autorierten Strings = **bestätigte** (dormante) Stored-XSS/Code-Exec-Exposition → **FS-03 auf Position 1**; FS-03 **gated** FS-07/FS-08 (Fill-Seite darf nicht live gehen, solange `new Function` im Pfad steht).

## Posten (Reihenfolge)

### FS-03 — `FormulaEvaluationService` in ticket (**Position 1**, Sicherheit)
- **Ziel:** playgrounds eval-freie Engine als reinen, getesteten ticket-Service portieren (ersetzt später `new Function`).
- **Dateien:** `app/Services/Formular/{FormulaEvaluationService,UnitConversionService,PlausibilityService}.php` + `tests/Unit/Formular*`.
- **Abh.:** **keine** (framework-neutral; Eingabe = formula-String + Operanden; Anbindung ans v2-Schema erst FS-07/08).
- **Auflagen:** kein `eval`/`new Function`; Whitelist-Parser (Shunting-Yard→RPN); Operatoren `+-*/`; Funktionen `SUM/MENGE/FLAECHE/VOLUMEN`; Operanden-Gate (vollständig/unvollständig/ungeprüft); UnitConversion; Rundung; min/max-Warnung; DoS-Grenzen (2000/32). Reine Funktion, keine Persistenz.
- **Abnahme-Anker:** Operanden-Gate-Tests grün (§10); nicht-whitelistetes Zeichen → `unvollstaendig`, nie Ausführung; `SUM` aggregiert Wiederholzeilen.
- **Nicht im Scope:** UI, Migration, Bestandseingriff, Anbindung an `product_formulas`.

### FS-01 — Ist-Bestand & Schema-Doku  ·  GEBAUT (2026-07-08)
> Abgedeckt durch docs/formular-synthese-ticket-playground-arbeitspaket.md (v1-Bestand + v2-Zielschema §5).
- **Ziel:** `fields`-v1-Bestand + Arbeitspaket fixieren. **Dateien:** `docs/formular-*`. **Abh.:** —. **Anker:** Schema v1 dokumentiert. **Nicht im Scope:** Code.

### FS-02 — ProductFormula JSON-Schema v2  ·  GEBAUT (2026-07-08)
> Additive Migration (schema_version int Default 1 + imported_from nullable, lokal+Test migriert, Rueckbau in down()); FormSchemaValidator (Server, reine Funktion) gegen v2-Schema; ProductFormula fillable+cast erweitert. 11 Tests gruen. Prod-Migration = Tag-X.
- **Ziel:** v2-Feldschema (§5) + Konzept `product_formulas.schema_version`/`imported_from` + Server-Validator. **Dateien:** `app/Models/ProductFormula.php`, Migration (**Tag-X**), Validator. **Abh.:** FS-01. **Anker:** Schema-Validierung grün; v1→v2 verträglich. **Auflagen:** additiv (2 nullable Spalten), kein Bestandseingriff. **Nicht im Scope:** Renderer.

### FS-04 — visible_if Engine (Alpine)  ·  GEBAUT (2026-07-08)
> VisibleIfService (Server-autoritativ): istSichtbar (=/!=/<>/<=/>=/in/not_in), sichtbareFelder mit Kaskade (kein verwaistes Kind), fehlendePflichtfelder (unsichtbares Pflichtfeld blockt NICHT), alpineAusdruck (x-show fuer Scope-2-Rendering). 8 Tests gruen. UI-Verdrahtung = FS-07.
- **Ziel:** `VisibleIfService` (Server-Auswertung `Feld op Wert`) + Alpine-Rendering (CLAUDE.md-Scope 2). **Abh.:** FS-02. **Anker:** Sichtbarkeits-Tests; unsichtbares Pflichtfeld blockt nicht. **Auflagen:** Alpine nur im Formular-Rendering-Scope. **Nicht im Scope:** jQuery-Variante (Alpine entschieden).

### FS-05 — SmartroutingService
- **Ziel:** Tabelle `product_formula_routing_rules` (Tag-X) + Port re-anchored (Artikelgruppe/Lead-Produkt-Zeile/Objekt-Typ = Phase 1). **Abh.:** FS-02. **Anker:** Routing-Tests (Mehrdeutigkeit→priority/Spezifität; kein Treffer→definierter Fallback/leere Liste+Hinweis). **Nicht im Scope:** Phase-2-Anker (deals/phase/branch).

### FS-06 — playground-Vorlagen-Mapper/Seeder **Dry-Run**
- **Ziel:** Stored-Procedure-SQL + Laravel-Seeder (Heizlast/Aufmaß/WP-Konfig) → ticket-Eloquent, `imported_from='playground:<slug>'`, **Dry-Run (kein Insert)** + Bericht. **Abh.:** FS-02. **Anker:** Bericht listet 21 Vorlagen/358 Felder korrekt; `risk_level='fachlich-vorlaeufig'` gesetzt (Heizlast ≠ DIN EN 12831). **Auflagen (§13-4):** **echter Seed erst nach Yama-Sichtung** des Berichts + Rückbau-Beweis. **Nicht im Scope:** produktiver Seed, Antwortdaten.

### FS-07 — Builder/UI minimal erweitern
- **Ziel:** EIN Editor / EIN Save-Pfad, v2-Keys, ticket-Design; `new Function` raus (Server-Engine); Leichen entfernen (`index1.blade.php`, `test.blade copy.php`), kaputte Route `product.formula.updates` bereinigen. **Abh.:** FS-02/03/04. **Anker:** Builder speichert v2, kein `new Function` mehr, Bestands-Views brechen nicht. **Nicht im Scope:** `sidebar.blade.php` (NAV-Strang, tabu).

### FS-08 — Antwortspeicherung & Auswertung
- **Ziel:** Server-Pflichtfeld-Validierung, `filled_values`+Snapshot, Berechnung persistiert (`is_calculated`), Lead-/Objekt-Verankerung, Ausfüll-Seite verdrahten. **Abh.:** FS-03/05/07. **⚠ Gate:** darf erst live, wenn `new Function` raus (FS-03/FS-07 grün) — Sicherheits-Befund. **Anker:** idempotentes Speichern; Pflichtfeld-Serverprüfung. **Nicht im Scope:** OCR/`recognition_json`-Inhalt.

### FS-09 — Regression/Tests
- **Ziel:** Suite §10. **Abh.:** alle. **Anker:** 0 Fehler, Anzahl ≥ Vorgänger; „keine zweite Formularwelt" grün.

### FS-10 — Navi/Terminologie-Cleanup + Alt-Checklisten-Rückbau (spät)
- **Ziel:** „Checklisten-Formulare" führend; Doppelbegriff „Wartungs-Checklisten" auflösen; **Alt-Checklisten-Code-Rückbau** (0 Zeilen, Muster `deal_invoices`, **vor Rückbau erneut zählen**). **Abh.:** Yama §13-1/5; NAV-Strang für sidebar. **Anker:** eine Formularwelt, ein Begriff. **Nicht im Scope:** NAV-Strang-Dateien (`sidebar.blade.php`) — Koordination nötig.
