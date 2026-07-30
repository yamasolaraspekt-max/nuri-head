/**
 * AUF-48 Scheibe 4d / K-04 — **das Eigenschaften-Panel, zwölf Bindungen erstmals verriegelt.**
 *
 * ---
 *
 * **Die Probe VOR dem Schreiben, wie K-04 sie verlangt — 15 Mutationen, 12 kamen durch:**
 *
 * ```text
 * blind (12)  Sicht-Schalter verdreht · Sperr-Schalter verdreht · Sicht-Beschriftung vertauscht
 *             Sperr-Knopf zeigt nie den aktiven Zustand · Rueckfrage vor dem Entsperren entfaellt
 *             Anbau-Laenge schreibt in die Breite · Anbau-Breite zeigt die Laenge
 *             Anbau-Basis vergisst die alten Werte · Pruefungs-Reiter zeigt Allgemein
 *             Schwere-TEXT entfernt · Schwere-SYMBOL entfernt · Reiter-Zustand fest auf "fertig"
 * gefangen(3) Reiter aus PANEL_TABS entfernt · Panel ohne Reiter-Bezug
 *             Reiter-Hinweis wieder eine Vertroestung
 * ```
 *
 * **Zwei davon sind keine Schönheitsfehler:**
 *
 * - **Die Schwere-Zeile.** Der Kommentar im Code sagt seit jeher *„Schwere als Symbol UND Text,
 *   nicht nur als Farbe (A11y)"*. Ich habe den Text entfernt und die Reihe blieb grün; dann das
 *   Symbol, wieder grün. **Eine ausdrückliche Barrierefreiheits-Entscheidung, durch nichts
 *   geschützt.**
 * - **Die Rückfrage vor dem Entsperren.** Sie verhindert, dass ein gesperrtes Objekt versehentlich
 *   freigegeben wird. Entfernt — grün.
 *
 * **Was diese Datei nicht kann:** sie liest Quelltext, sie rendert nicht. *Eine Quelltext-Zusage
 * über einen Schalter ist schwächer als ein geklickter Schalter* — sie fängt jede der zwölf
 * Mutationen, aber sie beweist nicht, dass der Browser daraus dasselbe macht. Dafür steht L-01.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { PANEL_TABS } from '../app/dashboard/panelTabs';
import { teil, ohneKommentare } from './_zerlegteApp';

const panelRoh = teil('app/rahmen/EigenschaftenPanel.tsx');
const panel = ohneKommentare(panelRoh);
const app = ohneKommentare(teil('app/HausplanerApp.tsx'));

// --- Sicht und Sperre (Dashboard v1 §5) ------------------------------------------------------------

test('K-04 (blind gewesen): der Sicht-Schalter kehrt den Zustand um, den er anzeigt', () => {
  // Mutation: `sichtbar: selectedNode.visible === true`. Nichts wurde rot — der Knopf hätte
  // „Ausblenden" gesagt und ausgeblendet gelassen.
  assert.match(panel, /sichtbar: selectedNode\.visible === false/,
    'der Sicht-Befehl setzt nicht den umgekehrten Zustand');
  // Und die Beschriftung gehört zum Zustand, nicht umgekehrt.
  assert.match(panel, /selectedNode\.visible === false \? '🙈 Ausgeblendet' : '👁 Sicht'/,
    'die Beschriftung des Sicht-Knopfes ist vertauscht');
});

test('K-04 (blind gewesen): der Sperr-Schalter kehrt um UND zeigt seinen Zustand', () => {
  assert.match(panel, /gesperrt: !selectedNode\.locked/, 'der Sperr-Befehl setzt nicht den umgekehrten Zustand');
  assert.match(panel, /style=\{knopf\(selectedNode\.locked === true\)\}/,
    'der Sperr-Knopf zeigt seinen aktiven Zustand nicht mehr an');
  assert.match(panel, /selectedNode\.locked \? '🔒 Gesperrt' : '🔓 Sperren'/,
    'die Beschriftung des Sperr-Knopfes ist vertauscht');
});

test('K-04 (blind gewesen): vor dem Entsperren wird gefragt', () => {
  // Die Rückfrage ist der einzige Schutz davor, eine bewusste Sperre versehentlich aufzuheben.
  // Entfernt kam die Mutation durch — der Schutz war nirgends festgehalten.
  assert.match(panel, /if \(selectedNode\.locked && !window\.confirm\(/,
    'ein gesperrtes Objekt lässt sich jetzt ohne Rückfrage freigeben');
  // Der Gegenpart: beim SPERREN wird NICHT gefragt — das wäre lästig und war nie so.
  const zeile = panel.split('\n').find((z) => z.includes('SET_NODES_GESPERRT')) ?? '';
  assert.ok(zeile.includes('selectedNode.locked &&'),
    'die Rückfrage hängt nicht mehr am gesperrten Zustand — dann fragt sie auch beim Sperren');
});

// --- Die L/T/U-Anbaumaße (Verdrahtung #1) ---------------------------------------------------------

test('K-04 (blind gewesen): jede Anbau-Eingabe schreibt in IHR eigenes Feld', () => {
  // Mutation: `setzeAnbau('length', …)` im Breite-Feld. Nichts wurde rot — man tippt die Länge
  // und die Breite ändert sich.
  const felder: ReadonlyArray<[string, string]> = [
    ['length', "value={a?.length ?? ''}"],
    ['width', "value={a?.width ?? ''}"],
  ];
  for (const [feld, anzeige] of felder) {
    const i = panel.indexOf(anzeige);
    assert.ok(i > 0, `das Eingabefeld für \`${feld}\` wurde nicht gefunden — die Zusage misst Leere`);
    const zeile = panel.slice(i, panel.indexOf('\n', i));
    assert.ok(zeile.includes(`setzeAnbau('${feld}'`),
      `das Feld, das \`${feld}\` ANZEIGT, schreibt in ein anderes: ${zeile.trim().slice(0, 110)}`);
  }
});

test('K-04 (blind gewesen): die Anbau-Basis übernimmt die vorhandenen Werte', () => {
  // Mutation: `length: 0, width: 0`. Nichts wurde rot — jede Eingabe hätte die drei anderen
  // Maße auf null gesetzt.
  assert.match(panel, /length: a\?\.length \?\? 0, width: a\?\.width \?\? 0, lengthB: a\?\.lengthB, widthB: a\?\.widthB/,
    'die Anbau-Basis vergisst vorhandene Maße — eine Eingabe löscht die anderen');
});

// --- Die Prüfungen: Schwere als Symbol UND Text ---------------------------------------------------

test('K-04 (blind gewesen): die Schwere steht als SYMBOL und als TEXT — beides, nicht eins', () => {
  // **Der schwerste der zwölf.** Der Kommentar im Code nennt die Regel („A11y"), aber nichts hat
  // sie gehalten: ich habe erst den Text entfernt, dann das Symbol — beide Male grün.
  // Wer nur Farbe und Symbol hat, bekommt vom Vorleseprogramm nichts; wer nur Text hat, verliert
  // die schnelle Erkennbarkeit. Die Regel verlangt beides.
  const i = panelRoh.indexOf('befunde.map(');
  assert.ok(i > 0, 'die Befundliste wurde nicht gefunden — die Zusage misst Leere');
  const eintrag = panelRoh.slice(i, panelRoh.indexOf('</ul>', i));
  assert.match(eintrag, /aria-hidden[^>]*>✋</, 'das Schwere-SYMBOL fehlt');
  assert.match(eintrag, /<strong[^>]*>Abgelehnt<\/strong>/, 'der Schwere-TEXT fehlt');
  // Und das Symbol bleibt für Vorleseprogramme verborgen — sonst liest es „Hand" vor.
  assert.match(eintrag, /<span aria-hidden/, 'das Symbol ist nicht mehr `aria-hidden` — es wird vorgelesen');
});

// --- Die Reiter -----------------------------------------------------------------------------------

test('K-04 (blind gewesen): der Prüfungs-Reiter zeigt die Prüfungen', () => {
  // Mutation: `aktiverTab === 'allgemein' ?`. Nichts wurde rot — der Prüfungs-Reiter hätte den
  // Allgemein-Inhalt gezeigt und umgekehrt.
  assert.match(panel, /aktiverTab === 'pruefungen' \?/, 'der Prüfungs-Zweig hängt an einem fremden Reiter');
  assert.match(panel, /aktiverTab !== 'allgemein' \?/, 'der Platzhalter-Zweig hängt an einem fremden Reiter');
});

test('K-04 (blind gewesen): jeder Reiter zeigt SEINEN Zustand — kein fester Wert', () => {
  // Mutation: `zustand={'fertig'}`. Nichts wurde rot — jeder unfertige Reiter hätte sich als
  // fertig ausgegeben. Genau die Sorte Anzeige, die AUF-44 aus dem Katalog entfernt hat.
  assert.match(panel, /zustand=\{PANEL_TABS\.find\(\(t\) => t\.id === aktiverTab\)\?\.zustand \?\? 'in_entwicklung'\}/,
    'der Reiter-Zustand kommt nicht mehr aus den Daten');
  // presence-Partner nach R2: die Daten tragen wirklich unterschiedliche Zustände.
  const zustaende = new Set(PANEL_TABS.map((t) => t.zustand));
  assert.ok(zustaende.size > 1, `alle Reiter haben denselben Zustand (${[...zustaende]}) — die Zusage misst Leere`);
});

// --- K-01 / K-02 der Zerlegung --------------------------------------------------------------------

test('K-02: das Panel hält keinen Zustand — der aktive Reiter bleibt in der Hauptfunktion', () => {
  for (const muster of [/usePlannerUiStore/, /useState/, /localStorage/]) {
    assert.doesNotMatch(panel, muster, `das Panel hält Zustand: ${muster}`);
  }
  assert.match(app, /const \[aktiverTab, setAktiverTab\] = useState<PanelTabId>/,
    'der aktive Reiter wird nicht mehr in der Hauptfunktion gehalten');
});

test('K-01: das Panel trägt 67 der 133 Inline-Stellen — unverändert', () => {
  // **Die Zahl, die diese Scheibe für AUF-38 wichtig macht.** Sie darf durch die Zerlegung weder
  // sinken (dann wurde aufgeräumt, und das ist ein anderer Auftrag) noch steigen.
  const zeilen = panelRoh.split('\n').filter((z) => z.includes('style={{')).length;
  assert.equal(zeilen, 67, `${zeilen} Inline-Zeilen im Panel statt 67`);
});

test('K-01: das Markup steht NICHT mehr ein zweites Mal in der Hauptfunktion', () => {
  for (const marke of ['SET_NODES_GESPERRT', 'Anbau / Verschneidung', 'role="tabpanel" id={PANEL_ID}']) {
    assert.ok(panel.includes(marke), `\`${marke}\` steht nicht im Panel`);
    assert.ok(!app.includes(marke), `\`${marke}\` steht noch ein zweites Mal in der Hauptfunktion`);
  }
  const rufe = [...app.matchAll(/<EigenschaftenPanel\b/g)];
  assert.equal(rufe.length, 1, `${rufe.length} Aufrufe des Panels — erwartet genau einer`);
});
