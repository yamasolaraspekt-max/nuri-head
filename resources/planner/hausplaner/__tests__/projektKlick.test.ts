/**
 * AUF-66 — **ein Klick zurück in die Arbeit.**
 *
 * Vorgeschichte in zwei Sätzen: AUF-40 Teil A hat die erfundenen Projekte entfernt, AUF-78 die
 * echten geliefert — und dabei ausdrücklich **nicht** verdrahtet, weil *wohin ein Klick führen
 * soll* eine Entscheidung war und keine Bauaufgabe. Sie ist getroffen. Hier wird geprüft, dass sie
 * auch wirklich gilt.
 *
 * **Gemessen wird am echten Render-Pfad** (`react-dom/server`, Muster AUF-30), nicht am Quelltext.
 * Ein Test, der nur nach Zeichenketten in der Datei sucht, hätte die geteilte Adresse — den
 * häufigsten Fehler solcher Listen — nicht gefunden: die steht im Quelltext genauso richtig da wie
 * die getrennte.
 *
 * **Was hier NICHT geprüft wird:** wie es aussieht. Serverseitiges Rendern liefert Markup, kein
 * Bild. Ob der dominante Eintrag bei 1024×768 ohne Scrollen sichtbar ist, entscheidet die
 * Sichtprobe — sie ist Teil der Abnahme, nicht ein Anhang.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { renderToStaticMarkup } from 'react-dom/server';
import { createElement } from 'react';
import { StartView } from '../app/StartView';
import { leseProjekte, type ProjektEintrag } from '../app/state/projekte';

const hier = dirname(fileURLToPath(import.meta.url));

/** Die Adressen sind hier frei erfunden — **absichtlich ohne den echten Pfad** (siehe K4). */
const eintrag = (id: number, name: string, adresse?: string): ProjektEintrag =>
  ({ id, name, ort: 'Musterstadt', datum: '26.07.2026', ...(adresse === undefined ? {} : { adresse }) });

function rendern(projekte: ProjektEintrag[]): string {
  return renderToStaticMarkup(createElement(StartView, {
    onGuided: () => {}, onKonfigurator: () => {}, projekte,
  }));
}

