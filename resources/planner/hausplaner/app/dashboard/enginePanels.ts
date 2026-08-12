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
// AUF-52 Scheibe B (tga-heizung) — wieder statisch, wieder ohne Rechnung in dieser Datei.
import { fbhAuslegung, type FbhEingabe } from '../../geometry/fbhAuslegung';
import { bewerteDeckung, type BetriebsBedingung } from '../../geometry/heizkoerperLeistung';
// AUF-52 Scheibe C (der Rest) — vier statische Importe, wieder ohne Rechnung in dieser Datei.
import { berechneUw, type UwEingabe } from '../../geometry/fensterProdukt';
import { pruefeAbwasser, type AbwasserEingabe } from '../../geometry/abwassergefaelle';
import { bewerteArbeitsdreieck, type Arbeitsdreieck } from '../../geometry/kuecheArbeitsdreieck';
import { pvSchnellBelegung, type PvEingabe } from '../../geometry/pvBelegung';

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
  /**
   * **Kein Gesamturteil anzeigen** — auch dann nicht, wenn die Engine ein `bestanden` liefert.
   *
   * Fuer N-003 (Sparren-Vorbemessung): die Rechnung liefert `bestanden` weiterhin, weil es
   * Information traegt — aber eine Plakette "Alle Pruefungen bestanden" behauptet einen NACHWEIS,
   * und den gibt es hier nicht. Der Vorbehalt steht stattdessen als Feld im Ergebnis.
   *
   * Ohne dieses Flag muesste man `bestanden` entfernen, um die Plakette loszuwerden — das waere
   * Informationsverlust statt Richtigstellung.
   */
  keinGesamturteil?: boolean;
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
    grundlage: 'Eurocode 5 (Biegung, Durchbiegung L/300) mit Schneelast nach DIN EN 1991-1-3 — '
      + 'VORBEMESSUNG im Entwurf: kein Ausführungsnachweis, keine Genehmigungsunterlage, keine '
      + 'Freigabe zur Ausführung. Wind, Mehrfeld, Knicken und Auflagerpressung sind NICHT erfasst.',
    keinGesamturteil: true,
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
      // N-003: der Vorbehalt steht IM SELBEN BLICK wie die beiden Ausnutzungen — eine
      // "Ausnutzung 0,85" liest jeder als "Nachweis erfuellt". Genau dort gehoert die Grenze hin.
      { schluessel: 'vorbehalt', label: 'Vorbehalt' },
    ],
    berechne: (werte) => berechneSparren(alsSparrenEingabe(werte)) as unknown as EngineErgebnis,
  },
  {
    // AUF-52 Scheibe B, erste von drei der Gruppe tga-heizung.
    engineId: 'engine-fbh',
    titel: 'Fussbodenheizung-Auslegung',
    zweck: 'Ermittelt Rohrlaenge, Anzahl und Laenge der Heizkreise sowie die spezifische Leistung '
      + 'aus Flaeche, Heizlast und Verlegeabstand.',
    grundlage: 'Auslegung nach Verlegeabstand und maximaler Heizkreislaenge; Pruefpunkte zu Leistung und Kreislaenge',
    felder: [
      { schluessel: 'flaeche', label: 'Beheizbare Grundflaeche', einheit: 'm²', pflicht: true, vorgabe: 20 },
      { schluessel: 'heizlast', label: 'Raumheizlast', einheit: 'W', pflicht: true, vorgabe: 1400 },
      {
        schluessel: 'verlegeabstand', label: 'Verlegeabstand', einheit: 'mm', pflicht: false,
        hinweis: 'Leer ⇒ die Engine setzt ihre Vorgabe. Enger verlegt heisst mehr Rohr und mehr Leistung.',
      },
      {
        schluessel: 'sperrflaeche', label: 'Sperrflaeche', einheit: 'm²', pflicht: false,
        hinweis: 'Flaeche unter Einbauten, die nicht beheizt wird.',
      },
      { schluessel: 'maxKreisLaenge', label: 'Maximale Heizkreislaenge', einheit: 'm', pflicht: false },
      { schluessel: 'anbindungProKreis', label: 'Anbindeleitung je Kreis', einheit: 'm', pflicht: false },
    ],
    ergebnisFelder: [
      { schluessel: 'nutzflaeche', label: 'Beheizte Nutzflaeche', einheit: 'm²' },
      { schluessel: 'rohrlaengeGesamt', label: 'Rohrlaenge gesamt (inkl. Anbindung)', einheit: 'm' },
      { schluessel: 'anzahlHeizkreise', label: 'Heizkreise' },
      { schluessel: 'rohrProKreis', label: 'Rohr je Kreis (laengster)', einheit: 'm' },
      { schluessel: 'spezifischeLeistung', label: 'Spezifische Leistung', einheit: 'W/m²' },
    ],
    berechne: (werte) => fbhAuslegung(alsFbhEingabe(werte)) as unknown as EngineErgebnis,
  },
  {
    engineId: 'engine-heizkoerper',
    titel: 'Heizkoerper-Leistung',
    zweck: 'Rechnet die Normleistung auf die tatsaechlichen Betriebstemperaturen um und prueft, ob '
      + 'sie die Raumheizlast deckt.',
    grundlage: 'Leistungsumrechnung ueber die arithmetische Uebertemperatur mit dem Heizkoerper-Exponenten n',
    felder: [
      { schluessel: 'normLeistung', label: 'Normleistung', einheit: 'W', pflicht: true, vorgabe: 1500 },
      { schluessel: 'raumheizlast', label: 'Raumheizlast', einheit: 'W', pflicht: true, vorgabe: 1200 },
      { schluessel: 'vorlauf', label: 'Vorlauftemperatur', einheit: '°C', pflicht: true, vorgabe: 55 },
      { schluessel: 'ruecklauf', label: 'Ruecklauftemperatur', einheit: '°C', pflicht: true, vorgabe: 45 },
      { schluessel: 'raumtemp', label: 'Raumtemperatur', einheit: '°C', pflicht: true, vorgabe: 20 },
      {
        schluessel: 'n', label: 'Heizkoerper-Exponent n', pflicht: false,
        hinweis: 'Leer ⇒ die Engine setzt ihre Vorgabe fuer einen typischen Kompaktheizkoerper.',
      },
      {
        schluessel: 'normUebertemperatur', label: 'Norm-Uebertemperatur', einheit: 'K', pflicht: false,
        hinweis: 'Uebertemperatur, auf die sich die Normleistung bezieht (50 K = 75/65/20).',
      },
    ],
    ergebnisFelder: [
      { schluessel: 'betriebsLeistung', label: 'Leistung im Betrieb', einheit: 'W' },
      { schluessel: 'deckungsgrad', label: 'Deckungsgrad', einheit: '%' },
      { schluessel: 'hinweis', label: 'Bewertung' },
    ],
    /**
     * **Eine Umbenennung, keine Rechnung — und sie wird hier offen ausgewiesen.**
     * `bewerteDeckung` nennt sein Bestehens-Merkmal `ausreichend`; die Huelle liest `bestanden`.
     * Der Wert wird **unveraendert durchgereicht**, nur unter dem Namen, den die Huelle kennt.
     * Nichts wird gerechnet, nichts entschieden — waere hier ein eigener Grenzwert, waere es ein
     * Defekt nach AUF-33 §3a.
     */
    berechne: (werte) => {
      const zahl = (k: string): number => Number((werte[k] ?? '').trim() || '0');
      const r = bewerteDeckung(zahl('normLeistung'), zahl('raumheizlast'), alsBetriebsBedingung(werte));
      return { ...r, bestanden: r.ausreichend } as unknown as EngineErgebnis;
    },
  },
  {
    engineId: 'engine-fensterprodukt',
    titel: 'Fenster Uw',
    zweck: 'Rechnet den Waermedurchgang des ganzen Fensters aus Glas, Rahmen und Randverbund.',
    grundlage: 'DIN EN ISO 10077-1 — Uw = (Ag·Ug + Af·Uf + lg·Psi) / (Ag + Af)',
    felder: [
      { schluessel: 'breiteMm', label: 'Fensterbreite', einheit: 'mm', pflicht: true, vorgabe: 1200 },
      { schluessel: 'hoeheMm', label: 'Fensterhoehe', einheit: 'mm', pflicht: true, vorgabe: 1400 },
      { schluessel: 'uf', label: 'Uf Rahmen', einheit: 'W/(m²·K)', pflicht: true, vorgabe: 1.3 },
      { schluessel: 'ug', label: 'Ug Glas', einheit: 'W/(m²·K)', pflicht: true, vorgabe: 0.6 },
      { schluessel: 'ansichtsbreiteMm', label: 'Rahmen-Ansichtsbreite', einheit: 'mm', pflicht: true, vorgabe: 70 },
      {
        schluessel: 'psiRandverbund', label: 'Psi Randverbund', einheit: 'W/(m·K)', pflicht: false,
        hinweis: 'Leer ⇒ die Engine setzt ihre Vorgabe fuer eine warme Kante.',
      },
    ],
    ergebnisFelder: [
      { schluessel: 'uw', label: 'Uw', einheit: 'W/(m²·K)' },
      { schluessel: 'agM2', label: 'Glasflaeche', einheit: 'm²' },
      { schluessel: 'afM2', label: 'Rahmenflaeche', einheit: 'm²' },
      { schluessel: 'lgM', label: 'Sichtbarer Glasumfang', einheit: 'm' },
    ],
    berechne: (werte) => berechneUw(alsUwEingabe(werte)) as unknown as EngineErgebnis,
  },
  {
    engineId: 'engine-abwasser',
    titel: 'Abwasser-Gefaelle',
    zweck: 'Prueft das Gefaelle einer liegenden Leitung gegen das Mindestgefaelle ihrer Nennweite '
      + 'und nennt den Hoehenverlust ueber die Laenge.',
    grundlage: 'Mindestgefaelle je Nennweite; Hoehenverlust = Gefaelle x Laenge',
    felder: [
      { schluessel: 'dn', label: 'Nennweite DN', pflicht: true, vorgabe: 100 },
      { schluessel: 'laenge', label: 'Leitungslaenge horizontal', einheit: 'm', pflicht: true, vorgabe: 8 },
      {
        schluessel: 'gefaelle', label: 'Gefaelle', einheit: '%', pflicht: false,
        hinweis: 'Leer ⇒ die Engine rechnet mit dem Mindestgefaelle der Nennweite.',
      },
    ],
    ergebnisFelder: [
      { schluessel: 'gefaelle', label: 'Verwendetes Gefaelle', einheit: '%' },
      { schluessel: 'mindestGefaelle', label: 'Mindestgefaelle', einheit: '%' },
      { schluessel: 'hoehenverlust', label: 'Hoehenverlust', einheit: 'mm' },
    ],
    berechne: (werte) => pruefeAbwasser(alsAbwasserEingabe(werte)) as unknown as EngineErgebnis,
  },
  {
    engineId: 'engine-kueche',
    titel: 'Kuechen-Arbeitsdreieck',
    zweck: 'Misst die Wege zwischen Spuele, Kochstelle und Kuehlgeraet und bewertet das Dreieck.',
    grundlage: 'Arbeitsdreieck der Kuechenplanung — Summe der drei Wege und Einzelstrecken',
    felder: [
      { schluessel: 'spueleX', label: 'Spuele X', einheit: 'mm', pflicht: true, vorgabe: 0 },
      { schluessel: 'spueleY', label: 'Spuele Y', einheit: 'mm', pflicht: true, vorgabe: 0 },
      { schluessel: 'kochenX', label: 'Kochen X', einheit: 'mm', pflicht: true, vorgabe: 1800 },
      { schluessel: 'kochenY', label: 'Kochen Y', einheit: 'mm', pflicht: true, vorgabe: 0 },
      { schluessel: 'kuehlenX', label: 'Kuehlen X', einheit: 'mm', pflicht: true, vorgabe: 900 },
      { schluessel: 'kuehlenY', label: 'Kuehlen Y', einheit: 'mm', pflicht: true, vorgabe: 2200 },
    ],
    ergebnisFelder: [
      { schluessel: 'wegSpKo', label: 'Spuele ↔ Kochen', einheit: 'mm' },
      { schluessel: 'wegKoKu', label: 'Kochen ↔ Kuehlen', einheit: 'mm' },
      { schluessel: 'wegKuSp', label: 'Kuehlen ↔ Spuele', einheit: 'mm' },
      { schluessel: 'summe', label: 'Summe der Wege', einheit: 'mm' },
    ],
    berechne: (werte) => bewerteArbeitsdreieck(alsArbeitsdreieck(werte)) as unknown as EngineErgebnis,
  },
  {
    engineId: 'engine-pv',
    titel: 'PV-Schnellbelegung',
    zweck: 'Legt Module auf eine Dachflaeche und nennt Anzahl, Leistung und Flaechennutzung.',
    grundlage: 'Rasterbelegung mit Randabstand und Modulspalt; hoch- und querformatig verglichen',
    felder: [
      { schluessel: 'dachLaenge', label: 'Dachbreite horizontal', einheit: 'mm', pflicht: true, vorgabe: 10000 },
      { schluessel: 'dachBreite', label: 'Dachlaenge in Falllinie', einheit: 'mm', pflicht: true, vorgabe: 6000 },
      { schluessel: 'modulBreite', label: 'Modulbreite', einheit: 'mm', pflicht: true, vorgabe: 1134 },
      { schluessel: 'modulHoehe', label: 'Modulhoehe', einheit: 'mm', pflicht: true, vorgabe: 1762 },
      { schluessel: 'modulLeistung', label: 'Modul-Nennleistung', einheit: 'Wp', pflicht: true, vorgabe: 440 },
      { schluessel: 'randabstand', label: 'Randabstand', einheit: 'mm', pflicht: false },
      { schluessel: 'modulabstand', label: 'Spalt zwischen Modulen', einheit: 'mm', pflicht: false },
    ],
    ergebnisFelder: [
      { schluessel: 'orientierung', label: 'Ausrichtung' },
      { schluessel: 'spalten', label: 'Spalten' },
      { schluessel: 'reihen', label: 'Reihen' },
      { schluessel: 'moduleGesamt', label: 'Module gesamt' },
      { schluessel: 'kWp', label: 'Leistung', einheit: 'kWp' },
      { schluessel: 'dachFlaecheM2', label: 'Dachflaeche', einheit: 'm²' },
      { schluessel: 'belegteFlaecheM2', label: 'Belegte Flaeche', einheit: 'm²' },
      { schluessel: 'flaechennutzung', label: 'Flaechennutzung', einheit: '%' },
    ],
    berechne: (werte) => pvSchnellBelegung(alsPvEingabe(werte)) as unknown as EngineErgebnis,
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

/**
 * AUF-52 Scheibe B — Feldwerte in die FBH-Eingabe. **Dieselbe Regel wie ueberall:** leere Felder
 * werden weggelassen, nicht auf 0 gesetzt; die Engine hat eigene Vorgaben (150 mm Verlegeabstand,
 * 100 m Kreislaenge, 5 m Anbindung). Eine 0 waere eine erfundene Angabe.
 */
export function alsFbhEingabe(werte: Record<string, string>): FbhEingabe {
  const zahl = (k: string): number | undefined => {
    const roh = (werte[k] ?? '').trim();
    if (roh === '') return undefined;
    const n = Number(roh);
    return Number.isFinite(n) ? n : undefined;
  };
  return {
    flaeche: zahl('flaeche') ?? 0,
    heizlast: zahl('heizlast') ?? 0,
    ...(zahl('verlegeabstand') !== undefined ? { verlegeabstand: zahl('verlegeabstand') } : {}),
    ...(zahl('sperrflaeche') !== undefined ? { sperrflaeche: zahl('sperrflaeche') } : {}),
    ...(zahl('maxKreisLaenge') !== undefined ? { maxKreisLaenge: zahl('maxKreisLaenge') } : {}),
    ...(zahl('anbindungProKreis') !== undefined ? { anbindungProKreis: zahl('anbindungProKreis') } : {}),
  };
}

/** AUF-52 Scheibe B — die Betriebsbedingung des Heizkoerpers, gelesen und weitergereicht. */
export function alsBetriebsBedingung(werte: Record<string, string>): BetriebsBedingung {
  const zahl = (k: string): number | undefined => {
    const roh = (werte[k] ?? '').trim();
    if (roh === '') return undefined;
    const n = Number(roh);
    return Number.isFinite(n) ? n : undefined;
  };
  return {
    vorlauf: zahl('vorlauf') ?? 0,
    ruecklauf: zahl('ruecklauf') ?? 0,
    raumtemp: zahl('raumtemp') ?? 0,
    ...(zahl('n') !== undefined ? { n: zahl('n') } : {}),
    ...(zahl('normUebertemperatur') !== undefined ? { normUebertemperatur: zahl('normUebertemperatur') } : {}),
  };
}

/** Eine Zahl aus einem Feld, oder `undefined` bei leer — die gemeinsame Lesehilfe aller Uebersetzer. */
function feldZahl(werte: Record<string, string>, k: string): number | undefined {
  const roh = (werte[k] ?? '').trim();
  if (roh === '') return undefined;
  const n = Number(roh);
  return Number.isFinite(n) ? n : undefined;
}

/** AUF-52 Scheibe C — Fenster-Uw. Sechs Zahlen, gelesen und weitergereicht. */
export function alsUwEingabe(werte: Record<string, string>): UwEingabe {
  return {
    breiteMm: feldZahl(werte, 'breiteMm') ?? 0,
    hoeheMm: feldZahl(werte, 'hoeheMm') ?? 0,
    uf: feldZahl(werte, 'uf') ?? 0,
    ug: feldZahl(werte, 'ug') ?? 0,
    ansichtsbreiteMm: feldZahl(werte, 'ansichtsbreiteMm') ?? 0,
    ...(feldZahl(werte, 'psiRandverbund') !== undefined ? { psiRandverbund: feldZahl(werte, 'psiRandverbund') } : {}),
  };
}

/** AUF-52 Scheibe C — Abwassergefaelle. Ohne Gefaelle-Angabe setzt die Engine das Mindestgefaelle. */
export function alsAbwasserEingabe(werte: Record<string, string>): AbwasserEingabe {
  return {
    dn: feldZahl(werte, 'dn') ?? 0,
    laenge: feldZahl(werte, 'laenge') ?? 0,
    ...(feldZahl(werte, 'gefaelle') !== undefined ? { gefaelle: feldZahl(werte, 'gefaelle') } : {}),
  };
}

/** AUF-52 Scheibe C — das Arbeitsdreieck aus sechs Koordinaten. Drei Punkte, nicht drei Annahmen. */
export function alsArbeitsdreieck(werte: Record<string, string>): Arbeitsdreieck {
  const punkt = (p: string) => ({ x: feldZahl(werte, `${p}X`) ?? 0, y: feldZahl(werte, `${p}Y`) ?? 0 });
  return { spuele: punkt('spuele'), kochen: punkt('kochen'), kuehlen: punkt('kuehlen') };
}

/** AUF-52 Scheibe C — PV-Schnellbelegung. */
export function alsPvEingabe(werte: Record<string, string>): PvEingabe {
  return {
    dachLaenge: feldZahl(werte, 'dachLaenge') ?? 0,
    dachBreite: feldZahl(werte, 'dachBreite') ?? 0,
    modulBreite: feldZahl(werte, 'modulBreite') ?? 0,
    modulHoehe: feldZahl(werte, 'modulHoehe') ?? 0,
    modulLeistung: feldZahl(werte, 'modulLeistung') ?? 0,
    ...(feldZahl(werte, 'randabstand') !== undefined ? { randabstand: feldZahl(werte, 'randabstand') } : {}),
    ...(feldZahl(werte, 'modulabstand') !== undefined ? { modulabstand: feldZahl(werte, 'modulabstand') } : {}),
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
