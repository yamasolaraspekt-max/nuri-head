/**
 * Batch 0 — EINE Fähigkeiten-Registry (Navigation). Konsolidiert die bisher zwei Register + die
 * bislang unsichtbaren Rechen-Engines zu EINER Wahrheit, aus der die Navi datengetrieben rendert.
 *
 * Quellen (eine Wahrheit, kein re-deklarieren):
 *  - die echten Werkzeuge aus `toolRegistry.TOOL_DEFINITIONS` (auswahl/wand/… — zustand 'aktiv'),
 *  - die 13 reinen Rechen-Engines aus `geometry/*` als `art:'engine'`, `zustand:'schlaeft'`
 *    (Panels folgen in Batch 1–3 — hier nur SICHTBAR machen; die echten Exports sind referenziert),
 *  - eine CAD-sinnvolle Teilmenge aus `toolCatalog.TOOL_KATALOG` (Transform/Ausrichten/Navigation),
 *    remappt in die Gruppe 'werkzeuge' — literale DTP-Tools (Text/Bézier/Rahmen/Farbfelder/Preflight)
 *    werden BEWUSST NICHT übernommen (Produkt-Scope: manueller Bauplaner, kein DTP).
 *
 * Regel: geometry/*-Engines werden NUR referenziert/aufgerufen, nie geändert (Byte-Treue).
 */
import { TOOL_DEFINITIONS } from './toolRegistry';

export type FaehigkeitGruppe =
  | 'dach-zimmerei' | 'tga-heizung' | 'energie-pv' | 'sanitaer' | 'kueche'
  | 'bau' | 'fenster-tuer' | 'treppe' | 'werkzeuge';

/** 'werkzeug' = setzt activeToolId · 'aktion' = Sofortbefehl · 'engine' = reine Eingang→Ergebnis-Rechnung. */
export type FaehigkeitArt = 'werkzeug' | 'aktion' | 'engine';

/** 'aktiv' = bedienbar · 'schlaeft' = registriert/sichtbar, Handler/Panel folgt (Batch 1–3). */
export type FaehigkeitZustand = 'verfuegbar' | 'voraussetzung' | 'nur_ergebnis' | 'in_entwicklung';

export interface Faehigkeit {
  id: string;
  label: string;
  gruppe: FaehigkeitGruppe;
  art: FaehigkeitArt;
  zustand: FaehigkeitZustand;
  /** Einzeiler: was die Fähigkeit fachlich tut. */
  funktion: string;
  /** Nur art:'engine': Eingang/Ausgang der echten Rechnung (fürs spätere Panel). */
  eingang?: string;
  ausgang?: string;
  /** Nur art:'engine': Doku-Referenz auf das echte Modul (nur aufgerufen, nie geändert). */
  engineModul?: string;
  /** Nur art:'engine': der ECHTE Export-Name im Modul (≠ Modulname). Vom Guard-Test verriegelt. */
  engineExport?: string;
  /** Nur art:'werkzeug'|'aktion': die TOOL_DEFINITIONS-id, die aktiviert wird (falls schon verdrahtet). */
  toolId?: string;
}

export const FAEHIGKEIT_GRUPPEN: ReadonlyArray<{ id: FaehigkeitGruppe; label: string }> = [
  { id: 'dach-zimmerei', label: 'Dach & Zimmerei' },
  { id: 'tga-heizung', label: 'TGA / Heizung' },
  { id: 'energie-pv', label: 'Energie & PV' },
  { id: 'sanitaer', label: 'Sanitär' },
  { id: 'kueche', label: 'Küche' },
  { id: 'bau', label: 'Bau' },
  { id: 'fenster-tuer', label: 'Fenster & Tür' },
  { id: 'treppe', label: 'Treppe' },
  { id: 'werkzeuge', label: 'Werkzeuge' },
];

