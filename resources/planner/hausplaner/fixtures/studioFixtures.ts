/**
 * Studio-Fixtures — deterministische, benannte SceneDocuments für die Sicht-Abnahme (`?fixture=<name>`).
 *
 * Zweck: Der Evaluator lädt eine feste Szene reproduzierbar (kein manuelles Klicken), kombiniert mit
 * `?capture=1` → `window.__hausplanerSnapshot3d()` → deterministischer 3D-Frame. Damit ist die visuelle
 * U-Platzierung (und künftig Decke-Slab/Navi) SELBST-BELEGBAR statt behauptet.
 *
 * Rein Daten (kein three/WebGL, keine Date.now → feste ISO-Zeit) ⇒ unit-testbar durch die volle
 * Pipeline (migriereSzene → Schema → Integrität). Additiv/hinter Flag: ohne `?fixture=` unverändert.
 */
import type { SceneDocument, WallNode, RoofNode, Level } from '../domain/scene.types';

/** Feste Zeit — Fixtures müssen bit-stabil sein (kein Date.now, das bräche Determinismus). */
const ISO = '2026-01-01T00:00:00.000Z';

const EG: Level = {
  id: 'lvl-eg',
  name: 'EG',
  elevation: 0,
  defaultWallHeight: 2800,
  floorThickness: 200,
  sortOrder: 0,
};

/** U-Grundriss (mm): Außenrechteck 12×10 m, Kerbe/Innenhof 5×4 m aus der oberen Kante. Ganze mm. */
const U_UMRISS: Array<{ x: number; y: number }> = [
  { x: 0, y: 0 },
  { x: 12000, y: 0 },
  { x: 12000, y: 10000 },
  { x: 8500, y: 10000 },
  { x: 8500, y: 6000 },
  { x: 3500, y: 6000 },
  { x: 3500, y: 10000 },
  { x: 0, y: 10000 },
];

/** Umriss → geschlossener Wandring (ein Segment je Kante). Reine Ableitung, ganze mm. */
function umrissZuWaenden(umriss: Array<{ x: number; y: number }>, levelId: string): WallNode[] {
  return umriss.map((p, i) => {
    const q = umriss[(i + 1) % umriss.length];
    return {
      id: `wall-${i}`,
      type: 'wall',
      levelId,
      visible: true,
      locked: false,
      tags: [],
      createdAt: ISO,
      updatedAt: ISO,
      start: { x: p.x, y: p.y },
      end: { x: q.x, y: q.y },
      thickness: 240,
      height: 2800,
    };
  });
}

/** U-Dach-Fixture: U-Grundriss + u-shape-Dach mit ALLEN vier Maßen (anbauZuEingabe liefert Eingabe, nicht null). */
function uDach(): SceneDocument {
  const dach: RoofNode = {
    id: 'roof-u',
    type: 'roof',
    levelId: EG.id,
    visible: true,
    locked: false,
    tags: [],
    createdAt: ISO,
    updatedAt: ISO,
    polygon: U_UMRISS,
    roofType: 'u-shape',
    neigungGrad: 35,
    firstAzimutGrad: 0,
    ueberstandMm: 500,
    traufhoeheMm: 2800,
    // Vier Maße: Außenrechteck 12×10 m, Innenhof/Kerbe 5×4 m (deckungsgleich mit U_UMRISS).
    anbau: { length: 12000, width: 10000, lengthB: 5000, widthB: 4000 },
  };
  return {
    id: 'fixture-u-dach',
    projectId: 1,
    schemaVersion: 2,
    revision: 1,
    units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [EG],
    nodes: umrissZuWaenden(U_UMRISS, EG.id),
    materials: [],
    roofs: [dach],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

/** Registry aller benannten Fixtures. Erweiterbar (decke-treppe, navi …) je Branch. */
export const STUDIO_FIXTURES: Record<string, () => SceneDocument> = {
  'u-dach': uDach,
};

/** Fixture-Name aus dem Query-String (`?fixture=u-dach`). Reine Funktion ⇒ testbar. */
export function fixtureNameAusSearch(search: string | null | undefined): string | null {
  try {
    return new URLSearchParams(search ?? '').get('fixture');
  } catch {
    return null;
  }
}

/** Benannte Fixture bauen; unbekannter/leerer Name ⇒ null (Boot fällt auf die eingebettete Szene zurück). */
export function ladeFixture(name: string | null | undefined): SceneDocument | null {
  if (!name) {
    return null;
  }
  const bauer = STUDIO_FIXTURES[name];
  return bauer ? bauer() : null;
}
