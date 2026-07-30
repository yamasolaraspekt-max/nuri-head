/**
 * AUF-48 / K-AB — **die Abhängigkeitslisten aller React-Haken, verglichen zwischen zwei Ständen.**
 *
 * ---
 *
 * **Warum es dieses Skript gibt.** Der AST-Vergleich aus dem S2-Blatt lag nur als *Befehl* in
 * einem Auftragstext und prüfte **`useMemo`**. Der Evaluator hat am S2-Prüfstand gemessen, was das
 * bedeutet:
 *
 * ```text
 * useMemo       15   <- geprueft
 * useCallback    7   <- UNGEPRUEFT
 * useEffect      7   <- UNGEPRUEFT
 * ----------------------
 * UNGEPRUEFT:   14  von 29
 * ```
 *
 * **Fast die Hälfte fiel aus dem Netz** — und er hat den Fehlerpfad nicht behauptet, sondern
 * versehentlich selbst erzeugt: `setSchienen(ladeSchienen(activeWorkspace))` **ohne**
 * `activeWorkspace` in der Liste lädt beim Bereichswechsel nicht neu, **und keine Zusage der Reihe
 * wird davon rot.**
 *
 * *Sein Satz dazu: „Der Aufwand, das zu schliessen, ist ein Wort." Er hatte recht — und weil ein
 * Befehl in einem Auftragstext nicht wiederholbar ist, steht er jetzt als Skript hier.*
 *
 * ---
 *
 * **Wie verglichen wird.** Je Haken ein Eintrag `art | name | abhängigkeiten`:
 *
 * - `useMemo` und `useCallback` hängen an einer Konstante → deren **Name** ist der Schlüssel.
 * - `useEffect` hat keinen Namen → Schlüssel ist die **Kurzfassung seines Rumpfes**. *Das ist
 *   schwächer als ein Name: ändert sich der Rumpf, gilt der Haken als ein anderer. Genau deshalb
 *   meldet das Skript beide Richtungen — fort UND neu —, statt nur eine Zahl zu vergleichen.*
 *
 * **Die Datei-Grenze wird bewusst ignoriert.** AUF-48 verschiebt Haken zwischen Dateien; verglichen
 * wird die **Menge über alle Teile**, sonst meldete jeder Umzug einen Unterschied, den es nicht gibt.
 *
 * ---
 *
 * **Aufruf**
 *
 * ```bash
 * node scripts/hook-abhaengigkeiten.mjs <basis-ref>
 * node scripts/hook-abhaengigkeiten.mjs 1406d2c6
 * ```
 *
 * Ohne Argument werden nur die Haken des Arbeitsbaums gezählt (Bestandsaufnahme, kein Vergleich).
 * Rückgabewert 1, sobald sich eine Liste unterscheidet.
 */
import { execFileSync } from 'node:child_process';
import { readFileSync } from 'node:fs';
import ts from 'typescript';

/** Die Teile, in die `HausplanerApp.tsx` zerlegt wurde. Aufgezählt, nicht erraten. */
const TEILE = [
  'resources/planner/hausplaner/app/HausplanerApp.tsx',
  'resources/planner/hausplaner/app/dashboard/Kopfrahmen.tsx',
  'resources/planner/hausplaner/app/rahmen/GruppenzeileUndSchiene.tsx',
  'resources/planner/hausplaner/app/rahmen/Buehne.tsx',
  'resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx',
];

/** **Die eine Änderung, um die es geht** — vorher stand hier nur `useMemo`. */
const HAKEN = ['useMemo', 'useCallback', 'useEffect'];

const einzeilig = (s) => s.replace(/\s+/g, ' ').trim();

/** Alle Haken mit Abhängigkeitsliste aus einem Quelltext. */
function haken(quelle, datei) {
  const f = ts.createSourceFile(datei, quelle, ts.ScriptTarget.Latest, true, ts.ScriptKind.TSX);
  const raus = [];
  const besuche = (n) => {
    if (ts.isCallExpression(n)) {
      const name = n.expression.getText(f);
      const art = HAKEN.find((h) => name === h || name.endsWith(`.${h}`));
      if (art && n.arguments.length >= 2) {
        const deps = einzeilig(n.arguments[1].getText(f));
        let schluessel = null;
        // useMemo/useCallback haengen an einer Konstante -> ihr Name ist der Schluessel.
        const eltern = n.parent;
        if (eltern && ts.isVariableDeclaration(eltern) && eltern.name) {
          schluessel = eltern.name.getText(f);
        } else {
          // useEffect: Kurzfassung des Rumpfes. Schwaecher, deshalb beide Richtungen melden.
          schluessel = `rumpf:${einzeilig(n.arguments[0].getText(f)).slice(0, 90)}`;
        }
        raus.push({ art, schluessel, deps });
      }
    }
    ts.forEachChild(n, besuche);
  };
  besuche(f);
  return raus;
}

function sammle(lies) {
  const alle = [];
  for (const datei of TEILE) {
    const quelle = lies(datei);
    if (quelle === null) continue;   // die Datei gab es im Basis-Stand noch nicht
    alle.push(...haken(quelle, datei));
  }
  return alle;
}

const basisRef = process.argv[2] ?? null;
const jetzt = sammle((d) => readFileSync(d, 'utf8'));

const zaehlung = {};
for (const h of jetzt) zaehlung[h.art] = (zaehlung[h.art] ?? 0) + 1;
console.log('Haken mit Abhaengigkeitsliste im Arbeitsbaum:');
for (const a of HAKEN) console.log(`  ${a.padEnd(12)} ${zaehlung[a] ?? 0}`);
console.log(`  ${'gesamt'.padEnd(12)} ${jetzt.length}`);

if (!basisRef) {
  console.log('\nKein Basis-Stand angegeben — nur gezaehlt, nicht verglichen.');
  process.exit(0);
}

const basis = sammle((d) => {
  try {
    return execFileSync('git', ['show', `${basisRef}:${d}`], { encoding: 'utf8', stdio: ['ignore', 'pipe', 'ignore'] });
  } catch {
    return null;
  }
});

const marke = (h) => `${h.art} ${h.schluessel} => ${h.deps}`;
const alsMenge = (liste) => {
  const m = new Map();
  for (const h of liste) m.set(marke(h), (m.get(marke(h)) ?? 0) + 1);
  return m;
};
const a = alsMenge(basis);
const b = alsMenge(jetzt);
const fort = [...a].filter(([k, n]) => (b.get(k) ?? 0) < n).map(([k]) => k);
const neu = [...b].filter(([k, n]) => (a.get(k) ?? 0) < n).map(([k]) => k);

console.log(`\nBasis ${basisRef}: ${basis.length} Haken   ·   Arbeitsbaum: ${jetzt.length} Haken`);
if (fort.length === 0 && neu.length === 0) {
  console.log('Kein Unterschied: jede Abhaengigkeitsliste ist unveraendert.');
  process.exit(0);
}
if (fort.length) { console.log('\nNUR IM BASIS-STAND (fort oder veraendert):'); for (const k of fort) console.log('  - ' + k); }
if (neu.length) { console.log('\nNUR IM ARBEITSBAUM (neu oder veraendert):'); for (const k of neu) console.log('  + ' + k); }
process.exit(1);
