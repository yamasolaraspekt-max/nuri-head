# 3D-0C — Mapping- und Migrationskonzept (Vorschlag)

**Stand:** 2026-07-15 · **read-only Konzept** (keine Migration geschrieben, keine Implementierung, keine Freigabe). **HEAD:** `59daa10`. Baut auf die **ratifizierte** 3D-0B-Entscheidung **Variante C** (gestuft, `gebaeude_geometrie` bleibt zunächst kanonisch). Bindet G0c-2, „kein Dual-Write", Rückfall-/Archiv-Regel.

## Ratifizierte Rahmenregeln (aus 3D-0B, verbindlich)
- **`gebaeude_geometrie` bleibt die alleinige fachliche Übergangs-Schreibwahrheit.** Relationale Strukturen (`building_*`) zunächst **nur als abgeleitete Projektion/kontrollierter Import**, nie als Zweit-Schreiber. **Kein Dual-Write** (s. G4). **Cutover** zu einer relationalen Wahrheit = **eigener Yama-Slice** mit Paritätsnachweis, genau einem Schreiber, Umschalt-/Rollback-Stufe.
- **Azimut-Transformation:** Zielkonvention `0°=Nord … 270°=West` (Uhrzeigersinn, `0 ≤ a < 360`, Dezimalgrade); abweichende Quellen (PVGIS Süd=0, `app/Services/Energie/InverterSizingService.php:69`) nur über **expliziten Adapter** umrechnen; **keine stillen 180°/Vorzeichen-Korrekturen**; Nordbezug + Herkunft mitführen.
- **Multi-Gebäude/-Geschoss:** Mapping-Ziel ist die Hierarchie `Objekt → Gebäude[] → Geschoss[] → {Räume, Wände, Öffnungen, Decken}`; **stabile Element-IDs** für alle fachlichen Elemente; Öffnungen an Wände gebunden (kein anonymes Polygonteil).
- **Herkunft:** jede Geometrie trägt `source` (manuell/import/**scan**/**playground**) + Zeitstempel/Konfidenz; Scan/Playground **nie** automatisch führend (Vorschlag → Gate → Bestätigung).

---

## G1. Mapping (Quelle → kanonisches Modell)

Zielentität = das kanonische Gebäudemodell (in Variante C: erweitertes `gebaeude_geometrie` bzw. der 3D-1A-Schema-Vertrag). mm-Integer, lokales Koordinatensystem, definierter Ursprung.

| Quelle | Quellfeld | Bedeutung | Zielentität | Zielfeld | Transformation | Validierung | Konfliktregel |
|---|---|---|---|---|---|---|---|
| `lead_alternative_adds` | `id` | Objekt | Gebäude | `verankerbar_id` | direkt | exists | 1 aktives Modell je Objekt |
| G1 `gebaeude_geometrie` | `raeume[]` | Räume (abgeleitet) | Raum | `rooms[]` | direkt (bereits Zielform) | Gate | – |
| G2 `raum_geometrien` | `polygon` (mm) | Raum-Umriss 2D | Raum | `footprint` | mm→mm (Ursprung normieren) | Topologie-Gate (Selbstschnitt/Entartung) | ungültig → Konfliktliste, kein Import |
| G2 | `wand_segmente[]` | Wände + Öffnungen | Wand/Öffnung | `walls[]`/`openings[]` | Segment→Wand; `oeffnungen`→Fenster/Tür | Σ Öffnung ≤ Σ Wand (Gate R7) | Überlappung → Konflikt |
| G2 | `hoehe_mm` | Raumhöhe | Geschoss/Raum | `height_mm` | direkt | >0 | fehlt → Default-Frage (kein stiller Default) |
| G2 | `geschoss` (Skalar) | Geschoss-Index | Geschoss | `storey` | Index→Storey-Entität | int | Mehrraum-Zusammenführung offen (3D-1A) |
| G2 | `decke`/`boden` | Hüllbauteile | Decke/Bodenplatte | `ceiling`/`floor_slab` | direkt | – | – |
| G2 | `wand_segmente.azimut_grad` | Ausrichtung | Wand | `azimuth` | **Konvention vereinheitlichen** (Nord=0) | 0–360 | Süd=0-Quelle (PVGIS) umrechnen |
| — (neu) | — | Nordwinkel Gebäude | Gebäude | `north_angle` | aus Georef/Import/Nutzer | 0–360 | fehlt → markiert „unbekannt" (kein Raten) |
| A2 `PVRoof` | `roof_pitch/azimuth/height` | Dachmaße (skalar) | Dach-Vorbereitung | `roof.*` | skalar→Dach-Vorbereitung | – | **kein** zweites Dachmodell; später an kanonisches Dach |
| G3 playground `geometry_json` | (opak) | Three-State | — | — | **erst nach Schema-Rekonstruktion** | Schema unbekannt | **nicht** ohne Source + Objekt-Mapping |
| U5 `roof_tiles` | Stammdaten | Dachziegel | Katalog | Katalog-Artikel | Katalog-Abgleich | verified | Dublette→EIN Katalog |
| alle | — | Herkunft | Modell | `source` (manuell/import/scan) | setzen | Enum | – |
| alle | — | Revision/Version | Modell | `version/status` | append-only | 1 aktiv | – |

