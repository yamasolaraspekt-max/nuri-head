# Initiale Zuordnungsliste — center_role je Abteilung (Vorschlag zur Yama-Freigabe)

**Stand:** 2026-07-02 · **Read-only Vorschlag — KEIN Code, KEINE Migration, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — Views später nur im ticket-Design.
**Grundlage:** `konzept-phase-2-kostenstellen-stammsatz.md` (Stammsatz) + `konzept-phase-2-kostenstellen-rollen.md` (Rollen). Planner-/Kanban-Dateien unberührt.

> **Kein Wahrheitsanspruch.** Das ist ein begründeter **Vorschlag** für den Backfill der `cost_centers`. Erst nach Yamas Freigabe wird die Rolle je Kostenstelle gesetzt — und ist danach **historisch stabil** (Änderung nur über neue KST/Versionierung, nicht still).

## Datenbasis (read-only gelesen)
16 aktive Abteilungen, **alle in einer Filiale**: `branch_id = 1` — **„Solar Aspekt Nord GmbH"**. Keine parent-Hierarchie, alle `status = active`. Da aktuell nur eine Filiale existiert, entsteht **je Abteilung genau eine Kostenstelle** (Modell trägt aber mehrere Filialen für die Zukunft).

Flag-Defaults je Rolle: `revenue` → Umsatz **ja** / direkte Kosten **ja** / Overhead **nein** · `overhead` → **nein/nein/ja** · `internal_service` → **nein/ja/ja**.

---

## 1. Zuordnungstabelle (alle 16 Abteilungen)

| dept_id | department_name | branch_id | branch_name | empf. center_role | Umsatz? | direkte Kosten? | Overhead? | Begründung | offene Rückfrage an Yama |
|---|---|---|---|---|---|---|---|---|---|
| 39 | Heizung | 1 | Solar Aspekt Nord GmbH | **revenue** | ja | ja | nein | Handwerks-Gewerk mit Kundenumsatz | Ist „Heizung" ein eigenes Profit-Center **oder** Teil von SHK? (Überschneidung) |
| 40 | Elektro | 1 | Solar Aspekt Nord GmbH | **revenue** | ja | ja | nein | Gewerk, Kernumsatz (PV/Elektro) | — (eindeutig) |
| 41 | SHK | 1 | Solar Aspekt Nord GmbH | **revenue** | ja | ja | nein | Sanitär/Heizung/Klima, Kundenumsatz | Verhältnis zu „Heizung" (39) klären — getrennt oder zusammenfassen? |
| 42 | Bauelemente | 1 | Solar Aspekt Nord GmbH | **revenue** | ja | ja | nein | Gewerk (Fenster/Türen), Kundenumsatz | — (eindeutig) |
| 43 | Schreiner | 1 | Solar Aspekt Nord GmbH | **revenue** | ja | ja | nein | Handwerks-Gewerk, Kundenumsatz | — (eindeutig) |
| 44 | Dachdecker | 1 | Solar Aspekt Nord GmbH | **revenue** | ja | ja | nein | Handwerks-Gewerk, Kundenumsatz | — (eindeutig) |
| 45 | Maler | 1 | Solar Aspekt Nord GmbH | **revenue** | ja | ja | nein | Handwerks-Gewerk, Kundenumsatz | — (eindeutig) |
| 46 | Fliesenleger | 1 | Solar Aspekt Nord GmbH | **revenue** | ja | ja | nein | Handwerks-Gewerk, Kundenumsatz | — (eindeutig) |
| 47 | Baudekoration | 1 | Solar Aspekt Nord GmbH | **revenue** | ja | ja | nein | Gewerk mit Eigenleistung/Umsatz | Eigener Kundenumsatz **oder** unterstützt es nur andere Gewerke (→ ggf. internal_service)? |
| 48 | Controlling | 1 | Solar Aspekt Nord GmbH | **overhead** | nein | nein | ja | Steuerungs-/Verwaltungsfunktion, kein Direktumsatz | — (eindeutig) |
| 49 | Marketing | 1 | Solar Aspekt Nord GmbH | **overhead** | nein | nein | ja | Gemeinkosten, kein Direktumsatz | — (eindeutig) |
| 50 | Finanzen | 1 | Solar Aspekt Nord GmbH | **overhead** | nein | nein | ja | Verwaltungsfunktion | Finanzen/Buchhaltung/Controlling getrennt lassen oder zu einer KST bündeln? |
| 51 | Buchhaltung | 1 | Solar Aspekt Nord GmbH | **overhead** | nein | nein | ja | Verwaltungsfunktion | s. 50 (Bündelung?) |
| 52 | Verwaltung | 1 | Solar Aspekt Nord GmbH | **overhead** | nein | nein | ja | klassischer Overhead | Ist **IT** hier enthalten? Falls IT intern verrechnet werden soll → internal_service abspalten |
| 53 | Management | 1 | Solar Aspekt Nord GmbH | **overhead** | nein | nein | ja | Leitungsfunktion | Management vs. Geschäftsführung als getrennte KST sinnvoll oder zusammenlegen? |
| 54 | Geschäftsführung | 1 | Solar Aspekt Nord GmbH | **overhead** | nein | nein | ja | Leitungsfunktion, Gemeinkosten | s. 53 |

