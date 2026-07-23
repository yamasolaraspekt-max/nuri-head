/**
 * Hausplaner — Command-System (P0). Renderer/Werkzeuge schreiben NIE direkt in die
 * Szene; jede Änderung ist ein Command. Der Store führt Commands über Immer
 * `produceWithPatches` aus — die inversen Patches sind die Undo-Historie.
 */
import type { SceneNode, RoofNode, RoofAufbau, Level } from './scene.types';

export type HausplanerCommand =
  | { type: 'ADD_NODE'; node: SceneNode }
  | { type: 'REMOVE_NODE'; nodeId: string }
  // Geschoss-Commands (P2b-5): operieren auf SceneDocument.levels. Undo/Redo/409 gelten
  // automatisch, weil levels Teil des Immer-Drafts sind. Löschen NIE stillschweigend Nodes/Dächer
  // eines Levels — ein nicht-leeres Level wird abgelehnt (level_nicht_leer).
  | { type: 'ADD_LEVEL'; level: Level }
  | { type: 'UPDATE_LEVEL'; levelId: string; changes: Partial<Level> }
  | { type: 'REMOVE_LEVEL'; levelId: string }
  // Dach-Commands (D-a): operieren auf SceneDocument.roofs (getrennt von den Node-Commands).
  // Undo/Redo/409 gelten automatisch, weil roofs Teil des Immer-Drafts sind.
  | { type: 'ADD_ROOF'; roof: RoofNode }
  | { type: 'UPDATE_ROOF'; roofId: string; changes: Record<string, unknown> }
  | { type: 'REMOVE_ROOF'; roofId: string }
  // Dachaufbau-Commands (W-3a): operieren auf RoofNode.aufbauten (Teil des Immer-Drafts ⇒ Undo/Redo/409
  // automatisch). Additiv — Dächer ohne aufbauten bleiben unberührt.
  | { type: 'ADD_ROOF_AUFBAU'; roofId: string; aufbau: RoofAufbau }
  | { type: 'REMOVE_ROOF_AUFBAU'; roofId: string; aufbauId: string }
  | { type: 'UPDATE_ROOF_AUFBAU'; roofId: string; aufbauId: string; changes: Record<string, unknown> }
  | {
      type: 'MOVE_NODE';
      nodeId: string;
      /** Wand: neue Endpunkte; Objekt/Route: Verschiebung der Position (mm, ganzzahlig). */
      position:
        | { start: { x: number; y: number }; end: { x: number; y: number } }
        | { x: number; y: number; z: number };
    }
  | {
      type: 'UPDATE_NODE';
      nodeId: string;
      /** Teil-Änderungen; Geometrie-Felder durchlaufen dieselben Regeln wie MOVE_NODE. */
      changes: Record<string, unknown>;
    }
  | { type: 'SET_NODES_SICHTBAR'; nodeIds: string[]; sichtbar: boolean }
  | { type: 'SET_NODES_GESPERRT'; nodeIds: string[]; gesperrt: boolean }
  | { type: 'UPDATE_SETTINGS'; changes: Partial<{ gridSize: number; snapEnabled: boolean; angleSnap: number }> };

export type AblehnungsGrund =
  | 'wand_zu_kurz'
  | 'oeffnung_passt_nicht'
  | 'wirtswand_fehlt'
  | 'node_unbekannt'
  | 'level_unbekannt'
  | 'level_existiert'
  | 'level_letztes'
  | 'level_nicht_leer'
  | 'nicht_ganzzahlig'
  | 'dach_pro_level_vorhanden'
  | 'dach_unbekannt'
  | 'aufbau_unbekannt';

/**
 * Definierter Fehlerzustand: Command verletzt eine Regel — Szene bleibt unverändert.
 * (Bewusst OHNE TS-Parameter-Property: node --experimental-strip-types trägt das nicht.)
 */
export class CommandAbgelehnt extends Error {
  readonly grund: AblehnungsGrund;

  constructor(message: string, grund: AblehnungsGrund) {
    super(message);
    this.name = 'CommandAbgelehnt';
    this.grund = grund;
  }
}
