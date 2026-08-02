/**
 * Z-10 — **Länge tippen statt ziehen, verriegelt.**
 *
 * ---
 *
 * **Die Mutationsprobe VOR diesen Zusagen — 7 Mutationen, SECHS kamen durch:**
 *
 * ```text
 * blind (6)  Laenge 0 zugelassen · negative Laenge zugelassen · keine Richtung zugelassen
 *            Winkel wird ignoriert · Enter uebernimmt nicht · Ziffer oeffnet nicht
 * gefangen   Escape raeumt den ganzen Zug ab (1 rot)
 * ```
 *
 * *Die eine gefangene traf die Escape-Rangfolge — also die Zusage, die im selben Zug nachgezogen
 * wurde. Von dem, was Z-10 NEU baut, hielt nichts.*
 *
 * ---
 *
 * **Was hier NICHT geprüft wird und warum das wichtig ist.** `geometry/bemassung.ts` und
 * `geometry/masskette.ts` heißen fast so und existieren längst — sie **zeigen** Maße an. *Das ist
 * die Anzeigerichtung.* Wer die beiden verwechselt, baut ein Kriterium, das vor dem Bau schon
 * grün ist (F-07).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  punktAusLaenge, istBrauchbareLaenge, richtungAus, oeffneMit, tippe, wechsleFeld, massEingabeText,
} from '../geometry/masseingabe';
import { tastenAbsicht } from '../app/tastenAbsicht';
import { ohneKommentare, teil } from './_zerlegteApp';

const app = ohneKommentare(teil('app/HausplanerApp.tsx'));
const START = { x: 1000, y: 2000 };
/** Der Zeiger steht rechts vom Startpunkt — irgendwo, die Entfernung darf nicht zählen. */
const RECHTS = { x: 1400, y: 2000 };

// --- K-05: die Rechnung, an den Fällen, an denen sie bricht ---------------------------------------

test('K-05: 4200 nach rechts landet EXAKT 4200 mm rechts — nicht ungefähr', () => {
  // *Der ganze Zweck der Scheibe:* wer tippt, will das Maß genau, nicht gezogen.
  const p = punktAusLaenge(START, RECHTS, 4200);
  assert.deepEqual(p, { x: 5200, y: 2000 });
  // **Die Entfernung des Zeigers zählt nicht, nur seine Richtung.** Sonst wäre die getippte
  // Länge eine Empfehlung.
  assert.deepEqual(punktAusLaenge(START, { x: 1010, y: 2000 }, 4200), { x: 5200, y: 2000 });
});

test('K-05: Länge 0 und negative Länge werden abgelehnt — kein Punkt', () => {
  // Mutationen „Laenge 0 zugelassen" und „negative Laenge zugelassen" kamen durch. *Null ist
  // keine Länge und negativ ist keine Richtung; beides wäre kein Punkt, sondern ein
  // stehengebliebener Zug.*
  assert.equal(punktAusLaenge(START, RECHTS, 0), null);
  assert.equal(punktAusLaenge(START, RECHTS, -4200), null);
  assert.equal(istBrauchbareLaenge(0), false);
  assert.equal(istBrauchbareLaenge(-1), false);
  // presence-Partner nach B4: eine echte Länge ist sehr wohl brauchbar.
  assert.equal(istBrauchbareLaenge(4200), true);
  assert.notEqual(punktAusLaenge(START, RECHTS, 4200), null);
});

test('K-05: liegt der Zeiger AUF dem Startpunkt, gibt es keine Richtung', () => {
  // Mutation „keine Richtung wird zugelassen" kam durch. **Der Fall tritt bei jedem ersten
  // Tastendruck auf**, solange die Maus sich seit dem Klick nicht bewegt hat. Ohne diesen Zweig
  // teilte die Normierung durch null — und `NaN` sieht in einer Koordinate aus wie ein Punkt,
  // bis jemand die Wand misst.
  // **Direkt an der Entscheidung geprueft** (B3): ueber `punktAusLaenge` blieb die Mutation
  // BLIND, weil die Endlichkeitspruefung den `NaN` ohnehin abfaengt. *Das ist kein Loch in der
  // Zusage, sondern eine Eigenschaft der Lage — und der Vertrag ist trotzdem echt.*
  assert.equal(richtungAus(START, { ...START }), null, 'aufeinanderliegende Punkte ergeben eine Richtung');
  const r = richtungAus(START, RECHTS);
  assert.ok(r && Math.abs(r.ex - 1) < 1e-9 && Math.abs(r.ey) < 1e-9, 'die Richtung wird gar nicht mehr gerechnet');
  assert.equal(punktAusLaenge(START, { ...START }, 4200), null);
  // Aber MIT Winkel geht es, denn dann kommt die Richtung nicht vom Zeiger.
  assert.deepEqual(punktAusLaenge(START, { ...START }, 4200, 0), { x: 5200, y: 2000 });
});

