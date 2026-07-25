/**
 * AUF-30 — der erste Test durch den **echten Render-Pfad**.
 *
 * Vorgeschichte: Auflage 3 des A1-Votums verlangt „mindestens ein Test durch den echten
 * Render-Pfad". Ich hatte sie zweimal als **nicht erfüllbar** zurückgegeben und das belegt:
 * `node --experimental-strip-types` versteht kein JSX, ein Import einer `.tsx` endete in
 * `ERR_UNKNOWN_FILE_EXTENSION`, und `jsdom`/`testing-library`/`react-test-renderer` fehlen.
 *
 * Beides ist jetzt gelöst — ohne neue Abhängigkeit: `test-hooks.mjs` übersetzt `.tsx` mit dem
 * ohnehin vorhandenen `esbuild`, und gerendert wird über `react-dom/server`, das **kein DOM**
 * braucht. Damit prüft dieser Test, was vorher nur behauptet werden konnte: dass die Komponente
 * tatsächlich ausgibt, was die Daten sagen.
 *
 * Was hier NICHT geprüft wird — und das gehört dazu: Layout, Umbruch, Sichtbarkeit bei 375 px.
 * Serverseitiges Rendern liefert Markup, kein Bild. **Die Sichtprobe in 1440/1024/375 bleibt
 * Pflicht** (Befund B3: vier Reiter im Test, drei auf dem Schirm). Dieser Test ersetzt sie nicht,
 * er schließt nur die Lücke zwischen „Daten stimmen" und „Komponente gibt sie aus".
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { renderToStaticMarkup } from 'react-dom/server';
import { createElement } from 'react';
import { FachFlaeche } from '../app/FachFlaeche';
import { FACH_FLAECHEN, fachFlaecheNach } from '../app/dashboard/fachFlaechen';

const heizlast = fachFlaecheNach('Heizlastberechnung')!;

function rendern(flaeche = heizlast, herkunft: 'start' | 'navi' | 'guided' = 'navi'): string {
  return renderToStaticMarkup(createElement(FachFlaeche, { flaeche, herkunft, onZurueck: () => {} }));
}

test('die Fläche rendert wirklich — Kopf, Gruppe und Zweck stehen im Markup', () => {
  const html = rendern();
  assert.ok(html.includes(heizlast.label), 'der Modulname fehlt');
  assert.ok(html.includes(heizlast.gruppe), 'die Gruppe fehlt');
  assert.ok(html.includes(heizlast.zweck.slice(0, 40)), 'der Zwecktext fehlt');
});

test('die Feldstruktur erscheint — jede Eingangs- und Ausgangsgröße mit Beschriftung', () => {
  const html = rendern();
  for (const feld of [...heizlast.eingaenge, ...heizlast.ausgaenge]) {
    assert.ok(html.includes(feld.label), `Feld „${feld.label}" wird nicht ausgegeben`);
  }
});

test('Kante 4 am gerenderten Markup: kein „Berechnen"-Knopf, alle Felder deaktiviert', () => {
  const html = rendern();
  assert.doesNotMatch(html, /Berechnen/i, 'eine Fläche ohne Rechnung darf keinen Rechnen-Knopf zeigen');
  const eingabefelder = html.match(/<input[^>]*>/g) ?? [];
  assert.ok(eingabefelder.length > 0, 'die Feldstruktur soll Eingabefelder zeigen');
  for (const f of eingabefelder) assert.match(f, /disabled/, `Feld nicht deaktiviert: ${f.slice(0, 60)}`);
});

test('der Zustand steht als Text im Markup, nicht nur als Farbe', () => {
  const html = rendern();
  assert.match(html, /in Entwicklung/i, 'ZustandBadge muss seinen Text mitgeben (A11y: nie nur Farbe)');
});

test('Kante 2 am Markup: der Zurück-Weg nennt die Herkunft', () => {
  assert.match(rendern(heizlast, 'start'), /Übersicht/);
  assert.match(rendern(heizlast, 'navi'), /Navigation/);
  assert.match(rendern(heizlast, 'guided'), /geführten Planung/);
});

test('alle 19 Flächen rendern ohne Wurf — keine hat ein Loch in den Daten', () => {
  for (const f of FACH_FLAECHEN) {
    const html = rendern(f);
    assert.ok(html.length > 200, `${f.label}: Markup verdächtig kurz`);
    assert.ok(html.includes(f.label), `${f.label}: eigener Name fehlt im Markup`);
  }
});
