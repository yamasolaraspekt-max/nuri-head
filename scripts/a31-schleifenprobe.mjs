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
 *
 * **Die Fangprobe liegt im Repo und ist jederzeit nachfahrbar** — vier Treffer, drei
 * Nicht-Treffer, und sie deckt genau die Fälle ab, an denen die ersten drei Fassungen dieses
 * Skripts gescheitert sind:
 *
 *   node scripts/a31-schleifenprobe.mjs \
 *     resources/planner/hausplaner/__proben__/a31-schleifenprobe-fangprobe.txt 4,11,16,23
 *
 * *Die Datei trägt `.txt` und keine Endung, die der Testlauf oder `tsc` einsammelt — sie ist
 * Prüfstoff für dieses Skript und kein Inselcode.*
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

/**
 * Was ist dieser Block: `schleife`, `funktion` oder `sonst`?
 *
 * **Die Unterscheidung, an der meine erste Fassung gescheitert ist.** Sie fragte nur „steht
 * irgendwo darüber eine Schleife" — und meldete damit in `Buehne.tsx` VIER und in
 * `EigenschaftenPanel.tsx` ZWEI Stellen. *Alle sechs sind `onDragEnd`/`onChange`-Handler innerhalb
 * eines Render-`map`.* **Ein Handler läuft pro Benutzer-Geste, nicht pro Schleifendurchlauf** — er
 * löst EINEN Befehl aus und ist genau das, was A-31 herstellen will.
 *
 * Ein Funktionsrumpf **schirmt** die Schleifen über ihm ab: der Aufruf ist aufgeschoben, nicht
 * wiederholt. **Ausnahme ist der Rückruf der Schleife selbst** (`.map(x => { … })`) — der läuft
 * je Element und zählt als Schleife.
 *
 * *Eine Barriere, die zu oft warnt, wird weggeklickt.*
 */
/** Ein Pfeil, der UNMITTELBAR das Argument einer laufenden Sammlung ist. */
const SAMMEL_RUECKRUF = /\.(forEach|map|flatMap|filter|reduce)\s*\(\s*(\([^()]*\)|[A-Za-z_$][\w$]*)\s*$/;

function blockArt(vorText) {
  // Rückwärts bis zum Ende der vorigen Anweisung — mehr braucht die Frage nicht.
  const schnitt = Math.max(
    vorText.lastIndexOf(';'), vorText.lastIndexOf('{'), vorText.lastIndexOf('}'),
  );
  const kopf = vorText.slice(schnitt + 1);
  if (/\b(for|while)\s*\([^;]*\)\s*$/.test(kopf)) return 'schleife';
  // Rückruf einer Sammlung: der Pfeil muss UNMITTELBAR das Argument des Aufrufs sein.
  if (SAMMEL_RUECKRUF.test(kopf.replace(/=>\s*$/, ''))) return 'schleife';
  if (/(=>|\bfunction\b[^()]*\([^()]*\))\s*$/.test(kopf)) return 'funktion';

  return 'sonst';
}

const stapel = [];
const treffer = [];
let zeile = 1;

for (let i = 0; i < src.length; i += 1) {
  const z = src[i];
  if (z === '\n') { zeile += 1; continue; }
  if (z === '{') {
    stapel.push({ art: blockArt(src.slice(0, i)), pos: i });
    continue;
  }
  if (z === '}') { stapel.pop(); continue; }
  if (src.startsWith('executeCommand(', i)) {
    // Ein Pfeil OHNE Rumpfklammern wirft keinen Block auf den Stapel und ist für die Klammern
    // allein unsichtbar — `ids.map((id) => …executeCommand(…))` genauso wie
    // `onChange={(e) => …executeCommand(…)}`. Steht ein solcher Pfeil zwischen der innersten
    // Klammer und dem Aufruf, ist er die INNERSTE Ebene und entscheidet allein:
    // Sammlungs-Rückruf ⇒ läuft je Element; jeder andere ⇒ aufgeschobener Handler.
    const zwischen = stapel.length ? src.slice(stapel[stapel.length - 1].pos + 1, i) : src.slice(0, i);
    const seitAnweisung = zwischen.slice(zwischen.lastIndexOf(';') + 1);
    const pfeil = seitAnweisung.lastIndexOf('=>');

    let inSchleife;
    if (pfeil !== -1) {
      inSchleife = SAMMEL_RUECKRUF.test(seitAnweisung.slice(0, pfeil));
    } else {
      // Sonst entscheiden die Klammern: von innen nach aussen, und der erste Funktionsrumpf
      // beendet die Frage — was dahinter liegt, wiederholt den Aufruf nicht.
      inSchleife = false;
      for (let k = stapel.length - 1; k >= 0; k -= 1) {
        if (stapel[k].art === 'funktion') break;
        if (stapel[k].art === 'schleife') { inSchleife = true; break; }
      }
    }
    if (inSchleife) {
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
