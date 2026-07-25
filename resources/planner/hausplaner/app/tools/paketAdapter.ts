/**
 * I2 (AUF-21) — Adapter vom Werkzeugpaket auf `ToolDefinition`.
 *
 * **Richtung der Anpassung ist Vorschrift, nicht Geschmack** (Bauordnung, Konflikt-Regel): *„Bei
 * Struktur-Konflikt passt sich immer der neue Code dem Bestand an — nie umgekehrt."* Deshalb wird
 * das Paket **auf** `ToolDefinition` abgebildet; das Werkzeug-Datenmodell des CRM bleibt, wie es ist.
 * Kein Feld von `ToolDefinition` wird geändert, keins ergänzt.
 *
 * **Was der Adapter NICHT tut:**
 * - Er erfindet keine Aktivierungsregeln. Seit **AUF-36** trägt er welche ein — aber **keine
 *   erfundenen**: sie kommen als Daten aus dem Funktionsvertrag (`werkzeugVertrag.ts`) und werden
 *   über die Tabelle in `vorbedingungen.ts` übersetzt. Ausgewertet werden sie weiterhin
 *   ausschließlich von `resolveToolState` (A1-Guardrail: kein zweiter Mechanismus, insbesondere
 *   kein zweites `resolveDisabledReasons`). Nicht messbare Vorbedingungen erzeugen **keine** Regel.
 * - Er weist keine Zone zu. Wohin ein Werkzeug in der Leiste gehört, entscheidet
 *   `toolPresentation.ts` — und für die 110 entscheidet es **I3** anhand von `prioritaet`/`anheftbar`.
 * - Er vergibt keine kollidierenden Tastenkürzel (siehe unten).
 *
 * **Tastenkürzel — Auflage 1 der A2-Abnahme, hier baulich erfüllt.** Das Paket bringt Kürzel mit,
 * die kollidieren: paketintern `g`, `s` und `Ctrl/Cmd+K` je zweimal, dazu `V`, `W`, `R` und `Delete`,
 * die bereits die Registry belegt (Auswahl, Wand, Treppe, Löschen). Ein Kürzel, das zwei Werkzeuge
 * auslöst, ist schlimmer als keins. Der Adapter **lässt genau diese Kürzel weg** und weist sie über
 * `verworfeneKuerzel()` nach — sichtbar, nicht stillschweigend. Die betroffenen Werkzeuge behalten
 * alles andere; nur ihr Kürzel ist frei, bis jemand es bewusst vergibt.
 */
import type { ToolDefinition, ViewType, WorkspaceId } from './toolTypes';
import { PAKET_WERKZEUGE, type PaketWerkzeug } from './werkzeugPaket';
import { TOOL_DEFINITIONS } from './toolRegistry';
import { themaVonWerkzeug } from './werkzeugThemen';
import { bereichVonThema } from '../dashboard/arbeitsbereiche';
import { vertrag } from './werkzeugVertrag';
import { regelnFuer } from './vorbedingungen';

/** Kürzel, die die bestehende Registry bereits belegt (klein geschrieben). */
const REGISTRY_KUERZEL = new Set(
  TOOL_DEFINITIONS.map((t) => t.shortcut?.toLowerCase()).filter((s): s is string => Boolean(s)),
);

/** Kürzel, die im Paket selbst mehrfach vorkommen. */
const PAKET_DOPPELT = ((): Set<string> => {
  const zaehler = new Map<string, number>();
  for (const w of PAKET_WERKZEUGE) {
    if (!w.shortcut) continue;
    const k = w.shortcut.toLowerCase();
    zaehler.set(k, (zaehler.get(k) ?? 0) + 1);
  }
  return new Set([...zaehler].filter(([, n]) => n > 1).map(([k]) => k));
})();

