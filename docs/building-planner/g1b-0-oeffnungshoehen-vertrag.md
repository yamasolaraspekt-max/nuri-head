# G1b-0 — Öffnungshöhen-Vertrag (Entscheidung A-3)

**Welle:** G1b-0 · **Basis:** `b31f451` · **Stand:** 2026-07-16 · **read-only Vertrag. Kein Validatorcode ändern.**
**Löst:** die offene G1a-Auflage **A-3** (aus CAD-Re-Evaluation): `lintel_height_mm`/`sill`-Kreuzprüfung + Datumsklarstellung.
**Bezug:** G1a-Modellvertrag §3.2, `resources/schemas/building-model/v1.schema.json` (`$defs.opening`, `$defs.wall`), Höhendatum `height_datum = okff_eg_zero`.

---

## 1. Höhendatum (verbindlich)

- **Nullpunkt:** OKFF Erdgeschoss = 0 mm (`height_datum = "okff_eg_zero"`).
- **Geschoss:** `finished_floor_level_mm` = OKFF des Geschosses, relativ zum globalen Nullpunkt.
- **Alle Öffnungshöhen (`sill_height_mm`, `lintel_height_mm`) beziehen sich auf die OKFF des Geschosses, in dem die Wirtswand liegt** — nicht auf die Wandunterkante, nicht auf Rohdecke.

## 2. Wand-Höhenbezug

| Feld | Bedeutung | Bezug |
|---|---|---|
| `wall.bottom_offset_mm` | Wandunterkante | relativ zur OKFF des Geschosses (i. d. R. 0) |
| `wall.height_mode` | `uniform` \| `profile` | — |
| `wall.height_mm` | Wandhöhe (bei `uniform`) | von Wandunterkante nach oben |
| **Wandoberkante** | abgeleitet | `bottom_offset_mm + height_mm` (bei `uniform`) |

## 3. Öffnungshöhen (uniforme Wand)

Definitionen (alle mm-Integer, OKFF-bezogen bzw. wie angegeben):

```
sill_height_mm      = Brüstungs-/Schwellenhöhe der ROHBAUöffnung, ab OKFF
rough_height_mm     = lichte Rohbauhöhe des Wandausschnitts (führt den Ausschnitt)
rough_top_mm        = sill_height_mm + rough_height_mm          (Oberkante Rohbauausschnitt, ab OKFF)
lintel_height_mm    = Sturz-/Öffnungsoberkante ab OKFF (optional geführt)
```

**Verbindliche Regeln:**

1. **Rohbaumaß führt den Ausschnitt.** `rough_width_mm`/`rough_height_mm` bestimmen den geometrischen Wandausschnitt. `finished_*` sind **ergänzend** und dürfen den Rohbau **nicht überstimmen** (`finished_* ≤ rough_*`).
2. **Sturzkonsistenz:** Ist `lintel_height_mm` angegeben, muss gelten
   ```
   lintel_height_mm == rough_top_mm  ( == sill_height_mm + rough_height_mm )
   ```
   **oder** es liegt ein **ausdrücklich gekennzeichneter Messkonflikt** vor (Fehlercode-Vorschlag `opening.lintel_conflict`, Severity error) — **keine stille Priorisierung** eines der beiden Werte.
3. **Vertikale Lage in der Wand:** die Öffnung liegt vollständig im Wandkörper:
   ```
   sill_height_mm            >= wall.bottom_offset_mm
   rough_top_mm              <= wall.bottom_offset_mm + wall.height_mm
   ```
   Verletzung → `opening.exceeds_wall_height` (bereits in G1a implementiert).
4. **Wandoberkante darf nicht überschritten werden** (Regel 3, obere Ungleichung).
5. **Nicht-negativ:** `sill_height_mm >= 0`, `rough_height_mm >= 1`.

## 4. Öffnungshöhen bei `height_mode = profile` (spätere Welle)

Bei geneigter/variabler Wandoberkante wird die zulässige Obergrenze **nicht** aus einem konstanten `height_mm` gebildet, sondern aus der **lokalen Wandhöhe an der Öffnungsstation**:

```
rough_top_mm <= wall.bottom_offset_mm + lokaleWandhöhe(station_mm)
```

`lokaleWandhöhe(station_mm)` wird aus dem späteren Höhenprofil interpoliert. **G1b-0 legt nur den Vertrag fest; der Profil-Algorithmus wird nicht gebaut.** Bis dahin gilt ausschließlich `uniform`.

## 5. Datenherkunft / fehlende Operanden

- Fehlt `sill_height_mm` in einer Altquelle (Mapping M16) → **`nutzerentscheidung`**, kein stiller Default (Automatisierungs-Prinzip / Operanden-Gate).
- Rohbau vs. Fertig im Alt-Format nicht getrennt (M15) → Wert als **Rohbau** übernehmen, `finished_* = null`.

## 6. Umsetzungsvormerkung (NICHT in G1b-0)

Die Sturzkonsistenz-Prüfung (Regel 2, `opening.lintel_conflict`) und die explizite `sill`-Datums-Assertion sind **in einer späteren G1b-Umsetzung** in den `CanonicalBuildingModelValidator` aufzunehmen — **hier nicht**. G1b-0 dokumentiert nur den Vertrag. Der bestehende G1a-Validator prüft bereits `opening.exceeds_wall_height` und `opening.finished_exceeds_rough` konsistent mit diesem Vertrag; er ist **nicht** zu ändern.

## 7. Konsistenz mit G1a
Dieser Vertrag widerspricht dem gebauten G1a-Validator nicht: G1a prüft `sill < bottom || sill+rough_height > bottom+height → opening.exceeds_wall_height` (Regel 3) und `finished > rough → opening.finished_exceeds_rough` (Regel 1). Neu (für spätere Umsetzung) ist nur die **Sturzkonsistenz** (Regel 2) und die **`profile`-Formel** (Abschnitt 4). Damit ist A-3 vollständig als Vertrag entschieden.
