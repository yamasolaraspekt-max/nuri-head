/**
 * Z1-W2-0 — **die Bedienbarkeits-Probe: ein Messgerät, das jedem Werkzeug nachgeht.**
 *
 * ---
 *
 * **Wofür sie da ist.** Jedes Anschlussblatt dieser Welle verlangt eine Browserabnahme, und bisher
 * hieß das: je Blatt ein eigener Handgriff, je Abnahme eine neue Verabredung darüber, was
 * „bedienbar" belegt. *Ein Messgerät, das nur einmal benutzt wird, ist ein Handgriff. Eines, das
 * bei jeder Abnahme läuft, ist eine Zusage.*
 *
 * **Der eigentliche Zweck ist nicht, die heutigen 13 zu prüfen** — es ist Z1-W2-0-f: ein neuer
 * Eintrag in `TOOL_DEFINITIONS` **erzeugt automatisch einen Fall**. Deshalb liest diese Datei die
 * Registry und führt **kein** eigenes Verzeichnis von Werkzeugnamen. Ein hartcodiertes Array
 * würde die Liste messen und nicht die Registry, und ein neuer Eintrag bliebe ungeprüft.
 *
 * ---
 *
 * ## Was hier gemessen wird — und was NICHT
 *
 * **Gemessen wird der BEDIENWEG**, nicht die Zeichenwirkung. Die Kette lautet:
 *
 * ```text
 *   Weg vorhanden  ->  aktivieren  ->  Leiste zeigt aktiv  ->  Wirkung zugesagt  ->  Escape
 * ```
 *
 * ⚠ **Die Grenze, und sie ist keine Bequemlichkeit:** Schritt „eine Aktion → Szene messbar
 * geändert" verlangt einen Klick auf die **Bühne**. Die gibt es in diesem Lauf nicht:
 * `dom-register.mjs` stellt jsdom **ohne Layout** bereit und lässt `getBoundingClientRect`
 * absichtlich werfen; `canvas` ist keine Abhängigkeit des Repos, Konva rendert hier also nicht.
 * *Gemessen, nicht vermutet — `node_modules/canvas` fehlt, und keine der fünf bestehenden
 * DOM-Proben montiert die Hauptansicht.*
 *
 * **Statt eine Bühnenwirkung zu behaupten, prüft diese Datei die ZUSAGE der Wirkung** an der
 * Stelle, an der sie im Bestand steht: `werkzeugVertrag.ts` nennt je Werkzeug seine `commandId`,
 * `werkzeugLandkarte.ts` sagt, ob ein Befehl in `applyCommand.ts` sie heute leistet. Ein Werkzeug
 * mit Marke `fehlt` ist ein benannter Bauvorrat und **kein grüner Fall**.
 * *Die Bühnenwirkung gehört in die Browserabnahme des jeweiligen Werkzeugblatts — dort ist sie
 * herstellbar, hier wäre sie erfunden.*
 *
 * ---
 *
 * ## Die drei Zuschnitte, alle aus der Registry gemessen
 *
 * | | |
 * |---|---|
 * | `art: 'aktion'` (3) | wird **ausgelöst**, nicht aktiviert — bleibt nicht aktiv, `Escape` stellt sie nicht zurück |
 * | ohne `shortcut` (3) | kein Tastenweg — der Weg führt über Leiste bzw. Anheften |
 * | `trimmen` | **beides**: Aktion *und* ohne Kürzel. Die Schnittmenge ist nicht leer. |
 *
 * **Der Weg wird je Eintrag aus dem DATENFELD gewählt**, nicht über eine Sonderbehandlung im
 * Testcode: wer ein `shortcut` trägt, wird über die Taste geprüft; wer keines trägt, über die
 * Leiste; wer in keiner fixen Leiste steht, über das Anheften aus dem Gruppenmenü.
 *
 * ## Der Befehl, mit Ort (Z1-W2-0-h)
 *
 * ```text
 * npm run test:hausplaner:dom
 * ```
 *
 * ⚠ Das Blatt nennt in (h) „Vitest im Repo-Wurzelverzeichnis". **Vitest gibt es in diesem Repo
 * nicht** (`grep -c vitest package.json` → 0, `node_modules/vitest` fehlt). Gemeldet in
 * `generator-messbefehl-defekt-Z1-W2-0-h.yaml`; hier steht der Befehl, der wirklich läuft.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import type { ToolDefinition } from '../app/tools/toolTypes';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { tastenAbsicht } from '../app/tastenAbsicht';
import { zoneTools } from '../app/tools/toolPresentation';
import { WERKZEUG_GRUPPEN } from '../app/dashboard/werkzeugGruppen';
import { leisteMitAngehefteten } from '../app/ableitungen';
import { vertrag } from '../app/tools/werkzeugVertrag';
import { WERKZEUG_LANDKARTE } from '../app/tools/werkzeugLandkarte';
import { usePlannerUiStore } from '../app/state/uiState';

/** Wie ein Eintrag aktiviert werden kann. `keiner` ist das Rot, das dieses Blatt fangen soll. */
type Weg = 'taste' | 'leiste' | 'anheften' | 'keiner';

