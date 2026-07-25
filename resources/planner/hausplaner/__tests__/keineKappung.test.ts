/**
 * AUF-26 (B3/B4) — Kappungs-Schutz für textführende Flächen.
 *
 * Anlass ist der teuerste Befund des Tages: `PANEL_TABS` hat vier Einträge, sechs Tests belegen das —
 * und auf dem Schirm war der vierte Reiter bei 1375 px **unsichtbar**. Ein Kriterium war grün, die
 * Sache war es nicht. Dieser Test schließt genau diese Lücke, so weit sie ohne Browser messbar ist:
 * er prüft die **Ursache** (die CSS-Eigenschaften, die kappen) statt der Wirkung (der Screenshot).
 *
 * Die Sichtprobe in 1440/1024/375 px bleibt Pflicht und ersetzt diesen Test nicht — sie ist Sache
 * des messenden Strangs. Was hier steht, verhindert nur den Rückfall.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
const app = readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8');
const navi = readFileSync(join(hier, '../app/FaehigkeitenNavi.tsx'), 'utf8');

test('B3: die Reiterzeile bricht um — sie kappt nicht', () => {
  // zeilenweise statt über `}`-Grenzen: die Stile enthalten `${…}`-Templates, an denen eine
  // `[^}]*`-Klammer abbricht — genau daran ist mein erster Testentwurf gescheitert.
  const zeile = app.split('\n').find((l) => l.includes('role="tablist"'));
  assert.ok(zeile, 'Reiterzeile nicht gefunden');
  assert.match(zeile, /flexWrap: 'wrap'/, 'ohne Umbruch verschwindet der vierte Reiter aus dem Bild');
});

test('B3: das Eigenschaften-Panel bricht lange Wörter um, statt sie abzuschneiden', () => {
  const panel = app.split('\n').find((l) => l.includes('width: 268,'));
  assert.ok(panel, 'Panel-Container nicht gefunden');
  assert.match(panel, /overflowWrap: 'anywhere'/, 'sonst bricht der Hinweistext mitten im Wort ab');
  assert.match(panel, /boxSizing: 'border-box'/, 'Padding darf die 268 px nicht sprengen');
});

test('B3: die Spiegel-Schaltflächen brechen um, statt „↕ Oben/Unten" zu kappen', () => {
  // Eindeutig über den Beschriftungstext: `spiegeleGrundriss('vertikal')` steht ZWEIMAL in der
  // Datei — einmal als Icon-Knopf in der Werkzeugleiste, einmal als beschrifteter Knopf im Panel.
  // Gekappt wurde der im Panel; ein `findIndex` auf den Aufruf trifft den falschen.
  const zeilen = app.split('\n');
  const i = zeilen.findIndex((l) => l.includes('↔ Links/Rechts'));
  assert.ok(i > 0, 'Spiegel-Zeile im Panel nicht gefunden');
  assert.match(zeilen[i - 1], /flexWrap: 'wrap'/, 'die umgebende Zeile muss umbrechen dürfen');
  assert.equal((app.match(/flex: '1 1 108px'/g) ?? []).length, 2, 'beide Schaltflächen mit Mindestbreite');
});

test('B4: das Fähigkeiten-Label bricht um — kein ellipsis, kein overflow:hidden', () => {
  const label = navi.match(/<span style=\{\{ flex: 1[^}]*\}\}>\{f\.label\}<\/span>/);
  assert.ok(label, 'Label-Span nicht gefunden');
  assert.doesNotMatch(label[0], /textOverflow: 'ellipsis'/, '„Horizont…" ist informationslos');
  assert.doesNotMatch(label[0], /whiteSpace: 'nowrap'/);
  assert.doesNotMatch(label[0], /overflow: 'hidden'/);
  assert.match(label[0], /overflowWrap: 'anywhere'/);
});

test('B4: die Zeile trägt weiterhin einen title — der Umbruch ersetzt ihn nicht', () => {
  assert.match(navi, /title=\{`\$\{f\.label\}/, 'gekappt oder nicht: der Zusammenhang gehört in den title');
});
