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

import type { RoofShape } from './roofShape';

// v3 (Z-06-N1): Herkunft und Freigabe an Decke und Dach, beide PFLICHT. Die Anhebung ist der
// Grund, warum eine v2-Datei nicht stillschweigend als v3 durchgeht — siehe migriereSzene.
export const SCHEMA_VERSION = 3 as const;

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

  /**
   * Decken (Geschossdecken, Feature A). Eigene Sammlung NEBEN nodes[]/roofs[] — additiv (Muster roofs),
   * damit die Node-Union + Konsumenten unberührt bleiben. Je Level max. 1 Decke (Regel in applyCommand).
   * v1/v2-Dokumente ohne `ceilings` werden per Lade-Migration auf `ceilings: []` gehoben. OPTIONAL/additiv:
   * Bestandsdokumente ohne die Sammlung bleiben gültig (Konsumenten nutzen `?? []`). Kein 422.
   */
  ceilings?: CeilingNode[];

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

  /**
   * AUF-76 — **Wandaufbau (Mengenermittlung M0).** Feldgleich mit `CeilingNode.schichten`: dieselben
   * zwei Feldnamen, dieselbe Einheit, dieselbe Optionalität. *Zwei Schreibweisen für dieselbe Sache
   * wären der Anfang der zweiten Wahrheit.*
   *
   * **`thickness` bleibt unberührt und bleibt die Wahrheit für den Rohbau.** Diese Liste sagt, woraus
   * die Wand besteht — nicht, wie dick sie ist. Beide Bezugsmaße werden geführt (Yama, 26.07.).
   *
   * **Optional, und ohne Vorbelegung.** Ein Bestandsdokument ohne dieses Feld bleibt gültig; es wird
   * nichts nachgetragen. *Eine erfundene Schichtung wäre schlimmer als keine — sie sähe aus wie eine
   * Angabe.*
   *
   * **Nicht** `geometry/wandaufbau.Schicht`: das ist ein Rechentyp mit `dicke` und `lambda`, dies ist
   * ein Modellfeld mit `dickeMm`. Sie zusammenzuziehen ist eine eigene Entscheidung.
   *
   * **Die Reihenfolge trägt (noch) keine Bedeutung** — innen→außen liegt nahe, ist aber eine
   * Festlegung und keine Beobachtung. Sie ist als Frage zurückgegeben; bis sie beantwortet ist,
   * darf sich niemand auf die Reihenfolge verlassen.
   */
  schichten?: Array<{ materialId?: string; dickeMm: number }>;
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

  /** Fenster-/Tuer-Produktkonfiguration (additiv, Solar Aspekt): Profil/Glas/Oeffnungsart/RC.
   *  Fachrechnung (Uw/RC/Preis) in geometry/fensterProdukt.ts; hier nur die Auswahl-IDs. */
  produkt?: {
    /** Bauart-ID aus FENSTER_TYPEN/TUER_TYPEN (Icon-Auswahl), z. B. '05_dreh_kipp_links'. */
    typ?: string;
    profilId?: string;
    verglasungId?: string;
    oeffnungsArt?: 'fest' | 'dreh' | 'kipp' | 'dreh-kipp';
    rc?: 'ohne' | 'RC1N' | 'RC2N' | 'RC2' | 'RC3';
  };
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
 * Dachaufbau-Typen (W-3a). STEHENDE Aufbauten auf der Dachfläche: Gauben (5 Typen), Dachfenster,
 * Kamin, Lüfter/Sat, Lichtkuppel. Deckungsgleich mit den Typen der reinen Engine
 * (geometry/gaubeGeometrie · geometry/aufbauPlatzierung) — hier nur die Modell-Auswahl.
 */
export type ObstacleType =
  | 'chimney'
  | 'window'
  | 'vent'
  | 'sat'
  | 'lichtkuppel'
  | 'schleppgaube'
  | 'trapezgaube'
  | 'flachgaube'
  | 'giebelgaube'
  | 'spitzgaube';

/**
 * Ein einzelner Dachaufbau (W-3a, additiv am RoofNode). Lage `x`,`y` sind RELATIV (0..1) auf der
 * Dachfläche (x = parallel Traufe, y = Traufe→First). Maße in ganzen mm (mm-Invariante); die reine
 * Geometrie/Anschluss-Rechnung bleibt in geometry/gaubeGeometrie — hier steht nur die Beschreibung.
 */
export interface RoofAufbau {
  id: string;
  typ: ObstacleType;
  x: number;                  // 0..1, parallel Traufe
  y: number;                  // 0..1, Traufe→First
  breiteMm: number;           // ganze mm (Breite parallel Traufe)
  hoeheMm: number;            // ganze mm (vertikale Aufbau-/Fronthöhe)
  tiefeMm: number;            // ganze mm (Ausdehnung entlang der Dachschräge)
  rotationGrad?: number;      // optional; Standard 0
  neigungGrad?: number;       // optional; wird i. d. R. aus dem Hauptdach abgeleitet
}

/**
 * Dach (D-a, ▲D1). Teil DERSELBEN Szenen-Wahrheit, aber in `SceneDocument.roofs` statt in der
 * Node-Union (additiv, kein Eingriff in bestehende Node-Konsumenten). Je Level max. 1 Dach, Bezug
 * aufs oberste Geschoss. Die Flächen-Azimute werden NIE gepflegt, sondern aus `firstAzimutGrad`
 * abgeleitet (▲D4, Nord = +y) — belastbare Quelle für PV/Heizlast (Nachfolger RoofAreaEstimator).
 */
