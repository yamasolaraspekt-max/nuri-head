/**
 * Hausplaner — Projektion Raum → RaumGeometrieProjektion (P0-Fixture, ▲K2).
 *
 * Erzeugt je erkanntem Raum exakt die `raum_geometrien`-Struktur der Heizlast
 * (wberechnung/ticket): polygon (mm), wand_segmente mit grenzflaeche innen/aussen,
 * ABGELEITETEM azimut_grad (nur außen; Nord = +y) und den Öffnungen der Wand.
 * decke/boden: in P0 ehrlich null (kein erfundener bauteil_typ — Operanden-Gate).
 * Der spätere ProjektionsService (P2) ruft genau diese Funktion — der Vertrag ist
 * ab P0 durch das Fixture eingefroren (Abnahmekriterium 9).
 */
import type { OpeningNode, RaumGeometrieProjektion, SceneNode, WallNode } from '../domain/scene.types';
import type { ErkannterRaum } from '../geometry/roomDetection';
import { signierteFlaeche } from '../geometry/roomDetection';
import { azimutDerNormalen } from '../geometry/wallGeometry';

function istOeffnung(n: SceneNode): n is OpeningNode {
  return n.type === 'window' || n.type === 'door' || n.type === 'opening';
}

const OEFFNUNGS_TYP = { window: 'fenster', door: 'tuer', opening: 'oeffnung' } as const;

/**
 * @param raum        erkannter Raum (Umlauf CCW, Fläche > 0)
 * @param alleRaeume  alle Räume des Geschosses (für innen/aussen: teilt sich eine
 *                    Wandkante zwei Räume, ist sie innen)
 * @param nodes       Szene-Nodes des Geschosses (Öffnungen je Wand)
 * @param geschoss    Level-sortOrder
 * @param hoeheMm     Geschoss-Standardhöhe
 */
export function projiziereRaum(
  raum: ErkannterRaum,
  alleRaeume: ErkannterRaum[],
  nodes: SceneNode[],
  geschoss: number,
  hoeheMm: number,
): RaumGeometrieProjektion {
  // Kanten-Nutzung zählen: dieselbe ungerichtete Kante in ZWEI Räumen ⇒ Innenwand.
  const kantenSchluessel = (von: { x: number; y: number }, bis: { x: number; y: number }): string => {
    const [p, q] = [von, bis].sort((l, r) => (l.x - r.x) || (l.y - r.y));

    return `${p.x},${p.y}|${q.x},${q.y}`;
  };
  const nutzung = new Map<string, number>();
  for (const r of alleRaeume) {
    for (const kante of r.kanten) {
      const s = kantenSchluessel(kante.von, kante.bis);
      nutzung.set(s, (nutzung.get(s) ?? 0) + 1);
    }
  }

  // Umlauf ist CCW (Fläche > 0, y nach oben) ⇒ Rauminneres liegt LINKS der Laufrichtung
  // ⇒ die nach AUSSEN zeigende Normale ist die RECHTE.
  const ccw = signierteFlaeche(raum.polygon) > 0;

  const wandVon = new Map<string, WallNode>();
  for (const n of nodes) {
    if (n.type === 'wall') {
      wandVon.set(n.id, n);
    }
  }

  return {
    geschoss,
    polygon: raum.polygon.map((p) => ({ x: p.x, y: p.y })),
    hoehe_mm: hoeheMm,
    wand_segmente: raum.kanten.map((kante) => {
      const aussen = (nutzung.get(kantenSchluessel(kante.von, kante.bis)) ?? 1) === 1;

      const oeffnungen = nodes
        .filter(istOeffnung)
        .filter((o) => {
          if (o.hostWallId !== kante.wallId) {
            return false;
          }
          const mitte = o.offsetFromWallStart + o.width / 2;

          return mitte >= kante.offsetVon && mitte <= kante.offsetBis;
        })
        .map((o) => ({
          typ: OEFFNUNGS_TYP[o.type],
          breite_mm: o.width,
          hoehe_mm: o.height,
          bruestung_mm: o.sillHeight,
        }));

      return {
        von: { x: kante.von.x, y: kante.von.y },
        bis: { x: kante.bis.x, y: kante.bis.y },
        grenzflaeche: aussen ? 'aussen' : 'innen',
        azimut_grad: aussen ? azimutDerNormalen(kante.von, kante.bis, ccw ? 'rechts' : 'links') : null,
        // **Z1-W1-1..5 / K-1 · ehrlicher Ausweis 21.08.:** dieser Ternary liefert heute für JEDE
        // Wand konstant `'wand'` — der Zweig `'aussenwand_gedaemmt'` ist tot. Gemessen über
        // `resources/`: `insulationType` hat **genau vier** Fundstellen, und keine davon schreibt —
        // `domain/scene.types.ts:109` (Typ), `domain/validation.ts:46` (Zod),
        // `domain/scene-document-v2.schema.json:142` (das aus dem Zod erzeugte Server-Schema),
        // und diese Zeile (Lesestelle). Der einzige `construction`-Regler im Panel
        // (`EigenschaftenPanel.tsx:324`) schreibt ausschließlich `materialId`.
        //
        // **Nachbesserung Z1-W1-5-1 (21.08.), und der Grund gehört dazu:** hier stand **drei**,
        // die vierte Fundstelle fehlte. Ein Ausweis, dessen Zweck die Ehrlichkeit über eine
        // Messung ist, lädt mit falscher Zahl dazu ein, sich auf ihn zu verlassen — der Fehler
        // wiegt hier schwerer als anderswo, gerade weil die Sache stimmt. Ausgerechnet das
        // Server-Schema fehlte, also die Stelle, an der das Feld die Insel verlässt.
        //
        // **Damit die Zahl nicht wieder still abläuft, steht die Messvorschrift dabei** statt
        // nur ihr Ergebnis — sie ist jederzeit nachfahrbar und nennt ihren Stand:
        //
        //     grep -rln insulationType resources/ | grep -v __tests__
        //
        // Gemessen 21.08.2026: **4** Dateien (die vier oben). `__tests__` bleibt ausgeschlossen,
        // weil ein Test das Feld benutzen darf, ohne dass es damit verdrahtet wäre — die Frage
        // lautet, wer es im Produktivweg schreibt, und die Antwort ist weiterhin: niemand.
        //
        // **Der Zweig wird trotzdem NICHT entfernt.** Das Feld ist nicht falsch, nur unverdrahtet;
        // ob ein Dämmungs-Regler kommt und was „gedämmt" fachlich für den Bauteiltyp jenseits der
        // PHP-Grenze bedeutet, ist Y-3 und liegt bei Yama. Bis dahin gilt hier dieselbe Haltung wie
        // zwei Zeilen tiefer bei `decke`/`boden`: **ehrlich benannt statt still erfunden.**
        bauteil_typ: wandVon.get(kante.wallId)?.construction?.insulationType ? 'aussenwand_gedaemmt' : 'wand',
        oeffnungen,
      };
    }),
    decke: null, // P0: ehrlich unbestimmt — kein erfundener Aufbau (Operanden-Gate)
    boden: null,
  };
}