interface Befund {
  id: string;
  art: string;
  weg: Weg;
  /** Führt der Weg wirklich zu DIESEM Werkzeug? Bei `taste`: liefert die Taste seine id? */
  wegTraegt: boolean;
  /** Setzt der UI-Zustand das Werkzeug als aktiv? Bei Aktionen: entfällt (siehe `ausnahmen`). */
  aktivierbar: boolean;
  /** Zusage der Wirkung: Vertrag vorhanden UND Landkarte nicht `fehlt`. */
  wirkungZugesagt: boolean;
  /** Stellt Escape auf `auswahl` zurück? Bei Aktionen: entfällt. */
  escapeStelltZurueck: boolean;
  /** Teilzusagen, die für diesen Eintrag fachlich nicht gelten — je einzeln begründet. */
  ausnahmen: string[];
}

/**
 * **Das Kürzel-Datenfeld in ein Tastenereignis übersetzen.**
 *
 * `shortcut` trägt im Bestand drei Formen: einen Buchstaben (`'V'`), einen Sondertastennamen
 * (`'Delete'`) und eine Modifikator-Schreibweise (`'Ctrl+D'`). *Wer das Feld prüfen will, muss es
 * lesen* — meine erste Fassung reichte den ganzen String als `key` durch, und `duplizieren` fiel
 * dadurch als „ohne Bedienweg" auf, obwohl es einen hat. **Die Probe hat den Fehler selbst
 * gefangen; ohne die Zerlegung hätte sie einen Mangel gemeldet, den es nicht gibt.**
 */
function tastenEreignisAus(kuerzel: string): Parameters<typeof tastenAbsicht>[0] {
  const teile = kuerzel.split('+');
  const taste = teile[teile.length - 1]!;
  const hat = (m: string): boolean => teile.slice(0, -1).some((x) => x.toLowerCase() === m);
  return {
    // Einzelne Buchstaben kommen vom Browser klein, solange Shift nicht gedrückt ist.
    key: taste.length === 1 && !hat('shift') ? taste.toLowerCase() : taste,
    ctrlKey: hat('ctrl'), metaKey: hat('cmd') || hat('meta'),
    shiftKey: hat('shift'), altKey: hat('alt'),
  } as Parameters<typeof tastenAbsicht>[0];
}

/** Die Standard-Rückfallmarke des UI-Zustands. Aus dem Bestand gelesen, nicht gesetzt. */
const RUECKFALL = 'auswahl';

/**
 * **Der Kern — und er nimmt seine Einträge als PARAMETER.**
 *
 * Genau dieselbe Bauform wie `zoneToolsIn` in `toolPresentation.ts`, die dort ausdrücklich „für
 * Gegenproben in Tests" existiert. Dadurch laufen die Rot-Probe (e) und die Wachstums-Probe (f)
 * gegen eine **erweiterte Liste**, ohne `toolRegistry.ts` anzufassen: der Schutzbeleg ist ein
 * leerer Diff, und ein Wegwerf-Dateibaum unter TMPDIR wird gar nicht erst gebraucht.
 */