/**
 * Anbau-/Schenkel-Maße für zusammengesetzte L/T/U-Dächer (W-3b Stufe 2a Teil 2, additiv). Speist die
 * reinen Verschneidungs-Engines: `length/width` = Hauptbau, `lengthB/widthB` = Anbau (u braucht nur
 * `length/width`; l/t brauchen zusätzlich `lengthB/widthB`). Alle Maße in ganzen mm (mm-Invariante).
 */
export interface RoofAnbauMasse {
  length: number;            // mm — Hauptbau-Länge
  width: number;             // mm — Hauptbau-Breite
  lengthB?: number;          // mm — Anbau-Länge (nur l/t)
  widthB?: number;           // mm — Anbau-Breite (nur l/t)
}

/**
 * Z-06-N1 (B10) — woher die Geometrie kommt, und ob jemand sie bestätigt hat.
 *
 * Zwei getrennte Fragen und deshalb zwei Felder: `manuell` heißt nicht automatisch `bestaetigt`
 * (jemand kann eine gezeichnete Kontur ablehnen), und `abgeleitet` heißt nicht `abgelehnt`.
 * *Ein Feld für beides hätte den Fall „geraten, aber geprüft und in Ordnung" nicht ausdrücken
 * können — und genau den braucht Z-06-N3.*
 */
export type GeometrieHerkunft = 'manuell' | 'abgeleitet' | 'erkannt' | 'geschaetzt';
export type Freigabe = 'bestaetigt' | 'vorschlag' | 'zu_pruefen' | 'abgelehnt';

/** Die vier Felder, die Decke und Dach gemeinsam tragen. PFLICHT — siehe validation.ts. */
export interface MitHerkunft {
  geometrieHerkunft: GeometrieHerkunft;
  freigabe: Freigabe;
  /** ISO-Zeitpunkt des letzten Freigabe-WECHSELS — nicht des letzten Speicherns. */
  freigabeAm?: string;
  freigabeVon?: string;
}

export interface RoofNode extends BaseNode, MitHerkunft {
  type: 'roof';

  /** Traufkontur in mm (Default = Gebäude-Umriss des Levels). */
  polygon: Array<{ x: number; y: number }>;

  // W-3b: eine RoofShape-Wahrheit (additiv um rect/l/t/u-shape erweitert; die 4 Alt-Formen unverändert).
  roofType: RoofShape;

  neigungGrad: number;        // 0 = flach; < 90 (cos > 0, sichererCos)
  firstAzimutGrad: number;    // First-RICHTUNG (Grad); Flächen-Azimute werden daraus abgeleitet
  ueberstandMm: number;       // Dachüberstand an Traufe/Giebel
  traufhoeheMm: number;       // Default: level.elevation + defaultWallHeight

  /** Dachaufbauten (W-3a, OPTIONAL/additiv): Gauben/Dachfenster/Kamin/… auf der Dachfläche. */
  aufbauten?: RoofAufbau[];

  /** Anbau-Maße für L/T/U-Verschneidungsformen (W-3b 2a-2, OPTIONAL/additiv). Fehlt bei den
   *  rechteckigen Formen und bei Bestandsdaten ⇒ kein 422, kein Migrations-Zwang. */
  anbau?: RoofAnbauMasse;
}

/** Durchbruch in einer Decke (z. B. Treppenauge) — Loch-Polygon in mm. Vorbild dachAusschnitt. */
export interface CeilingOeffnung {
  polygon: Array<{ x: number; y: number }>;
}

/**
 * Geschossdecke (Feature A, additiv). Slab auf der Wand-Oberkante des Levels (level.elevation +
 * defaultWallHeight), Dicke `dickeMm` (Default level.floorThickness). Durchbrüche (Treppe) als
 * `oeffnungen`. `schichten` (Fußbodenaufbau) bleibt in Feature A leer — Feature B füllt/rechnet.
 * Eigene Sammlung `SceneDocument.ceilings` (Muster roofs) ⇒ Node-Union unberührt.
 */
export interface CeilingNode extends BaseNode, MitHerkunft {
  type: 'ceiling';
  /** Umriss in mm (Default = Gebäude-Umriss/Raumerkennung des Levels). */
  polygon: Array<{ x: number; y: number }>;
  /** Deckendicke in mm (Default = level.floorThickness). */
  dickeMm: number;
  /** Durchbrüche (z. B. Treppenauge) — Loch-Polygone in mm. */
  oeffnungen?: CeilingOeffnung[];
  /** Fußbodenaufbau (Feature B) — in Feature A leer/optional; feldgleich mit wandaufbau.Schicht. */
  schichten?: Array<{ materialId?: string; dickeMm: number }>;
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
  // W-3b: additiv auf die eine RoofShape-Wahrheit geweitet. Zur Laufzeit projiziert Stufe 1 nur die 4
  // rechteckigen Alt-Formen (dachFlaechen liefert für rect/l/t/u [] ⇒ kein Eintrag); Stufe 2 ergänzt L/T/U.
  dachtyp: RoofShape;
  flaeche_m2: number;
  azimut_grad: number | null;   // null = horizontal (Flachdach); sonst abgeleitet aus der First-Richtung
  neigung_grad: number;
  first_laenge_mm: number;
}