// --- 1) Echte Werkzeuge (aus der bestehenden Registry, zustand 'aktiv') --------------------------
const WERKZEUG_GRUPPE: Record<string, FaehigkeitGruppe> = {
  auswahl: 'werkzeuge', wand: 'bau', fenster: 'fenster-tuer', tuer: 'fenster-tuer',
  dach: 'dach-zimmerei', decke: 'bau', treppe: 'treppe', loeschen: 'werkzeuge', duplizieren: 'werkzeuge',
};
const werkzeugFaehigkeiten: Faehigkeit[] = TOOL_DEFINITIONS.map((t) => ({
  id: t.id,
  label: t.label,
  gruppe: WERKZEUG_GRUPPE[t.id] ?? 'werkzeuge',
  art: t.art,
  zustand: 'verfuegbar',
  funktion: t.helpText,
  toolId: t.id,
}));

// --- 2) Reine Rechen-Engines (echte Exports aus geometry/*, zustand 'schlaeft') ------------------
const engineFaehigkeiten: Faehigkeit[] = [
  { id: 'engine-fbh', label: 'Fußbodenheizung-Auslegung', gruppe: 'tga-heizung', art: 'engine', zustand: 'in_entwicklung', funktion: 'Heizfläche, Rohrlänge, Heizkreise', eingang: 'FbhEingabe', ausgang: 'FbhErgebnis', engineModul: 'geometry/fbhAuslegung', engineExport: 'fbhAuslegung' },
  { id: 'engine-heizkoerper', label: 'Heizkörper-Leistung', gruppe: 'tga-heizung', art: 'engine', zustand: 'in_entwicklung', funktion: 'Über-/Unterdeckung nach EN 442', eingang: 'Normleistung + Betriebsbedingung', ausgang: 'DeckungErgebnis', engineModul: 'geometry/heizkoerperLeistung', engineExport: 'bewerteDeckung' },
  { id: 'engine-heizkreis', label: 'Heizkreis-Verteiler', gruppe: 'tga-heizung', art: 'engine', zustand: 'in_entwicklung', funktion: 'Abgänge, Massenstrom je Kreis', eingang: 'HeizkreisEingabe[]', ausgang: 'VerteilerErgebnis', engineModul: 'geometry/heizkreisVerteiler', engineExport: 'auslegeVerteiler' },
  { id: 'engine-abwasser', label: 'Abwasser-Gefälle', gruppe: 'sanitaer', art: 'engine', zustand: 'in_entwicklung', funktion: 'Mindestgefälle DIN 1986-100', eingang: 'AbwasserEingabe', ausgang: 'AbwasserErgebnis', engineModul: 'geometry/abwassergefaelle', engineExport: 'pruefeAbwasser' },
  { id: 'engine-kueche', label: 'Küchen-Arbeitsdreieck', gruppe: 'kueche', art: 'engine', zustand: 'in_entwicklung', funktion: 'Ergonomie DIN 18022', eingang: 'Arbeitsdreieck', ausgang: 'DreieckErgebnis', engineModul: 'geometry/kuecheArbeitsdreieck', engineExport: 'bewerteArbeitsdreieck' },
  { id: 'engine-pv', label: 'PV-Schnellbelegung', gruppe: 'energie-pv', art: 'engine', zustand: 'in_entwicklung', funktion: 'Modulzahl, kWp, Flächennutzung', eingang: 'PvEingabe', ausgang: 'PvBelegung', engineModul: 'geometry/pvBelegung', engineExport: 'pvSchnellBelegung' },
  { id: 'engine-uwert', label: 'U-Wert (Wandaufbau)', gruppe: 'bau', art: 'engine', zustand: 'in_entwicklung', funktion: 'U-Wert aus Schichten DIN EN ISO 6946', eingang: 'Schicht[]', ausgang: 'UErgebnis', engineModul: 'geometry/wandaufbau', engineExport: 'berechneUWert' },
  { id: 'engine-fensterprodukt', label: 'Fenster Uw / RC / Preis', gruppe: 'fenster-tuer', art: 'engine', zustand: 'in_entwicklung', funktion: 'Uw (ISO 10077), RC (EN 1627), Preis', eingang: 'UwEingabe', ausgang: 'UwErgebnis', engineModul: 'geometry/fensterProdukt', engineExport: 'berechneUw' },
  { id: 'engine-sparren', label: 'Sparren-Vorbemessung', gruppe: 'dach-zimmerei', art: 'engine', zustand: 'in_entwicklung', funktion: 'Biegenachweis Eurocode 5', eingang: 'SparrenEingabe', ausgang: 'SparrenErgebnis', engineModul: 'geometry/sparrenBerechnung', engineExport: 'berechneSparren' },
  { id: 'engine-treppe', label: 'Treppen-Auslegung', gruppe: 'treppe', art: 'engine', zustand: 'in_entwicklung', funktion: 'Stufen/Steigung DIN 18065', eingang: 'TreppenEingabe', ausgang: 'TreppenErgebnis', engineModul: 'geometry/treppenBerechnung', engineExport: 'berechneTreppe' },
  { id: 'engine-holzmengen', label: 'Holz-Mengen (BOM)', gruppe: 'dach-zimmerei', art: 'engine', zustand: 'in_entwicklung', funktion: 'Sparren/Konter/Latten summieren', eingang: 'HolzStück[]', ausgang: 'HolzMengen', engineModul: 'geometry/holzMengen', engineExport: 'holzMengenAusListe' },
  { id: 'engine-holzbauteile', label: 'Holz-Bauteile (BOM)', gruppe: 'dach-zimmerei', art: 'engine', zustand: 'in_entwicklung', funktion: 'Pfetten/Grat-/Kehlsparren aggregieren', eingang: 'Holzliste', ausgang: 'HolzBauteile', engineModul: 'geometry/holzBauteile', engineExport: 'holzBauteileAusListe' },
  { id: 'engine-schifter', label: 'Schifter-Liste', gruppe: 'dach-zimmerei', art: 'engine', zustand: 'in_entwicklung', funktion: 'Schiftsparren klassifizieren + Stückliste', eingang: 'Fläche (u/v)', ausgang: 'SchifterSparren[]', engineModul: 'geometry/schifterListe', engineExport: 'klassifiziereSchifter' },
];

