/**
 * AUF-27 — die linke Schiene trägt Reiter statt drei gestapelter Blöcke.
 *
 * Geprüft wird, was der Auftrag als Abnahme benennt: **genau drei** Reiter in fester Reihenfolge
 * mit Standard `werkzeuge` (K3), **immer genau ein** sichtbarer Abschnitt (K4), **unverändert 22**
 * erreichbare Fachplaner-Einträge (K5), das **wiederverwendete** Reiter-Muster aus v2.2/AUF-19 (K6)
 * und **null** sichtbare Beschriftung mit dem Wort „Fähigkeit" (K11).
 *
 * **Warum teils über den Quelltext:** dieselbe Lage wie in `panelReiterVerknuepfung.test.ts` — die
 * Testumgebung hat kein DOM, und `HausplanerApp.tsx` zieht react-konva und three nach. Ein
 * gerendertes „genau ein Abschnitt" ist so nicht messbar. Was hier geprüft wird, ist die
 * **Verdrahtung, die das Verhalten erzeugt** — ausdrücklich benannt, nicht als Verhaltensbeleg
 * behauptet. Kommentare werden vorher entfernt, sonst misst der Test Prosa statt Code.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { SCHIENEN_REITER, SCHIENE_STANDARD, schienenReiter, type SchienenReiterId } from '../app/dashboard/schienenReiter';
import { alleFaehigkeiten } from '../app/tools/faehigkeiten';

const hier = dirname(fileURLToPath(import.meta.url));
/** Siehe `leisteAusZonen.test.ts`: erklärende Kommentare dürfen den Befund nicht auslösen. */
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const appQuelle = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));
const naviQuelle = ohneKommentare(readFileSync(join(hier, '../app/FaehigkeitenNavi.tsx'), 'utf8'));

// --- K3: genau drei Reiter, feste Reihenfolge, Standard `werkzeuge` ----------------------------
test('K3: genau drei Reiter in fester Reihenfolge werkzeuge · projekt · fachplaner', () => {
  assert.equal(SCHIENEN_REITER.length, 3);
  assert.deepEqual(
    SCHIENEN_REITER.map((r) => r.id),
    ['werkzeuge', 'projekt', 'fachplaner'] satisfies SchienenReiterId[],
  );
});

test('K3: Standard ist `werkzeuge` — der häufigste Job', () => {
  assert.equal(SCHIENE_STANDARD, 'werkzeuge');
  assert.equal(SCHIENEN_REITER[0].id, SCHIENE_STANDARD, 'der Standard steht auch vorne');
  assert.match(appQuelle, /useState<SchienenReiterId>\(SCHIENE_STANDARD\)/, 'die App startet auf dem Standard');
});

test('K3: jede id ist eindeutig, jeder Reiter hat Beschriftung und Hinweis', () => {
  const ids = SCHIENEN_REITER.map((r) => r.id);
  assert.equal(new Set(ids).size, ids.length, 'doppelte Reiter-id');
  for (const r of SCHIENEN_REITER) {
    assert.ok(r.label.length > 1, `${r.id}: Beschriftung fehlt`);
    assert.ok(r.hinweis.length > 10, `${r.id}: Hinweis fehlt oder ist zu dünn — ein Reiter ohne Ansage`);
  }
  assert.equal(schienenReiter('gibt-es-nicht'), undefined, 'unbekannte id liefert undefined statt zu werfen');
});

// --- K4: immer genau ein Abschnitt sichtbar ----------------------------------------------------
test('K4: jeder Reiter hat genau EINE Sichtbarkeitsbedingung — nie zwei, nie keine', () => {
  // Die Bedingungen prüfen alle DIESELBE Variable auf Gleichheit; damit können nie zwei zugleich
  // wahr sein. Dass keine fehlt, zeigt der Mengenvergleich gegen die Reiter-Daten.
  const treffer = [...appQuelle.matchAll(/schienenTab === '([a-z]+)'/g)].map((m) => m[1]);
  assert.deepEqual(
    [...treffer].sort(),
    SCHIENEN_REITER.map((r) => r.id).sort(),
    'jeder Reiter genau einmal bedingt gerendert — ein fehlender Reiter wäre eine leere Fläche',
  );
});

test('K4: die Scroll-Höhe gehört dem Abschnitt, nicht der Spalte', () => {
  // Der Mangel war: EINE Scroll-Höhe für drei Blöcke. Deshalb scrollt jetzt der Inhaltsbereich.
  const panel = appQuelle.match(/<div\s+role="tabpanel" id=\{SCHIENE_ID\}[\s\S]*?>/);
  assert.ok(panel, 'Inhaltsbereich der Schiene nicht gefunden');
  assert.match(panel[0], /overflowY: 'auto'/, 'der Abschnitt braucht seine eigene Scroll-Höhe');
  assert.match(panel[0], /minHeight: 0/, 'ohne minHeight:0 wächst ein Flex-Kind statt zu scrollen');
});

