/**
 * AUF-36 — der Funktionsvertrag der 110 Werkzeuge.
 *
 * Geprüft wird, was der Auftrag als Abnahme benennt: **eine** Aktivierungs-Engine (K3), **eine**
 * Ausführungsschicht (K4), die Bijektion 9+101=110 (K5), **alle zwölf** Vorbedingungen zugeordnet
 * ohne Zeile „sonstige" (K6) und die **fünf heute unerfüllbaren** mit ehrlichem Grund (K7).
 *
 * Je Vorbedingung steht ein **erfüllter und ein verletzter Fall**, und geprüft wird nicht nur das
 * Boolean, sondern **der Grund** (§4.4) — ein gesperrtes Werkzeug, das nicht sagt warum, ist der
 * Mangel, den dieser Posten behebt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  WERKZEUG_VERTRAEGE, vertrag, vertraegeDerFamilie, vokabular, doppelteVertraege,
} from '../app/tools/werkzeugVertrag';
import {
  VORBEDINGUNGEN, regelnFuer, heuteUnerfuellbar, offeneLuecken, unbekannteVorbedingungen,
  FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_WAND_DA, FAEHIGKEIT_ANSICHT_BEREIT,
  RECHT_BEARBEITEN, RECHT_IMPORTIEREN,
} from '../app/tools/vorbedingungen';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { resolveToolState } from '../app/tools/activation';
import { baueAktivierungsKontext } from '../app/tools/toolContext';
import type { ObjectType } from '../app/tools/toolTypes';
import { WERKZEUGE_GESAMT } from '../app/tools/toolRegistry';
import { EIGENE_WERKZEUGE } from '../app/tools/toolRegistry';

const hier = dirname(fileURLToPath(import.meta.url));

/** Der Normalfall der laufenden App: Plan offen, Geschoss aktiv, Wand da, Bearbeitungsrecht. */
const ALLES_DA = {
  workspace: 'architektur', view: '2d' as const, selectionTypes: [] as ObjectType[],
  permissions: [RECHT_BEARBEITEN],
  capabilities: [FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_WAND_DA, FAEHIGKEIT_ANSICHT_BEREIT],
};

// --- Der Vertrag selbst -------------------------------------------------------------------------
test('K5: 110 Verträge, je Werkzeug genau einer, keine Dublette', () => {
  assert.equal(WERKZEUG_VERTRAEGE.length, WERKZEUGE_GESAMT);
  assert.deepEqual(doppelteVertraege(), []);
  for (const t of [...TOOL_DEFINITIONS, ...TOOL_KATALOG]) {
    assert.ok(vertrag(t.id), `${t.id} hat keinen Funktionsvertrag`);
  }
  // und kein Vertrag zeigt auf ein Werkzeug, das es nicht gibt
  const ids = new Set([...TOOL_DEFINITIONS, ...TOOL_KATALOG].map((t) => t.id));
  for (const v of WERKZEUG_VERTRAEGE) {
    assert.ok(ids.has(v.werkzeugId), `Vertrag für unbekanntes Werkzeug: ${v.werkzeugId}`);
  }
});

test('K6: die Vokabulare sind so klein wie gemessen — 12 / 11 / 9', () => {
  const v = vokabular();
  assert.equal(v.vorbedingungen.length, 12);
  assert.equal(v.seiteneffekte.length, 11);
  assert.equal(v.familien.length, 9);
  assert.equal(new Set(WERKZEUG_VERTRAEGE.map((x) => x.commandId)).size, WERKZEUGE_GESAMT, 'je Werkzeug eine eigene commandId');
});

test('K6: jede Vorbedingung aus den Verträgen ist zugeordnet — keine Zeile „sonstige"', () => {
  assert.deepEqual(unbekannteVorbedingungen(vokabular().vorbedingungen), []);
  assert.equal(Object.keys(VORBEDINGUNGEN).length, 12, 'die Tabelle führt genau die zwölf');
});

