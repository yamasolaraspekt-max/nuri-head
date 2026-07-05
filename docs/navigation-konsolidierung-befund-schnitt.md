# Navigation — Konsolidierungs-Befund + Ziel-Schnitt

**Stand:** 2026-07-04 · **Scope:** Read-only-Konsolidierungsanalyse der Sidebar (Fokus W-Berechnung/Energie,
Doppelbegriffe, Fehlplatzierungen) · **Quelle:** `resources/views/admin/layouts/sidebar.blade.php` (aktueller,
teils uncommitteter Stand) + `docs/navigation-*`, `docs/uebernahme/ticket-NAV-01-*`.

## 0. Governance / Arbeitsweise

- **Kein Bau in diesem Schritt.** Dies ist das geforderte Befund-/Ziel-Schnitt-Dokument.
- **`sidebar.blade.php` + `EconomicCalculationController.php` haben uncommittete Änderungen der parallelen
  Instanz** (Energie-/Navi-Strang). Diese Dateien werden **nicht angefasst** (Regel: keine fremden/
  unversionierten Änderungen überschreiben). Sidebar-Korrekturen erst, wenn dieser Strang committet ist.
- Befunde wurden **live gegen den Code verifiziert** — nicht nur aus der Doku übernommen (siehe §2).

## 1. Kernbefund — Energie / W-Berechnung (der Fokus): SAUBER KONSOLIDIERT ✓

Bereich **12 „Energie"** (`sidebar.blade.php:531–539`): **PVGIS** (`admin.pvgis.index`) ·
**Heizkörper-Check** (`radiator.config.view`) · **Wirtschaftlichkeit** (`economic_calculations.index`) ·
**Förderungen** (`foerderungen.index`, Finance-Gate). Alle Regeln erfüllt:

| Regel | Status |
|---|---|
| Kein Menüpunkt „wberechnung" | ✓ nicht vorhanden |
| Kein Sammelbereich „Tools" / „Werkzeuge-Übersicht" | ✓ entfernt |
| Fertige Energie-Funktionen unter „Energie" | ✓ |
| Heizkörper nur unter Energie (nicht mehr Artikel) | ✓ (0 Treffer im Artikel-Block) |
| Heizlast/WP/WR/Sanierung erst sichtbar, wenn fertig | ✓ nicht in Sidebar (kein Platzhalter) |
| Wirtschaftlichkeit sichtbar | ✓ |

**→ Der Energie-Schnitt ist regelkonform. Kein Handlungsbedarf.**

## 2. Verifikations-Korrektur (wichtig)

