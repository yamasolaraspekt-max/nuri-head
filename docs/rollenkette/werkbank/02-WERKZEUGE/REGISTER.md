# REGISTER DER WERKZEUGE

> Eine Zeile je Werkzeug. **Hier steht, was es gibt und woran es hängt** —
> nicht, wie weit es ist. Der Auftragszustand steht in `docs/STATUS.md`.
>
> Reifegrad: `LEER` (nur Ordner) · `BESCHRIEBEN` (Blätter gefüllt) ·
> `GEBAUT` (Code vorhanden) · `GEPRÜFT` (Kriterien grün, Grenzen belegt)

---

## Stufe 1 — Fundament (alles andere hängt daran)

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-01 | Raster und Fang | LEER | — | F-040, F-041, F-001, F-003, F-004 |
| W-02 | Wand zeichnen | LEER | W-01 | F-001, F-002, F-030 |
| W-13 | Auswahl und Griffe | LEER | W-02 | F-012, F-003 |
| W-12 | Ansicht und Kamera | LEER | — | F-032 |

## Stufe 2 — Grundriss

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-03 | Wand bearbeiten | LEER | W-02, W-13 | F-003, F-004, F-030 |
| W-04 | Öffnung (Tür/Fenster) | LEER | W-02 | F-003, F-031 |
| W-05 | Raum erkennen | LEER | W-02 | F-010, F-011, F-012, F-013 |
| W-10 | Decke und Boden | LEER | W-05 | F-011, F-030 |
| W-16 | Grundriss unterlegen | LEER | W-12 | F-032 |

## Stufe 3 — Aufbau in der Höhe

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-06 | Geschoss verwalten | LEER | W-02 | F-032 |
| W-07 | **Dach aus Kontur** | **BESCHRIEBEN** | W-05, W-06 | F-010, F-013, **F-014, F-025, F-026**, F-020, F-021, F-022 |
| W-08 | Dachfläche messen | LEER | W-07 | F-011, F-023, F-024 |
| W-09 | Treppe | LEER | W-06 | F-001, F-030 |
| W-21 | **Sparren und Lattung** | LEER | W-07 | F-001, F-030 · Quelle M-01/M-02 |
| W-22 | **Gaube** | LEER | W-07 | **F-027**, F-031 |
| W-23 | **Deckung und Material** | LEER | W-07, W-08 | **F-050** |

## Stufe 4 — Darstellung und Ausgabe

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-11 | Maß und Bemaßung | LEER | W-13 | F-001, F-002, F-003 |
| W-14 | Kopieren/Spiegeln/Drehen | LEER | W-13 | F-032 |
| W-15 | Material und Farbe | LEER | W-13 | — |
| W-17 | Export und Speichern | LEER | alle | — |

## Stufe 5 — Prüfung und Auswertung

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-18 | Topologie prüfen | LEER | W-02, W-05 | F-004, F-013 |
| W-19 | Sonne und Verschattung | LEER | W-07, W-08 | F-024 |
| W-20 | Stückliste und Mengen | LEER | W-05, W-08 | F-011, F-023 |

---

## Abhängigkeitskette — die Reihenfolge, in der gebaut werden muss

```
W-01 Fang ─┬─► W-02 Wand ─┬─► W-03 bearbeiten
           │              ├─► W-04 Öffnung
           │              ├─► W-05 Raum ──┬─► W-10 Decke
           │              │               └─► W-20 Mengen
           │              └─► W-06 Geschoss ─► W-07 DACH ─┬─► W-08 messen
           │                                  │           └─► W-19 Sonne
           │                                  └─► W-09 Treppe
           └─► W-13 Auswahl ─┬─► W-11 Maß
                             ├─► W-14 Kopieren
                             └─► W-15 Material
W-12 Kamera (quer zu allem)
W-17 Speichern (braucht alles)
W-18 Prüfung (braucht W-02, W-05)
```

> **Was diese Kette bedeutet:** W-01 zuerst. Ohne verlässlichen Fang ist jede
> Wand ungenau, und jede Ungenauigkeit vererbt sich nach oben — bis das Dach
> nicht mehr schließt. **Ein wackliger Fang ist kein Schönheitsfehler,
> er ist ein Fundamentfehler.**

---

## Was schon im Repo existiert

Beim Anlegen dieses Registers gemessen — **noch nicht in die Werkzeugordner
eingearbeitet**, das ist der nächste Schritt:

| Fundstelle im Repo | Gehört zu |
|---|---|
| `resources/planner/hausplaner/geometry/dachGeometrie.ts` | W-07 |
| `resources/planner/hausplaner/renderers/three-d/szene.ts` | W-07, Schicht 4 |
| `resources/planner/hausplaner/app/tools/workspaceIds.ts` | Werkzeugregistrierung |

## Was aus Yamas eigenem Bestand kommt

Ausgewertet am 07.08.2026 — Einzelheiten in `../05-MATERIALQUELLEN/BESTAND-YAMA.md`.

| Fundstelle | Gehört zu | Was daraus wurde |
|---|---|---|
| `dachdecker_pro_3d.tsx:139-147` | W-07 | **F-014** Erkennung einspringender Ecken |
| `dachdecker_pro_3d.tsx:153-158` | W-07 | **F-025** Grat · Kehle · Ortgang |
| `dachdecker_pro_3d.tsx:101-131, 1134-1137` | W-07 | **F-026** Dach über Grundform — **kann L und T** |
| `dachdecker_pro_3d.tsx:1190-1255` | W-22 | **F-027** Gaubenaufbau |
| `dachdecker_pro_3d.tsx:186-191` | W-23 | **F-050** Materialkennwerte |
| `dachdecker_pro_3d.tsx:194-198` | W-20 | **F-051** Zeitwerte je Gewerk |
| `profi_holzbau_solar_cad.tsx` | W-21 | noch auszuwerten |
| `solarmaster_konstruktion.tsx` | W-19 | noch auszuwerten |
| Pfettendach-Fachbilder | W-21 | Bauteilbenennung |

> **Der wichtigste Befund:** `F-026` kann **L- und T-Grundrisse** — genau der Fall,
> an dem Auftrag Z-07 scheiterte. Der Code liegt auf Yamas Schreibtisch und läuft.
> Vor jedem weiteren Bau an W-07 ist zu entscheiden, welcher der beiden Dachwege
> zuerst kommt. Der Vergleich steht in der Formelsammlung, Gruppe 6.
