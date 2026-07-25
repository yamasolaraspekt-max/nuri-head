/**
 * AUF-45 — der erste Schritt.
 *
 * Geprüft wird, was der Auftrag als Abnahme benennt: **keine zweite Aktivierungsquelle** (K3), die
 * **Aktivierung ist unverändert** (K4), die Zählung stimmt **aus den Daten** (K5), der Wegweiser
 * **verschwindet** (K6), die beiden Platzhalter-Fälle sind unterschieden (K7) und kein Blindtext (K8).
 *
 * Der wichtigste Test ist K4: dieser Posten darf **keine einzige Sperre** verändern. Er ändert nur,
 * was die Oberfläche über die Sperren sagt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { naechsterSchritt, gruende, wegweiserSatz, type Kandidat } from '../app/tools/naechsterSchritt';
import { handlungZuGrund, VORBEDINGUNGEN, FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_ANSICHT_BEREIT, FAEHIGKEIT_GESCHOSS_DA, RECHT_BEARBEITEN } from '../app/tools/vorbedingungen';
import { brauchtOptionen, GESTEN_EINGABEN, vertrag, WERKZEUG_VERTRAEGE } from '../app/tools/werkzeugVertrag';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { resolveToolState } from '../app/tools/activation';
import { baueAktivierungsKontext } from '../app/tools/toolContext';
import type { ObjectType } from '../app/tools/toolTypes';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const modul = ohneKommentare(readFileSync(join(hier, '../app/tools/naechsterSchritt.ts'), 'utf8'));

const ALLE = [...TOOL_DEFINITIONS, ...TOOL_KATALOG];
const kontext = (caps: string[], sel: ObjectType[] = []) => baueAktivierungsKontext({
  workspace: 'architektur', view: '2d', selectionTypes: sel,
  permissions: [RECHT_BEARBEITEN], capabilities: caps,
});
const LEER = kontext([FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_ANSICHT_BEREIT]);
const MIT_GESCHOSS = kontext([FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_ANSICHT_BEREIT, FAEHIGKEIT_GESCHOSS_DA]);
const MIT_AUSWAHL = kontext([FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_ANSICHT_BEREIT, FAEHIGKEIT_GESCHOSS_DA], ['wall']);
const zustaende = (ctx: ReturnType<typeof kontext>) => ALLE.map((t) => resolveToolState(t, ctx));
/** Ein Kandidat, wie ihn die App baut: Grund + dieselben Werkzeuge im hypothetischen Kontext. */
const kandidat = (grund: string, ctx: ReturnType<typeof kontext>): Kandidat => ({ grund, danach: zustaende(ctx) });
const GESCHOSS_KANDIDAT = () => kandidat('Kein aktives Geschoss.', MIT_GESCHOSS);
const gesperrt = (ctx: ReturnType<typeof kontext>) => ALLE.filter((t) => !resolveToolState(t, ctx).enabled).map((t) => t.id);

// --- K4: die Aktivierung ist unverändert --------------------------------------------------------
test('K4: dieser Posten lockert KEINE Sperre — die gesperrten Mengen sind exakt die gemessenen', () => {
  // Hart hinterlegt aus der Messung VOR dem Umbau. Würde der Wegweiser irgendetwas freischalten,
  // stünde hier eine andere Zahl.
  assert.equal(gesperrt(LEER).length, 73, 'leerer Plan');
  assert.equal(gesperrt(MIT_GESCHOSS).length, 53, 'mit Geschoss');
  assert.equal(gesperrt(MIT_AUSWAHL).length, 28, 'mit Geschoss und Auswahl');
  // und jede gesperrte nennt ihren Grund — sonst wäre der Wegweiser nicht zählbar
  for (const ctx of [LEER, MIT_GESCHOSS, MIT_AUSWAHL]) {
    for (const z of zustaende(ctx)) {
      if (!z.enabled) assert.ok((z.reason ?? '').length > 5, 'gesperrt ohne Grund');
    }
  }
});

