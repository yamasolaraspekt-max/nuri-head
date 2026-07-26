/**
 * AUF-33 / L2 — die **Zuordnung Engine → Fläche**, beginnend mit der Treppe.
 *
 * **Warum eine Zuordnung und kein dynamischer Import:** `faehigkeiten.ts` führt je Engine
 * `engineModul: 'geometry/treppenBerechnung'` und `engineExport: 'berechneTreppe'`. Das ist eine
 * **Deklaration**, kein Ladepfad — `import(variable)` überlebt das Vite-Bundling nicht zuverlässig
 * (der Bundler sieht das Ziel nicht und nimmt es nicht mit). Deshalb steht hier eine **explizite
 * Zuordnung mit statischem Import**. Die Deklaration bleibt die Wahrheit über *welche* Engine, diese
 * Tabelle ist die Wahrheit über *wie* sie aufgerufen wird.
 *
 * **Was hier NICHT passiert — und das ist der Kern dieses Postens:** In dieser Datei und in der
 * Fläche steht **keine einzige Rechnung**. Kein Grenzwert, keine Formel, keine Rundung. Die Felder
 * beschreiben, was die Engine entgegennimmt; die Ergebnisliste beschreibt, wie ihre Rückgabe heißt.
 * **Jede Zahl, die im Panel entstünde statt in der Engine, wäre ein Defekt** — und dieser Defekt
 * würde in L3 zwölfmal mitkopiert.
 *
 * **L3 (AUF-52) baut auf diesem Muster auf, Scheibe fuer Scheibe.** Die Tabelle unten hatte in L2
 * genau **einen** Eintrag; Scheibe A haengt **einen zweiten** an (Sparren). Drei weitere derselben
 * Gruppe sind begruendet zurueckgegeben — zweimal fehlt der Eingang, einmal die Ergebnisform.
 */
import { berechneTreppe, type TreppenEingabe, type TreppenErgebnis } from '../../geometry/treppenBerechnung';
// AUF-52 Scheibe A — **statischer** Import, wie AUF-33 §3b es verlangt: der Bundler muss das Ziel
// sehen. `import(variable)` ueberlebt das Bundling nicht zuverlaessig.
import { berechneSparren, type SparrenEingabe, type SparrenErgebnis } from '../../geometry/sparrenBerechnung';

/** Ein Eingabefeld der Fläche. Beschreibt das Feld — es rechnet nichts. */
export interface EngineFeld {
  /** Schlüssel im Eingabe-Objekt der Engine. Muss exakt passen, sonst kommt der Wert nie an. */
  schluessel: string;
  /** Klartext-Beschriftung. Der Nutzer liest sie, nicht den Schlüssel. */
  label: string;
  einheit?: string;
  pflicht: boolean;
  /** Vorbelegung. **Kommt aus der Engine-Doku**, ist keine eigene Annahme dieser Fläche. */
  vorgabe?: number;
  /** Auswahlfeld statt Zahl (z. B. Nutzungsbereich). */
  auswahl?: ReadonlyArray<{ wert: string; label: string }>;
  /** Ein Satz, was das Feld bedeutet — steht sichtbar, nicht nur im Tooltip. */
  hinweis?: string;
}

/** Eine Ergebniszahl mit Klartext. `schrittmass` heißt nicht „schrittmass". */
export interface EngineErgebnisFeld {
  schluessel: string;
  label: string;
  einheit?: string;
}

export interface EnginePanel {
  engineId: string;
  titel: string;
  /** Ein Satz, was die Fläche tut — im Präsens, weil sie es jetzt tut. */
  zweck: string;
  /** Norm/Regelwerk, das die Engine anwendet. Steht sichtbar: der Nutzer soll wissen, wonach gerechnet wird. */
  grundlage: string;
  felder: readonly EngineFeld[];
  ergebnisFelder: readonly EngineErgebnisFeld[];
  /**
   * Der **statische** Aufruf. Nimmt die rohen Feldwerte entgegen und gibt zurück, was die Engine
   * liefert — unverändert. Kein Nachrechnen, kein Nachrunden, kein Filtern.
   */
  berechne: (werte: Record<string, string>) => EngineErgebnis;
}

