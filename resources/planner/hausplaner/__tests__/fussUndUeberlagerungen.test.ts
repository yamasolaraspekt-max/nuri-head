/**
 * AUF-48 Scheibe 4e / K-06 — **Statusleiste, Befehlspalette und Engine-Fläche, erstmals verriegelt.**
 *
 * ---
 *
 * **Die Probe VOR dem Schreiben — 8 Mutationen, 8 kamen durch:**
 *
 * ```text
 * Palette zeichnet auch geschlossen        keine Zusage rot   (aus dem Blatt)
 * Klick daneben schliesst nicht mehr       keine Zusage rot   (aus dem Blatt)
 * Statusleiste: Zoom falsch gerechnet      keine Zusage rot   (aus dem Blatt)
 * Palette nicht mehr `position: fixed`     keine Zusage rot   (aus dem Blatt)
 * Engine-Flaeche immer sichtbar            keine Zusage rot
 * Zurueck aus der Engine tut nichts        keine Zusage rot
 * Statusleiste: x und y vertauscht         keine Zusage rot
 * Palette ohne Dialog-Rolle (A11y)         keine Zusage rot
 * ```
 *
 * *Gleichauf mit Scheibe 4c (6 von 6). In S4a waren es 9 von 15, in S4b 3 von 8, in S4d 12 von 15.*
 *
 * **Was diese Datei nicht kann:** sie liest Quelltext, sie rendert nicht. *Eine Quelltext-Zusage
 * über ein Overlay ist schwächer als ein geöffnetes Overlay* — sie fängt jede der acht Mutationen,
 * aber sie beweist nicht, dass der Browser daraus dasselbe macht. Dafür steht L-01.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { TEILE, teil, ohneKommentare } from './_zerlegteApp';
import { FANG_TEXT, type FangArt } from '../geometry/fangKern';

/** Die Stilschicht — seit AUF-38-P3 traegt sie einen Teil der Wahrheit dieser Datei. */
const css = teil('hausplaner.css');

const hier = dirname(fileURLToPath(import.meta.url));
const fussRoh = teil('app/rahmen/FussUndUeberlagerungen.tsx');
const fuss = ohneKommentare(fussRoh);
const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

// --- K-05: die Teile-Liste ist vollständig --------------------------------------------------------

test('K-05: JEDES Modul unter `app/rahmen/` steht in der Teile-Liste', () => {
  // **Der Gegenbeweis aus dem Blatt, gefahren und bestätigt:** ich habe eine verbotene Zeichenkette
  // in das neue Modul gesetzt, OHNE es einzutragen — **keine einzige Absenz-Zusage wurde rot.**
  // Mit Eintrag wurde sie rot. *Die handgepflegte Liste ist damit eine echte Einzelstelle, und
  // eine Einzelstelle ohne Zusage ist eine Falle, die genau einmal zuschlägt.*
  //
  // **Warum hier ein `readdir` steht, obwohl der Kopf von `_zerlegteApp.ts` eins ablehnt:** dort
  // ging es ums LESEN der Inhalte — eine mitwachsende Leseliste macht Zusagen zufällig grün. Hier
  // wird nichts gelesen, sondern nur **gezählt, ob die Liste vollständig ist**. Das ist der
  // Unterschied zwischen „automatisch benutzen" und „bemerken, dass etwas fehlt".
  // **Diese Zusage hatte selbst ein Loch, und es war meins.** Sie las `readdir('app/rahmen')` —
  // `app/dashboard/Kopfrahmen.tsx` ist aber ebenfalls ein zerlegtes Teil und stand ausserhalb
  // ihres Blicks. Ein neues Modul dort wäre stillschweigend durchgefallen: *genau der Fehler,
  // gegen den sie gebaut wurde.*
  //
  // **Ein Verzeichnis-Scan war ohnehin die falsche Frage.** In `app/` liegen Dutzende Module, die
  // mit der Zerlegung nichts zu tun haben. Die Teile sind **das, was die Hauptfunktion einbindet** —
  // also wird genau das gelesen, statt einen Ordner zu raten.
  const app = teil('app/HausplanerApp.tsx');
  const eingebunden = [...app.matchAll(/from '\.\/(rahmen\/[A-Za-z]+|dashboard\/Kopfrahmen)'/g)]
    .map((m) => `app/${m[1]}.tsx`)
    .sort();
  assert.ok(eingebunden.length >= 5,
    `nur ${eingebunden.length} eingebundene Teile gefunden — die Zusage misst Leere`);
  for (const t of eingebunden) {
    assert.ok(TEILE.includes(t as typeof TEILE[number]),
      `\`${t}\` wird von der Hauptfunktion eingebunden, fehlt aber in TEILE — Absenz-Zusagen sehen es nicht`);
  }
  // Und umgekehrt: kein Eintrag in TEILE, den es nicht gibt.
  for (const t of TEILE) {
    assert.ok(existsSync(join(hier, '..', t)), `TEILE nennt \`${t}\`, aber die Datei gibt es nicht`);
  }
});

