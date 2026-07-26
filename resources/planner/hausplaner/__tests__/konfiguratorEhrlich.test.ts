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
test('K3: die Beschreibung nennt das Ergebnis — jetzt zwei Wege, keiner davon versprochen', () => {
  // AUF-81 hat diese Stelle geändert, und zwar in die richtige Richtung: es WIRD jetzt gespeichert.
  // Die Absicht von AUF-74 bleibt — die Fläche sagt, was tatsächlich passiert, nicht was gut klingt.
  const stelle = autark(wizard.match(/\? '(Ergebnis: gespeichert[^']*)'/));
  assert.doesNotMatch(stelle, /verlustfrei/, 'das Versprechen ins Projekt bleibt weg');
  assert.match(stelle, /gespeichert in deiner Paketliste/, 'das ist seit AUF-81 wahr');
  assert.match(stelle, /zusätzlich als Datei zum Herunterladen/, 'der Download bleibt genannt');
  assert.match(stelle, /über den Experten/, 'der Weg ins Gebäude wird weiter genannt');
});

test('K3: die Statuszeile ebenso — sie nennt beide Wege', () => {
  const stelle = autark(wizard.match(/\? '(Ergebnis: Paketliste[^']*)'/));
  assert.doesNotMatch(stelle, /speicherbar/, 'das alte Möglichkeitswort bleibt weg');
  assert.match(stelle, /Paketliste \+ Datei/);
});

test('K3: die Meldung nach dem Klick sagt, was WIRKLICH geschehen ist — Weg für Weg', () => {
  // AUF-81: es gibt jetzt zwei Wege, und jeder wird EINZELN gemeldet. Ein „gespeichert", das nur
  // den Versuch meldet oder den anderen Weg mitmeint, wäre der alte Fehler mit neuem Vorzeichen.
  assert.match(wizard, /if \(gespeichert\) teile\.push\('in deiner Paketliste gespeichert'\)/);
  assert.match(wizard, /if \(entstanden\) teile\.push\(`als Datei „\$\{dateiname\}" heruntergeladen`\)/);
  assert.match(wizard, /teile\.length > 0/, 'ohne Ergebnis wird kein Erfolg gemeldet');
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
  // AUF-81: die Meldung hängt jetzt an ZWEI Ausgängen — sie wird aus dem zusammengesetzt, was
  // wirklich geklappt hat, statt an einer einzelnen Bedingung zu hängen.
  assert.match(wizard, /const teile: string\[\] = \[\];/);
  assert.match(wizard, /onÜbernehmen\(teile\.length > 0/, 'die Meldung hängt am tatsächlichen Ausgang');
  // AUF-81: der Fehlerfall deckt jetzt BEIDE Wege ab — schlägt auch das Speichern fehl, sagt die
  // Fläche genau das, statt einen der beiden Wege stillschweigend zu unterstellen.
  const fehler = autark(wizard.match(/: `(\$\{wahl\.label\}: Es ist nichts entstanden[^`]*)`/));
  assert.match(fehler, /weder gespeichert noch heruntergeladen/);
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
