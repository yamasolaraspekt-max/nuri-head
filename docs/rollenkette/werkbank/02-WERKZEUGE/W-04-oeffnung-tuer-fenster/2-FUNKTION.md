# W-04 · Öffnung (Tür/Fenster) — FUNKTION

## Zwei Kataloge, die verschiedene Fragen beantworten

| Modul | Frage | Umfang | Zeilen/Ausfuhren |
|---|---|---|---|
| `resources/planner/hausplaner/geometry/oeffnungsBauarten.ts` | **Wie sieht sie aus?** | 24 Fenster + 24 Tür = **48 Bauarten** | 75 Z, 5 |
| `resources/planner/hausplaner/geometry/oeffnungsTypen.ts` | **Wie groß ist sie?** | 5 Tür + 7 Fenster = **12 Vorlagen** | 49 Z, 7 |

**Die Trennung steht im Code selbst:** *„Premium-Bauarten (Yamas SVG-Sätze) — Icon-Auswahl im Panel.
Getrennt von den Zeichen-Vorlagen in `oeffnungsTypen.ts` (Standardmaße)."* (`oeffnungsBauarten.ts:1-2`)

### Die Ausfuhren mit Fundstelle

**Bauarten** — `OeffnungsBauart` (5) · `FENSTER_BAUARTEN` (16) · `TUER_BAUARTEN` (43) ·
`fensterBauartNach()` (70) · `tuerBauartNach()` (73)

**Typen** — `TuerTyp` (7) · `FensterTyp` (8) · `TypVorlage` (10) · `TUER_TYPEN` (22) ·
`FENSTER_TYPEN` (31) · `tuerTyp()` (42) · `fensterTyp()` (47)

## Die Türgeometrie steht NICHT in diesem Blatt

Wie ein Türblatt aufgeht — Anschlag links/rechts, Öffnung innen/außen, der Schwenkbogen — **gehört
W-02 und wird dort beschrieben**:

```text
resources/planner/hausplaner/geometry/wallGeometry.ts:267   TuerAnschlag  'links' | 'rechts'
resources/planner/hausplaner/geometry/wallGeometry.ts:268   TuerOeffnung  'innen' | 'aussen'
resources/planner/hausplaner/geometry/wallGeometry.ts:270   TuerBlattGeometrie
resources/planner/hausplaner/geometry/wallGeometry.ts:291   tuerBlattGeometrie()
```

*Ein zweites Mal beschrieben wäre es eine zweite Wahrheit — und die driftet.*

## Was nach der Auswahl passiert

**Die Vorlage ist ein Startwert, kein Gesetz.** Der Code sagt es selbst: *„Nach dem Setzen sind die
Maße frei überschreibbar (Maßkette/Panel)."* (`oeffnungsTypen.ts:2-3`)