**P1 „Datenbankbereinigung ungegatet" (Sicherheitsalarm) ist FALSCH — bereits gelöst.**
Der Inventur-Agent stützte sich auf `ist-befund-inventur.md §9` (Stand **2026-07-03**: „ungeschützt, Gate
berechnet, nicht genutzt"). Der **aktuelle Sidebar-Code** widerlegt das:
- `sidebar.blade.php:674`: `if ($permission && !$hasPermission($permission, 'is_read'))` — `permission` wird aktiv geprüft.
- `sidebar.blade.php:633`: `'permission' => 'Administrator'` auf „Datenbankbereinigung".
- `sidebar.blade.php:632` (Kommentar): „Commit 2 (P0): Nav-Gate gesetzt — GarbageController prüft item_id='Administrator'; Nav gleichzieht (is_admin bypass bleibt)".

→ **Gegatet.** Kein offener Sicherheitspunkt. **Folge-Doku-Notiz:** `ist-befund-inventur.md §9` ist an dieser
Stelle veraltet und sollte nachgezogen werden (Doku-Sync, keine Code-Änderung).

## 3. Ziel-Schnitt — Entscheidungen je Punkt

Entscheidung: **behalten / verschieben / entfernen / nur-URL**. Nur die Punkte, die eine Entscheidung
brauchen, sind gelistet — der Rest der 23 Bereiche ist **behalten** (produktiv, regelkonform).

| Punkt | Ort (IST) | Befund | Entscheidung | Bedingung |
|---|---|---|---|---|
| **Rechnungen** | Aufträge (`:480`, `admin.invoices.index`) | fachlich Finanzen/Buchhaltung, funktional korrekt verlinkt | **verschieben** → Finanzen | **erst nach S1-09/S1-10** (Buchhaltungs-Regel); jetzt behalten |
| **Finanzen-Bereich** | fehlt (Finance-Gate auf 3 Punkte verteilt: Förderungen/Energie, Betriebskosten+Raten/Filialen `:613–614`) | in `navi-konzept` + `NAV-01` geplant, nicht umgesetzt = **IA-2 offen** | **Entscheidung Yama:** eigener Bereich *oder* Item-Gate-Verteilung beibehalten | bewusste offene IA-2 |
| **Rechnungen-Canvas-Hinweis** | `:480` `active_routes` `/invoices/canvas` | Platzhalter-artiger UI-Hinweis | **klären** (nur-URL lassen oder als Aktion) | nicht blockierend |
| **Buchhaltung/DATEV/SKR** | nicht sichtbar (`:407` Kommentar „Weiche 3 eingefroren") | korrekt ausgeblendet | **behalten so (unsichtbar)** | bis Weiche 3 produktiv |
| **OMD** | (noch nicht in Sidebar) | Regel: keine eigene OMD-Hauptnavi | **nur** als connector_type unter Lieferanten-Schnittstellen (Artikel `:496`) | OMD-Phase 4 |

## 4. Weitere Befunde (nicht blockierend, bekannt/dokumentiert)

- **P3 — „Anfragen" vs. „Leads"**: begrifflich mehrdeutig (Leads = Pipeline / Website-Leads / Mailtabelle). Glossar-Schärfung (IA-4), keine Struktur-Änderung.
- **P5 — Unterpunkte meist nur auf Bereichs-Ebene gegatet**: feingranulares Rechtemodell offen (Phase-II Rechte-Zielmodell). Bewusst, dokumentiert.
- **„4–5-Unterpunkte-Regel"**: nicht umgesetzt (Artikel 8, Mitarbeiter 8, …) — das ist **Yamas bewusste Revision v2** (23 Ein-Wort-Bereiche), also **keine Abweichung**, sondern Entscheidung.
- Keine doppelten Routen; „Junk"/„Gelöscht"/„Übersicht" sind kontextbezogen wiederholt, **kein** Doppelbegriff-Problem.

## 5. Doku-Verbindlichkeit (welche Datei ist der Ziel-Schnitt?)

| Datei | Rolle | Stand |
|---|---|---|
| `sidebar.blade.php` | **REALITÄT** (23 Bereiche, Revision v2) | 2026-07-04, teils uncommittet |
| `docs/uebernahme/ticket-NAV-01-finale-navi-liste.md` §2 | **verbindlicher Ziel-Schnitt** (Revision v2) | 2026-07-04 |
| `docs/navigation-wberechnung-energie-inventur.md` | **verbindlich für Energie-Schnitt** | 2026-07-04 |
| `docs/navigation-ist-befund-inventur.md` | Inventur-Grundlage — **§9 veraltet** (s. §2) | 2026-07-03 |
| `docs/navi-konzept-ticket.md` | Planungs-Checkliste (v1-Input, tw. überholt) | 2026-07-03 |

## 6. Empfohlene Korrekturen (KEINE jetzt umgesetzt)

1. **Keine Sidebar-Änderung durch diese Instanz**, solange `sidebar.blade.php` uncommittete Fremd-Änderungen trägt.
2. **Doku-Sync** (sicher, sobald Fremd-Strang committet): `ist-befund-inventur.md §9` auf „gegatet" nachziehen.
3. **IA-2 (Finanzen)**: Yama-Entscheidung nötig (eigener Bereich vs. Item-Gate) — dann sauber nach S1-09/S1-10.
4. Alles Übrige: **behalten**. Der Ist-Schnitt ist funktional 1:1 tragfähig; der Energie-Fokus ist erledigt.
