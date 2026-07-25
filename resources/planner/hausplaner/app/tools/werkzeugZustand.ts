/**
 * I3 (AUF-21) — die sechs Werkzeug-Zustände der Leiste als **reine Regel**.
 *
 * Sie stammen wörtlich aus Yamas Entwurf `dashboard-tools-v1.html`, Legende „Werkzeug-Zustände":
 *
 * | Zeichen | Zustand | Bedeutung laut Entwurf |
 * |---|---|---|
 * | ★ | `angeheftet` | persönlich, bleibt sichtbar |
 * | — | `empfohlen`  | vom Wizard empfohlen, temporär im Kontext |
 * | ▶ | `aktiv`      | aktuell gewähltes Werkzeug |
 * | ◌ | `gesperrt`   | Voraussetzung fehlt — der Grund steht im Tooltip |
 * | ⋯ | `weitere`    | im Überlauf, per Befehlspalette erreichbar |
 * | ⌂ | `system`     | Pflichtwerkzeug, nicht entfernbar |
 *
 * **Warum eine Funktion und kein Feld:** Vier der sechs Zustände hängen vom *Moment* ab — welches
 * Werkzeug gerade gewählt ist, was der Wizard empfiehlt, ob die Voraussetzung erfüllt ist. Ein
 * gespeicherter Zustand wäre sofort veraltet und die zweite Wahrheit neben `resolveToolState`.
 * Diese Funktion **liest** die vorhandenen Wahrheiten und leitet daraus die Anzeige ab; sie fragt
 * nirgends nach und speichert nichts.
 *
 * **Was hier NICHT entschieden wird:** wo die persönlichen Anheftungen liegen. Die Funktion nimmt
 * sie als Parameter entgegen. Ob sie im UI-State, im Store oder am Benutzer in der Datenbank
 * hängen, ist eine Architektur- und Datenschutzfrage — sie gehört Yama, nicht diesem Posten
 * (Operanden-Gate: kein erfundener Speicherort).
 */
import type { ToolDefinition, WerkzeugZustand } from './toolTypes';
import { praesentation } from './toolPresentation';

/** Die sechs Anzeigezustände aus dem Entwurf. */
export type WerkzeugAnzeige = 'system' | 'aktiv' | 'gesperrt' | 'angeheftet' | 'empfohlen' | 'weitere';

/** Zeichen je Zustand — wie im Entwurf. Nie nur Farbe: das Zeichen trägt die Bedeutung mit. */
export const ANZEIGE_ZEICHEN: Readonly<Record<WerkzeugAnzeige, string>> = {
  system: '⌂',
  aktiv: '▶',
  gesperrt: '◌',
  angeheftet: '★',
  empfohlen: '·',
  weitere: '⋯',
};

/** Klartext je Zustand — für Tooltip und Vorlesen; ein Zeichen allein ist keine Information. */
export const ANZEIGE_TEXT: Readonly<Record<WerkzeugAnzeige, string>> = {
  system: 'Pflichtwerkzeug — nicht entfernbar',
  aktiv: 'aktuell gewähltes Werkzeug',
  gesperrt: 'Voraussetzung fehlt — Grund im Tooltip',
  angeheftet: 'angeheftet — bleibt sichtbar',
  empfohlen: 'vom Wizard empfohlen — temporär im Kontext',
  weitere: 'im Überlauf — über die Befehlspalette erreichbar',
};

export interface ZustandKontext {
  /** id des gerade gewählten Werkzeugs. */
  aktivId: string | null;
  /** Persönlich angeheftete ids. Herkunft entscheidet Yama — hier nur Eingabe. */
  angeheftet: ReadonlySet<string>;
  /** Vom Wizard im aktuellen Schritt empfohlene ids. */
  empfohlen: ReadonlySet<string>;
  /** Ergebnis von `resolveToolState` für dieses Werkzeug. */
  aktivierung: WerkzeugZustand;
}

/**
 * Der Anzeigezustand eines Werkzeugs.
 *
 * **Reihenfolge ist bewusst und nicht beliebig:**
 * 1. `aktiv` schlägt alles — was der Nutzer gerade benutzt, muss er sehen.
 * 2. `gesperrt` schlägt **jeden** anderen Zustand: ein unbenutzbarer Knopf muss den **Grund**
 *    zeigen, nicht seinen Stern, seine Herkunft oder seine Zone.
 * 3. `system` vor `angeheftet`: ein Pflichtwerkzeug bleibt Pflichtwerkzeug, auch wenn es zusätzlich
 *    angeheftet ist — sonst verspricht der Stern eine Entfernbarkeit, die es nicht gibt.
 * 4. `angeheftet` vor `empfohlen`: die persönliche Entscheidung schlägt den Vorschlag des Wizards.
 * 5. sonst `weitere`.
 *
 * **AUF-36 hat hier einen Fehler sichtbar gemacht.** Bis dahin galt `gesperrt` nur für angeheftete
 * oder Pflichtwerkzeuge; ein Katalog-Werkzeug in der Zone `weitere` fiel trotz fehlender
 * Voraussetzung auf `weitere` durch — die Zeile las sich „in Entwicklung", obwohl das Werkzeug
 * gesperrt war. Folgenlos war das nur, solange Katalog-Werkzeuge **nie** gesperrt sein konnten
 * (keine Aktivierungsregeln). Seit der Funktionsvertrag Vorbedingungen liefert, können sie es —
 * und die Anzeige log. In der Sichtprobe: „Hydraulischer Abgleich" war ausgegraut und meldete
 * „in Entwicklung" statt „gesperrt: Dafür muss das Heiznetz verbunden sein."
 */
export function werkzeugAnzeige(tool: ToolDefinition, k: ZustandKontext): WerkzeugAnzeige {
  if (k.aktivId === tool.id) return 'aktiv';
  const regel = praesentation(tool.id);
  const istSystem = regel?.zone === 'fix' && regel.herkunft === 'registry';
  if (!k.aktivierung.enabled) return 'gesperrt';
  if (istSystem) return 'system';
  if (k.angeheftet.has(tool.id)) return 'angeheftet';
  if (k.empfohlen.has(tool.id)) return 'empfohlen';
  return 'weitere';
}

/**
 * Darf dieses Werkzeug angeheftet werden?
 * Pflichtwerkzeuge sind ohnehin dauerhaft sichtbar — sie anzuheften wäre ein Knopf ohne Wirkung.
 */
export function darfAngeheftetWerden(toolId: string): boolean {
  const regel = praesentation(toolId);
  if (!regel) return false;
  if (regel.zone === 'fix' && regel.herkunft === 'registry') return false;
  return regel.anheftbar === true;
}

/**
 * Pflichtwerkzeuge der Domäne: `prioritaet: 'primary'` aus dem Paket **oder** Fix-Zone der Registry.
 * Sie sind der Kern, den ein Nutzer nicht wegkonfigurieren kann.
 */
export function istPflichtwerkzeug(toolId: string): boolean {
  const regel = praesentation(toolId);
  if (!regel) return false;
  return (regel.zone === 'fix' && regel.herkunft === 'registry') || regel.prioritaet === 'primary';
}

/** Der Grund, der bei `gesperrt` sichtbar gehört — nie nur ein ausgegrautes Zeichen. */
export function sperrGrund(k: ZustandKontext): string | null {
  return k.aktivierung.enabled ? null : k.aktivierung.reason;
}
