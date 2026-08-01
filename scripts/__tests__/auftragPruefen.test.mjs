/**
 * AUF-87 — **die Zusagen des Validators.**
 *
 * Geprüft wird an **eigens erzeugten Blättern** in einem temporären Verzeichnis, nie an echten:
 * der Ausschluss des Blattes lautet *„der Validator wird an ihnen GEMESSEN, nicht an ihnen
 * repariert"*, und ein Test, der ein Bestandsblatt braucht, ändert sich mit dessen Inhalt.
 *
 * **Die Mutationspflicht des Blattes gilt hier wörtlich:** jede Gegenprobe wird zuerst auf
 * *Auffinden* geprüft — und die Datei muss nach der Mutation noch laufen. *Eine Mutation, die das
 * Skript zerlegt, liefert ein wertloses Rot.* Deshalb mutieren die Gegenproben **Daten**
 * (Testblätter, Denylist-Kopie), nicht die Quelldatei.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { mkdtempSync, writeFileSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import {
  pruefeBlatt, sammleBefehle, lieseKopfRoh, lieseAlleBloecke, zaehleBloecke, verbotenesMuster,
  bericht, strukturBefunde, aktiveBlaetter, baubareBlaetter, vergleicheErwartung, baumStand, brechendesGlied, STUFEN, DENYLIST,
  gateMuster, pruefeEintrag, GATE_MUSTER,
} from '../auftrag-pruefen.mjs';

const verz = mkdtempSync(join(tmpdir(), 'auf87-'));
process.on('exit', () => { try { rmSync(verz, { recursive: true, force: true }); } catch { /* egal */ } });

/** Ein Blatt aus einem YAML-Rumpf bauen — mit Prosa davor und danach, wie die echten. */
function blatt(name, yamlRumpf, { mitKopf = true } = {}) {
  const pfad = join(verz, name);
  const inhalt = mitKopf
    ? `# Testblatt\n\nProsa davor.\n\n\`\`\`yaml\n${yamlRumpf}\n\`\`\`\n\nProsa danach.\n`
    : `# Testblatt ohne Kopf\n\nNur Prosa, so wie die aelteren Blaetter.\n`;
  writeFileSync(pfad, inhalt, 'utf8');
  return pfad;
}

const KRIT = (id, befehl) => `  - id: ${id}\n    pruefung:\n      befehl: "${befehl}"`;

// --- K-01: die MENGE, nicht das Muster ------------------------------------------------------------

test('K-01: er findet jeden Prüfbefehl — scope UND jedes Kriterium', () => {
  const p = blatt('k01.md', [
    'auftrag:', '  id: TEST', 'scope:', '  population_command: "echo eins"',
    'kriterien:', KRIT('K-01', 'echo zwei'), KRIT('K-02', 'echo drei'),
  ].join('\n'));
  const e = pruefeBlatt(p, verz);
  assert.equal(e.kopf, true, 'der Kopf wurde nicht erkannt');
  assert.deepEqual(e.eintraege.map((x) => x.id), ['scope.population_command', 'K-01', 'K-02']);
});

test('K-01 (Gegenprobe): ein Kriterium mehr ⇒ die gefundene Zahl STEIGT', () => {
  // **Die Gegenprobe aus dem Blatt, wörtlich.** Steigt sie nicht, sucht er nach Muster statt nach
  // Menge — das ist F-01, die Klasse mit vier Ausprägungen.
  const rumpf = ['auftrag:', '  id: TEST', 'kriterien:', KRIT('K-01', 'echo a')].join('\n');
  const vorher = pruefeBlatt(blatt('k01a.md', rumpf), verz).eintraege.length;
  const nachher = pruefeBlatt(blatt('k01b.md', `${rumpf}\n${KRIT('K-02', 'echo b')}`), verz).eintraege.length;
  assert.equal(vorher, 1);
  assert.equal(nachher, 2, 'die Zahl ist nicht gestiegen — er sucht nach Muster, nicht nach Menge');
});

// --- K-02 / K-03: die drei Stufen -----------------------------------------------------------------

test('K-02: exit != 0 ⇒ FEHLSCHLAG, mit K-id, Befehl und Exitcode', () => {
  // **Der Fall aus T1a Fassung 1:** das K-05 nannte eine Testdatei mit `innerWidth`, `grep` lieferte
  // `exit 1`, und das Blatt lag trotzdem.
  // **Nachgezogen mit AUF-87-N2/K-07:** ein `grep` mit null Treffern ist seit dieser Stufe ein
  // NULLTREFFER, kein Fehlschlag. Der echte Fehlschlag ist ein Befehl, den es nicht gibt —
  // `exit 127`, und genau den trägt `AUFTRAGSSCHEMA.md` (gemessen im Bestandslauf 08:03).
  const p = blatt('k02.md', ['auftrag:', '  id: TEST', 'kriterien:',
    KRIT('K-05', './scripts/gibt-es-nicht.sh')].join('\n'));
  const e = pruefeBlatt(p, verz);
  const treffer = e.eintraege[0];
  assert.equal(treffer.stufe, STUFEN.FEHLSCHLAG);
  assert.equal(treffer.id, 'K-05');
  assert.match(treffer.hinweis, /exit \d/, 'der Exitcode fehlt in der Meldung');
  const text = bericht(e);
  assert.match(text, /FEHLSCHLAG/);
  assert.match(text, /gibt-es-nicht/, 'der Befehl steht nicht im Bericht');
});

