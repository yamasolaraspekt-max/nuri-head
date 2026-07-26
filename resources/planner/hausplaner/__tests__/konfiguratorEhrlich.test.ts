/**
 * AUF-74 — der Konfigurator sagt, was wirklich passiert.
 *
 * **Der Befund:** `grep -rl "ConfiguratorPackage" app/ database/migrations/ routes/` ist **leer** —
 * serverseitig existiert nichts. Was der Konfigurator „speichern" nannte, war
 * `a.download = konfigurator-<art>-<id>.json`: **eine Datei im Download-Ordner.** Gelesen hat der
 * Nutzer dabei „als ConfiguratorPackage **speicherbar**", „später **verlustfrei ins Projekt**" und
 * „**gespeichert** (Download)".
 *
 * **Yamas Entscheidung:** nicht bauen, sondern den Satz wahr machen. Die echte Speicherung bleibt
 * als AUF-40 Teil B stehen — nicht gestrichen, nur nicht dran.
 *
 * **Warum die Prüfungen hier eng sind:** Ein breiter `grep` auf „speichern" findet den Zweig, der
 * die **Wahrheit** sagt („als ein Command ins Gebäudemodell"), und meldet ihn als Fehler. Geprüft
 * wird deshalb jede Stelle einzeln, nicht die Datei im Ganzen.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const wizard = ohneKommentare(readFileSync(join(hier, '../app/ConfigWizard.tsx'), 'utf8'));

/** Der Zweig, der den autarken Fall beschreibt — nur er stand zur Debatte. */
const autark = (m: RegExpMatchArray | null): string => {
  assert.ok(m, 'die Stelle wurde nicht gefunden — der Test prüft ins Leere');
  return m[1];
};

