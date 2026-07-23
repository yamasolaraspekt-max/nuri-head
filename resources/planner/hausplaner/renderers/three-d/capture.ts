/**
 * 3D-Capture-Flag (`?capture=1`) — schaltet `preserveDrawingBuffer` + das Snapshot-Fenster frei.
 *
 * Warum eigenes Modul: die Flag-Ableitung ist reines Query-String-Parsing (kein three/WebGL) und
 * damit unit-testbar — der WebGL-Frame selbst ist nur im Browser prüfbar. `preserveDrawingBuffer`
 * kostet im Normalbetrieb Leistung, deshalb NUR mit gesetztem Flag (Perf, keine Dauer-Regression).
 */

/** True, wenn der Query-String `capture=1` trägt. Reine Funktion (kein window) ⇒ testbar. */
export function istCaptureFlag(search: string | undefined | null): boolean {
  try {
    return new URLSearchParams(search ?? '').get('capture') === '1';
  } catch {
    return false;
  }
}

/** Flag aus der aktuellen Studio-URL (guarded für kein-window/SSR). */
export function captureAusFenster(): boolean {
  try {
    return typeof window !== 'undefined' && istCaptureFlag(window.location.search);
  } catch {
    return false;
  }
}

/** Name des window-Globals, über das der Evaluator (Puppeteer/CDP) den 3D-Snapshot zieht. */
export const SNAPSHOT_GLOBAL = '__hausplanerSnapshot3d';

/**
 * Ehrlicher Leer-Fall (Sizing-Fix): ist der 3D-Container zur Capture-Zeit real 0×0 (3D-Ansicht nicht
 * gelegt), liefert der Snapshot KEINEN stillen leeren PNG (der sähe wie „ok" aus), sondern einen
 * Klartext-Marker. Reine Funktion ⇒ testbar ohne WebGL. Bei echter Größe: null (Snapshot fährt fort).
 */
export function snapshotLeerMarker(breite: number, hoehe: number): string | null {
  if (breite > 0 && hoehe > 0) return null;
  return `data:text/plain;charset=utf-8,${encodeURIComponent(`3D-Ansicht nicht aktiv (Container ${breite}x${hoehe}px) — kein Snapshot`)}`;
}