test('K-03: exit 0 mit LEERER Ausgabe ⇒ VERDÄCHTIG — eine eigene Stufe', () => {
  // **Der gefährlichere Fall: er sieht aus wie Erfolg.** So ist dem Planner die Grundgesamtheit von
  // T3 durchgerutscht — der Befehl lief, er beschrieb nur einen Stand von vor vier Tagen.
  const p = blatt('k03.md', ['auftrag:', '  id: TEST', 'kriterien:',
    KRIT('K-01', 'true'), KRIT('K-02', 'echo etwas')].join('\n'));
  const e = pruefeBlatt(p, verz);
  assert.equal(e.eintraege[0].stufe, STUFEN.VERDAECHTIG);
  assert.equal(e.eintraege[1].stufe, STUFEN.OK);
  // **Drei unterscheidbare Stufen, nicht zwei** — das ist die Aussage des Kriteriums.
  assert.notEqual(STUFEN.VERDAECHTIG, STUFEN.FEHLSCHLAG);
  assert.notEqual(STUFEN.VERDAECHTIG, STUFEN.OK);
  assert.match(e.eintraege[0].hinweis, /KEINE Ausgabe/);
});

// --- K-04: ein Blatt ohne Kopf ist kein Fehler ----------------------------------------------------

test('K-04: ohne YAML-Kopf ⇒ eigene Meldung, kein Fehlschlag', () => {
  // **67 der 80 Blätter im Bestand haben keinen Kopf** (gemessen 30.07.). Ein Werkzeug, das bei
  // ihnen rot wird, wird abgeschaltet — und fängt dann auch die neuen nicht mehr.
  const e = pruefeBlatt(blatt('k04.md', '', { mitKopf: false }), verz);
  assert.equal(e.kopf, false);
  assert.deepEqual(e.eintraege, [], 'ohne Kopf darf nichts ausgeführt werden');
  assert.match(bericht(e), /KEIN KOPF/);
  assert.doesNotMatch(bericht(e), /FEHLSCHLAG/, 'ein kopfloses Blatt wird als Fehler gemeldet');
});

test('K-04: ein UNLESBARER Kopf wird als solcher benannt — nicht als „kein Kopf"', () => {
  // Die beiden Fälle sind verschieden: „hat keinen" ist normal, „hat einen kaputten" ist ein
  // Befund. Sie zusammenzuwerfen versteckt den zweiten hinter der Häufigkeit des ersten.
  const e = pruefeBlatt(blatt('k04b.md', 'auftrag:\n  id: [unabgeschlossen\n  x: "'), verz);
  assert.equal(e.kopf, false);
  assert.ok(e.unlesbar, 'der Parser-Fehler wird verschwiegen');
  assert.match(bericht(e), /KOPF UNLESBAR/);
});

// --- K-05: er tut nicht so, als hätte er alles geprüft ---------------------------------------------

test('K-05: ein visuelles Kriterium ⇒ NICHT MASCHINELL, mit `ausgefuehrt_von`', () => {
  // **Der Validator darf kein grünes Häkchen für ein Blatt geben, dessen Kriterien sämtlich
  // visuell sind** — er gibt die Liste dessen, was ein Mensch ansehen muss.
  const p = blatt('k05.md', ['auftrag:', '  id: TEST', 'kriterien:',
    '  - id: K-01', '    ausgefuehrt_von: evaluator', '    pruefung:', '      typ: visuell',
    '      schritte: "1440 px"'].join('\n'));
  const e = pruefeBlatt(p, verz);
  assert.equal(e.eintraege.length, 1, 'ein Kriterium ohne Befehl wird übersehen');
  assert.equal(e.eintraege[0].stufe, STUFEN.NICHT_MASCHINELL);
  assert.equal(e.eintraege[0].id, 'K-01');
  assert.match(e.eintraege[0].hinweis, /typ: visuell/);
  assert.match(e.eintraege[0].hinweis, /ausgefuehrt_von: evaluator/);
});

// --- K-06: die Denylist greift, und sie schweigt nicht ---------------------------------------------

/**
 * **Die ausgeschriebene zweite Meinung.** Befund `AUF-87-B1` (P1): meine erste Zusage lief mit
 * `for (const muster of DENYLIST)` über genau die Liste, die sie sichern sollte. Wer einen Eintrag
 * **ersetzt**, lässt die Länge gleich — und keine einzige Zusage wurde rot. Der Evaluator hat es
 * gemessen: `curl` ausgetauscht, Suite 14/0 grün, `verbotenesMuster('curl http://x')` ⇒ `null`.
 *
 * **Hier ist eine Doppelung erwünscht**, weil der Test die *zweite Meinung* ist und nicht die
 * Wiederholung der ersten. Weicht die Quelle ab, wird es rot — in beide Richtungen.
 */
