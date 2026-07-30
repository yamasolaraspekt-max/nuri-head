/**
 * AUF-48 Scheibe 4c / K-04 — **die Bühne, erstmals verriegelt.**
 *
 * ---
 *
 * **Der gemessene Befund vor dem Schreiben — der Auftrag hat ihn vorhergesagt, und er ist
 * eingetreten:** *„Bei S1 waren 8 von 8 unverriegelt, bei S2 wieder — rechne nicht damit, dass es
 * hier anders ist, aber behaupte es auch nicht ungemessen."*
 *
 * Sechs Mutationen auf grüner Grundlinie (1511/0), **sechs kamen durch:**
 *
 * ```text
 * Waende VOR Raeume getauscht              keine Zusage rot   (die Mutation aus dem Blatt)
 * Vorschau beim Wandzeichnen entfernt      keine Zusage rot   (die zweite aus dem Blatt)
 * Treppen-Vorschau entfernt                keine Zusage rot
 * Referenzunterlage vom ERSTEN ans LETZTE Kind             keine Zusage rot
 * Mausrad zoomt verkehrt herum             keine Zusage rot
 * Zeigerposition wird nicht mehr gefuehrt  keine Zusage rot
 * ```
 *
 * **Die vierte ist die schwerste.** `AUF-88-P1 / K-03` hat eigens festgelegt, dass die
 * Referenzunterlage das **erste** Kind der Ebene ist — sonst liegt das eingelesene Planbild **über**
 * der Zeichnung statt darunter, und man zeichnet blind. *Diese Festlegung war bis heute durch
 * nichts geschützt.*
 *
 * **Was diese Datei nicht kann:** sie liest Quelltext, sie rendert kein Konva. Die Reihe ohne DOM
 * kann die Bühne nicht mounten — die Ebenenfolge wird als **Reihenfolge im Quelltext** geprüft,
 * nicht als gemessene z-Ordnung im Bild. *Das ist schwächer, und es steht hier, statt verschwiegen
 * zu werden.* Die gerenderte Bühne belegt L-01.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { teil, ohneKommentare } from './_zerlegteApp';

const buehneRoh = teil('app/rahmen/Buehne.tsx');
const buehne = ohneKommentare(buehneRoh);
const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

/** Der Inhalt der einen Konva-Ebene — dort steht die Zeichenreihenfolge. */
function ebene(): string {
  const von = buehneRoh.indexOf('<Layer>');
  const bis = buehneRoh.indexOf('</Layer>', von);
  assert.ok(von > 0 && bis > von, 'die Konva-Ebene wurde nicht gefunden — die Zusage misst Leere');
  return buehneRoh.slice(von, bis);
}

// --- K-01: die Ebenenfolge IST die Zeichenreihenfolge ---------------------------------------------

/**
 * Die acht Marken in ihrer gewollten Reihenfolge. **Gesucht wird der Anker im Markup, nicht der
 * Kommentar** — ein Kommentar liesse sich verschieben, ohne dass sich am Bild etwas ändert, und
 * umgekehrt. *Genau diese Verwechslung hat in diesem Zyklus schon dreimal eine Zusage entwertet.*
 */
const FOLGE: ReadonlyArray<readonly [string, string]> = [
  ['Referenzunterlage', '<UnterlagenEbene'],
  ['Räume', 'raeume.map('],
  ['Wände', 'waende.map('],
  ['Öffnungen', 'nodes.filter(istOeffnung)'],
  ['Dächer', 'scene.roofs'],
  // **Zwei Anker, die sich fast gleichen — und mein erster war falsch.** Beide Ebenen filtern
  // `n.type === 'object'`; die Treppen nehmen `objectType === 'stair'`, die Objekte alles andere.
  // Mein erster Anker traf deshalb schon im Treppen-Block und meldete eine vertauschte Reihenfolge,
  // die es nicht gab. *Ein Anker, der zweimal passt, misst nicht die Stelle, die er meint.*
  ['Treppen', "n.objectType === 'stair'"],
  ['Objekte', "n.objectType !== 'stair'"],
  // **Z-01 hat die Form dieser zwei Bedingungen geaendert, nicht ihre Aussage.** Vorher stand
  // `werkzeug === 'wand' && wandStart`; jetzt entscheidet `zeigtVorschau` zusaetzlich, ob der
  // Zeiger ueberhaupt auf der Flaeche steht. **Der Anker greift deshalb die Ebene ueber ihren
  // Inhalt**, nicht ueber die Bedingung davor — sonst misst er die Gestalt der Bedingung.
  ['Vorschau Treppe', 'treppeStart.x, treppeStart.y'],
  ['Vorschau Wand', 'mitWinkelSnap(wandStart, cursor)'],
];

