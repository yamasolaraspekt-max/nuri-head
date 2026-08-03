/**
 * Hausplaner — die fuenf Arbeitsbereich-Ids, und NICHTS sonst.
 *
 * **Dieses Modul importiert nichts.** Das ist seine ganze Aufgabe: es ist das Blatt am Ende
 * jeder Kette. Bis 03.08.2026 wohnten die Ids in `toolRegistry.ts`; `dashboard/arbeitsbereiche.ts`
 * holte sie sich von dort und baute `ARBEITSBEREICHE` auf MODULEBENE. Solange die Registry
 * selbst nichts von `paketAdapter` brauchte, war das harmlos — mit W-05 braucht sie es, und
 * damit schloss sich der Kreis toolRegistry -> paketAdapter -> arbeitsbereiche -> toolRegistry
 * (gefahren, nicht vermutet: `ReferenceError: Cannot access 'WORKSPACE_IMPORT' before
 * initialization` bei `arbeitsbereiche.ts:69`).
 *
 * **Die WERTE sind tabu, der ORT war es nie** — sie stehen im UI-Zustand des Nutzers.
 * Wer hier eine Zeichenkette aendert, aendert gespeicherte Zustaende. Wer eine Id ergaenzt,
 * ergaenzt sie hier und nirgends sonst.
 */

export const WORKSPACE_IMPORT = 'import';
export const WORKSPACE_ARCHITEKTUR = 'architektur';
export const WORKSPACE_BAUPHYSIK = 'bauphysik';
export const WORKSPACE_HEIZUNG = 'heizung';
export const WORKSPACE_ELEKTRO_PV = 'elektro-pv';
