/**
 * AUF-48 Scheibe 4b / K-02 + K-03 — **die Schiene, an drei Stellen erstmals verriegelt.**
 *
 * ---
 *
 * **Die Probe VOR dem Schreiben, von grüner Grundlinie (1504/0), acht Mutationen:**
 *
 * ```text
 * gefangen (5)  Werkzeuge-Reiter zeigt Projekt-Inhalt · Fuss sagt wieder die Vertroestung
 *               Inhaltsbereich ohne Reiter-Bezug · gesperrte Werkzeuge sehen bedienbar aus
 *               Modus-Schalter fest auf 2D  (den fing meine eigene Zusage aus Scheibe 4a)
 * blind    (3)  zwei Reiter-Beschriftungen vertauscht   <- genau die aus dem Blatt
 *               Klapp-Schalter klappt nur auf, nie zu
 *               zugeklappte Schiene bleibt 220 px breit
 * ```
 *
 * **Der Auftrag sagt zu K-03:** *„Wird keine Zusage rot, ist DAS der Befund — dann melden statt
 * Tests nachreichen, die nur das Vorhandene bestätigen."* Fünf wurden rot, drei nicht. **Also
 * beides:** gemeldet ist der Befund, und geschlossen sind die drei — jede gegen ihre eigene
 * Mutation gegengeprüft, die Datei danach md5-identisch wiederhergestellt.
 *
 * **Was diese Datei nicht kann:** sie liest Quelltext, sie rendert nicht. Die Reihe ohne DOM kann
 * die Schiene nicht mounten (Konva). *Sie fängt jede der drei Mutationen — sie beweist nicht, dass
 * der Browser daraus dasselbe Bild macht.* Dafür steht L-01.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { SCHIENEN_REITER } from '../app/dashboard/schienenReiter';
import { teil, ohneKommentare } from './_zerlegteApp';

const schiene = ohneKommentare(teil('app/rahmen/GruppenzeileUndSchiene.tsx'));
const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

// --- K-02: kein Zustand ist mitgewandert ----------------------------------------------------------

test('K-02: die Scheibe nimmt Werte entgegen — sie hält keinen eigenen Zustand', () => {
  // Der Auftrag nennt den Befehl selbst. Käme hier ein `useState` hinzu, gäbe es einen zweiten
  // Ort, an dem der Zustand der Oberfläche wohnt — genau die zweite Wahrheit, die die Zerlegung
  // verhindern soll.
  for (const muster of [/useState/, /useRef/, /usePlannerUiStore/, /localStorage/]) {
    assert.doesNotMatch(schiene, muster, `die Scheibe hält Zustand: ${muster}`);
  }
  // presence-Partner nach R2: die Hauptfunktion hält ihn weiterhin — sonst misst die Zusage Leere.
  for (const muster of [/useState/, /usePlannerUiStore/]) {
    assert.match(app, muster, `die Hauptfunktion hält keinen Zustand mehr: ${muster}`);
  }
});

// --- Die drei blinden Stellen ---------------------------------------------------------------------

test('K-03 (blind gewesen): jeder Schienen-Reiter zeigt den Inhalt, der zu SEINER id gehört', () => {
  // **Die Mutation aus dem Blatt: zwei Reiter-Beschriftungen vertauscht — nichts wurde rot.**
  // Der Grund: keine Zusage verband je eine Beschriftung mit ihrer id. Ein vertauschtes Paar
  // hätte „Werkzeuge" über den Projektbrowser geschrieben, und die Reihe wäre grün geblieben.
  const erwartet: Record<string, string> = {
    werkzeuge: 'Werkzeuge',
    fachplaner: 'Fachplaner',
    projekt: 'Projekt',
  };
  assert.equal(SCHIENEN_REITER.length, 3, `${SCHIENEN_REITER.length} Schienen-Reiter statt drei`);
  for (const r of SCHIENEN_REITER) {
    assert.equal(r.label, erwartet[r.id], `der Reiter \`${r.id}\` trägt die Beschriftung „${r.label}"`);
  }
  // Und jede id hat im Markup ihren eigenen Zweig — kein Reiter zeigt den Inhalt eines anderen.
  for (const id of Object.keys(erwartet)) {
    const treffer = [...schiene.matchAll(new RegExp(`schienenTab === '${id}'`, 'g'))];
    assert.equal(treffer.length, 1, `\`${id}\` hat ${treffer.length} Sichtbarkeitsbedingungen statt genau einer`);
  }
});

test('K-03 (blind gewesen): der Schalter KLAPPT UM — er klappt nicht nur auf', () => {
  // Mutation: `klappeSchiene('links', !schienen.links)` -> `(…, true)`. Nichts wurde rot.
  // `zustandsfunktionen.test.ts` (Scheibe 3) prüft, dass `klappeSchiene` den übergebenen Wert
  // ungedreht setzt — **aber nicht, dass die Aufrufstelle den umgekehrten übergibt.** Die
  // Funktion war verriegelt, ihr Aufruf nicht.
  assert.match(schiene, /klappeSchiene\('links', !schienen\.links\)/,
    'der Schalter übergibt nicht den umgekehrten Zustand — die Schiene liesse sich nicht mehr zuklappen');
});

test('K-03 (blind gewesen): zugeklappt ist die Schiene SCHMAL — sonst klappt sie sichtbar nichts', () => {
  // Mutation: `schienen.links ? 220 : 220`. Nichts wurde rot — die Schiene hätte ihren Platz
  // behalten, und der ganze Zweck von AUF-83-T5 (Platz für die Bühne) wäre still weg gewesen.
  const treffer = schiene.match(/width: schienen\.links \? (\d+) : (\d+)/);
  assert.ok(treffer, 'die Breitenregel der Schiene wurde nicht gefunden — die Zusage misst Leere');
  const [, offen, zu] = treffer.map(Number);
  assert.equal(offen, 220, `offen misst die Schiene ${offen} px statt 220`);
  assert.ok(zu < offen / 2, `zugeklappt misst sie ${zu} px — das ist kein Zuklappen`);
});

// --- K-01 der Zerlegung: wirklich ausgezogen, nicht kopiert ---------------------------------------

test('K-01: die Hauptfunktion ruft beide Teile — je genau einmal', () => {
  for (const name of ['ArbeitsbereichZeilen', 'PlanerSchiene']) {
    const rufe = [...app.matchAll(new RegExp(`<${name}\\b`, 'g'))];
    assert.equal(rufe.length, 1, `${rufe.length} Aufrufe von <${name}> — erwartet genau einer`);
    assert.match(schiene, new RegExp(`export function ${name}\\(`), `${name} fehlt — die Zusage misst Leere`);
  }
});

test('K-01: das Markup steht NICHT mehr ein zweites Mal in der Hauptfunktion', () => {
  // Ohne diesen Partner bliebe die Zusage oben grün, wenn beides nebeneinander stünde.
  for (const marke of ['<WerkzeugGruppenMenue', 'schienenReiter(schienenTab)?.hinweis', '<FaehigkeitenNavi']) {
    assert.ok(schiene.includes(marke), `\`${marke}\` steht nicht in der neuen Datei`);
    assert.ok(!app.includes(marke), `\`${marke}\` steht noch ein zweites Mal in der Hauptfunktion`);
  }
});

test('K-01 (Grenze): die Messreihe ist NICHT mitgewandert — sie umschliesst auch die Bühne', () => {
  // **Der Befund dieser Scheibe.** Der Auftrag ankerte „bis zur Zeile vor `<Stage`" — dort stehen
  // aber zwei ÖFFNENDE `div`, die erst nach der Bühne schliessen. Wären sie mitgezogen, hätte die
  // neue Datei zwei unausgeglichene Tags, und `inhaltRef` (das Maszband aus AUF-72) läge nicht
  // mehr um Bühne, 3D-Bereich und Panel.
  assert.match(app, /<div ref=\{inhaltRef\}/, 'die Messreihe ist aus der Hauptfunktion verschwunden');
  assert.doesNotMatch(schiene, /inhaltRef/, 'die Messreihe ist in die Schiene gewandert');
  // Und die Bühne steht weiterhin in derselben Reihe wie die Schiene — Geschwister, nicht verschachtelt.
  const reihe = app.indexOf('<div ref={inhaltRef}');
  assert.ok(app.indexOf('<PlanerSchiene', reihe) > reihe, 'die Schiene steht nicht mehr in der Messreihe');
  assert.ok(app.indexOf('<Stage', reihe) > reihe, 'die Bühne steht nicht mehr in der Messreihe');
});
