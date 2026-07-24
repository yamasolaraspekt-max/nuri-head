/**
 * Hausplaner — Zod-Validierung des SceneDocument (P0).
 *
 * Spiegel von scene.types.ts — bewusst STRENG:
 * - alle Längen ganze Millimeter (int) — die mm-Invariante fällt hier zuerst
 * - schemaVersion literal 1; unbekannte Version/Node-Typ ⇒ Laden verweigern (Kante)
 * - discriminated union über node.type
 * - Wand: Länge > 0 und ≥ Wanddicke (Kante „Mini-Wand")
 * - Öffnung: width/height > 0; die Wandbindungs-Regeln (offset+width ≤ Wandlänge)
 *   sind KONTEXT-Regeln und leben in validateSceneIntegrity (braucht Nachbar-Nodes)
 */
import { z } from 'zod/v4';

// mm-Invariante: ganze Millimeter, keine Floats in der Persistenz.
const mm = z.number().int();
const mmPos = z.number().int().positive();
const mmNonNeg = z.number().int().nonnegative();
const grad = z.number(); // Rotation/AngleSnap dürfen nicht-ganzzahlig sein (keine Länge)

const punkt2 = z.object({ x: mm, y: mm }).strict();
const punkt3 = z.object({ x: mm, y: mm, z: mm }).strict();
const isoDatum = z.string().min(4);

const baseNode = {
  id: z.string().min(1),
  levelId: z.string().min(1),
  name: z.string().optional(),
  visible: z.boolean(),
  locked: z.boolean(),
  tags: z.array(z.string()),
  createdAt: isoDatum,
  updatedAt: isoDatum,
};

export const wallNodeSchema = z
  .object({
    ...baseNode,
    type: z.literal('wall'),
    start: punkt2,
    end: punkt2,
    thickness: mmPos,
    height: mmPos,
    construction: z
      .object({
        materialId: z.string().optional(),
        insulationType: z.string().optional(),
        insulationThickness: mmNonNeg.optional(),
        uValue: z.number().positive().optional(),
      })
      .strict()
      .optional(),
  })
  .strict()
  .superRefine((w, ctx) => {
    const laenge = Math.hypot(w.end.x - w.start.x, w.end.y - w.start.y);
    if (laenge <= 0) {
      ctx.addIssue({ code: z.ZodIssueCode.custom, message: 'Wand hat Länge 0.' });
    } else if (laenge < w.thickness) {
      ctx.addIssue({ code: z.ZodIssueCode.custom, message: 'Wand kürzer als ihre Dicke.' });
    }
  });

export const openingNodeSchema = z
  .object({
    ...baseNode,
    type: z.enum(['window', 'door', 'opening']),
    hostWallId: z.string().min(1),
    offsetFromWallStart: mmNonNeg,
    width: mmPos,
    height: mmPos,
    sillHeight: mmNonNeg,
    catalogItemId: z.string().optional(),
    thermalProperties: z
      .object({
        uValue: z.number().positive().optional(),
        frameType: z.string().optional(),
        glazingType: z.string().optional(),
      })
      .strict()
      .optional(),
    produkt: z
      .object({
        typ: z.string().optional(),
        profilId: z.string().optional(),
        verglasungId: z.string().optional(),
        oeffnungsArt: z.enum(['fest', 'dreh', 'kipp', 'dreh-kipp']).optional(),
        rc: z.enum(['ohne', 'RC1N', 'RC2N', 'RC2', 'RC3']).optional(),
      })
      .strict()
      .optional(),
    clamped: z.boolean().optional(),
    anschlag: z.enum(['links', 'rechts']).optional(),
    oeffnung: z.enum(['innen', 'aussen']).optional(),
  })
  .strict();

export const objectNodeSchema = z
  .object({
    ...baseNode,
    type: z.literal('object'),
    objectType: z.enum([
      'radiator', 'heat_pump_indoor', 'heat_pump_outdoor', 'buffer_tank', 'hot_water_tank',
      'battery', 'inverter', 'wallbox', 'furniture', 'sanitary', 'stair',
    ]),
    catalogItemId: z.string().min(1),
    transform: z
      .object({
        position: punkt3,
        rotation: z.object({ x: grad, y: grad, z: grad }).strict(),
        scale: z.object({ x: z.number().positive(), y: z.number().positive(), z: z.number().positive() }).strict(),
      })
      .strict(),
    parameters: z.record(z.string(), z.union([z.string(), z.number(), z.boolean(), z.null()])),
  })
  .strict();

