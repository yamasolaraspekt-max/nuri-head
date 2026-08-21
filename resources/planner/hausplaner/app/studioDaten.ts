/**
 * Studio-Daten & Design-Tokens (v9-Synthese). Reine Daten/Konstanten — keine Logik, kein Rendern.
 * Tokens aus dem v9-Entwurf: Akzent Teal für Rahmen/Navigation, Marke Grün für Primäraktionen.
 */

export const T = {
  bg: '#f5f7f8', surface: '#ffffff', surface2: '#fafbfc',
  ink: '#232a31', muted: '#697079', faint: '#a7aeb7',
  controlBorder: '#8b949e', hair: '#edf0f2', hair2: '#f2f4f6',
  accent: '#12807d', accentSoft: '#e6f2f1', accentInk: '#0c5f5d',
  brand: '#7fae1c', brandSoft: '#f4f8e9', brandWash: 'rgba(127,174,28,0.12)',
  brandGhost: 'rgba(127,174,28,0.06)', brandInk: '#496700',
  ok: '#1a9e5f', okSoft: '#e6f5ec', okInk: '#137a49', okBorder: '#bbf7d0',
  info: '#2f6df0', infoSoft: '#e9f0fd', infoInk: '#2358c9',
  warn: '#d98218', warnSoft: '#fdf2e3', warnInk: '#9c5c0d',
  err: '#d24b3e', errSoft: '#fef2f2', errInk: '#b91c1c', errBorder: '#fecaca',
  canvasGrid: '#eef0f2', canvasGridStrong: '#e2e4e7',
  canvasWall: '#374151', canvasWallFill: '#4b5563', canvasWallGhost: 'rgba(55,65,81,0.06)',
  materialWood: '#b08968',

  // AUF-56 — **zwei Elevation-Rollen, wertgleich Zeichen für Zeichen.**
  //
  // Eine Rolle ist die ganze Aussage „wie hoch schwebt diese Fläche", nicht nur ihre Farbe: Versatz,
  // Weichzeichnung und Farbwert gehören zusammen. Ein reiner Farb-Token liesse die Geometrie
  // weiterhin an jeder Stelle einzeln stehen — die zweite Wahrheit wäre nur kleiner geworden.
  //
  // **Was NICHT angeglichen wird:** die rund acht „nah dran"-Werte und die selteneren Schatten
  // (`.18`, `.30`, der weisse). Ihre Angleichung waere eine **sichtbare** Farbaenderung und bleibt
  // Yamas Entscheidung. Ein Token fuer einen einzigen Aufruf ist ausserdem keine Rolle, sondern eine
  // Umbenennung.
  /**
   * Ruhende Flaeche: Karte, Plakette, Panel.
   *
   * **Hier stand "10 Fundstellen am 26.07." — die Zahl ist abgelaufen, und ihr Bruch ist
   * dokumentiert:** AUF-38 Scheibe 2 hat einen Teil der Verwendungen in die Stilschicht
   * verschoben (`var(--hp-schatten-flach)`). Dieselbe Verschiebung hat schon die ZUSAGE in
   * `__tests__/elevationTokens.test.ts` rot gemacht; sie wurde daraufhin von der ANZAHL auf die
   * EIGENSCHAFT umgestellt ("die Rolle wird benutzt, der Rohwert nirgends") — dieser Kommentar
   * wurde dabei uebersehen und trug die alte Zahl weiter.
   *
   * **Deshalb steht hier die Eigenschaft und der Messbefehl, nicht mehr die nackte Zahl.**
   * Eine Anzahl bricht bei jeder Verschiebung; die Eigenschaft haelt:
   *
   *     Rolle in TS/TSX:  T.schattenFlach   (ohne Kommentare, ohne diese Datei)
   *     Rolle in der CSS: var(--hp-schatten-flach)
   *     Rohwert rgba(...) darf an KEINER der beiden Stellen stehen — das ist die Zusage.
   *
   * Gemessen 21.08.2026 mit der Methode des Waechters: **5** in TS/TSX, **7** in der CSS.
   * Die Zahlen sind eine Momentaufnahme und kein Versprechen; verbindlich ist die Zusage.
   */
  schattenFlach: '0 1px 2px rgba(28,40,48,.05)',
  /**
   * Angehobene Flaeche: Karte unter dem Zeiger.
   *
   * Hier stand "3 Fundstellen am 26.07."; nachgemessen 21.08.2026 sind es **weiterhin 3** in
   * TS/TSX und **0** in der CSS. Die Zahl stimmt also noch — sie wird trotzdem ersetzt, denn
   * dass sie zufaellig gehalten hat, macht sie nicht haltbarer als ihre Nachbarin darueber.
   * Es gilt dieselbe Zusage: Rolle benutzt, Rohwert nirgends.
   */
  schattenGehoben: '0 10px 34px rgba(28,50,55,.10)',
} as const;

