/**
 * AUF-39 / L5 — die elf Wizard-Schritte aus dem Modell.
 *
 * **Das eigentliche Kriterium dieses Auftrags ist K5:** ein leeres Dokument liefert **keinen**
 * grünen Schritt und **keinen** grünen Prüfpunkt. Vorher stand dort „Bauherr & Adresse ✓" und
 * „Maßstab erkannt · 1:50 ✓" — in einem Projekt, in dem nichts angelegt war.
 *
 * Dazu: Reinheit (K3), unveränderte Titel (K4), kein Blindtext (K6) und die Nachrechenbarkeit
 * Dokument → Prüfpunkt → Status an einem gebauten Beispiel (K7).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { ableitenSchritte, statusAus, schrittTitel, SCHRITTE_OHNE_GRUNDLAGE } from '../app/dashboard/fahrschritte';
import { STEPS_STILLGELEGT } from '../app/studioDaten';
import type { SceneDocument, SceneNode } from '../domain/scene.types';

/** Ein leeres, aber gültiges Dokument — genau der Zustand nach „Neues Projekt". */
function leeresDokument(): SceneDocument {
  return {
    id: '11111111-1111-4111-8111-111111111111', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'Erdgeschoss', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [], materials: [], roofs: [], ceilings: [],
    metadata: { createdAt: '2026-07-25T00:00:00Z', updatedAt: '2026-07-25T00:00:00Z' },
  } as unknown as SceneDocument;
}

const knoten = (id: string, felder: Record<string, unknown>): SceneNode => ({
  id, levelId: 'eg', visible: true, locked: false, tags: [],
  createdAt: '2026-07-25T00:00:00Z', updatedAt: '2026-07-25T00:00:00Z', ...felder,
} as unknown as SceneNode);

// --- K5: das eigentliche Kriterium --------------------------------------------------------------
test('K5: ein LEERES Dokument liefert keinen grünen Schritt und keinen grünen Prüfpunkt', () => {
  for (const [name, scene] of [['null', null], ['leeres Dokument', leeresDokument()]] as const) {
    const schritte = ableitenSchritte(scene);
    assert.equal(schritte.length, 11, `${name}: es sind elf Schritte`);
    const gruen = schritte.filter((s) => s.status === 'ok');
    assert.deepEqual(gruen.map((s) => s.titel), [], `${name}: kein Schritt darf grün sein`);
    const gruenePunkte = schritte.flatMap((s) => s.checks).filter((c) => c.status === 'ok');
    assert.deepEqual(gruenePunkte.map((c) => c.text), [], `${name}: kein Prüfpunkt darf grün sein`);
  }
});

test('K5-Gegenprobe an den stillgelegten Demo-Daten: DIE behaupteten grün — genau das war der Mangel', () => {
  // Ohne diesen Vergleich bliebe unbelegt, dass sich überhaupt etwas geändert hat.
  const alteGruene = STEPS_STILLGELEGT.filter((s) => s.status === 'ok');
  assert.ok(alteGruene.length > 0, 'die Demo-Daten trugen mindestens einen grünen Schritt');
  const alteGruenePunkte = STEPS_STILLGELEGT.flatMap((s) => s.checks).filter((c) => c.status === 'ok');
  assert.ok(alteGruenePunkte.length >= 5, `die Demo-Daten trugen ${alteGruenePunkte.length} grüne Prüfpunkte`);
  assert.ok(alteGruenePunkte.some((c) => /Maßstab erkannt/.test(c.text)), 'inklusive des erfundenen Maßstabs');
});

// --- K3: rein -----------------------------------------------------------------------------------
test('K3: zweimal mit demselben Dokument ⇒ tief gleiches Ergebnis (keine Zeit, kein Zufall)', () => {
  const doc = leeresDokument();
  assert.deepEqual(ableitenSchritte(doc), ableitenSchritte(doc));
  const voll = { ...doc, nodes: [knoten('w1', { type: 'wall' }), knoten('f1', { type: 'window' })] } as SceneDocument;
  assert.deepEqual(ableitenSchritte(voll), ableitenSchritte(voll));
});