## G2. Migrationsstufen (Reihenfolge, jede einzeln prüfbar + Pflicht-Stopp bei Schema)
1. **Read-only Bestandsprüfung** — Zeilenzahlen/Objektbezug je Store (lokal 0 Bestandsgeometrie erwartet; Hetzner erst Deploy-Tag).
2. **Schema-/Datenqualitätsbericht** — ungültige Polygone, fehlende Höhen/Nordwinkel, Einheiten, Objekt-Zuordenbarkeit.
3. **Additive Zielstruktur** (3D-1A/1B) — Migration additiv (kein Drop/Rename), Up/Down + Rollback belegt, **Pflicht-Stopp + Yama-Migrationsfreigabe**.
4. **Dry-Run-Mapping** — Quelle→Ziel ohne Schreiben; Konfliktliste.
5. **Vergleich Alt/Neu** — abgeleitete Werte (Flächen/Heizlast) per-Zeile-Hash identisch (Golden-Master).
6. **Dual-Read, Single-Write** — Leser lesen neu ODER alt; **genau ein** fachlicher Schreiber (der 2D-Editor/Gate).
7. **Umschalten der Leser** (Heizlast/PV/Dach) auf das kanonische Modell.
8. **Sperren alter Schreibpfade** (G2 read-only; Wächter-Test gegen Zweit-Schreiber).
9. **Integritätsprüfung** — 1 aktives Modell/Objekt, Versionskette konsistent.
10. **Späteres Archivieren** (kein Sofort-Löschen; Rückfall-/Archiv-Regel, Manifest).

## G3. Konfliktfälle + Regel
| Fall | Regel |
|---|---|
| Objekt ohne Geometrie | Modell `leer/entwurf`, kein Raten |
| mehrere Geometrien/Objekt | Objekt-Zuordnungsstrategie + manuelle Review-Liste (kein Auto-Merge) |
| unterschiedliche Einheiten | alles → mm-Integer; Meter-Eingabe wird abgelehnt (bestehender Gate-Test) |
| ungültiges Polygon / Selbstschnitt | Topologie-Gate blockt; Konfliktliste; kein Import |
| unklare Höhen / fehlender Nordwinkel | als „unbekannt" markieren, Nutzer ergänzt; **kein stiller Default** |
| abweichende Heizlast-/PV-Geometrie | Heizlast-Geometrie führt; PV-Dach dockt an; **kein** zweites Dachmodell |
| playground-Demo ohne Objektbezug | nicht migrieren (K3) |
| Scan ohne Kontrollmaß | nicht führend; Vorschlag bis Kontrollmaß/Gate |
| nicht zuordenbare Öffnung | Konfliktliste, manuell |
| doppelte Räume | dedup per Objekt+Geschoss+Footprint; Review |
| fehlende UUIDs | Ziel vergibt stabile IDs; Herkunft mitführen |

## G4. Kein Dual-Write (verbindlich)
> Während eines Übergangs: **mehrere Leser, aber genau ein fachlicher Schreiber je Wert.** Kein dauerhaftes Dual-Write zwischen alter (`raum_geometrien`) und neuer Geometrie. Der 3D-Viewer schreibt nie; er ist rein abgeleitet. Ein Wächter-Test (wie `GeometrieSchreibpfadWaechterTest`) sichert den einen Schreibpfad.

---

*Keine Migration geschrieben, keine `building_*`-Tabelle angelegt, kein Store verändert. Umsetzung erst nach 3D-0B-Entscheidung + eigenem Startblock je Stufe + Yama-Migrationsfreigabe.*