export const zoneNodeSchema = z
  .object({
    ...baseNode,
    type: z.literal('zone'),
    zoneType: z.enum(['room', 'underfloor_heating', 'pv_area', 'maintenance_area', 'sound_area', 'restricted_area']),
    polygon: z.array(punkt2).min(3),
    derived: z.boolean(),
    parameters: z.record(z.string(), z.union([z.string(), z.number(), z.boolean(), z.null()])),
  })
  .strict()
  .superRefine((zone, ctx) => {
    // P0: Räume sind AUSSCHLIESSLICH abgeleitet (Spec §1) — direkt editierte rooms sind ungültig.
    if (zone.zoneType === 'room' && !zone.derived) {
      ctx.addIssue({ code: z.ZodIssueCode.custom, message: "zoneType 'room' muss derived=true sein (P0)." });
    }
  });

export const routeNodeSchema = z
  .object({
    ...baseNode,
    type: z.literal('route'),
    routeType: z.enum(['heating_pipe', 'water_pipe', 'refrigerant_line', 'electrical_line', 'pv_dc_line', 'drainage']),
    points: z.array(punkt3).min(2),
    diameter: mmPos.optional(),
    crossSection: mmPos.optional(),
    insulationThickness: mmNonNeg.optional(),
  })
  .strict();

// zod-discriminatedUnion verträgt keine superRefine-gewrappten Members — deshalb z.union
// (Performance unkritisch bei P0-Szenengrößen; Diskriminierung übernimmt das type-Literal je Schema).
export const nodeSchema = z.union([
  wallNodeSchema,
  openingNodeSchema,
  objectNodeSchema,
  zoneNodeSchema,
  routeNodeSchema,
]);

export const levelSchema = z
  .object({
    id: z.string().min(1),
    name: z.string().min(1),
    elevation: mm,
    defaultWallHeight: mmPos,
    floorThickness: mmPos,
    sortOrder: z.number().int(),
  })
  .strict();

export const materialSchema = z
  .object({
    id: z.string().min(1),
    name: z.string().min(1),
    color: z.string().optional(),
    uValue: z.number().positive().optional(),
  })
  .strict();

// Dachaufbau (W-3a, additiv): stehender Aufbau auf der Dachfläche. x/y relativ (0..1); Maße ganze mm.
export const aufbauSchema = z
  .object({
    id: z.string().min(1),
    typ: z.enum([
      'chimney', 'window', 'vent', 'sat', 'lichtkuppel',
      'schleppgaube', 'trapezgaube', 'flachgaube', 'giebelgaube', 'spitzgaube',
    ]),
    x: z.number().min(0).max(1),
    y: z.number().min(0).max(1),
    breiteMm: mmPos,
    hoeheMm: mmPos,
    tiefeMm: mmPos,
    rotationGrad: grad.optional(),
    neigungGrad: z.number().min(0).max(89).optional(),
  })
  .strict();

// Anbau-Maße (W-3b 2a-2, additiv): L/T/U-Schenkel für die Verschneidungs-Engines. length/width Pflicht
// (u genügt das), lengthB/widthB optional (l/t). Ganze mm (mm-Invariante).
export const anbauMasseSchema = z
  .object({
    length: mmPos,
    width: mmPos,
    lengthB: mmPos.optional(),
    widthB: mmPos.optional(),
  })
  .strict();

// Dach (D-a, ▲D1): eigenes Schema, NICHT Teil der Node-Union (roofs[] ist eine eigene Sammlung).
// neigungGrad in [0, 89]: 0 = flach; < 90, damit cos > 0 (sichererCos, Kante 2).
export const roofNodeSchema = z
  .object({
    ...baseNode,
    type: z.literal('roof'),
    polygon: z.array(punkt2).min(3),
    // W-3b: additiv um rect/l/t/u-shape erweitert (die 4 Alt-Formen unverändert → Bestand bleibt gültig,
    // kein 422). Spiegelt domain/roofShape.ts (ROOF_SHAPES) — eine Wahrheit.
    roofType: z.enum(['sattel', 'walm', 'pult', 'flach', 'rect', 'l-shape', 't-shape', 'u-shape']),
    neigungGrad: z.number().min(0).max(89),
    firstAzimutGrad: z.number(),
    ueberstandMm: mmNonNeg,
    traufhoeheMm: mm,
    // W-3a: OPTIONAL ⇒ Bestands-Dächer ohne aufbauten bleiben gültig (additiv, kein 422).
    aufbauten: z.array(aufbauSchema).optional(),
    // W-3b 2a-2: OPTIONAL ⇒ rechteckige Formen + Bestand ohne anbau bleiben gültig (kein 422).
    anbau: anbauMasseSchema.optional(),
  })
  .strict();

// Decke (Feature A, additiv): eigenes Schema, NICHT Teil der Node-Union (ceilings[] eigene Sammlung).
export const ceilingOeffnungSchema = z.object({ polygon: z.array(punkt2).min(3) }).strict();
export const ceilingNodeSchema = z
  .object({
    ...baseNode,
    type: z.literal('ceiling'),
    polygon: z.array(punkt2).min(3),
    dickeMm: mmPos,
    oeffnungen: z.array(ceilingOeffnungSchema).optional(),
    schichten: z.array(z.object({ materialId: z.string().optional(), dickeMm: mmPos }).strict()).optional(),
  })
  .strict();

