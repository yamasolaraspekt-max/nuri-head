/**
 * AUF-55 — **die Snapshot-Naht wird ausgesprochen.**
 *
 * **Der gemessene Zustand vor diesem Posten:** `objekt.blade.php` setzt `data-snapshots-url`, drei
 * Routen legen Planungsstände an, listen und stellen sie wieder her — und **kein Zeichen davon
 * erreicht die Insel**. `main.tsx` liest `speichernUrl`, `rechte`, `projekte` und `paketeUrl`, aber
 * nicht die Snapshot-Adresse.
 *
 * **Und: es gibt keine wirkungslose Snapshot-Fläche.** Der Auftrag ging davon aus, eine solche
 * Fläche sei zu kennzeichnen. Gemessen ist es umgekehrt — es gibt **gar keine**. Eine Naht, die
 * niemand sieht, ist schlimmer als eine leere Fläche: sie wird beim nächsten Mal neu erfunden,
 * weil niemand weiß, dass sie schon da ist.
 *
 * **Was dieser Posten NICHT tut:** anbinden. Kein `fetch`, keine Route angefasst, kein Zeichen im
 * Blade geändert. *Wer „nur schnell" die Liste anschließt, hat einen Backend-Posten gebaut, den
 * niemand beauftragt hat.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { renderToStaticMarkup } from 'react-dom/server';
import { createElement } from 'react';
import { PANEL_TABS, panelTab } from '../app/dashboard/panelTabs';
import { ZustandBadge } from '../app/studioUi';

const hier = dirname(fileURLToPath(import.meta.url));
const wurzel = join(hier, '..');
const historie = panelTab('historie')!;

// --- K1: kein Blindtext, keine Vertröstung -------------------------------------------------------
test('K1: der Hinweis ist nicht leer und nicht dünn', () => {
  assert.ok(historie, 'den Reiter gibt es');
  assert.ok(historie.hinweis.length > 40, `zu dünn: „${historie.hinweis}"`);
});

test('K1: KEIN Reiter endet auf eine Vertröstung', () => {
  // „folgt", „in Kürze", „demnächst" sind Versprechen auf später — sie sagen nichts darüber, was
  // die Fläche zeigen wird, und altern zu einer Unwahrheit, sobald niemand sie einlöst.
  for (const t of PANEL_TABS) {
    for (const wort of ['folgt', 'in Kürze', 'demnächst', 'coming soon', 'keine Daten']) {
      assert.ok(!t.hinweis.includes(wort), `${t.id}: „${wort}" steht im Hinweis`);
    }
  }
});

test('K1: der Hinweis sagt, WAS entstehen wird — nicht nur, dass etwas entsteht', () => {
  assert.match(historie.hinweis, /welche Befehle dieses Bauteil verändert haben/);
  assert.match(historie.hinweis, /Planungsstände des Objekts/);
});

test('K1: und er sagt, was heute schon da ist — die Naht bleibt sichtbar', () => {
  // **Der Kern dieses Postens.** Nicht „hier kommt mal was", sondern: der Server kann es bereits,
  // die Fläche fehlt. Wer das nicht liest, baut die drei Routen ein zweites Mal.
  assert.match(historie.hinweis, /der Server heute schon anlegt, listet und wiederherstellt/);
  assert.match(historie.hinweis, /Angebunden ist die Fläche noch nicht\./,
    'der offene Rest wird benannt, nicht verschwiegen');
});

test('K1: der Zustand ist ausgewiesen — Text UND Symbol, nicht nur Farbe', () => {
  assert.equal(historie.zustand, 'in_entwicklung');
  const markup = renderToStaticMarkup(createElement(ZustandBadge, { zustand: historie.zustand }));
  assert.match(markup, /in Entwicklung/, 'das Wort steht da, nicht nur eine Farbe');
});

// --- K2: nichts angebunden, nichts angefasst ------------------------------------------------------
test('K2: die Insel liest die Snapshot-Adresse weiterhin NICHT', () => {
  const einstieg = readFileSync(join(wurzel, 'main.tsx'), 'utf8');
  assert.doesNotMatch(einstieg, /snapshotsUrl/, 'keine Anbindung — das wäre ein anderer Posten');
});

test('K2: nirgends in der Insel wird eine Snapshot-Route gerufen', () => {
  // Die Zeichenkette wird zusammengesetzt, damit diese Datei nicht selbst zum Treffer wird.
  const nadel = 'snapshots' + 'Url';
  const treffer: string[] = [];
  const gehe = (verzeichnis: string): void => {
    for (const name of readdirSync(verzeichnis)) {
      if (name === 'node_modules' || name === '__tests__') continue;
      const pfad = join(verzeichnis, name);
      if (statSync(pfad).isDirectory()) { gehe(pfad); continue; }
      if (!/\.tsx?$/.test(pfad)) continue;
      if (readFileSync(pfad, 'utf8').includes(nadel)) treffer.push(pfad.slice(wurzel.length + 1));
    }
  };
  gehe(wurzel);
  assert.deepEqual(treffer, [], `angebunden in: ${treffer.join(', ')}`);
});

test('K2: die tote Adresse im Blade bleibt stehen — sie ist die Naht', () => {
  // **Ausdrücklich nicht entfernt.** Wer sie wegräumt, muss sie später neu finden; genau daran ist
  // dieser Zustand entstanden.
  const blade = readFileSync(join(wurzel, '../../views/admin/hausplaner/objekt.blade.php'), 'utf8');
  assert.match(blade, /data-snapshots-url=/, 'die Naht ist unverändert vorhanden');
});

// --- Der Reiter bleibt, was er war ---------------------------------------------------------------
test('kein neuer Reiter, keine neue Fläche — es sind weiterhin vier', () => {
  // Der ehrliche Zustand steht dort, wo der Nutzer den Verlauf sucht. Eine fünfte Fläche wäre eine
  // Layout-Entscheidung, und die hat dieser Posten nicht.
  assert.equal(PANEL_TABS.length, 4);
  assert.deepEqual(PANEL_TABS.map((t) => t.id), ['allgemein', 'beziehungen', 'pruefungen', 'historie']);
});

test('die anderen drei Reiter sind unberührt', () => {
  assert.equal(panelTab('allgemein')?.zustand, 'verfuegbar');
  assert.equal(panelTab('pruefungen')?.zustand, 'verfuegbar');
  assert.equal(panelTab('beziehungen')?.hinweis,
    'Zeigt später, woran ein Bauteil hängt: Wand ↔ Öffnung, Geschoss, Dachfläche.');
});