/** Ist dieses Kürzel vergebbar, ohne eine Doppelbelegung zu erzeugen? */
export function kuerzelFrei(shortcut: string | undefined): boolean {
  if (!shortcut) return false;
  const k = shortcut.toLowerCase();
  return !REGISTRY_KUERZEL.has(k) && !PAKET_DOPPELT.has(k);
}

/**
 * Ansichten des Pakets (`2D`/`3D`) auf `ViewType` abbilden.
 * `split` zeigt 2D und 3D nebeneinander — ein Werkzeug, das in 2D arbeitet, arbeitet dort ebenfalls.
 * Deshalb erbt `split` die 2D-Zulassung. Leere Liste hieße „überall zulässig" und wäre eine
 * stillschweigende Ausweitung; sie entsteht hier nicht, weil jedes Paket-Werkzeug Ansichten nennt.
 */
function ansichten(w: PaketWerkzeug): ViewType[] {
  const v: ViewType[] = [];
  if (w.ansichten.includes('2D')) v.push('2d', 'split');
  if (w.ansichten.includes('3D')) v.push('3d');
  return v;
}

/**
 * AUF-34 — die Arbeitsbereiche eines Werkzeugs, **abgeleitet aus seinem Thema**.
 *
 * Bis hierher trugen alle 101 Paket-Werkzeuge `supportedWorkspaces: []` — der Adapter füllte das
 * Feld nie, also galt jedes Werkzeug überall. Jetzt bekommt es seine Bindung, aber **nicht aus einer
 * zweiten Tabelle**: Quelle ist ausschließlich die Themen-Bindung in `dashboard/arbeitsbereiche.ts`,
 * dieselbe, aus der auch die Leiste gefiltert wird. Sonst gäbe die Leiste eine andere Antwort als
 * `resolveToolState`, und der Nutzer sähe ein Werkzeug, das sich nicht benutzen lässt — oder
 * umgekehrt.
 *
 * **Leere Liste heißt weiterhin „überall gültig"** — die bestehende Bedeutung wird nicht geändert
 * (so liest sie `resolveToolState` seit UI-2), sie wird nur endlich benutzt. Ein durchgängiges Thema
 * liefert deshalb `[]`, kein Aufzählen aller fünf Bereiche: fünf Einträge zu pflegen, wo „überall"
 * gemeint ist, bricht beim sechsten Bereich.
 */
export function arbeitsbereicheVon(werkzeugId: string): WorkspaceId[] {
  const t = themaVonWerkzeug(werkzeugId);
  const bereich = t ? bereichVonThema(t.id) : undefined;
  return bereich ? [bereich] : [];
}

/** Ein Paket-Werkzeug auf die Bestandsform abbilden. */
export function paketZuTool(w: PaketWerkzeug): ToolDefinition {
  const kuerzel = kuerzelFrei(w.shortcut) ? w.shortcut : undefined;
  return {
    id: w.id,
    label: w.label,
    icon: w.icon,
    groupId: w.kategorie,
    art: 'werkzeug',
    supportedWorkspaces: arbeitsbereicheVon(w.id),
    supportedViews: ansichten(w),
    // AUF-36: die Vorbedingungen des Funktionsvertrags — als DATEN in die bestehende Engine.
    // `regelnFuer` lässt nicht messbare Vorbedingungen aus, statt sie zu raten (Operanden-Gate).
    activationRules: regelnFuer(vertrag(w.id)?.vorbedingungen ?? []),
    ...(kuerzel ? { shortcut: kuerzel } : {}),
    helpText: w.funktion,
    meaning: w.funktion,
    usageArea: w.einsatz,
    group: w.kategorie,
    tooltip: {
      title: w.label,
      body: w.funktion,
      usage: `Einsatzbereich: ${w.einsatz}`,
      ...(kuerzel ? { shortcut: kuerzel } : {}),
    },
  };
}

/** Alle 110 Paket-Werkzeuge in der Bestandsform. */
export const PAKET_ALS_TOOLS: readonly ToolDefinition[] = PAKET_WERKZEUGE.map(paketZuTool);

