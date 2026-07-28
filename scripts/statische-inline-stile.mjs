/**
 * AUF-38 — **das Messwerkzeug für „statisch".**
 *
 * **Warum es das gibt.** „Statisch" war in vier Scheiben nirgends definiert. Jeder Beteiligte hat
 * ehrlich gemessen und kam anders heraus — *19 ↔ 17*, *29 ↔ 28* —, und beim Versuch, die Definition
 * ad hoc hinzuschreiben, war sie **im ersten Anlauf falsch**. Ein Maßstab, den jeder neu erfindet,
 * ist kein Maßstab. **Hier steht er einmal, ausführbar.**
 *
 * **Was es NICHT ist:** Produktionscode. Nichts hiervon läuft im Browser oder im Bündel; es liest
 * Quelltext und zählt. (Spur B, Auftrag des Planners vom 29.07.)
 *
 * ## Die Definition
 *
 * Ein `style={{…}}`-Block ist **statisch**, wenn sein Ausdruck ausschließlich aus **Literalen** und
 * **`T.*`-Zugriffen** besteht:
 * kein `?:` · kein Spread · kein Aufruf · kein anderer Bezeichner als `T`.
 * Eigenschaftsnamen zählen nicht als Bezeichner; `${…}` wird vorher aufgelöst.
 *
 * **Ausnahme** — darf inline bleiben, obwohl statisch:
 * der Block trägt einen **Rohwert ohne Token** (`#…` / `rgb(` / `rgba(`) **oder** stammt aus einem
 * **Ein-Wahrheit-Modul** (`GESPERRT_*`, siehe `app/dashboard/gesperrtStil.ts`).
 *
 * **Offen** = statisch und keine Ausnahme. *Das ist die Zahl, die eine Scheibe abarbeitet.*
 *
 * ## Aufruf
 *
 * ```sh
 * node scripts/statische-inline-stile.mjs            # Bericht über alle Dateien
 * node scripts/statische-inline-stile.mjs <datei>…   # nur diese
 * ```
 */
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { join } from 'node:path';

export const WURZEL = 'resources/planner/hausplaner';

/** Alle `.tsx` unterhalb eines Verzeichnisses — Testdateien bleiben draußen. */
export function tsxDateien(wurzel = WURZEL) {
  const gefunden = [];
  const lauf = (verzeichnis) => {
    for (const eintrag of readdirSync(verzeichnis)) {
      const pfad = join(verzeichnis, eintrag);
      if (statSync(pfad).isDirectory()) {
        if (eintrag !== '__tests__' && eintrag !== '__domtests__' && eintrag !== 'node_modules') lauf(pfad);
      } else if (eintrag.endsWith('.tsx')) {
        gefunden.push(pfad);
      }
    }
  };
  lauf(wurzel);
  return gefunden.sort();
}

/**
 * Die `style={{…}}`-Blöcke einer Quelle — **mit echter Klammerzählung**.
 *
 * Ein naives „bis zum ersten `}}`" schneidet verschachtelte Objekte und Vorlagen-Zeichenketten
 * mitten durch und misst dann etwas anderes, als dasteht. Deshalb wird hier gezählt, und
 * Zeichenketten werden übersprungen.
 */
export function stilBloecke(quelle) {
  const bloecke = [];
  const marke = 'style={{';
  for (let i = quelle.indexOf(marke); i !== -1; i = quelle.indexOf(marke, i + 1)) {
    let tiefe = 0;
    let j = i + marke.length - 2; // auf die erste `{` der beiden
    let zeichenkette = null;
    for (; j < quelle.length; j++) {
      const c = quelle[j];
      if (zeichenkette) {
        if (c === '\\') { j++; continue; }
        if (c === zeichenkette) zeichenkette = null;
        continue;
      }
      if (c === "'" || c === '"' || c === '`') { zeichenkette = c; continue; }
      if (c === '{') tiefe++;
      else if (c === '}') { tiefe--; if (tiefe === 0) break; }
    }
    // `j` steht auf der schliessenden Klammer, mit der die Tiefe 0 erreicht — das ist die zweite
    // von `}}`. Sie gehoert dazu, das naechste Zeichen nicht.
    bloecke.push({
      text: quelle.slice(i, j + 1),
      zeile: quelle.slice(0, i).split('\n').length,
    });
  }
  return bloecke;
}

