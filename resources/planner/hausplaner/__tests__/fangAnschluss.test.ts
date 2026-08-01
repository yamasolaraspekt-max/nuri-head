/**
 * Z-02 — **der Fang-Kern ist angeschlossen, und die Toleranz hängt am Zoom.**
 *
 * ---
 *
 * **Warum eine eigene Datei und nicht ein Anhang an `fangKern.test.ts`.** K-04 verlangt, dass die
 * zwölf Zusagen des Kerns *„grün und unverändert in der Zahl"* bleiben. Hier hinein zu schreiben
 * hiesse, genau die Zahl zu bewegen, die als Schranke dient. **Und die Trennung ist auch
 * inhaltlich richtig:** `fangKern.test.ts` prüft, *dass der Kern rechnet*; diese Datei prüft,
 * *dass ihn jemand benutzt*. Das sind zwei Aussagen, und die zweite war zwei Wochen lang falsch,
 * während die erste zwölfmal grün war.
 *
 * ---
 *
 * **Die Mutationsprobe VOR diesen Zusagen — 8 Mutationen, SECHS kamen durch:**
 *
 * ```text
 * BLIND (6)  Toleranz mit dem Zoom MULTIPLIZIERT statt geteilt
 *            `aktiv` ignoriert - der Fang liesse sich nicht mehr ausschalten
 *            FANG_PX von 12 auf 1200
 *            nur Wandanfaenge als Kandidaten - jedes zweite Wandende faengt nicht mehr
 *            Waechter gegen zoom = 0 entfernt - Toleranz Infinity, alles faengt
 *            Raster abgeschaltet
 * gefangen   y-Spiegelung gedreht (1 rot) · Endpunkt gewinnt nie (3 rot)
 * ```
 *
 * *Die zwei gefangenen trafen bestehende Zusagen über die Zeichenfläche — nicht über den Fang.
 * Von dem, was Z-02 wirklich anfasst, hielt nichts.*
 *
 * ---
 *
 * **Gelesen wird kommentarfrei.** Der Umbau in `HausplanerApp` trägt einen Kommentar, der
 * `wandFangpunkte()` und `aktiv` beim Namen nennt — er erklärt, warum das eine *nicht* benutzt
 * wird. Eine Zusage, die den Rohtext liest, hielte diese Erklärung für Code. **Das ist F-09**, und
 * sie ist heute schon zweimal zugeschnappt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { fange, toleranzAusZoom, FANG_PX } from '../geometry/fangKern';
import { ohneKommentare } from './_zerlegteApp';

const hier = dirname(fileURLToPath(import.meta.url));
const app = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));

// --- K-03: die Toleranz hängt am Zoom, und zwar in der richtigen Richtung -------------------------

/**
 * **Geprüft wird die RECHNUNG, nicht die Zahl** — so verlangt es K-03 ausdrücklich, und aus gutem
 * Grund: eine Zusage auf `12` friert nur ein, was gebaut wurde (F-06), und sagt über die Wirkung
 * nichts. Die Wirkung ist: *zehnmal weiter herausgezoomt heisst zehnmal grössere Welt-Toleranz.*
 *
 * Mutation „Toleranz MIT dem Zoom multipliziert" kam durch. Sie dreht genau diese Richtung um —
 * der Fang wäre beim Herauszoomen noch toter als vorher.
 */
test('K-03: zehnfacher Zoom-Unterschied ergibt zehnfache Welt-Toleranz', () => {
  const weit = toleranzAusZoom(0.02);
  const nah = toleranzAusZoom(0.2);

  assert.ok(Math.abs(weit / nah - 10) < 1e-9,
    `Faktor ${weit / nah} statt 10 — die Toleranz haengt nicht mehr linear am Zoom`);
  assert.ok(weit > nah,
    'weit herausgezoomt ergibt eine KLEINERE Welt-Toleranz — die Richtung ist verdreht');
});