test('Metadaten sind mitgekommen: umkehrbar 78/33, protokollpflichtig 92/19', () => {
  // Z-05-N1: `kontur` ist umkehrbar (Esc verwirft den Zug) und NICHT protokollpflichtig
  // (sie schreibt nichts). Deshalb waechst nur die erste Zahl. **Bewusst hart und nicht
  // `+ EIGENE_WERKZEUGE.length`:** ob ein Werkzeug umkehrbar ist, ist eine Aussage ueber
  // dieses Werkzeug, keine Bilanz — eine Formel wuerde hier eine Regel behaupten, die es nicht gibt.
  assert.equal(WERKZEUG_VERTRAEGE.filter((v) => v.umkehrbar).length, 78);
  assert.equal(WERKZEUG_VERTRAEGE.filter((v) => v.protokollpflichtig).length, 92);
  assert.equal(vertraegeDerFamilie('selection').length, 4);
  assert.equal(vertrag('wand')?.commandId, 'WallCommand');
});

// --- K3/K4: genau eine Engine, genau eine Ausführungsschicht ------------------------------------
/** Alle Quelldateien des Planers — für die beiden Eindeutigkeits-Belege. */
function alleQuellen(pfad: string, treffer: string[] = []): string[] {
  for (const e of readdirSync(pfad)) {
    const p = join(pfad, e);
    if (statSync(p).isDirectory()) alleQuellen(p, treffer);
    else if (/\.(ts|tsx)$/.test(e)) treffer.push(p);
  }
  return treffer;
}
const quellen = alleQuellen(join(hier, '..')).filter((p) => !p.includes('__tests__'));

/**
 * Kommentare weg, bevor über den Quelltext geprüft wird. Sonst schlägt der Test auf die **Zusage**
 * an, die genau das verbietet, was er sucht („es entsteht kein zweites `resolveDisabledReasons`") —
 * und meldet als Fund, was in Wahrheit das Versprechen ist. Genau das ist mir hier passiert.
 */
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const code = (p: string): string => ohneKommentare(readFileSync(p, 'utf8'));

test('K3: außer resolveToolState erzeugt keine Funktion einen Sperrgrund', () => {
  const zweite = quellen.filter((p) => /resolveDisabledReasons|function\s+\w*[Dd]isabledReason/.test(code(p)));
  assert.deepEqual(zweite, [], 'eine zweite Sperrgrund-Quelle wäre eine zweite Wahrheit');
  // Die Engine steht genau einmal.
  const engines = quellen.filter((p) => /export function resolveToolState/.test(code(p)));
  assert.equal(engines.length, 1);
});

