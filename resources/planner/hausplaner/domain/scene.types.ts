/**
 * Hausplaner — Scene-Datenmodell (P0, Spec docs/hausplaner/p0-spec-foundation.md).
 *
 * EINE Wahrheit: 2D und 3D sind Renderer DERSELBEN Daten. Alle Längen sind GANZE
 * MILLIMETER (Integer — die mm-Invariante hat einen eigenen Test). Koordinaten:
 * x/y horizontal, z = Höhe; Nord = +y (Azimut-Ableitung, ▲K2). Three.js-Umrechnung
 * (y-up) passiert erst in P1, ausschließlich im 3D-Adapter.
 *
 * P0 aktiv: WallNode, OpeningNode, ZoneNode(room, derived). ObjectNode/RouteNode
 * und weitere Zone-Typen sind hier definiert, bekommen aber in P0 weder Werkzeuge
 * noch Renderer (Union vollständig, damit schema_version 1 stabil bleibt).
 */

export const SCHEMA_VERSION = 2 as const;

// ---------------------------------------------------------------- Dokument

export interface SceneDocument {
  id: string;                 // Dokument-UUID (nur im JSON; DB-PK bleibt bigint)
  projectId: number;

  schemaVersion: typeof SCHEMA_VERSION;
  revision: number;           // Server-vergeben; Client sendet base_revision beim Speichern

  units: 'mm';

  settings: {
    gridSize: number;         // mm
    snapEnabled: boolean;
    angleSnap: number;        // Grad (z. B. 15)
  };

  levels: Level[];
  nodes: SceneNode[];
  materials: MaterialDefinition[];

  /**
   * Dächer (D-a, schemaVersion 2). Eigene Sammlung NEBEN nodes[] — additiv, damit die Node-Union
   * und alle ihre Konsumenten (Raumerkennung/Projektion/Wand-Renderer) unberührt bleiben. Je Level
   * max. 1 Dach (Regel in applyCommand). v1-Dokumente werden per Lade-Migration auf `roofs: []` gehoben.
   */
  roofs: RoofNode[];

  metadata: {
    createdAt: string;        // ISO-8601
    updatedAt: string;
  };
}

export interface Level {
  id: string;
  name: string;

  elevation: number;          // mm über ±0
  defaultWallHeight: number;  // mm
  floorThickness: number;     // mm

  sortOrder: number;
}

export interface MaterialDefinition {
  id: string;
  name: string;
  color?: string;             // Anzeige 2D; 3D-Material = P1
  uValue?: number;            // W/(m²K) — optional, Heizlast bleibt führend fürs Rechnen
}

// ---------------------------------------------------------------- Nodes

export interface BaseNode {
  id: string;
  type: string;
  levelId: string;

  name?: string;
  visible: boolean;
  locked: boolean;

  tags: string[];

  createdAt: string;
  updatedAt: string;
}

/** Wand = fachliches Bauteil (nicht „irgendein 3D-Objekt"). Geteilte Wand existiert EINMAL. */
export interface WallNode extends BaseNode {
  type: 'wall';

  start: { x: number; y: number };   // mm
  end: { x: number; y: number };     // mm

  thickness: number;                 // mm
  height: number;                    // mm

  construction?: {
    materialId?: string;
    insulationType?: string;
    insulationThickness?: number;    // mm
    uValue?: number;                 // W/(m²K)
  };
}

/** Öffnung ist WANDGEBUNDEN: lebt relativ zur Wirtswand, wandert/verschwindet mit ihr. */
export interface OpeningNode extends BaseNode {
  type: 'window' | 'door' | 'opening';

  hostWallId: string;
  offsetFromWallStart: number;       // mm entlang der Wandachse
  width: number;                     // mm
  height: number;                    // mm
  sillHeight: number;                // mm (Tür: 0)

  catalogItemId?: string;

  thermalProperties?: {
    uValue?: number;
    frameType?: string;
    glazingType?: string;
  };

  /** Schema-Review-Entscheid: beim Wand-Kürzen geklemmt statt still verschoben/gelöscht. */
  clamped?: boolean;

  /** Nur Tür (P2b-4, additiv): Anschlagseite (Angel links/rechts der Wandlaufrichtung)
   *  und Aufschlagseite (innen = linke Wandnormale, außen = rechte). */
  anschlag?: 'links' | 'rechts';
  oeffnung?: 'innen' | 'aussen';
}

/** Frei platzierbare Objekte — P0 nur Typ-Definition, keine Werkzeuge. */
export interface ObjectNode extends BaseNode {
  type: 'object';

  objectType:
    | 'radiator'
    | 'heat_pump_indoor'
    | 'heat_pump_outdoor'
    | 'buffer_tank'
    | 'hot_water_tank'
    | 'battery'
    | 'inverter'
    | 'wallbox'
    | 'furniture'
    | 'sanitary'
    | 'stair'; // Treppe: laeuft durch ObjectNode-CRUD, Fachdaten in parameters (treppe.*)

  catalogItemId: string;

  transform: {
    position: { x: number; y: number; z: number };  // mm
    rotation: { x: number; y: number; z: number };  // Grad
    scale: { x: number; y: number; z: number };     // Faktor (placement.allowScaling gate)
  };

