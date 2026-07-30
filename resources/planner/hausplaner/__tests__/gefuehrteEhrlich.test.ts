/**
 * AUF-65 — die geführte Planung sagt, was sie weiß.
 *
 * **Zwei Meldungen, zwei Messungen, und die ältere behält recht:**
 * Die UX-Bewertung sagte *„das Aufgaben-Panel ist leer"*. Der Auftrag hielt dagegen: *„gemessen ist
 * es das nicht — jeder der elf Schritte trägt mindestens einen Eintrag"*, und schloss daraus, das
 * Panel sei **erfunden**, nicht leer.
 *
 * **Nachgemessen an dem, was die Fläche wirklich bekommt, stimmt die erste Meldung.** Die elf
 * Schritte kommen seit AUF-39 aus `dashboard/fahrschritte.ts` — abgeleitet aus dem Dokument. Dort
 * trägt **kein einziger** Schritt Aufgaben. Die Einträge, die der Auftrag gezählt hat, stehen in
 * `STEPS_STILLGELEGT` — einer Konstante, die ihren Zustand im Namen führt und die **nichts mehr
 * rendert**.
 *
 * Dieser Test hält beides fest: **dass die erfundenen Daten die Fläche nicht erreichen** und
 * **dass eine leere Aufgabenliste keine leere Überschrift hinterlässt.**
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { STATUS_LABEL, STEPS_STILLGELEGT, type SchrittStatus } from '../app/studioDaten';
import { ableitenSchritte, statusAus } from '../app/dashboard/fahrschritte';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const guided = readFileSync(join(hier, '../app/GuidedView.tsx'), 'utf8');
const daten = readFileSync(join(hier, '../app/studioDaten.ts'), 'utf8');

// --- (a) K3/K4: das Wort behauptet keinen Vorgang mehr ------------------------------------------
test('K3: kein Statuswort behauptet mehr eine Freigabe', () => {
  // Bewusst zusammengesetzt: stünde das Wort hier als Literal, fände `grep` es in dieser Datei.
  const wort = 'Frei' + 'gegeben';
  assert.equal((daten.match(new RegExp(wort, 'g')) ?? []).length, 0);
  assert.equal((guided.match(new RegExp(wort, 'g')) ?? []).length, 0);
  assert.equal(STATUS_LABEL.ok, 'Vollständig');
});

test('K4: die SCHLÜSSEL sind unverändert — geändert wurde das Wort, nicht der Wert', () => {
  assert.deepEqual(Object.keys(STATUS_LABEL).sort(), ['ok', 'open', 'prog', 'warn'].sort());
  assert.equal(STATUS_LABEL.prog, 'In Bearbeitung');
  assert.equal(STATUS_LABEL.warn, 'Prüfung erforderlich');
  assert.equal(STATUS_LABEL.open, 'Offen');
  // Und die Ableitung selbst ist unangetastet: dieselbe Regel liefert denselben Schlüssel.
  const s: SchrittStatus = statusAus([{ status: 'ok', text: 'a' }, { status: 'ok', text: 'b' }]);
  assert.equal(s, 'ok');
  assert.equal(statusAus([{ status: 'warn', text: 'a' }]), 'warn');
  assert.equal(statusAus([]), 'open');
});

test('K4: kein Schritt hat seinen Status gewechselt', () => {
  const schritte = ableitenSchritte(null);
  assert.equal(schritte.length, 11, 'die elf Schritte und ihre Reihenfolge bleiben');
  // Ohne Szene ist jeder Schritt offen — das war vor diesem Posten so und bleibt so.
  assert.deepEqual([...new Set(schritte.map((s) => s.status))], ['open']);
});

// --- (c) K6/K7: die leere Karte verschwindet ----------------------------------------------------
test('K6: eine leere Aufgabenliste hinterlässt KEINE leere Überschrift', () => {
  const q = ohneKommentare(guided);
  assert.match(q, /\{s\.aufgaben\.length > 0 && \(/, 'die Karte wird nicht bedingt gerendert');
  // Die Überschrift steht INNERHALB der Bedingung — sonst bliebe sie stehen.
  const abBedingung = q.slice(q.indexOf('{s.aufgaben.length > 0 && ('));
  const bisEnde = abBedingung.slice(0, abBedingung.indexOf('{s.empfehlung &&'));
  assert.match(bisEnde, />Aufgabe<\/h4>/, 'die Überschrift muss mit der Karte verschwinden');
});

test('K7: eine nicht-leere Liste rendert unverändert — Zeichen für Zeichen', () => {
  const q = ohneKommentare(guided);
  // Der Inhalt der Karte ist unangetastet: Titel, Detail, Warnzeichen, Trennlinie.
  assert.match(q, /\{s\.aufgaben\.map\(\(a\) => \(/);
  assert.match(q, /\{a\.titel\}/);
  // **Nachgezogen in AUF-38 Scheibe 6:** das Detail trug seinen Stil inline und traegt ihn jetzt
  // als `.hp-gf-detail`. **Die Aussage dieser Zusage ist der Inhalt, nicht die Gestalt** — dass das
  // Detail ueberhaupt gerendert wird, und nur wenn es eines gibt. Der Stil wird deshalb dort
  // geprueft, wo er wohnt: in der Stilschicht.
  assert.match(q, /\{a\.detail && <div className="hp-gf-detail">\{a\.detail\}<\/div>\}/);
  const stil = readFileSync(new URL('../hausplaner.css', import.meta.url), 'utf8');
  assert.match(stil, /\.hp-gf-detail \{[^}]*font-size: 12\.5px[^}]*color: var\(--hp-muted\)[^}]*\}/,
    'die Werte des Details sind beim Umzug verlorengegangen');
  assert.match(q, /a\.warn \? T\.warnInk : T\.ink/);
});

// --- Der Befund, der den Auftrag korrigiert -----------------------------------------------------
test('gemessen: KEIN Schritt trägt heute Aufgaben — die leere Karte ist der Regelfall', () => {
  const schritte = ableitenSchritte(null);
  assert.equal(schritte.filter((s) => s.aufgaben.length > 0).length, 0,
    'trägt wieder einer Aufgaben, ist das eine Änderung an der Ableitung und gehört begründet');
  assert.equal(schritte.filter((s) => s.empfehlung !== null).length, 0,
    'dasselbe für die Empfehlungskarte — auch sie rendert heute nie');
});

test('die erfundenen Daten erreichen die Fläche nicht — sie sind stillgelegt', () => {
  // Genau das hat der Auftrag gezählt, als er „jeder Schritt trägt einen Eintrag" schrieb.
  assert.ok(STEPS_STILLGELEGT.some((s) => s.aufgaben.length > 0), 'die Demo-Daten tragen Einträge');
  // Aber niemand rendert sie: die geführte Planung bekommt ihre Schritte als Eigenschaft herein.
  const q = ohneKommentare(guided);
  assert.doesNotMatch(q, /STEPS_STILLGELEGT/, 'die Fläche darf die Demo-Konstante nicht kennen');
  assert.match(q, /const STEPS = schritte;/, 'die Schritte kommen aus dem Dokument, nicht aus der Datei');
});

test('die abgeleiteten Schritte tragen echte Prüfpunkte — die Fläche ist nicht inhaltslos', () => {
  // Wichtig für die Einordnung: es verschwindet nur die leere Hülle, nicht die Aussage.
  const schritte = ableitenSchritte(null);
  assert.ok(schritte.filter((s) => s.checks.length > 0).length >= 5,
    'die Schrittkarte selbst zeigt weiterhin, was im Modell steht');
});