test('K-01: die Ebenen stehen in genau dieser Reihenfolge — sie ist die Zeichenreihenfolge', () => {
  // **Die Mutation aus dem Blatt (Waende vor Raeume) kam durch.** Ein getauschtes Paar hätte die
  // Raumfüllung ÜBER die Wände gelegt; im Bild wären die Wände verschwunden.
  const e = ebene();
  const orte = FOLGE.map(([name, anker]) => {
    const i = e.indexOf(anker);
    assert.ok(i >= 0, `die Ebene „${name}" wurde nicht gefunden (Anker \`${anker}\`) — die Zusage misst Leere`);
    return [name, i] as const;
  });
  for (let i = 1; i < orte.length; i += 1) {
    assert.ok(orte[i][1] > orte[i - 1][1],
      `„${orte[i][0]}" steht vor „${orte[i - 1][0]}" — die Zeichenreihenfolge ist vertauscht`);
  }
});

test('K-01: die Referenzunterlage ist das ERSTE Kind der Ebene — AUF-88-P1 / K-03', () => {
  // **Die schwerste der sechs blinden Stellen.** Liegt die Unterlage nicht zuunterst, deckt das
  // eingelesene Planbild die eigene Zeichnung zu — man zeichnet blind. Die Festlegung stammt aus
  // AUF-88-P1 und war bis heute durch nichts geschützt.
  const e = ebene();
  const unterlage = e.indexOf('<UnterlagenEbene');
  assert.ok(unterlage > 0, 'die Referenzunterlage steht nicht mehr in der Ebene');
  for (const [name, anker] of FOLGE.slice(1)) {
    const i = e.indexOf(anker);
    assert.ok(i > unterlage, `„${name}" steht VOR der Referenzunterlage — sie liegt dann über der Zeichnung`);
  }
});

// --- K-03: kein Zustand ist mitgewandert ----------------------------------------------------------

test('K-03: die Bühne hält keinen Zustand — `stageRef` wird durchgereicht, nicht neu angelegt', () => {
  for (const muster of [/useState/, /useRef/, /usePlannerUiStore/, /localStorage/]) {
    assert.doesNotMatch(buehne, muster, `die Bühne hält Zustand: ${muster}`);
  }
  // Die Ausnahme, die der Auftrag benennt: der Griff kommt von aussen.
  assert.match(buehne, /stageRef: React\.RefObject<Konva\.Stage \| null>/, '`stageRef` ist keine Eigenschaft mehr');
  assert.match(app, /const stageRef = useRef<Konva\.Stage \| null>\(null\)/, 'der Griff wird nicht mehr in der Hauptfunktion angelegt');
});