export function bedienwege(defs: readonly ToolDefinition[]): Befund[] {
  const fixeLeiste = new Set(zoneTools('fix').map((t) => t.id));
  const imGruppenmenue = new Set(WERKZEUG_GRUPPEN.flatMap((g) => g.werkzeuge.map((t) => t.id)));

  return defs.map((t) => {
    const istAktion = t.art === 'aktion';

    // --- 1. Welcher Weg? Die Wahl trifft das DATENFELD, nicht eine Sonderbehandlung ------------
    const weg: Weg = t.shortcut ? 'taste'
      : fixeLeiste.has(t.id) ? 'leiste'
        : imGruppenmenue.has(t.id) ? 'anheften'
          : 'keiner';

    // --- 2. Trägt der Weg zu DIESEM Werkzeug? -------------------------------------------------
    let wegTraegt = false;
    if (weg === 'taste') {
      const absicht = tastenAbsicht(tastenEreignisAus(t.shortcut!));
      // Werkzeuge kommen als Absicht 'werkzeug' mit ihrer id zurück. Aktionen haben eigene
      // Absichtsarten (Löschen, Duplizieren) — dort genügt, dass die Taste ÜBERHAUPT etwas auslöst.
      wegTraegt = istAktion ? Boolean(absicht) && absicht.art !== 'nichts'
        : absicht?.werkzeugId === t.id;
    } else if (weg === 'leiste') {
      wegTraegt = fixeLeiste.has(t.id);
    } else if (weg === 'anheften') {
      // Angeheftetes wandert über `leisteMitAngehefteten` wirklich in die Leiste — nachgefahren,
      // nicht angenommen. Ohne diesen Schritt wäre 'anheften' ein Weg, der im Menü endet.
      wegTraegt = leisteMitAngehefteten(new Set([t.id])).some((x) => x.id === t.id);
    }

    // --- 3. Nimmt der UI-Zustand es als aktiv an? ----------------------------------------------
    let aktivierbar = false;
    let escapeStelltZurueck = false;
    const vorher = usePlannerUiStore.getState().activeToolId;
    try {
      if (!istAktion) {
        usePlannerUiStore.getState().setActiveTool(t.id);
        aktivierbar = usePlannerUiStore.getState().activeToolId === t.id;
        // --- 5. Escape: zurück auf den Rückfall ------------------------------------------------
        usePlannerUiStore.getState().setActiveTool(RUECKFALL);
        escapeStelltZurueck = usePlannerUiStore.getState().activeToolId === RUECKFALL;
      }
    } finally {
      usePlannerUiStore.getState().setActiveTool(vorher);
    }

    // --- 4. Ist eine Wirkung zugesagt? ---------------------------------------------------------
    const v = vertrag(t.id);
    const marke = WERKZEUG_LANDKARTE.find((e) => e.werkzeugId === t.id)?.marke;
    const wirkungZugesagt = Boolean(v?.commandId) && marke !== undefined && marke !== 'fehlt';

    const ausnahmen: string[] = [];
    if (istAktion) {
      ausnahmen.push(
        `${t.id}: art 'aktion' — wird ausgeloest, nicht aktiviert. Die Teilzusagen `
        + `"bleibt aktiv" und "Escape stellt zurueck" gelten fachlich nicht.`,
      );
    }
    if (marke === 'fehlt') {
      ausnahmen.push(
        `${t.id}: Landkarte 'fehlt' — es gibt heute keinen Modellbefehl dafuer. `
        + `Benannter Bauvorrat, KEIN gruener Fall.`,
      );
    }

    return { id: t.id, art: t.art, weg, wegTraegt, aktivierbar, wirkungZugesagt, escapeStelltZurueck, ausnahmen };
  });
}

/** Ein Fall ist grün, wenn jede Teilzusage gilt, die für ihn überhaupt gilt. */
function gruen(b: Befund): boolean {
  const istAktion = b.art === 'aktion';
  return b.weg !== 'keiner' && b.wegTraegt && b.wirkungZugesagt
    && (istAktion || (b.aktivierbar && b.escapeStelltZurueck));
}

// ── Z1-W2-0-a · über die Registry, nicht über eine Liste ───────────────────────────────────────
test('Z1-W2-0-a: die Faelle entstehen aus TOOL_DEFINITIONS, nicht aus einem Handverzeichnis', () => {
  const befunde = bedienwege(TOOL_DEFINITIONS);
  assert.equal(befunde.length, TOOL_DEFINITIONS.length,
    'die Fallzahl folgt der Registry — sonst misst die Probe ihre eigene Liste');
  // Die Reihenfolge ist die der Registry: ein Beleg, dass nichts umsortiert oder gefiltert wurde.
  assert.deepEqual(befunde.map((b) => b.id), TOOL_DEFINITIONS.map((t) => t.id));
});

