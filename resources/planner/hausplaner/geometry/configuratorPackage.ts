/**
 * Hausplaner — Autarkes Konfigurator-Paket (Rückgrat der modularen Plattform).
 *
 * Leitplanke (Yamas verbindliche Architekturvorgabe): KEIN Konfigurator darf voraussetzen,
 * dass bereits ein vollständiges BuildingDocument existiert. Jede Konfiguration muss autark
 * gestartet, gespeichert, versioniert, geprüft, freigegeben und später VERLUSTFREI in ein
 * Gesamtprojekt überführt werden können.
 *
 * Reine Typen + deterministische Statuslogik. KEINE Szene-Mutation, kein Rendern, keine
 * Abhängigkeit aufs SceneDocument — deshalb autark testbar und app-entkoppelt. Zeit/Id werden
 * injiziert (Parameter), damit die Fabrik deterministisch bleibt.
 */

export type ConfiguratorType =
  | 'window' | 'door' | 'stair' | 'roof' | 'wall-buildup'
  | 'kitchen' | 'bathroom' | 'room' | 'furniture' | 'facade'
  | 'heating' | 'heat-pump' | 'radiator' | 'underfloor-heating' | 'heating-circuit'
  | 'pv' | 'battery' | 'wallbox' | 'ventilation' | 'sanitary' | 'electrical' | 'tga';

/**
 * Freigabegrade (Yamas Abschnitt 18/3). Eine Konfiguration kann existieren, OHNE Teil eines
 * Hauses zu sein. `integrated` = in ein Projekt übernommen; `outdated` = durch spätere Änderung
 * veraltet (muss neu geprüft werden).
 */
export type ConfiguratorStatus =
  | 'draft' | 'incomplete' | 'generated' | 'checked' | 'approved' | 'integrated' | 'outdated';

export interface TechnicalPort {
  id: string;
  /** z. B. 'water-cold' | 'water-warm' | 'drain' | 'power' | 'power-3ph' | 'data' | 'exhaust' | 'heating-flow' | 'heating-return' */
  medium: string;
  /** optionale Position relativ zur Paket-Geometrie (mm). */
  x?: number;
  y?: number;
  z?: number;
  /** Anschlusswert (W, l/s, DN …) — mediumabhängig, nur Referenz. */
  wert?: number;
  einheit?: string;
}

export interface MaterialReference { id: string; label?: string; }
export interface CatalogReference { id: string; hersteller?: string; artikelnummer?: string; katalogVersion?: number; }
export interface ConfiguratorDocument { id: string; art: string; titel?: string; }

export type PruefSchwere = 'info' | 'warnung' | 'fehler';
export interface ConfiguratorCheck {
  id: string;
  schwere: PruefSchwere;
  meldung: string;
  bestanden: boolean;
}
export interface ConfiguratorWarning { id: string; meldung: string; }

export interface ConfiguratorGeometry {
  /** grobe Hüllmaße in mm (Referenz für Integrations-Abgleich). */
  breite?: number;
  hoehe?: number;
  tiefe?: number;
  /** optionale Kontur als Punktliste (mm). */
  kontur?: ReadonlyArray<{ x: number; y: number }>;
}

/**
 * Standardisiertes autarkes Paket. Merge aus Yamas beiden Fassungen (Programm-Konfiguratoren §12
 * + Autarke-Module §3): trägt sowohl `revision` als auch die vollen Zuordnungs-/Audit-Felder.
 */
export interface ConfiguratorPackage {
  id: string;
  type: ConfiguratorType;
  schemaVersion: number;
  revision: number;
  status: ConfiguratorStatus;

  customerId?: string;
  projectId?: string;
  sourceBuildingDocumentId?: string;

  geometry?: ConfiguratorGeometry;
  parameters: Record<string, unknown>;
  technicalData: Record<string, unknown>;

  connectionPorts: TechnicalPort[];
  catalogItems: CatalogReference[];
  materials: MaterialReference[];

  checks: ConfiguratorCheck[];
  warnings: ConfiguratorWarning[];
  documents: ConfiguratorDocument[];

  createdBy: string;
  updatedBy: string;
  createdAt: string;
  updatedAt: string;
}

/** Aktuelle Schema-Version autarker Pakete. */
export const CONFIGURATOR_SCHEMA_VERSION = 1;

/**
 * Erlaubte Statusübergänge. Bewusst streng: aus `approved`/`integrated` geht es nur über
 * `outdated` zurück in die Bearbeitung (Freigabe-Schutz — keine stille Rückstufung).
 */
export const STATUS_UEBERGAENGE: Readonly<Record<ConfiguratorStatus, ReadonlyArray<ConfiguratorStatus>>> = {
  draft:      ['incomplete', 'generated', 'checked'],
  incomplete: ['draft', 'generated', 'checked'],
  generated:  ['draft', 'checked', 'incomplete'],
  checked:    ['draft', 'approved', 'generated'],
  approved:   ['integrated', 'outdated'],
  integrated: ['outdated'],
  outdated:   ['draft', 'checked', 'generated'],
};

/** Ist der Übergang von→zu erlaubt? (Gleichbleiben ist immer erlaubt.) */
export function statusUebergangErlaubt(von: ConfiguratorStatus, zu: ConfiguratorStatus): boolean {
  if (von === zu) return true;
  return STATUS_UEBERGAENGE[von].includes(zu);
}

/** Nur ein fachlich freigegebenes Paket darf in ein Projekt übernommen werden. */
export function kannIntegrieren(paket: Pick<ConfiguratorPackage, 'status'>): boolean {
  return paket.status === 'approved';
}

/** Ein integriertes/freigegebenes Paket, das durch spätere Änderung berührt wird, wird veraltet. */
export function markiereVeraltet<T extends ConfiguratorPackage>(paket: T, jetzt: string, durch: string): T {
  if (paket.status !== 'approved' && paket.status !== 'integrated') return paket;
  return { ...paket, status: 'outdated', updatedAt: jetzt, updatedBy: durch };
}

export interface NeuesPaketOpt {
  id: string;
  type: ConfiguratorType;
  jetzt: string;
  autor: string;
  customerId?: string;
  projectId?: string;
  parameters?: Record<string, unknown>;
  geometry?: ConfiguratorGeometry;
}

/** Deterministische Fabrik für ein frisches autarkes Paket (Status `draft`). */
export function neuesPaket(opt: NeuesPaketOpt): ConfiguratorPackage {
  return {
    id: opt.id,
    type: opt.type,
    schemaVersion: CONFIGURATOR_SCHEMA_VERSION,
    revision: 1,
    status: 'draft',
    customerId: opt.customerId,
    projectId: opt.projectId,
    geometry: opt.geometry,
    parameters: opt.parameters ?? {},
    technicalData: {},
    connectionPorts: [],
    catalogItems: [],
    materials: [],
    checks: [],
    warnings: [],
    documents: [],
    createdBy: opt.autor,
    updatedBy: opt.autor,
    createdAt: opt.jetzt,
    updatedAt: opt.jetzt,
  };
}

/** Neue Revision + Zeitstempel nach einer Änderung (Status bleibt, sofern nicht anders gesetzt). */
export function naechsteRevision<T extends ConfiguratorPackage>(paket: T, jetzt: string, durch: string): T {
  return { ...paket, revision: paket.revision + 1, updatedAt: jetzt, updatedBy: durch };
}