/** Alles, was die Tastfolge betritt: Verweise mit Ziel und alles mit `tabindex="0"`. */
const fokussierbare = (html: string): RegExpMatchArray[] =>
  [...html.matchAll(/<a href="|tabindex="0"/g)];

// --- K2: ein Klick genügt, und er führt zum EIGENEN Projekt --------------------------------------
test('K2: jeder Eintrag trägt die Adresse seines eigenen Objekts', () => {
  const html = rendern([eintrag(203, 'Haus Nord', '/z/203'), eintrag(7, 'Haus Süd', '/z/7')]);

  assert.match(html, /<a href="\/z\/203"/, 'der erste Eintrag zeigt auf sein Objekt');
  assert.match(html, /<a href="\/z\/7"/, 'der zweite auf seins');

  // **Die geteilte Adresse ist der Fehler, den dieser Test verhindern soll.**
  const teil = html.slice(html.indexOf('Haus Süd') - 900, html.indexOf('Haus Süd'));
  assert.doesNotMatch(teil, /203/, 'der zweite Eintrag darf das Ziel des ersten nicht tragen');
});

test('K2: ein Klick, nicht zwei — der Eintrag selbst ist der Verweis', () => {
  const html = rendern([eintrag(203, 'Haus Nord', '/z/203')]);
  // Der Name steht INNERHALB des Verweises; es braucht keinen zweiten Knopf daneben.
  assert.match(html, /<a href="\/z\/203"[^>]*>[\s\S]*?Haus Nord[\s\S]*?<\/a>/);
  assert.equal(fokussierbare(html.slice(0, html.indexOf('Haus Nord'))).length, 1,
    'genau ein Ziel bis zum Projektnamen — kein zweiter Weg zur selben Handlung');
});

// --- Die dominante Handlung ----------------------------------------------------------------------
test('der erste Eintrag ist die dominante Handlung und heißt „Weiterarbeiten"', () => {
  const html = rendern([eintrag(203, 'Haus Nord', '/z/203'), eintrag(7, 'Haus Süd', '/z/7')]);
  assert.match(html, /Weiterarbeiten/, 'die Handlung ist benannt');
  // **Genau einmal** — sie ist die dominante, nicht eine von vielen.
  assert.equal([...html.matchAll(/Weiterarbeiten/g)].length, 1);
  assert.ok(html.indexOf('Weiterarbeiten') < html.indexOf('Haus Süd'),
    'sie steht am ersten Eintrag, nicht am zweiten');
});

test('K3: der dominante Eintrag ist ERSTER in der Tastfolge', () => {
  const html = rendern([eintrag(203, 'Haus Nord', '/z/203'), eintrag(7, 'Haus Süd', '/z/7')]);
  const erste = fokussierbare(html)[0];
  assert.ok(erste, 'es gibt überhaupt etwas Fokussierbares');
  assert.ok((erste.index ?? 0) < html.indexOf('Haus Süd'), 'nichts liegt davor');
  assert.ok(html.slice(erste.index).startsWith('<a href="/z/203"'),
    'das erste fokussierbare Element ist der Weg zurück in die Arbeit');
});

test('K3: jeder Eintrag mit Ziel ist fokussierbar — drei Projekte, drei neue Wege', () => {
  const leer = fokussierbare(rendern([])).length;
  const drei = fokussierbare(rendern([
    eintrag(1, 'A', '/z/1'), eintrag(2, 'B', '/z/2'), eintrag(3, 'C', '/z/3'),
  ])).length;
  assert.equal(drei - leer, 3, `fokussierbar: ${leer} ohne Liste, ${drei} mit drei Projekten`);
});

test('K3: die Leertaste löst aus — Enter kann der Verweis selbst', () => {
  // **Ohne Kommentare gemessen.** Der erklärende Satz nebenan nennt `istAusloeser` beim Namen —
  // gegen den rohen Text geprüft, hätte der Test meinen eigenen Kommentar für Code gehalten.
  const quelle = readFileSync(join(hier, '../app/StartView.tsx'), 'utf8')
    .replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
  const stelle = quelle.slice(quelle.indexOf('function ProjektKachel'), quelle.indexOf('function HubKarte'));
  assert.match(stelle, /e\.key === ' ' \|\| e\.key === 'Spacebar'/, 'die Leertaste wird behandelt');
  assert.match(stelle, /e\.preventDefault\(\)/, 'sonst rollt die Seite, statt zu öffnen');
  assert.match(stelle, /e\.currentTarget\.click\(\)/);
  // Und Enter wird NICHT zusätzlich abgefangen — er käme sonst zweimal an.
  assert.doesNotMatch(stelle, /istAusloeser/);
});

test('K3: der Fokusring ist nicht neu — das Studio hat ihn app-weit', () => {
  // Ein zweiter Ring wäre eine zweite Wahrheit über dieselbe Sache. Wiederverwenden statt bauen.
  const studio = readFileSync(join(hier, '../app/HausplanerStudio.tsx'), 'utf8');
  assert.match(studio, /\.hp-studio :focus-visible\{outline:2px solid/);
  assert.match(studio, /className="hp-studio"/, 'und die Startfläche liegt darin');
});

// --- K4: die Insel baut keine Adresse ------------------------------------------------------------
/**
 * Die gesuchten Zeichenketten werden **zusammengesetzt**, damit diese Datei nicht selbst zum
 * Treffer wird. Genau diese Falle hat mich in früheren Posten mehrfach erwischt.
 */
const NADELN = ['/adm' + 'in/hausplaner', '/ob' + 'jekt/'];

function durchsuchen(wurzel: string, auchTests: boolean): string[] {
  const treffer: string[] = [];
  const gehe = (verzeichnis: string): void => {
    for (const name of readdirSync(verzeichnis)) {
      if (name === 'node_modules') continue;
      if (!auchTests && name === '__tests__') continue;
      const pfad = join(verzeichnis, name);
      if (statSync(pfad).isDirectory()) { gehe(pfad); continue; }
      if (!/\.tsx?$/.test(pfad)) continue;
      const inhalt = readFileSync(pfad, 'utf8');
      for (const nadel of NADELN) {
        if (inhalt.includes(nadel)) treffer.push(`${pfad.slice(wurzel.length + 1)} → ${nadel}`);
      }
    }
  };
  gehe(wurzel);
  return treffer;
}

test('K4: im ausgelieferten Inselcode wird kein Pfad zusammengesetzt — null Treffer', () => {
  const treffer = durchsuchen(join(hier, '..'), false);
  assert.deepEqual(treffer, [], `die ausgelieferte Insel kennt das Routing nicht; gesehen: ${treffer.join(', ')}`);
});

test('K4: der eine Treffer im Baum ist ein DATEIPFAD, keine Adresse — er wird benannt, nicht versteckt', () => {
  // **Buchstäblich null im ganzen Verzeichnis ist nicht erreichbar** — und zwar aus einem Grund,
  // der mit dem Kriterium nichts zu tun hat: `rechte.test.ts` liest die Blade-Vorlage über ihren
  // Pfad auf der Platte (`resources/views/…`). Das ist kein Ziel, das jemand anklickt.
  // Statt die Messung stillschweigend zu verkleinern, steht der Treffer hier namentlich.
  const alle = durchsuchen(join(hier, '..'), true);
  assert.deepEqual(alle.map((t) => t.split(' → ')[0]), ['__tests__/rechte.test.ts'],
    'genau dieser eine, sonst keiner');
  const roh = readFileSync(join(hier, 'rechte.test.ts'), 'utf8');
  for (const zeile of roh.split('\n').filter((z) => z.includes(NADELN[0]!))) {
    assert.match(zeile, /readFileSync|resources\/views/, 'nur als Datei gelesen, nie als Ziel gesetzt');
  }
});

test('K4: die Adresse wird gelesen, nicht gerechnet', () => {
  const regel = readFileSync(join(hier, '../app/state/projekte.ts'), 'utf8');
  assert.doesNotMatch(regel, /`[^`]*\$\{[^}]*id[^}]*\}[^`]*`/, 'kein aus der Kennung gebauter Pfad');
  // Sie kommt herein wie alles andere: geprüft, oder gar nicht.
  assert.deepEqual(leseProjekte(JSON.stringify([{ id: 1, name: 'A', ort: '', datum: '', adresse: '/z/1' }])),
    [{ id: 1, name: 'A', ort: '', datum: '', adresse: '/z/1' }]);
  assert.deepEqual(leseProjekte(JSON.stringify([{ id: 1, name: 'A', ort: '', datum: '', adresse: 7 }])), [],
    'eine Zahl im Adressfeld landete sonst ungeprüft in einem Ziel');
});

// --- K5: der Leerzustand bleibt, Zeichen für Zeichen ---------------------------------------------
test('K5: ohne Projekte steht der Satz aus AUF-40 Teil A unverändert da', () => {
  const html = rendern([]);
  assert.match(html, /Noch kein Projekt geöffnet\./);
  assert.match(html, /Ein Vorhaben beginnt unten mit <b>Hausplaner<\/b>/);
  assert.doesNotMatch(html, /Weiterarbeiten/, 'es gibt nichts fortzusetzen — also verspricht es auch niemand');
});

test('K5: ohne Projekte gibt es keinen einzigen Verweis', () => {
  assert.doesNotMatch(rendern([]), /<a href="/, 'kein Ziel, wo kein Projekt ist');
});

// --- K6: kein Ziel, kein Versprechen -------------------------------------------------------------
test('K6 (Mutation): ohne Adresse ist der Eintrag sichtbar, aber KEINE Schaltfläche', () => {
  // **Der Gegenbeweis dieses Postens.** Wird die Adresse aus dem Bündel entfernt, darf der Klick
  // nicht bloß ins Leere gehen — er darf gar nicht erst angeboten werden.
  const html = rendern([eintrag(203, 'Haus Nord')]);
  assert.match(html, /Haus Nord/, 'das Projekt gibt es, also wird es gezeigt');
  assert.doesNotMatch(html, /<a href="/, 'aber es führt nirgendwohin — und behauptet es auch nicht');
  assert.match(html, /cursor:default/, 'nicht einmal der Zeiger verspricht etwas');
});

test('K6: ein Eintrag mit Ziel und einer ohne stehen nebeneinander, ohne sich zu verwechseln', () => {
  const html = rendern([eintrag(203, 'Haus Nord', '/z/203'), eintrag(7, 'Haus Süd')]);
  assert.equal([...html.matchAll(/<a href="/g)].length, 1, 'genau einer führt irgendwohin');
  assert.match(html, /Haus Süd/, 'der andere verschwindet nicht');
});

// --- Die doppelte Karte ist fort -----------------------------------------------------------------
test('die untere Karte „Weiterarbeiten" ist fort — zwei Wege zur selben Handlung sind eine Frage', () => {
  const html = rendern([eintrag(203, 'Haus Nord', '/z/203')]);
  assert.equal([...html.matchAll(/Bestandsprojekt öffnen und fortsetzen/g)].length, 0);
  assert.doesNotMatch(html, /noch nicht verdrahtet/, 'der Grund ist mit der Karte gegangen');
  // Der Bereich „Projekt" trägt jetzt zwei Karten, nicht drei.
  const projektteil = html.slice(html.indexOf('Das komplette Vorhaben'), html.indexOf('Fachplaner —'));
  assert.equal([...projektteil.matchAll(/Sanierungsplan|>Hausplaner</g)].length, 2);
});
