/**
 * AUF-40 Teil A — der Startbildschirm sagt, was es gibt.
 *
 * **Zwei gemessene Befunde:**
 *
 * **(a) Er zeigte erfundene Projekte.** „EFH Mustermann", „Fenster-Angebot Hahn", „Sanierung
 * Musterstr. 5" — bei **jedem** Nutzer, auch beim allerersten Start, auch ohne ein einziges eigenes
 * Projekt. Ein Startbildschirm, der fremde Projekte zeigt, ist keine Vorschau; er ist eine
 * Falschauskunft über den eigenen Bestand.
 *
 * **(b) Die drei Projektkarten waren dieselbe Karte.** Alle drei riefen `onGuided(1)` — drei
 * Versprechen, ein Ziel. „Weiterarbeiten" öffnete kein Bestandsprojekt, sondern begann bei
 * Schritt 1.
 *
 * **Was dieser Test NICHT prüft:** ob die echte Projektliste ankommt. Sie braucht eine Route und
 * ist **Teil B** — der liegt bei Yama. Geprüft wird, dass die Fläche ohne Liste **ehrlich** ist:
 * der Leerzustand ist heute der Normalfall, nicht die Ausnahme.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { ZULETZT_STILLGELEGT } from '../app/studioDaten';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const lies = (p: string): string => ohneKommentare(readFileSync(join(hier, '../app/', p), 'utf8'));
const start = lies('StartView.tsx');
const daten = lies('studioDaten.ts');

// --- K3: keine Demo-Daten mehr im Auslieferungspfad ---------------------------------------------
test('K3: der erfundene Kundenname steht NUR noch in der stillgelegten Datei', () => {
  assert.doesNotMatch(start, /Mustermann/, 'die Startfläche darf ihn nicht kennen');
  assert.match(daten, /Mustermann/, 'als Beleg dessen, was vorher behauptet wurde, bleibt er stehen');
  assert.match(daten, /ZULETZT_STILLGELEGT/, 'der Name sagt den Zustand');
  // Und niemand rendert die Konstante mehr.
  assert.doesNotMatch(start, /ZULETZT/);
});

test('die Demo-Daten sind stillgelegt, nicht gelöscht — Muster wie bei den Werkzeugen', () => {
  assert.equal(ZULETZT_STILLGELEGT.length, 3, 'drei erfundene Einträge, als Vergleichsgrundlage erhalten');
  assert.deepEqual(ZULETZT_STILLGELEGT.map((z) => z.name),
    ['EFH Mustermann', 'Fenster-Angebot Hahn', 'Sanierung Musterstr. 5']);
});

// --- K4: der Leerzustand ist ehrlich ------------------------------------------------------------
test('K4: ohne Projekte kein Listeneintrag — und ein Satz, der nichts verspricht', () => {
  assert.match(start, /projekte\.length === 0 \?/, 'der leere Fall wird unterschieden');
  assert.match(start, /Noch kein Projekt geöffnet\./);
  // Der Satz darf nicht ins Leere zeigen: er nennt den Weg, eins anzulegen.
  assert.match(start, /Ein Vorhaben beginnt unten mit <b>Hausplaner<\/b>/);
  // Und er endet nicht auf eine Vertröstung.
  assert.doesNotMatch(start, /Noch kein Projekt geöffnet\.[^<]*(folgt|in Kürze|demnächst)/i);
});

test('K4: der Grundzustand IST leer — beim ersten Start ist das der Normalfall', () => {
  assert.match(start, /projekte = \[\]/, 'ohne Zulieferung zeigt die Fläche nichts, statt zu erfinden');
  assert.match(start, /projekte\?: readonly ZuletztEintrag\[\]/);
});

// --- K5: drei Karten, drei Ziele ----------------------------------------------------------------
test('K5: keine zwei Karten rufen dasselbe Ziel auf', () => {
  const karten = [...start.matchAll(/<Karte [^>]*titel="([^"]+)"[^>]*?(?:onClick=\{\(\) => ([^}]+)\})?[^>]*\/>/gs)];
  assert.equal(karten.length, 3, 'die drei Projektkarten');
  const ziele = karten.map((m) => m[2]).filter((z): z is string => Boolean(z));
  assert.equal(new Set(ziele).size, ziele.length, `zwei Karten mit demselben Ziel: ${ziele.join(' / ')}`);
  // Genau eine Karte hat heute ein echtes Ziel; die anderen beiden sagen es.
  assert.deepEqual(ziele, ['onGuided(1)']);
});

test('K5: eine Karte ohne Ziel ist als `in Entwicklung` ausgewiesen — mit Grund', () => {
  assert.match(start, /<ZustandBadge zustand="in_entwicklung" \/>/);
  for (const grund of [
    /grund="Der Sanierungsablauf ist ein eigener Weg/,
    /grund="Braucht die Liste der eigenen Projekte/,
  ]) {
    assert.match(start, grund, 'ohne Grund wäre die Marke nur ein Etikett');
  }
});

test('eine Karte ohne Ziel ist KEINE Schaltfläche mehr', () => {
  // Sonst wäre sie fokussierbar und anklickbar und täte nichts — genau der Fall, den AUF-44
  // aus der Icon-Zeile entfernt hat.
  const ohneZiel = start.match(/if \(!onClick\) \{[\s\S]*?\n  \}/);
  assert.ok(ohneZiel, 'der ziellose Zweig fehlt');
  assert.doesNotMatch(ohneZiel[0], /role="button"/);
  assert.doesNotMatch(ohneZiel[0], /tabIndex/);
  assert.doesNotMatch(ohneZiel[0], /onKeyDown/);
  assert.match(ohneZiel[0], /cursor: 'default'/);
});

test('die Karte MIT Ziel ist unverändert bedienbar', () => {
  const mitZiel = start.slice(start.indexOf('return (\n    <div\n      role="button"'));
  assert.match(mitZiel, /role="button" tabIndex=\{0\} onClick=\{onClick\}/);
  assert.match(mitZiel, /if \(istAusloeser\(e\)\) onClick\(\)/, 'Enter und Leertaste, wie AUF-49 es verlangt');
});

// --- Was NICHT angefasst wurde ------------------------------------------------------------------
test('Teil A hat weder Route noch Controller berührt — das ist Teil B', () => {
  // Der Auftrag verbietet es ausdrücklich: alles, was `routes/` oder `app/Http/` berührt, liegt
  // hinter Yamas Freigabe. Die Zulieferung der Liste bleibt deshalb offen.
  assert.doesNotMatch(start, /fetch\(|axios|\/admin\/hausplaner/, 'die Insel holt sich die Liste nicht selbst');
  assert.doesNotMatch(start, /dataset\./, 'auch nicht über eine Naht, die es noch nicht gibt');
});