// --- 3) Werkzeug-Katalog: seit I4 NICHT mehr hier -----------------------------------------------
// Bis I2 spiegelte die Fähigkeiten-Navi eine Teilmenge des Werkzeug-Katalogs als `cad-*`-Einträge.
// Das waren anklickbare Zeilen ohne Handler — falsche Versprechen (AUF-28). Seit I4 stehen die 110
// Werkzeuge dort, wo sie hingehören: in den Kategorie-Gruppen der oberen Werkzeugleiste
// (`dashboard/werkzeugGruppen.ts`). Die Navi führt wieder nur das, was sie meint — Fachbereiche und
// Rechen-Engines. EINE Wahrheit je Sachverhalt, und keine Zeile, die etwas verspricht.
const werkzeugKatalogFaehigkeiten: Faehigkeit[] = [];

/** DIE eine Fähigkeiten-Liste — Navi rendert ausschließlich hieraus. */
export const FAEHIGKEITEN: readonly Faehigkeit[] = [
  ...werkzeugFaehigkeiten,
  ...engineFaehigkeiten,
  ...werkzeugKatalogFaehigkeiten,
];

/** Fähigkeiten einer Gruppe (Anzeigereihenfolge). */
export function faehigkeitenNach(gruppe: FaehigkeitGruppe): Faehigkeit[] {
  return FAEHIGKEITEN.filter((f) => f.gruppe === gruppe);
}

/** Alle Fähigkeiten. */
export function alleFaehigkeiten(): Faehigkeit[] {
  return [...FAEHIGKEITEN];
}

/** Doppelte ids (Konsolidierungs-Schutz). Leere Liste = eine Wahrheit ohne Kollision. */
export function doppelteIds(): string[] {
  const gesehen = new Set<string>();
  const doppelt = new Set<string>();
  for (const f of FAEHIGKEITEN) {
    if (gesehen.has(f.id)) doppelt.add(f.id);
    gesehen.add(f.id);
  }
  return [...doppelt];
}