// --- K3: keine zweite Aktivierungsquelle --------------------------------------------------------
test('K3: das Modul wertet keine Vorbedingung aus — es zählt nur Zustände', () => {
  for (const verboten of ['resolveToolState', 'capabilities', 'supportedWorkspaces', 'VORBEDINGUNGEN', 'activationRules']) {
    assert.ok(!modul.includes(verboten), `${verboten} gehört nicht in den Wegweiser`);
  }
  // Die einzige Eingabe sind fertige Zustände.
  assert.match(modul, /export function naechsterSchritt\(\s*jetzt: readonly WerkzeugZustand\[\]/);
});

// --- K5: die Zählung stimmt aus den Daten -------------------------------------------------------
test('K5: im leeren Plan ist der größte Hemmschuh das fehlende Geschoss', () => {
  const w = naechsterSchritt(zustaende(LEER), [GESCHOSS_KANDIDAT()]);
  assert.ok(w);
  assert.equal(w.grund, VORBEDINGUNGEN['activeLevel.exists'].regel.grund);
  assert.equal(w.grund, 'Kein aktives Geschoss.');
});

test('K5: die genannte Zahl ist die GEMESSENE Differenz, nicht die Zahl der Wartenden', () => {
  const jetzt = zustaende(LEER);
  const w = naechsterSchritt(jetzt, [GESCHOSS_KANDIDAT()])!;
  const echteDifferenz = gesperrt(LEER).length - gesperrt(MIT_GESCHOSS).length;
  assert.equal(w.entsperrt, echteDifferenz, 'der Satz verspricht genau so viel, wie freikommt');
  // Und die Wartenden sind MEHR als die Freikommenden — genau deshalb steht die Differenz im Satz.
  assert.ok(w.wartend > w.entsperrt!, `wartend ${w.wartend} muss über entsperrt ${w.entsperrt} liegen`);
  assert.equal(w.entsperrt, 20);
  assert.equal(w.wartend, 22);
});

test('K5: ohne Kandidaten gewinnt die blosse Häufigkeit — und die zeigt auf den falschen Schritt', () => {
  // GEMESSEN und der Grund, warum es Kandidaten gibt: im leeren Plan sperrt „etwas auswählen" 23
  // Werkzeuge und damit MEHR als das fehlende Geschoss (22). Als erster Schritt wäre das
  // unbrauchbar — in einem leeren Plan gibt es nichts auszuwählen.
  const nurHaeufigkeit = naechsterSchritt(zustaende(LEER))!;
  assert.equal(nurHaeufigkeit.grund, 'Dafür muss zuerst etwas ausgewählt sein.');
  assert.equal(nurHaeufigkeit.wartend, 23);
  assert.equal(nurHaeufigkeit.entsperrt, null, 'ohne Messung wird nichts versprochen');
  assert.match(wegweiserSatz(nurHaeufigkeit, 'Wähle ein Bauteil aus'), /darauf warten 23 Werkzeuge/);

  // Mit gemessenem Kandidaten gewinnt der Schritt, der wirklich etwas löst.
  const gemessen = naechsterSchritt(zustaende(LEER), [GESCHOSS_KANDIDAT()])!;
  assert.equal(gemessen.grund, 'Kein aktives Geschoss.');
  assert.match(wegweiserSatz(gemessen, 'Lege ein Geschoss an'), /das schaltet 20 Werkzeuge frei/);
});

test('K5: Gleichstand entscheidet alphabetisch, nicht nach Katalog-Reihenfolge', () => {
  const kunst = [
    { enabled: false, reason: 'B-Grund' }, { enabled: false, reason: 'A-Grund' },
    { enabled: true, reason: null },
  ];
  assert.equal(naechsterSchritt(kunst)?.grund, 'A-Grund');
});

test('K5: nichts gesperrt ⇒ kein Wegweiser', () => {
  assert.equal(naechsterSchritt([{ enabled: true, reason: null }]), null);
  assert.equal(naechsterSchritt([]), null);
  assert.equal(gruende([{ enabled: true, reason: null }]).size, 0);
});

// --- K6: der Wegweiser verschwindet -------------------------------------------------------------
test('K6: mit aktivem Geschoss nennt der Wegweiser das Geschoss nicht mehr', () => {
  // Mit Geschoss löst kein messbarer Kandidat mehr etwas — der Wegweiser schweigt, statt einen
  // Ratschlag ohne Wirkung zu geben. Ohne Kandidaten bliebe die reine Häufigkeit übrig.
  assert.equal(naechsterSchritt(zustaende(MIT_GESCHOSS), [GESCHOSS_KANDIDAT()]), null);
  const ohneKandidaten = naechsterSchritt(zustaende(MIT_GESCHOSS))!;
  assert.notEqual(ohneKandidaten.grund, 'Kein aktives Geschoss.');
  assert.equal(ohneKandidaten.grund, 'Dafür muss zuerst etwas ausgewählt sein.', 'der nächste Hemmschuh rückt nach');
});

test('K6: der Wegweiser hängt am ORT, nicht mehr an einem hartkodierten Grund', () => {
  // AUF-57 hat genau diese Zeile ersetzt: sie war auf den einen Grund festgenagelt, der nie
  // eintritt (eine Szene hat immer ein Geschoss) — der Wegweiser konnte also nie erscheinen.
  const app = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));
  assert.doesNotMatch(app, /wegweiser\?\.grund === 'Kein aktives Geschoss\.'/, 'der Grund ist wieder hartkodiert');
  assert.match(app, /wegweiser\?\.ort === 'geschoss' \? wegweiser\.satz : null/);
  assert.match(app, /wegweiser\?\.ort === 'schiene' &&/);
});