test('K-05: der Winkel SCHLÄGT den Zeiger', () => {
  // Mutation „Winkel wird ignoriert" kam durch. *Wer ihn tippt, meint ihn — sonst wäre die
  // Eingabe eine Bitte und keine Angabe.*
  const p = punktAusLaenge(START, RECHTS, 1000, 90);
  assert.deepEqual(p, { x: 1000, y: 3000 }, 'der Zeiger nach rechts hat den Winkel 90 überstimmt');
});

test('K-05: 360 Grad und 0 Grad ergeben denselben Punkt', () => {
  // Eine Umdrehung ist keine Richtungsänderung. *Wäre das verschieden, hätte dieselbe Eingabe
  // zwei Ergebnisse — und niemand wüsste, welches.*
  assert.deepEqual(punktAusLaenge(START, RECHTS, 1000, 360), punktAusLaenge(START, RECHTS, 1000, 0));
});

test('K-05: eine sehr grosse Länge ergibt keinen Überlauf und kein NaN', () => {
  // 1e9 mm sind 1000 km — unsinnig als Wand, aber tippbar. *Eine Koordinate, die nicht endlich
  // ist, wandert sonst in den Knoten und macht die ganze Szene unbrauchbar.*
  const p = punktAusLaenge(START, RECHTS, 1e9);
  assert.ok(p && Number.isFinite(p.x) && Number.isFinite(p.y), `nicht endlich: ${JSON.stringify(p)}`);
  assert.equal(p.x, 1000 + 1e9);
});

// --- Die Eingabe selbst: rein, damit die Ansicht sie nur anzeigt ----------------------------------

test('Z-10: die Ziffer, die öffnet, steht schon drin — Tab wechselt nur das Feld', () => {
  const e = oeffneMit('4');
  assert.equal(e.laenge, '4');
  assert.equal(e.feld, 'laenge');
  const e2 = tippe(e, '2');
  assert.equal(e2.laenge, '42');
  const e3 = tippe(wechsleFeld(e2), '9');
  assert.equal(e3.winkel, '9', 'Tab hat das Feld nicht gewechselt');
  assert.equal(e3.laenge, '42', 'der Wechsel hat die Länge verändert');
});

test('Z-10: der Satz in der Fussfläche nennt BEIDE Ausgänge', () => {
  // Dieselbe Regel wie beim Konturzug: *eine offene Eingabe ohne Ausweg sieht aus wie eine
  // Sackgasse.*
  const t = massEingabeText(oeffneMit('4'));
  assert.match(t, /Enter setzt/);
  assert.match(t, /Esc verwirft/);
  assert.match(t, /Tab wechselt/);
  // presence-Partner: ohne laufende Eingabe steht dort NICHTS, nicht das Wort „keine".
  assert.equal(massEingabeText(null), '');
});

// --- Die Absicht sitzt in der EINEN Tastenabbildung ------------------------------------------------

test('K-01: eine Ziffer öffnet die Maßeingabe — aber nur während eines laufenden Zugs', () => {
  // Mutation „Ziffer oeffnet nicht" kam durch. Und die Bedingung zählt: **ohne Ausgangspunkt gibt
  // es keine Richtung**, und eine Länge ohne Richtung ist kein Punkt — dann bleibt die Ziffer ein
  // Werkzeug-Kürzel.
  const roh = { ctrlKey: false, metaKey: false, zielIstEingabe: false, paletteOffen: false };
  assert.equal(tastenAbsicht({ ...roh, key: '4', zugLaeuft: true }).art, 'masseingabe-oeffnen');
  assert.equal(tastenAbsicht({ ...roh, key: '4', zugLaeuft: true }).ziffer, '4');
  assert.notEqual(tastenAbsicht({ ...roh, key: '4', zugLaeuft: false }).art, 'masseingabe-oeffnen');
});

test('K-01: solange die Eingabe offen ist, gehören Ziffern, Tab und Enter IHR', () => {
  // *Sonst schlüge „4" das Werkzeug-Kürzel und Enter das Kontur-Schliessen — mitten im Tippen.*
  const offen = { ctrlKey: false, metaKey: false, zielIstEingabe: false, paletteOffen: false, masseingabeOffen: true };
  assert.equal(tastenAbsicht({ ...offen, key: '7' }).art, 'masseingabe-ziffer');
  assert.equal(tastenAbsicht({ ...offen, key: 'Tab' }).art, 'masseingabe-feld');
  assert.equal(tastenAbsicht({ ...offen, key: 'Tab' }).preventDefault, true,
    'ohne preventDefault wandert der Fokus aus der Fläche — und der Zug wäre weg');
  assert.equal(tastenAbsicht({ ...offen, key: 'Enter' }).art, 'masseingabe-uebernehmen');
  // presence-Partner: OHNE offene Eingabe bleibt Enter beim Kontur-Schliessen.
  assert.equal(tastenAbsicht({ ...offen, masseingabeOffen: false, key: 'Enter' }).art, 'kontur-schliessen');
});

