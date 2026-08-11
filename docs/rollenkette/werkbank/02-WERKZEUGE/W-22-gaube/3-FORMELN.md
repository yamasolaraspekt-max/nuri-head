# W-22 · Gaube — FORMELN

**Nur Nummern.** Die Formeln stehen in `../../01-MATHEMATIK/FORMELSAMMLUNG.md`.

## F-027 wurde GEPRÜFT, nicht übernommen — Thema ja, Formel nein

Das Register nennt **F-027 · Gaubenaufbau**. Gemessen, Merkmal für Merkmal:

| F-027 verlangt | im Hausplaner-Code |
|---|---|
| `rise = d · tan(φ)` | **teilweise** — `Math.tan` 6×, `rise` 15×, **aber mit anderem Operanden**: `riseG = halfW · tan(pG)` (Z.248), beim Kamin `(d/2) · tan(a) + 0.15` (Z.393) |
| **Körper: Quader**(b, h+Sockel, d) | **NEIN** — 0 Treffer auf „Quader"; das Modul liefert **Dreiecke und Linien** (`Dreieck`, `Linie`, Z.37-38) |
| Ausrichtung: **`atan2`**(fall_x, fall_z) | **NEIN** — 0 Treffer auf `Math.atan2`. Die Ausrichtung kommt aus `aufbauBasis()` (Z.76), einem **lokalen Dreibein**, nicht aus einer Drehung um y |
| Eigenneigung ohne Vorgabe → **15°** | **NEIN** — die Vorgaben hier sind `MIN_PULT_GRAD = 5` und `MIN_FLACH_GRAD = 2` (Z.102-103) |

**Belegstelle von F-027 laut Sammlung:** `dachdecker_pro_3d.tsx:1190–1210, :1255`. **Das ist M-01 auf
Yamas Desktop** — die Datei existiert (132 KB), **sie ist aber nicht der ticket-Code.**

> **Die Zuordnung stimmt, die Formel nicht.** Thematisch gehört F-027 hierher — das Modul setzt genau
> diese Gauben auf eine Dachfläche, und der Planner hat die Zuordnung als ✓ bestätigt. **Aber die
> Formel, so wie sie dasteht, beschreibt einen anderen Bau.** *Der trigonometrische Kern ist derselbe,
> weil Trigonometrie derselbe ist; die Konstruktion ist es nicht.* **Als Belegstelle für diesen Code
> taugt F-027 nicht — ihre eigene Belegstelle zeigt auf M-01, und das ist ehrlich.**

## F-031 trifft ebenfalls nicht zu

F-031 ist **CSG-Differenz** (Öffnung durch die Wand schneiden). Im Modul: **0 Treffer** auf
`csg`/`differenz`/`subtract`. *Hier wird nichts ausgeschnitten — es wird angeschlossen.*

## Was das Modul stattdessen benutzt

```text
Math.max 20 ×   Math.tan 6 ×   Math.hypot 4 ×   Math.min 3 ×
Math.abs  3 ×   Math.sin 2 ×   Math.cos  2 ×    Math.atan 1 ×
```

**Keine dieser Rechnungen hat eine eigene F-Nummer**, die auf dieses Werkzeug zeigt. *Gemeldet, nicht
erfunden.*