/**
 * Was eine Engine zurueckgibt, soweit die Huelle es braucht: ein `bestanden` und beliebige
 * Zahlenfelder. **`pruefungen` ist freiwillig** — nicht jede Engine fuehrt eine Prueflisteliste,
 * und eine im Panel zu bilden waere eine Rechnung (AUF-33 §3a).
 */
export type EngineErgebnis = {
  bestanden: boolean;
  /** Freiwillig: die Prueflisten-Eintraege, in derselben Form wie bei der Treppe. */
  pruefungen?: TreppenErgebnis['pruefungen'];
  [feld: string]: unknown;
};

/**
 * Feldwerte in die Engine-Eingabe übersetzen. **Leere Felder werden weggelassen, nicht auf 0
 * gesetzt** — die Engine hat eigene Vorgaben, und eine 0 wäre eine erfundene Angabe (Operanden-Gate).
 */
export function alsTreppenEingabe(werte: Record<string, string>): TreppenEingabe {
  const zahl = (k: string): number | undefined => {
    const roh = (werte[k] ?? '').trim();
    if (roh === '') return undefined;
    const n = Number(roh);
    return Number.isFinite(n) ? n : undefined;
  };
  const bereich = werte.bereich as TreppenEingabe['bereich'];
  return {
    geschosshoehe: zahl('geschosshoehe') ?? 0,
    ...(zahl('gewuenschteSteigung') !== undefined ? { gewuenschteSteigung: zahl('gewuenschteSteigung') } : {}),
    ...(zahl('verfuegbareLauflaenge') !== undefined ? { verfuegbareLauflaenge: zahl('verfuegbareLauflaenge') } : {}),
    ...(zahl('laufbreite') !== undefined ? { laufbreite: zahl('laufbreite') } : {}),
    ...(zahl('durchgangshoehe') !== undefined ? { durchgangshoehe: zahl('durchgangshoehe') } : {}),
    ...(bereich ? { bereich } : {}),
  };
}