const ERWARTETE_MUSTER = [
  'git commit', 'git push', 'git add', 'git reset', 'git checkout', 'git switch',
  'rm', 'mv', 'chmod', 'truncate', 'dd', 'tee',
  'umleitung', 'npm run build', 'npx vite build', 'curl', 'wget',
];

test('K-06: die Denylist ist GENAU diese Menge — Zusage und Quelle sind zwei Meinungen', () => {
  // **In beide Richtungen.** Ein gelöschtes Muster fällt auf, ein ERSETZTES auch — das war die
  // Lücke: die Länge gleich zu lassen genügte, um jedes einzelne unschädlich zu machen.
  assert.deepEqual([...DENYLIST].sort(), [...ERWARTETE_MUSTER].sort(),
    'die Denylist weicht von der ausgeschriebenen Erwartung ab — Löschung ODER Austausch');
});

test('K-06: jedes einzelne Muster wirkt — an einem Befehl, der es wirklich enthält', () => {
  // Die Wirkung je Muster, nicht die Anwesenheit in einer Liste. Die Beispiele sind ausgeschrieben,
  // damit auch hier nichts über die Quelle iteriert.
  const faelle = [
    ['git commit -m x', 'git commit'], ['git push origin main', 'git push'],
    ['git add .', 'git add'], ['git reset --hard', 'git reset'],
    ['git checkout -- x', 'git checkout'], ['git switch main', 'git switch'],
    ['rm -rf /tmp/x', 'rm'], ['mv a b', 'mv'], ['chmod +x x', 'chmod'],
    ['truncate -s 0 x', 'truncate'], ['dd if=/dev/zero of=x', 'dd'], ['echo x | tee y', 'tee'],
    ['echo x > datei', 'umleitung'], ['npm run build:hausplaner', 'npm run build'],
    ['npx vite build --config x', 'npx vite build'], ['curl http://x', 'curl'], ['wget http://x', 'wget'],
  ];
  assert.equal(faelle.length, ERWARTETE_MUSTER.length, 'nicht jedes Muster hat einen eigenen Fall');
  for (const [befehl, muster] of faelle) {
    assert.equal(verbotenesMuster(befehl), muster, `„${befehl}" wird nicht als ${muster} erkannt`);
  }
});

test('K-06 (Befund AUF-87-B2): die neun Schreibweisen, die vorher durchschlüpften', () => {
  // **Der P2-Befund, ausgeschrieben.** `includes('rm ')` griff nur mit Leerzeichen; neun
  // realistische Formen kamen durch. Jede davon steht hier einzeln.
  const vorherDurchgeschluepft = [
    'echo x >datei', 'echo x >>docs/a.md', 'rm	-rf /tmp/x', 'git  commit -m x',
    'git switch main', 'npx vite build', 'echo x | tee y', 'truncate -s 0 x', 'dd if=/dev/zero of=x',
  ];
  for (const befehl of vorherDurchgeschluepft) {
    assert.ok(verbotenesMuster(befehl), `„${befehl}" schlüpft weiterhin durch`);
  }
});

test('K-06: harmlose Befehle werden NICHT abgelehnt — die Wortgrenze trägt', () => {
  // **Der Preis der Verschärfung, und er muss gemessen werden.** `rm` als Wortmuster darf `npm`
  // nicht fangen, `dd` nicht `--add`. Ohne diese Zusage wäre die Liste sicher und unbrauchbar.
  for (const harmlos of [
    'npm run test:hausplaner', 'grep -c foo bar', 'node scripts/statische-inline-stile.mjs x',
    'npm run tsc:hausplaner', 'sed -n "1,20p" datei', 'git log --oneline -5',
    'php artisan route:list', 'grep -rn "format" src',
  ]) {
    assert.equal(verbotenesMuster(harmlos), null, `„${harmlos}" wird faelschlich abgelehnt`);
  }
});

test('K-06: ein schreibender Befehl wird ÜBERSPRUNGEN — mit Grund', () => {
  const p = blatt('k06.md', ['auftrag:', '  id: TEST', 'kriterien:',
    KRIT('K-01', 'git commit -m x')].join('\n'));
  const e = pruefeBlatt(p, verz);
  assert.equal(e.eintraege[0].stufe, STUFEN.UEBERSPRUNGEN);
  assert.match(e.eintraege[0].hinweis, /git commit/, 'der Grund nennt das Muster nicht');
  assert.match(bericht(e), /UEBERSPRUNGEN/, 'er verschweigt den übersprungenen Befehl');
});


// --- AUF-87-N2 / K-07: der erwartete Nulltreffer ---------------------------------------------------

test('N2/K-07: ein `grep` mit null Treffern ist NULLTREFFER, kein Fehlschlag', () => {
  // **Der T5-Fall.** Der Befehl verkettet drei `grep`; der letzte sucht `collapsed|klappZu|…` und
  // findet **0** — genau das ist das gewünschte Ergebnis, es beweist die Lücke. `grep` liefert
  // dafür exit 1 und reißt die Kette. *Der Befehl ist richtig, sein Exitcode ist es nicht.*
  const p = blatt('n2k07.md', ['auftrag:', '  id: TEST', 'kriterien:',
    KRIT('K-01', 'grep -c GibtEsGarantiertNichtXY /dev/null')].join('\n'));
  const e = pruefeBlatt(p, verz);
  assert.equal(e.eintraege[0].stufe, STUFEN.NULLTREFFER);
  assert.notEqual(e.eintraege[0].stufe, STUFEN.FEHLSCHLAG);
});

