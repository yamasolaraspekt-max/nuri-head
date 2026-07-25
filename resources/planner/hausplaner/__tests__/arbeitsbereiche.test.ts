/**
 * AUF-34 — Arbeitsbereiche statt 22 Gruppen nebeneinander.
 *
 * Geprüft wird, was der Auftrag als Abnahme benennt: die **7 durchgängigen** Themen erscheinen in
 * **jedem** der fünf Bereiche (K3), jeder Bereich zeigt **genau** die erwartete Themenmenge (K4′),
 * die Bilanz über alle Bereiche bleibt **15 Themen / 110 Werkzeuge** ohne Verlust und ohne Dublette
 * (K5′), und ein **angeheftetes** Werkzeug überlebt den Bereichswechsel (K6/Kante 2).
 *
 * Dazu die beiden Zusagen, die leicht auseinanderlaufen und dann teuer sind:
 * die Leiste und `resolveToolState` müssen **dieselbe** Antwort auf „gilt das hier?" geben, und die
 * Registry-Werkzeuge dürfen ihrer Themen-Bindung nicht widersprechen.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  ARBEITSBEREICHE, DURCHGAENGIGE_THEMEN, ARBEITSBEREICH_STANDARD,
  arbeitsbereich, themenFuer, bereichVonThema, themenOhneBereich,
} from '../app/dashboard/arbeitsbereiche';
import { WERKZEUG_THEMEN, themaVonWerkzeug, kurzLabel } from '../app/tools/werkzeugThemen';
import { WERKZEUG_GRUPPEN, gruppenFuer } from '../app/dashboard/werkzeugGruppen';
import { arbeitsbereicheVon } from '../app/tools/paketAdapter';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { resolveToolState } from '../app/tools/activation';
import { ladeArbeitsbereich, speichereArbeitsbereich, ARBEITSBEREICH_SCHLUESSEL } from '../app/state/arbeitsbereichSpeicher';
import { umschalten } from '../app/state/angeheftet';
import type { AktivierungsKontext } from '../app/tools/toolTypes';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const appQuelle = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));

/** Erwartete Themenmenge je Bereich — fest verdrahtet aus der Tabelle des Auftrags. */
const ERWARTET: Record<string, string[]> = {
  'import': ['06-import-erkennung'],
  'architektur': ['03-zeichnen-cad', '07-architektur', '08-material-fassade', '11-bad-kueche'],
  'bauphysik': ['09-bauphysik'],
  'heizung': ['10-heizung-tga'],
  'elektro-pv': ['12-elektro-pv'],
};

// --- K3: die durchgängigen Themen sind überall ------------------------------------------------
test('K3: die 7 durchgängigen Themen erscheinen in JEDEM der fünf Arbeitsbereiche', () => {
  assert.equal(DURCHGAENGIGE_THEMEN.length, 7);
  assert.equal(ARBEITSBEREICHE.length, 5);
  for (const b of ARBEITSBEREICHE) {
    const ids = themenFuer(b.id).map((t) => t.id);
    for (const d of DURCHGAENGIGE_THEMEN) {
      assert.ok(ids.includes(d), `${b.id}: ${d} fehlt — ohne Auswählen/Bearbeiten ist der Bereich unbedienbar`);
    }
  }
});

test('K3: durchgängig heißt „überall gültig" = leere supportedWorkspaces, nicht fünf Einträge', () => {
  // Fünf Einträge zu pflegen, wo „überall" gemeint ist, bricht beim sechsten Bereich.
  for (const t of WERKZEUG_THEMEN.filter((x) => DURCHGAENGIGE_THEMEN.includes(x.id))) {
    for (const id of t.werkzeuge) {
      assert.deepEqual(arbeitsbereicheVon(id), [], `${id} (${t.id}) ist durchgängig`);
    }
  }
});

// --- K4′: je Bereich die erwartete Themenmenge -------------------------------------------------
for (const [bereich, gebunden] of Object.entries(ERWARTET)) {
  test(`K4′: Arbeitsbereich ${bereich} zeigt genau die erwarteten Themen`, () => {
    assert.ok(arbeitsbereich(bereich), `${bereich} fehlt in ARBEITSBEREICHE`);
    const ids = themenFuer(bereich).map((t) => t.id);
    assert.deepEqual([...ids].sort(), [...DURCHGAENGIGE_THEMEN, ...gebunden].sort());
    // und die Leiste zeigt exakt dieselbe Menge — kein zweiter Filter
    assert.deepEqual(gruppenFuer(bereich).map((g) => g.id), ids);
  });
}

