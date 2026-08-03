/**
 * Z-06-N1 (B10) — **Herkunft und Freigabe überleben das Speichern, und die Sperre greift.**
 *
 * ---
 *
 * **Die Mutationsprobe VOR diesen Zusagen — 9 Mutationen, 9 blind.**
 *
 * ```text
 * M1  Felder `.optional()` gemacht                         BLIND
 * M2  schemaVersion nicht angehoben (bei 2 gelassen)       BLIND
 * M3  Migration setzt Bestand auf 'bestaetigt'             BLIND
 * M4  geometrieHerkunft beim Anlegen fest 'manuell'        BLIND
 * M5  freigabe beim Anlegen fest 'bestaetigt'              BLIND
 * M6  istFreigegeben() gibt immer true                     BLIND
 * M7  Ablehnung ohne Grund (leere Meldung)                 BLIND
 * M8  Dach setzt die Felder nicht                          BLIND
 * M9  verlangeFreigabe() gibt still zurueck statt zu werfen BLIND
 * ```
 *
 * **Neun von neun, und das ist keine Nachlässigkeit der 1649 bestehenden Zusagen** — vor dieser
 * Scheibe gab es die Felder nicht, also konnte keine von ihnen etwas darüber sagen. *Die Zahl
 * misst hier den Ausgangszustand, nicht eine Lücke.*
 *
 * **B3 — gemessen wird an der Entscheidungsfunktion, nicht am Schirm.** `istFreigegeben` und
 * `verlangeFreigabe` entscheiden; ein Renderer, der die Farbe ändert, führt nur aus. Eine Probe
 * über die Oberfläche wäre grün, sobald jemand die Kennzeichnung einbaut — auch wenn die Sperre
 * gar nicht greift.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { migriereSzene, sceneDocumentSchema } from '../domain/validation';
import { readFileSync } from 'node:fs';
import { FreigabeFehlt, HERKUNFT_NEUES_DACH, herkunftFuerNeueDecke, istFreigegeben, setzeFreigabe, verlangeFreigabe } from '../geometry/freigabe';
import type { Freigabe } from '../geometry/freigabe';

const ISO = '2026-08-03T00:00:00.000Z';
const UMRISS = [{ x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 6000 }, { x: 0, y: 6000 }];

function szene(over: Record<string, unknown> = {}): Record<string, unknown> {
  return {
    id: 'd', projectId: 1, schemaVersion: 3, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'l1', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [], materials: [], roofs: [], ceilings: [],
    metadata: { createdAt: ISO, updatedAt: ISO },
    ...over,
  };
}

function decke(over: Record<string, unknown> = {}): Record<string, unknown> {
  return {
    id: 'c1', type: 'ceiling', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO, polygon: UMRISS, dickeMm: 200,
    geometrieHerkunft: 'abgeleitet', freigabe: 'vorschlag', ...over,
  };
}

function dach(over: Record<string, unknown> = {}): Record<string, unknown> {
  return {
    id: 'r1', type: 'roof', levelId: 'l1', visible: true, locked: false, tags: [],
    createdAt: ISO, updatedAt: ISO, polygon: UMRISS,
    roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 0, ueberstandMm: 500, traufhoeheMm: 2500,
    geometrieHerkunft: 'abgeleitet', freigabe: 'vorschlag', ...over,
  };
}

/** Der Weg, den eine Datei wirklich nimmt: schreiben → Text → lesen → prüfen. */
function durchDieAblage(doc: Record<string, unknown>): Record<string, any> {
  const wieder = JSON.parse(JSON.stringify(doc));
  const geparst = sceneDocumentSchema.safeParse(migriereSzene(wieder));
  assert.equal(geparst.success, true, `Dokument kam nicht durch das Schema: ${JSON.stringify((geparst as any).error?.issues?.slice(0, 2))}`);
  return (geparst as any).data;
}

// ─────────────────────────────────────────────────────────── K-05: das Überleben

test('K-05: eine abgeleitete Decke behält Herkunft UND Freigabe über Speichern und Laden', () => {
  const d = durchDieAblage(szene({ ceilings: [decke()] }));
  assert.equal(d.ceilings[0].geometrieHerkunft, 'abgeleitet');
  assert.equal(d.ceilings[0].freigabe, 'vorschlag');
});

test('K-05: eine manuelle, bestätigte Decke ebenso — der andere Zweig, nicht nur der eine', () => {
  const d = durchDieAblage(szene({ ceilings: [decke({ geometrieHerkunft: 'manuell', freigabe: 'bestaetigt' })] }));
  assert.equal(d.ceilings[0].geometrieHerkunft, 'manuell');
  assert.equal(d.ceilings[0].freigabe, 'bestaetigt');
});