const ROHWERT = /#[0-9a-fA-F]{3,8}\b|rgba?\(/;
const HEXFARBE = /#[0-9a-fA-F]{3,8}\b/g;

/** Trägt der Block einen Rohwert (Farbe ohne Token)? */
export function hatRohwert(text) {
  return ROHWERT.test(text);
}

/** Stammt der Block aus dem Sperrstil-Modul, der einen Wahrheit über gesperrte Flächen? */
export function ausSperrmodul(text) {
  return text.includes('GESPERRT_');
}

/** Die Hexfarben, die im Block roh dastehen — klein geschrieben, ohne Doppel. */
export function rohfarben(text) {
  return [...new Set((text.match(HEXFARBE) ?? []).map((f) => f.toLowerCase()))];
}

/** **Die Definition.** Nur Literale und `T.*` — sonst nichts. */
export function istStatisch(text) {
  const aufgeloest = text.replace(/\$\{([^}]*)\}/g, '$1');
  if (aufgeloest.includes('?') || aufgeloest.includes('...')) return false;
  let kern = aufgeloest.replace(/'[^']*'|"[^"]*"|`[^`]*`/g, '0');
  kern = kern.slice('style={{'.length, -2);
  kern = kern.replace(/[A-Za-z_$][\w$]*\s*:/g, '');   // Eigenschaftsnamen sind keine Bezeichner
  kern = kern.replace(/\bT\.[A-Za-z0-9_]+/g, '');     // Token-Zugriffe sind erlaubt
  return !/[A-Za-z_$]/.test(kern);
}

/** Statisch, aber zugelassen: Rohwert ohne Token oder Sperrstil-Modul. */
export function istAusnahme(text) {
  return hatRohwert(text) || ausSperrmodul(text);
}

/** Eine Datei auszählen. */
export function messeDatei(pfad) {
  const quelle = readFileSync(pfad, 'utf8');
  const bloecke = stilBloecke(quelle);
  const statisch = bloecke.filter((b) => istStatisch(b.text));
  const ausnahmen = statisch.filter((b) => istAusnahme(b.text));
  const offen = statisch.filter((b) => !istAusnahme(b.text));
  return {
    pfad,
    gesamt: bloecke.length,
    statisch: statisch.length,
    ausnahmen: ausnahmen.length,
    offen: offen.map((b) => b.zeile),
    farben: bloecke.flatMap((b) => rohfarben(b.text).map((f) => ({ farbe: f, zeile: b.zeile }))),
  };
}

/** Alle Dateien auszählen. */
export function messeAlle(dateien = tsxDateien()) {
  return dateien.map(messeDatei);
}

// --- Aufruf von der Kommandozeile ----------------------------------------------------------------
const direkt = process.argv[1] && process.argv[1].endsWith('statische-inline-stile.mjs');
if (direkt) {
  const argumente = process.argv.slice(2);
  const messungen = messeAlle(argumente.length ? argumente : tsxDateien());
  const breite = Math.max(...messungen.map((m) => m.pfad.length));
  let gesamt = 0; let offenGesamt = 0;
  console.log(`${'Datei'.padEnd(breite)}  gesamt  statisch  Ausnahme  offen`);
  for (const m of messungen) {
    if (m.gesamt === 0) continue;
    gesamt += m.gesamt; offenGesamt += m.offen.length;
    const zeilen = m.offen.length ? `  (Z${m.offen.join(', Z')})` : '';
    console.log(
      `${m.pfad.padEnd(breite)}  ${String(m.gesamt).padStart(6)}  ${String(m.statisch).padStart(8)}`
      + `  ${String(m.ausnahmen).padStart(8)}  ${String(m.offen.length).padStart(5)}${zeilen}`,
    );
  }
  console.log(`\n${gesamt} Stellen insgesamt, davon ${offenGesamt} offen.`);
}