/**
 * AUF-54 — **die Farben der Treppen-Grundrisszeichnung, hier statt in `geometry/`.**
 *
 * Yamas Entscheidung vom 25.07.: *„Farbe als Parameter."* Vorher standen diese sechs Werte roh in
 * `geometry/treppeSvg.ts` — eine reine Geometrie-Datei, die über Aussehen entschied.
 *
 * **Wert- und verhaltenstreu:** jeder der sechs Werte ist zeichengleich der, der vorher gerendert
 * wurde. Dieser Posten verschiebt **Herkunft, nicht Aussehen**; der Beweis ist ein Byte-Vergleich
 * des erzeugten SVG über vier Treppenarten.
 *
 * **Warum hier rohe Werte und keine `T`-Rollen:** zwei der sechs sind zeichengleich mit
 * vorhandenen Rollen (`umriss` = `T.canvasWall`, `bg` = `T.surface`) — vier haben **keine**. Sie
 * an `T` zu binden hieße, für vier Farben neue Rollen zu erfinden oder sie an fremde anzugleichen,
 * und **Angleichung ist ausdrücklich Yamas Entscheidung** (Auftrag AUF-56 §„nicht zu bauen").
 * Die zwei Überschneidungen sind gemessen und im Test festgehalten, damit die Rollen-Entscheidung
 * später auf Zahlen trifft und nicht auf Erinnerung.
 *
 * **`lauflinie` ist das dritte Grün für dieselbe Rolle** (`#93c21c` hier · `#7fae1c` als `brand` ·
 * `0xa3e635` in `szene.ts`). Es bleibt vorerst, was es ist: **ob die Lauflinie überhaupt
 * Markenfarbe tragen soll, ist nicht entschieden** — und ein stiller Tausch wäre genau die
 * sichtbare Änderung, die dieser Posten nicht machen darf.
 */
export const TREPPE_FARBEN = {
  umriss: '#374151',
  stufe: '#9ca3af',
  lauflinie: '#93c21c',
  text: '#6b7280',
  rahmen: '#e5e7eb',
  bg: '#ffffff',
} as const;

/**
 * AUF-48 Scheibe 4a — **die Rollen-Farben, umgezogen aus `HausplanerApp.tsx`.**
 *
 * Sie standen dort auf Modulebene und wurden 77-mal benutzt. Der Kopfrahmen braucht zwei davon
 * (`auswahl`, `gedaempft`); importierte er sie aus `HausplanerApp`, entstünde ein Ringschluss —
 * die Hauptfunktion importiert ja den Kopfrahmen. **Umgezogen, nicht verdoppelt:** es gibt
 * weiterhin genau eine Definition, und sie steht jetzt dort, wo `T` und `TREPPE_FARBEN` schon
 * stehen. *Kein Wert hat sich geändert.*
 */
export const FARBEN = {
  text: T.ink, gedaempft: T.muted, linie: T.faint, raster: T.canvasGrid, rasterGrob: T.canvasGridStrong,
  wand: T.canvasWall, wandFuellung: T.canvasWallFill, auswahl: T.brand, raum: T.brandGhost,
  warnung: T.warnInk, gefahr: T.errInk, erfolg: T.okInk,
} as const;

/**
 * AUF-48 Scheibe 4a — **die Token-Tabelle der Bedienknöpfe, ebenfalls umgezogen.**
 *
 * `opKnopfBild` (rein, getestet) nennt **Token-Namen**, keine Farben — das ist Absicht und bleibt
 * so. Erst diese Tabelle macht daraus Farben. Sie hatte zwei Nutzer: `opStil` (mit der
 * Bedien-Werkzeugleiste in den Kopfrahmen gezogen) und `knopf()` (bleibt in der Hauptfunktion).
 * **Zwei Nutzer, eine Tabelle** — die Alternative wäre eine zweite Wahrheit über dieselben sechs
 * Farben gewesen. *Kein Wert hat sich geändert.*
 */
export const OP_TOKEN: Record<string, string> = {
  brandInk: T.brandInk, brandWash: T.brandWash, surface: T.surface, hair2: T.hair2,
  ink: FARBEN.text, faint: T.faint,
};

export type StudioModus = 'start' | 'guided' | 'expert';