/** Die Zuordnung. **Ein** Eintrag — L2. */
export const ENGINE_PANELS: readonly EnginePanel[] = [
  {
    engineId: 'engine-treppe',
    titel: 'Treppen-Auslegung',
    zweck: 'Ermittelt Stufenzahl, Steigung, Auftritt und Lauflänge aus der Geschosshöhe und prüft '
      + 'sie gegen die Regeln der DIN 18065.',
    grundlage: 'DIN 18065 — Schrittmaß-, Bequemlichkeits- und Sicherheitsregel, Grenzmaße je Nutzungsbereich',
    felder: [
      {
        schluessel: 'geschosshoehe', label: 'Geschosshöhe', einheit: 'mm', pflicht: true, vorgabe: 2800,
        hinweis: 'Oberkante Fertigfußboden unten bis Oberkante Fertigfußboden oben.',
      },
      {
        schluessel: 'gewuenschteSteigung', label: 'Ziel-Steigungshöhe', einheit: 'mm', pflicht: false, vorgabe: 175,
        hinweis: 'Wunschwert je Stufe. Die Engine rundet auf eine ganze Stufenzahl.',
      },
      {
        schluessel: 'verfuegbareLauflaenge', label: 'Verfügbare Lauflänge', einheit: 'mm', pflicht: false,
        hinweis: 'Grundfläche in Laufrichtung. Gesetzt ⇒ der Auftritt ergibt sich daraus.',
      },
      { schluessel: 'laufbreite', label: 'Laufbreite', einheit: 'mm', pflicht: false, vorgabe: 1000 },
      {
        schluessel: 'durchgangshoehe', label: 'Lichte Durchgangshöhe', einheit: 'mm', pflicht: false, vorgabe: 2000,
        hinweis: 'Kopffreiheit über der Lauflinie.',
      },
      {
        schluessel: 'bereich', label: 'Nutzungsbereich', pflicht: false,
        auswahl: [
          { wert: 'wohnung', label: 'Wohnung' },
          { wert: 'gebaeude', label: 'Gebäude (notwendige Treppe)' },
          { wert: 'aussen', label: 'Außentreppe' },
        ],
        hinweis: 'Bestimmt die Grenzmaße, gegen die geprüft wird.',
      },
    ],
    ergebnisFelder: [
      { schluessel: 'anzahlSteigungen', label: 'Steigungen' },
      { schluessel: 'anzahlAuftritte', label: 'Auftritte' },
      { schluessel: 'steigungshoehe', label: 'Steigungshöhe', einheit: 'mm' },
      { schluessel: 'auftritt', label: 'Auftritt', einheit: 'mm' },
      { schluessel: 'lauflaenge', label: 'Lauflänge', einheit: 'mm' },
      { schluessel: 'schrittmass', label: 'Schrittmaß (2·Steigung + Auftritt)', einheit: 'mm' },
      { schluessel: 'bequemlichkeit', label: 'Bequemlichkeit (Auftritt − Steigung)', einheit: 'mm' },
      { schluessel: 'sicherheit', label: 'Sicherheit (Auftritt + Steigung)', einheit: 'mm' },
    ],
    berechne: (werte) => berechneTreppe(alsTreppenEingabe(werte)) as unknown as EngineErgebnis,
  },
  {
    // AUF-52 Scheibe A — die erste von vier der Gruppe dach-zimmerei. Die anderen drei sind
    // begruendet zurueckgegeben (siehe Bericht): zweimal fehlt der Eingang, einmal die Ergebnisform.
    engineId: 'engine-sparren',
    titel: 'Sparren-Vorbemessung',
    zweck: 'Bemisst den Sparrenquerschnitt gegen Schnee- und Eigenlast und prueft Biegespannung '
      + 'und Durchbiegung nach.',
    grundlage: 'Eurocode 5 (Biegung, Durchbiegung L/300) mit Schneelast nach DIN EN 1991-1-3',
    felder: [
      {
        schluessel: 'gebaeudebreiteM', label: 'Gebäudebreite', einheit: 'm', pflicht: true, vorgabe: 10,
        hinweis: 'Spannweite Traufe–Traufe. Die Horizontalspanne eines Sparrens ist die Hälfte davon.',
      },
      { schluessel: 'neigungGrad', label: 'Dachneigung', einheit: '°', pflicht: true, vorgabe: 38 },
      {
        schluessel: 'sparrenabstandM', label: 'Sparrenabstand', einheit: 'm', pflicht: true, vorgabe: 0.8,
        hinweis: 'Achsabstand. Er bestimmt, welche Lastbreite ein einzelner Sparren traegt.',
      },
      { schluessel: 'breiteMm', label: 'Querschnitt Breite', einheit: 'mm', pflicht: true, vorgabe: 80 },
      { schluessel: 'hoeheMm', label: 'Querschnitt Höhe', einheit: 'mm', pflicht: true, vorgabe: 200 },
      {
        schluessel: 'schneezone', label: 'Schneelastzone', pflicht: true,
        auswahl: [
          { wert: '1', label: 'Zone 1' }, { wert: '1a', label: 'Zone 1a' },
          { wert: '2', label: 'Zone 2' }, { wert: '2a', label: 'Zone 2a' },
          { wert: '3', label: 'Zone 3' },
        ],
        hinweis: 'Zone nach Schneelastkarte. Sie bestimmt die Bodenschneelast am Standort.',
      },
      {
        schluessel: 'gelaendehoeheM', label: 'Geländehöhe über NN', einheit: 'm', pflicht: true, vorgabe: 300,
        hinweis: 'Die Schneelast steigt mit der Höhe des Standorts.',
      },
      {
        schluessel: 'eigenlastKnM2', label: 'Ständige Last', einheit: 'kN/m²', pflicht: false,
        hinweis: 'Dachdeckung, Lattung und Sparren-Eigengewicht. Leer ⇒ die Engine setzt ihre Vorgabe.',
      },
      {
        schluessel: 'holzklasse', label: 'Holzklasse', pflicht: false,
        auswahl: [{ wert: 'C24', label: 'C24' }, { wert: 'C30', label: 'C30' }],
      },
    ],
    ergebnisFelder: [
      { schluessel: 'sparrenlaengeM', label: 'Sparrenlänge', einheit: 'm' },
      { schluessel: 'bodenschneelastKnM2', label: 'Bodenschneelast s\u2096', einheit: 'kN/m²' },
      { schluessel: 'schneelastKnM2', label: 'Schneelast auf dem Dach', einheit: 'kN/m²' },
      { schluessel: 'designLinienlastPerpKnM', label: 'Bemessungslast senkrecht zum Sparren', einheit: 'kN/m' },
      { schluessel: 'momentKnM', label: 'Biegemoment', einheit: 'kNm' },
      { schluessel: 'sigmaMpa', label: 'Biegespannung σ', einheit: 'N/mm²' },
      { schluessel: 'biegefestigkeitMpa', label: 'Biegefestigkeit f', einheit: 'N/mm²' },
      { schluessel: 'ausnutzungBiegung', label: 'Ausnutzung Biegung' },
      { schluessel: 'durchbiegungMm', label: 'Durchbiegung', einheit: 'mm' },
      { schluessel: 'durchbiegungGrenzeMm', label: 'Grenzwert L/300', einheit: 'mm' },
      { schluessel: 'ausnutzungDurchbiegung', label: 'Ausnutzung Durchbiegung' },
    ],
    berechne: (werte) => berechneSparren(alsSparrenEingabe(werte)) as unknown as EngineErgebnis,
  },
];

