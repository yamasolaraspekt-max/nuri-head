/**
 * AUF-48 Scheibe 4a / K-03 — **die Bindungen des Kopfrahmens, erstmals verriegelt.**
 *
 * ---
 *
 * **Der gemessene Befund vor dem Schreiben — und er fällt anders aus als in Scheibe 1 bis 3.**
 *
 * Die Entnahme hat **42 geerbte Zusagen rot gemacht**; der Kopfrahmen war also alles andere als
 * unverriegelt. *In S1 waren 7 von 7 ausgelagerten Funktionen ohne jede Zusage, in S2 8 von 8.*
 * Hier ist es zum ersten Mal umgekehrt.
 *
 * **Aber: 15 Mutationsproben, 9 kamen durch.** Die 42 Zusagen prüfen, dass etwas **da** ist und in
 * welcher **Reihenfolge** — fast keine prüft, **woran es gebunden** ist:
 *
 * ```text
 * gefangen (6)   Duplizieren-Sperre · Suchen-Knopf ruft oeffnePalette · Speichern-Sperre
 *                Gruppenname (Vorleseprogramm) · Spiegelachse · Zoom-Anzeige
 * durchgekommen  Ansichtsmodus 2D/Split zeigen fremden Zustand · 3D-Knopf schaltet auf 2D
 *          (9)   sichtbares ⌘K entfernt · Marke im Studio statt ausserhalb · Zoomschritt 1,2→1,5
 *                Raster-Zustand verdreht · Fang-Zustand verdreht · Suchen-Knopf ohne Tooltip
 * ```
 *
 * **Diese Datei schliesst genau diese neun.** Jede Zusage unten ist gegen die Mutation geprüft, die
 * sie fangen soll — gemessen, nicht behauptet.
 *
 * **Was sie NICHT kann, offen gesagt:** sie liest den Quelltext, sie rendert nicht. Die Testreihe
 * ohne DOM kann den Kopfrahmen nicht mounten. *Eine Quelltext-Zusage ist schwächer als ein
 * gerenderter Knopf* — sie fängt jede der neun Mutationen, aber sie beweist nicht, dass der Browser
 * daraus dasselbe Bild macht. Dafür steht die Laufzeitprobe L-01 im Blatt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
/** **Ohne Kommentare gemessen.** Die Erklärung oben nennt Bezeichner beim Namen; ein Test, der rohen
 *  Text durchsucht, hielte sonst meinen eigenen Kommentar für Code. *Die Falle hat in diesem Zyklus
 *  schon zweimal zugeschlagen.* */
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const kopf = ohneKommentare(readFileSync(join(hier, '../app/dashboard/Kopfrahmen.tsx'), 'utf8'));
const app = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));

// --- K-01: der Kopfrahmen ist ausgezogen, und die Hauptfunktion ruft ihn --------------------------