/**
 * **Der Wächter gegen `zoom = 0`.** Mutation „Wächter entfernt" kam durch: ohne ihn liefert die
 * Division `Infinity`, und eine unendliche Toleranz fängt jeden Punkt auf den nächstbesten
 * Endpunkt. *Das sähe nicht nach einem Fehler aus, sondern nach einem sehr eifrigen Fang.*
 */
test('K-03: bei Zoom 0 oder negativ bleibt die Toleranz endlich', () => {
  for (const z of [0, -1, Number.NaN]) {
    const t = toleranzAusZoom(z);
    assert.ok(Number.isFinite(t), `zoom=${z} ergibt ${t} — eine unendliche Toleranz faengt alles`);
    assert.ok(t > 0, `zoom=${z} ergibt ${t} — eine Toleranz von null oder weniger faengt nie`);
  }
});

/**
 * **Keine Zusage auf die 12 — eine auf die Grössenordnung.** Mutation „FANG_PX auf 1200" kam
 * durch. 1200 Bildschirmpixel sind mehr als die halbe Fensterbreite; der Zeiger klebte dann
 * dauerhaft an irgendeinem Wandende. Umgekehrt ist ein Radius unter etwa vier Pixeln mit der Maus
 * nicht mehr zu treffen. **Die Schranke ist bewusst weit** — sie soll eine Gestaltungsentscheidung
 * nicht einfrieren, nur den Unsinn abfangen.
 */
test('K-03: der Fangradius bleibt in einer bedienbaren Groessenordnung', () => {
  assert.ok(FANG_PX >= 4 && FANG_PX <= 40,
    `${FANG_PX} px ist kein bedienbarer Fangradius — unter 4 px trifft niemand, ueber 40 px klebt der Zeiger`);
});

// --- K-01 / K-02: angeschlossen heisst BEIDES ------------------------------------------------------

/**
 * **Die Zahl aus K-01 allein ist auch durch einen unbenutzten Import erreichbar** — das steht so
 * im Blatt. Deshalb prüft diese Zusage den Aufruf, nicht den Import, und die nächste prüft, dass
 * die alte Wahrheit wirklich weg ist. *Eines allein heisst nichts.*
 */
