/**
 * A-25 — jeder Datensatz in `docs/STATUS.md` bekommt seinen eigenen ```yaml-Zaun.
 *
 * **Was dieses Werkzeug NICHT tut:** es ändert keinen Wert, keinen Feldnamen, keinen Vermerktext
 * und entfernt keinen Datensatz. Es setzt Zäune — mehr nicht.
 *
 * **Die Falle, und sie hat den Planner selbst erwischt** (A-25-1, wörtlich im Kriterium): ein
 * Muster, das JEDEN ```-Zaun als Umschalter zählt, meldet einen Bereich statt zwei — denn nach
 * CommonMark schliesst nur ein Zaun OHNE Info-String. **```yaml ist ein ÖFFNER und niemals ein
 * Schliesser.** Ein solches Muster kann nach dem Bau NULL melden und grün sein, ohne dass etwas
 * behoben wäre. Deshalb steht die Zaunlogik hier in einer eigenen Funktion und wird geprüft.
 *
 *   node scripts/a25-zaeune.mjs            nur messen
 *   node scripts/a25-zaeune.mjs --schreiben  messen, absichern, schreiben
 */
import { readFileSync, writeFileSync } from 'node:fs';
import { execSync } from 'node:child_process';

const DATEI = 'docs/STATUS.md';
const schreiben = process.argv.includes('--schreiben');

/**
 * CommonMark: ein Zaun mit Info-String (```yaml) ÖFFNET; nur ein nackter ``` schliesst.
 * Rückgabe: je Zeile die Blocknummer (0 = ausserhalb), plus die Liste der Blöcke.
 */
function zaeune(zeilen) {
  const bloecke = [];
  let offen = null;
  const lage = new Array(zeilen.length).fill(0);
  zeilen.forEach((z, i) => {
    const m = z.match(/^(\s*)```(.*)$/);
    if (!m) { if (offen) lage[i] = bloecke.length; return; }
    const info = m[2].trim();
    if (!offen) {
      if (info === '') return;            // ein nackter Zaun ausserhalb: kein Öffner für uns
      offen = { start: i, info };
      return;
    }
    if (info === '') { bloecke.push({ ...offen, ende: i }); offen = null; return; }
    lage[i] = bloecke.length;             // ```yaml INNERHALB eines Blocks ist INHALT
  });
  if (offen) bloecke.push({ ...offen, ende: zeilen.length - 1 });
  return { bloecke, lage };
}

const roh = readFileSync(DATEI, 'utf8');
const zeilen = roh.split('\n');
const { bloecke } = zaeune(zeilen);

/** Datensätze eines Blocks: jede `auftrag:`-Zeile am Zeilenanfang. */
const datensaetze = (b) => {
  const treffer = [];
  for (let i = b.start + 1; i < b.ende; i++) if (/^auftrag: /.test(zeilen[i])) treffer.push(i);
  return treffer;
};

const mehrfach = bloecke.map((b) => ({ b, d: datensaetze(b) })).filter((x) => x.d.length > 1);

// --- Messung ------------------------------------------------------------------------------------
console.log(`Bloecke gesamt: ${bloecke.length}`);
for (const { b, d } of mehrfach) {
  console.log(`  Zeile ${b.start + 1}-${b.ende + 1}  ${b.ende - b.start + 1} Zeilen · ${d.length} Datensaetze: ` +
    d.map((i) => zeilen[i].replace(/^auftrag: "?|"?\s*$/g, '')).join(' · '));
}

// A-25-1b: Blöcke mit Zustandsfeldern, aber ohne Zuordnung.
const ohneZuordnung = bloecke.filter((b) => {
  const inhalt = zeilen.slice(b.start + 1, b.ende);
  const hatZustand = inhalt.some((z) => /^(zustand|ballbesitz): /.test(z));
  const hatZuordnung = inhalt.some((z) => /^(auftrag|vorgang): /.test(z));
  return hatZustand && !hatZuordnung;
});
for (const b of ohneZuordnung) {
  console.log(`  Zeile ${b.start + 1}: Block mit Zustandsfeld OHNE auftrag:/vorgang: — „${zeilen[b.start + 1].slice(0, 60)}"`);
}

const auftraege = () => zeilen.filter((z) => /^auftrag: /.test(z)).sort();
const zustaende = () => zeilen.filter((z) => /^zustand: /.test(z)).sort();
console.log(`Datensaetze gesamt: ${auftraege().length} · zustand-Felder: ${zustaende().length}`);

if (!schreiben) {
  console.log(mehrfach.length === 0 && ohneZuordnung.length === 0
    ? '\nA-25-1 und A-25-1b: GRUEN — kein Block traegt mehr als einen Datensatz, kein Zustandsfeld ist unzuordenbar.'
    : `\nA-25-1: ROT — ${mehrfach.length} Bereich(e) mit mehreren Datensaetzen, ${ohneZuordnung.length} unzuordenbare(r) Block/Bloecke.`);
  process.exit(mehrfach.length === 0 && ohneZuordnung.length === 0 ? 0 : 1);
}

// --- A-25-6: Nebenläufigkeit ---------------------------------------------------------------------
const kopfVorher = execSync('git rev-parse HEAD').toString().trim();

// --- Schreiben: von hinten nach vorne, damit die Zeilennummern nicht wandern ---------------------
const neu = [...zeilen];
for (const { d } of [...mehrfach].reverse()) {
  // Der ERSTE Datensatz bleibt im bestehenden Zaun. Vor jeden weiteren kommt ein Schliesser,
  // eine Leerzeile und ein neuer Öffner.
  for (const i of [...d].slice(1).reverse()) neu.splice(i, 0, '```', '', '```yaml');
}

// --- A-25-2: Gegenprobe VOR dem Schreiben --------------------------------------------------------
const vorher = { a: auftraege(), z: zustaende() };
const nachher = {
  a: neu.filter((z) => /^auftrag: /.test(z)).sort(),
  z: neu.filter((z) => /^zustand: /.test(z)).sort(),
};
const gleich = (x, y) => x.length === y.length && x.every((v, i) => v === y[i]);

if (!gleich(vorher.a, nachher.a) || !gleich(vorher.z, nachher.z)) {
  console.error('ABBRUCH: auftrag- oder zustand-Werte weichen ab. Es wird NICHT geschrieben.');
  process.exit(2);
}
const nurZaeune = neu.filter((z) => z !== '```' && z !== '```yaml' && z !== '');
const altOhne = zeilen.filter((z) => z !== '```' && z !== '```yaml' && z !== '');
if (!gleich(nurZaeune, altOhne)) {
  console.error('ABBRUCH: ausser Zaeunen und Leerzeilen hat sich Text geaendert. Es wird NICHT geschrieben.');
  process.exit(2);
}

const kopfNachher = execSync('git rev-parse HEAD').toString().trim();
if (kopfVorher !== kopfNachher) {
  console.error(`ABBRUCH: HEAD hat sich waehrend des Laufs bewegt (${kopfVorher} -> ${kopfNachher}).`);
  process.exit(3);
}

writeFileSync(DATEI, neu.join('\n'));
console.log(`\nGESCHRIEBEN. ${mehrfach.reduce((s, x) => s + x.d.length - 1, 0)} neue Zaeune gesetzt.`);
console.log(`Gegenprobe: ${vorher.a.length} auftrag-Werte und ${vorher.z.length} zustand-Werte zeichengleich.`);