test('K4: `runTool` aus dem Paket kommt im Repo nicht vor — Ausführung bleibt bei applyCommand', () => {
  const treffer = quellen.filter((p) => /\brunTool\b/.test(code(p)));
  assert.deepEqual(treffer, [], 'ein zweiter Ausführungsweg verlöre Undo und CommandAbgelehnt');
  // Der Vertrag nennt Dienstmethoden — aufgerufen wird keine.
  const aufrufe = quellen.filter((p) => /dienstMethode\s*\(|\.dienstMethode\b\s*\(/.test(code(p)));
  assert.deepEqual(aufrufe, [], '`dienstMethode` ist ein Metadatum, kein Aufruf');
});

test('K1/I2-Zusage: der Vertrag hängt NEBEN der Werkzeugdefinition, kein Feld der Bestandsform geändert', () => {
  const typen = readFileSync(join(hier, '../app/tools/toolTypes.ts'), 'utf8');
  for (const feld of ['commandId', 'preconditions', 'sideEffects', 'undoable', 'auditRequired', 'serviceMethod']) {
    assert.doesNotMatch(typen, new RegExp(`^\\s*${feld}[?]?:`, 'm'), `${feld} gehört nicht in ToolDefinition`);
  }
});

// --- Je Vorbedingung: ein erfüllter, ein verletzter Fall — mit Grund ----------------------------
const FAELLE: Array<{ bedingung: string; verletzt: Partial<typeof ALLES_DA>; erfuellt?: Partial<typeof ALLES_DA> }> = [
  { bedingung: 'project.open', verletzt: { capabilities: [FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_ANSICHT_BEREIT] } },
  { bedingung: 'viewport.ready', verletzt: { capabilities: [FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA] } },
  { bedingung: 'activeLevel.exists', verletzt: { capabilities: [FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_ANSICHT_BEREIT] } },
  { bedingung: 'hostWall.exists', verletzt: { capabilities: [FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_ANSICHT_BEREIT] } },
  { bedingung: 'selection.count >= 1', verletzt: { selectionTypes: [] }, erfuellt: { selectionTypes: ['wall'] as ObjectType[] } },
  { bedingung: 'selection.hasRoofFace', verletzt: { selectionTypes: ['wall'] as ObjectType[] }, erfuellt: { selectionTypes: ['roof'] as ObjectType[] } },
  { bedingung: 'permission.edit', verletzt: { permissions: [] } },
  { bedingung: 'permission.import', verletzt: { permissions: [RECHT_BEARBEITEN] }, erfuellt: { permissions: [RECHT_BEARBEITEN, RECHT_IMPORTIEREN] } },
];

/**
 * Geprüft wird an einem **synthetischen** Werkzeug, das genau EINE Vorbedingung trägt. Grund: ein
 * echtes Werkzeug trägt oft mehrere, und die Engine meldet die **erste** verletzte — der Test
 * würde dann den Grund einer anderen Bedingung messen und wäre wertlos. Das Muster (synthetisches
 * Werkzeug für die Regelprüfung) steht schon in `activation.test.ts`, ich erfinde kein zweites.
 * Dass die Bedingung auch in den **echten** Daten vorkommt, prüft derselbe Test gleich mit.
 */
function synthetisch(bedingung: string): typeof TOOL_KATALOG[number] {
  return {
    id: `pruef-${bedingung}`, label: 'Prüfwerkzeug', icon: '', groupId: 'Prüfung', art: 'werkzeug',
    supportedWorkspaces: [], supportedViews: [], helpText: 'nur für den Test',
    activationRules: regelnFuer([bedingung]),
  } as typeof TOOL_KATALOG[number];
}

for (const fall of FAELLE) {
  test(`Vorbedingung ${fall.bedingung}: erfüllt aktiv, verletzt gesperrt — mit Grund`, () => {
    const abbildung = VORBEDINGUNGEN[fall.bedingung];
    assert.ok(abbildung, `${fall.bedingung} fehlt in der Tabelle`);
    // Die Bedingung kommt in den echten Verträgen vor — sonst prüfte der Test eine tote Zeile.
    const echte = [...TOOL_DEFINITIONS, ...TOOL_KATALOG]
      .filter((t) => (vertrag(t.id)?.vorbedingungen ?? []).includes(fall.bedingung));
    assert.ok(echte.length > 0, `kein Werkzeug trägt ${fall.bedingung}`);

    const werkzeug = synthetisch(fall.bedingung);
    const erfuellt = resolveToolState(werkzeug, baueAktivierungsKontext({ ...ALLES_DA, ...(fall.erfuellt ?? {}) }));
    const verletzt = resolveToolState(werkzeug, baueAktivierungsKontext({ ...ALLES_DA, ...fall.verletzt }));

    // Der verletzte Fall ist gesperrt UND nennt den Grund — nicht nur `false`.
    assert.equal(verletzt.enabled, false, `${fall.bedingung} verletzt, trotzdem aktiv`);
    assert.equal(verletzt.reason, abbildung.regel.grund, `${fall.bedingung}: falscher oder fehlender Grund`);
    // Der erfüllte Fall ist aktiv — bei `permission.import` nur, wenn das Recht mitgegeben wird.
    assert.equal(erfuellt.enabled, true, `${fall.bedingung}: alles da, trotzdem gesperrt (${erfuellt.reason})`);
  });
}

test('am echten Werkzeug: dieselbe Sperre, derselbe Grund — nicht nur am Prüfobjekt', () => {
  // `raum` trägt laut Vertrag `project.open` und `activeLevel.exists`. Ohne Geschoss ist es
  // gesperrt, und der Grund ist der des Vertrags — der Weg über die echten Daten, nicht das Muster.
  const raum = TOOL_KATALOG.find((t) => t.id === 'raum');
  assert.ok(raum);
  assert.ok((vertrag('raum')?.vorbedingungen ?? []).includes('activeLevel.exists'));
  const ohneGeschoss = resolveToolState(raum, baueAktivierungsKontext({
    ...ALLES_DA, capabilities: [FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_ANSICHT_BEREIT],
  }));
  assert.equal(ohneGeschoss.enabled, false);
  assert.equal(ohneGeschoss.reason, VORBEDINGUNGEN['activeLevel.exists'].regel.grund);
});

// --- K7: die fünf heute unerfüllbaren, mit ehrlichem Grund --------------------------------------
test('K7: die Fach-Vorbedingungen sind unerfüllbar — benannt, nicht ausgelassen', () => {
  // AUF-53 hat `permission.import` aus dieser Liste geholt: seit der Zuordnung auf `Hausplaner,add`
  // hängt sie an einem Recht, das es wirklich gibt. Übrig bleiben die vier Fach-Operanden aus der
  // Auslegung — sie brauchen einen Rechenstand, den der Planer nicht führt.
  const luecken = offeneLuecken();
  assert.deepEqual(
    luecken.map((l) => l.vorbedingung).sort(),
    ['component.thermalRelevant', 'heatEmitters.sized', 'heatingLoad.approved', 'heatingNetwork.connected'],
  );
});

test('K7: kein Grund ist leer, keiner vertröstet auf „folgt" oder „in Kürze"', () => {
  for (const [name, a] of Object.entries(VORBEDINGUNGEN)) {
    assert.ok(a.regel.grund.length > 10, `${name}: Grund zu dünn`);
    assert.doesNotMatch(a.regel.grund, /folgt|in Kürze|demnächst|bald|kommt noch/i, `${name}: Vertröstung statt Grund`);
    // Der Grund ist ein deutscher Satz, nicht das Vokabular (§4.3).
    assert.doesNotMatch(a.regel.grund, /activeLevel|project\.open|viewport|precondition/i, `${name}: Vokabular statt Satz`);
    if (!a.heuteErfuellbar) {
      assert.ok((a.lueckeGrund ?? '').length > 30, `${name}: die Lücke ist nicht erklärt`);
    }
  }
});

test('K7: ein Werkzeug mit Fach-Vorbedingung ist gesperrt und sagt es — auch wenn alles andere da ist', () => {
  const mitFach = TOOL_KATALOG.find((t) => heuteUnerfuellbar(vertrag(t.id)?.vorbedingungen ?? []).length > 0);
  assert.ok(mitFach, 'kein Werkzeug trägt eine Fach-Vorbedingung');
  const zustand = resolveToolState(mitFach, baueAktivierungsKontext(ALLES_DA));
  assert.equal(zustand.enabled, false);
  assert.ok((zustand.reason ?? '').length > 10);
  assert.doesNotMatch(zustand.reason ?? '', /undefined|null/);
});

// --- Die Regeln kommen als DATEN in die vorhandene Engine ---------------------------------------
test('K3: die Regeln eines Werkzeugs sind genau die seines Vertrags — nichts erfunden, nichts verloren', () => {
  for (const t of TOOL_KATALOG) {
    const erwartet = regelnFuer(vertrag(t.id)?.vorbedingungen ?? []);
    assert.deepEqual(t.activationRules ?? [], erwartet, `${t.id}: Regeln weichen vom Vertrag ab`);
  }
});

test('die 9 Registry-Werkzeuge behalten ihre handgepflegten Regeln (Bestand gewinnt)', () => {
  // Der Vertrag hängt an ihnen als Daten, aber er überschreibt die Bestandsdefinition nicht —
  // Konflikt-Regel der Bauordnung: der neue Code passt sich an, nicht umgekehrt.
  const wand = TOOL_DEFINITIONS.find((t) => t.id === 'wand');
  assert.ok(wand);
  assert.ok(vertrag('wand'), 'der Vertrag ist trotzdem da');
  assert.equal(resolveToolState(wand, baueAktivierungsKontext(ALLES_DA)).enabled, true);
});
