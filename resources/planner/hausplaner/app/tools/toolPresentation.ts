/**
 * Wizard-Welle A1 — Werkzeug-Präsentationsschicht. REINE Daten + reine Funktionen, kein React.
 *
 * Zweck: Die Kuratierung „welches Werkzeug erscheint wo" war bisher als lokale Konstante
 * (`CAD_TEILMENGE` in `faehigkeiten.ts`) im Nebensatz einer UI-nahen Datei versteckt. Hier steht
 * sie als benannte, getestete Datenschicht — für JEDES Werkzeug aus Registry ODER Katalog genau
 * eine Zone und eine Begründung.
 *
 * Abgrenzung (keine zweite Wahrheit):
 *  - WELCHE Werkzeuge es gibt        → `toolRegistry.TOOL_DEFINITIONS` / `toolCatalog.TOOL_KATALOG`
 *  - OB ein Werkzeug bedienbar ist   → `activation.resolveToolState` (Grund aus `ToolActivationRule.grund`)
 *  - WO es erscheint                 → diese Datei
 * Es wird kein Katalog-Eintrag gelöscht: `versteckt` ist eine Regel, kein Datenverlust — der Rückweg
 * bleibt offen, falls ein Werkzeug doch gebraucht wird.
 *
 * Erweiterungspunkt (A2): Diese Regeln sind der System-Default. Eine persönliche Ebene
 * (Pinnen/Workspace-Preset) legt sich später DARÜBER (persönlich → Preset → System-Default) und
 * ersetzt diese Datei nicht.
 */
import type { ToolDefinition } from './toolTypes';
import { TOOL_DEFINITIONS, toolNach } from './toolRegistry';
import { TOOL_KATALOG, katalogTool } from './toolCatalog';

/**
 * Zone der Werkzeugleiste:
 * - 'fix'       = immer sichtbare Bau-Werkzeuge (Rail-Kopf)
 * - 'kontext'   = Sofortbefehle auf die Auswahl
 * - 'weitere'   = kuratiert verfügbar, Handler folgt (A2/A3)
 * - 'versteckt' = bewusst nicht angeboten (Produkt-Scope), Daten bleiben erhalten
 */
export type RailZone = 'fix' | 'kontext' | 'weitere' | 'versteckt';

/** Woher die Werkzeug-Definition stammt (Registry hat bei Namensgleichheit Vorrang). */
export type ToolHerkunft = 'registry' | 'katalog';

export interface ToolPresentationRule {
  toolId: string;
  zone: RailZone;
  /** Anzeigereihenfolge innerhalb der Zone (aufsteigend, stabil). */
  ordnung: number;
  herkunft: ToolHerkunft;
  /** Warum diese Zone — erscheint im Konfigurationsdialog (A3) und in der Abnahme. */
  begruendung: string;
  /**
   * I3: Rang aus dem Werkzeugpaket. `primary` = Pflichtwerkzeug der Domäne („System", ⌂),
   * `secondary` = auf Wunsch anheftbar. Fehlt bei Registry-Regeln, die ihren Rang aus der
   * Fix-Zone beziehen.
   */
  prioritaet?: 'primary' | 'secondary';
  /** I3: Darf der Nutzer es anheften (★)? Aus dem Paketfeld `canPin`. */
  anheftbar?: boolean;
}

const GRUND_BAU = 'Bau-Werkzeug, Bestand';
const GRUND_SOFORT = 'Sofortbefehl auf die Auswahl';
const GRUND_PAKET = 'Fach-Werkzeug aus dem 110er-Paket — Zone entscheidet I3 (prioritaet/anheftbar)';

/**
 * Der Default-Regelsatz = exakt der gemessene Ist-Zustand (keine neue Fachentscheidung).
 * 9 Registry-ids + 54 Katalog-ids = 63 Regeln. Welche CAD-Werkzeuge ein Bauplaner wirklich in der
 * Leiste braucht, entscheidet Yama (Fach-Freigabe) — A1 baut nur den Mechanismus.
 */
