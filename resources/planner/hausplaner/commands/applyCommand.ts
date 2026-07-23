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
import type { SceneDocument, SceneNode, WallNode, OpeningNode, RoofNode, RoofAufbau, CeilingNode, CeilingOeffnung } from '../domain/scene.types';
import { CommandAbgelehnt, type HausplanerCommand } from '../domain/commands.types';
import { wandLaenge } from '../geometry/wallGeometry';
import { parametereZuTreppe } from '../geometry/treppeObjekt';

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

/** Dach aus dem Draft holen oder ablehnen (W-3a, für Aufbau-Commands). */
function dachOderFehler(draft: SceneDocument, roofId: string): RoofNode {
  if (!Array.isArray(draft.roofs)) {
    draft.roofs = [];
  }
  const roof = draft.roofs.find((r) => r.id === roofId);
  if (!roof) {
    throw new CommandAbgelehnt(`Dach ${roofId} existiert nicht.`, 'dach_unbekannt');
  }
  return roof;
}

/** mm-Invariante der Aufbau-Maße (W-3a): Breite/Höhe/Tiefe in ganzen mm. */
function pruefeAufbauGanzzahlig(aufbau: RoofAufbau): void {
  pruefeGanzzahlig([aufbau.breiteMm, aufbau.hoeheMm, aufbau.tiefeMm], 'Dachaufbau');
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

/** mm-Invariante der Deckengeometrie (Umriss + Öffnungen + Dicke). */
function pruefeDeckeGanzzahlig(ceiling: CeilingNode): void {
  const koords = ceiling.polygon.flatMap((p) => [p.x, p.y]);
  const lochKoords = (ceiling.oeffnungen ?? []).flatMap((o) => o.polygon.flatMap((p) => [p.x, p.y]));
  pruefeGanzzahlig([...koords, ...lochKoords, ceiling.dickeMm], 'Decke');
}

/** Je Level max. 1 Decke (wie Dach). */
function pruefeDeckeProLevel(draft: SceneDocument, ceiling: CeilingNode): void {
  if ((draft.ceilings ?? []).some((c) => c.id !== ceiling.id && c.levelId === ceiling.levelId)) {
    throw new CommandAbgelehnt(`Level ${ceiling.levelId} hat bereits eine Decke (max. 1 je Level).`, 'decke_pro_level_vorhanden');
  }
}

/** Treppendurchbrüche: je Treppe im Level ein Loch-Rechteck (Lauflinie ± halbe Laufbreite), mm-ganzzahlig. */
function treppenDurchbrueche(draft: SceneDocument, levelId: string): CeilingOeffnung[] {
  const loecher: CeilingOeffnung[] = [];
  for (const n of draft.nodes) {
    if (n.type !== 'object') continue;
    if (n.objectType !== 'stair' || n.levelId !== levelId) continue;
    const tp = parametereZuTreppe(n.parameters);
    if (!tp) continue;
    const dx = tp.endX - tp.startX, dy = tp.endY - tp.startY;
    const len = Math.hypot(dx, dy) || 1;
    const nx = -dy / len, ny = dx / len;          // Normale zur Lauflinie
    const h = tp.laufbreite / 2;
    const p = (x: number, y: number): { x: number; y: number } => ({ x: Math.round(x), y: Math.round(y) });
    loecher.push({ polygon: [
      p(tp.startX + nx * h, tp.startY + ny * h),
      p(tp.endX + nx * h, tp.endY + ny * h),
      p(tp.endX - nx * h, tp.endY - ny * h),
      p(tp.startX - nx * h, tp.startY - ny * h),
    ] });
  }
  return loecher;
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

    case 'SET_NODES_SICHTBAR': {
      for (const id of command.nodeIds) {
        const node = nodeOderFehler(draft, id);
        node.visible = command.sichtbar;
        node.updatedAt = jetztIso;
      }
      break;
    }
    case 'SET_NODES_GESPERRT': {
      for (const id of command.nodeIds) {
        const node = nodeOderFehler(draft, id);
        node.locked = command.gesperrt;
        node.updatedAt = jetztIso;
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

    case 'ADD_CEILING': {
      const ceiling = command.ceiling;
      if (!draft.levels.some((l) => l.id === ceiling.levelId)) {
        throw new CommandAbgelehnt(`Level ${ceiling.levelId} existiert nicht.`, 'level_unbekannt');
      }
      if (!Array.isArray(draft.ceilings)) {
        draft.ceilings = []; // Robustheit für Drafts ohne ceilings (Migration ist Lade-seitig).
      }
      pruefeDeckeProLevel(draft, ceiling);
      // Treppen im Level ⇒ automatische Durchbrüche, wenn nicht schon welche gesetzt sind.
      const auto = (ceiling.oeffnungen && ceiling.oeffnungen.length > 0) ? ceiling.oeffnungen : treppenDurchbrueche(draft, ceiling.levelId);
      const gespeichert: CeilingNode = { ...ceiling, oeffnungen: auto.length > 0 ? auto : undefined, createdAt: jetztIso, updatedAt: jetztIso };
      pruefeDeckeGanzzahlig(gespeichert);
      draft.ceilings.push(gespeichert);
      break;
    }

    case 'UPDATE_CEILING': {
      if (!Array.isArray(draft.ceilings)) {
        draft.ceilings = [];
      }
      const ceiling = draft.ceilings.find((c) => c.id === command.ceilingId);
      if (!ceiling) {
        throw new CommandAbgelehnt(`Decke ${command.ceilingId} existiert nicht.`, 'decke_unbekannt');
      }
      Object.assign(ceiling as object, command.changes);
      ceiling.updatedAt = jetztIso;
      pruefeDeckeProLevel(draft, ceiling); // falls levelId geändert wurde
      pruefeDeckeGanzzahlig(ceiling);
      break;
    }

    case 'REMOVE_CEILING': {
      if (!Array.isArray(draft.ceilings)) {
        draft.ceilings = [];
      }
      const vorher = draft.ceilings.length;
      draft.ceilings = draft.ceilings.filter((c) => c.id !== command.ceilingId);
      if (draft.ceilings.length === vorher) {
        throw new CommandAbgelehnt(`Decke ${command.ceilingId} existiert nicht.`, 'decke_unbekannt');
      }
      break;
    }

    case 'ADD_ROOF_AUFBAU': {
      const roof = dachOderFehler(draft, command.roofId);
      pruefeAufbauGanzzahlig(command.aufbau);
      if (!Array.isArray(roof.aufbauten)) {
        roof.aufbauten = [];
      }
      roof.aufbauten.push({ ...command.aufbau });
      roof.updatedAt = jetztIso;
      break;
    }

    case 'REMOVE_ROOF_AUFBAU': {
      const roof = dachOderFehler(draft, command.roofId);
      const vorher = roof.aufbauten?.length ?? 0;
      roof.aufbauten = (roof.aufbauten ?? []).filter((a) => a.id !== command.aufbauId);
      if (roof.aufbauten.length === vorher) {
        throw new CommandAbgelehnt(`Aufbau ${command.aufbauId} existiert nicht.`, 'aufbau_unbekannt');
      }
      roof.updatedAt = jetztIso;
      break;
    }

    case 'UPDATE_ROOF_AUFBAU': {
      const roof = dachOderFehler(draft, command.roofId);
      const aufbau = (roof.aufbauten ?? []).find((a) => a.id === command.aufbauId);
      if (!aufbau) {
        throw new CommandAbgelehnt(`Aufbau ${command.aufbauId} existiert nicht.`, 'aufbau_unbekannt');
      }
      Object.assign(aufbau as object, command.changes);
      pruefeAufbauGanzzahlig(aufbau);
      roof.updatedAt = jetztIso;
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
