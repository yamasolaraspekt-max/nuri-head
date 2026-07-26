/**
 * AUF-78 — **die zuletzt bearbeiteten Projekte, gelesen statt erfunden.**
 *
 * Bis AUF-40 Teil A zeigte der Startbildschirm drei erfundene Namen; seither zeigt er einen
 * ehrlichen Leerzustand. **Jetzt kommt die echte Liste** — über dieselbe Naht wie `data-rechte`
 * und `data-speichern-url`: das Blade setzt, `main.tsx` liest, der UI-Zustand hält.
 *
 * **Die Liste ist fertig, wenn sie hier ankommt.** Der Controller hat gefiltert, begrenzt und auf
 * vier Felder reduziert; diese Datei rechnet nichts und ergänzt nichts. Sie prüft nur, ob das,
 * was ankommt, die erwartete Form hat.
 *
 * **Alles Unerwartete ergibt die leere Liste — nie eine halb gefüllte.** Dieselbe Richtung wie bei
 * den Rechten (AUF-60): ein fehlender oder kaputter Wert darf nie mehr behaupten als ein
 * vorhandener. Eine Liste, die aus unlesbaren Daten drei Einträge macht, wäre wieder eine
 * Erfindung — und genau die hat AUF-40 Teil A entfernt.
 */

/** Das Attribut am Mount-Knoten (`data-projekte`) — dieselbe Naht wie `data-rechte`. */
export const PROJEKTE_ATTRIBUT = 'projekte';

/** Ein Projekt, so wie der Startbildschirm es anzeigt: Bezeichnung, Ort, Datum. Mehr nicht. */
export interface ProjektEintrag {
  id: number;
  name: string;
  ort: string;
  datum: string;
}

function istEintrag(x: unknown): x is ProjektEintrag {
  if (typeof x !== 'object' || x === null) return false;
  const o = x as Record<string, unknown>;
  return typeof o.id === 'number' && typeof o.name === 'string'
    && typeof o.ort === 'string' && typeof o.datum === 'string';
}

/**
 * Die Projekte aus dem Attribut.
 *
 * **Fehlt es, ist es leer oder unlesbar, gilt die leere Liste.** Kein Wurf: ein Startbildschirm,
 * der wegen eines kaputten Attributs gar nicht erscheint, wäre schlimmer als einer ohne Liste.
 *
 * **Ein einziger unpassender Eintrag verwirft alles.** Nicht „die guten behalten": eine Liste, von
 * der man nicht weiß, was fehlt, ist schlechter als eine, die sichtbar leer ist.
 */
export function leseProjekte(roh: string | null | undefined): ProjektEintrag[] {
  if (!roh) {
    return [];
  }
  let geparst: unknown;
  try {
    geparst = JSON.parse(roh);
  } catch {
    return [];
  }
  if (!Array.isArray(geparst) || !geparst.every(istEintrag)) {
    return [];
  }
  return geparst;
}
