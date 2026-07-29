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
// AUF-27: die Reiterzeile steht seit dem Schienen-Umbau in der gemeinsamen `ReiterLeiste` — eine
// Leiste, zwei Benutzer. Der Messpunkt wandert mit; die Zusage bleibt dieselbe.
const leiste = readFileSync(join(hier, '../app/dashboard/ReiterLeiste.tsx'), 'utf8');
/** Die Stilschicht — seit AUF-38 wohnen die statischen Stile dort, nicht mehr inline. */
const stilschicht = readFileSync(join(hier, '../hausplaner.css'), 'utf8');

test('B3: die Reiterzeile bricht um — sie kappt nicht', () => {
  // Über den Element-Block statt zeilenweise: das Attribut steht auf einer anderen Zeile als der
  // Stil. `[\s\S]*?>` endet am ersten `>` — die Stile enthalten `${…}`-Templates, an denen eine
  // `[^}]*`-Klammer abbräche; genau daran ist mein erster Testentwurf gescheitert.
  // **Nachgezogen in AUF-38 Scheibe 8c:** der Umbruch stand als Inline-Stil und steht jetzt als
  // `.hp-rl-leiste` in `hausplaner.css`. **Die Absicht ist unveraendert** — ohne Umbruch faellt
  // der vierte Reiter aus dem Bild. Geprueft wird die Eigenschaft dort, wo sie wohnt.
  const block = leiste.match(/<div\s*\n\s*role="tablist"[\s\S]*?\n\s*>/);
  assert.ok(block, 'Reiterzeile nicht gefunden');
  assert.match(block[0], /className="hp-rl-leiste"/, 'die Reiterzeile traegt ihre Klasse nicht mehr');
  assert.match(stilschicht, /\.hp-rl-leiste \{[^}]*flex-wrap: wrap[^}]*\}/,
    'ohne Umbruch verschwindet der vierte Reiter aus dem Bild');
  // AUF-27 / Kante 3: drei Reiter in 220 px. Umbrechen, nicht kappen — auch INNERHALB eines Wortes,
  // sonst heisst der dritte Reiter „Fachpla…".
  const knopf = leiste.match(/<button\n[\s\S]*?role="tab"[\s\S]*?\n\s*>/);
  assert.ok(knopf, 'Reiter-Knopf nicht gefunden');
  assert.match(knopf[0], /overflowWrap: 'anywhere'/, 'sonst wird die Beschriftung gekappt');
  assert.doesNotMatch(knopf[0], /textOverflow|whiteSpace: 'nowrap'/, 'Kappen ist ausgeschlossen');
});

test('B3: das Eigenschaften-Panel bricht lange Wörter um, statt sie abzuschneiden', () => {
  const panel = app.split('\n').find((l) => l.includes('width: 268,'));
  assert.ok(panel, 'Panel-Container nicht gefunden');
  assert.match(panel, /overflowWrap: 'anywhere'/, 'sonst bricht der Hinweistext mitten im Wort ab');
  assert.match(panel, /boxSizing: 'border-box'/, 'Padding darf die 268 px nicht sprengen');
});

test('B3: die Spiegel-Schaltflächen können nicht mehr kappen — es gibt sie nicht mehr', () => {
  // AUF-26 hatte die beschrifteten Panel-Knöpfe „↔ Links/Rechts" / „↕ Oben/Unten" gegen Kappung
  // gesichert. AUF-59 hat sie entfernt: sie waren eine DUBLETTE der beiden Spiegel-Icons in der
  // Bedienzeile — gemessen derselbe Aufruf und dieselbe Sperrbedingung. Was es nicht gibt, kann
  // auch nicht kappen; geprüft wird deshalb der Nachfolgezustand, statt die Zusage stillschweigend
  // fallen zu lassen.
  // Kommentare weg: der Kommentar, der die Entfernung ERKLÄRT, zitiert die Beschriftung — sonst
  // schlägt der Test auf die eigene Begründung an. (Derselbe Fallstrick wie in AUF-27/36.)
  const code = app.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
  assert.ok(!code.includes('↔ Links/Rechts'), 'der Textknopf ist zurück — dann gilt die alte Zusage wieder');
  assert.ok(!code.includes('↕ Oben/Unten'));
  // Die Handlung ist erreichbar geblieben — als Icon mit Tooltip, der nichts kappen kann.
  assert.equal((app.match(/spiegeleGrundriss\('vertikal'\)/g) ?? []).length, 1);
  assert.equal((app.match(/spiegeleGrundriss\('horizontal'\)/g) ?? []).length, 1);
  assert.match(app, /icon="mirror-h"/);
  assert.match(app, /icon="mirror-v"/);
});

test('B4: das Fähigkeiten-Label bricht um — kein ellipsis, kein overflow:hidden', () => {
  // **Nachgezogen in AUF-38 Scheibe 8c:** der Stil des Labels steht jetzt als `.hp-fn-label`.
  // **Die Absicht ist unveraendert:** umbrechen statt kappen — „Horizont…" ist informationslos.
  // Geprueft wird deshalb die Regel in der Schicht, nicht der Inline-Stil.
  const label = navi.match(/<span className="hp-fn-label">\{f\.label\}<\/span>/);
  assert.ok(label, 'Label-Span nicht gefunden — traegt er seine Klasse nicht mehr?');
  const regel = stilschicht.match(/\.hp-fn-label \{[^}]*\}/);
  assert.ok(regel, '.hp-fn-label fehlt in der Stilschicht');
  assert.doesNotMatch(regel[0], /text-overflow: ellipsis/, '„Horizont…" ist informationslos');
  assert.doesNotMatch(regel[0], /white-space: nowrap/);
  assert.doesNotMatch(regel[0], /overflow: hidden/);
  assert.match(regel[0], /overflow-wrap: anywhere/);
});

test('B4: die Zeile trägt weiterhin einen title — der Umbruch ersetzt ihn nicht', () => {
  assert.match(navi, /title=\{`\$\{f\.label\}/, 'gekappt oder nicht: der Zusammenhang gehört in den title');
});