test('N2/K-07: die Kette nennt das brechende Glied UND was danach nicht mehr lief', () => {
  // **Ohne diese Zahl wäre die neue Stufe eine Beruhigung.** Sie sagt nicht „alles gut", sondern
  // „hier bricht es, und diese Glieder hast du nie gemessen".
  const kette = 'echo eins && grep -c GibtEsGarantiertNichtXY /dev/null && echo drei && echo vier';
  const p = blatt('n2k07b.md', ['auftrag:', '  id: TEST', 'kriterien:', KRIT('K-01', kette)].join('\n'));
  const e = pruefeBlatt(p, verz);
  assert.equal(e.eintraege[0].stufe, STUFEN.NULLTREFFER);
  assert.match(e.eintraege[0].hinweis, /grep -c/, 'das brechende Glied wird nicht genannt');
  assert.match(e.eintraege[0].hinweis, /2 weitere/, 'die nicht gelaufenen Glieder werden nicht gezählt');
});

test('N2/K-07 (Grenze): `exit 127` bleibt FEHLSCHLAG — die Unterscheidung ist der Punkt', () => {
  // **Sonst wäre die neue Stufe ein Freibrief.** `exit 1` von `grep` heißt „nichts gefunden";
  // `exit 127` heißt „den Befehl gibt es nicht" — und genau der steht in `AUFTRAGSSCHEMA.md`.
  const p = blatt('n2k07c.md', ['auftrag:', '  id: TEST', 'kriterien:',
    KRIT('K-01', './scripts/zaehle-statische-stile.sh x')].join('\n'));
  assert.equal(pruefeBlatt(p, verz).eintraege[0].stufe, STUFEN.FEHLSCHLAG);
  // **Und ein nicht-suchender Befehl mit exit 1 ebenfalls: nur SUCHEN darf null liefern.**
  // (`false` statt eines `node -e`-Aufrufs — dessen Anführungszeichen zerbrechen das Test-YAML,
  // und ein kaputtes Blatt hätte hier den Kopf unlesbar gemacht statt den Befehl zu prüfen.
  // *Erst gebaut, dann daran gescheitert, dann gemerkt.*)
  const q = blatt('n2k07d.md', ['auftrag:', '  id: TEST', 'kriterien:',
    KRIT('K-01', 'false')].join('\n'));
  assert.equal(pruefeBlatt(q, verz).eintraege[0].stufe, STUFEN.FEHLSCHLAG);
  assert.equal(brechendesGlied('false', verz), null);
});

// --- AUF-87-N2 / K-06: ALLE Blöcke ----------------------------------------------------------------

test('N2/K-06: der zweite Block wird gelesen, nicht nur gezählt', () => {
  // **Mein eigener Befund vom 08:03:** *„2 yaml-Blöcke, geprüft wurde der ERSTE"*. Ehrlich, aber
  // folgenlos — die R19-Messblöcke des Planners stehen im zweiten und waren unsichtbar.
  const pfad = join(verz, 'n2k06.md');
  writeFileSync(pfad, [
    '```yaml', 'auftrag:', '  id: ERSTER', 'kriterien:', KRIT('K-01', 'echo kopf'), '```',
    'Prosa', '```yaml', 'measurements:', '  - was: Schienen', 'kriterien:',
    KRIT('M-01', 'echo messblock'), '```',
  ].join('\n'), 'utf8');
  const e = pruefeBlatt(pfad, verz);
  assert.equal(e.bloecke, 2);
  assert.deepEqual(e.eintraege.map((x) => x.id), ['K-01', 'block2.M-01'],
    'der zweite Block wird nicht gelesen');
  assert.match(bericht(e), /2 yaml-Bloecke gelesen/);
  assert.doesNotMatch(bericht(e), /geprueft wurde der ERSTE/, 'der alte Hinweis steht noch');
});

test('N2/K-06: `lieseAlleBloecke` findet jeden Block, `lieseKopfRoh` bleibt der erste', () => {
  const text = '```yaml\na: 1\n```\nx\n```yaml\nb: 2\n```\ny\n```yaml\nc: 3\n```';
  assert.deepEqual(lieseAlleBloecke(text), ['a: 1', 'b: 2', 'c: 3']);
  assert.equal(lieseKopfRoh(text), 'a: 1', 'der Kopf ist nicht mehr der erste Block');
});

// --- AUF-87-N2 / K-01 bis K-05: die fünf Strukturprüfungen -----------------------------------------

