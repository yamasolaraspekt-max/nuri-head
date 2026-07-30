/**
 * AUF-83-T3 / K-05b — **die Palette bekommt einen Einstieg, den man sieht.**
 *
 * Das Kriterium ist der Ersatz für das abgetrennte K-06 (jetzt `AUF-85`), und es ist kleiner:
 * **nur im Expertenmodus**, weil `HausplanerStudio.tsx:140` die App nur dort einhängt — in
 * *Übersicht* und *Geführt* gibt es die Palette gar nicht.
 *
 * **Die Grenze ist die eigentliche Aussage: erreichbar machen, nicht bauen.** `oeffnePalette`
 * liegt lokal in `HausplanerApp`; der Knopf ruft genau ihn. Eine zweite Aktivierungslogik wäre
 * der Fehler, den das Blatt ausdrücklich verbietet — und sie sähe im Quelltext nach Fleiß aus.
 *
 * **Warum die Zusagen den ORT mitprüfen:** der Knopf sitzt in der Arbeitsbereich-Zeile, nicht in
 * der Werkzeugzeile darunter. Das ist keine Geschmacksfrage — `eineWerkzeugzeile.test.ts` hält
 * aus AUF-70 fest, dass diese Zeile **sechzehn** Knöpfe trägt (2·3·6·4·1). Ein siebzehnter dort
 * hätte eine abgenommene Zusage gebrochen. *Der Ort ist hier Teil der Wirkung.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
/** **Ohne Kommentare gemessen** — die Erklärung nebenan nennt `oeffnePalette` und `⌘K` beim
 *  Namen, und ein Test, der rohen Text durchsucht, hielte meinen eigenen Kommentar für Code.
 *  Die Falle hat in diesem Zyklus mehrfach zugeschlagen. */
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const app = ohneKommentare((readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8')
  // AUF-48 Scheibe 4a: der Kopfrahmen (Werkzeugzeile, Arbeitsbereich-Waehler,
  // Bedien-Werkzeugleiste) ist nach `dashboard/Kopfrahmen.tsx` ausgezogen. **Beide Dateien
  // werden gelesen** — die geprueften Eigenschaften sind unveraendert, und eine Absenz-Zusage
  // darf nicht dadurch gruen werden, dass Inhalt eine Datei weiter gewandert ist.
  + readFileSync(join(hier, '../app/dashboard/Kopfrahmen.tsx'), 'utf8')));
/**
 * AUF-48 Scheibe 3: die Zuordnung „welche Taste bedeutet was" wohnt jetzt in `tastenAbsicht.ts`.
 * **Die geprüfte Eigenschaft ist unverändert** — ⌘K öffnet die Palette, und der Knopf ist ein
 * zweiter Griff an derselben Klinke. *Nur liegt der Beleg dafür jetzt in zwei Dateien.*
 */
const tasten = ohneKommentare(readFileSync(join(hier, '../app/tastenAbsicht.ts'), 'utf8'));
const css = readFileSync(join(hier, '../hausplaner.css'), 'utf8');

/** Die Arbeitsbereich-Zeile: von ihrer Überschrift bis zum Beginn der Werkzeugzeile. */
function arbeitszeile(): string {
  const start = app.indexOf('Arbeitsbereich</span>');
  const ende = app.indexOf('<OpGruppe name="Verlauf">', start);
  assert.ok(start > 0 && ende > start, 'die Arbeitsbereich-Zeile wurde nicht gefunden');
  return app.slice(start, ende);
}

// --- K-05b: der Einstieg ist da, und er ist SICHTBAR ----------------------------------------------

test('K-05b: die Arbeitszeile trägt einen sichtbaren Einstieg in die Palette', () => {
  const z = arbeitszeile();
  assert.match(z, /className="hp-az-suchen"/, 'kein Einstieg in der Arbeitsbereich-Zeile');
  assert.match(z, />\s*Suchen/, 'der Einstieg trägt kein lesbares Wort — ein Icon allein sagt es nicht');
  assert.match(z, /⌘K/, 'das Kürzel steht nicht am Knopf — dann lernt es niemand');
});

test('K-05b: er ruft `oeffnePalette` — denselben Aufruf wie das Kürzel', () => {
  // **Die Wirkung, nicht die Gestalt.** Ein Knopf, der `setPaletteOffen(true)` selbst setzt, sähe
  // im Bild identisch aus und wäre genau die zweite Logik, die die Grenze verbietet.
  assert.match(arbeitszeile(), /onClick=\{oeffnePalette\}/, 'der Einstieg ruft nicht `oeffnePalette`');
});

