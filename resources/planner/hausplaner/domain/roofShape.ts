/**
 * W-3b — EINE RoofShape-Wahrheit (B1-Auflösung). Bisher zwei Vokabulare: das Modell kannte nur
 * `sattel|walm|pult|flach`, die Engine (geometry/dachformVorlagen · EngineRoofShape) zusätzlich
 * `rect|l-shape|t-shape|u-shape`. Diese Datei ist die EINE typisierte Quelle, auf die Modell (RoofNode),
 * Zod-Validierung und (ab Stufe 2) der Vorlagen-Picker referenzieren — kein gespiegelter Zweit-Typ.
 *
 * ADDITIV: die vier Alt-Formen bleiben unverändert an erster Stelle ⇒ Bestands-Dächer bleiben gültig.
 * `rect` = rechteckige Grundform (rechteckige Kontur); `l/t/u-shape` = zusammengesetzte Grundrisse, die
 * über die Verschneidungs-Engine (dachVerschneidung/dachUForm) ausgewertet werden (Render: Stufe 2).
 */

export type RoofShape =
  | 'sattel'
  | 'walm'
  | 'pult'
  | 'flach'
  | 'rect'
  | 'l-shape'
  | 't-shape'
  | 'u-shape';

/** Alle RoofShape-Werte in kanonischer Reihenfolge (Alt-Formen zuerst) — auch für Zod/Tests. */
export const ROOF_SHAPES: readonly RoofShape[] = [
  'sattel', 'walm', 'pult', 'flach', 'rect', 'l-shape', 't-shape', 'u-shape',
];

/**
 * Formen mit rechteckiger Traufkontur — die bestehende Rechteck-Geometrie (pruefeRechteckigeKontur →
 * dachMeshWelt/dachflaechen) gilt unverändert. `rect` läuft geometrisch wie eine rechteckige Fläche.
 */
export const RECHTECK_FORMEN: readonly RoofShape[] = ['sattel', 'walm', 'pult', 'flach', 'rect'];

/**
 * Zusammengesetzte Formen (L/T/U): NICHT rechteckig. Sie werden NICHT über die pauschale
 * Rechteck-Prüfung geworfen, sondern (Stufe 2) an die Verschneidungs-Engine übergeben. In Stufe 1
 * validieren sie bereits, rendern aber noch leer (kein Crash).
 */
export const VERSCHNEIDUNGS_FORMEN: readonly RoofShape[] = ['l-shape', 't-shape', 'u-shape'];

/** true = zusammengesetzte L/T/U-Form (Verschneidungs-Pfad statt Rechteck-Pfad). */
export function istVerschneidungsForm(shape: RoofShape): boolean {
  return VERSCHNEIDUNGS_FORMEN.includes(shape);
}