export const sceneDocumentSchema = z
  .object({
    id: z.string().min(1),
    projectId: z.number().int().positive(),
    schemaVersion: z.literal(2), // v2 (D-a: roofs[]); v1 wird per migriereSzene angehoben (s. u.)
    revision: z.number().int().positive(),
    units: z.literal('mm'),
    settings: z
      .object({ gridSize: mmPos, snapEnabled: z.boolean(), angleSnap: grad })
      .strict(),
    levels: z.array(levelSchema).min(1),
    nodes: z.array(nodeSchema),
    materials: z.array(materialSchema),
    roofs: z.array(roofNodeSchema),
    ceilings: z.array(ceilingNodeSchema).optional(), // additiv: Bestand ohne ceilings bleibt gültig (kein 422)
    metadata: z.object({ createdAt: isoDatum, updatedAt: isoDatum }).strict(),
  })
  .strict();

/**
 * Lade-Migration v1 → v2 (D-a, ▲D1). REIN ADDITIV und MINIMAL: setzt ausschließlich
 * `schemaVersion: 2` und ergänzt `roofs: []`, falls es fehlt. Jedes andere Feld (levels/nodes/
 * materials/settings/metadata/…) bleibt UNVERÄNDERT — kein stilles Umschreiben von Bestand.
 * v2-Dokumente ohne `roofs` (dürfte nicht vorkommen) werden ebenfalls tolerant auf `roofs: []` gehoben.
 * Unbekannte/andere Versionen werden NICHT verändert ⇒ das Schema lehnt sie ab (Lade-Verweigerung).
 *
 * Aufruf VOR sceneDocumentSchema.safeParse (siehe main.tsx).
 */
export function migriereSzene(roh: unknown): unknown {
  if (!roh || typeof roh !== 'object' || Array.isArray(roh)) {
    return roh;
  }
  const o = roh as Record<string, unknown>;
  const mitSammlungen = (): Record<string, unknown> => ({
    ...o,
    schemaVersion: 2,
    roofs: Array.isArray(o.roofs) ? o.roofs : [],
    ceilings: Array.isArray(o.ceilings) ? o.ceilings : [], // Feature A: additiv, Bestand → [] (kein 422)
  });
  if (o.schemaVersion === 1) {
    return mitSammlungen();
  }
  if (o.schemaVersion === 2 && (!Array.isArray(o.roofs) || !Array.isArray(o.ceilings))) {
    return mitSammlungen();
  }

  return roh;
}

/**
 * Kontext-Regeln über Node-Grenzen hinweg (nach dem Schema-Parse):
 * - Öffnung referenziert existierende Wand desselben Levels
 * - offset + width ≤ Wandlänge (sonst Kanten-Verhalten: ablehnen/klemmen laut Review)
 * - levelId existiert
 * Rückgabe: Liste menschlicher Fehlermeldungen (leer = konsistent).
 */
export function validateSceneIntegrity(doc: {
  levels: Array<{ id: string }>;
  nodes: Array<Record<string, unknown>>;
}): string[] {
  const fehler: string[] = [];
  const levelIds = new Set(doc.levels.map((l) => l.id));
  const walls = new Map<string, { sx: number; sy: number; ex: number; ey: number; levelId: string }>();

  for (const n of doc.nodes) {
    if (!levelIds.has(String(n.levelId))) {
      fehler.push(`Node ${n.id}: unbekanntes Level ${n.levelId}.`);
    }
    if (n.type === 'wall') {
      const s = n.start as { x: number; y: number };
      const e = n.end as { x: number; y: number };
      walls.set(String(n.id), { sx: s.x, sy: s.y, ex: e.x, ey: e.y, levelId: String(n.levelId) });
    }
  }

  for (const n of doc.nodes) {
    if (n.type === 'window' || n.type === 'door' || n.type === 'opening') {
      const wall = walls.get(String(n.hostWallId));
      if (!wall) {
        fehler.push(`Öffnung ${n.id}: Wirtswand ${n.hostWallId} existiert nicht.`);
        continue;
      }
      if (wall.levelId !== String(n.levelId)) {
        fehler.push(`Öffnung ${n.id}: liegt auf anderem Geschoss als ihre Wirtswand.`);
      }
      const laenge = Math.hypot(wall.ex - wall.sx, wall.ey - wall.sy);
      const offset = Number(n.offsetFromWallStart);
      const breite = Number(n.width);
      if (offset + breite > laenge) {
        fehler.push(`Öffnung ${n.id}: ragt über das Wandende (offset ${offset} + width ${breite} > ${Math.round(laenge)}).`);
      }
    }
  }

  return fehler;
}