test('N2/K-01: genau EIN Blatt mit `status: aktiv` — sonst werden alle genannt', () => {
  // **Der Beleg ist frisch und er ist der des Planners:** am 30.07. trug die Auftragstafel sieben
  // Steuerungsmarken statt einer, sechs davon auf abgenommene Posten.
  const ergebnisse = [
    { pfad: 'a.md', status: 'aktiv' }, { pfad: 'b.md', status: 'gesperrt' }, { pfad: 'c.md', status: 'aktiv' },
  ];
  assert.deepEqual(aktiveBlaetter(ergebnisse), ['a.md', 'c.md'], 'nicht alle aktiven werden genannt');
  assert.deepEqual(aktiveBlaetter([{ pfad: 'a.md', status: 'aktiv' }]), ['a.md']);
  assert.deepEqual(aktiveBlaetter([{ pfad: 'a.md', status: 'gesperrt' }]), []);
});

test('N2/K-02: ein Kriterium ohne Befehl und ohne Prüftyp ⇒ Strukturbefund', () => {
  const befunde = strukturBefunde({
    auftrag: { id: 'T' },
    kriterien: [{ id: 'K-01', typ: 'presence', pruefung: {} }],
  });
  assert.ok(befunde.some((b) => b.regel === 'S-02' && b.id === 'K-01'), 'S-02 greift nicht');
  // Mit Befehl ist es sauber. **S-09 wird herausgefiltert** — dieser Kopf traegt bewusst keinen
  // `status`, und seit dem 01.08. meldet der Validator das eigenstaendig (F-08b). Die Zusage hier
  // prueft S-02, nicht die Statuspflicht.
  assert.deepEqual(
    strukturBefunde({ kriterien: [{ id: 'K-01', typ: 'presence', pruefung: { befehl: 'echo x' } }] })
      .filter((b) => b.regel !== 'S-09'), []);
});

test('N2/K-03: `absence` P1 ohne presence-Partner ⇒ Strukturbefund', () => {
  // **Die Prüfung mit dem höchsten Wert.** T2 hatte vier `absence`-Kriterien mit `grep`; der
  // Evaluator holte die entfernte Navigation zurück — kein Test wurde rot.
  // *Ohne Partner hat man nicht aufgeräumt, sondern entfernt.*
  const ohne = strukturBefunde({
    kriterien: [
      { id: 'K-01', typ: 'absence', kritikalitaet: 'P1', pruefung: { befehl: 'echo a' } },
      { id: 'K-02', typ: 'absence', kritikalitaet: 'P1', pruefung: { befehl: 'echo b' } },
    ],
  });
  assert.equal(ohne.filter((b) => b.regel === 'S-03').length, 2, 'S-03 greift nicht je Kriterium');

  const mit = strukturBefunde({
    kriterien: [
      { id: 'K-01', typ: 'absence', kritikalitaet: 'P1', pruefung: { befehl: 'echo a' } },
      { id: 'K-02', typ: 'presence', kritikalitaet: 'P1', pruefung: { befehl: 'echo b' } },
    ],
  });
  assert.deepEqual(mit.filter((b) => b.regel === 'S-03'), [], 'ein Partner genügt, und er wird nicht gesehen');

  // P2 ist ausgenommen — die Regel nennt P0/P1.
  const p2 = strukturBefunde({ kriterien: [{ id: 'K-01', typ: 'absence', kritikalitaet: 'P2', pruefung: { befehl: 'echo a' } }] });
  assert.deepEqual(p2.filter((b) => b.regel === 'S-03'), []);
});

test('N2/K-04: `coverage` ohne Grundgesamtheit ⇒ Strukturbefund', () => {
  const ohne = strukturBefunde({ kriterien: [{ id: 'K-01', typ: 'coverage', pruefung: {} }] });
  assert.ok(ohne.some((b) => b.regel === 'S-04'), 'S-04 greift nicht');
  const mit = strukturBefunde({
    scope: { population_command: 'ls' },
    kriterien: [{ id: 'K-01', typ: 'coverage', pruefung: { befehl: 'echo x' } }],
  });
  assert.deepEqual(mit.filter((b) => b.regel === 'S-04'), []);
});

test('N2/K-05: ein Ausschluss ohne `entschieden_von` ⇒ Strukturbefund', () => {
  const befunde = strukturBefunde({
    scope: { ausschluesse: [{ stelle: 'irgendwo', grund: 'weil' }] },
    kriterien: [],
  });
  assert.ok(befunde.some((b) => b.regel === 'S-05' && /entschieden_von/.test(b.text)));
  // Und ohne Grund ebenfalls — beide Felder, nicht nur eines.
  const ohneGrund = strukturBefunde({ scope: { ausschluesse: [{ stelle: 'x', entschieden_von: 'planner' }] }, kriterien: [] });
  assert.ok(ohneGrund.some((b) => b.regel === 'S-05' && /grund/.test(b.text)));
  // Vollständig ⇒ kein Befund.
  assert.deepEqual(
    strukturBefunde({ scope: { ausschluesse: [{ stelle: 'x', grund: 'y', entschieden_von: 'planner' }] }, kriterien: [], auftrag: { status: 'bereit' } }), []);
});