export interface FachHub {
  name: string;
  hub?: boolean;
  desc: string;
  /** Icon-Pfad(e) für ein 24er-viewBox-SVG (nur d/rect-Inhalt). */
  icon: string;
  /** Untermodule (nur Hubs). [Label, istFensterKonfig?]. */
  sub?: Array<[string, boolean?]>;
}

/** Fachplaner in Yamas Reihenfolge; Haustechnik & PV-Planer sind Hubs mit Untermodulen. */
export const FACH: readonly FachHub[] = [
  {
    name: 'Haustechnik', hub: true, desc: 'Wärme, Luft, Klima, Flächenheizung, Auslegung & Contracting',
    icon: '<path d="M8 3v18M12 3v18M16 3v18"/>',
    sub: [['Heizung'], ['Heizlastberechnung'], ['Lüftung'], ['Klima'], ['Wärmepumpe'], ['Heizkörper'], ['Fußbodenheizung'], ['Wärme-Contracting']],
  },
  {
    name: 'PV-Planer', hub: true, desc: 'Photovoltaik, Speicher, Ladeinfrastruktur & Energiedienste',
    icon: '<rect x="3" y="4" width="18" height="12" rx="1"/><path d="M3 8h18M3 12h18M9 4v12M15 4v12"/>',
    sub: [['PV-Module'], ['Speicherauslegung'], ['Wallbox'], ['Carport'], ['Zaun'], ['Freiland'], ['HEMS'], ['Messstellenbetrieb'], ['dynamischer Stromtarif'], ['Mietstrom']],
  },
  {
    name: 'Bauelemente', hub: true, desc: 'Fenster und Türen',
    icon: '<rect x="4" y="4" width="16" height="16" rx="1"/><path d="M12 4v16M4 12h16"/>',
    sub: [['Fenster', true], ['Tür']],
  },
  {
    name: 'Bad', desc: 'Sanitär, Vorwand, Fliesen — ein Raum genügt',
    icon: '<path d="M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4z"/><path d="M6 12V6a2 2 0 0 1 4 0"/>',
  },
  {
    name: 'Küche', desc: 'Form, Schränke, Geräte, Anschlüsse',
    icon: '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 15h18M8 3v6"/>',
  },
];

export interface ProjektEintrag { name: string; icon: string; }
export const PROJ: readonly ProjektEintrag[] = [
  { name: 'Sanierungsplan', icon: '<path d="M3 21h18M6 21V11l6-4 6 4v10"/><path d="M9 21v-5h6v5"/>' },
  { name: 'Hausplaner', icon: '<path d="M3 21h18M5 21V8l7-4 7 4v13"/>' },
  { name: 'Weiterarbeiten', icon: '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/>' },
];

export interface ZuletztEintrag { name: string; meta: string; icon: string; goto?: number; win?: boolean; }
/**
 * AUF-40 Teil A — **stillgelegt, nicht gelöscht** (Muster `toolCatalogStillgelegt.ts`,
 * `STEPS_STILLGELEGT`).
 *
 * Diese drei Einträge erschienen bei **jedem** Nutzer, auch beim allerersten Start, auch ohne ein
 * einziges eigenes Projekt: „EFH Mustermann", „Fenster-Angebot Hahn", „Sanierung Musterstr. 5".
 * **Ein Startbildschirm, der fremde Projekte zeigt, ist keine Vorschau — er ist eine Falschauskunft
 * über den eigenen Bestand.**
 *
 * Sie bleiben als Beleg dessen stehen, was vorher behauptet wurde, und als Vergleichsgrundlage für
 * den Test. **Nichts rendert sie mehr.**
 *
 * **ÜBERHOLT (A-23, 13.08.), und nicht gelöscht.** *Hier stand: „Die echte Liste kommt aus dem
 * Bestand und braucht eine Route — das ist **Teil B** und liegt bei Yama."*
 * **Gemeint ist die PROJEKTLISTE, und sie ist gebaut (AUF-78):**
 * `HausplanerController.php:101` → `:55` → `objekt.blade.php:141` → `main.tsx:82`.
 * *Sie braucht keine Route — sie kommt über das Mount-Attribut, ohne Lade-Fetch (`:57`).*
 *
 * **Die zweite Hälfte, die Paketspeicherung, ist ebenfalls gebaut** (AUF-81, Tor 1, 26.07.):
 * `web.php:5016/5018/5020` → `objekt.blade.php:144` → `main.tsx:89` → `paketSpeichern.ts:45` →
 * benutzt in `ConfigWizard.tsx:255`. *Dort GIBT es eine Route — sie wird nur als Attribut gereicht
 * statt in der Insel gebaut.*
 */