// --- K3: keine Stelle behauptet mehr eine Speicherung im Programm -------------------------------
test('K3: die Beschreibung nennt das Ergebnis — eine Datei, kein Versprechen', () => {
  const stelle = autark(wizard.match(/\{standalone \? '(Ergebnis: eine Datei[^']*)'/));
  assert.doesNotMatch(stelle, /verlustfrei/, 'das Versprechen ins Projekt ist weg');
  assert.doesNotMatch(stelle, /speicherbar|gespeichert/);
  assert.match(stelle, /Datei zum Herunterladen/, 'das tatsächliche Ergebnis steht da');
  // §3.1: kein „noch nicht" ohne Aussage darüber, was stattdessen geht.
  assert.match(stelle, /über den Experten/, 'der Weg ins Gebäude wird genannt');
});

test('K3: die Statuszeile ebenso', () => {
  const stelle = autark(wizard.match(/\{standalone \? '(Ergebnis: Datei[^']*)' : 'Undo\/Redo im Modell'\}/));
  assert.doesNotMatch(stelle, /speicherbar/);
  assert.match(stelle, /Datei zum Herunterladen/);
});

test('K3: die Meldung nach dem Klick sagt, was entstanden ist', () => {
  const stelle = autark(wizard.match(/\? `(\$\{wahl\.label\}: Datei[^`]*)`/));
  assert.doesNotMatch(stelle, /gespeichert/, 'nichts wurde gespeichert — es wurde heruntergeladen');
  assert.match(stelle, /heruntergeladen/);
  assert.match(stelle, /\$\{dateiname\}/, 'der Nutzer erfährt, wie die Datei heißt');
});

// --- K4: der Download bleibt --------------------------------------------------------------------
test('K4: `a.download` funktioniert unverändert', () => {
  assert.match(wizard, /a\.href = url; a\.download = dateiname; a\.click\(\);/);
  assert.match(wizard, /URL\.revokeObjectURL\(url\)/, 'ohne Freigabe bliebe der Blob hängen');
  assert.match(wizard, /const dateiname = `konfigurator-\$\{art\}-\$\{wahl\.id\}\.json`/,
    'derselbe Dateiname wie vorher — der Download ändert sich nicht, nur seine Beschreibung');
});

// --- K5: kein Versprechen auf später ------------------------------------------------------------
test('K5: kein „folgt", kein „in Kürze", kein „geplant", kein „demnächst"', () => {
  // Genau diese Sorte Satz hat AUF-44 aus der Icon-Zeile entfernt; sie kommt hier nicht zurück.
  for (const wort of ['folgt', 'in Kürze', 'geplant', 'demnächst']) {
    assert.ok(!wizard.includes(wort), `„${wort}" ist ein Versprechen auf später`);
  }
});

// --- K6: die wahre Aussage bleibt Zeichen für Zeichen -------------------------------------------
test('K6: der Übernahme-Weg ins Modell ist unberührt — er war schon wahr', () => {
  // Das ist die Aussage, die stimmt. Sie darf beim Aufräumen nicht mitgehen.
  assert.match(wizard, /'Als Fachobjekt speichern — als ein Command ins Gebäudemodell, Undo\/Redo inklusive\.'/);
  assert.match(wizard, /'Undo\/Redo im Modell'/);
});

test('K6: die Platzierungs-Meldungen der nicht-autarken Wege sind unverändert', () => {
  for (const satz of [
    /ins Modell gesetzt — im Plan verschiebbar/,
    /auf die gewählte Wand gesetzt/,
    /Platzierung abgelehnt/,
  ]) {
    assert.match(wizard, satz);
  }
});

// --- Die vierte Stelle: der Fehlerfall ----------------------------------------------------------
test('die VIERTE Stelle: ein fehlgeschlagener Download meldet nicht mehr Erfolg', () => {
  // Sie stand nicht im Auftrag. Vorher: `catch { /* Download optional */ }` — und danach lief die
  // Erfolgsmeldung trotzdem. Wer zehn Minuten konfiguriert und eine Erfolgsmeldung ohne Datei
  // bekommt, sucht sie im Download-Ordner.
  assert.doesNotMatch(wizard, /Download optional/, 'der verschluckte Fehler ist weg');
  assert.match(wizard, /let entstanden = true;/);
  assert.match(wizard, /catch \{\s*entstanden = false;\s*\}/);
  assert.match(wizard, /onÜbernehmen\(entstanden\s*\?/, 'die Meldung hängt am tatsächlichen Ausgang');
  const fehler = autark(wizard.match(/: `(\$\{wahl\.label\}: Die Datei konnte nicht[^`]*)`/));
  assert.match(fehler, /es ist nichts entstanden/);
  // Auch im Fehlerfall: kein „noch nicht" ohne den Weg, der offen steht.
  assert.match(fehler, /über den Experten/);
});

// --- Die FÜNFTE Stelle, eine Fläche weiter ------------------------------------------------------
test('die FÜNFTE Stelle: der Startbildschirm machte dasselbe Versprechen', () => {
  // Bei der Sichtprobe standen „verlustfrei" und „gespeichert" weiter im Seitentext — nicht im
  // Konfigurator, sondern auf der Startseite dahinter: „Fachplaner — … später verlustfrei ins
  // Projekt übernehmbar". Wörtlich dieselbe Zusage, eine Fläche weiter. §6 des Auftrags verlangt,
  // solche Funde aufzunehmen statt abzuzählen.
  const startView = ohneKommentare(readFileSync(join(hier, '../app/StartView.tsx'), 'utf8'));
  assert.doesNotMatch(startView, /verlustfrei/, 'das Versprechen ist auch hier weg');
  assert.match(startView, /Fenster, Türen, Treppen und Heizkörper setzt der Experte ins Gebäude/,
    'was wirklich geht — und es deckt sich mit den vier Arten in `KonfigArt`');
  assert.match(startView, /sonst entsteht eine Datei zum Herunterladen/);
});

test('die Aussage der fünften Stelle deckt sich mit dem, was der Konfigurator kann', () => {
  // Vier Arten, vier Platzierungswege — die Zusage ist nicht größer als die Funktion.
  assert.match(wizard, /export type KonfigArt = 'fenster' \| 'tuer' \| 'treppe' \| 'heizkoerper'/);
  assert.match(wizard, /Heizkörper „\$\{wahl\.label\}" ins Modell gesetzt/);
  assert.match(wizard, /Treppe „\$\{wahl\.label\}" ins Modell gesetzt/);
  assert.match(wizard, /auf die gewählte Wand gesetzt/);
});

// --- Was NICHT angefasst wurde ------------------------------------------------------------------
test('kein Umbau: Schritte, Bauarten und die Struktur bleiben', () => {
  assert.match(wizard, /const SCHRITTE = \['Bauart', 'Maße', 'Material', 'Prüfung', 'Übernehmen'\]/);
  assert.match(wizard, /erzeugeConfiguratorPackage|configuratorPackage/, 'die Paket-Struktur bleibt');
});