test('K2: die Ableitung LIEST das Dokument — sie ändert es nicht', () => {
  const doc = leeresDokument();
  const vorher = JSON.stringify(doc);
  ableitenSchritte(doc);
  assert.equal(JSON.stringify(doc), vorher, 'das Dokument darf nicht angefasst werden');
});

// --- K4: elf Schritte, Titel byte-genau ---------------------------------------------------------
test('K4: elf Schritte, Titel und Reihenfolge unverändert gegenüber den stillgelegten STEPS', () => {
  assert.equal(STEPS_STILLGELEGT.length, 11);
  assert.deepEqual(schrittTitel(), STEPS_STILLGELEGT.map((s) => s.titel));
});

// --- K6: kein Blindtext -------------------------------------------------------------------------
test('K6: kein Hinweis ist leer, keiner vertröstet auf „folgt" oder „in Kürze"', () => {
  const doc = { ...leeresDokument(), nodes: [knoten('w1', { type: 'wall' })] } as SceneDocument;
  for (const scene of [null, leeresDokument(), doc]) {
    for (const s of ableitenSchritte(scene)) {
      assert.ok(s.hinweis.trim().length > 15, `${s.titel}: Hinweis zu dünn`);
      assert.doesNotMatch(s.hinweis, /folgt|in Kürze|demnächst|bald|kommt noch/i, `${s.titel}: Vertröstung`);
      for (const c of s.checks) {
        assert.ok(c.text.trim().length > 3, `${s.titel}: leerer Prüfpunkt`);
      }
    }
  }
});

test('K6: ein Schritt ohne Modellgrundlage sagt, WAS fehlt — und trägt keine erfundenen Prüfpunkte', () => {
  const schritte = ableitenSchritte(leeresDokument());
  for (const titel of Object.keys(SCHRITTE_OHNE_GRUNDLAGE)) {
    const s = schritte.find((x) => x.titel === titel);
    assert.ok(s, `${titel} fehlt`);
    assert.equal(s.status, 'open', `${titel}: ohne Grundlage darf nichts anderes als offen stehen`);
    assert.deepEqual(s.checks, [], `${titel}: ein Schritt ohne Grundlage hat keine Prüfpunkte`);
    assert.ok(s.hinweis.length > 40, `${titel}: der Grund ist nicht erklärt`);
  }
  assert.equal(Object.keys(SCHRITTE_OHNE_GRUNDLAGE).length, 6, 'sechs der elf haben heute keine Grundlage');
});

// --- K7: Nachrechenbarkeit Dokument → Prüfpunkt → Status ----------------------------------------
test('K7 Geschosse: gezählt wird, was BEBAUT ist — ein leeres Geschoss ist kein Fortschritt', () => {
  const zweiEbenen = [
    { id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 },
    { id: 'og', name: 'OG', elevation: 2700, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 1 },
  ];
  // Zwei Geschosse, aber nichts darin: der Nutzer hat noch nichts getan.
  const leer = { ...leeresDokument(), levels: zweiEbenen } as unknown as SceneDocument;
  const sLeer = ableitenSchritte(leer).find((x) => x.titel === 'Geschosse und Gebäude')!;
  assert.equal(sLeer.status, 'open');
  assert.ok(sLeer.checks.every((c) => c.status !== 'ok'), 'leere Geschosse sind kein grüner Haken');

  // Eine Wand im EG ⇒ ein bebautes Geschoss von zweien, Decke fehlt ⇒ `prog`
  const doc = { ...leer, nodes: [knoten('w1', { type: 'wall' })] } as SceneDocument;
  const s = ableitenSchritte(doc).find((x) => x.titel === 'Geschosse und Gebäude')!;
  assert.ok(s.checks.some((c) => c.status === 'ok' && c.text === '1 von 2 Geschossen bebaut'));
  assert.equal(s.status, 'prog');
});

