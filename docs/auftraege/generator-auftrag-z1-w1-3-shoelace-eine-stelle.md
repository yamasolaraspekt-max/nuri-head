# Z1-W1-3 · Eine Formel, eine Stelle: polygonM2-Kopie zusammenführen

```yaml
zustand: ENTWURF
basis_sha: 11f7c4c3
herkunft: Befund R-1 (docs/backlog/inventur-2026-08-20-z1.md) · Fahrplan-Posten 1.3
baut: generator (Agent frontend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
kopplung: gleiche Datei wie Z1-W1-2 — läuft NACH W1-2, eigener Commit
```

## Ziel
`geometry/dachGeometrie.ts` benutzt `polygonFlaecheM2` aus `polygonFlaeche.ts`; die private
Kopie `polygonM2` entfällt. Der Satz in `kontur.ts:21-23` („an genau einer Stelle") wird wieder wahr.

## Ist-Beleg
`dachGeometrie.ts:39-45` private Kopie OHNE `Number.isFinite`-Schutz; `polygonFlaeche.ts:31-48`
mit Schutz und Zusage „niemals NaN" (:29). Live-Kette `szene.ts:35` → `dachMesh.ts:12` →
`pruefeRechteckigeKontur` → `polygonM2`. Einheiten-Falle (Synthese-Auflage): `polygonM2` nimmt
**mm**, `polygonFlaecheM2` ist im Kopf auf **Meter** verpflichtet (`polygonFlaeche.ts:11-13`) —
exakt die W-08-Falle „m gegen mm".

## Scope · Dateien
- `geometry/dachGeometrie.ts` (Import statt Kopie; Umrechnung **erkennbar** am Aufrufort oder
  Vertragstext von `polygonFlaeche.ts` auf beide Einheiten-Fälle gezogen — eines von beiden,
  im Bericht benannt)
- `geometry/kontur.ts:21-23` (Satz wahr machen oder berichtigen — nicht stehen lassen)
- Tests.
**Nicht-Ziele:** kein Verhaltensumbau von `pruefeRechteckigeKontur` über den Formeltausch hinaus;
die anderen Shoelace-Vorkommen (K3-Vorbau-Module, m-basiert) sind NICHT Gegenstand.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Formelmuster `a.x * b.y` existiert in `geometry/` nur noch in `polygonFlaeche.ts` | Zusammenführung | *n.U.* | grep-Rohausgabe |
| B: NaN-Verhalten von `pruefeRechteckigeKontur` VORHER und NACHHER gemessen und gegenübergestellt (kein Finder hat die Fallrichtung gemessen — sie wird nicht angenommen) | Messpflicht | *n.U.* | beide Rohausgaben |
| C: Einheiten-Vertrag aufgelöst: Umrechnung erkennbar ODER Vertragstext gezogen | Vertrag | *n.U.* | Zitat |
| D: `kontur.ts:21-23` stimmt wieder mit dem Bestand überein | Doku | *n.U.* | Zitat |
| E: Suite grün, `tsc:hausplaner` exit 0 | Schutz | *n.U.* | Zähler |

**P1-Kriterium A ist vor dem Bau wirksam rot** (heute 2 Fundstellen der Formel: `:39-45` + Modul).

## Rückweg
Ein Commit, zurückdrehbar; kein Schema, keine Daten.