test('N2: die Strukturbefunde stehen im Bericht — sie werden nicht still gezählt', () => {
  const p = blatt('n2bericht.md', ['auftrag:', '  id: TEST', 'kriterien:',
    '  - id: K-01', '    typ: absence', '    kritikalitaet: P1', '    pruefung:',
    '      befehl: "echo a"'].join('\n'));
  const text = bericht(pruefeBlatt(p, verz));
  assert.match(text, /STRUKTUR S-03/, 'der Strukturbefund erscheint nicht im Bericht');
  assert.match(text, /STRUKTUR-Befund\(e\)/, 'die Zusammenfassung zählt ihn nicht');
});

// --- Der Kopf selbst -------------------------------------------------------------------------------

test('mehrere yaml-Blöcke: ALLE werden gelesen, und die Zahl wird gesagt', () => {
  // **Umgehängt mit AUF-87-N2 / K-06.** Die erste Fassung hielt fest, dass nur der Kopf geprüft
  // wird — ehrlich gemeldet, aber folgenlos: die R19-Messblöcke des Planners standen in zweiten
  // Blöcken und waren unsichtbar. *Die Zusage wird nicht gelöscht, sondern auf das neue Verhalten
  // gedreht: gelesen wird jetzt jeder Block, und die Zahl steht weiterhin im Bericht.*
  const pfad = join(verz, 'drei.md');
  writeFileSync(pfad, [
    '```yaml', 'auftrag:', '  id: ERSTER', 'kriterien:', KRIT('K-01', 'echo a'), '```',
    'Prosa', '```yaml', 'kriterien:', KRIT('M-01', 'echo b'), '```',
    'Prosa', '```yaml', 'x: 1', '```',
  ].join('\n'), 'utf8');
  const e = pruefeBlatt(pfad, verz);
  assert.equal(e.bloecke, 3);
  assert.deepEqual(e.eintraege.map((x) => x.id), ['K-01', 'block2.M-01'],
    'nicht jeder Block wird gelesen — der dritte trägt keine Kriterien und darf nichts beitragen');
  assert.match(bericht(e), /3 yaml-Bloecke gelesen/);
});

test('der Kopf wird an den Zäunen erkannt, nicht geraten', () => {
  assert.equal(lieseKopfRoh('kein block hier'), null);
  assert.equal(lieseKopfRoh('```yaml\nohne Ende'), null, 'ein offener Block wird als Kopf gelesen');
  assert.equal(lieseKopfRoh('```yaml\na: 1\n```'), 'a: 1');
  assert.equal(zaehleBloecke('```yaml\n```\n```yaml\n```'), 2);
});

test('ein Kopf ohne jeden Prüfbefehl wird gesagt — nicht als „alles gut" gemeldet', () => {
  const e = pruefeBlatt(blatt('leer.md', 'auftrag:\n  id: TEST\n  ziel: "nur Prosa"'), verz);
  assert.deepEqual(e.eintraege, []);
  assert.match(bericht(e), /KEIN PRUEFBEFEHL/);
});

test('sammleBefehle liest auch einen Kopf, dessen Felder unter `auftrag:` hängen', () => {
  // Die Blätter sind nicht einheitlich: manche setzen `scope`/`kriterien` auf oberster Ebene,
  // manche unter `auftrag`. **Beide Formen kommen im Bestand vor** — er darf an keiner scheitern.
  const oben = sammleBefehle({ scope: { population_command: 'echo a' }, kriterien: [{ id: 'K-1', pruefung: { befehl: 'echo b' } }] });
  const unten = sammleBefehle({ auftrag: { scope: { population_command: 'echo a' }, kriterien: [{ id: 'K-1', pruefung: { befehl: 'echo b' } }] } });
  assert.equal(oben.length, 2);
  assert.equal(unten.length, 2, 'die verschachtelte Form wird übersehen');
});

// --- PB-019: der Validator muss SPERREN, nicht nur reden ------------------------------------------

/**
 * **Der Befund, der diese vier Zusagen nötig gemacht hat.** *„Der Validator **benennt** `KEIN KOPF`
 * — aber `exit 0` ließe sechs aktive Blätter durch."* Gemessen am 01.08. am damals aktiven
 * AUF-38-P2-Blatt: `exit 0`. **Ein Gate, das nur redet, ist keine Barriere** (R9 verlangt bei der
 * zweiten Wiederholung eine technische Sperre, keinen dritten Vorsatz).
 *
 * **Die Grenze ist mit Bedacht gezogen:** ein Blatt ohne Kopf bleibt straffrei — 67 von 80 im
 * Bestand haben keinen, und ein Werkzeug, das bei ihnen rot wird, wird abgeschaltet. Gesperrt wird
 * nur, wo nach dem Blatt **gebaut wird** (`status: aktiv`) oder wo jemand einen Kopf geschrieben
 * hat, der **nichts misst**.
 */
/** Eine Datei ohne Kopf, Inhalt frei waehlbar — der Helfer `blatt()` kann das nicht. */
function roheDatei(name, inhalt) {
  const pfad = join(verz, name);
  writeFileSync(pfad, inhalt, 'utf8');
  return pfad;
}

