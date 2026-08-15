/**
 * A-35 — **Trimmen: die Rollen, die sechs Kanten und der eine Undo-Schritt.**
 *
 * ---
 *
 * **Was hier rot werden kann.** Die tragende Aussage des Werkzeugs ist die **Rollenzuweisung**:
 * das ZULETZT angeklickte Objekt wird gekürzt, alle vorher gewählten sind Schnittkanten. *Wäre sie
 * falsch herum, kürzte das Werkzeug die Schnittkante statt des Ziels* — und das sähe im Bild
 * zunächst genauso plausibel aus. **Deshalb prüft `T-01` sie mit DREI Objekten in bekannter
 * Reihenfolge**, nicht mit zweien: bei zwei Objekten wäre „das letzte" und „das andere" dasselbe,
 * und die Zusage bewiese nichts.
 *
 * **Die Kanten werden über die PARAMETER geprüft, nicht über die Koordinate** — `0 ≤ t ≤ 1` und
 * `0 ≤ u ≤ 1`. *`T-07` fährt genau den Fall, an dem sich die beiden Lesarten unterscheiden.*
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { useHausplanerStore } from '../store/hausplanerStore';
import { befehleTrimmen, TRIMM_MELDUNG } from '../app/tools/trimmen';
import type { SceneDocument, SceneNode, WallNode } from '../domain/scene.types';

const JETZT = '2026-08-15T09:00:00.000Z';

function wand(
  id: string,
  start: { x: number; y: number },
  ende: { x: number; y: number },
  extra: Partial<WallNode> = {},
): WallNode {
  return {
    id, type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    start, end: ende, thickness: 240, height: 2500,
    ...extra,
  } as WallNode;
}

function szene(nodes: SceneNode[]): SceneDocument {
  return {
    id: 'a35', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes, roofs: [], materials: [], metadata: { createdAt: JETZT, updatedAt: JETZT },
  } as unknown as SceneDocument;
}

const store = useHausplanerStore;

/** Wie in `sammelBefehle.test.ts`: erst die Historie leeren, dann `init` — der Store hält sie als Modul-Singleton. */
function frischerStand(scene: SceneDocument): void {
  while (store.getState().kannUndo()) {
    store.getState().undo();
  }
  store.getState().init(scene, '', '');
}

function wandAus(id: string): WallNode {
  const n = store.getState().scene?.nodes.find((x) => x.id === id);
  assert.ok(n && n.type === 'wall', `Wand ${id} fehlt`);
  return n as WallNode;
}

/**
 * **T-01 (A-35-3) — die Hauptrolle ist `primaerId`, nicht `ids[0]`.**
 *
 * Auswahlreihenfolge **A → B → C**: `C` muss gekürzt werden, `A` und `B` sind Schnittkanten.
 * *Läse das Werkzeug `ids[0]`, würde `A` gekürzt — und `A` bliebe hier unverändert, was die Zusage
 * ebenfalls fängt.*
 *
 * **Die Lage ist ABSICHTLICH unsymmetrisch** (2000 und 9000, nicht 2000 und 8000): *bei Symmetrie
 * greift die Mehrdeutigkeits-Wache aus `T-10`, und diese Zusage würde am falschen Grund rot.*
 */
test('T-01: gekürzt wird das ZULETZT gewählte Objekt, nicht das erste (A7-Rollen)', () => {
  const a = wand('A', { x: 2000, y: -1000 }, { x: 2000, y: 1000 });   // senkrecht bei x=2000
  const b = wand('B', { x: 9000, y: -1000 }, { x: 9000, y: 1000 });   // senkrecht bei x=9000
  const c = wand('C', { x: 0, y: 0 }, { x: 10000, y: 0 });            // waagerecht, das Ziel

  const { befehle, grund } = befehleTrimmen({ ids: ['A', 'B', 'C'], primaerId: 'C' }, [a, b, c]);

  assert.equal(grund, null, 'der Vorgang hätte laufen müssen');
  assert.equal(befehle.length, 1, 'ein Trimmvorgang ist EIN Befehl');
  const befehl = befehle[0] as { type: string; nodeId: string };
  assert.equal(befehl.type, 'MOVE_NODE');
  assert.equal(befehl.nodeId, 'C', 'gekürzt wurde nicht das zuletzt gewählte Objekt');
});