// --- Die acht Bindungen, die vorher niemand geprüft hat -------------------------------------------

test('K-06 (blind gewesen): die Palette wird NUR gezeichnet, wenn sie offen ist', () => {
  // Mutation: `{true && (`. Nichts wurde rot — das Overlay hätte dauerhaft über der Bühne gelegen.
  assert.match(fuss, /\{paletteOffen && \(/, 'die Palette hängt nicht mehr an ihrem Offen-Zustand');
  assert.doesNotMatch(fuss, /\{true && \(/, 'irgendetwas wird bedingungslos gezeichnet');
});

test('K-06 (blind gewesen): ein Klick neben die Palette schliesst sie — und einer DARIN nicht', () => {
  // Zwei Hälften derselben Sache: der Hintergrund schliesst, der Dialog hält das Ereignis auf.
  // Fehlt die zweite, schliesst sich die Palette beim Tippen im eigenen Feld.
  assert.match(fuss, /if \(e\.target === e\.currentTarget\) schliessePalette\(\);/,
    'der Klick auf den Hintergrund schliesst die Palette nicht mehr');
  assert.match(fuss, /onMouseDown=\{\(e\) => e\.stopPropagation\(\)\}/,
    'der Dialog hält den Klick nicht mehr auf — ein Klick INS Feld würde schliessen');
});

test('K-06 (blind gewesen): die Palette liegt ausserhalb des Flusses — `fixed`, nicht `absolute`', () => {
  // Mutation: `position: 'absolute'`. Nichts wurde rot. Kante 10: als `absolute` richtet sich das
  // Overlay am nächsten positionierten Vorfahren aus — die Studio-Shell liefe über.
  //
  // **AUF-38-P3 hat diese Wahrheit eine Datei weiter geschoben.** Sie stand als Inline-Stil hier
  // und steht jetzt in `hausplaner.css`. *Die alte Fassung las nur den Inline-Stil und wurde rot,
  // obwohl nichts kaputt ist — genau die Lage, aus der F-15 entstanden ist.* Geprüft wird
  // deshalb jetzt BEIDES: die Klasse sitzt am Element, und die Regel trägt die Eigenschaft.
  // Eines allein sagt nichts: eine Klasse ohne Regel ist ungestyltes Markup, eine Regel ohne
  // Träger ist wirkungslos.
  assert.match(fuss, /className="hp-fu-palette-flaeche"/, 'das Overlay trägt seine Klasse nicht mehr');
  const regel = css.match(/\.hp-fu-palette-flaeche\s*\{([^}]*)\}/);
  assert.ok(regel, 'die Regel der Palettenfläche fehlt in der Stilschicht');
  assert.match(regel[1]!, /position: fixed/, 'das Overlay liegt nicht mehr ausserhalb des Flusses (Kante 10)');
  assert.match(regel[1]!, /inset: 0/, 'das Overlay füllt den Bildschirm nicht mehr');
  assert.match(regel[1]!, /z-index: 60/, 'das Overlay hat seine Stapelhöhe verloren');
});

test('K-06 (blind gewesen): die Palette ist ein Dialog — mit Rolle, Modus und Namen', () => {
  // Ohne die drei Angaben ist das Overlay für ein Vorleseprogramm ein `div` wie jedes andere:
  // es wird nicht als Dialog angesagt, der Hintergrund bleibt bedienbar, und es hat keinen Namen.
  assert.match(fuss, /role="dialog"/, 'die Palette ist kein Dialog mehr');
  assert.match(fuss, /aria-modal="true"/, 'der Hintergrund gilt weiterhin als bedienbar');
  assert.match(fuss, /aria-label="Befehle suchen"/, 'der Dialog hat seinen Namen verloren');
  // Und das Filterfeld bekommt den Fokus — sonst tippt man ins Leere.
  assert.match(fuss, /autoFocus/, 'das Filterfeld bekommt den Fokus nicht mehr');
});

test('K-06 (blind gewesen): die Statusleiste zeigt x als x und y als y', () => {
  // Mutation: die beiden vertauscht. Nichts wurde rot — und eine vertauschte Koordinatenanzeige
  // ist in einem Planer die Sorte Fehler, die man erst nach dem dritten falschen Maß bemerkt.
  assert.match(fuss, /x \{cursor\.x\} mm · y \{cursor\.y\} mm/, 'die Koordinaten der Statusleiste sind vertauscht');
});

test('K-06 (blind gewesen): die Statusleiste rechnet Zoom und Fläche unverändert', () => {
  assert.match(fuss, /Zoom \{\(zoom \* 100\)\.toFixed\(0\)\} %/, 'der Zoom wird anders gerechnet als vorher');
  assert.match(fuss, /\/ 1_000_000\)\.toFixed\(2\)\} m²/, 'die Fläche wird anders von mm² auf m² gerechnet');
});

test('K-06 (blind gewesen): die Engine-Fläche erscheint nur mit offener Engine UND vorhandenem Panel', () => {
  // Mutation: `{true && (`. Nichts wurde rot. Beide Bedingungen zählen: ohne die zweite läge eine
  // Fläche ohne Inhalt über dem Planer.
  assert.match(fuss, /\{offeneEngine && enginePanel\(offeneEngine\) && \(/,
    'die Engine-Fläche hängt nicht mehr an beiden Bedingungen');
  // Und der Rückweg führt wirklich zurück.
  assert.match(fuss, /onZurueck=\{\(\) => setOffeneEngine\(null\)\}/,
    'der Rückweg aus der Engine-Fläche schliesst sie nicht mehr');
});

// --- K-02 / K-01 der Zerlegung --------------------------------------------------------------------

test('K-02: die Scheibe hält keinen Zustand — die Palette schliesst über den Rückruf', () => {
  for (const muster of [/useState/, /usePlannerUiStore/, /localStorage/]) {
    assert.doesNotMatch(fuss, muster, `die Scheibe hält Zustand: ${muster}`);
  }
  // presence-Partner nach R2: der Zustand der Palette wohnt weiterhin in der Hauptfunktion.
  assert.match(app, /const \[paletteOffen, setPaletteOffen\] = useState/, 'der Palettenzustand ist aus der Hauptfunktion verschwunden');
});

test('K-03: der Escape-Weg ist NICHT mitgewandert — er hängt am Stapel, nicht am Markup', () => {
  // **Die Grenze dieser Scheibe.** AUF-83-T5 hat die Rangfolge Palette > Dialog > Menü > Schiene
  // gesetzt; ein zweiter, direkter `schliessePalette()`-Aufruf im Tastenpfad war schon einmal die
  // Ursache eines echten Fehlers. Er darf hier nicht wieder entstehen.
  assert.doesNotMatch(fuss, /useEscapeEbene/, 'der Escape-Stapel ist in die Scheibe gewandert');
  assert.match(app, /useEscapeEbene\('palette'/, 'die Palette hängt nicht mehr am Escape-Stapel');

  // **Meine erste Fassung dieser Zusage war zu grob, und sie ist an sich selbst rot geworden:**
  // sie verbot JEDE Erwähnung von Escape. Gemessen wertet das Filterfeld Escape sehr wohl aus —
  // aber nur mit `preventDefault(); return;`, weil manche Browser ein Textfeld bei Escape nativ
  // leeren. **Geschlossen wird dort nicht.** *Die verbotene Sache ist ein zweiter SCHLIESSWEG,
  // nicht die Kenntnis der Taste.* Genau das prüft die Zusage jetzt.
  const escapeZweig = fuss.match(/if \(e\.key === 'Escape'\) \{[^}]*\}/);
  assert.ok(escapeZweig, 'der Escape-Zweig des Filterfelds wurde nicht gefunden — die Zusage misst Leere');
  assert.match(escapeZweig[0], /e\.preventDefault\(\); return;/, 'der Escape-Zweig tut etwas anderes als vorher');
  assert.ok(!escapeZweig[0].includes('schliessePalette'),
    'das Filterfeld schliesst die Palette selbst — genau der zweite Weg, der schon einmal ein echter Fehler war');
});

test('K-01: die Hauptfunktion ruft die Scheibe — genau einmal, und trägt sie nicht mehr selbst', () => {
  const rufe = [...app.matchAll(/<FussUndUeberlagerungen\b/g)];
  assert.equal(rufe.length, 1, `${rufe.length} Aufrufe — erwartet genau einer`);
  for (const marke of ['role="dialog"', 'Strg/⌘+K · Befehle', '<EngineFlaeche']) {
    assert.ok(fuss.includes(marke), `\`${marke}\` steht nicht in der neuen Datei`);
    assert.ok(!app.includes(marke), `\`${marke}\` steht noch ein zweites Mal in der Hauptfunktion`);
  }
});

test('K-01: die Scheibe trägt 8 Inline-Vorkommen — 12 sind nach AUF-38-P3 Klassen', () => {
  // **Vorkommen, nicht Zeilen.** Der Planner hat das Kriterium nach meinem S4d-Befund korrigiert:
  // `grep -c` zählt Zeilen und bewegt sich schon, wenn jemand eine Zeile umbricht.
  //
  // **Die Zahl ist von 20 auf 8 gefallen, und das ist der Auftrag** (AUF-38-P3, K-01: *„8 Stellen
  // insgesamt, davon 0 offen"*). Die verbliebenen acht sind DYNAMISCH und ausserhalb des Blattes.
  const vorkommen = (fussRoh.match(/style=\{\{/g) ?? []).length;
  assert.equal(vorkommen, 8, `${vorkommen} Inline-Vorkommen statt 8`);
});

// --- Z-03 / K-02: die Fussflaeche zeigt die Fangart im Klartext ----------------------------------
//
// **Die Mutationsprobe VOR dieser Zusage — 8 Mutationen, 6 kamen durch:**
//
// ```text
// mittelpunkt zeigt „Endpunkt“        keine Zusage rot
// achse zeigt „Raster“                keine Zusage rot
// verlaengerung zeigt „Wandflucht“    keine Zusage rot
// endpunkt zeigt „Wandmitte“          keine Zusage rot
// raster zeigt „Endpunkt“             keine Zusage rot
// ortho zeigt „Raster“                keine Zusage rot
// keiner redet dauernd „kein Fang“    fail 1   (fangKern.test.ts hatte den Partner)
// Fussflaeche friert auf endpunkt ein fail 1   (fangAnschluss.test.ts hatte den Partner)
// ```
//
// **Warum sechs blind waren:** die vorhandene Zusage lautete `FANG_TEXT[art].length >= 5`. Sie
// prueft, DASS ein Text da ist — nicht, dass es der RICHTIGE ist. *Ein Fang, der „Endpunkt“ meldet
// und in Wahrheit die Wandmitte gegriffen hat, ist schlimmer als gar keine Meldung: er macht aus
// einer Unsicherheit eine falsche Gewissheit.* Das ist F-06 in Reinform.

/**
 * **Begriffs-Anker statt eingefrorener Zeichenketten.**
 *
 * Die Zusage bindet jede Fangart an den BEGRIFF, den sie benennen muss — nicht an ihren heutigen
 * Wortlaut. „Wandmitte“ darf zu „Mitte der Wand“ werden, ohne dass die Zusage bricht; sie bricht,
 * sobald die Mitte als Endpunkt ausgegeben wird. *Genau das ist der Unterschied zwischen einer
 * Zuordnungs-Zusage und einem Textabzug.*
 */
const BEGRIFF: Record<Exclude<FangArt, 'keiner'>, RegExp> = {
  endpunkt: /\bendpunkt\b/i,
  mittelpunkt: /mitte/i,
  achse: /flucht|achse/i,
  verlaengerung: /verläng|verlaeng/i,
  ortho: /winkel|ortho/i,
  raster: /raster/i,
};

test('Z-03/K-02: jede Fangart nennt ihren eigenen Begriff — keine zeigt den einer anderen', () => {
  for (const [art, muster] of Object.entries(BEGRIFF)) {
    const text = FANG_TEXT[art as FangArt];
    assert.match(text, muster, `\`${art}\` meldet „${text}“ — das ist nicht der Begriff dieser Fangart`);
  }
});

test('Z-03/K-02: die Begriffs-Tabelle deckt JEDE Fangart ab — eine neue rutscht nicht durch', () => {
  // **Ohne diese Haelfte waere die Zusage oben eine mitwachsende Leseliste:** wer eine siebte
  // Fangart ergaenzt und in `BEGRIFF` vergisst, bekommt keine rote Zusage, sondern Stille.
  const arten = Object.keys(FANG_TEXT).filter((a) => a !== 'keiner').sort();
  assert.deepEqual(arten, Object.keys(BEGRIFF).sort(),
    'FANG_TEXT und die Begriffs-Tabelle sind auseinandergelaufen — eine Fangart ist ungeprueft');
});

test('Z-03/K-02: zwei Fangarten teilen sich nie denselben Klartext — sonst stirbt der Tausch leise', () => {
  // **Der Fall, den die Begriffs-Anker allein nicht faengt:** zwei Arten vertauschen. Beide Texte
  // existieren weiter, beide sind lesbar — nur zeigen sie aufeinander. Die Doppelung faengt es.
  const texte = Object.entries(FANG_TEXT).filter(([a]) => a !== 'keiner').map(([, t]) => t);
  assert.equal(new Set(texte).size, texte.length, `zwei Fangarten melden denselben Text: ${texte.join(' · ')}`);
});

test('Z-03/K-02: die Fussflaeche zeigt den Hinweis NUR, wenn wirklich gefangen wurde', () => {
  // Der Partner zur leeren Zeichenkette bei `keiner`: eine Statuszeile, die dauernd „kein Fang“
  // sagt, ist Rauschen. Die Kurzschluss-Bedingung ist die Stelle, an der das durchgesetzt wird.
  assert.match(fuss, /\{fangHinweis && <span className="hp-fu-fang">/,
    'der Fang-Hinweis haengt nicht mehr an einer Kurzschluss-Bedingung — er steht dann dauerhaft da');
});
