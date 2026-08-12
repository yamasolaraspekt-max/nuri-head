# W-07 · CODE

> **Dieses Blatt war bis 12.08. die unveränderte Vorlage** — bis auf die Überschrift byte-identisch
> mit zwölf anderen Werkzeugen. *Es zählte als „gefüllt", weil die Platzhalter-Messung nach `<…>`
> sucht und die Vorlage keine enthält. Belegmethode der Berichtigung: `md5` des Inhalts ab Zeile 2.*

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 1 Domäne | `domain/scene.types.ts:316` | `RoofNode` — `type: 'roof'`, Polygon, Dachtyp, Neigung, `firstAzimutGrad`, Überstand, Traufhöhe |
| 2 Geometrie | `geometry/dachGeometrie.ts` (153 Z.) | **Kern**: Rechteckigkeits-Prüfung + belastbare Flächen/Azimute |
| | `geometry/dachformVorlagen.ts` (2.399 Z.) | Dachform-Katalog samt `abbundhinweis` |
| | `geometry/dachAusschnitt.ts` (510 Z.) | Ausschnitte, `istAchsenRechteck` — **anderer** Rechteckbegriff, siehe Warnung unten |
| | `geometry/dachVerschneidung.ts` (205 Z.) | Verschneidung mehrerer Dachkörper |
| | `geometry/dachUForm.ts` (126 Z.) · `dachWerte.ts` (103) · `dachOeffnung.ts` (96) · `dachVorlage.ts` (34) | U-Form, Kennwerte, Öffnungen, Vorlage |
| 3 Werkzeug | **kein Modul unter `app/tools/`** (gemessen: 0 Treffer) | die Führung liegt in `app/HausplanerApp.tsx:964-1002` |
| 4 Darstellung | `renderers/three-d/dachMesh.ts` · `dachAufbautenMesh.ts` · `nichtDarstellbar.ts` | Mesh-Aufbau; Sonderfall „nicht darstellbar" |
| 5 Oberfläche | `app/HausplanerApp.tsx:331` (`dachAbsage`) | Fußleiste: Absagegrund und Näherungshinweis |

> **Summe der Rechenschicht: 3.626 Zeilen in acht Modulen.** *Das Werkzeug ist keine kleine Funktion,
> und `dachGeometrie.ts` ist mit 153 Zeilen das kleinste — aber das entscheidende.*

## Schnittstelle

```ts
// geometry/dachGeometrie.ts
export interface DachFlaeche {          // :15
  flaeche_m2: number;
  azimut_grad: number | null;           // null = horizontal (Flachdach)
  neigung_grad: number;
  first_laenge_mm: number;
}

export interface DachKontur {           // :49
  laengeMm: number;                     // Ausdehnung entlang der Firstachse (u)
  spannMm: number;                      // quer dazu (v)
  cx: number; cy: number;               // Schwerpunkt in mm — fuer den Mesh-Aufbau
}

export class DachGeometrieUngueltig extends Error {   // :22
  readonly grund: string;
}

export function pruefeRechteckigeKontur(poly: readonly P[], azGrad: number): DachKontur;  // :64
export function dachFlaechen(roof: RoofNode): DachFlaeche[];                              // :105
```

## Kernstelle

**Die eine Stelle, auf die es ankommt** — `geometry/dachGeometrie.ts:88` (Zeilennummer aus dem
Ausschnitt `:64-104`):

```ts
if (bboxM2 <= 0 || Math.abs(konturM2 - bboxM2) / bboxM2 > 0.01) {
  throw new DachGeometrieUngueltig(/* … */);
}
```

**Ein Prozent Abweichung zwischen Konturfläche und Bounding-Box — mehr wird abgelehnt.** *Das ist
die V1-Grenze („Kante 1") aus dem Dateikopf: **nur rechteckige Traufkonturen, alles andere wird
abgelehnt — nie ein stilles Falschdach.** Und die Prüfung liefert im Erfolgsfall gleich die Maße,
damit **belastbare Fläche und Render-Mesh nie auseinanderlaufen**.*

> **Warnung, die im Code selbst steht und hierher gehört** ([HausplanerApp.tsx:982-984](../../../../../../resources/planner/hausplaner/app/HausplanerApp.tsx#L982)):
> *„`istAchsenRechteck` aus `dachAusschnitt.ts` wäre der falsche Zeuge — es weist ein Rechteck mit
> kollinearem Zwischenpunkt ab, das der Renderer klaglos zeichnet."* **Es gibt zwei
> Rechteckbegriffe im Haus; nur einer ist der Zeuge des Renderers.**

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| `domain/scene.types` (`RoofNode`) | Eingabetyp | **ja** — reiner Typ-Import, kein Kreis |
| `sichererCos` (▲D2) | Divisionsschutz bei 89°, wird **wiederverwendet, nicht neu erfunden** (Dateikopf `:9`) | **ja** |
| `store/hausplanerStore` → `Historie` | `ADD_ROOF` und dessen Rücknahme | **ja** — nur Schicht 3 ruft den Store, die Geometrie kennt ihn nicht |
| **kein** `three`, **kein** React in Schicht 2 | Dateikopf `:2`: „reine Funktionen (kein three/React)" | **ja** — gemessen, der Kern ist rendererfrei |

## Fachliche Grundlage, wörtlich aus dem Dateikopf

```text
dachGeometrie.ts:3   Grundlage: docs/hausplaner/dach-andock-spec.md §1 (▲D2/▲D4), §3 (Kanten 1–3),
                     §4.1/§4.2 (Abnahme).
```