/**
 * Die Kürzel, die der Adapter bewusst NICHT übernommen hat — mit Grund.
 * Leerer Rückgabewert hieße: das Paket ist kollisionsfrei. Er ist es nicht, und das gehört sichtbar.
 */
export function verworfeneKuerzel(): Array<{ id: string; shortcut: string; grund: string }> {
  const raus: Array<{ id: string; shortcut: string; grund: string }> = [];
  for (const w of PAKET_WERKZEUGE) {
    if (!w.shortcut || kuerzelFrei(w.shortcut)) continue;
    const k = w.shortcut.toLowerCase();
    raus.push({
      id: w.id,
      shortcut: w.shortcut,
      grund: REGISTRY_KUERZEL.has(k)
        ? 'bereits von einem Registry-Werkzeug belegt'
        : 'im Paket doppelt vergeben',
    });
  }
  return raus;
}

/** `prioritaet`/`anheftbar` bleiben am Paket — I3 liest sie von dort. */
export function paketWerkzeug(id: string): PaketWerkzeug | undefined {
  return PAKET_WERKZEUGE.find((w) => w.id === id);
}

/**
 * AUF-31 — **die harte Grenze zwischen Anzeige und Speicherung.**
 *
 * Die Eindeutschung betrifft **UI-ID · Icon-Dateiname · Label**. Sie betrifft **NICHT** die im
 * Szenendokument gespeicherten Werte (`type`, `objectType`, `zoneType`, `routeType`). Diese stehen
 * in `scene-document-v2.schema.json`, werden vom PHP-Validator gelesen und liegen in bereits
 * gespeicherten Szenen. Sie umzubenennen wäre eine **Migration an Bestandsdaten** — DAUERDIREKTIVE,
 * niemals Beifang — und erzeugte sonst **422 beim Speichern**.
 *
 * Diese Tabelle bildet die deutsche UI-ID auf den englischen Schutzwert ab. Sonderfälle aus der
 * führenden Namenstabelle: Paket `slab` → Schema **`ceiling`**, Paket `stairs` → Schema **`stair`**
 * (das Schema gewinnt). Wo ein Werkzeug zwei Schutzwerte bedienen kann (Wärmepumpe innen/außen,
 * Rohrleitung Heizung/Wasser), steht der **erste** — die Auswahl trifft die Fachlogik, nicht diese
 * Abbildung.
 */
export const SCHEMA_SCHUTZWERT: Readonly<Record<string, { feld: string; wert: string }>> = {
  'batteriespeicher': { feld: 'objectType', wert: 'battery' },
  'dach': { feld: 'type', wert: 'roof' },
  'decke': { feld: 'type', wert: 'ceiling' },
  'fenster': { feld: 'type', wert: 'window' },
  'fussbodenheizung': { feld: 'zoneType', wert: 'underfloor_heating' },
  'heizkoerper': { feld: 'objectType', wert: 'radiator' },
  'oeffnung': { feld: 'type', wert: 'opening' },
  'pv-modul': { feld: 'zoneType', wert: 'pv_area' },
  'raum': { feld: 'zoneType', wert: 'room' },
  'rohrleitung': { feld: 'routeType', wert: 'heating_pipe' },
  'sanitaeranschluss': { feld: 'objectType', wert: 'sanitary' },
  'treppe': { feld: 'objectType', wert: 'stair' },
  'tuer': { feld: 'type', wert: 'door' },
  'waermepumpe': { feld: 'objectType', wert: 'heat_pump_indoor' },
  'wallbox': { feld: 'objectType', wert: 'wallbox' },
  'wand': { feld: 'type', wert: 'wall' },
};

/** Der gespeicherte Schema-Wert zu einer deutschen UI-ID — oder undefined, wenn nichts gespeichert wird. */
export function schemaSchutzwert(uiId: string): { feld: string; wert: string } | undefined {
  return SCHEMA_SCHUTZWERT[uiId];
}
