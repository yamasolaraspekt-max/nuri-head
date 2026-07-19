/**
 * Reparatur 2: reine, testbare Statuslogik für die PV-Belegung des 3D-Planers.
 *
 * Ziel: Eine vorhandene PV-Belegung darf bei Änderungen an Gebäude- oder
 * Dachgeometrie nicht mehr STILL verschwinden. Sie wird stattdessen als
 * „prüfpflichtig" markiert, der Nutzer wird gewarnt, und abhängige Kennzahlen
 * gelten nicht mehr als gesichert gültig.
 *
 * Diese Datei ist bewusst rein (keine React-/THREE-Abhängigkeit) und damit
 * vollständig per Unit-Test prüfbar. Sie ändert NICHTS an Reparatur 1
 * (dachWerte.ts) und baut keine neue Belegung/Stückliste.
 */

export type BelegungStatus = "keine" | "gueltig" | "pruefpflichtig";

/** Standard-Warntext, wenn eine Geometrieänderung die Belegung betrifft. */
export const BELEGUNG_WARNUNG =
  "Die Dachgeometrie wurde geändert. Die vorhandene PV-Belegung wurde entfernt und muss neu belegt werden.";

/**
 * Eine Geometrie-/Maß-/Neigungs-/Überstandsänderung macht eine VORHANDENE
 * Belegung prüfpflichtig. Ohne vorhandene Module gibt es nichts zu warnen.
 */
export function geometrieMachtPruefpflichtig(modulAnzahlVorher: number): boolean {
  return typeof modulAnzahlVorher === "number" && modulAnzahlVorher > 0;
}

/**
 * Der sichtbare Belegungsstatus aus Modulanzahl + Prüfpflicht-Flag.
 * - prüfpflichtig  -> eine Belegung war vorhanden und muss neu geprüft/belegt werden
 * - gültig         -> Module vorhanden und frisch (neu) belegt
 * - keine          -> keine Belegung (oder bewusst entfernt)
 */
export function belegungStatusText(modulAnzahl: number, pruefpflichtig: boolean): BelegungStatus {
  if (pruefpflichtig) return "pruefpflichtig";
  return modulAnzahl > 0 ? "gueltig" : "keine";
}

/** true, wenn die abhängigen Kennzahlen (Anzahl/Leistung/Gewicht) NICHT als gesichert gültig gelten dürfen. */
export function kennzahlenUnsicher(pruefpflichtig: boolean): boolean {
  return pruefpflichtig === true;
}