// --- Die Handlung kommt aus der Vorbedingungs-Tabelle -------------------------------------------
test('die Aufforderung steht bei der Vorbedingung — kein zweites Register', () => {
  assert.equal(handlungZuGrund('Kein aktives Geschoss.')?.handlung, 'Lege ein Geschoss an');
  assert.equal(handlungZuGrund('Kein aktives Geschoss.')?.faehigkeit, 'activeLevel.exists');
  assert.equal(handlungZuGrund('Dafür muss zuerst etwas ausgewählt sein.')?.handlung, 'Wähle ein Bauteil aus');
  // Ein Grund ohne benannte Handlung ⇒ kein Ratschlag, kein erfundener Satz.
  assert.equal(handlungZuGrund('Keine Berechtigung zum Importieren.'), undefined);
  assert.equal(handlungZuGrund('gibt es nicht'), undefined);
});

// --- K7: zwei Platzhalter-Fälle -----------------------------------------------------------------
test('K7: „Markieren" braucht keine Optionen — es ist nicht in Entwicklung', () => {
  assert.equal(brauchtOptionen('auswahl'), false, 'Zeiger und Auswahlmodus sind Gesten, keine Optionen');
  assert.equal(brauchtOptionen('wand'), true, 'Wandtyp, Höhe, Dicke sind Optionen');
  const app = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));
  assert.match(app, /brauchtOptionen\(werkzeug\) \?/);
  assert.match(app, /Dieses Werkzeug braucht keine Optionen\./);
});

test('K7: die Regel des Auftrags („ohne eingaben") hätte NIEMANDEN getroffen — gemessen', () => {
  // Diese Messung ist der Grund, warum die Regel anders lautet als im Auftragstext. Sie steht als
  // Test da, damit die Abweichung nicht als Nachlässigkeit gelesen wird.
  assert.equal(WERKZEUG_VERTRAEGE.filter((v) => v.eingaben.length === 0).length, 0);
  const nurGesten = WERKZEUG_VERTRAEGE.filter((v) => v.eingaben.every((e) => GESTEN_EINGABEN.test(e)));
  assert.deepEqual(nurGesten.map((v) => v.werkzeugId).sort(), ['auswahl', 'entsperren', 'kopieren']);
});

test('K7: ohne Vertrag wird „Optionen folgen" gesagt — Vollständigkeit wird nicht behauptet', () => {
  assert.equal(vertrag('gibt-es-nicht'), undefined);
  assert.equal(brauchtOptionen('gibt-es-nicht'), true);
});

// --- K8: kein Blindtext -------------------------------------------------------------------------
test('K8: kein Wegweiser-Satz ist leer oder vertröstet', () => {
  for (const ctx of [LEER, MIT_GESCHOSS, MIT_AUSWAHL]) {
    const w = naechsterSchritt(zustaende(ctx));
    if (!w) continue;
    const h = handlungZuGrund(w.grund);
    if (!h) continue;
    const satz = wegweiserSatz(w, h.handlung);
    assert.ok(satz.length > 20, `zu dünn: ${satz}`);
    assert.doesNotMatch(satz, /folgt|in Kürze|demnächst|bald/i);
    assert.match(satz, /\d+ Werkzeug/, 'ohne Zahl ist es kein Wegweiser, sondern ein Spruch');
  }
});

// ================= AUF-57: Anlass und Ort ======================================================

/**
 * AUF-57 — der Wegweiser hängt jetzt am **Ort**, nicht an einem hartkodierten Grund. Jede benannte
 * Handlung hat genau eine Fläche, dort wo die Handlung stattfindet.
 */
