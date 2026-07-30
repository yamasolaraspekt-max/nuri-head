/**
 * AUF-48 — **die zerlegte Hauptansicht als EINE benannte Quelle.**
 *
 * ---
 *
 * **Warum es diese Datei gibt.** Viele Zusagen prüfen Eigenschaften der Hauptansicht, indem sie
 * `HausplanerApp.tsx` als Text lesen. AUF-48 zerlegt genau diese Datei — und **jede Scheibe machte
 * bisher rund zwanzig geerbte Zusagen rot**, nicht weil sich etwas geändert hätte, sondern weil
 * Markup eine Datei weiter gezogen ist. In S4a habe ich das an 24 Stellen einzeln nachgezogen.
 * *Beim dritten Mal ist Einzelnachziehen kein Vorgehen mehr, sondern eine Gewohnheit.*
 *
 * **Die Regel, die diese Datei durchsetzt:** eine Absenz-Zusage darf **nicht dadurch grün werden,
 * dass Inhalt umzieht.** Wer „in der Hauptansicht steht X nicht" prüft, muss alle Teile lesen, in
 * die die Hauptansicht zerlegt wurde — sonst prüft er einen Ausschnitt und nennt ihn das Ganze.
 *
 * **Die Liste ist ausdrücklich AUFGEZÄHLT, nicht erraten.** Ein `readdir` über `app/` würde mit
 * jedem neuen Modul stillschweigend wachsen, und irgendwann läse eine Zusage Dateien mit, die mit
 * der Hauptansicht nichts zu tun haben — dann wird sie zufällig grün oder zufällig rot. *Kommt
 * eine Scheibe hinzu, wird sie hier eingetragen; das ist eine bewusste Zeile, keine Automatik.*
 */
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));

/**
 * Die Teile, in die `HausplanerApp.tsx` zerlegt wurde — in der Reihenfolge, in der sie gerendert
 * werden. **Reihenfolge ist keine Aussage:** wer sie braucht, prüft im Einzelteil, nicht hier.
 */
export const TEILE = [
  'app/HausplanerApp.tsx',
  'app/dashboard/Kopfrahmen.tsx',            // AUF-48-S4a: Werkzeugzeile, Bereich-Wähler, Bedienleiste
  'app/rahmen/GruppenzeileUndSchiene.tsx',   // AUF-48-S4b: Themen-Gruppen, Kontext-Optionen, Schiene
  'app/rahmen/Buehne.tsx',                   // AUF-48-S4c: die Konva-Ebenen des 2D-Grundrisses
] as const;

/** Der rohe Text eines einzelnen Teils. */
export function teil(pfad: string): string {
  return readFileSync(join(hier, '..', pfad), 'utf8');
}

/** Kommentare fort — sonst hält ein Test die Erklärung daneben für Code. */
export const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');

/**
 * **Die ganze zerlegte Hauptansicht als ein Text.** Für Zusagen, die eine Eigenschaft der Ansicht
 * prüfen und nicht die einer Datei.
 */
export function zerlegteApp(): string {
  return TEILE.map(teil).join('\n');
}

/** Dasselbe ohne Kommentare — der häufigere Fall. */
export function zerlegteAppRein(): string {
  return ohneKommentare(zerlegteApp());
}