// ── Z1-W2-0-d · alle sind erfasst, Ausnahmen einzeln benannt ───────────────────────────────────
test('Z1-W2-0-d: ALLE Eintraege sind erfasst — gruen oder je begruendet ausgenommen', () => {
  const befunde = bedienwege(TOOL_DEFINITIONS);
  const ohneWeg = befunde.filter((b) => b.weg === 'keiner' || !b.wegTraegt);
  const bauvorrat = befunde.filter((b) => b.ausnahmen.some((a) => a.includes("'fehlt'")));
  const gruene = befunde.filter(gruen);

  console.log(`\n  ${gruene.length} von ${befunde.length} Eintraegen sind bedienbar belegt.`);
  for (const b of befunde) {
    console.log(`    ${gruen(b) ? '✔' : '✖'} ${b.id.padEnd(15)} ${b.art.padEnd(8)} weg=${b.weg.padEnd(9)}`
      + `traegt=${String(b.wegTraegt).padEnd(5)} aktiv=${String(b.aktivierbar).padEnd(5)}`
      + `wirkung=${String(b.wirkungZugesagt).padEnd(5)} escape=${b.escapeStelltZurueck}`);
  }
  for (const b of befunde) for (const a of b.ausnahmen) console.log(`    — Ausnahme: ${a}`);

  assert.equal(ohneWeg.length, 0,
    `ohne tragenden Bedienweg: ${ohneWeg.map((b) => `${b.id} (${b.weg})`).join(', ')}`);
  assert.equal(gruene.length + bauvorrat.length, befunde.length,
    'jeder Eintrag ist entweder gruen oder als Bauvorrat benannt — eine stille Auslassung gibt es nicht');
});

// ── Z1-W2-0-b · die Kette, je Werkzeug ausgeloest ──────────────────────────────────────────────
test('Z1-W2-0-b: je Werkzeug — aktivieren, aktiv, Wirkung zugesagt, Escape stellt zurueck', () => {
  for (const b of bedienwege(TOOL_DEFINITIONS).filter((x) => x.art === 'werkzeug')) {
    assert.ok(b.wegTraegt, `${b.id}: der Weg '${b.weg}' fuehrt nicht zu diesem Werkzeug`);
    assert.ok(b.aktivierbar, `${b.id}: wird nach dem Aktivieren nicht als aktiv gefuehrt`);
    assert.ok(b.escapeStelltZurueck, `${b.id}: Escape stellt nicht auf '${RUECKFALL}' zurueck`);
    if (!b.ausnahmen.some((a) => a.includes("'fehlt'"))) {
      assert.ok(b.wirkungZugesagt, `${b.id}: keine zugesagte Wirkung (Vertrag/Landkarte)`);
    }
  }
});

// ── Z1-W2-0-c · aktivieren geht auch ohne Kuerzel ──────────────────────────────────────────────
test('Z1-W2-0-c: die kuerzellosen Eintraege haben einen Weg — ueber die Leiste, nicht uebersprungen', () => {
  const ohneKuerzel = bedienwege(TOOL_DEFINITIONS.filter((t) => !t.shortcut));
  assert.ok(ohneKuerzel.length >= 3, `nur ${ohneKuerzel.length} kuerzellose Eintraege — der Zuschnitt hat sich geaendert`);
  for (const b of ohneKuerzel) {
    assert.notEqual(b.weg, 'taste', `${b.id} hat kein Kuerzel, wurde aber ueber die Taste geprueft`);
    assert.ok(b.wegTraegt, `${b.id}: kein tragender Weg ohne Kuerzel (weg=${b.weg})`);
  }
  console.log(`\n  kuerzellos: ${ohneKuerzel.map((b) => `${b.id} -> ${b.weg}`).join(' · ')}`);
});

