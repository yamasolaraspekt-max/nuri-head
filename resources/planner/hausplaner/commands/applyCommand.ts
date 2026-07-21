/**
 * Hausplaner — Command-Anwendung (P0). REINE Funktion auf einem Immer-Draft;
 * der Store ruft sie über produceWithPatches (Undo = inverse Patches).
 *
 * Fachregeln (Spec §1/§4 + Schema-Review-Entscheide):
 * - Wand löschen ⇒ ihre Öffnungen werden IM SELBEN Command mit gelöscht (EIN Undo für beides).
 * - Wand-Geometrie ändern ⇒ Öffnungen werden neu eingepasst:
 *     passt die Öffnung nicht mehr ganz ⇒ KLEMMEN ans Wandende + clamped=true (Entscheid a);
 *     ist die Öffnung breiter als die neue Wand ⇒ Command ABLEHNEN (nie stilles Löschen).
 * - mm-Invariante: alle Geometriewerte ganzzahlig, sonst Ablehnung.
 * - Ablehnung = CommandAbgelehnt-Throw VOR jeder Mutation relevanter Werte; der Store
 *   verwirft den Draft, die Szene bleibt unverändert.
 */
import type { SceneDocument, SceneNode, WallNode, OpeningNode, RoofNode } from '../domain/scene.types';
import { CommandAbgelehnt, type HausplanerCommand } from '../domain/commands.types';
import { wandLaenge } from '../geometry/wallGeometry';

function nodeOderFehler(draft: SceneDocument, nodeId: string): SceneNode {
  const node = draft.nodes.find((n) => n.id === nodeId);
  if (!node) {
    throw new CommandAbgelehnt(`Node ${nodeId} existiert nicht.`, 'node_unbekannt');
  }

  return node;
}

function pruefeGanzzahlig(werte: number[], kontext: string): void {
  if (werte.some((w) => !Number.isInteger(w))) {
    throw new CommandAbgelehnt(`${kontext}: Geometrie muss in ganzen Millimetern vorliegen.`, 'nicht_ganzzahlig');
  }
}

function istOeffnung(n: SceneNode): n is OpeningNode {
  return n.type === 'window' || n.type === 'door' || n.type === 'opening';
}

/** Öffnungen einer Wand nach Geometrieänderung einpassen (Entscheid a: klemmen, sonst ablehnen). */
function passeOeffnungenEin(draft: SceneDocument, wand: WallNode, jetzt: string): void {
  const laenge = wandLaenge(wand.start, wand.end);

  for (const node of draft.nodes) {
    if (!istOeffnung(node) || node.hostWallId !== wand.id) {
      continue;
    }
    if (node.width > laenge) {
      throw new CommandAbgelehnt(
        `Öffnung ${node.id} (${node.width} mm) passt nicht mehr auf die Wand (${Math.floor(laenge)} mm) — Änderung abgelehnt.`,
        'oeffnung_passt_nicht',
      );
    }
    if (node.offsetFromWallStart + node.width > laenge) {
      node.offsetFromWallStart = Math.floor(laenge) - node.width;
      node.clamped = true;
      node.updatedAt = jetzt;
    }
  }
}

/** Dach-Geometrie: mm-Invariante über Traufkontur + Überstand + Traufhöhe (D-a). */
function pruefeDachGanzzahlig(roof: RoofNode): void {
  const koords = roof.polygon.flatMap((p) => [p.x, p.y]);
  pruefeGanzzahlig([...koords, roof.ueberstandMm, roof.traufhoeheMm], 'Dach');
}

function pruefeDachProLevel(draft: SceneDocument, roof: RoofNode): void {
  if (draft.roofs.some((r) => r.id !== roof.id && r.levelId === roof.levelId)) {
    throw new CommandAbgelehnt(
      `Level ${roof.levelId} hat bereits ein Dach (max. 1 je Level).`,
      'dach_pro_level_vorhanden',
    );
  }
}