/**
 * **T-02 — die kleinste Kürzung gewinnt, und sie hängt NICHT an der Auswahlreihenfolge.**
 *
 * Dieselbe Lage wie `T-01`: die Kante bei `x = 8000` nimmt 2000 mm weg, die bei `x = 2000` nähme
 * 2000 mm vom anderen Ende — gleich viel. *Deshalb steht hier eine unsymmetrische Lage:* `x = 9000`
 * nimmt 1000 mm, `x = 2000` nähme 2000 mm. **Die Zusage wird zweimal gefahren, mit vertauschter
 * Reihenfolge** — käme ein anderes Ergebnis heraus, entschiede die Liste und nicht die Geometrie.
 */
test('T-02 (K6): von mehreren Schnittkanten gewinnt die mit der kleinsten Kürzung — reihenfolgeunabhängig', () => {
  const nah = wand('NAH', { x: 9000, y: -1000 }, { x: 9000, y: 1000 });
  const fern = wand('FERN', { x: 2000, y: -1000 }, { x: 2000, y: 1000 });
  const ziel = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });

  for (const ids of [['NAH', 'FERN', 'Z'], ['FERN', 'NAH', 'Z']]) {
    const { befehle, grund } = befehleTrimmen({ ids, primaerId: 'Z' }, [nah, fern, ziel]);
    assert.equal(grund, null, `Reihenfolge ${ids.join('→')}: abgewiesen statt getrimmt`);
    const p = (befehle[0] as { position: { start: { x: number }; end: { x: number } } }).position;
    assert.deepEqual(
      { s: p.start.x, e: p.end.x },
      { s: 0, e: 9000 },
      `Reihenfolge ${ids.join('→')}: nicht an der nächstgelegenen Kante geschnitten`,
    );
  }
});

/**
 * **T-03 (K1) — eine Auswahl ohne zweite Wand wird ABGEWIESEN, nicht stillschweigend ignoriert.**
 *
 * *Ein Werkzeug, das nichts tut und nichts sagt, sieht aus wie ein defektes Werkzeug.*
 */
test('T-03 (K1): nur EIN Objekt gewählt ⇒ abgewiesen mit Grund', () => {
  const z = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });
  const { befehle, grund } = befehleTrimmen({ ids: ['Z'], primaerId: 'Z' }, [z]);

  assert.equal(grund, 'keine_schnittkante');
  assert.equal(befehle.length, 0);
  assert.match(TRIMM_MELDUNG.keine_schnittkante, /Schnittkante/);
});

/** **T-04 (K2) — parallele Wände schneiden sich nirgends; keine erfundene Ecke, kein Absturz.** */
test('T-04 (K2): parallele Wände ⇒ abgewiesen mit Grund', () => {
  const k = wand('K', { x: 0, y: 3000 }, { x: 10000, y: 3000 });
  const z = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });
  const { befehle, grund } = befehleTrimmen({ ids: ['K', 'Z'], primaerId: 'Z' }, [k, z]);

  assert.equal(grund, 'parallel');
  assert.equal(befehle.length, 0);
});

/**
 * **T-05 (K4) — das Ziel darf in der Auswahl stehen und ist NICHT seine eigene Schnittkante.**
 *
 * `auswahlModus.ts` Fall `add` erlaubt genau das. *Zählte das Werkzeug sich selbst mit, käme es auf
 * `geradenSchnittParameter(z, z)` — deckungsgleich, also `null`; der Fall liefe still ins Leere.*
 */
test('T-05 (K4): primaerId steht auch in ids ⇒ definiert behandelt, nicht selbst geschnitten', () => {
  const k = wand('K', { x: 9000, y: -1000 }, { x: 9000, y: 1000 });
  const z = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });
  const { befehle, grund } = befehleTrimmen({ ids: ['K', 'Z'], primaerId: 'Z' }, [k, z]);

  assert.equal(grund, null, 'die eigene Anwesenheit in ids darf den Vorgang nicht verhindern');
  assert.equal((befehle[0] as { nodeId: string }).nodeId, 'Z');
});

