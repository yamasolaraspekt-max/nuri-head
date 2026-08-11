# W-22 · Gaube — BEDIENUNG

## Kein eigenes Werkzeug

**0 Treffer** auf `gaube`, `kamin`, `aufbau` in der Werkzeugregistrierung. **Dieselbe Lage wie W-01,
W-05 und W-21:** die Rechenschicht steht, die Werkzeugschicht fehlt. *Benannt, nicht gelöst.*

## Was der Anwender angibt

| Größe | Anmerkung |
|---|---|
| **Bauart** | `schleppgaube` · `flachgaube` · `trapezgaube` · `giebelgaube` · `spitzgaube` · `chimney` |
| **Position** `x`, `y` | **relativ, 0…1 auf der Fläche** (Z.47) — nicht in Millimetern |
| Breite, Höhe, Tiefe | die Höhe wird ggf. **geklemmt**, siehe `7-GRENZEN` |
| Eigenneigung | untere Schranken: **5°** Pult, **2°** Flach |

**Die Position ist relativ.** *Eine Gaube „bei 0,5 / 0,3" sitzt auf jeder Dachgröße an der gleichen
Stelle — und wandert mit, wenn das Dach sich ändert.*

## Was er zurückbekommt

Nicht nur Geometrie, sondern eine **Ampel mit Kernbefund im Klartext**:

```text
gruen  "«Bauart»: Geometrie im Toleranzband, Anschluss auf Hauptdach, kein Vertex ueber First."
sonst  "«Bauart»: AK-Nummer verletzt (ist=…, soll …)."
```

*Aus `resources/planner/hausplaner/geometry/gaubeGeometrie.ts:493-495`.* **Der Befund nennt das verletzte Kriterium mit Ist
und Soll** — nicht „ungültig".
