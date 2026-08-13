/**
 * A-31-5 — steht ein `executeCommand`-Aufruf INNERHALB einer Schleife?
 *
 * ---
 *
 * **Warum es dieses Skript gibt und nicht ein grep.** Der Planner hat die Reichweite zuerst mit
 * einem Rückwärts-Scan gemessen — von jedem Aufruf zur nächsten Schleife, mit Abbruch am
 * Funktionsanfang. Der fand DREI von fünf, weil `const g = spiegelteWand(…)` genau zwischen
 * Schleife und Aufruf steht und als Funktionsanfang gelesen wurde. **Ein Muster, das eine
 * Schreibweise voraussetzt, misst die Schreibweise und nicht die Sache.**
 *
 * Hier wird die Datei EINMAL durchlaufen und dabei die echte Klammer-Verschachtelung mitgeführt.
 * Für jede offene `{` wird vermerkt, ob ihr Kopf eine Schleife ist (`for`, `while`, oder ein
 * `=>`-Rumpf hinter `.forEach(`/`.map(`). Trifft der Durchlauf auf `executeCommand(`, wird der
 * Stapel gefragt. **Zeilenabstände, Zwischenzuweisungen und Einrückung spielen keine Rolle.**
 *
 * Kommentare und Zeichenketten werden vorher durch Leerzeichen ersetzt — sonst zählt eine `{` im
 * Fließtext als Block. Zeilenumbrüche bleiben erhalten, damit die Zeilennummern stimmen.
 *
 *   node scripts/a31-schleifenprobe.mjs <datei> [erwartete,zeilen]
 *
 * Ohne zweites Argument meldet es die Fundstellen und endet mit 1, sobald es welche gibt.
 * MIT erwarteten Zeilennummern prüft es sich selbst: findet es genau diese, ist das Muster
 * belegt — das ist der Lauf am Stand VOR dem Bau (Pflichtprüfung 4).
 */
import { readFileSync } from 'node:fs';

const datei = process.argv[2];
const erwartet = process.argv[3] ? process.argv[3].split(',').map(Number) : null;

if (!datei) {
  console.error('Aufruf: node scripts/a31-schleifenprobe.mjs <datei> [erwartete,zeilen]');
  process.exit(2);
}

const roh = readFileSync(datei, 'utf8');

/** Kommentare und Zeichenketten durch Leerzeichen ersetzen, Zeilenumbrüche behalten. */
function entkerne(text) {
  const aus = [...text];
  let i = 0;
  const leeren = (von, bis) => {
    for (let k = von; k < bis && k < aus.length; k += 1) {
      if (aus[k] !== '\n') aus[k] = ' ';
    }
  };
  while (i < text.length) {
    const z = text[i];
    const zwei = text.slice(i, i + 2);
    if (zwei === '//') {
      const ende = text.indexOf('\n', i);
      leeren(i, ende === -1 ? text.length : ende);
      i = ende === -1 ? text.length : ende;
    } else if (zwei === '/*') {
      const ende = text.indexOf('*/', i + 2);
      const bis = ende === -1 ? text.length : ende + 2;
      leeren(i, bis);
      i = bis;
    } else if (z === '"' || z === "'" || z === '`') {
      let k = i + 1;
      while (k < text.length) {
        if (text[k] === '\\') { k += 2; continue; }
        if (text[k] === z) break;
        k += 1;
      }
      leeren(i + 1, k);
      i = k + 1;
    } else {
      i += 1;
    }
  }

  return aus.join('');
}

const src = entkerne(roh);

/** Ist der Text VOR dieser `{` ein Schleifenkopf? */
function istSchleifenkopf(vorText) {
  // Rückwärts bis zum Ende der vorigen Anweisung — mehr braucht die Frage nicht.
  const schnitt = Math.max(
    vorText.lastIndexOf(';'), vorText.lastIndexOf('{'), vorText.lastIndexOf('}'),
  );
  const kopf = vorText.slice(schnitt + 1);
  if (/\b(for|while)\s*\(/.test(kopf)) return true;
  // Rumpf einer Rückruffunktion, die über eine Sammlung läuft.
  if (/=>\s*$/.test(kopf) && /\.(forEach|map|flatMap|filter|reduce)\s*\(/.test(kopf)) return true;

  return false;
}

const stapel = [];
const treffer = [];
let zeile = 1;

for (let i = 0; i < src.length; i += 1) {
  const z = src[i];
  if (z === '\n') { zeile += 1; continue; }
  if (z === '{') {
    stapel.push(istSchleifenkopf(src.slice(0, i)));
    continue;
  }
  if (z === '}') { stapel.pop(); continue; }
  if (src.startsWith('executeCommand(', i)) {
    if (stapel.some(Boolean)) {
      treffer.push({ zeile, text: roh.split('\n')[zeile - 1].trim().slice(0, 96) });
    }
    i += 'executeCommand'.length;
  }
}

console.log(`A-31-5 — ${datei}`);
if (treffer.length === 0) {
  console.log('  KEIN executeCommand-Aufruf innerhalb einer Schleife.');
} else {
  for (const t of treffer) console.log(`  :${t.zeile}  ${t.text}`);
}

if (erwartet) {
  const gefunden = treffer.map((t) => t.zeile);
  const gleich = gefunden.length === erwartet.length && gefunden.every((z, k) => z === erwartet[k]);
  console.log(gleich
    ? `  SELBSTPRUEFUNG BESTANDEN — genau die erwarteten Zeilen [${erwartet}]`
    : `  SELBSTPRUEFUNG GESCHEITERT — gefunden [${gefunden}], erwartet [${erwartet}]`);
  process.exit(gleich ? 0 : 1);
}

process.exit(treffer.length === 0 ? 0 : 1);