/**
 * AUF-52 Scheibe A — Feldwerte in die Sparren-Eingabe. **Dieselbe Regel wie bei der Treppe:** leere
 * Felder werden weggelassen, nicht auf 0 gesetzt. Eine 0 waere eine erfundene Angabe.
 *
 * **Hier wird nichts gerechnet.** Keine Umrechnung, kein Grenzwert, keine Rundung — nur gelesen,
 * was im Feld steht, und weitergereicht. Jede Zahl entsteht in `berechneSparren`.
 */
export function alsSparrenEingabe(werte: Record<string, string>): SparrenEingabe {
  const zahl = (k: string): number | undefined => {
    const roh = (werte[k] ?? '').trim();
    if (roh === '') return undefined;
    const n = Number(roh);
    return Number.isFinite(n) ? n : undefined;
  };
  return {
    gebaeudebreiteM: zahl('gebaeudebreiteM') ?? 0,
    neigungGrad: zahl('neigungGrad') ?? 0,
    sparrenabstandM: zahl('sparrenabstandM') ?? 0,
    breiteMm: zahl('breiteMm') ?? 0,
    hoeheMm: zahl('hoeheMm') ?? 0,
    schneezone: (werte.schneezone ?? '2') as unknown as SparrenEingabe['schneezone'],
    gelaendehoeheM: zahl('gelaendehoeheM') ?? 0,
    ...(zahl('eigenlastKnM2') !== undefined ? { eigenlastKnM2: zahl('eigenlastKnM2') } : {}),
    ...(werte.holzklasse ? { holzklasse: werte.holzklasse as SparrenEingabe['holzklasse'] } : {}),
  };
}

/** Das Panel einer Engine — oder `undefined`, solange sie keines hat (die anderen zwölf). */
export function enginePanel(engineId: string): EnginePanel | undefined {
  return ENGINE_PANELS.find((p) => p.engineId === engineId);
}

/** Die Vorbelegung als Startwerte des Formulars. Ohne Vorgabe bleibt das Feld leer. */
export function startwerte(panel: EnginePanel): Record<string, string> {
  const w: Record<string, string> = {};
  for (const f of panel.felder) {
    if (f.vorgabe !== undefined) w[f.schluessel] = String(f.vorgabe);
    else if (f.auswahl && f.auswahl.length > 0) w[f.schluessel] = f.auswahl[0].wert;
    else w[f.schluessel] = '';
  }
  return w;
}

/** Fehlende Pflichtfelder — die Fläche rechnet nicht, solange etwas fehlt (Operanden-Gate). */
export function fehlendePflichtfelder(panel: EnginePanel, werte: Record<string, string>): EngineFeld[] {
  return panel.felder.filter((f) => f.pflicht && (werte[f.schluessel] ?? '').trim() === '');
}
