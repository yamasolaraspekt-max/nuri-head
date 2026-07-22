/**
 * Hausplaner — Werkzeug-/Node-Registry (Plattform-Kern). Muster von Pascal Editor abgeleitet,
 * Code eigen und an UNSERE SceneDocument-/Command-Welt angepasst. Eine Registry-Map `kind ->
 * WerkzeugNode` macht Werkzeugleiste, Platzierung, 2D-/3D-Render und Panel DATENGETRIEBEN, statt
 * pro Bauteil hart verdrahtet. Reines Modul: keine Szene-Mutation, kein React/Konva/three hier —
 * nur der Vertrag + Verwaltung. Die konkreten Bauteile (bauteile/<kind>/) registrieren sich selbst.
 */

export type WerkzeugKategorie = 'bau' | 'bauelement' | 'haustechnik' | 'pv';

/** Ergebnis der reinen Parametrik eines Bauteils: Geometrie + fachliche Prüfung (z. B. DIN). */
export interface Parametrik {
  /** true, wenn alle harten Fachprüfungen bestanden sind (z. B. DIN 18065 bei der Treppe). */
  bestanden: boolean;
  /** frei nutzbare, bauteil-spezifische Kennwerte (für Panel/Label/Prüfanzeige). */
  kennwerte?: Record<string, number | string | boolean>;
}

/**
 * Vertrag eines Bauteils. Generisch über den validierten Datentyp `T` (aus dem Zod-Schema des
 * Bauteils). Der Editor kennt nur diesen Vertrag — nicht die einzelnen Bauteile.
 */
export interface WerkzeugNode<T = unknown> {
  kind: string;
  schemaVersion: number;
  kategorie: WerkzeugKategorie;
  /** Kürzel für Werkzeugleiste/Tastatur (z. B. 'R' für Treppe). */
  kuerzel?: string;
  /** Anzeigename + Tooltip-Funktionsbeschreibung. */
  label: string;
  beschreibung: string;
  /** reine Geometrie + Fachprüfung; hier hängen unsere DIN-Rechenkerne. */
  parametrik: (daten: T) => Parametrik;
  faehigkeiten: {
    waehlbar: boolean;
    ziehbar: boolean;
    dupliziert: boolean;
    loeschbar: boolean;
  };
  /** optionale Migration alter Persistenz auf schemaVersion. */
  migrate?: (alt: unknown, vonVersion: number) => T;
}

const registry = new Map<string, WerkzeugNode<unknown>>();

/** Registriert ein Bauteil. Doppelte `kind` sind ein Programmierfehler (wirft). */
export function registriereWerkzeug<T>(def: WerkzeugNode<T>): void {
  if (registry.has(def.kind)) {
    throw new Error(`Werkzeug '${def.kind}' ist bereits registriert.`);
  }
  registry.set(def.kind, def as WerkzeugNode<unknown>);
}

/** Ein Bauteil nach kind holen (oder undefined). */
export function werkzeug(kind: string): WerkzeugNode<unknown> | undefined {
  return registry.get(kind);
}

/** Alle registrierten Bauteile (Registrierreihenfolge), optional je Kategorie gefiltert. */
export function alleWerkzeuge(kategorie?: WerkzeugKategorie): WerkzeugNode<unknown>[] {
  const list = [...registry.values()];
  return kategorie ? list.filter((w) => w.kategorie === kategorie) : list;
}

/** Nur für Tests/Hot-Reload: Registry leeren. */
export function _leereRegistry(): void {
  registry.clear();
}