  parameters: Record<string, string | number | boolean | null>;
}

/** Zonen — P0 aktiv NUR zoneType 'room' und NUR abgeleitet (Raumerkennung). */
export interface ZoneNode extends BaseNode {
  type: 'zone';

  zoneType:
    | 'room'
    | 'underfloor_heating'
    | 'pv_area'
    | 'maintenance_area'
    | 'sound_area'
    | 'restricted_area';

  polygon: Array<{ x: number; y: number }>;         // mm

  /** true = von der Raumerkennung erzeugt; nicht direkt editierbar (P0: rooms immer derived). */
  derived: boolean;

  parameters: Record<string, string | number | boolean | null>;
}

/** Leitungen — P0 nur Typ-Definition. */
export interface RouteNode extends BaseNode {
  type: 'route';

  routeType:
    | 'heating_pipe'
    | 'water_pipe'
    | 'refrigerant_line'
    | 'electrical_line'
    | 'pv_dc_line'
    | 'drainage';

  points: Array<{ x: number; y: number; z: number }>; // mm

  diameter?: number;          // mm
  crossSection?: number;      // mm²
  insulationThickness?: number; // mm
}

export type SceneNode = WallNode | OpeningNode | ObjectNode | ZoneNode | RouteNode;

/**
 * Dach (D-a, ▲D1). Teil DERSELBEN Szenen-Wahrheit, aber in `SceneDocument.roofs` statt in der
 * Node-Union (additiv, kein Eingriff in bestehende Node-Konsumenten). Je Level max. 1 Dach, Bezug
 * aufs oberste Geschoss. Die Flächen-Azimute werden NIE gepflegt, sondern aus `firstAzimutGrad`
 * abgeleitet (▲D4, Nord = +y) — belastbare Quelle für PV/Heizlast (Nachfolger RoofAreaEstimator).
 */
export interface RoofNode extends BaseNode {
  type: 'roof';

  /** Traufkontur in mm (Default = Gebäude-Umriss des Levels). */
  polygon: Array<{ x: number; y: number }>;

  roofType: 'sattel' | 'walm' | 'pult' | 'flach';

  neigungGrad: number;        // 0 = flach; < 90 (cos > 0, sichererCos)
  firstAzimutGrad: number;    // First-RICHTUNG (Grad); Flächen-Azimute werden daraus abgeleitet
  ueberstandMm: number;       // Dachüberstand an Traufe/Giebel
  traufhoeheMm: number;       // Default: level.elevation + defaultWallHeight
}

// ------------------------------------------- Projektions-Kontrakt (▲K2, P0-Fixture)

/**
 * Ziel-Struktur der Heizlast-Projektion — feldgleich mit `raum_geometrien`
 * (wberechnung/ticket). Der ProjektionsService ist P2; dieses Interface + das
 * Fixture (Abnahmekriterium 9) frieren den Vertrag ab P0 ein.
 * azimut_grad wird aus der Wand-Normalen ABGELEITET (Nord = +y, 0–359, ganzzahlig).
 */
export interface RaumGeometrieProjektion {
  geschoss: number;
  polygon: Array<{ x: number; y: number }>;     // mm, lichter Raum-Umriss
  hoehe_mm: number | null;
  wand_segmente: Array<{
    von: { x: number; y: number };
    bis: { x: number; y: number };
    grenzflaeche: string;                        // z. B. 'aussen' | 'innen' | 'erdreich'
    azimut_grad: number | null;                  // nur Außenwände; abgeleitet, nie gepflegt
    bauteil_typ: string;
    oeffnungen: Array<{
      typ: 'fenster' | 'tuer' | 'oeffnung';
      breite_mm: number;
      hoehe_mm: number;
      bruestung_mm: number;
    }>;
  }>;
  decke: { grenzflaeche: string; bauteil_typ: string } | null;  // null = ehrlich unbestimmt
  boden: { grenzflaeche: string; bauteil_typ: string } | null;
}

/**
 * Dach-Projektions-Kontrakt (D-d, ▲D4). EIGENER Vertrag NEBEN RaumGeometrieProjektion — je Dachfläche
 * ein Eintrag mit belastbarer Fläche + ABGELEITETEM Azimut (Nord = +y), Nachfolger des stillgelegten
 * RoofAreaEstimator und Quelle für PV/Heizlast. Die Zuordnung „welche Dachfläche ist Decke welches Raums"
 * ist BEWUSST NICHT hier — `RaumGeometrieProjektion.decke` bleibt null, bis diese Regel entschieden ist
 * (kein stilles Befüllen). Das Fixture (dachProjektion.test.ts) friert diesen Vertrag ein.
 */
export interface DachFlaecheProjektion {
  geschoss: number;
  roof_id: string;
  dachtyp: 'sattel' | 'walm' | 'pult' | 'flach';
  flaeche_m2: number;
  azimut_grad: number | null;   // null = horizontal (Flachdach); sonst abgeleitet aus der First-Richtung
  neigung_grad: number;
  first_laenge_mm: number;
}