test('K-02: die Bühne trägt KEINE Inline-Stelle — Konva arbeitet über Props', () => {
  // Die Zahl, die diese Scheibe besonders macht. Käme hier eine hinzu, wäre die Aussage
  // „AUF-38 hat hier nichts zu holen" nicht mehr wahr.
  assert.equal((buehneRoh.match(/style=\{\{/g) ?? []).length, 0,
    'die Bühne trägt jetzt Inline-Stile — dann gehört sie zu AUF-38 Scheibe 7');
});

// --- Die vier weiteren blinden Stellen ------------------------------------------------------------

test('K-04 (blind gewesen): beide Vorschauen beim Zeichnen sind da', () => {
  // Ohne sie zieht man eine Wand blind: kein Strich, keine Länge, bis der zweite Klick sitzt.
  // **Z-01: beide haengen jetzt ZUSAETZLICH daran, ob der Zeiger auf der Flaeche steht.**
  // Vorher blieb die Vorschau stehen, wo der Zeiger die Flaeche zuletzt beruehrt hat — der
  // "lange Strich" aus Yamas Meldung. Die alte Zusage haette den Umbau nicht ueberlebt und
  // ihn auch nicht gefordert; diese fordert ihn.
  assert.match(buehne, /zeigtVorschau\(\{ wandStart, treppeStart, zeigerDrinnen \}, 'wand'\) && wandStart && werkzeug === 'wand'/,
    'die Vorschau beim Wandzeichnen fehlt oder haengt nicht mehr am Zeiger-Zustand');
  assert.match(buehne, /zeigtVorschau\(\{ wandStart, treppeStart, zeigerDrinnen \}, 'treppe'\) && treppeStart && werkzeug === 'treppe'/,
    'die Vorschau beim Treppezeichnen fehlt oder haengt nicht mehr am Zeiger-Zustand');
  // Und die Wand-Vorschau rastet mit — sonst zeigt sie etwas anderes als das, was entsteht.
  assert.match(buehne, /mitWinkelSnap\(wandStart, cursor\)/, 'die Vorschau folgt nicht demselben Winkel-Fang wie die Wand');
});

test('K-04 (blind gewesen): das Mausrad zoomt in die Richtung, in die gedreht wird', () => {
  // Mutation: die beiden Faktoren vertauscht. Nichts wurde rot — und ein verkehrt herum zoomendes
  // Mausrad ist die Sorte Fehler, die jeder sofort merkt und niemand messen konnte.
  assert.match(buehne, /e\.evt\.deltaY < 0 \? 1\.1 : 1 \/ 1\.1/,
    'das Mausrad zoomt verkehrt herum oder mit anderem Faktor');
  assert.match(buehne, /Math\.min\(1, Math\.max\(0\.02, z \* faktor\)\)/,
    'die Zoomgrenzen (0,02 bis 1) sind fort oder verändert');
});

test('K-04 (blind gewesen): die Zeigerposition wird geführt — sonst steht jede Vorschau still', () => {
  assert.match(buehne, /onMouseMove=\{\(e\) => setCursor\(weltPunkt\(e\)\)\}/,
    'die Bühne führt die Zeigerposition nicht mehr nach');
});

test('K-04: verschoben wird nur mit dem Auswahl-Werkzeug — an ALLEN drei Stellen', () => {
  // Sonst zöge ein Klick beim Wandzeichnen die ganze Bühne weg.
  //
  // **Meine erste Fassung war zu lose, und die Mutationsprobe hat es gezeigt:** sie suchte das
  // Muster irgendwo in der Datei. `draggable` steht dreimal darin (Bühne, Wand, Treppe) — wer
  // genau den Bühnen-Treffer verdreht, liess zwei stehen, und die Zusage blieb grün.
  // *Eine Zusage, die „irgendwo" prüft, prüft nichts Bestimmtes.*
  //
  // **Und die Zahl selbst war zunaechst falsch bei mir: ich schrieb drei, gemessen sind es fuenf.**
  // Ein frueherer `grep | head -3` hatte mir drei Zeilen gezeigt, und ich hielt das fuer die Summe.
  // Gegen den Stand VOR der Entnahme nachgemessen: dort waren es ebenfalls fuenf, alle im Block.
  // *Eine abgeschnittene Ausgabe ist keine Zaehlung.*
  const alle = [...buehne.matchAll(/draggable=\{werkzeug === 'auswahl'\}/g)];
  assert.equal(alle.length, 5, `${alle.length} Stellen binden das Verschieben ans Auswahl-Werkzeug — erwartet fuenf`);
  assert.doesNotMatch(buehne, /draggable=\{true\}/, 'etwas ist unabhängig vom Werkzeug verschiebbar geworden');
  // Und die Bühne selbst, an ihrem eigenen Ort geprüft:
  const stage = buehne.slice(buehne.indexOf('<Stage'), buehne.indexOf('>', buehne.indexOf('onWheel')));
  assert.match(stage, /draggable=\{werkzeug === 'auswahl'\}/, 'die Bühne ist unabhängig vom Werkzeug verschiebbar');
});

// --- K-05 der Zerlegung ---------------------------------------------------------------------------

test('K-05: die Hauptfunktion ruft die Bühne — genau einmal, und trägt sie nicht mehr selbst', () => {
  const rufe = [...app.matchAll(/<Buehne\b/g)];
  assert.equal(rufe.length, 1, `${rufe.length} Aufrufe der Bühne — erwartet genau einer`);
  assert.match(buehne, /export function Buehne\(/, 'die Komponente fehlt — die Zusage misst Leere');
  assert.ok(!app.includes('<Stage'), 'die Bühne steht noch ein zweites Mal in der Hauptfunktion');
  assert.ok(!app.includes('<UnterlagenEbene'), 'die Referenzunterlage steht noch ein zweites Mal in der Hauptfunktion');
});

test('K-05 (Grenze): die Hülle um die Bühne ist geblieben — sie hängt an `modus`, nicht am Bild', () => {
  // Sie blendet die Bühne im 3D-Modus aus und trägt die Trennlinie im Split. Wäre sie mitgezogen,
  // hätte die neue Datei ein unausgeglichenes Tag — dasselbe Muster wie in Scheibe 4b.
  assert.match(app, /display: modus === '3d' \? 'none' : 'block'/, 'die Hülle der Bühne ist mitgewandert');
  assert.doesNotMatch(buehne, /modus/, 'die Bühne kennt den Ansichtsmodus — den entscheidet die Hülle');
});
