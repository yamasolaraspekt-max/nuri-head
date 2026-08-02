/**
 * **ANKER-INVENTUR — wer trägt den Browser-Anker ausgeschrieben, wer als Verweis, wer gar nicht?**
 *
 * ---
 *
 * **Warum STRUKTUR und nicht TEXT — die Lehre aus zwei Korrekturen an derselben Zusage.**
 *
 * Die erste Fassung von W-08 K-05 zählte mit `grep -rl "2  MONTIEREN"`. Das Blatt traf sich
 * dabei selbst, weil es sein eigenes Suchmuster zitierte. Die Korrektur nahm eine
 * Blattnamen-Ausnahme — und die hielt keine zwei Stunden:
 *
 * ```text
 * Ausgangswert im Blatt   6
 * gemessen 03.08.         8
 *   neu: FEHLERKLASSEN.md   <- ZITAT in der Beschreibung von F-19
 *        z06n1-…            <- echter Traeger
 * ```
 *
 * **Die Fehlerklasse, die den Selbsttreffer beschreibt, trifft sich selbst.** *Jede Namensliste
 * wächst mit dem nächsten Zitat; wer eine Ausnahme braucht, hat die falsche Naht gewählt.*
 *
 * **Die Naht liegt hier:** ein Zitat steht in Fließtext oder in einem ```text-Block. Ein Anker
 * ist ein KRITERIUM in einem ```yaml-Block. Das ist maschinell trennbar und kann per Definition
 * kein Zitat treffen — auch das nächste nicht.
 *
 * Gelesen wird mit demselben Parser wie in `auftrag-pruefen.mjs`; ein zweiter Leser für dieselbe
 * Frage wäre eine zweite Wahrheit, die driftet.
 *
 *   node scripts/anker-inventur.mjs            Zusammenfassung
 *   node scripts/anker-inventur.mjs --lang     je Blatt eine Zeile
 */
import { readdirSync, readFileSync, statSync } from 'node:fs';
import yaml from 'js-yaml';

/** Die Staende, deren Anker wirklich gefahren wird — S-11 greift genau hier. */
export const GEFAHRENE_STAENDE = ['aktiv', 'bereit', 'gebaut', 'entwurf', 'gesperrt'];

/** Alle ```yaml-Bloecke eines Blattes, geladen. Unlesbare werden uebersprungen, nicht geraten. */
function bloecke(text) {
  return [...text.matchAll(/^```yaml\n([\s\S]*?)^```/gm)]
    .map((m) => { try { return yaml.load(m[1]); } catch { return null; } })
    .filter((b) => b && typeof b === 'object');
}

/** Alle Kriterien eines Blattes — sie stehen je nach Blatt im Kopf oder in einem eigenen Block. */
function kriterien(geladen) {
  const raus = [];
  for (const b of geladen) {
    for (const schluessel of ['abnahmekriterien', 'kriterien']) {
      if (Array.isArray(b[schluessel])) raus.push(...b[schluessel]);
    }
  }

  return raus;
}

/**
 * **Der Befund je Blatt.**
 *
 * `ausgeschrieben` heisst: der Anker traegt seine Stufen selbst — das ist die Kopie, die driftet.
 * `verweis` heisst: er zeigt auf die eine Quelle. `browserOhneAnker` ist der Fall, den K-06
 * meint: ein Blatt faehrt Browser-Zahlen und hat gar keinen Anker.
 */
export function befund(text) {
  const geladen = bloecke(text);
  const krit = kriterien(geladen);
  const kopf = geladen.find((b) => b.auftrag)?.auftrag ?? {};
  const anker = krit.filter((k) => String(k?.id ?? '').toLowerCase().includes('anker'));
  const browser = krit.some((k) => k?.pruefung?.typ === 'browser' || k?.typ === 'browser');

  return {
    status: String(kopf.status ?? '').trim(),
    gefahren: GEFAHRENE_STAENDE.includes(String(kopf.status ?? '').trim()),
    browser,
    anker: anker.length,
    verweis: anker.filter((k) => k?.typ === 'verweis').length,
    ausgeschrieben: anker.filter((k) => k?.typ !== 'verweis').length,
    quellen: anker.filter((k) => k?.typ === 'verweis').map((k) => k?.quelle).filter(Boolean),
  };
}

/** Alle Blaetter unter `docs/auftraege/`, rekursiv. */
export function blaetter(wurzel = 'docs/auftraege') {
  const raus = [];
  const sammle = (verz) => {
    for (const e of readdirSync(verz)) {
      const p = `${verz}/${e}`;
      if (statSync(p).isDirectory()) sammle(p);
      else if (e.endsWith('.md')) raus.push(p);
    }
  };
  sammle(wurzel);

  return raus;
}

export function inventur(wurzel = 'docs/auftraege') {
  const zeilen = blaetter(wurzel).map((p) => ({ pfad: p, ...befund(readFileSync(p, 'utf8')) }));

  return {
    zeilen,
    mitAnkerOderBrowser: zeilen.filter((z) => z.anker > 0 || z.browser).length,
    ausgeschrieben: zeilen.filter((z) => z.ausgeschrieben > 0).length,
    verweis: zeilen.filter((z) => z.verweis > 0).length,
    browserOhneAnker: zeilen.filter((z) => z.browser && z.anker === 0).length,
  };
}

if (process.argv[1] && process.argv[1].endsWith('anker-inventur.mjs')) {
  const i = inventur();
  if (process.argv.includes('--lang')) {
    for (const z of i.zeilen.filter((x) => x.anker > 0 || x.browser)) {
      console.log(`${z.ausgeschrieben ? 'AUSGESCHRIEBEN' : z.verweis ? 'verweis       ' : 'OHNE ANKER    '}`
        + `  ${String(z.status).padEnd(9)} ${z.pfad.replace('docs/auftraege/', '')}`);
    }
    console.log('');
  }
  console.log(`Blaetter mit Anker oder Browser-Kriterium   ${i.mitAnkerOderBrowser}`);
  console.log(`Anker AUSGESCHRIEBEN                        ${i.ausgeschrieben}`);
  console.log(`Anker als VERWEIS                           ${i.verweis}`);
  console.log(`Browser OHNE Anker                          ${i.browserOhneAnker}`);
}