test('K-01: die Hauptansicht RUFT den Kern auf, sie importiert ihn nicht nur', () => {
  assert.match(app, /import \{ fange, toleranzAusZoom \} from '\.\.\/geometry\/fangKern'/,
    'der Kern wird nicht mehr importiert');
  assert.match(app, /fange\(\s*\{ x, y \}/, 'der Kern wird importiert, aber nicht aufgerufen');
  assert.match(app, /toleranzMm: toleranzAusZoom\(zoom\)/,
    'die Toleranz kommt nicht mehr aus dem Zoom — dann ist der Anschluss nur halb');
});

test('K-02: die zweite Fang-Wahrheit im Bauteil ist weg', () => {
  assert.doesNotMatch(app, /hypot\(p\.x - x, p\.y - y\)/,
    'die alte Endpunkt-Schleife steht wieder da — zwei Fang-Wahrheiten nebeneinander');
  assert.doesNotMatch(app, /<= 150\b/, 'der fest verdrahtete 150-mm-Radius ist zurueck');
  // presence-Partner nach R2: die Funktion, in der beides stand, gibt es ueberhaupt noch.
  assert.match(app, /function weltPunkt\(/, 'weltPunkt ist verschwunden — die Zusage misst Leere');
});

// --- Die Operanden, die die Hauptansicht liefert ---------------------------------------------------

/**
 * Mutation „nur Wandanfänge als Kandidaten" kam durch. **Jedes zweite Wandende fienge dann nicht
 * mehr** — und zwar lautlos: der Raster-Fang springt ein und liefert einen plausibel aussehenden
 * Punkt. *Ein Fang, der die Hälfte verfehlt, ist schwerer zu bemerken als einer, der ganz fehlt.*
 */
test('Z-02: beide Enden jeder Wand sind Fang-Kandidaten', () => {
  assert.match(app, /kandidaten\.push\(w\.start, w\.end\)/,
    'nicht mehr beide Wandenden werden als Kandidaten gereicht');
});

/**
 * **Die Grenze zu Z-04, und sie ist verführerisch.** `wandFangpunkte()` liegt fertig im Kern und
 * liefert Endpunkte *und Mittelpunkte*. Sie hier zu benutzen wäre eine Zeile weniger — und brächte
 * stillschweigend den Mittelpunkt-Fang mit, der eine eigene, noch nicht abgenommene Scheibe ist.
 */
test('Z-02 (Grenze): der Mittelpunkt-Fang aus Z-04 wird NICHT mitgebaut', () => {
  assert.doesNotMatch(app, /wandFangpunkte/,
    'die Hauptansicht benutzt wandFangpunkte() — das bringt den Mittelpunkt-Fang aus Z-04 mit');
  assert.doesNotMatch(app, /ortho:/,
    'der Ortho-Fang wird mitgereicht — auch das ist eine andere Scheibe');
});

/**
 * Mutation „`aktiv` ignoriert" kam durch: der Schalter in den Einstellungen hätte keine Wirkung
 * mehr. Mutation „Raster abgeschaltet" ebenso — dann rastet nichts mehr auf das Gitter, und der
 * Nutzer setzt Punkte auf krumme Millimeter, ohne dass es auffällt.
 */
test('Z-02: Abschalter und Rasterweite kommen aus den Einstellungen, nicht aus Konstanten', () => {
  assert.match(app, /aktiv: scene\.settings\.snapEnabled/,
    'der Fang haengt nicht mehr am Schalter der Einstellungen — er liesse sich nicht ausschalten');
  assert.match(app, /raster: scene\.settings\.gridSize \|\| 100/,
    'die Rasterweite kommt nicht mehr aus den Einstellungen');
});

// --- Was der Kern daraus macht, an einem gerechneten Fall -----------------------------------------

/**
 * **Der Durchstich: dieselben Operanden, die die Hauptansicht reicht, durch den echten Kern.**
 *
 * Zwei Wandenden bei (0,0) und (5000,0), Zeiger 80 mm daneben.
 *
 * ```text
 * Zoom 0.02   Toleranz 600 mm   80 mm liegen drin    -> Endpunkt
 * Zoom 0.50   Toleranz  24 mm   80 mm liegen drauss. -> Raster
 * ```
 *
 * *Genau dieser Unterschied ist der Zweck von Z-02. Mit den alten festen 150 mm fienge beides
 * gleich — und beim Herauszoomen wären 150 mm drei Bildschirmpixel gewesen.*
 */
test('Z-02 (Durchstich): derselbe Punkt faengt weit herausgezoomt, nah herangezoomt nicht', () => {
  const kandidaten = [{ x: 0, y: 0 }, { x: 5000, y: 0 }];
  const zeiger = { x: 80, y: 0 };

  const weit = fange(zeiger, kandidaten, { toleranzMm: toleranzAusZoom(0.02), raster: 100, aktiv: true });
  assert.equal(weit.art, 'endpunkt', 'weit herausgezoomt faengt das Wandende nicht mehr');
  assert.deepEqual(weit.punkt, { x: 0, y: 0 });

  const nah = fange(zeiger, kandidaten, { toleranzMm: toleranzAusZoom(0.5), raster: 100, aktiv: true });
  assert.equal(nah.art, 'raster', 'nah herangezoomt zieht der Fang den Zeiger immer noch von weit her an');

  const aus = fange(zeiger, kandidaten, { toleranzMm: toleranzAusZoom(0.02), raster: 100, aktiv: false });
  assert.equal(aus.art, 'keiner', 'der ausgeschaltete Fang faengt trotzdem');
});