/** **T-06 (K5) — eine gesperrte Wand wird nicht gekürzt; als Schnittkante darf sie dienen.** */
test('T-06 (K5): gesperrtes Ziel ⇒ abgewiesen; gesperrte Schnittkante ⇒ erlaubt', () => {
  const kanteGesperrt = wand('K', { x: 9000, y: -1000 }, { x: 9000, y: 1000 }, { locked: true });
  const zielGesperrt = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 }, { locked: true });
  const zielFrei = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });

  const a = befehleTrimmen({ ids: ['K', 'Z'], primaerId: 'Z' }, [kanteGesperrt, zielGesperrt]);
  assert.equal(a.grund, 'ziel_gesperrt', 'eine gesperrte Wand darf nicht verändert werden');
  assert.equal(a.befehle.length, 0);

  // Die Kante wird nur GELESEN — sperren schützt vor Veränderung, nicht vor Benutztwerden.
  const b = befehleTrimmen({ ids: ['K', 'Z'], primaerId: 'Z' }, [kanteGesperrt, zielFrei]);
  assert.equal(b.grund, null, 'eine gesperrte Schnittkante wird nicht verändert und darf dienen');
});

/**
 * **T-07 (K3, A-35-9) — der Fall, den K2s Wache DURCHLÄSST.**
 *
 * `EPS_SINUS` wirkt auf den **Sinus**, der Schaden ist eine **Abstands**größe. Bei 0,001° Winkel
 * zwischen zwei 6000-mm-Wänden liegt der Schnittpunkt rund **286 m** entfernt — `geradenSchnitt`
 * liefert ihn, die Parallel-Wache greift nicht.
 *
 * > **Ohne die Streckenprüfung würde hier lautlos eine 6-Meter-Wand auf Hunderte Meter verlängert**
 * > — mathematisch korrekt, baulich Unsinn, ohne jede Meldung.
 */
test('T-07 (K3/A-35-9): Schnittpunkt weit außerhalb ⇒ abgewiesen, NICHT verlängert', () => {
  const ziel = wand('Z', { x: 0, y: 0 }, { x: 6000, y: 0 });
  // 0,001 Grad Neigung, um 5 mm versetzt: der Schnittpunkt liegt bei rund 286 m.
  const dy = Math.tan((0.001 * Math.PI) / 180) * 6000;
  const kante = wand('K', { x: 0, y: 5 }, { x: 6000, y: 5 + dy });

  const { befehle, grund } = befehleTrimmen({ ids: ['K', 'Z'], primaerId: 'Z' }, [kante, ziel]);

  assert.equal(grund, 'ausserhalb', 'der ferne Schnittpunkt hätte abgewiesen werden müssen');
  assert.equal(befehle.length, 0, 'es darf NICHTS bewegt worden sein');

  // Und die Gegenprobe, dass die Lage wirklich die beschriebene ist: die Geraden schneiden sich,
  // nur eben weit hinter beiden Enden. Sonst prüfte T-07 den Parallel-Fall und nicht K3.
  assert.equal(
    befehleTrimmen({ ids: ['K', 'Z'], primaerId: 'Z' }, [kante, ziel]).grund !== 'parallel',
    true,
    'die Wache hat doch gegriffen — dann misst diese Zusage den falschen Fall',
  );
});

/**
 * **T-08 (K3-Präzisierung) — `t = 1 + 1e-9` wird abgewiesen.**
 *
 * *Das ist der Fall, an dem die beiden Lesarten von „liegt auf der Strecke" gegenteilig antworten:*
 * **über den Parameter** weist er ab, **über die Koordinate** liefe er durch (der Punkt liegt
 * 6 Nanometer hinter dem Ende, jedes Abstands-Epsilon ließe ihn passieren). **Diese Zusage hält
 * fest, dass Weg A gebaut ist.**
 */
test('T-08 (K3-Präzisierung): knapp hinter dem Ende (t = 1 + 1e-9) ⇒ abgewiesen', () => {
  const ziel = wand('Z', { x: 0, y: 0 }, { x: 6000, y: 0 });
  const x = 6000 * (1 + 1e-9); // 6000,000006 mm — sechs Nanometer hinter dem Ende
  const kante = wand('K', { x, y: -1000 }, { x, y: 1000 });

  const { grund } = befehleTrimmen({ ids: ['K', 'Z'], primaerId: 'Z' }, [kante, ziel]);
  assert.equal(grund, 'ausserhalb', 'über die Koordinate gerechnet wäre dieser Fall durchgelaufen');
});

/**
 * **T-09 — `u` wird geprüft, nicht nur `t`.**
 *
 * Der Schnittpunkt liegt **mitten auf dem Ziel**, aber **hinter dem Ende der Schnittkante**. *Wer
 * nur `t` prüft, trimmt hier — obwohl sich die beiden Wände nirgends berühren.* **Genau dafür wurde
 * `u` überhaupt gerechnet** (es gab es vor A-35 nicht).
 */