export const ZULETZT_STILLGELEGT: readonly ZuletztEintrag[] = [
  { name: 'EFH Mustermann', meta: 'Rev. 42 · Schritt 2/11', icon: '<path d="M3 21h18M5 21V8l7-4 7 4v13"/>', goto: 1 },
  { name: 'Fenster-Angebot Hahn', meta: 'ConfiguratorPackage · gestern', icon: '<rect x="4" y="4" width="16" height="16" rx="1"/><path d="M12 4v16M4 12h16"/>', win: true },
  { name: 'Sanierung Musterstr. 5', meta: 'Rev. 12 · vor 3 Tagen', icon: '<path d="M3 21h18M6 21V11l6-4 6 4v10"/>', goto: 1 },
];

export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';
export interface Pruefpunkt { status: SchrittStatus; text: string; }
export interface Aufgabe { warn?: boolean; titel: string; detail?: string; }
export interface Empfehlung { titel: string; aktion: string; cfg?: boolean; }
export interface Fahrschritt {
  titel: string;
  status: SchrittStatus;
  hinweis: string;
  checks: Pruefpunkt[];
  aufgaben: Aufgabe[];
  empfehlung: Empfehlung | null;
}

/**
 * L5 (AUF-39) — **STILLGELEGT.** Diese elf Schritte waren „präsentativ": sie behaupteten Tatsachen
 * über ein Projekt, das der Nutzer gerade erst angelegt hatte — „Bauherr & Adresse ✓", „Maßstab
 * erkannt · 1:50 ✓" in einem leeren Dokument. Seit AUF-39 leitet `dashboard/fahrschritte.ts` die
 * Schritte aus dem `SceneDocument` ab.
 *
 * **Nicht gelöscht, sondern stillgelegt** (Muster `toolCatalogStillgelegt.ts`): die Demo-Daten
 * bleiben als Beleg dafür, was vorher behauptet wurde — und als Vergleichsgrundlage für den Test,
 * der die elf Titel byte-genau gegenprüft. **Nichts rendert sie mehr.**
 */