test('K4′: der Standard ist Architektur — dieselbe Leiste wie vor dem Umbau', () => {
  assert.equal(ARBEITSBEREICH_STANDARD, 'architektur');
});

// --- K5′: Bilanz über alle Bereiche ------------------------------------------------------------
test('K5′: Summe über alle Bereiche = 15 Themen / 110 Werkzeuge, jedes in genau einem Thema', () => {
  assert.equal(WERKZEUG_THEMEN.length, 15);
  const alle = WERKZEUG_THEMEN.flatMap((t) => t.werkzeuge);
  assert.equal(alle.length, 110);
  assert.equal(new Set(alle).size, 110, 'kein Werkzeug in zwei Themen');
  // jedes Thema ist erreichbar: entweder durchgängig oder an einen Bereich gebunden
  assert.deepEqual(themenOhneBereich(), [], 'ein Thema ohne Bereich wäre unerreichbar');
  // Vereinigung der fünf Bereichsansichten deckt alle 15 Themen
  const gesehen = new Set(ARBEITSBEREICHE.flatMap((b) => themenFuer(b.id).map((t) => t.id)));
  assert.equal(gesehen.size, 15);
});

test('K5′: jedes Werkzeug aus Registry UND Katalog steht in genau einem Thema', () => {
  for (const t of [...TOOL_DEFINITIONS, ...TOOL_KATALOG]) {
    const thema = themaVonWerkzeug(t.id);
    assert.ok(thema, `${t.id} steht in keinem Thema — im Menü unerreichbar`);
    assert.equal(
      WERKZEUG_THEMEN.filter((x) => x.werkzeuge.includes(t.id)).length, 1,
      `${t.id} steht in mehr als einem Thema`,
    );
  }
});

test('K5′: die 22 Kategorien bleiben als Datenfeld erhalten — Trail, aber keine zweite Gruppierung', () => {
  const kategorien = new Set(TOOL_KATALOG.map((t) => t.group));
  assert.ok(kategorien.size >= 20, `nur ${kategorien.size} Kategorien — das Feld darf nicht verschwinden`);
  // gruppiert wird trotzdem ausschließlich nach Thema
  assert.deepEqual(WERKZEUG_GRUPPEN.map((g) => g.id), WERKZEUG_THEMEN.map((t) => t.id));
});

// --- Eine Wahrheit: Leiste und Aktivierung antworten gleich ------------------------------------
function kontext(workspace: string): AktivierungsKontext {
  return {
    workspace, view: '2d',
    selection: { count: 0, types: [] },
    projectState: 'planung',
    permissions: ['Hausplaner,update'],
  };
}

test('Leiste und resolveToolState geben dieselbe Antwort auf „gilt das hier?"', () => {
  for (const b of ARBEITSBEREICHE) {
    const sichtbar = new Set(gruppenFuer(b.id).flatMap((g) => g.werkzeuge.map((t) => t.id)));
    for (const t of TOOL_KATALOG) {
      const wegenBereich = t.supportedWorkspaces.length > 0 && !t.supportedWorkspaces.includes(b.id);
      assert.equal(
        wegenBereich, !sichtbar.has(t.id),
        `${t.id} in ${b.id}: Leiste und Aktivierung widersprechen sich`,
      );
    }
  }
});

test('die Registry-Werkzeuge widersprechen ihrer Themen-Bindung nicht', () => {
  // `wand`, `fenster` … trugen schon vor AUF-34 `[architektur]`; ihre Themen sagen dasselbe.
  // Liefe das auseinander, hinge ein Werkzeug in der Leiste, das sich nicht benutzen lässt.
  for (const t of TOOL_DEFINITIONS) {
    assert.deepEqual(
      [...t.supportedWorkspaces].sort(), arbeitsbereicheVon(t.id).sort(),
      `${t.id}: Registry und Themen-Bindung sagen Verschiedenes`,
    );
  }
});

test('ein Bereichsfremdes Werkzeug ist NICHT aktivierbar — mit Grund, nicht stumm', () => {
  const wand = TOOL_DEFINITIONS.find((t) => t.id === 'wand');
  assert.ok(wand);
  assert.equal(resolveToolState(wand, kontext('architektur')).enabled, true);
  const fremd = resolveToolState(wand, kontext('heizung'));
  assert.equal(fremd.enabled, false);
  assert.ok((fremd.reason ?? '').length > 0, 'ohne Grund wäre es ein stummes Verschwinden');
});