test('T-09: Schnittpunkt auf dem Ziel, aber hinter dem Ende der Schnittkante ⇒ abgewiesen', () => {
  const ziel = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });
  const kante = wand('K', { x: 5000, y: 2000 }, { x: 5000, y: 4000 }); // endet 2 m ÜBER dem Ziel

  const { grund } = befehleTrimmen({ ids: ['K', 'Z'], primaerId: 'Z' }, [kante, ziel]);
  assert.equal(grund, 'ausserhalb', 'ohne die u-Prüfung wäre hier getrimmt worden');
});

/** **T-10 (K6, Gleichstand) — zwei Kanten, gleich viel Verlust, verschiedene Stellen ⇒ abgewiesen.** */
test('T-10 (K6): symmetrische Schnittkanten ⇒ mehrdeutig, kein Raten', () => {
  const links = wand('L', { x: 2000, y: -1000 }, { x: 2000, y: 1000 });
  const rechts = wand('R', { x: 8000, y: -1000 }, { x: 8000, y: 1000 });
  const ziel = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });

  const { befehle, grund } = befehleTrimmen({ ids: ['L', 'R', 'Z'], primaerId: 'Z' }, [links, rechts, ziel]);
  assert.equal(grund, 'mehrdeutig');
  assert.equal(befehle.length, 0);
});

/** **T-11 — ohne Ziel (nichts zuletzt geklickt) und mit einem Nicht-Wand-Ziel wird abgewiesen.** */
test('T-11: kein Ziel oder kein Wand-Ziel ⇒ abgewiesen mit Grund', () => {
  const z = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });
  assert.equal(befehleTrimmen({ ids: ['Z'], primaerId: null }, [z]).grund, 'kein_ziel');
  assert.equal(befehleTrimmen({ ids: ['Z'], primaerId: 'GIBTESNICHT' }, [z]).grund, 'kein_ziel');
});

/**
 * **T-12 (A-35-4) — EIN Trimmvorgang ist EIN Undo-Schritt.**
 *
 * *Gemessen am echten Store und am Zustand nach genau einem `undo`, nicht an der Zahl der Befehle.*
 */
test('T-12 (A-35-4): ein Trimmvorgang, ein Undo — der Ausgangszustand ist wieder da', () => {
  const kante = wand('K', { x: 9000, y: -1000 }, { x: 9000, y: 1000 });
  const ziel = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });
  frischerStand(szene([kante, ziel]));

  const { befehle, grund } = befehleTrimmen(
    { ids: ['K', 'Z'], primaerId: 'Z' },
    store.getState().scene?.nodes ?? [],
  );
  assert.equal(grund, null);
  assert.equal(store.getState().executeCommands(befehle), true, 'der Reducer hat den Befehl abgelehnt');

  assert.equal(wandAus('Z').end.x, 9000, 'die Wand wurde nicht gekürzt');

  store.getState().undo();
  assert.equal(wandAus('Z').end.x, 10000, 'EIN Undo hat den Ausgangszustand nicht wiederhergestellt');
  assert.equal(store.getState().kannUndo(), false, 'es lag mehr als EIN Historien-Eintrag vor');
});

/**
 * **T-13 — der Schnittpunkt wird auf ganze Millimeter gerundet.**
 *
 * *Der Reducer nimmt nur ganze Millimeter (`pruefeGanzzahlig`); ein ungerundeter Schnittpunkt würde
 * den ganzen Vorgang ablehnen.* **Die Zusage fährt eine schräge Kante, deren Schnittpunkt keine
 * ganze Zahl ist.**
 */
test('T-13: krummer Schnittpunkt wird gerundet und vom Reducer angenommen', () => {
  const ziel = wand('Z', { x: 0, y: 0 }, { x: 10000, y: 0 });
  const kante = wand('K', { x: 9000, y: -1000 }, { x: 9001, y: 2000 }); // trifft bei x = 9000,333…
  frischerStand(szene([kante, ziel]));

  const { befehle, grund } = befehleTrimmen(
    { ids: ['K', 'Z'], primaerId: 'Z' },
    store.getState().scene?.nodes ?? [],
  );
  assert.equal(grund, null);
  const p = (befehle[0] as { position: { end: { x: number; y: number } } }).position;
  assert.equal(Number.isInteger(p.end.x) && Number.isInteger(p.end.y), true, 'nicht ganzzahlig');
  assert.equal(store.getState().executeCommands(befehle), true, 'der Reducer hat abgelehnt');
  assert.equal(wandAus('Z').end.x, 9000);
});
