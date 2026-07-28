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
 * ## Reichweite — was gezählt wird und was nicht
 *
 * **Die Grundgesamtheit sind die `.tsx`-Dateien der Insel** (Testverzeichnisse bleiben draußen).
 * Von den **114** `.ts`-Dateien trägt **genau eine** ein `style={{` — `app/stil/tokenVariablen.ts`,
 * die Token-Datei selbst. **Das ist kein Loch**, aber es stand nirgends geschrieben, und jeder
 * Prüfende musste die Frage neu stellen. *(Benannt vom Evaluator, 29.07.)*
 *
 * Bewusst **keine Zusage** daraus: eine Zusage über die `.ts`-Dateien wäre eine Zusage über eine
 * Menge, die niemand pflegt — sie ginge beim ersten neuen Token-Modul rot, ohne dass ein Fehler
 * vorliegt. Ein Satz im Kopf ist hier die richtige Form.
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
 * **Kommentare ausblenden — längentreu.**
 *
 * *Nachtrag nach den Befunden `AUF38-MW-1` und `AUF38-MW-2` (Evaluator, 29.07.).* Beide hatten
 * dieselbe Wurzel: **Kommentare wurden nirgends übersprungen.** Das hat zweierlei angerichtet —
 *
 * 1. Ein Kommentar **im** Block liess seinen Text als Bezeichner stehen; jeder kommentierte, sonst
 *    rein statische Stil-Block galt dadurch als „dynamisch" (Fundstelle: `WerkzeugGruppenMenue.tsx`
 *    Z82 — nur Literale und `T.*`, und trotzdem nicht gezählt).
 * 2. Ein **einzelnes** Anführungszeichen in einem Kommentar (`StartView.tsx` Z155 trägt
 *    `„nah dran"`) schickte den Zeichenketten-Scanner in einen Modus, aus dem er nicht mehr
 *    herausfand: ab dort wurden **alle** Klammern übersprungen und der Block lief bis zum
 *    Dateiende. Falsch-rot war damit erreichbar, sobald irgendeine Farbe dahinter einen Token
 *    bekommt.
 *
 * **Längentreu** ist die Bedingung, an der alles hängt: jedes Zeichen wird durch ein Leerzeichen
 * ersetzt, Zeilenumbrüche bleiben stehen. Dadurch stimmen Abstände und Zeilennummern weiterhin mit
 * der Originalquelle überein — die Maske darf gefahrlos anstelle des Originals gelesen werden.
 *
 * Und sie ist **zeichenketten-bewusst**: ein `//` in `'url(//cdn…)'` ist kein Kommentar. Die
 * umgekehrte Reihenfolge wäre genau der Fehler, der hier behoben wird, nur andersherum.
 */
export function ohneKommentare(quelle) {
  let aus = '';
  let zeichenkette = null;
  for (let i = 0; i < quelle.length; i++) {
    const c = quelle[i];
    if (zeichenkette) {
      aus += c;
      if (c === '\\') { aus += quelle[++i] ?? ''; continue; }
      if (c === zeichenkette) zeichenkette = null;
      continue;
    }
    if (c === "'" || c === '"' || c === '`') { zeichenkette = c; aus += c; continue; }
    if (c === '/' && quelle[i + 1] === '/') {
      while (i < quelle.length && quelle[i] !== '\n') { aus += ' '; i++; }
      aus += quelle[i] ?? '';                       // der Zeilenumbruch bleibt
      continue;
    }
    if (c === '/' && quelle[i + 1] === '*') {
      const ende = quelle.indexOf('*/', i + 2);
      const bis = ende === -1 ? quelle.length : ende + 2;
      for (; i < bis; i++) aus += quelle[i] === '\n' ? '\n' : ' ';
      i--;
      continue;
    }
    aus += c;
  }
  return aus;
}

/**
 * Die `style={{…}}`-Blöcke einer Quelle — **mit echter Klammerzählung**.
 *
 * Ein naives „bis zum ersten `}}`" schneidet verschachtelte Objekte und Vorlagen-Zeichenketten
 * mitten durch und misst dann etwas anderes, als dasteht. Deshalb wird hier gezählt, und
 * Zeichenketten werden übersprungen.
 *
 * **Gelesen wird die kommentarfreie Maske**, nicht die Rohquelle (siehe `ohneKommentare`). Weil die
 * Maske längentreu ist, bleiben Zeilennummern gültig; und weil die zurückgegebenen Blöcke aus ihr
 * stammen, ist ein Kommentar für **jeden** nachgelagerten Schritt unsichtbar — Einstufung wie
 * Farbsuche. *Eine Farbe, die nur im Kommentar steht, ist kein Stilwert.*
 */
export function stilBloecke(rohquelle) {
  const quelle = ohneKommentare(rohquelle);
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

/**
 * **Die Definition.** Nur Literale und `T.*` — sonst nichts.
 *
 * Kommentare werden **hier noch einmal** ausgeblendet, obwohl `stilBloecke` das bereits tut. Grund:
 * die Funktion wird auch direkt aufgerufen — von den Zusagen und von jedem, der die Definition an
 * einem Schnipsel nachrechnet. Ein Maßstab, der nur über einen bestimmten Einstieg richtig misst,
 * ist der Fehler, gegen den dieses Skript gebaut wurde. *(Befund `AUF38-MW-1`.)*
 */
export function istStatisch(rohtext) {
  const text = ohneKommentare(rohtext);

  // **Vorlagen-Ausdrücke werden HERAUSGEHOBEN, nicht an Ort und Stelle aufgelöst.** *(Befund
  // `AUF38-MW-4`.)* An Ort und Stelle aufgelöst blieb der Ausdruck innerhalb der Backticks und
  // wurde eine Zeile später mit der Zeichenkette entwertet — eine Breite aus einem fremden
  // Bezeichner galt dadurch als **statisch**. Das ist die gefährliche Richtung: `MW-1` und `MW-2`
  // zählen zu wenig, `MW-4` zählt zu viel — und nur zu viel erzeugt falsche Arbeit, weil die
  // Stelle dann zur Umstellung in eine Klasse beauftragt würde.
  const ausdruecke = [...text.matchAll(/\$\{([^}]*)\}/g)].map((m) => m[1]).join(' ');
  const ohneAusdruecke = text.replace(/\$\{[^}]*\}/g, '');

  // **Zeichenketten werden ZUERST entwertet, dann wird auf `?` und `...` geprüft.** *(Befund
  // `AUF38-MW-3`.)* In der umgekehrten Reihenfolge machte ein Fragezeichen oder eine Ellipse **im
  // Text** eine statische Stelle dynamisch — ein Fragezeichen als Inhalt ist kein Ternär.
  const entwertet = ohneAusdruecke.replace(/'[^']*'|"[^"]*"|`[^`]*`/g, '0');
  if (`${entwertet} ${ausdruecke}`.includes('?')) return false;
  if (`${entwertet} ${ausdruecke}`.includes('...')) return false;

  let kern = entwertet.slice('style={{'.length, -2);
  kern = kern.replace(/[A-Za-z_$][\w$]*\s*:/g, '');   // Eigenschaftsnamen sind keine Bezeichner
  kern = `${kern} ${ausdruecke}`;                     // die herausgehobenen Ausdrücke zählen mit
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
