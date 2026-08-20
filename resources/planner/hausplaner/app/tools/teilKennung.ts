/**
 * AUF-35b — **die Teil-Identität: eine Wandseite, eine Dachfläche.**
 *
 * **Der Befund:** Auswahl ist heute knotenweise. Der 3D-Renderer hängt an jedes Mesh
 * `userData.nodeId`, der Store hält `selectedNodeIds` — **es gibt keine Bezeichnung für einen Teil
 * eines Knotens.** Eine Wand trägt `start`, `end`, `thickness`, `height`; ihre **zwei Seiten sind
 * implizit** und existieren nirgends als Daten. `surfaceId` gibt es, aber **nicht im Schema**
 * (`domain/validation.ts` 0 Treffer, `scene-document-v2.schema.json` 0 Treffer).
 * *(PRAEZISIERT 20.08.: hier stand "nur innerhalb von `geometry/dachAusschnitt.ts`". Das trifft
 * nicht mehr — vergeben werden die Kennungen in `renderers/three-d/dachMesh.ts:196/243ff` als
 * `${roof.id}#N`, und die Funktion `dachflaechen` wird produktiv aus `szene.ts:522` gerufen.
 * Der tragende Halbsatz bleibt richtig und ist der Punkt: sie sind ABGELEITET, nicht gespeichert.)*
 *
 * **Diese Datei ist Anzeige-Zustand, kein Modell.**
 *
 * Die Teil-Kennung wird **abgeleitet**, nicht gespeichert: kein Schema, kein Command, kein Undo,
 * keine Persistenz. Sie überlebt kein Neuladen — **und das ist richtig so**, genau wie die heutige
 * Auswahl. Eine neue persistierte Struktur an Bestandsdaten wäre ein Datenvorgang, kein
 * Auswahlwerkzeug (Dauerdirektive).
 *
 * **Rein und ohne DOM.** Dieselbe Eingabe ergibt dasselbe Ergebnis; die Zuordnung der Seiten ist
 * geometrisch bestimmt und damit über Neuladen hinweg stabil.
 *
 * **Warum links/rechts und nicht innen/außen:** eine Wand kennt ihre Innenseite nicht von allein —
 * dafür braucht es den Raumbezug. Die Seiten heißen deshalb **geometrisch**, nach der Achsrichtung.
 * *Eine fachliche Benennung ohne Raumbezug wäre geraten, nicht bestimmt* (im Bericht zurückgegeben).
 */
import type { WallNode, RoofNode } from '../../domain/scene.types';

/** Das Trennzeichen zwischen Knoten und Teil. Ein Zeichen, das in keiner `id` vorkommt. */
export const TEIL_TRENNER = '#';

export type TeilArt = 'seite' | 'flaeche';

/** Links oder rechts der Achsrichtung `start → end`. Geometrisch, nicht fachlich. */
export type WandSeite = 'links' | 'rechts';

export interface Teil {
  /** `"<nodeId>#seite:links"` · `"<nodeId>#flaeche:2"` */
  teilId: string;
  nodeId: string;
  art: TeilArt;
  /** `links`/`rechts` bei einer Wandseite, der Index als Text bei einer Dachfläche. */
  wert: string;
}

/** Baut die Kennung. **Eine Stelle** — sonst entstünden zwei Schreibweisen für dieselbe Sache. */
export function baueTeilId(nodeId: string, art: TeilArt, wert: string): string {
  return `${nodeId}${TEIL_TRENNER}${art}:${wert}`;
}

/**
 * Zerlegt eine Kennung. Gibt `null` zurück, wenn es keine Teil-Kennung ist — **eine reine
 * Knoten-id ist kein Fehler**, sie ist der Normalfall.
 */
export function zerlegeTeilId(id: string): Teil | null {
  const trenner = id.indexOf(TEIL_TRENNER);
  if (trenner <= 0) return null;
  const nodeId = id.slice(0, trenner);
  const rest = id.slice(trenner + 1);
  const doppel = rest.indexOf(':');
  if (doppel <= 0) return null;
  const art = rest.slice(0, doppel);
  const wert = rest.slice(doppel + 1);
  if ((art !== 'seite' && art !== 'flaeche') || wert === '') return null;
  return { teilId: id, nodeId, art, wert };
}

/** Die Knoten-id einer Auswahl-Kennung — ob Teil oder nicht. */
export function knotenVon(id: string): string {
  return zerlegeTeilId(id)?.nodeId ?? id;
}

/**
 * Die zwei Seiten einer Wand.
 *
 * **Keine neue Geometrie-Rechnung:** die Achse steht im Knoten, mehr braucht die Zuordnung nicht.
 * Eine Wand ohne Länge hat keine Seiten — bei ihr wäre „links" eine Behauptung ohne Richtung.
 */
export function wandSeiten(wand: WallNode): Teil[] {
  const dx = wand.end.x - wand.start.x;
  const dy = wand.end.y - wand.start.y;
  if (dx === 0 && dy === 0) {
    return [];
  }
  return (['links', 'rechts'] as WandSeite[]).map((seite) => ({
    teilId: baueTeilId(wand.id, 'seite', seite),
    nodeId: wand.id,
    art: 'seite' as const,
    wert: seite,
  }));
}

/**
 * Auf welcher Seite der Wandachse liegt ein Punkt?
 *
 * **Das Kreuzprodukt der Achsrichtung mit dem Zeigervektor.** Positiv heißt links im
 * mathematischen Sinn (Nord = +y, wie im ganzen Modell). **Genau auf der Achse gibt es keine
 * Seite** — dort wird `null` geliefert statt einer geratenen.
 */
export function seiteVonPunkt(wand: WallNode, punkt: { x: number; y: number }): WandSeite | null {
  const dx = wand.end.x - wand.start.x;
  const dy = wand.end.y - wand.start.y;
  const kreuz = dx * (punkt.y - wand.start.y) - dy * (punkt.x - wand.start.x);
  if (kreuz === 0) return null;
  return kreuz > 0 ? 'links' : 'rechts';
}

/**
 * Die Flächen eines Daches — **gelesen, nicht nachgebaut.**
 *
 * Die Anzahl kommt aus dem Knoten selbst (`flaechen`, wenn vorhanden). **Was `geometry/` schon
 * kann, wird nicht ein zweites Mal gerechnet**; fehlt die Angabe, hat das Dach hier keine
 * benennbaren Teilflächen — und dann wird auch keine erfunden.
 */
export function dachFlaechen(dach: RoofNode & { flaechen?: readonly unknown[] }): Teil[] {
  const anzahl = Array.isArray(dach.flaechen) ? dach.flaechen.length : 0;
  return Array.from({ length: anzahl }, (_, i) => ({
    teilId: baueTeilId(dach.id, 'flaeche', String(i)),
    nodeId: dach.id,
    art: 'flaeche' as const,
    wert: String(i),
  }));
}

/**
 * Der Teil im **Klartext** — „Wand 3 · Seite links", nicht `wall-7#seite:links`.
 *
 * *Eine Oberfläche, die einen Schlüssel anzeigt, hat den Nutzer zum Datenbankleser gemacht.*
 */
export function teilKlartext(teil: Teil, knotenName: string): string {
  if (teil.art === 'seite') {
    return `${knotenName} · Seite ${teil.wert}`;
  }
  return `${knotenName} · Fläche ${Number(teil.wert) + 1}`;
}