test('PB-019: ein altes Blatt ohne Kopf bleibt straffrei', () => {
  const e = pruefeBlatt(roheDatei('alt.md', '# Altes Blatt\n\nNur Fliesstext, kein Kopf.\n'), verz);
  assert.equal(e.kopf, false);
  assert.ok(!e.aktivOhneKopf, 'ein Blatt ohne `status: aktiv` darf nicht sperren');
  assert.match(bericht(e), /kein Fehler/);
});

test('PB-019: ein Blatt ohne Kopf, das sich `status: aktiv` nennt, sperrt', () => {
  const e = pruefeBlatt(roheDatei('aktiv-ohne-kopf.md', '# Blatt\n\nstatus: aktiv\n\nkein Kopf.\n'), verz);
  assert.equal(e.kopf, false);
  assert.equal(e.aktivOhneKopf, true);
  assert.match(bericht(e), /SPERRE.*KEIN KOPF/);
});

test('PB-019: ein Kopf ohne jeden Prüfbefehl sperrt', () => {
  const e = pruefeBlatt(blatt('leer2.md', 'auftrag:\n  id: TEST\n  ziel: "nur Prosa"'), verz);
  assert.deepEqual(e.eintraege, []);
  assert.match(bericht(e), /SPERRE.*KEIN PRUEFBEFEHL/);
});

test('PB-019: ein unlesbarer Kopf sperrt immer — jemand hat einen geschrieben, und er trägt nicht', () => {
  const e = pruefeBlatt(blatt('kaputt.md', 'auftrag:\n  id: [unbeendet'), verz);
  assert.equal(e.kopf, false);
  assert.ok(e.unlesbar, 'der Grund gehört in den Bericht');
  assert.equal(e.aktivOhneKopf, true);
});


// --- F-08: die Schlange darf nicht leerlaufen -----------------------------------------------------

/**
 * **Der Fall, der diese Zusagen nötig gemacht hat.** Am 01.08. um 11:20 hatte der Generator genau
 * EIN Blatt. Er hat es gezogen, gemessen, und mit einem Befund zurückgegeben — *völlig richtig* —
 * und stand danach ohne Arbeit da. **R16 verlangt seit dem 29.07. mindestens zwei baubare Aufträge;
 * die Regel war ein Vorsatz und hat nicht getragen.**
 */
test('F-08: `bereit` und `aktiv` sind baubar', () => {
  const e = [{ pfad: 'a', status: 'aktiv' }, { pfad: 'b', status: 'bereit' }];
  assert.deepEqual(baubareBlaetter(e), ['a', 'b']);
});

test('F-08: gesperrt, ruht, erledigt und zurueckgestellt sind NICHT baubar', () => {
  const e = [
    { pfad: 'a', status: 'gesperrt' },
    { pfad: 'b', status: 'ruht' },
    { pfad: 'c', status: 'erledigt' },
    { pfad: 'd', status: 'zurueckgestellt' },
  ];
  assert.deepEqual(baubareBlaetter(e), [], 'ein gesperrtes Blatt kann niemand ziehen');
});

test('F-08: `ruht` zaehlt nicht — der Zustand ist ausdruecklich NICHT nachgemessen', () => {
  // Am 01.08. sind 15 Blaetter von `aktiv` auf `ruht` gesetzt worden, weil ihr Zustand nicht
  // nachgemessen war. Wuerden sie als baubar zaehlen, meldete die Schlange 16 statt 1 - und
  // die Barriere waere von Anfang an blind.
  const e = Array.from({ length: 15 }, (_, i) => ({ pfad: `alt${i}`, status: 'ruht' }));
  e.push({ pfad: 'echt', status: 'aktiv' });
  assert.deepEqual(baubareBlaetter(e), ['echt']);
});

test('F-08: ein Blatt ohne Kopf hat keinen Status und ist nicht baubar', () => {
  assert.deepEqual(baubareBlaetter([{ pfad: 'alt.md', status: null }]), []);
});

// --- F-07 / F-04: was schon steht, wird nicht noch einmal bestellt -------------------------------

/**
 * **Der Fall:** `geometry/fangKern.ts` lag am 01.08. seit Tagen fertig da — 103 Zeilen, 12 Zusagen —
 * **und wurde von nichts benutzt.** Beinahe hätte ich ihn ein zweites Mal bauen lassen (F-07).
 * Und `ausgangswert` ist eine Messung vom Tag des Schreibens: **sie veraltet, ohne dass es jemand
 * merkt** (F-04).
 *
 * **Die Grenze, die diese Zusagen ziehen:** eine **Wache** — ein Kriterium, dessen `ausgangswert`
 * schon gleich `erwartet` ist — meldet KEIN „steht schon". Sie sagt ja gerade: *das ist heute so
 * und soll so bleiben.* Ohne diese Grenze waren drei von acht Meldungen falscher Alarm.
 */
test('S-07: was `mindestens N` schon vor dem Bau erfuellt, ist kein Auftrag', () => {
  const e = { id: 'K-1', stufe: STUFEN.OK, ausgabe: '3', erwartet: 'mindestens 1', ausgangswert: '0' };
  assert.match(vergleicheErwartung(e).text, /STEHT SCHON/);
  assert.equal(vergleicheErwartung(e).regel, 'S-07');
});

