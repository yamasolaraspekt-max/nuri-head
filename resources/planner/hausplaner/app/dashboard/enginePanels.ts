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
 * **L3 ist ausdrücklich nicht Teil dieses Auftrags.** Wer das Muster zwölfmal kopiert, bevor es
 * abgenommen ist, hat es nicht prüfen lassen. Die Tabelle unten hat deshalb genau **einen** Eintrag.
 */
import { berechneTreppe, type TreppenEingabe, type TreppenErgebnis } from '../../geometry/treppenBerechnung';

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
  berechne: (werte: Record<string, string>) => TreppenErgebnis;
}

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
    berechne: (werte) => berechneTreppe(alsTreppenEingabe(werte)),
  },
];

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
