/**
 * AUF-83-T1a — **die Breite lernt, was die Höhe schon kann.**
 *
 * Der Aufbau folgt `buehnenHoehe.test.ts`: erst die Kanten der reinen Rechnung, dann die Regel,
 * die sich das Modul selbst gibt — **keine Pixelkonstante für eine Schiene** —, geprüft am
 * Quelltext. Dieselbe Form, weil es dieselbe Lösung auf der anderen Achse ist.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  buehnenBreite, freieBreite, ERSATZ_BREITE, MIN_BREITE, SCHIENEN_MERKMAL,
} from '../app/dashboard/buehnenBreite';

const hier = dirname(fileURLToPath(import.meta.url));
const modul = readFileSync(join(hier, '../app/dashboard/buehnenBreite.ts'), 'utf8');
const app = (readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8')
  // AUF-48 Scheibe 4a: der Kopfrahmen (Werkzeugzeile, Arbeitsbereich-Waehler,
  // Bedien-Werkzeugleiste) ist nach `dashboard/Kopfrahmen.tsx` ausgezogen. **Beide Dateien
  // werden gelesen** — die geprueften Eigenschaften sind unveraendert, und eine Absenz-Zusage
  // darf nicht dadurch gruen werden, dass Inhalt eine Datei weiter gewandert ist.
  + readFileSync(join(hier, '../app/dashboard/Kopfrahmen.tsx'), 'utf8'));

// --- K-01: die Fensterkonstante ist fort ---------------------------------------------------------

test('K-01: die Breitenrechnung nennt kein `innerWidth` mehr', () => {
  // **Die Wirkung, nicht die Gestalt:** nicht „die neue Zeile existiert", sondern „die alte
  // Rechnung gibt es nicht mehr". Eine Zusage, die nur das Neue prüft, bleibt grün, wenn das Alte
  // danebenstehen bleibt.
  //
  // **Nachgeschärft in AUF-83-T5.** `innerWidth` kommt seither ein zweites Mal vor —
  // `useIstSchmal` fragt `window.innerWidth < 1024` für die Overlay-Schwelle (K-05), das ist
  // KEIN Zusammenzählen von Schienenbreiten. Die alte Regression hatte die Form `innerWidth - Zahl`
  // (eine Subtraktion); genau die bleibt verboten, ein Vergleich (`<`, `>=`, …) ist eine andere
  // Aussage und nicht die Konstante, die dieses Kriterium verbietet.
  const codeZeilen = app.split('\n').filter((z) => !z.trim().startsWith('*') && !z.trim().startsWith('//'));
  const rechnung = codeZeilen.filter((z) => z.includes('const') && /innerWidth\s*-\s*\d/.test(z));
  assert.deepEqual(rechnung, [], `die Fensterrechnung (Subtraktion) steht noch:\n${rechnung.join('\n')}`);
});

test('K-01 (presence-Partner): die Bühne bezieht ihre Breite aus dem Modul', () => {
  // Ohne diesen Partner wäre die Zusage oben auch grün, wenn jemand die Zeile ersatzlos löscht.
  assert.match(app, /const breite = buehnenBreite\(gemesseneBreite\)/);
  assert.match(app, /useGemesseneBreite\(inhaltRef\)/, 'gemessen wird die Inhaltsreihe');
});

// --- K-02: dasselbe Muster wie die Höhe ----------------------------------------------------------

test('K-02: das Modul trägt KEINE Pixelkonstante für eine Schiene', () => {
  // **Die Regel, die `buehnenHoehe.ts` sich selbst gibt** — hier wörtlich übernommen. Stünde eine
  // Schienenbreite im Modul, wäre die alte Konstante nur umgezogen.
  const ohneKommentare = modul.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
  for (const zahl of ['220', '268', '488']) {
    assert.doesNotMatch(ohneKommentare, new RegExp(`\\b${zahl}\\b`),
      `${zahl} steht im Modul — das ist die alte Konstante an neuem Ort`);
  }
});

test('K-02: die Schienen melden sich selbst, statt gezählt zu werden', () => {
  // **Nachgebessert in AUF-83-T1a-N1 (K-02): auf Wortgrenze geprüft.** Die erste Fassung suchte
  // `data-schiene` als Teilzeichenkette — und `data-schienex` enthält sie. Der Evaluator hat es
  // an seiner eigenen Mutation bemerkt: sie blieb grün, und er hat das ehrlich als *„meine
  // Mutation war unwirksam"* ausgewiesen statt als bestandene Prüfung.
  //
  // **Es ist die zweite Ausprägung derselben Sache:** Scheibe 8a hatte dieselbe Präfix-Schwäche
  // in der Klassen-Zusage (`hp-ef-wert` steckt in `hp-ef-wertzeile`). *Zweimal dieselbe Lücke aus
  // demselben Grund — ein `includes` misst Enthaltensein, keine Identität.*
  assert.equal(SCHIENEN_MERKMAL, 'data-schiene');
  const treffer = [...app.matchAll(/data-schiene(?![\w-])/g)];
  assert.ok(treffer.length >= 2, `nur ${treffer.length} Schiene(n) markiert — links und rechts erwartet`);
});

// --- K-01: die Höhe hängt nicht am Modus ---------------------------------------------------------

test('K-01: die Inselhöhe trägt keinen Modus-Ternär mehr', () => {
  // **Das Gegenstück zur Breiten-Zusage, und bis jetzt fehlte es.** T1a hat
  // `height: imStudio ? '100%' : '100vh'` auf `height: '100%'` gebracht — belegt war das nur durch
  // einen `grep` zum Abnahmezeitpunkt. **Der Evaluator hat gemessen, dass die Mutation die Suite
  // grün lässt**, und genau das macht den Unterschied zwischen einer Eigenschaft und einer Absicht.
  //
  // Warum die Eigenschaft zählt: mit T1b sitzt die Insel in `.main-content-scroll`. Eine
  // Fensterhöhe erzeugt dort einen **zweiten Bildlauf** und schiebt den Zeichenbereich unter die
  // Falz — auf der Objektseite genauso wie im Studio.
  const codeZeilen = app.split('\n').filter((z) => !z.trim().startsWith('*') && !z.trim().startsWith('//'));
  const mitVh = codeZeilen.filter((z) => z.includes('100vh'));
  assert.deepEqual(mitVh, [], `die Fensterhöhe ist zurück:\n${mitVh.join('\n')}`);
  const ternaer = codeZeilen.filter((z) => /height:\s*imStudio\s*\?/.test(z));
  assert.deepEqual(ternaer, [], `die Höhe hängt wieder am Modus:\n${ternaer.join('\n')}`);
  // presence-Partner nach R2: die Stelle gibt es überhaupt noch.
  assert.match(app, /height: '100%'/, 'die Höhenangabe ist ersatzlos fort — dann prüft das hier nichts');
});

// --- K-03: der Ersatz für das verlorene K-07 -----------------------------------------------------

test('K-03: bei unveränderten Schienen rechnet die Messung wie die alte Formel', () => {
  // **Der Ersatz für ein Kriterium, das nicht mehr prüfbar ist.** T1a K-07 verlangte
  // Bildschirmfotos gegen den Stand *vor* T1a — den kann niemand mehr ausliefern, seit T1b das
  // Bild absichtlich verändert hat. **Ein Bild ist hier ohnehin der schwächere Beleg:** es zeigt,
  // dass es gleich *aussieht*; die Rechnung zeigt, dass es gleich *ist*.
  //
  // Die alte Formel war `innerWidth − 220 − 268`. Bei unveränderten Schienenbreiten muss die
  // Messung denselben Wert liefern — sonst hätte T1a das Bild verschoben, statt nur das Verfahren
  // zu wechseln.
  const ALT = (fenster: number): number => fenster - 220 - 268;
  for (const fenster of [1440, 1920, 1280, 1024, 800]) {
    assert.equal(freieBreite(fenster, [220, 268]), ALT(fenster),
      `bei ${fenster} px weicht die Messung von der alten Rechnung ab`);
  }
  // Der bekannte Wert aus der Sichtprobe, ausgeschrieben: 1440 − 488 = 952.
  assert.equal(freieBreite(1440, [220, 268]), 952);

  // **Und die Gegenrichtung, die den Sinn des Umbaus trägt:** sobald sich eine Schiene ändert,
  // MUSS die Messung von der alten Formel abweichen. Täte sie es nicht, wäre die Rechnung nur
  // umgeschrieben und nicht behälterbezogen.
  assert.notEqual(freieBreite(1440, [220, 0]), ALT(1440), 'ein zugeklapptes Panel ändert nichts?');
  assert.notEqual(freieBreite(900, [220, 268]), ALT(1440), 'ein schmalerer Behälter ändert nichts?');
});

test('K-02: ohne Messung gilt die Ersatzbreite, nicht die Null', () => {
  // Die Kante aus `buehnenHoehe`: beim ersten Rendern ist gemessen 0. Eine Bühne mit Breite 0 ist
  // ein leerer Bildschirm.
  assert.equal(buehnenBreite(null), ERSATZ_BREITE);
  assert.equal(buehnenBreite(0), ERSATZ_BREITE);
  assert.equal(buehnenBreite(-5), ERSATZ_BREITE);
});

test('K-02: eine echte Messung gilt — nur unter der Mindestbreite wird angehoben', () => {
  assert.equal(buehnenBreite(900), 900);
  assert.equal(buehnenBreite(MIN_BREITE), MIN_BREITE);
  assert.equal(buehnenBreite(MIN_BREITE - 1), MIN_BREITE, 'zu schmal wird angehoben, nicht durchgereicht');
});

test('K-02: die Ersatzbreite ist die alte Rechnung ohne Fenster — nichts verschiebt sich', () => {
  assert.equal(ERSATZ_BREITE, 1200 - 220 - 268, 'sonst wandern bestehende Testwerte');
});

// --- freieBreite: die Rechnung selbst ------------------------------------------------------------

test('freieBreite zieht ab, was die Schienen wirklich einnehmen', () => {
  assert.equal(freieBreite(1440, [220, 268]), 952);
  assert.equal(freieBreite(900, [220, 268]), 412, 'ein schmalerer Behälter ergibt eine schmalere Bühne');
});

test('freieBreite: eine zugeklappte Schiene gibt ihren Platz frei', () => {
  // **Der Fall, für den dieses Modul gebaut ist.** Klappt das rechte Panel zu, ändert sich das
  // Fenster nicht — die alte Rechnung hätte es nie bemerkt.
  assert.equal(freieBreite(1440, [220, 0]), 1220);
  assert.equal(freieBreite(1440, []), 1440, 'ohne Schienen gehört die ganze Reihe der Bühne');
});

test('freieBreite rundet ab und wird nie negativ', () => {
  assert.equal(freieBreite(1000.9, [220.4]), 780, 'ein aufgerundetes Pixel steht rechts wieder heraus');
  assert.equal(freieBreite(300, [220, 268]), 0, 'kein negativer Wert, auch wenn die Schienen breiter sind');
});