test('S-07 schweigt bei einer WACHE — Ausgangswert und Erwartung sind gleich', () => {
  const wache = { id: 'K-2', stufe: STUFEN.OK, ausgabe: '0', erwartet: '0', ausgangswert: '0' };
  assert.equal(vergleicheErwartung(wache), null, 'eine Wache ist kein Bauziel');
});

test('S-08: eine gebrochene Wache meldet sich — genau so fiel PB-023/K-03 auf', () => {
  const wache = { id: 'K-3', stufe: STUFEN.OK, ausgabe: '1', erwartet: '0', ausgangswert: '0' };
  const b = vergleicheErwartung(wache);
  assert.equal(b.regel, 'S-08');
  assert.match(b.text, /Blatt sagt 0, gemessen 1/);
});

test('S-08: ein veralteter Ausgangswert meldet sich', () => {
  const e = { id: 'K-4', stufe: STUFEN.OK, ausgabe: '18', erwartet: '0', ausgangswert: '27' };
  assert.match(vergleicheErwartung(e).text, /AUSGANGSWERT VERALTET/);
});

test('kein Alarm, wenn nichts eindeutig vergleichbar ist', () => {
  assert.equal(vergleicheErwartung({ id: 'K-5', stufe: STUFEN.OK, ausgabe: 'gruen', erwartet: 'gruen' }), null);
  assert.equal(vergleicheErwartung({ id: 'K-6', stufe: STUFEN.NICHT_MASCHINELL, ausgabe: null }), null);
});

// --- F-08b und F-03/F-12: Statuspflicht und der wandernde Baum ------------------------------------

test('S-09: ein Kopf ohne `status` wird gemeldet — die Lage stuende sonst nur in Tafel und Ledger', () => {
  const befunde = strukturBefunde({ auftrag: { id: 'T' }, kriterien: [] });
  assert.ok(befunde.some((b) => b.regel === 'S-09'), 'F-08b: die Entscheidung gehoert ins Blatt');
});

test('S-09 schweigt, wenn der Status dasteht', () => {
  const befunde = strukturBefunde({ auftrag: { id: 'T', status: 'bereit' }, kriterien: [] });
  assert.equal(befunde.filter((b) => b.regel === 'S-09').length, 0);
});

test('S-10: baumStand liefert einen SHA oder null — nie einen Fehler', () => {
  const s = baumStand();
  assert.ok(s === null || /^[0-9a-f]{40}$/.test(s), `unerwartet: ${s}`);
  assert.equal(baumStand('/gibt/es/nicht'), null, 'ausserhalb eines Repos ist es null, kein Wurf');
});

// --- GATE: Testsuiten und Buildketten gehoeren nicht in den Validator-Lauf (01.08.2026) ----------

test('GATE: ein npm-run-Befehl wird NICHT gefahren, sondern als Gate gemeldet', () => {
  const e = pruefeEintrag({ id: 'K-X', befehl: 'npm run test:hausplaner -- --filter=stilschicht' }, verz);
  assert.equal(e.stufe, STUFEN.NICHT_MASCHINELL, 'ein Gate ist nicht maschinell geprueft');
  assert.match(e.hinweis, /GATE/, 'die Meldung sagt, WARUM er nicht lief');
  assert.match(e.hinweis, /npm run/, 'sie nennt das Muster');
});

test('GATE: php artisan zaehlt genauso', () => {
  assert.equal(gateMuster('php artisan test --filter=Unterlage'), 'php artisan');
});

test('GATE (Gegenprobe ROT): ein harmloser Befehl wird weiterhin AUSGEFUEHRT', () => {
  const e = pruefeEintrag({ id: 'K-Y', befehl: 'echo zwei' }, verz);
  assert.equal(e.stufe, STUFEN.OK, 'sonst prueft der Validator gar nichts mehr');
  assert.equal(e.ausgabe, 'zwei');
  assert.equal(gateMuster('echo zwei'), null);
});

test('GATE (Gegenprobe ROT): `npm` OHNE `run` ist kein Gate — die Wortform greift', () => {
  assert.equal(gateMuster('node scripts/zaehle.mjs datei npm'), null,
    'sonst faengt das Muster jedes Blatt, das das Wort npm nur erwaehnt');
});

test('GATE: die Denylist hat Vorrang — `npm run build` bleibt UEBERSPRUNGEN, nicht Gate', () => {
  const e = pruefeEintrag({ id: 'K-Z', befehl: 'npm run build:hausplaner' }, verz);
  assert.equal(e.stufe, STUFEN.UEBERSPRUNGEN, 'Bauen veraendert etwas - das ist der schaerfere Grund');
  assert.match(e.hinweis, /npm run build/);
});

test('GATE: die Liste steht im Werkzeug, nicht im Blatt', () => {
  assert.ok(GATE_MUSTER.includes('npm run'));
  assert.ok(GATE_MUSTER.includes('php artisan'));
  assert.ok(!GATE_MUSTER.includes('node'), 'node ist der Validator selbst - nie ein Gate');
});