test('K7 Öffnungen: 12 Fenster und 3 Türen stehen mit ihrer Zahl im Prüfpunkt', () => {
  const nodes = [
    ...Array.from({ length: 12 }, (_, i) => knoten(`f${i}`, { type: 'window' })),
    ...Array.from({ length: 3 }, (_, i) => knoten(`t${i}`, { type: 'door' })),
    knoten('st1', { type: 'object', objectType: 'stair' }),
  ];
  const s = ableitenSchritte({ ...leeresDokument(), nodes } as SceneDocument).find((x) => x.titel === 'Fenster, Türen und Treppen')!;
  assert.ok(s.checks.some((c) => c.text === '12 Fenster gesetzt' && c.status === 'ok'));
  assert.ok(s.checks.some((c) => c.text === '3 Türen gesetzt' && c.status === 'ok'));
  assert.ok(s.checks.some((c) => c.text === '1 Treppe gesetzt' && c.status === 'ok'));
  assert.equal(s.status, 'ok', 'alle drei erfüllt ⇒ grün');
});

test('K7 der verletzte Zwang: zwei Geschosse ohne Treppe ⇒ `warn`, nicht `open`', () => {
  const doc = {
    ...leeresDokument(),
    levels: [
      { id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 },
      { id: 'og', name: 'OG', elevation: 2700, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 1 },
    ],
    nodes: [knoten('f1', { type: 'window' }), knoten('t1', { type: 'door' })],
  } as unknown as SceneDocument;
  const s = ableitenSchritte(doc).find((x) => x.titel === 'Fenster, Türen und Treppen')!;
  const treppe = s.checks.find((c) => /Treppe/.test(c.text))!;
  assert.equal(treppe.status, 'warn', 'zwei Geschosse ohne Verbindung sind kein „noch offen", sondern ein Widerspruch');
  assert.equal(s.status, 'warn', 'ein verletzter Zwang schlägt auf den Schritt durch');
});

// --- Der Status folgt aus den Prüfpunkten, nicht umgekehrt --------------------------------------
test('statusAus: alle erfüllt ⇒ ok · alle offen ⇒ open · gemischt ⇒ prog · ein warn ⇒ warn', () => {
  const ok = { status: 'ok' as const, text: 'a' };
  const off = { status: 'open' as const, text: 'b' };
  const warn = { status: 'warn' as const, text: 'c' };
  assert.equal(statusAus([ok, ok]), 'ok');
  assert.equal(statusAus([off, off]), 'open');
  assert.equal(statusAus([ok, off]), 'prog');
  assert.equal(statusAus([ok, warn]), 'warn', 'eine Verletzung schlägt jede Erfüllung');
  assert.equal(statusAus([]), 'open', 'ohne prüfbare Aussage ist ein Schritt nicht fertig, sondern unbekannt');
});

test('nichts rendert die stillgelegten Demo-Daten mehr', async () => {
  const { readFileSync, readdirSync, statSync } = await import('node:fs');
  const { fileURLToPath } = await import('node:url');
  const { dirname, join } = await import('node:path');
  const wurzel = join(dirname(fileURLToPath(import.meta.url)), '../app');
  const dateien: string[] = [];
  const sammle = (p: string): void => {
    for (const e of readdirSync(p)) {
      const voll = join(p, e);
      if (statSync(voll).isDirectory()) sammle(voll);
      else if (/\.(ts|tsx)$/.test(e) && !e.endsWith('studioDaten.ts')) dateien.push(voll);
    }
  };
  sammle(wurzel);
  const nutzer = dateien.filter((f) => /\bSTEPS_STILLGELEGT\b/.test(readFileSync(f, 'utf8')));
  assert.deepEqual(nutzer, [], 'die Demo-Daten sind Beleg, keine Quelle');
});
