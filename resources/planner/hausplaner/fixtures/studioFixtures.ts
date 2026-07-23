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
import type { SceneDocument, WallNode, CeilingNode, ObjectNode, Level } from '../domain/scene.types';
import { treppeZuParametern } from '../geometry/treppeObjekt';

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

/** Rechteck-Grundriss (mm) für die Decke-Slab-Sicht: 10×8 m. */
const RECHTECK_UMRISS: Array<{ x: number; y: number }> = [
  { x: 0, y: 0 }, { x: 10000, y: 0 }, { x: 10000, y: 8000 }, { x: 0, y: 8000 },
];

/**
 * Decke-Slab-Fixture: Rechteck-Grundriss + Treppe + Geschossdecke mit Treppenauge-Durchbruch. Der
 * Durchbruch ist als Loch-Polygon GESETZT (Fixtures umgehen den ADD_CEILING-Reducer, der es sonst aus
 * der Lauflinie ableitet) — Rechteck um den Lauf (± halbe Laufbreite), deckungsgleich zur Reducer-Regel.
 */
function deckeTreppe(): SceneDocument {
  const treppe: ObjectNode = {
    id: 'stair-1', type: 'object', objectType: 'stair', levelId: EG.id,
    visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO, catalogItemId: 'stair',
    transform: { position: { x: 0, y: 0, z: 0 }, rotation: { x: 0, y: 0, z: 0 }, scale: { x: 1, y: 1, z: 1 } },
    parameters: treppeZuParametern({ startX: 2000, startY: 2000, endX: 5000, endY: 2000, laufbreite: 1000, geschosshoehe: 2800, bereich: 'wohnung' }),
  };
  const decke: CeilingNode = {
    id: 'ceiling-1', type: 'ceiling', levelId: EG.id,
    visible: true, locked: false, tags: [], createdAt: ISO, updatedAt: ISO,
    polygon: RECHTECK_UMRISS, dickeMm: 200,
    // Treppenauge: Rechteck um die Lauflinie (2000..5000) ± laufbreite/2 (1500..2500).
    oeffnungen: [{ polygon: [{ x: 2000, y: 1500 }, { x: 5000, y: 1500 }, { x: 5000, y: 2500 }, { x: 2000, y: 2500 }] }],
  };
  return {
    id: 'fixture-decke-treppe',
    projectId: 1,
    schemaVersion: 2,
    revision: 1,
    units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [EG],
    nodes: [...umrissZuWaenden(RECHTECK_UMRISS, EG.id), treppe],
    materials: [],
    roofs: [],
    ceilings: [decke],
    metadata: { createdAt: ISO, updatedAt: ISO },
  };
}

/** Registry aller benannten Fixtures. Decke-Branch: nur decke-treppe (u-dach lebt auf der Dach-Linie,
 *  die das anbau/u-shape-Roofmodell trägt — kein Fixture-Import aus einer fremden Modell-Welt). */
export const STUDIO_FIXTURES: Record<string, () => SceneDocument> = {
  'decke-treppe': deckeTreppe,
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