test('AUF-57: jede benannte Handlung hat genau einen Ort — und die Orte sind verschieden', () => {
  const mitHandlung = Object.entries(VORBEDINGUNGEN).filter(([, a]) => a.handlung);
  assert.equal(mitHandlung.length, 3, 'drei benannte Handlungen: Geschoss, Auswahl, Wand');
  for (const [name, a] of mitHandlung) {
    assert.ok(a.ort, `${name}: Handlung ohne Ort — dann erschiene sie nirgends oder überall`);
    assert.match(a.ort, /^(geschoss|schiene)$/);
  }
  assert.equal(VORBEDINGUNGEN['activeLevel.exists'].ort, 'geschoss');
  assert.equal(VORBEDINGUNGEN['selection.count >= 1'].ort, 'schiene');
  assert.equal(VORBEDINGUNGEN['hostWall.exists'].ort, 'schiene');
});

test('AUF-57: ein Grund ohne Handlung hat auch keinen Ort — kein erfundener Ratschlag', () => {
  for (const [name, a] of Object.entries(VORBEDINGUNGEN)) {
    if (!a.handlung) assert.equal(a.ort, undefined, `${name}: Ort ohne Handlung`);
  }
  assert.equal(handlungZuGrund('Keine Berechtigung zum Importieren.'), undefined);
});

test('AUF-57: die Auswahl ist ein messbarer Anlass — dieselbe Engine, ein anderes Feld', () => {
  const h = handlungZuGrund('Dafür muss zuerst etwas ausgewählt sein.');
  assert.equal(h?.brauchtAuswahl, true, 'ohne diese Kennzeichnung baut niemand den Vergleich');
  assert.equal(h?.faehigkeit, undefined, 'die Auswahl ist keine Fähigkeit');
  // Gemessen: mit einer Auswahl werden 25 Werkzeuge bedienbar.
  const mitAuswahl = ALLE.map((t) => resolveToolState(t, kontext(
    [FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_ANSICHT_BEREIT, FAEHIGKEIT_GESCHOSS_DA], ['wall'],
  )));
  const w = naechsterSchritt(zustaende(MIT_GESCHOSS), [{ grund: 'Dafür muss zuerst etwas ausgewählt sein.', danach: mitAuswahl }])!;
  assert.equal(w.grund, 'Dafür muss zuerst etwas ausgewählt sein.');
  assert.equal(w.entsperrt, 25, 'die Zahl ist gemessen, nicht gesetzt');
  assert.match(wegweiserSatz(w, 'Wähle ein Bauteil aus'), /das schaltet 25 Werkzeuge frei/);
});

test('AUF-57: der Arbeitsbereich ist KEIN Anlass — gemessen sperrt jeder Wechsel MEHR', () => {
  // Das Ergebnis, mit dem dieser Posten rechnet (§5): nichts erfinden, damit etwas erscheint.
  // Architektur ist der grösste Bereich; ihn zu verlassen kostet Werkzeuge, statt welche zu bringen.
  const jetzt = zustaende(MIT_GESCHOSS);
  const gesperrtJetzt = jetzt.filter((z) => !z.enabled).length;
  for (const ws of ['import', 'bauphysik', 'heizung', 'elektro-pv']) {
    const danach = ALLE.map((t) => resolveToolState(t, {
      ...kontext([FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_ANSICHT_BEREIT, FAEHIGKEIT_GESCHOSS_DA]), workspace: ws,
    }));
    const differenz = gesperrtJetzt - danach.filter((z) => !z.enabled).length;
    assert.ok(differenz < 0, `${ws}: entsperrt ${differenz} — dann wäre es doch ein Anlass`);
    // Und der Mechanismus lehnt ihn von selbst ab: nur `entsperrt > 0` gewinnt.
    assert.equal(naechsterSchritt(jetzt, [{ grund: 'Bereichswechsel', danach }]), null);
  }
});

test('AUF-57: Schweigen bleibt möglich — löst kein Kandidat etwas, erscheint nirgends ein Hinweis', () => {
  const jetzt = zustaende(MIT_AUSWAHL);
  // Ein Kandidat, der nichts ändert: derselbe Zustand als „danach".
  assert.equal(naechsterSchritt(jetzt, [{ grund: 'egal', danach: jetzt }]), null);
});

test('AUF-57: die Aktivierung ist unverändert — dieselben Mengen wie zu AUF-45', () => {
  assert.equal(gesperrt(LEER).length, 73);
  assert.equal(gesperrt(MIT_GESCHOSS).length, 53);
  assert.equal(gesperrt(MIT_AUSWAHL).length, 28);
});
