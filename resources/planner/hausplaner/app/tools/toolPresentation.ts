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
const GRUND_PAKET = 'Fach-Werkzeug — über seine Kategorie-Gruppe in der oberen Leiste erreichbar (I4)';

/**
 * Der Default-Regelsatz = exakt der gemessene Ist-Zustand (keine neue Fachentscheidung).
 * 9 Registry-ids + 54 Katalog-ids = 63 Regeln. Welche CAD-Werkzeuge ein Bauplaner wirklich in der
 * Leiste braucht, entscheidet Yama (Fach-Freigabe) — A1 baut nur den Mechanismus.
 */
// **W-05: `bemassen` und `flaeche-messen` sind ab jetzt `herkunft: 'registry'`.**
//
// *Nicht kosmetisch:* eine Zusage in `leisteAusZonen.test.ts` prueft, dass jede als `katalog`
// gefuehrte Regel auch im `TOOL_KATALOG` steht — und die beiden stehen dort nicht mehr, sie sind
// in die Registry gehoben. Die Regel htte sonst behauptet, ein Werkzeug komme aus einem Katalog,
// der es nicht kennt. **Die ZONE bleibt `weitere`** — wo sie erscheinen, hat W-05 nicht
// entschieden; nur WOHER sie kommen, hat sich geaendert.
export const TOOL_PRESENTATION_RULES: readonly ToolPresentationRule[] = [
  // --- Registry: die 7 modus-schaltenden Bau-Werkzeuge (Reihenfolge = Registry-Reihenfolge) ------
  { toolId: 'auswahl', zone: 'fix', ordnung: 1, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'wand', zone: 'fix', ordnung: 2, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'fenster', zone: 'fix', ordnung: 3, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'tuer', zone: 'fix', ordnung: 4, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'dach', zone: 'fix', ordnung: 5, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'decke', zone: 'fix', ordnung: 6, herkunft: 'registry', begruendung: GRUND_BAU },
  { toolId: 'treppe', zone: 'fix', ordnung: 7, herkunft: 'registry', begruendung: GRUND_BAU },
  // Z-05-N1: die Kontur steht am Ende der Fix-Zone, nicht mittendrin — die Ordnung folgt der
  // Registry-Reihenfolge, und ein Einschub haette alle nachfolgenden Zahlen verschoben.
  { toolId: 'kontur', zone: 'fix', ordnung: 8, herkunft: 'registry', begruendung: GRUND_BAU },

  // --- Registry: die 2 Sofort-Aktionen ----------------------------------------------------------
  { toolId: 'loeschen', zone: 'kontext', ordnung: 1, herkunft: 'registry', begruendung: GRUND_SOFORT },
  { toolId: 'duplizieren', zone: 'kontext', ordnung: 2, herkunft: 'registry', begruendung: GRUND_SOFORT },

  // --- Katalog: die 110 Fach-Werkzeuge aus Yamas Paket (I2, AUF-21) -----------------------------
  // Alle in `weitere`: seit I4 ist jedes über seine Kategorie-Gruppe in der oberen Werkzeugleiste
  // erreichbar — `versteckt` hieße „bewusst nicht angeboten", und das stimmt nicht mehr. Die linke
  // Leiste bleibt trotzdem schlank: dort stehen nur `fix` und was der Nutzer anheftet.
  { toolId: 'direktauswahl', zone: 'weitere', ordnung: 1, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'rechteckauswahl', zone: 'weitere', ordnung: 2, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'lassoauswahl', zone: 'weitere', ordnung: 3, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verschieben', zone: 'weitere', ordnung: 4, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'primary', anheftbar: true },
  { toolId: 'drehen', zone: 'weitere', ordnung: 5, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'skalieren', zone: 'weitere', ordnung: 6, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'horizontal-spiegeln', zone: 'weitere', ordnung: 7, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'vertikal-spiegeln', zone: 'weitere', ordnung: 8, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kopieren', zone: 'weitere', ordnung: 9, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'sperren', zone: 'weitere', ordnung: 10, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'entsperren', zone: 'weitere', ordnung: 11, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'einblenden', zone: 'weitere', ordnung: 12, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'ausblenden', zone: 'weitere', ordnung: 13, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'gruppieren', zone: 'weitere', ordnung: 14, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'ausrichten', zone: 'weitere', ordnung: 15, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verteilen', zone: 'weitere', ordnung: 16, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'linie', zone: 'weitere', ordnung: 17, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'polylinie', zone: 'weitere', ordnung: 18, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'rechteck', zone: 'weitere', ordnung: 19, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'polygon', zone: 'weitere', ordnung: 20, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kreis', zone: 'weitere', ordnung: 21, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'bogen', zone: 'weitere', ordnung: 22, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'trimmen', zone: 'weitere', ordnung: 23, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verlaengern', zone: 'weitere', ordnung: 24, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'versatz', zone: 'weitere', ordnung: 25, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'teilen', zone: 'weitere', ordnung: 26, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verbinden', zone: 'weitere', ordnung: 27, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'raum', zone: 'weitere', ordnung: 28, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'gaube', zone: 'weitere', ordnung: 29, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'dachfenster', zone: 'weitere', ordnung: 30, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'stuetze', zone: 'weitere', ordnung: 31, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'unterzug', zone: 'weitere', ordnung: 32, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'oeffnung', zone: 'weitere', ordnung: 33, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'boden', zone: 'weitere', ordnung: 34, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'schnitt', zone: 'weitere', ordnung: 35, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'aufriss', zone: 'weitere', ordnung: 36, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'vergroessern', zone: 'weitere', ordnung: 37, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verkleinern', zone: 'weitere', ordnung: 38, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'alles-anzeigen', zone: 'weitere', ordnung: 39, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'hand', zone: 'weitere', ordnung: 40, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'umkreisen', zone: 'weitere', ordnung: 41, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'raster', zone: 'weitere', ordnung: 42, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'fang', zone: 'weitere', ordnung: 43, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'distanz-messen', zone: 'weitere', ordnung: 44, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'bemassen', zone: 'weitere', ordnung: 45, herkunft: 'registry', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'winkel-messen', zone: 'weitere', ordnung: 46, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'flaeche-messen', zone: 'weitere', ordnung: 47, herkunft: 'registry', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'volumen-messen', zone: 'weitere', ordnung: 48, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'datei-importieren', zone: 'weitere', ordnung: 49, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'primary', anheftbar: true },
  { toolId: 'bild-importieren', zone: 'weitere', ordnung: 50, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kalibrieren', zone: 'weitere', ordnung: 51, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'beschneiden', zone: 'weitere', ordnung: 52, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'nordrichtung-setzen', zone: 'weitere', ordnung: 53, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'grundriss-erkennen', zone: 'weitere', ordnung: 54, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'ki-assistent', zone: 'weitere', ordnung: 55, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'erkennung-bestaetigen', zone: 'weitere', ordnung: 56, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'material-zuweisen', zone: 'weitere', ordnung: 57, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'textur', zone: 'weitere', ordnung: 58, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'material-aufnehmen', zone: 'weitere', ordnung: 59, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'fassadensystem', zone: 'weitere', ordnung: 60, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'klinker', zone: 'weitere', ordnung: 61, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'daemmung', zone: 'weitere', ordnung: 62, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'u-wert', zone: 'weitere', ordnung: 63, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'thermische-huelle', zone: 'weitere', ordnung: 64, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'lueftung', zone: 'weitere', ordnung: 65, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'heizkoerper', zone: 'weitere', ordnung: 66, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'fussbodenheizung', zone: 'weitere', ordnung: 67, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'rohrleitung', zone: 'weitere', ordnung: 68, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'pumpe', zone: 'weitere', ordnung: 69, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'waermepumpe', zone: 'weitere', ordnung: 70, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'hydraulischer-abgleich', zone: 'weitere', ordnung: 71, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'sanitaeranschluss', zone: 'weitere', ordnung: 72, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'badewanne', zone: 'weitere', ordnung: 73, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'dusche', zone: 'weitere', ordnung: 74, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'wc', zone: 'weitere', ordnung: 75, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kuechenplanung', zone: 'weitere', ordnung: 76, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'schrank', zone: 'weitere', ordnung: 77, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'geraet', zone: 'weitere', ordnung: 78, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'elektroplanung', zone: 'weitere', ordnung: 79, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'steckdose', zone: 'weitere', ordnung: 80, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'schalter', zone: 'weitere', ordnung: 81, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'leuchte', zone: 'weitere', ordnung: 82, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'verteiler', zone: 'weitere', ordnung: 83, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'pv-modul', zone: 'weitere', ordnung: 84, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'batteriespeicher', zone: 'weitere', ordnung: 85, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'wallbox', zone: 'weitere', ordnung: 86, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'assistent', zone: 'weitere', ordnung: 87, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'prozessuebersicht', zone: 'weitere', ordnung: 88, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'uebergabepaket', zone: 'weitere', ordnung: 89, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'freigeben', zone: 'weitere', ordnung: 90, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'kommentar', zone: 'weitere', ordnung: 91, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'historie', zone: 'weitere', ordnung: 92, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'revision', zone: 'weitere', ordnung: 93, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'pruefen', zone: 'weitere', ordnung: 94, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'warnungen', zone: 'weitere', ordnung: 95, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'fehler', zone: 'weitere', ordnung: 96, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'einstellungen', zone: 'weitere', ordnung: 97, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'suche', zone: 'weitere', ordnung: 98, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'befehlspalette', zone: 'weitere', ordnung: 99, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'exportieren', zone: 'weitere', ordnung: 100, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
  { toolId: 'pdf', zone: 'weitere', ordnung: 101, herkunft: 'katalog', begruendung: GRUND_PAKET , prioritaet: 'secondary', anheftbar: true },
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