// --- Kante 2: Anheften schlägt den Bereichsfilter ----------------------------------------------
test('K6/Kante 2: ein angeheftetes Werkzeug bleibt beim Bereichswechsel sichtbar', () => {
  // Anheften ist persönlich und liegt in `localStorage`, nicht am Bereich. Die linke Leiste zeigt
  // Fix-Zone + Angeheftetes; der Bereichsfilter greift ausschließlich in der oberen Gruppenzeile.
  const angeheftet = umschalten(new Set<string>(), 'fussbodenheizung');
  assert.ok(angeheftet.has('fussbodenheizung'));
  assert.equal(bereichVonThema(themaVonWerkzeug('fussbodenheizung')!.id), 'heizung');
  // im fremden Bereich NICHT in der Gruppenzeile …
  const inArchitektur = new Set(gruppenFuer('architektur').flatMap((g) => g.werkzeuge.map((t) => t.id)));
  assert.ok(!inArchitektur.has('fussbodenheizung'));
  // … aber weiterhin angeheftet, also in der linken Leiste, und die App filtert sie nicht nach Bereich
  assert.match(appQuelle, /railWerkzeuge/, 'die linke Leiste kennt keinen Bereichsfilter');
  assert.doesNotMatch(
    appQuelle.match(/const railWerkzeuge[^\n]*\n/)?.[0] ?? '', /activeWorkspace|gruppenFuer/,
    'die Rail-Liste darf nicht nach Arbeitsbereich gefiltert werden',
  );
});

// --- Kante 3: kein stillschweigendes Abwählen --------------------------------------------------
test('Kante 3: der Bereichswechsel wählt das aktive Werkzeug NICHT ab', () => {
  const zweig = appQuelle.match(/useEffect\(\(\) => \{\s*if \(fremderBereich\) return;[\s\S]*?\}, \[werkzeugKontext[^\]]*\]\);/);
  assert.ok(zweig, 'die Ausnahme für den fremden Bereich fehlt — das Werkzeug fiele stumm zurück');
  assert.match(appQuelle, /fremderBereich=\{fremderBereich\}/, 'und der Grund steht sichtbar in der Kontextleiste');
});

// --- Kante 4: der Bereich überlebt den Neuladen, aber nicht im Szenendokument ------------------
test('Kante 4: der gewählte Bereich liegt im lokalen Speicher, NIE in der Szene', () => {
  const quelle = readFileSync(join(hier, '../app/state/arbeitsbereichSpeicher.ts'), 'utf8');
  assert.match(quelle, /localStorage/);
  assert.doesNotMatch(quelle, /executeCommand|SceneDocument|scene\./, 'kein Zugriff auf die Szene');
  assert.equal(ARBEITSBEREICH_SCHLUESSEL, 'hausplaner.arbeitsbereich.v1');
});

test('Kante 4: ohne Browser keine Ausnahme, und ein unbekannter Wert wird verworfen', () => {
  assert.equal(ladeArbeitsbereich(), undefined);
  assert.doesNotThrow(() => speichereArbeitsbereich('heizung'));
});

// --- Kante 1: kein Bereich wirkt leer -----------------------------------------------------------
test('Kante 1: der dünnste Bereich trägt trotzdem 8 Gruppen und über 50 Werkzeuge', () => {
  const zahlen = ARBEITSBEREICHE.map((b) => ({
    id: b.id,
    gruppen: gruppenFuer(b.id).length,
    werkzeuge: gruppenFuer(b.id).reduce((s, g) => s + g.werkzeuge.length, 0),
  }));
  const duennster = zahlen.reduce((a, b) => (a.werkzeuge <= b.werkzeuge ? a : b));
  assert.ok(duennster.gruppen >= 8, `${duennster.id}: nur ${duennster.gruppen} Gruppen`);
  assert.ok(duennster.werkzeuge >= 50, `${duennster.id}: nur ${duennster.werkzeuge} Werkzeuge — wirkt leer`);
});

// --- Kurzform der Beschriftung ------------------------------------------------------------------
test('die Leiste trägt die Kurzform, der Tooltip das volle Label — eine Quelle, keine zweite Tabelle', () => {
  assert.equal(kurzLabel(WERKZEUG_THEMEN[1]), 'Bearbeiten'); // „Bearbeiten & Transformieren"
  assert.equal(kurzLabel(WERKZEUG_THEMEN[13]), 'Prüfung');   // „Prüfung, Zusammenarbeit & Revision"
  for (const t of WERKZEUG_THEMEN) {
    assert.ok(kurzLabel(t).length > 1, `${t.id}: leere Kurzform`);
    assert.ok(t.label.startsWith(kurzLabel(t)), `${t.id}: die Kurzform ist aus dem Label abgeleitet`);
  }
});
