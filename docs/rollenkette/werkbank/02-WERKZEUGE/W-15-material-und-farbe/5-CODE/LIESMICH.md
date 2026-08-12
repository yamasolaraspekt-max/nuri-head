# W-15 · CODE

## NOCH NICHT GEBAUT — und das ist der Kern dieses Blattes

**Es gibt keinen Code für W-15. Es gibt einen VERTRAG.** *Der Unterschied ist der wichtigste Satz
der ganzen Mappe: **ein `5-CODE`, das einen Vertrag wie eine Implementierung liest, wäre die
schlimmste Form dieses Blattes.***

```text
Vertrag        werkzeugVertrag.ts:874  material-aufnehmen  -> services.material.execute('paint', input)
               werkzeugVertrag.ts:886  material-zuweisen   -> services.material.execute('material', input)
               werkzeugVertrag.ts:898  textur              -> services.material.execute('texture', input)

Implementierung  grep -rn 'services\.material' resources/  ->  3 Treffer, ALLE im Vertrag selbst
                 dieselbe Suche OHNE den Vertrag           ->  0 Treffer
```

> **`services.material` existiert im Repo nicht.** *Der Vertrag nennt die Dienstmethode dreimal, und
> dreimal zeigt sie auf etwas, das niemand gebaut hat.* **Stufe 2 kann deshalb nicht „anbinden", sie
> muss bauen — und das ist im Auftrag ausdrücklich Nicht-Ziel dieser Stufe.**

## Wo der Code leben WIRD

| Schicht | Datei im Repo | Zweck | Zustand |
|---|---|---|---|
| 1 Domäne | `resources/planner/hausplaner/domain/…` | Zuweisung am Objekt, Revision | **fehlt** |
| 2 Geometrie | — | **keine** — W-15 rechnet nicht (`3-FORMELN.md`) | entfällt |
| 3 Werkzeug | `resources/planner/hausplaner/app/tools/…` | `services.material.execute(...)` | **fehlt** |
| 4 Darstellung | `resources/planner/hausplaner/renderers/…` | `renderer.refreshAffectedObjects` | **fehlt** |
| 5 Oberfläche | `resources/planner/hausplaner/ui/…` | Auswahl, Vorschau, Absagen | **fehlt** |

> **Der Code steht im Repo, nicht in diesem Ordner.** Hier liegen nur Schnittstellenbeschreibung,
> Ablaufskizze und — wo nötig — ein kurzer Auszug der Kernstelle mit Zeilennummer.

## Schnittstelle — aus dem Vertrag abgeleitet, nicht aus Code abgelesen

```ts
// VORGABE fuer Stufe 2. Die Feldnamen sind zitiert (werkzeugVertrag.ts:889-890),
// die TypeScript-Form ist Entwurf.
interface MaterialZuweisenEingabe {
  objectIds: string[];          // :889
  surfaceSlot: string;          // :889
  surfaceMaterialId: string;    // :889
  variantId: string;            // :889
}
interface MaterialZuweisenErgebnis {
  materialAssignmentIds: string[];   // :890
}
```

## Kernstelle — die einzige, die es heute gibt

```ts
// resources/planner/hausplaner/app/tools/werkzeugVertrag.ts:886-895
{
  werkzeugId: 'material-zuweisen',
  commandId: 'MaterialCommand',
  familie: 'assign-or-calculate',
  eingaben: ['objectIds', 'surfaceSlot', 'surfaceMaterialId', 'variantId'],
  ergebnisse: ['materialAssignmentIds'],
  vorbedingungen: ['project.open', 'selection.count >= 1', 'permission.edit'],
  seiteneffekte: ['model.revision.increment', 'autosave.markDirty',
                  'dependentResults.invalidate', 'renderer.refreshAffectedObjects'],
  umkehrbar: true,
  protokollpflichtig: true,
  dienstMethode: 'services.material.execute(\'material\', input)',
}
```

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **W-13 Auswahl und Griffe** | `selection.count >= 1` ist Vorbedingung aller drei Einträge | **ja** — W-13 steht auf `BESCHRIEBEN` (`REGISTER.md:37`), deshalb ist W-15 überhaupt frei; W-15s eigene Zeile ist `REGISTER.md:68` |
| Projektzustand (`project.open`) | Vorbedingung `:891` | ja — kein Kreis: W-15 liest den Zustand, ändert ihn nicht |
| Rechteprüfung (`permission.edit`) | Vorbedingung `:891` | ja — einseitig |
| **`services.material`** | die im Vertrag genannte Dienstmethode | **NEIN — existiert nicht.** Kein Kreis, sondern eine Lücke |
| Materialkatalog | woher `surfaceMaterialId` und `variantId` kommen | **ungemessen** — im Repo kein Katalog gefunden; offene Frage für Stufe 2 |
