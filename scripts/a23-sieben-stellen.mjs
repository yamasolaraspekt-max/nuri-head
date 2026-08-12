/**
 * A-23-2 als PRÜFBARE Zusage — und zugleich die Fangprobe.
 *
 * Geprüft wird, was das Kriterium verlangt: jede der sieben Stellen trägt die Kennzeichnung
 * ÜBERHOLT AN DERSELBEN STELLE, nennt die gemeinte HÄLFTE und eine FUNDSTELLE des gebauten Wegs.
 * Ein blosses „Teil B kommt nicht mehr vor" wäre die falsche Prüfung: der alte Wortlaut SOLL
 * stehen bleiben.
 */
import { readFileSync } from 'node:fs';

const stellen = [
  ['app/StartView.tsx',                     'Gefüllt wird sie in',              'Projektliste'],
  ['app/StartView.tsx',                     'Die echte Liste braucht',          'Projektliste'],
  ['app/studioDaten.ts',                    'Bestand und braucht eine Route',   'Projektliste'],
  ['__tests__/startEhrlich.test.ts',        'der liegt bei Yama',               'Projektliste'],
  ['__tests__/startEhrlich.test.ts',        'das ist Teil B",',                 'Projektliste'],
  ['__tests__/startEhrlich.test.ts',        'bleibt deshalb offen',             'Projektliste'],
  ['__tests__/konfiguratorEhrlich.test.ts', 'nicht gestrichen, nur nicht dran', 'PAKETSPEICHERUNG'],
];

const wurzel = 'resources/planner/hausplaner/';
let rot = 0;

for (const [datei, marke, haelfte] of stellen) {
  const text = readFileSync(wurzel + datei, 'utf8');
  const i = text.indexOf(marke);
  if (i < 0) { console.log(`✖ ${datei}  „${marke}" — nicht gefunden`); rot++; continue; }

  // Der Umkreis der Stelle: 1200 Zeichen davor und danach. „An DERSELBEN Stelle" heisst nicht
  // „irgendwo in der Datei" — ein Satz, dessen Kennzeichnung einen Absatz weiter steht, wird
  // später als Beleg gelesen.
  const umkreis = text.slice(Math.max(0, i - 1200), i + 1200);
  const hatMarke = /ÜBERHOLT \(A-23, 13\.08\.\), und nicht gelöscht/.test(umkreis);
  const hatHaelfte = new RegExp(haelfte === 'PAKETSPEICHERUNG' ? 'PAKETSPEICHERUNG' : 'PROJEKTLISTE').test(umkreis);
  const hatFundstelle = haelfte === 'PAKETSPEICHERUNG'
    ? /web\.php:5016/.test(umkreis)
    : /HausplanerController\.php:101/.test(umkreis);

  const ok = hatMarke && hatHaelfte && hatFundstelle;
  if (!ok) rot++;
  console.log(`${ok ? '✔' : '✖'} ${datei}  „${marke.slice(0, 34)}"` +
    `  [überholt ${hatMarke ? 'ja' : 'NEIN'} · Hälfte ${hatHaelfte ? 'ja' : 'NEIN'} · Fundstelle ${hatFundstelle ? 'ja' : 'NEIN'}]`);
}

console.log(`\n${stellen.length - rot} von ${stellen.length} Stellen belegt.`);
process.exit(rot === 0 ? 0 : 1);
