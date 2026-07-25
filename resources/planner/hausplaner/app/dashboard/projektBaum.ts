/**
 * Dashboard v2.3 (§32 / UI-8) — Projektbrowser als DATEN.
 *
 * Reine Funktion, kein React, kein Store-Zugriff: Der Baum ist damit prüfbar (siehe
 * `__tests__/projektBaum.test.ts`) statt in JSX verstreut. Das ist dasselbe Muster wie
 * `panelTabs.ts` aus Batch 1 — Fläche als Daten, JSX bleibt dünn.
 *
 * EINE Wahrheit: die Gruppierung liest ausschließlich die vorhandenen Knotentypen aus
 * `domain/scene.types.ts`. Es entsteht kein zweiter Bauteilbegriff und keine zweite Zählung.
 *
 * Leere Gruppen werden WEGGELASSEN — ein leerer Kasten „Zonen (0)" behauptet eine Fläche, die
 * nichts trägt. Ist gar nichts da, meldet der Aufrufer `PROJEKTBAUM_LEER` mit dem Zustand
 * `voraussetzung`: es fehlt eine Voraussetzung (Bauteile), keine Funktion.
 *
 * Kante 6 (Auftrag §6): ab `GRUPPEN_GRENZE` Einträgen wird eine Gruppe nicht ungebremst
 * gerendert. Sie liefert dann `eingeklappt: true`, ihre echte `anzahl` und eine LEERE
 * `eintraege`-Liste. Virtuelles Scrollen ist v6 und wird hier nicht vorweggenommen.
 */
import type { Level, ObjectNode, RoofNode, SceneNode, ZoneNode } from '../../domain/scene.types';

/** Ab dieser Einträge-Zahl klappt eine Gruppe zu (Kante 6). */
export const GRUPPEN_GRENZE = 200;

/** Leerzustand des Browsers — wörtlich, mit `ZustandBadge zustand="voraussetzung"`. */
export const PROJEKTBAUM_LEER = 'Noch keine Bauteile in diesem Geschoss.';

export interface BaumEintrag {
  /** Knoten-id — Klick ruft damit das vorhandene `selectNodes([id])`. */
  id: string;
  label: string;
  /** Konkreter Knotentyp ('wall' · 'window' · 'door' · 'opening' · 'roof' · 'stair' · 'object' · 'zone'). */
  typ: string;
}

export interface BaumGruppe {
  gruppe: string;
  /** Sichtbare Einträge. Leer, wenn `eingeklappt` (Kante 6) — die Zahl steht in `anzahl`. */
  eintraege: BaumEintrag[];
  /** Tatsächliche Anzahl der Bauteile dieser Gruppe, unabhängig von `eingeklappt`. */
  anzahl: number;
  eingeklappt: boolean;
}

/** Anzeigereihenfolge der Gruppen — fest, Abnahmekriterium Batch 2, Punkt 8. */
export const GRUPPEN_REIHENFOLGE = ['Wände', 'Öffnungen', 'Dächer', 'Treppen', 'Objekte', 'Zonen'] as const;

/** Basis-Beschriftung je Knotentyp, wenn der Knoten keinen eigenen Namen trägt. */
const OEFFNUNG_LABEL: Record<string, string> = { window: 'Fenster', door: 'Tür', opening: 'Öffnung' };

function istObjekt(n: SceneNode): n is ObjectNode {
  return n.type === 'object';
}
function istZone(n: SceneNode): n is ZoneNode {
  return n.type === 'zone';
}

/** Name des Knotens, sonst „<Basis> <laufende Nummer>". Kein Blindtext, keine leere Zeile. */
function beschriftung(name: string | undefined, basis: string, nr: number): string {
  const eigen = (name ?? '').trim();
  return eigen.length > 0 ? eigen : `${basis} ${nr}`;
}

/**
 * Baut den Projektbaum für EIN Geschoss.
 *
 * @param nodes  Knoten der Szene (dürfen alle Geschosse enthalten — es wird nach `level` gefiltert).
 * @param roofs  Dächer der Szene (`scene.roofs`), ebenfalls nach `level` gefiltert.
 * @param level  aktives Geschoss; `null` ⇒ leerer Baum (Kante 1: kein Wurf, nur Leerzustand).
 *
 * Nicht enthalten: `RouteNode` (Leitungen). Für sie gibt es heute kein Werkzeug und keine der
 * sechs vom Design (§32) festgelegten Gruppen — eine siebte Gruppe zu erfinden wäre Umfang, den
 * dieser Auftrag nicht deckt. Vermerkt statt still gebaut.
 */
export function projektBaum(
  nodes: readonly SceneNode[],
  roofs: readonly RoofNode[] | undefined,
  level: Pick<Level, 'id'> | null,
): BaumGruppe[] {
  if (!level) return [];

  const eigene = nodes.filter((n) => n.levelId === level.id);
  const eigeneDaecher = (roofs ?? []).filter((r) => r.levelId === level.id);

  const waende: BaumEintrag[] = [];
  const oeffnungen: BaumEintrag[] = [];
  const daecher: BaumEintrag[] = [];
  const treppen: BaumEintrag[] = [];
  const objekte: BaumEintrag[] = [];
  const zonen: BaumEintrag[] = [];

  for (const n of eigene) {
    if (n.type === 'wall') {
      waende.push({ id: n.id, label: beschriftung(n.name, 'Wand', waende.length + 1), typ: n.type });
    } else if (n.type === 'window' || n.type === 'door' || n.type === 'opening') {
      const basis = OEFFNUNG_LABEL[n.type] ?? 'Öffnung';
      oeffnungen.push({ id: n.id, label: beschriftung(n.name, basis, oeffnungen.length + 1), typ: n.type });
    } else if (istObjekt(n) && n.objectType === 'stair') {
      // Treppen laufen fachlich über ObjectNode (objectType 'stair') — eigene Gruppe, wie im Design.
      treppen.push({ id: n.id, label: beschriftung(n.name, 'Treppe', treppen.length + 1), typ: 'stair' });
    } else if (istObjekt(n)) {
      objekte.push({ id: n.id, label: beschriftung(n.name, 'Objekt', objekte.length + 1), typ: n.type });
    } else if (istZone(n)) {
      const basis = n.zoneType === 'room' ? 'Raum' : 'Zone';
      zonen.push({ id: n.id, label: beschriftung(n.name, basis, zonen.length + 1), typ: n.type });
    }
  }
  for (const r of eigeneDaecher) {
    daecher.push({ id: r.id, label: beschriftung(r.name, 'Dach', daecher.length + 1), typ: 'roof' });
  }

  const roh: Array<{ gruppe: string; eintraege: BaumEintrag[] }> = [
    { gruppe: 'Wände', eintraege: waende },
    { gruppe: 'Öffnungen', eintraege: oeffnungen },
    { gruppe: 'Dächer', eintraege: daecher },
    { gruppe: 'Treppen', eintraege: treppen },
    { gruppe: 'Objekte', eintraege: objekte },
    { gruppe: 'Zonen', eintraege: zonen },
  ];

  return roh
    .filter((g) => g.eintraege.length > 0)
    .map((g) => {
      const eingeklappt = g.eintraege.length >= GRUPPEN_GRENZE;
      return {
        gruppe: g.gruppe,
        eintraege: eingeklappt ? [] : g.eintraege,
        anzahl: g.eintraege.length,
        eingeklappt,
      };
    });
}