test('K-05b (Grenze): es gibt weiterhin GENAU EINEN Ort, der die Palette öffnet', () => {
  // **Der eigentliche Schutz.** Die Zusage oben bliebe grün, wenn jemand daneben einen zweiten
  // Weg legte. Gezählt wird deshalb, wie oft der Zustand überhaupt auf „offen" gesetzt wird:
  // einmal, in `oeffnePalette` selbst.
  const setzer = [...app.matchAll(/setPaletteOffen\(true\)/g)];
  assert.equal(setzer.length, 1, `${setzer.length} Stellen öffnen die Palette — erwartet genau eine`);
  const ref = [...app.matchAll(/paletteOffenRef\.current = true/g)];
  assert.equal(ref.length, 1, 'auch der Spiegel wird nur an einer Stelle gesetzt');
  // presence-Partner nach R2: die Funktion, auf die sich das alles bezieht, gibt es noch.
  assert.match(app, /const oeffnePalette = React\.useCallback\(/, '`oeffnePalette` ist fort');
  // Und das Kürzel benutzt sie unverändert weiter — der Knopf ist ein ZWEITER Griff an
  // DERSELBEN Klinke, kein Ersatz.
  //
  // **AUF-48-S3: die Kette läuft jetzt über zwei Dateien**, und beide Glieder werden geprüft.
  // Vorher stand `e.key.toLowerCase() === 'k'` direkt neben `oeffnePalette()`; heute bildet
  // `tastenAbsicht` die Taste auf eine Absicht ab, und die Komponente führt sie aus.
  // *Die Aussage ist dieselbe: ⌘K landet bei `oeffnePalette`, und nirgends sonst.*
  assert.match(tasten, /kleines === 'k'[\s\S]{0,120}?'palette-oeffnen'/,
    '⌘K bildet nicht mehr auf die Absicht `palette-oeffnen` ab');
  assert.match(app, /case 'palette-oeffnen':[\s\S]{0,120}?oeffnePalette\(\)/,
    'die Absicht `palette-oeffnen` ruft `oeffnePalette` nicht mehr');
});

// --- Der Ort ist Teil der Wirkung -----------------------------------------------------------------

test('K-05b: der Einstieg steht NICHT in der Werkzeugzeile — AUF-70 hält sie bei sechzehn', () => {
  // **Warum diese Zusage hier steht und nicht in `eineWerkzeugzeile`:** jene Datei prüft, dass die
  // Werkzeugzeile sechzehn Knöpfe hat. Sie würde rot, wenn ich danebengriffe — aber sie sagt
  // nicht, WARUM der Suchen-Knopf woanders sitzt. Dieser Test hält den Grund fest, damit der
  // nächste Umbau ihn nicht für Zufall hält.
  const start = app.indexOf('<OpGruppe name="Verlauf">');
  const ende = app.indexOf('Zoom {(zoom * 100)', start);
  assert.ok(start > 0 && ende > start, 'die Werkzeugzeile wurde nicht gefunden');
  const werkzeugzeile = app.slice(start, ende);
  assert.ok(!werkzeugzeile.includes('hp-az-suchen'),
    'der Einstieg ist in die Werkzeugzeile gerutscht — dort bricht er die 16-Knopf-Zusage aus AUF-70');
});

// --- Die Auflage des Blattes: className statt Inline-Stil ------------------------------------------

test('K-05b: das neue Markup trägt `className`, keinen Inline-Stil', () => {
  // Auflage aus dem Blatt vom 30.07., 06:20: neues Markup in `HausplanerApp` zieht an AUF-38 mit,
  // statt Scheibe 7 wachsen zu lassen. **Geprüft wird der Knopf selbst**, nicht die Datei —
  // die trägt 78 offene Stellen, und die sind nicht meine.
  const knopf = arbeitszeile().match(/<button[\s\S]*?>/);
  assert.ok(knopf, 'der Knopf wurde nicht gefunden');
  assert.ok(!knopf[0].includes('style={{'),
    `der neue Knopf trägt einen Inline-Stil: ${knopf[0].slice(0, 120)}`);
});

test('K-05b: die Klassen sind in der Stilschicht auch definiert', () => {
  // Ohne diesen Partner wäre die Zusage oben auch grün, wenn der Knopf eine Klasse trüge, die es
  // nirgends gibt — dann stünde er ungestylt in der Zeile.
  for (const klasse of ['hp-az-suchen', 'hp-az-kuerzel']) {
    assert.match(css, new RegExp(`\\.${klasse}\\s*[,{:]`), `\`.${klasse}\` fehlt in hausplaner.css`);
  }
});

test('K-05b: die Stilschicht nennt keine Rohfarbe — nur Tokens', () => {
  // Die generische Barriere aus AUF-38 gilt auch für neue Regeln. Gemessen wird der eigene Block.
  const block = css.slice(css.indexOf('.hp-az-suchen'));
  const roh = [...block.matchAll(/#[0-9a-fA-F]{3,8}\b|rgba?\(/g)].map((m) => m[0]);
  assert.deepEqual(roh, [], `Rohfarbe in der neuen Stilschicht: ${roh.join(', ')}`);
});