test('K-01: die Hauptfunktion ruft den Kopfrahmen — genau einmal', () => {
  const rufe = [...app.matchAll(/<Kopfrahmen\b/g)];
  assert.equal(rufe.length, 1, `${rufe.length} Aufrufe des Kopfrahmens — erwartet genau einer`);
  assert.match(app, /import \{ Kopfrahmen \} from '\.\/dashboard\/Kopfrahmen'/, 'der Kopfrahmen wird nicht importiert');
  // presence-Partner nach R2: die Komponente, auf die sich das bezieht, gibt es auch.
  assert.match(kopf, /export function Kopfrahmen\(/, 'die Komponente fehlt — die Zusage misst Leere');
});

test('K-01: die drei Zeilen sind WIRKLICH ausgezogen — nicht kopiert', () => {
  // Ohne diesen Partner bliebe K-01 grün, wenn das Markup an beiden Orten stünde. Dann sähe die
  // Oberfläche doppelt aus, und beide Fassungen drifteten auseinander.
  for (const marke of ['Speichern (Strg+S)', 'hp-az-suchen', '<OpGruppe name="Verlauf">']) {
    assert.ok(kopf.includes(marke), `\`${marke}\` steht nicht im Kopfrahmen`);
    assert.ok(!app.includes(marke), `\`${marke}\` steht noch ein zweites Mal in der Hauptfunktion`);
  }
});

// --- K-04: einzeln benannte Eigenschaften ---------------------------------------------------------

test('K-04: kein Sammelobjekt — die Eigenschaften stehen einzeln da', () => {
  assert.doesNotMatch(kopf, /\bprops\b|\.\.\.props/, 'der Kopfrahmen nimmt einen props-Klumpen entgegen');
  // presence-Partner: es gibt überhaupt benannte Eigenschaften, und zwar mehr als eine Handvoll.
  const felder = [...kopf.matchAll(/^ {2}(\w+)[?]?:/gm)].map((m) => m[1]);
  assert.ok(felder.length >= 20, `nur ${felder.length} benannte Eigenschaften — die Zusage misst Leere`);
});

// --- Die neun Bindungen, die vorher niemand geprüft hat ------------------------------------------

/** Die drei Knöpfe des Ansichtsmodus, Zeile für Zeile. */
function modusKnoepfe(): string[] {
  const von = kopf.indexOf('<OpGruppe name="Ansichtsmodus">');
  const bis = kopf.indexOf('</OpGruppe>', von);
  assert.ok(von > 0 && bis > von, 'die Ansichtsmodus-Gruppe wurde nicht gefunden — die Zusage misst Leere');
  return kopf.slice(von, bis).split('\n').filter((z) => z.includes('<OpBtn'));
}

test('K-03 (Bindung): jeder Ansichtsmodus-Knopf zeigt SEINEN Zustand und schaltet auf SEINEN Modus', () => {
  // Gegen drei Mutationen, die alle durchkamen: `aktiv={modus === '2d'}` auf '3d' gedreht,
  // der Split-Knopf mit fremdem Zustand, der 3D-Knopf mit fremdem Ziel. **Die Knöpfe sahen
  // unverändert aus und zeigten den falschen Zustand an.**
  const zeilen = modusKnoepfe();
  assert.equal(zeilen.length, 3, `${zeilen.length} Ansichtsmodus-Knöpfe statt drei`);
  for (const [beschriftung, id] of [['2D', '2d'], ['Split', 'split'], ['3D', '3d']] as const) {
    const z = zeilen.find((x) => x.includes(`label="${beschriftung}"`));
    assert.ok(z, `der Knopf „${beschriftung}" fehlt`);
    assert.ok(z.includes(`aktiv={modus === '${id}'}`), `„${beschriftung}" zeigt einen fremden Zustand: ${z.trim()}`);
    assert.ok(z.includes(`setModus('${id}')`), `„${beschriftung}" schaltet auf einen fremden Modus: ${z.trim()}`);
  }
});

test('K-03 (Bindung): Raster und Fang zeigen ihren Zustand UNGEDREHT', () => {
  // Beide Mutationen (`aktiv={!rasterAn}`, `aktiv={!scene.settings.snapEnabled}`) kamen durch.
  // Ein verdrehter Schalter zeigt „aus", wenn er an ist — die verwirrendste Art von Fehler.
  assert.match(kopf, /icon="grid" aktiv=\{rasterAn\}/, 'der Raster-Schalter zeigt einen verdrehten Zustand');
  assert.match(kopf, /icon="fang" aktiv=\{scene\.settings\.snapEnabled\}/, 'der Fang-Schalter zeigt einen verdrehten Zustand');
  // Und das Umschalten bleibt eine Umkehr, kein fester Wert.
  assert.match(kopf, /setRasterAn\(\(v\) => !v\)/, 'Raster schaltet nicht mehr um');
  assert.match(kopf, /snapEnabled: !scene\.settings\.snapEnabled/, 'Fang schaltet nicht mehr um');
});

test('K-03 (Bindung): die drei Zoom-Knöpfe behalten ihre gemessenen Faktoren und Grenzen', () => {
  // Die Mutation 1,2 -> 1,5 kam durch. Der Zoomschritt ist eine sichtbare Eigenschaft der
  // Oberfläche; er darf sich ändern, aber nicht unbemerkt.
  assert.match(kopf, /Math\.min\(1, z \* 1\.2\)/, 'der Vergrössern-Schritt ist nicht mehr 1,2 (oder die Obergrenze ist fort)');
  assert.match(kopf, /Math\.max\(0\.02, z \/ 1\.2\)/, 'der Verkleinern-Schritt ist nicht mehr 1,2 (oder die Untergrenze ist fort)');
  assert.match(kopf, /setZoom\(0\.12\)/, 'der Rücksetzwert ist nicht mehr 0,12');
});

test('K-03 (Bindung): das Kürzel ⌘K steht SICHTBAR am Knopf — nicht nur im Tooltip', () => {
  // **Die Mutation, die am meisten über die geerbten Zusagen sagt:** ich habe das sichtbare
  // `⌘K` entfernt, und nichts wurde rot. `arbeitszeileSuche` sucht `/⌘K/` im Ausschnitt — und
  // findet es im `title`-Attribut. *Die Zusage prüft seit jeher den Tooltip, nicht die Anzeige.*
  const ohneTitel = kopf.replace(/title="[^"]*"/g, '');
  const von = ohneTitel.indexOf('className="hp-az-suchen"');
  const bis = ohneTitel.indexOf('</button>', von);
  assert.ok(von > 0 && bis > von, 'der Suchen-Knopf wurde nicht gefunden — die Zusage misst Leere');
  assert.match(ohneTitel.slice(von, bis), /⌘K/, 'das Kürzel steht nur im Tooltip — sichtbar ist es nicht');
  // Und der Tooltip bleibt trotzdem: er nennt beide Kürzel, auch das für Windows.
  assert.match(kopf, /title="Befehle und Werkzeuge durchsuchen \(⌘K \/ Strg\+K\)"/, 'der Suchen-Knopf hat seinen Tooltip verloren');
});

test('K-03 (Bindung): die Marke steht NUR ausserhalb des Studios', () => {
  // Die Mutation `{!imStudio && (` -> `{imStudio && (` kam durch. Im Studio gibt es kein Objekt;
  // eine Marke „Hausplaner · Solar Aspekt" über einer Testfläche wäre genau die Anzeige, die
  // AUF-40 entfernt hat — nur andersherum.
  const von = kopf.indexOf('{!imStudio && (');
  assert.ok(von > 0, 'die Bedingung der Marke ist fort oder umgekehrt');
  const bis = kopf.indexOf('<GeschossFlaeche', von);
  assert.ok(bis > von, 'der Ausschnitt der Marke wurde nicht gefunden — die Zusage misst Leere');
  assert.match(kopf.slice(von, bis), /Solar Aspekt/, 'die Marke steht nicht in ihrem eigenen Zweig');
  assert.doesNotMatch(kopf, /\{imStudio && \(/, 'irgendetwas erscheint jetzt NUR im Studio');
});

// --- K-01 der Betriebsart: der Kopfrahmen kennt keinen eigenen Speicher --------------------------

test('K-01: der Kopfrahmen fasst `localStorage` nicht an und hält keinen eigenen Zustand', () => {
  // Er stellt dar und reicht weiter. Käme hier ein `useState` hinzu, gäbe es einen zweiten Ort,
  // an dem der Zustand der Oberfläche wohnt — genau die zweite Wahrheit, die die Zerlegung
  // verhindern soll.
  assert.doesNotMatch(kopf, /localStorage/, 'der Kopfrahmen greift selbst auf den Speicher zu');
  assert.doesNotMatch(kopf, /useState|useReducer/, 'der Kopfrahmen hält eigenen Zustand');
  // presence-Partner nach R2: die Hauptfunktion hält ihn weiterhin.
  assert.match(app, /useState/, 'die Hauptfunktion hält keinen Zustand mehr — dann misst die Zusage Leere');
});