export const TOOL_PRESENTATION_RULES: readonly ToolPresentationRule[] = [
  // --- Registry: die 7 modus-schaltenden Bau-Werkzeuge (Reihenfolge = Registry-Reihenfolge) ------
  { toolId: 'auswahl', zone: 'fix', ordnung: 1, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'wand', zone: 'fix', ordnung: 2, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'fenster', zone: 'fix', ordnung: 3, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'tuer', zone: 'fix', ordnung: 4, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'dach', zone: 'fix', ordnung: 5, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'decke', zone: 'fix', ordnung: 6, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'treppe', zone: 'fix', ordnung: 7, herkunft: 'registry', begruendung: GRUND_BAU },

  // --- Registry: die 2 Sofort-Aktionen ----------------------------------------------------------
  { toolId: 'loeschen', zone: 'kontext', ordnung: 1, herkunft: 'registry', begruendung: GRUND_SOFORT },
  { toolId: 'duplizieren', zone: 'kontext', ordnung: 2, herkunft: 'registry', begruendung: GRUND_SOFORT },

  // --- Katalog: die 110 Fach-Werkzeuge aus Yamas Paket (I2, AUF-21) -----------------------------
  // Alle in `versteckt`: sie sind als Daten vorhanden, aber noch nicht in der Leiste. Wohin sie
  // gehören, entscheidet **I3** anhand von `prioritaet`/`anheftbar` aus dem Paket — nicht dieser
  // Tausch. Der Unterschied zum alten Zustand: vorher standen 15 Werkzeuge in `weitere` und damit
  // sichtbar in der Fähigkeiten-Navi, OHNE dass ein Klick etwas tat. Genau diese falschen
  // Versprechen fallen mit I2 weg (AUF-28); die neuen versprechen nichts, bis I3 sie einordnet.
  { toolId: 'direktauswahl', zone: 'versteckt', ordnung: 1, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'rechteckauswahl', zone: 'versteckt', ordnung: 2, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'lassoauswahl', zone: 'versteckt', ordnung: 3, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verschieben', zone: 'versteckt', ordnung: 4, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'primary', anheftbar: true },
  { toolId: 'drehen', zone: 'versteckt', ordnung: 5, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'skalieren', zone: 'versteckt', ordnung: 6, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'horizontal-spiegeln', zone: 'versteckt', ordnung: 7, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'vertikal-spiegeln', zone: 'versteckt', ordnung: 8, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kopieren', zone: 'versteckt', ordnung: 9, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'sperren', zone: 'versteckt', ordnung: 10, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'entsperren', zone: 'versteckt', ordnung: 11, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'einblenden', zone: 'versteckt', ordnung: 12, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'ausblenden', zone: 'versteckt', ordnung: 13, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'gruppieren', zone: 'versteckt', ordnung: 14, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'ausrichten', zone: 'versteckt', ordnung: 15, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verteilen', zone: 'versteckt', ordnung: 16, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'linie', zone: 'versteckt', ordnung: 17, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'polylinie', zone: 'versteckt', ordnung: 18, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'rechteck', zone: 'versteckt', ordnung: 19, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'polygon', zone: 'versteckt', ordnung: 20, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kreis', zone: 'versteckt', ordnung: 21, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'bogen', zone: 'versteckt', ordnung: 22, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'trimmen', zone: 'versteckt', ordnung: 23, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verlaengern', zone: 'versteckt', ordnung: 24, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'versatz', zone: 'versteckt', ordnung: 25, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'teilen', zone: 'versteckt', ordnung: 26, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verbinden', zone: 'versteckt', ordnung: 27, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'raum', zone: 'versteckt', ordnung: 28, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'gaube', zone: 'versteckt', ordnung: 29, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'dachfenster', zone: 'versteckt', ordnung: 30, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'stuetze', zone: 'versteckt', ordnung: 31, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'unterzug', zone: 'versteckt', ordnung: 32, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'oeffnung', zone: 'versteckt', ordnung: 33, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'boden', zone: 'versteckt', ordnung: 34, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'schnitt', zone: 'versteckt', ordnung: 35, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'aufriss', zone: 'versteckt', ordnung: 36, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'vergroessern', zone: 'versteckt', ordnung: 37, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verkleinern', zone: 'versteckt', ordnung: 38, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'alles-anzeigen', zone: 'versteckt', ordnung: 39, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'hand', zone: 'versteckt', ordnung: 40, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'umkreisen', zone: 'versteckt', ordnung: 41, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'raster', zone: 'versteckt', ordnung: 42, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'fang', zone: 'versteckt', ordnung: 43, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'distanz-messen', zone: 'versteckt', ordnung: 44, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'bemassen', zone: 'versteckt', ordnung: 45, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'winkel-messen', zone: 'versteckt', ordnung: 46, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'flaeche-messen', zone: 'versteckt', ordnung: 47, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'volumen-messen', zone: 'versteckt', ordnung: 48, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'datei-importieren', zone: 'versteckt', ordnung: 49, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'primary', anheftbar: true },
  { toolId: 'bild-importieren', zone: 'versteckt', ordnung: 50, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kalibrieren', zone: 'versteckt', ordnung: 51, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'beschneiden', zone: 'versteckt', ordnung: 52, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'nordrichtung-setzen', zone: 'versteckt', ordnung: 53, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'grundriss-erkennen', zone: 'versteckt', ordnung: 54, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'ki-assistent', zone: 'versteckt', ordnung: 55, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'erkennung-bestaetigen', zone: 'versteckt', ordnung: 56, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'material-zuweisen', zone: 'versteckt', ordnung: 57, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'textur', zone: 'versteckt', ordnung: 58, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'material-aufnehmen', zone: 'versteckt', ordnung: 59, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'fassadensystem', zone: 'versteckt', ordnung: 60, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'klinker', zone: 'versteckt', ordnung: 61, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'daemmung', zone: 'versteckt', ordnung: 62, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'u-wert', zone: 'versteckt', ordnung: 63, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'thermische-huelle', zone: 'versteckt', ordnung: 64, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'lueftung', zone: 'versteckt', ordnung: 65, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'heizkoerper', zone: 'versteckt', ordnung: 66, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'fussbodenheizung', zone: 'versteckt', ordnung: 67, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'rohrleitung', zone: 'versteckt', ordnung: 68, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'pumpe', zone: 'versteckt', ordnung: 69, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'waermepumpe', zone: 'versteckt', ordnung: 70, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'hydraulischer-abgleich', zone: 'versteckt', ordnung: 71, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'sanitaeranschluss', zone: 'versteckt', ordnung: 72, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'badewanne', zone: 'versteckt', ordnung: 73, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'dusche', zone: 'versteckt', ordnung: 74, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'wc', zone: 'versteckt', ordnung: 75, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kuechenplanung', zone: 'versteckt', ordnung: 76, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'schrank', zone: 'versteckt', ordnung: 77, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'geraet', zone: 'versteckt', ordnung: 78, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'elektroplanung', zone: 'versteckt', ordnung: 79, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'steckdose', zone: 'versteckt', ordnung: 80, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'schalter', zone: 'versteckt', ordnung: 81, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'leuchte', zone: 'versteckt', ordnung: 82, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verteiler', zone: 'versteckt', ordnung: 83, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'pv-modul', zone: 'versteckt', ordnung: 84, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'batteriespeicher', zone: 'versteckt', ordnung: 85, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'wallbox', zone: 'versteckt', ordnung: 86, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'assistent', zone: 'versteckt', ordnung: 87, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'prozessuebersicht', zone: 'versteckt', ordnung: 88, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'uebergabepaket', zone: 'versteckt', ordnung: 89, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'freigeben', zone: 'versteckt', ordnung: 90, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kommentar', zone: 'versteckt', ordnung: 91, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'historie', zone: 'versteckt', ordnung: 92, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'revision', zone: 'versteckt', ordnung: 93, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'pruefen', zone: 'versteckt', ordnung: 94, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'warnungen', zone: 'versteckt', ordnung: 95, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'fehler', zone: 'versteckt', ordnung: 96, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'einstellungen', zone: 'versteckt', ordnung: 97, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'suche', zone: 'versteckt', ordnung: 98, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'befehlspalette', zone: 'versteckt', ordnung: 99, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'exportieren', zone: 'versteckt', ordnung: 100, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'pdf', zone: 'versteckt', ordnung: 101, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
];

const REGEL_NACH_ID = new Map(TOOL_PRESENTATION_RULES.map((r) => [r.toolId, r]));

/** Die Regel eines Werkzeugs (oder undefined, wenn es keine gibt). */
export function praesentation(toolId: string): ToolPresentationRule | undefined {
  return REGEL_NACH_ID.get(toolId);
}

/**
 * Löst eine id gegen Registry (Vorrang) und Katalog auf.
 * Vorrang der Registry ist Absicht: bei echter Namensgleichheit gilt die gepflegte Werkzeug-Wahrheit;
 * verschiedene ids (z. B. Registry 'auswahl' vs. Katalog 'selection') bleiben verschieden — sie werden
 * hier NICHT heimlich vereinheitlicht.
 */
function loeseAuf(toolId: string): ToolDefinition | undefined {
  return toolNach(toolId) ?? katalogTool(toolId);
}

/** Kern von `zoneTools`, gegen einen beliebigen Regelsatz (für Gegenproben in Tests). */
export function zoneToolsIn(
  regeln: readonly ToolPresentationRule[],
  zone: RailZone,
): ToolDefinition[] {
  return regeln
    .map((regel, index) => ({ regel, index }))
    .filter(({ regel }) => regel.zone === zone)
    // Stabil: primär `ordnung`, bei Gleichstand die Regel-Reihenfolge — nie nach `id`,
    // sonst springt die Leiste bei jeder Umbenennung.
    .sort((a, b) => (a.regel.ordnung - b.regel.ordnung) || (a.index - b.index))
    .map(({ regel }) => loeseAuf(regel.toolId))
    // Unbekannte id wird ausgelassen, nicht geworfen — `verwaisteRegeln()` macht sie sichtbar.
    .filter((t): t is ToolDefinition => t !== undefined);
}

/** Die Werkzeuge einer Zone, in Anzeigereihenfolge. Leere Zone ⇒ leeres Array (nie undefined). */
export function zoneTools(zone: RailZone): ToolDefinition[] {
  return zoneToolsIn(TOOL_PRESENTATION_RULES, zone);
}

/** Kern von `verwaisteRegeln`, gegen einen beliebigen Regelsatz (für Gegenproben in Tests). */
export function verwaisteRegelnIn(regeln: readonly ToolPresentationRule[]): string[] {
  return regeln.filter((r) => loeseAuf(r.toolId) === undefined).map((r) => r.toolId);
}

/** ids in Regeln, die weder in der Registry noch im Katalog existieren — muss leer sein. */
export function verwaisteRegeln(): string[] {
  return verwaisteRegelnIn(TOOL_PRESENTATION_RULES);
}

/** ids aus Registry/Katalog ohne Regel — muss leer sein (jedes Werkzeug hat genau eine Zone). */
export function regelloseWerkzeuge(): string[] {
  const alle = [...TOOL_DEFINITIONS.map((t) => t.id), ...TOOL_KATALOG.map((t) => t.id)];
  return [...new Set(alle.filter((id) => !REGEL_NACH_ID.has(id)))];
}