test('K-05: das DACH läuft denselben Weg — B10 nennt den Folgeprozess ausdrücklich', () => {
  const d = durchDieAblage(szene({ roofs: [dach({ geometrieHerkunft: 'geschaetzt', freigabe: 'zu_pruefen' })] }));
  assert.equal(d.roofs[0].geometrieHerkunft, 'geschaetzt');
  assert.equal(d.roofs[0].freigabe, 'zu_pruefen');
});

test('K-05 ROT: ein v3-Dokument OHNE die Felder wird ABGELEHNT — sie sind Pflicht, nicht Zierrat', () => {
  // **Die eigentliche Zusage dieser Scheibe.** Sie fällt, sobald jemand die Felder `.optional()`
  // macht, um sich die Migration zu sparen — und genau dann verliert Bestandsgeometrie ihren
  // Status wieder, ohne dass es auffällt.
  const ohne = decke();
  delete ohne.geometrieHerkunft;
  delete ohne.freigabe;
  const roh = szene({ ceilings: [ohne] });

  assert.equal(sceneDocumentSchema.safeParse(roh).success, false,
    'ein v3-Dokument ohne die Felder wurde angenommen — die Felder sind optional geworden');
});

test('K-05: dasselbe Dokument als v2 wird MIGRIERT statt abgelehnt', () => {
  const ohne = decke();
  delete ohne.geometrieHerkunft;
  delete ohne.freigabe;
  const d = durchDieAblage(szene({ schemaVersion: 2, ceilings: [ohne] }));

  assert.equal(d.schemaVersion, 3);
  assert.equal(d.ceilings[0].freigabe, 'zu_pruefen');
  assert.equal(d.ceilings[0].geometrieHerkunft, 'abgeleitet');
});

// ─────────────────────────────────────────────────────────── K-07: die Sperre

test('K-07: nur `bestaetigt` gibt frei — die anderen drei nicht', () => {
  assert.equal(istFreigegeben({ freigabe: 'bestaetigt' }), true);
  for (const f of ['vorschlag', 'zu_pruefen', 'abgelehnt'] as Freigabe[]) {
    assert.equal(istFreigegeben({ freigabe: f }), false, `${f} hat freigegeben`);
  }
});

test('K-07: verlangeFreigabe reicht bestätigte Geometrie durch', () => {
  const knoten = { freigabe: 'bestaetigt' as Freigabe, geometrieHerkunft: 'manuell' as const, id: 'c1', type: 'ceiling' };
  assert.equal(verlangeFreigabe(knoten), knoten);
});

test('K-07 ROT: die drei anderen werfen — und die Meldung NENNT den Grund', () => {
  // Der Grund ist Teil der Zusage, nicht Zierrat: eine Ablehnung ohne ihn schickt den nächsten
  // Leser suchen. Dieselbe Auflage wie bei der Erlaubnisliste (W-01) und beim Commit-Tor.
  const erwartet: Record<string, RegExp> = {
    vorschlag: /Vorschlag/i,
    zu_pruefen: /Bestand|geprüft/i,
    abgelehnt: /abgelehnt/i,
  };

  for (const [f, muster] of Object.entries(erwartet)) {
    const knoten = { freigabe: f as Freigabe, geometrieHerkunft: 'abgeleitet' as const, id: 'c1', type: 'ceiling' };
    assert.throws(
      () => verlangeFreigabe(knoten, 'die Dachauflage'),
      (e: unknown) => {
        assert.ok(e instanceof FreigabeFehlt, `${f}: falscher Fehlertyp`);
        assert.match(e.message, muster, `${f}: die Meldung nennt den Grund nicht`);
        assert.match(e.message, /die Dachauflage/, `${f}: die Meldung nennt den Zweck nicht`);
        assert.match(e.message, /c1/, `${f}: die Meldung nennt den Knoten nicht`);
        return true;
      },
    );
  }
});

test('K-07: ein unbekannter fünfter Wert gilt als NICHT freigegeben', () => {
  // `istFreigegeben` fragt auf `=== bestaetigt` statt auf `!== abgelehnt`. Käme morgen ein Wert
  // dazu, wäre er mit der Negativ-Form stillschweigend freigegeben — der teuerste Weg, eine
  // Aufzählung zu erweitern.
  assert.equal(istFreigegeben({ freigabe: 'in_pruefung' as unknown as Freigabe }), false);
});

// ─────────────────────────────────────────────────────────── K-08: der Zeitstempel

test('K-08: ein WECHSEL setzt Zeitpunkt und Urheber', () => {
  const vorher = { freigabe: 'vorschlag' as Freigabe, geometrieHerkunft: 'abgeleitet' as const, id: 'c1' };
  const nachher = setzeFreigabe(vorher, 'bestaetigt', 'yama', '2026-08-03T19:40:00.000Z');

  assert.equal(nachher.freigabe, 'bestaetigt');
  assert.equal(nachher.freigabeAm, '2026-08-03T19:40:00.000Z');
  assert.equal(nachher.freigabeVon, 'yama');
  assert.equal((vorher as Record<string, unknown>).freigabeAm, undefined, 'der Eingabewert wurde verändert');
});