// ── Z1-W2-0-e · die Rot-Probe: ein Eintrag OHNE Bedienweg faellt durch ─────────────────────────
test('Z1-W2-0-e: ein erfundener Eintrag ohne Bedienweg macht die Probe rot — ausgeloest, nicht behauptet', () => {
  const ohneWeg: ToolDefinition = {
    id: 'zz-probe-ohne-bedienweg', label: 'Probe ohne Bedienweg', icon: 'fehler',
    art: 'werkzeug', groupId: 'global', supportedWorkspaces: [], supportedViews: [],
    helpText: 'Erfunden fuer die Rot-Probe. Kein Kuerzel, keine Leiste, kein Gruppenmenue.',
  };
  const befunde = bedienwege([...TOOL_DEFINITIONS, ohneWeg]);
  const fall = befunde.find((b) => b.id === ohneWeg.id);

  assert.ok(fall, 'der erfundene Eintrag hat keinen Fall erzeugt — dann waechst die Probe nicht mit');
  assert.equal(fall.weg, 'keiner', 'der Eintrag haette keinen Weg haben duerfen');
  assert.equal(gruen(fall), false, 'ein Eintrag ohne Bedienweg gilt als gruen — die Probe faengt nichts');

  // Und die Zusage aus (d) faellt an ihm — mit seiner id in der Meldung.
  const fehler = befunde.filter((b) => b.weg === 'keiner' || !b.wegTraegt);
  assert.equal(fehler.length, 1);
  assert.equal(fehler[0]!.id, ohneWeg.id);

  // GEGENPROBE, ohne die (e) nur zeigt, dass irgendetwas rot ist: dieselbe Messung ohne den
  // Eintrag ist grün. Sonst wäre nicht belegt, dass DER EINTRAG den Ausschlag gab.
  assert.equal(bedienwege(TOOL_DEFINITIONS).filter((b) => b.weg === 'keiner' || !b.wegTraegt).length, 0);
});

// ── Z1-W2-0-f · ein neues Werkzeug wird am Tag seiner Eintragung erfasst ───────────────────────
test('Z1-W2-0-f: ein gueltiger neuer Eintrag erzeugt automatisch einen Fall — und er ist gruen', () => {
  // Ein Werkzeug MIT Bedienweg: es leiht sich das Kürzel eines bestehenden nicht, sondern
  // nimmt einen freien Buchstaben — sonst prüfte die Probe eine Kürzel-Kollision statt Wachstum.
  const belegt = new Set(TOOL_DEFINITIONS.map((t) => (t.shortcut ?? '').toUpperCase()));
  const frei = [...'QXYZJ'].find((c) => !belegt.has(c));
  assert.ok(frei, 'kein freier Buchstabe fuer die Probe — der Zuschnitt hat sich geaendert');

  const neu: ToolDefinition = {
    id: 'zz-probe-mit-bedienweg', label: 'Probe mit Bedienweg', icon: 'pruefen',
    art: 'werkzeug', groupId: 'global', supportedWorkspaces: [], supportedViews: [],
    shortcut: frei, helpText: 'Erfunden fuer die Wachstums-Probe.',
  };
  const befunde = bedienwege([...TOOL_DEFINITIONS, neu]);

  assert.equal(befunde.length, TOOL_DEFINITIONS.length + 1,
    `Fallzahl ${befunde.length} statt ${TOOL_DEFINITIONS.length + 1} — die Probe waechst nicht mit der Registry`);
  const fall = befunde.find((b) => b.id === neu.id)!;
  assert.equal(fall.weg, 'taste', 'ein Eintrag mit Kuerzel wird ueber die Taste geprueft');
  assert.ok(fall.aktivierbar, 'der neue Eintrag laesst sich nicht aktivieren');
  assert.ok(fall.escapeStelltZurueck, 'Escape stellt nach dem neuen Eintrag nicht zurueck');
  console.log(`\n  Wachstums-Probe: ${TOOL_DEFINITIONS.length} -> ${befunde.length} Faelle, `
    + `neuer Eintrag ueber Kuerzel '${frei}' aktivierbar.`);
});

// ── Der Schutzbeleg zu (g): diese Datei fasst die Registry nicht an ────────────────────────────
test('Z1-W2-0-g: die Registry bleibt unveraendert — die Proben laufen ueber Parameter', () => {
  const vorher = TOOL_DEFINITIONS.length;
  bedienwege([...TOOL_DEFINITIONS, {
    id: 'zz-fluechtig', label: 'x', icon: 'x', art: 'werkzeug', groupId: 'global',
    supportedWorkspaces: [], supportedViews: [], helpText: 'x',
  }]);
  assert.equal(TOOL_DEFINITIONS.length, vorher,
    'die Registry hat sich waehrend der Probe geaendert — dann ist sie kein fester Bezug mehr');
  assert.equal(usePlannerUiStore.getState().activeToolId, RUECKFALL,
    'der UI-Zustand wurde nicht zurueckgestellt — die Probe hinterlaesst Spuren');
});
