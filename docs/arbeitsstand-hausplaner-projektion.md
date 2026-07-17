# Arbeitsstand-Board — Hausplaner & Szene-Projektion (ticket)

**Stand:** 2026-07-17 · **lebendes Board** (operativ, kurz). Strategische Heimat = Kapitel 4
(Gebäudegeometrie) in `docs/gesamtfahrplan-gebaeude-energie-angebot.md`. Der Master ist auf Stand
14.07. und kennt Hausplaner/TopologieGate/Projektion NOCH NICHT → eigener Auffrisch-Schritt (Spur D).

## Rollen & Schleife (immer gleich)
- **Planner** (Claude, Planner-Hut): Spec, kein Code.
- **Generator** (Claude): baut additiv, meist UNVERDRAHTET zuerst; grep-first (Code = Wahrheit, CLAUDE.md Z.27);
  `php -l`, Gegen-Grep; meldet „umgesetzt", nie „grün".
- **Evaluator** (eigener Strang): führt Tests SELBST aus, Gegen-Beweis, Urteil grün/rot. Claude schreibt
  den Auftragstext.
- **Yama**: Richtungs-/Verdrahtungs-Entscheidungen, Browser-/Sicht-Abnahmen, `migrate`, Push.

---

## Spur A — Hausplaner in ticket produktiv machen
| Schritt | Was | Wer | Status |
|---|---|---|---|
| A1 | Code-Layer T-a/b/c (Domäne, Controller, Routen, Insel, Sperre, Einstieg) | Claude→Evaluator | ✅ abgenommen (cc40fc9) |
| A2 | T-d: 5 Browser-Sichtproben (`docs/abnahme-hausplaner-t-d.md`) | **Yama** | offen → danach Claude: „In Abnahme"-Marker entfernen (additiv) |
| A3 | Rechte-Mapping `hausplaner.*` ins item/action-Raster (Mitarbeiter-Zugriff ohne is_admin) | Planner-Spec→Claude→Evaluator→Yama | wartet auf A2 (ändert Abnahme-Routen) |

## Spur B — Naht Szene → belastbare Wahrheit (`gebaeude_geometrie`)
| Schritt | Was | Wer | Status |
|---|---|---|---|
| B1 | P2-1a `SzeneProjektionService` Ein-Raum (rein, unverdrahtet) | Claude→Evaluator | ✅ grün abgenommen (dbb630d) |
| B2 | P2-1b planare Raumerkennung, innen/aussen, Mehrraum | Claude→Evaluator | ✅ grün abgenommen (3498cde), inkl. T-Punkt-Bonus |
| B3 | P2-1c mehrere Geschosse + Öffnungs-Feinheiten | Claude→Evaluator | nach B2 |
| B4 | P2-2 Verdrahtung: Szene → `gebaeude_geometrie` (neue Profil-Version, bestehender Schreibpfad) | **Yama-Go** + Referenzfall → Claude→Evaluator→Yama | 📋 Spec fertig (ff92aba); Evaluator-Spec-Review vorbereitet; wartet Yama-Go + Referenzfall |

## Spur C — Gewerke auf der Naht (Zielbild §10, je eigenes Paket, NACH Spur B)
Reihenfolge, jeweils Planner-Spec → Claude → Evaluator:
C1 Dach-Andock (RoofNode in dieselbe Szene, Fusion Dachplaner→Hausplaner) ·
C2 belastbare Dachfläche + Azimut→PV (Azimut liefert B bereits) ·
C3 PV-Belegung als Projektion ·
C4 Textur/PBR (three.js-Renderer) ·
C5 Möbel-Katalog-Assets (GLB) + ObjectNode platzieren ·
C6 Innenraum (Bad/Küche/Fenster) als Katalog-Module ·
C7 glTF-Export → Fotorealismus (angedockt).

## Spur D — Governance & Betrieb (laufend)
- **Yama:** `php artisan migrate` (3 Hausplaner-Tabellen, für A2) · playground `.env.testing`
  (Hausplaner-Contract-Tests) · Push-Script (Commit-Berg) · MySQL-Root-Reset (3307-Instanz).
- **Claude:** „eine Wahrheit je Wert"-Regel ins Zielbild (nach Yama-OK zur Formulierung) ·
  Master-Fahrplan Kapitel 4 auffrischen (Hausplaner/TopologieGate/Projektion nachtragen).

---

## Sofort — die nächsten 3 Schritte
1. **Yama:** T-d durchgehen (falls nötig vorher `migrate`) → dann meldet Claude den Marker weg.
2. **Evaluator:** P2-1a (dbb630d) prüfen (Auftragstext liegt vor).
3. **Claude:** nach P2-1a-grün → B2 (P2-1b) bauen. Parallel möglich, wenn du willst: A3-Rechte-Mapping-Spec
   schreiben (baubar erst nach A2).

**Merksatz gegen den Überblicksverlust:** Spur A macht den Hausplaner *bedienbar*, Spur B macht ihn
*belastbar* (verbindet ihn mit Heizlast/PV), Spur C füllt die Gewerke. A und B laufen parallel; C kommt
nach B.