function pruefeNeueOeffnung(draft: SceneDocument, oeffnung: OpeningNode): void {
  const wand = draft.nodes.find((n) => n.id === oeffnung.hostWallId);
  if (!wand || wand.type !== 'wall') {
    throw new CommandAbgelehnt(`Wirtswand ${oeffnung.hostWallId} existiert nicht.`, 'wirtswand_fehlt');
  }
  pruefeGanzzahlig([oeffnung.offsetFromWallStart, oeffnung.width, oeffnung.height, oeffnung.sillHeight], 'Öffnung');
  const laenge = wandLaenge(wand.start, wand.end);
  if (oeffnung.offsetFromWallStart + oeffnung.width > laenge) {
    throw new CommandAbgelehnt('Öffnung ragt über das Wandende.', 'oeffnung_passt_nicht');
  }
}

export function applyCommand(draft: SceneDocument, command: HausplanerCommand, jetztIso: string): void {
  switch (command.type) {
    case 'ADD_NODE': {
      const node = command.node;
      if (!draft.levels.some((l) => l.id === node.levelId)) {
        throw new CommandAbgelehnt(`Level ${node.levelId} existiert nicht.`, 'level_unbekannt');
      }
      if (node.type === 'wall') {
        pruefeGanzzahlig([node.start.x, node.start.y, node.end.x, node.end.y, node.thickness, node.height], 'Wand');
        const laenge = wandLaenge(node.start, node.end);
        if (laenge <= 0 || laenge < node.thickness) {
          throw new CommandAbgelehnt('Wand hat Länge 0 oder ist kürzer als ihre Dicke.', 'wand_zu_kurz');
        }
      }
      if (istOeffnung(node)) {
        pruefeNeueOeffnung(draft, node);
      }
      draft.nodes.push({ ...node, createdAt: jetztIso, updatedAt: jetztIso });
      break;
    }

    case 'REMOVE_NODE': {
      const node = nodeOderFehler(draft, command.nodeId);
      const zuEntfernen = new Set<string>([node.id]);
      if (node.type === 'wall') {
        for (const n of draft.nodes) {
          if (istOeffnung(n) && n.hostWallId === node.id) {
            zuEntfernen.add(n.id); // Kaskade im SELBEN Command ⇒ EIN Undo stellt Wand + Öffnungen wieder her
          }
        }
      }
      draft.nodes = draft.nodes.filter((n) => !zuEntfernen.has(n.id));
      break;
    }

    case 'MOVE_NODE': {
      const node = nodeOderFehler(draft, command.nodeId);
      if (node.type === 'wall') {
        const pos = command.position;
        if (!('start' in pos)) {
          throw new CommandAbgelehnt('Wand braucht start/end.', 'nicht_ganzzahlig');
        }
        pruefeGanzzahlig([pos.start.x, pos.start.y, pos.end.x, pos.end.y], 'Wand');
        const laenge = wandLaenge(pos.start, pos.end);
        if (laenge <= 0 || laenge < node.thickness) {
          throw new CommandAbgelehnt('Wand würde Länge 0 bekommen oder kürzer als ihre Dicke.', 'wand_zu_kurz');
        }
        node.start = { ...pos.start };
        node.end = { ...pos.end };
        node.updatedAt = jetztIso;
        passeOeffnungenEin(draft, node, jetztIso);
      } else if (node.type === 'object' || node.type === 'route') {
        const pos = command.position;
        if ('start' in pos) {
          throw new CommandAbgelehnt('Objekt/Route braucht x/y/z.', 'nicht_ganzzahlig');
        }
        pruefeGanzzahlig([pos.x, pos.y, pos.z], 'Position');
        if (node.type === 'object') {
          node.transform.position = { ...pos };
        }
        node.updatedAt = jetztIso;
      } else {
        throw new CommandAbgelehnt(`MOVE_NODE für Typ ${node.type} nicht definiert (P0).`, 'node_unbekannt');
      }
      break;
    }

    case 'UPDATE_NODE': {
      const node = nodeOderFehler(draft, command.nodeId);
      const geometrieAenderung = node.type === 'wall' && ('start' in command.changes || 'end' in command.changes);

      Object.assign(node as object, command.changes);
      node.updatedAt = jetztIso;

      if (node.type === 'wall') {
        pruefeGanzzahlig([node.start.x, node.start.y, node.end.x, node.end.y], 'Wand');
        const laenge = wandLaenge(node.start, node.end);
        if (laenge <= 0 || laenge < node.thickness) {
          throw new CommandAbgelehnt('Wand würde Länge 0 bekommen oder kürzer als ihre Dicke.', 'wand_zu_kurz');
        }
        if (geometrieAenderung) {
          passeOeffnungenEin(draft, node, jetztIso);
        }
      }
      if (istOeffnung(node)) {
        pruefeNeueOeffnung(draft, node);
      }
      break;
    }

    case 'ADD_ROOF': {
      const roof = command.roof;
      if (!draft.levels.some((l) => l.id === roof.levelId)) {
        throw new CommandAbgelehnt(`Level ${roof.levelId} existiert nicht.`, 'level_unbekannt');
      }
      if (!Array.isArray(draft.roofs)) {
        draft.roofs = []; // Robustheit für v1-Drafts ohne roofs (Migration ist Lade-seitig).
      }
      pruefeDachProLevel(draft, roof);
      pruefeDachGanzzahlig(roof);
      draft.roofs.push({ ...roof, createdAt: jetztIso, updatedAt: jetztIso });
      break;
    }

    case 'UPDATE_ROOF': {
      if (!Array.isArray(draft.roofs)) {
        draft.roofs = [];
      }
      const roof = draft.roofs.find((r) => r.id === command.roofId);
      if (!roof) {
        throw new CommandAbgelehnt(`Dach ${command.roofId} existiert nicht.`, 'dach_unbekannt');
      }
      Object.assign(roof as object, command.changes);
      roof.updatedAt = jetztIso;
      pruefeDachProLevel(draft, roof); // falls levelId geändert wurde
      pruefeDachGanzzahlig(roof);
      break;
    }

    case 'REMOVE_ROOF': {
      if (!Array.isArray(draft.roofs)) {
        draft.roofs = [];
      }
      const vorher = draft.roofs.length;
      draft.roofs = draft.roofs.filter((r) => r.id !== command.roofId);
      if (draft.roofs.length === vorher) {
        throw new CommandAbgelehnt(`Dach ${command.roofId} existiert nicht.`, 'dach_unbekannt');
      }
      break;
    }

    case 'ADD_LEVEL': {
      const level = command.level;
      if (draft.levels.some((l) => l.id === level.id)) {
        throw new CommandAbgelehnt(`Level ${level.id} existiert bereits.`, 'level_existiert');
      }
      pruefeGanzzahlig(
        [level.elevation, level.defaultWallHeight, level.floorThickness, level.sortOrder],
        'Geschoss',
      );
      draft.levels.push({ ...level });
      draft.levels.sort((a, b) => a.sortOrder - b.sortOrder);
      break;
    }

    case 'UPDATE_LEVEL': {
      const level = draft.levels.find((l) => l.id === command.levelId);
      if (!level) {
        throw new CommandAbgelehnt(`Level ${command.levelId} existiert nicht.`, 'level_unbekannt');
      }
      // id ist unveränderlich — Nodes/Dächer referenzieren sie (eine Wahrheit).
      const { id: _ignoriereId, ...aenderbar } = command.changes;
      Object.assign(level as object, aenderbar);
      pruefeGanzzahlig(
        [level.elevation, level.defaultWallHeight, level.floorThickness, level.sortOrder],
        'Geschoss',
      );
      draft.levels.sort((a, b) => a.sortOrder - b.sortOrder);
      break;
    }

    case 'REMOVE_LEVEL': {
      if (draft.levels.length <= 1) {
        throw new CommandAbgelehnt('Das letzte Geschoss kann nicht gelöscht werden.', 'level_letztes');
      }
      const level = draft.levels.find((l) => l.id === command.levelId);
      if (!level) {
        throw new CommandAbgelehnt(`Level ${command.levelId} existiert nicht.`, 'level_unbekannt');
      }
      // Kein stilles Löschen von Fach-Daten: belegte Geschosse werden abgelehnt.
      const hatNodes = draft.nodes.some((n) => n.levelId === command.levelId);
      const hatDach = Array.isArray(draft.roofs) && draft.roofs.some((r) => r.levelId === command.levelId);
      if (hatNodes || hatDach) {
        throw new CommandAbgelehnt(
          `Geschoss ${level.name} enthält noch Elemente — erst leeren, dann löschen.`,
          'level_nicht_leer',
        );
      }
      draft.levels = draft.levels.filter((l) => l.id !== command.levelId);
      break;
    }

    case 'UPDATE_SETTINGS': {
      Object.assign(draft.settings, command.changes);
      break;
    }
  }

  draft.metadata.updatedAt = jetztIso;
}