---

## 2. Eindeutig **revenue** (9)
Elektro (40), SHK (41), Bauelemente (42), Schreiner (43), Dachdecker (44), Maler (45), Fliesenleger (46) — plus **Heizung (39)** und **Baudekoration (47)** als revenue **mit Rückfrage** (siehe Tabelle).
→ **7 zweifelsfrei**, 2 revenue-aber-mit-Klärung.

## 3. Eindeutig **overhead** (7)
Controlling (48), Marketing (49), Finanzen (50), Buchhaltung (51), Verwaltung (52), Management (53), Geschäftsführung (54).
→ Alle 7 klar overhead; offene Punkte betreffen nur **Bündelung/IT-Abspaltung**, nicht die Rolle selbst.

## 4. Unklare / **internal_service**-Kandidaten
**Aktuell keine** Abteilung ist ein benannter interner Leistungserbringer (kein IT/Disposition/Fuhrpark/Lager/Werkstatt als eigene Abteilung vorhanden). Kandidaten **nur bei bewusster Entscheidung**:
- **IT** — existiert nicht als eigene Abteilung; steckt vermutlich in „Verwaltung" (52). Falls IT-Leistungen später intern verrechnet werden sollen → als `internal_service` abspalten.
- **Baudekoration (47)** — nur falls sie faktisch andere Gewerke unterstützt statt eigenen Kundenumsatz zu machen.
→ **Empfehlung:** `internal_service` in Phase 2 **nicht** vergeben (kein klarer Fall); Rolle bleibt als Wert reserviert, bis interne Verrechnung gewünscht ist.

## 5. „Yama entscheiden" — markierte Unsicherheiten
1. **Heizung (39) ↔ SHK (41):** eigenständiges Gewerk oder Überschneidung/Zusammenlegung? *(Rolle in beiden Fällen revenue — betrifft nur, ob 1 oder 2 KST.)*
2. **Baudekoration (47):** revenue (eigener Umsatz) oder internal_service (unterstützend)?
3. **Overhead-Bündelung:** Finanzen/Buchhaltung/Controlling und Management/Geschäftsführung je einzeln als KST **oder** zusammengefasst? *(Rolle bleibt overhead.)*
4. **IT:** in Verwaltung enthalten? Später als internal_service abspalten?

Alle vier betreffen **Granularität/Abgrenzung**, nicht die grundsätzliche revenue/overhead-Trennung — die ist stabil.

---

## 6. Initial-Empfehlung für den `cost_centers`-Backfill

- **1 Kostenstelle je Abteilung** (16 Stück), alle `branch_id = 1`, `status = active`, `valid_from = Startdatum`, `department_id` gesetzt, `name = "<Abteilung> — Solar Aspekt Nord GmbH"`.
- **center_role** gemäß Tabelle: **9 revenue, 7 overhead, 0 internal_service.**
- **Flags** per Rollen-Default setzen (revenue: ja/ja/nein · overhead: nein/nein/ja).
- **`code` (KOST1)**: von Yama zu bestätigende Systematik — Vorschlag z. B. revenue `1xxx`, overhead `9xxx` (rein Vorschlag; DATEV-Kompatibilität weich mit Steuerberater abgleichen).
- **`allocation_*`** bleibt NULL (keine Umlage in Phase 2).
- **Unsichere Fälle (§5)** vor dem realen Backfill klären; bis dahin trotzdem als revenue/overhead anlegbar (Rolle stabil, Granularität später über neue KST/Merge lösbar).
- **Dry-Run zuerst**, idempotent, Report der vorgeschlagenen Rollen; realer Lauf erst nach Freigabe.

---

## 7. Definition of Done (Freigabe dieser Liste)
1. Yama bestätigt oder korrigiert die `center_role` je Abteilung (16 Zeilen).
2. Die vier „Yama entscheiden"-Punkte (§5) sind beantwortet (Heizung/SHK, Baudekoration, Overhead-Bündelung, IT).
3. Entscheidung, ob `internal_service` initial vergeben wird (Empfehlung: nein).
4. `code`-Systematik (KOST1) grob bestätigt (Detail-Abgleich mit Steuerberater darf später folgen — kein Blocker).
5. Freigegebene Liste dient als Eingabe für den Backfill-Dry-Run; danach ist die Rolle je KST **historisch stabil**.

> **Kein Automatismus:** Diese Liste wird nicht selbstständig scharf. Sie ist Yamas Freigabe-Vorlage; erst danach entstehen Kostenstellen mit fixierter Rolle.