// --- K5: nichts ist beim Umbau verschwunden ----------------------------------------------------
test('K5: die Anzahl erreichbarer Fachplaner-Einträge ist unverändert 22', () => {
  assert.equal(alleFaehigkeiten().length, 22);
  // und sie hängen weiterhin an genau einer Stelle im Baum — dem Reiter `fachplaner`.
  assert.equal((appQuelle.match(/<FaehigkeitenNavi/g) ?? []).length, 1);
  assert.match(appQuelle, /schienenTab === 'fachplaner' && \(\s*<FaehigkeitenNavi/);
});

// --- K6: das Reiter-Muster ist wiederverwendet, nicht ein zweites erfunden ---------------------
test('K6: es gibt im ganzen Planer genau EINE Stelle, die Reiter erzeugt', () => {
  const leisteQuelle = ohneKommentare(readFileSync(join(hier, '../app/dashboard/ReiterLeiste.tsx'), 'utf8'));
  assert.equal((leisteQuelle.match(/role="tab"/g) ?? []).length, 1);
  assert.equal((appQuelle.match(/role="tab"/g) ?? []).length, 0, 'kein zweiter Tab-Mechanismus in der App');
  assert.equal((appQuelle.match(/role="tablist"/g) ?? []).length, 0);
  // Alle Benutzer kommen aus derselben Komponente: Eigenschaften-Panel und Schiene (AUF-27),
  // seit AUF-34 zusätzlich der Arbeitsbereich-Wähler.
  assert.equal((appQuelle.match(/<ReiterLeiste/g) ?? []).length, 3);
});

test('K6: die Leiste trägt tablist, aria-controls und Pfeiltasten mit Fokusnachführung', () => {
  const leisteQuelle = ohneKommentare(readFileSync(join(hier, '../app/dashboard/ReiterLeiste.tsx'), 'utf8'));
  assert.match(leisteQuelle, /role="tablist" aria-label=\{ariaLabel\}/);
  assert.match(leisteQuelle, /aria-controls=\{panelId\}/);
  assert.match(leisteQuelle, /aria-selected=\{aktivT\}/);
  assert.match(leisteQuelle, /tabIndex=\{aktivT \? 0 : -1\}/);
  const tastenZweig = leisteQuelle.match(/if \(e\.key !== 'ArrowLeft' && e\.key !== 'ArrowRight'\) return;[\s\S]*?\n\s*\}\}/);
  assert.ok(tastenZweig, 'Pfeiltasten-Zweig nicht gefunden');
  assert.match(tastenZweig[0], /reiterRefs\.current\[ziel\]\?\.focus\(\)/);
});

test('K6: aria-controls der Schiene zeigt auf eine id, die es im Baum gibt', () => {
  // Ein Verweis ins Leere wäre schlimmer als kein Verweis (Kante 5 aus AUF-19).
  assert.match(appQuelle, /^const SCHIENE_ID = 'hp-schiene-panel';$/m);
  assert.match(appQuelle, /id=\{SCHIENE_ID\}/);
  assert.match(appQuelle, /aria-labelledby=\{schienenReiterId\(schienenTab\)\}/);
  // Eigenes Präfix: Schienen- und Panel-Reiter teilen im selben Dokument keine id.
  assert.match(appQuelle, /`hp-schiene-tab-\$\{id\}`/);
  assert.match(appQuelle, /`hp-eigenschaften-tab-\$\{id\}`/);
});

// --- K6 / Befund B1: Reiter sind fokussierbar und dürfen nicht neu montiert werden -------------
test('B1: die Reiterleiste ist eine Komponente auf Modulebene, nicht im Rumpf der App', () => {
  const leisteQuelle = readFileSync(join(hier, '../app/dashboard/ReiterLeiste.tsx'), 'utf8');
  assert.match(leisteQuelle, /^export function ReiterLeiste\(/m);
  assert.doesNotMatch(appQuelle, /function ReiterLeiste/, 'keine zweite Definition im App-Rumpf');
});

// --- Kante 2: der gewählte Reiter berührt das Szenendokument nicht -----------------------------
test('Kante 2: der Reiter-Zustand ist lokal — kein Feld, kein Zod, kein Schema', () => {
  const zustand = appQuelle.match(/const \[schienenTab, setSchienenTab\] = [^\n]*/);
  assert.ok(zustand, 'Zustand nicht gefunden');
  assert.match(zustand[0], /useState/);
  assert.doesNotMatch(zustand[0], /executeCommand|scene|store/i, 'der Reiter gehört nicht ins Gebäude');
});

// --- K11 (Nachtrag): „Fähigkeit" steht in keiner sichtbaren Beschriftung mehr -----------------
test('K11: keine gerenderte Beschriftung enthält das Wort „Fähigkeit"', () => {
  for (const [name, quelle] of [['HausplanerApp.tsx', appQuelle], ['FaehigkeitenNavi.tsx', naviQuelle]] as const) {
    assert.doesNotMatch(quelle, /Fähigkeit/, `${name}: sichtbarer Text nennt noch „Fähigkeit"`);
  }
  // Der Reiter heisst „Fachplaner" — derselbe Begriff wie in Ebene 2 der Inventur.
  assert.equal(schienenReiter('fachplaner')?.label, 'Fachplaner');
  // Interne Bezeichner und Dateinamen dürfen bleiben — eigener Posten, kein Beifang.
  assert.match(appQuelle, /<FaehigkeitenNavi/, 'der Modulname ist NICHT Gegenstand dieses Auftrags');
});