// --- Die Verdrahtung: gebaut UND aufgerufen --------------------------------------------------------

test('Z-10: Enter ruft die Übernahme wirklich auf — nicht nur, dass es sie gibt', () => {
  // **Die Klasse, die am 01.08. veröffentlicht hat:** Mechanismus gebaut, Aufruf vergessen.
  // Mutation „Enter uebernimmt nicht" kam durch, solange nur die reine Rechnung geprüft war.
  // *Den Aufruf selbst prüft die Zusage weiter unten — sie kennt den Spiegel, der beim
  // Browsertest nötig wurde. Hier steht nur noch, dass die Übernahme rechnet.*
  assert.match(app, /function uebernimmMass\(\)/, 'die Übernahme selbst ist verschwunden');
  assert.match(app, /punktAusLaenge\(start, cursorRef\.current, Number\(e\.laenge\), winkel\)/,
    'die Übernahme rechnet nicht mehr mit der einen Funktion aus der Geometrie');
  // **Der Fehler, den der Browsertest gefunden hat, und keine Zusage.** Enter erzeugte keine
  // Wand: `uebernimmMass` las `wandStart` und `cursor` aus der Closure des EINMAL angemeldeten
  // Tastenhoerers — also den Stand vom ersten Render. *Kante 8, fuer `konturPunkte` und
  // `werkzeug` bedacht und fuer diese beiden vergessen.*
  assert.match(app, /: wandStartRef\.current;/, 'die Uebernahme liest den Wandanfang wieder aus der Closure');
  assert.match(app, /wandStartRef\.current = wandStart;/, 'der Spiegel wird nicht mehr nachgefuehrt');
  assert.match(app, /cursorRef\.current = cursor;/, 'der Zeiger-Spiegel wird nicht mehr nachgefuehrt');
});

test('Z-10: die Maßeingabe setzt DENSELBEN Punkt wie die Maus — keine zweite Kopie', () => {
  // *Ohne die Trennung `klick` / `setzePunkt` hätte ich die Werkzeug-Zweige ein zweites Mal
  // abgeschrieben — und die zweite Kopie wäre beim nächsten Werkzeug vergessen worden.*
  assert.match(app, /function klick\([^)]*\): void \{\s*\n\s*setzePunkt\(weltPunkt\(e\), e\.evt\);/,
    'der Klick geht nicht mehr über setzePunkt');
  assert.match(app, /setMassEingabe\(null\);\s*\n\s*setzePunkt\(ziel\);/,
    'die Übernahme setzt den Punkt nicht mehr über denselben Weg');
  assert.equal([...app.matchAll(/function setzePunkt\(/g)].length, 1, 'es gibt mehr als einen Setz-Weg');
});

test('Z-10: der Tastenhoerer ruft die AKTUELLE Uebernahme — nicht die vom ersten Render', () => {
  // **Der zweite Fund des Browsertests, und er ging eine Ebene tiefer als der erste.**
  // Nach dem Spiegeln von `wandStart` und `cursor` schloss das Feld — aber es entstand keine
  // Wand. `uebernimmMass` ruft `setzePunkt`, und BEIDE sind Funktionen aus dem Render. Der
  // einmal angemeldete Hoerer hielt die vom ersten: sie sah `wandStart = null` und setzte nur
  // wieder den Anfang.
  //
  // *Einzelne Werte zu spiegeln reicht nicht, wenn die FUNKTION selbst veraltet ist.*
  assert.match(app, /case 'masseingabe-uebernehmen':\s*\n\s*uebernimmMassRef\.current\(\);/,
    'der Hoerer ruft wieder die Fassung aus seiner eigenen Closure');
  assert.match(app, /uebernimmMassRef\.current = uebernimmMass;/,
    'der Spiegel wird nicht mehr bei jedem Render nachgefuehrt — dann zeigt er auf die erste Fassung');
  // presence-Partner nach B4: die Uebernahme, auf die er zeigen soll, gibt es ueberhaupt.
  assert.match(app, /function uebernimmMass\(\): void \{/, 'die Uebernahme ist verschwunden');
});