test('K-08 ROT: OHNE Wechsel bleibt der alte Stempel stehen — sonst wird er zur Speicher-Uhr', () => {
  // **Die scharfe Zusage.** Wer bei jedem Speichern stempelt, macht die Zeile wertlos, die
  // Vertrauen stiften soll: „bestätigt am …" sagt dann nichts mehr darüber, wann jemand hinsah.
  const alt = {
    freigabe: 'bestaetigt' as Freigabe, geometrieHerkunft: 'manuell' as const, id: 'c1',
    freigabeAm: '2026-08-01T08:00:00.000Z', freigabeVon: 'pruefer',
  };
  const gleich = setzeFreigabe(alt, 'bestaetigt', 'jemand-anders', '2026-08-03T19:40:00.000Z');

  assert.equal(gleich.freigabeAm, '2026-08-01T08:00:00.000Z');
  assert.equal(gleich.freigabeVon, 'pruefer');
  assert.equal(gleich, alt, 'ohne Wechsel darf nicht einmal ein neues Objekt entstehen');
});

// ─────────────────────────────────────────────── K-05/K-08: die Setzstellen beim Anlegen
//
// **Diese vier Zusagen entstanden aus der Mutationsprobe, nicht aus dem Blatt.** M4, M5 und M8
// kamen durch: „Herkunft fest auf manuell", „Freigabe fest auf bestaetigt", „Dach setzt die
// Felder nicht". *Alle drei saßen in `HausplanerApp.tsx` — und eine Entscheidung in einem
// Klick-Handler ist nur über einen Klick prüfbar.* Sie wohnt jetzt in der Domäne.

test('K-05 Setzstelle: aus einer KONTUR wird die Decke manuell und bestätigt', () => {
  assert.deepEqual(herkunftFuerNeueDecke(true), { geometrieHerkunft: 'manuell', freigabe: 'bestaetigt' });
});

test('K-05 Setzstelle ROT: aus dem UMRISS wird sie abgeleitet und bleibt Vorschlag', () => {
  // Die scharfe Hälfte. Wer hier `bestaetigt` einsetzt, macht aus jeder Bounding-Box eine
  // geprüfte Decke — bei L-, T- und U-Form ist sie messbar falsch (Z-06), sähe aber bestätigt aus.
  assert.deepEqual(herkunftFuerNeueDecke(false), { geometrieHerkunft: 'abgeleitet', freigabe: 'vorschlag' });
});

test('K-05 Setzstelle: die beiden Zweige sind VERSCHIEDEN — sonst entscheidet die Bedingung nichts', () => {
  assert.notDeepEqual(herkunftFuerNeueDecke(true), herkunftFuerNeueDecke(false));
});

test('K-05 Setzstelle: das neue Dach ist abgeleitet und Vorschlag', () => {
  assert.deepEqual(HERKUNFT_NEUES_DACH, { geometrieHerkunft: 'abgeleitet', freigabe: 'vorschlag' });
  assert.equal(istFreigegeben(HERKUNFT_NEUES_DACH as never), false);
});

test('K-05 Verdrahtung: die App RUFT die Entscheidung, statt sie erneut zu treffen', () => {
  // Ohne diese Zusage darf jemand die Literale zurück in den Klick-Handler schreiben — die vier
  // Zusagen oben blieben grün, und die Mutationen kämen wieder durch. *Eine ausgelagerte
  // Entscheidung ist nur so viel wert wie die Zusage, dass sie auch benutzt wird.*
  const quelle = readFileSync(new URL('../app/HausplanerApp.tsx', import.meta.url), 'utf8');
  const code = quelle.split('\n').filter((z) => !/^\s*(\/\/|\*|\/\*)/.test(z)).join('\n');

  assert.match(code, /herkunftFuerNeueDecke\(ausKontur\)/, 'die Decke entscheidet wieder selbst');
  assert.match(code, /\.\.\.HERKUNFT_NEUES_DACH/, 'das Dach entscheidet wieder selbst');

  // **BEIDE Felder, nicht nur eines.** Die erste Fassung verbot nur ein `geometrieHerkunft`-
  // Literal — der Evaluator hat diff-belegt vorgeführt, dass `freigabe: 'bestaetigt'` HINTER dem
  // Spread bei 1667/0 durchkommt. *Ein Spread lässt sich von rechts überschreiben; wer nur den
  // linken Teil bewacht, bewacht die Form und nicht die Wirkung.* Selbst nachgestellt, bevor
  // diese Zeile stand: 1667 pass / 0 fail mit eingebauter Mutation.
  for (const feld of ['geometrieHerkunft', 'freigabe'] as const) {
    const literal = new RegExp(`${feld}:\\s*'[a-z_]+'`);
    assert.doesNotMatch(code, literal,
      `ein ${feld}-Literal steht wieder direkt im Klick-Handler — der Spread wird überschrieben`);
  }
});