export const STEPS_STILLGELEGT: readonly Fahrschritt[] = [
  {
    titel: 'Projektgrundlagen', status: 'ok', hinweis: 'Projektstammdaten sind erfasst und freigegeben.',
    checks: [{ status: 'ok', text: 'Bauherr & Adresse' }, { status: 'ok', text: 'Grundstück & Ausrichtung' }],
    aufgaben: [{ titel: 'Abgeschlossen', detail: 'Nichts zu tun.' }], empfehlung: null,
  },
  {
    titel: 'Import oder Grundriss', status: 'prog', hinweis: 'Bestätige den erkannten Maßstab und prüfe die markierten Außenwände.',
    checks: [{ status: 'ok', text: 'Datei geladen (PDF)' }, { status: 'ok', text: 'Maßstab erkannt · 1:50' }, { status: 'warn', text: '4 Prüfstellen offen' }, { status: 'open', text: 'Modell freigeben' }],
    aufgaben: [{ warn: true, titel: 'Maßstab prüfen', detail: 'Zwei Kontrollmaße bestätigen.' }, { warn: true, titel: 'Außenwände prüfen', detail: '1 Wand unsicher erkannt.' }, { titel: 'Räume bestätigen', detail: '5 Räume erkannt.' }],
    empfehlung: { titel: 'Zu den 4 Prüfstellen führen', aktion: 'Weiter prüfen' },
  },
  {
    titel: 'Geschosse und Gebäude', status: 'open', hinweis: 'Geschosse anlegen — oder eines als Vorlage übernehmen.',
    checks: [{ status: 'ok', text: 'Erdgeschoss angelegt' }, { status: 'open', text: 'Obergeschoss aus Vorlage' }],
    aufgaben: [{ titel: 'Geschoss ergänzen', detail: 'OG aus dem EG ableiten.' }], empfehlung: { titel: 'Obergeschoss aus EG erzeugen', aktion: 'Duplizieren' },
  },
  {
    titel: 'Fenster, Türen und Treppen', status: 'open', hinweis: 'Öffnung wählen und auf eine Wand setzen — Bauart zuerst.',
    checks: [{ status: 'ok', text: 'Haustür gesetzt' }, { status: 'open', text: 'Fenster Wohnen' }, { status: 'open', text: 'Treppe ins OG' }],
    aufgaben: [{ titel: 'Fenster konfigurieren', detail: 'Bauart → Maße → Material → Prüfung.' }], empfehlung: { titel: 'Fenster-Konfigurator öffnen', aktion: 'Konfigurieren', cfg: true },
  },
  {
    titel: 'Dach und Fassade', status: 'open', hinweis: 'Dachform wählen — Flächen aus dem Umriss.',
    checks: [{ status: 'open', text: 'Dachform wählen' }, { status: 'open', text: 'PV-Fläche prüfen' }],
    aufgaben: [{ titel: 'Dach aufsetzen', detail: 'Kontur → Form → Neigung.' }], empfehlung: { titel: 'Dach aus Vorlage aufsetzen', aktion: 'Vorlage' },
  },
  {
    titel: 'Räume und Einrichtung', status: 'open', hinweis: 'Räume benennen, Nutzung zuweisen, möblieren.',
    checks: [{ status: 'open', text: 'Raumnutzung zuweisen' }, { status: 'open', text: 'Möblierung' }],
    aufgaben: [{ titel: 'Nutzung festlegen', detail: 'Basis für Elektro/TGA.' }], empfehlung: null,
  },
  {
    titel: 'Küche und Bad', status: 'open', hinweis: 'Konfigurator erzeugt Anschlüsse für Elektro & TGA gleich mit.',
    checks: [{ status: 'open', text: 'Küche konfigurieren' }, { status: 'open', text: 'Bad konfigurieren' }],
    aufgaben: [{ titel: 'Küche planen', detail: 'Erzeugt Anschluss-Anforderungen.' }], empfehlung: null,
  },
  {
    titel: 'Elektro', status: 'warn', hinweis: 'Prüfe die automatisch erzeugten Vorschläge — 3 Objekte ohne Stromkreis.',
    checks: [{ status: 'ok', text: 'Raumprofile festgelegt' }, { status: 'ok', text: 'Schalter automatisch' }, { status: 'ok', text: 'Steckdosen automatisch' }, { status: 'warn', text: 'Medienbereich Wohnzimmer prüfen' }, { status: 'warn', text: '3 Objekte ohne Stromkreis' }, { status: 'open', text: 'Verteiler freigeben' }],
    aufgaben: [{ warn: true, titel: 'Medienwand prüfen', detail: 'TV/Netzwerk bestätigen.' }, { warn: true, titel: 'Stromkreise', detail: '3 Objekte zuordnen.' }], empfehlung: { titel: 'Zu den offenen Prüfungen', aktion: 'Prüfen' },
  },
  {
    titel: 'TGA', status: 'open', hinweis: 'Systeme wählen, Stränge/Schächte festlegen, Routing erzeugen.',
    checks: [{ status: 'open', text: 'Systeme wählen' }, { status: 'open', text: 'Routing prüfen' }],
    aufgaben: [{ titel: 'Sanitär/Heizung', detail: 'Anschlüsse übernehmen.' }], empfehlung: null,
  },
  {
    titel: 'Prüfung und Koordination', status: 'open', hinweis: 'Gewerkeübergreifende Kollisions- und Plausibilitätsprüfung.',
    checks: [{ status: 'open', text: 'Kollisionsprüfung' }, { status: 'open', text: 'Freigaben sammeln' }],
    aufgaben: [{ titel: 'Gesamtprüfung', detail: 'Alle Gewerke gegeneinander.' }], empfehlung: null,
  },
  {
    titel: 'Dokumentation und Rendering', status: 'open', hinweis: 'Pläne, Stücklisten und Renderings erzeugen.',
    checks: [{ status: 'open', text: 'Planset' }, { status: 'open', text: 'Renderings' }],
    aufgaben: [{ titel: 'Ausgabe', detail: 'Grundrisse, Schnitte, Listen, 3D.' }], empfehlung: null,
  },
];

/**
 * AUF-65 — die Statuswörter beschreiben einen **Zustand**, sie behaupten keinen **Vorgang**.
 *
 * `ok` trug hier ein Wort aus der Freigabe-Sprache. Es behauptet einen Vorgang, den es nicht
 * gegeben hat — **niemand hat etwas geprüft und bestätigt.** Der Wert wird aus dem Dokument abgeleitet (`dashboard/fahrschritte.ts`) und heißt „alle
 * Prüfpunkte dieses Schrittes sind erfüllt". Genau das sagt jetzt auch die Beschriftung.
 *
 * **Die Schlüssel bleiben unverändert** (`ok`/`prog`/`warn`/`open`) — geändert ist das Wort, nicht
 * der Wert. Die drei übrigen Beschriftungen beschreiben bereits Zustände und bleiben, wie sie sind.
 */
export const STATUS_LABEL: Record<SchrittStatus, string> = {
  ok: 'Vollständig', prog: 'In Bearbeitung', warn: 'Prüfung erforderlich', open: 'Offen',
};
