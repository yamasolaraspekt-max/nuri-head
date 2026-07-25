/**
 * L4 (Fahrplan §3) — die Fachplaner-Untermodule als DATEN.
 *
 * Warum ein eigenes, reines Modul und nicht Markup im Studio: bis heute endeten die
 * Fachplaner-Untermodule in `zeigeToast('… Konfigurator folgt.')` — Klicks ins Nichts. Damit die
 * Ersatz-Flächen prüfbar sind (Zahl, Eindeutigkeit, Zustand, Zwecktext, Engine-Bezug) liegen sie
 * hier als Daten, genau wie `panelTabs.ts` (Batch 1) und `projektBaum.ts` (Batch 2). Kein React
 * zur Laufzeit: `StudioZustand` und `KonfigArt` kommen als `import type` und werden beim
 * Übersetzen entfernt.
 *
 * Yamas stehende Regel „erst Layout fertig, auch wenn die Funktion nicht programmiert ist" gilt
 * hier wörtlich: die Fläche darf ohne Rechnung dastehen — sie muss ihren Zustand aber aussprechen
 * (`zustand: 'in_entwicklung'`, Badge in der Fläche) und sagen, was dort einmal entsteht (`zweck`).
 *
 * EINE Wahrheit, kein zweiter Modulbegriff:
 *  - die Modulnamen sind die aus `studioDaten.FACH` (Navigation + Start-Hubs) — `fehlendeFlaechen()`
 *    und `verwaisteFlaechen()` verriegeln beide Richtungen gegen Drift;
 *  - `KONFIGURATOR_NAMEN` ist die einzige Stelle, die sagt, welches Modul schon einen echten
 *    Konfigurator hat. `HausplanerStudio` liest sie, statt die Namen erneut zu erwähnen. Damit ist
 *    Kante 3 („ein Modul mit Konfigurator bekommt KEINE L4-Fläche") baulich erfüllt: was hier steht,
 *    steht nicht in `FACH_FLAECHEN`;
 *  - `engine` verweist auf die id einer der 13 fertigen Rechen-Engines aus `tools/faehigkeiten.ts`.
 *    Die Feldstruktur wird NICHT erfunden, sondern trägt in `typ` den echten Eingangs-/Ausgangs-
 *    Namen der Engine, damit L2/L3 die Fläche nur noch verdrahten muss. Der Test verriegelt das.
 *
 * NICHT in diesem Modul: Rechnen. Es gibt keine Formel, keinen Default-Wert und keinen
 * Vorschlagswert — das Automatisierungs-Prinzip (kein erfundener Operand) bleibt unangetastet.
 */
import { FACH } from '../studioDaten';
import type { StudioZustand } from '../studioUi';
import type { KonfigArt } from '../ConfigWizard';

/** Navigationsgruppe der Fläche. Entspricht dem Hub in `FACH`; Direkt-Module stehen unter „Fachplaner". */
export type FachGruppe = 'Haustechnik' | 'PV-Planer' | 'Fachplaner';

/** Woher der Nutzer die Fläche geöffnet hat — bestimmt die Beschriftung des Zurück-Wegs (Kante 2). */
export type FlaechenHerkunft = 'start' | 'navi' | 'guided';

/** Beschriftung des Zurück-Wegs je Herkunft. Nie pauschal „zur Startseite". */
export const HERKUNFT_ZURUECK: Readonly<Record<FlaechenHerkunft, string>> = {
  start: 'Zurück zur Übersicht',
  navi: 'Zurück zur Navigation',
  guided: 'Zurück zur geführten Planung',
};

/**
 * Ein Feld der Struktur-Vorschau — Eingangsgröße oder Ergebniszeile.
 * `label` ist die Beschriftung, `einheit` die Werteform, `typ` (nur bei angeschlossener Engine) der
 * ECHTE Eingangs-/Ausgangsname der Engine aus `faehigkeiten.ts`.
 */
export interface FeldVorschau {
  label: string;
  einheit?: string;
  typ?: string;
}

export interface FachFlaeche {
  /** Eindeutiger Schlüssel. Bewusst NICHT aus dem Label abgeleitet — Labels tragen ß, Umlaute,
   *  Bindestriche und Kleinschreibung („dynamischer Stromtarif"). Kante 1. */
  id: string;
  /** Modulname exakt wie in `studioDaten.FACH` — sonst findet der Klick seine Fläche nicht. */
  label: string;
  gruppe: FachGruppe;
  /** Ein Satz, was hier entsteht. Konkret, im Futur, kein Blindtext. */
  zweck: string;
  eingaenge: FeldVorschau[];
  ausgaenge: FeldVorschau[];
  zustand: StudioZustand;
  /** id der Rechen-Engine aus `tools/faehigkeiten.ts`, die diese Fläche in L2/L3 bedient. */
  engine?: string;
}

/**
 * Module, die HEUTE schon einen echten Konfigurator öffnen (`ConfigWizard`). Sie bekommen keine
 * L4-Fläche — sonst zwei Wahrheiten für dieselbe Aktion (Kante 3).
 */
export const KONFIGURATOR_NAMEN: Readonly<Record<string, KonfigArt | undefined>> = {
  Fenster: 'fenster',
  Tür: 'tuer',
  Treppe: 'treppe',
  Heizkörper: 'heizkoerper',
};

/** Grund, warum jedes Feld deaktiviert ist. Steht sichtbar in der Fläche, nicht nur als Tooltip (Kante 4). */
export const GRUND_DEAKTIVIERT =
  'Alle Felder sind deaktiviert: die Rechnung ist noch nicht angeschlossen. Die Struktur zeigt, welche Angaben später gebraucht werden — sie nimmt keine Werte entgegen und rechnet nichts.';

/** Zusatz für Flächen mit bereits fertiger Engine — sagt, was verdrahtet wird. */
export const HINWEIS_ENGINE =
  'Die Rechnung dahinter ist fertig und getestet; angeschlossen wird sie mit dem Panel-Muster (Fahrplan L2/L3).';

/** Zusatz für Flächen ohne Engine — sagt ehrlich, dass die Rechnung noch nicht existiert. */
export const HINWEIS_OHNE_ENGINE =
  'Für dieses Modul gibt es noch keine geprüfte Rechnung. Die Struktur ist der Anforderungsrahmen dafür.';

/**
 * Die Flächen in Navigationsreihenfolge (Haustechnik → PV-Planer → Direkt-Module).
 * Alle tragen `in_entwicklung`: Layout steht, Funktion nicht — und sagt das.
 */
export const FACH_FLAECHEN: readonly FachFlaeche[] = [
  // --- Haustechnik ------------------------------------------------------------------------------
  {
    id: 'fach-heizung',
    label: 'Heizung',
    gruppe: 'Haustechnik',
    engine: 'engine-heizkreis',
    zweck: 'Hier wird der Heizkreis-Verteiler ausgelegt: je Raum ein Kreis, daraus Abgangszahl und Massenstrom.',
    eingaenge: [
      { label: 'Heizkreise je Raum', einheit: 'Liste', typ: 'HeizkreisEingabe[]' },
      { label: 'Heizlast je Kreis', einheit: 'W' },
      { label: 'Spreizung Vor-/Rücklauf', einheit: 'K' },
      { label: 'Vorlauftemperatur', einheit: '°C' },
    ],
    ausgaenge: [
      { label: 'Verteiler-Auslegung', einheit: 'Ergebnis', typ: 'VerteilerErgebnis' },
      { label: 'Abgänge am Verteiler', einheit: 'Stück' },
      { label: 'Massenstrom je Kreis', einheit: 'kg/h' },
      { label: 'Gesamtmassenstrom', einheit: 'kg/h' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-heizlast',
    label: 'Heizlastberechnung',
    gruppe: 'Haustechnik',
    zweck: 'Hier entsteht die raumweise Norm-Heizlast nach DIN EN 12831 als Grundlage für Erzeuger und Heizflächen.',
    eingaenge: [
      { label: 'Raumgeometrie (Fläche, Höhe)', einheit: 'm², m' },
      { label: 'U-Werte der Bauteile', einheit: 'W/(m²·K)' },
      { label: 'Norm-Außentemperatur', einheit: '°C' },
      { label: 'Innentemperatur je Nutzung', einheit: '°C' },
      { label: 'Luftwechselrate', einheit: '1/h' },
    ],
    ausgaenge: [
      { label: 'Norm-Heizlast je Raum', einheit: 'W' },
      { label: 'Gebäude-Heizlast', einheit: 'kW' },
      { label: 'Spezifische Heizlast', einheit: 'W/m²' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-lueftung',
    label: 'Lüftung',
    gruppe: 'Haustechnik',
    zweck: 'Hier wird der Lüftungsbedarf je Raum bestimmt und daraus Gerät und Kanalnetz dimensioniert.',
    eingaenge: [
      { label: 'Raumvolumen', einheit: 'm³' },
      { label: 'Nutzung und Personenzahl', einheit: 'Auswahl' },
      { label: 'Feuchtelast', einheit: 'g/h' },
      { label: 'Geforderter Luftwechsel', einheit: '1/h' },
    ],
    ausgaenge: [
      { label: 'Zuluftvolumenstrom je Raum', einheit: 'm³/h' },
      { label: 'Abluftvolumenstrom je Raum', einheit: 'm³/h' },
      { label: 'Gerätegröße', einheit: 'm³/h' },
      { label: 'Kanalquerschnitte', einheit: 'mm' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-klima',
    label: 'Klima',
    gruppe: 'Haustechnik',
    zweck: 'Hier wird die Kühllast je Raum ermittelt und das passende Innen-/Außengerät samt Leitungsweg gewählt.',
    eingaenge: [
      { label: 'Fensterfläche und Orientierung', einheit: 'm², Himmelsrichtung' },
      { label: 'Interne Lasten (Personen, Geräte)', einheit: 'W' },
      { label: 'Zieltemperatur Raum', einheit: '°C' },
      { label: 'Auslegungs-Außentemperatur', einheit: '°C' },
    ],
    ausgaenge: [
      { label: 'Kühllast je Raum', einheit: 'W' },
      { label: 'Gerätegröße', einheit: 'kW' },
      { label: 'Kältemittel-Leitungslänge', einheit: 'm' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-waermepumpe',
    label: 'Wärmepumpe',
    gruppe: 'Haustechnik',
    zweck: 'Hier wird die Wärmepumpe auf die Gebäudeheizlast ausgelegt, samt Bivalenzpunkt, Speicher und Jahresarbeitszahl.',
    eingaenge: [
      { label: 'Gebäude-Heizlast', einheit: 'kW' },
      { label: 'Auslegungs-Vorlauftemperatur', einheit: '°C' },
      { label: 'Warmwasserbedarf', einheit: 'l/d' },
      { label: 'Wärmequelle (Luft/Sole/Wasser)', einheit: 'Auswahl' },
      { label: 'Klimadatensatz (Standort)', einheit: 'Auswahl' },
    ],
    ausgaenge: [
      { label: 'Nennleistung', einheit: 'kW' },
      { label: 'Bivalenzpunkt', einheit: '°C' },
      { label: 'Jahresarbeitszahl', einheit: '–' },
      { label: 'Pufferspeicher-Volumen', einheit: 'l' },
      { label: 'Stromverbrauch', einheit: 'kWh/a' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-fbh',
    label: 'Fußbodenheizung',
    gruppe: 'Haustechnik',
    engine: 'engine-fbh',
    zweck: 'Hier wird die Fußbodenheizung ausgelegt: Heizfläche, Verlegeabstand, Rohrlänge und Zahl der Heizkreise je Raum.',
    eingaenge: [
      { label: 'Auslegungsdaten des Raums', einheit: 'Satz', typ: 'FbhEingabe' },
      { label: 'Raumfläche', einheit: 'm²' },
      { label: 'Heizlast des Raums', einheit: 'W' },
      { label: 'Verlegeabstand', einheit: 'mm' },
      { label: 'Maximale Kreislänge', einheit: 'm' },
    ],
    ausgaenge: [
      { label: 'Auslegungsergebnis', einheit: 'Satz', typ: 'FbhErgebnis' },
      { label: 'Heizfläche', einheit: 'm²' },
      { label: 'Rohrlänge', einheit: 'm' },
      { label: 'Heizkreise', einheit: 'Stück' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-waerme-contracting',
    label: 'Wärme-Contracting',
    gruppe: 'Haustechnik',
    zweck: 'Hier wird das Contracting-Modell aufgestellt: Wärmepreis, Grundpreis und Laufzeit im Vergleich zur Eigeninvestition.',
    eingaenge: [
      { label: 'Investitionssumme', einheit: '€' },
      { label: 'Vertragslaufzeit', einheit: 'Jahre' },
      { label: 'Kalkulationszins', einheit: '%' },
      { label: 'Wärmemenge je Jahr', einheit: 'kWh/a' },
      { label: 'Betriebs- und Wartungskosten', einheit: '€/a' },
    ],
    ausgaenge: [
      { label: 'Arbeitspreis Wärme', einheit: 'ct/kWh' },
      { label: 'Grundpreis', einheit: '€/Monat' },
      { label: 'Vergleich zur Eigenanlage', einheit: '€/a' },
      { label: 'Amortisationszeit', einheit: 'Jahre' },
    ],
    zustand: 'in_entwicklung',
  },
  // --- PV-Planer --------------------------------------------------------------------------------
  {
    id: 'fach-pv-module',
    label: 'PV-Module',
    gruppe: 'PV-Planer',
    engine: 'engine-pv',
    zweck: 'Hier wird die Dachfläche mit Modulen belegt und daraus Modulzahl, Leistung und Flächennutzung bestimmt.',
    eingaenge: [
      { label: 'Belegungsvorgabe der Fläche', einheit: 'Satz', typ: 'PvEingabe' },
      { label: 'Dachfläche (Breite × Höhe)', einheit: 'm' },
      { label: 'Modulmaß', einheit: 'mm' },
      { label: 'Randabstand', einheit: 'mm' },
      { label: 'Ausrichtung und Neigung', einheit: '°' },
    ],
    ausgaenge: [
      { label: 'Belegung der Fläche', einheit: 'Satz', typ: 'PvBelegung' },
      { label: 'Modulzahl', einheit: 'Stück' },
      { label: 'Anlagenleistung', einheit: 'kWp' },
      { label: 'Genutzte Fläche', einheit: '%' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-speicher',
    label: 'Speicherauslegung',
    gruppe: 'PV-Planer',
    zweck: 'Hier wird der Batteriespeicher auf Lastprofil und Erzeugung ausgelegt und der erreichbare Autarkiegrad gezeigt.',
    eingaenge: [
      { label: 'Jahresstromverbrauch', einheit: 'kWh/a' },
      { label: 'Lastprofil', einheit: 'Viertelstundenwerte' },
      { label: 'PV-Erzeugung', einheit: 'kWh/a' },
      { label: 'Gewünschter Autarkiegrad', einheit: '%' },
      { label: 'Entladetiefe', einheit: '%' },
    ],
    ausgaenge: [
      { label: 'Speicherkapazität', einheit: 'kWh' },
      { label: 'Erreichter Autarkiegrad', einheit: '%' },
      { label: 'Eigenverbrauchsquote', einheit: '%' },
      { label: 'Vollzyklen je Jahr', einheit: '1/a' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-wallbox',
    label: 'Wallbox',
    gruppe: 'PV-Planer',
    zweck: 'Hier werden Ladepunkte geplant: Ladeleistung, Leitungsweg und Lastmanagement am vorhandenen Hausanschluss.',
    eingaenge: [
      { label: 'Zahl der Fahrzeuge', einheit: 'Stück' },
      { label: 'Tagesfahrleistung', einheit: 'km/d' },
      { label: 'Hausanschlussleistung', einheit: 'kW' },
      { label: 'Leitungslänge zum Ladepunkt', einheit: 'm' },
      { label: 'Ladezeitfenster', einheit: 'h' },
    ],
    ausgaenge: [
      { label: 'Ladeleistung je Ladepunkt', einheit: 'kW' },
      { label: 'Leitungsquerschnitt', einheit: 'mm²' },
      { label: 'Absicherung', einheit: 'A' },
      { label: 'Grenze des Lastmanagements', einheit: 'kW' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-carport',
    label: 'Carport',
    gruppe: 'PV-Planer',
    zweck: 'Hier wird der PV-Carport geplant: Stellplätze, Tragwerksraster und Modulbelegung des Daches.',
    eingaenge: [
      { label: 'Zahl der Stellplätze', einheit: 'Stück' },
      { label: 'Grundmaße (Länge × Breite)', einheit: 'm' },
      { label: 'Schnee- und Windlastzone', einheit: 'Auswahl' },
      { label: 'Modultyp', einheit: 'Auswahl' },
    ],
    ausgaenge: [
      { label: 'Stützenraster', einheit: 'm' },
      { label: 'Zahl der Stützen', einheit: 'Stück' },
      { label: 'Modulzahl', einheit: 'Stück' },
      { label: 'Anlagenleistung', einheit: 'kWp' },
      { label: 'Holz-/Stahlmenge', einheit: 'm³ bzw. kg' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-zaun',
    label: 'Zaun',
    gruppe: 'PV-Planer',
    zweck: 'Hier wird der Solarzaun geplant: Länge, Ausrichtung und Modulzahl je Feld samt Pfosten- und Fundamentliste.',
    eingaenge: [
      { label: 'Zaunlänge', einheit: 'm' },
      { label: 'Zaunhöhe', einheit: 'm' },
      { label: 'Ausrichtung', einheit: '°' },
      { label: 'Modulmaß', einheit: 'mm' },
      { label: 'Pfostenraster', einheit: 'm' },
    ],
    ausgaenge: [
      { label: 'Zahl der Felder', einheit: 'Stück' },
      { label: 'Modulzahl', einheit: 'Stück' },
      { label: 'Anlagenleistung', einheit: 'kWp' },
      { label: 'Pfosten- und Fundamentliste', einheit: 'Liste' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-freiland',
    label: 'Freiland',
    gruppe: 'PV-Planer',
    zweck: 'Hier wird die Freiflächenanlage ausgelegt: Reihenabstand, Tischzahl und Flächennutzung ohne gegenseitige Verschattung.',
    eingaenge: [
      { label: 'Flurstücksfläche', einheit: 'm²' },
      { label: 'Reihenabstand', einheit: 'm' },
      { label: 'Tischgeometrie', einheit: 'm' },
      { label: 'Aufständerungswinkel', einheit: '°' },
      { label: 'Breitengrad des Standorts', einheit: '°' },
    ],
    ausgaenge: [
      { label: 'Zahl der Tische', einheit: 'Stück' },
      { label: 'Modulzahl', einheit: 'Stück' },
      { label: 'Anlagenleistung', einheit: 'kWp' },
      { label: 'Verschattungsverlust', einheit: '%' },
      { label: 'Flächennutzungsgrad', einheit: '%' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-hems',
    label: 'HEMS',
    gruppe: 'PV-Planer',
    zweck: 'Hier wird das Energiemanagement eingerichtet: Erzeuger, Speicher und Verbraucher bekommen Regeln für den Eigenverbrauch.',
    eingaenge: [
      { label: 'Erzeuger und Speicher', einheit: 'Liste' },
      { label: 'Steuerbare Verbraucher', einheit: 'Liste' },
      { label: 'Zählpunkte', einheit: 'Liste' },
      { label: 'Prioritätsregeln', einheit: 'Reihenfolge' },
      { label: 'Tarifzeitfenster', einheit: 'h' },
    ],
    ausgaenge: [
      { label: 'Regelplan je Verbraucher', einheit: 'Liste' },
      { label: 'Erwartete Eigenverbrauchsquote', einheit: '%' },
      { label: 'Benötigte Schnittstellen', einheit: 'Liste' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-messstellenbetrieb',
    label: 'Messstellenbetrieb',
    gruppe: 'PV-Planer',
    zweck: 'Hier wird das Messkonzept erfasst: welcher Zähler misst welchen Erzeugungs- und Verbrauchspfad.',
    eingaenge: [
      { label: 'Marktlokationen', einheit: 'Liste' },
      { label: 'Zählpunktbezeichnungen', einheit: 'Text' },
      { label: 'Erzeugungs- und Verbrauchspfade', einheit: 'Liste' },
      { label: 'Variante des Messkonzepts', einheit: 'Auswahl' },
    ],
    ausgaenge: [
      { label: 'Messkonzept-Skizze', einheit: 'Zeichnung' },
      { label: 'Zählerliste', einheit: 'Liste' },
      { label: 'Umfang der Anmeldeunterlagen', einheit: 'Liste' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-dyn-tarif',
    label: 'dynamischer Stromtarif',
    gruppe: 'PV-Planer',
    zweck: 'Hier wird ein dynamischer Tarif gegen den Festpreis gerechnet — auf Basis des eigenen Lastprofils, nicht auf Basis einer Annahme.',
    eingaenge: [
      { label: 'Lastprofil', einheit: 'Viertelstundenwerte' },
      { label: 'Börsenpreisreihe', einheit: 'ct/kWh' },
      { label: 'Netzentgelte', einheit: 'ct/kWh' },
      { label: 'Steuern und Abgaben', einheit: 'ct/kWh' },
      { label: 'Speicher-Fahrweise', einheit: 'Auswahl' },
    ],
    ausgaenge: [
      { label: 'Jahreskosten dynamisch', einheit: '€/a' },
      { label: 'Jahreskosten Festpreis', einheit: '€/a' },
      { label: 'Differenz', einheit: '€/a' },
      { label: 'Verschiebbare Strommenge', einheit: 'kWh/a' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-mietstrom',
    label: 'Mietstrom',
    gruppe: 'PV-Planer',
    zweck: 'Hier wird das Mieterstrom-Modell aufgestellt: Teilnehmer, Reststrombezug und Preisbildung je Wohneinheit.',
    eingaenge: [
      { label: 'Wohneinheiten', einheit: 'Stück' },
      { label: 'Teilnahmequote', einheit: '%' },
      { label: 'PV-Erzeugung', einheit: 'kWh/a' },
      { label: 'Verbrauch je Einheit', einheit: 'kWh/a' },
      { label: 'Reststrompreis', einheit: 'ct/kWh' },
    ],
    ausgaenge: [
      { label: 'Mieterstrompreis', einheit: 'ct/kWh' },
      { label: 'Deckungsgrad je Einheit', einheit: '%' },
      { label: 'Reststrommenge', einheit: 'kWh/a' },
      { label: 'Abrechnungsliste', einheit: 'Liste' },
    ],
    zustand: 'in_entwicklung',
  },
  // --- Direkt-Module unter „Fachplaner" ---------------------------------------------------------
  {
    id: 'fach-bad',
    label: 'Bad',
    gruppe: 'Fachplaner',
    engine: 'engine-abwasser',
    zweck: 'Hier wird das Bad geplant und die Abwasserleitung auf das Mindestgefälle nach DIN 1986-100 geprüft.',
    eingaenge: [
      { label: 'Leitungsdaten', einheit: 'Satz', typ: 'AbwasserEingabe' },
      { label: 'Leitungslänge', einheit: 'm' },
      { label: 'Höhenversatz', einheit: 'mm' },
      { label: 'Nennweite', einheit: 'DN' },
      { label: 'Anschlusswerte der Objekte', einheit: 'l/s' },
    ],
    ausgaenge: [
      { label: 'Gefälle-Prüfung', einheit: 'Satz', typ: 'AbwasserErgebnis' },
      { label: 'Ist-Gefälle', einheit: '%' },
      { label: 'Mindestgefälle', einheit: '%' },
      { label: 'Bewertung', einheit: 'erfüllt / nicht erfüllt' },
    ],
    zustand: 'in_entwicklung',
  },
  {
    id: 'fach-kueche',
    label: 'Küche',
    gruppe: 'Fachplaner',
    engine: 'engine-kueche',
    zweck: 'Hier wird die Küche geplant und das Arbeitsdreieck Kühlen–Spülen–Kochen nach DIN 18022 bewertet.',
    eingaenge: [
      { label: 'Arbeitsdreieck', einheit: 'Satz', typ: 'Arbeitsdreieck' },
      { label: 'Lage Kühlen', einheit: 'x/y in mm' },
      { label: 'Lage Spülen', einheit: 'x/y in mm' },
      { label: 'Lage Kochen', einheit: 'x/y in mm' },
      { label: 'Raumkontur', einheit: 'Polygon' },
    ],
    ausgaenge: [
      { label: 'Bewertung des Dreiecks', einheit: 'Satz', typ: 'DreieckErgebnis' },
      { label: 'Schenkellängen', einheit: 'm' },
      { label: 'Summe der Schenkel', einheit: 'm' },
      { label: 'Bewertung', einheit: 'gut / eng / weit' },
    ],
    zustand: 'in_entwicklung',
  },
];

/** Eine Fläche nach Modulnamen (exakt wie in `FACH`). Unbekannt → `undefined`, nie werfen. */
export function fachFlaecheNach(label: string): FachFlaeche | undefined {
  return FACH_FLAECHEN.find((f) => f.label === label);
}

/** Eine Fläche nach id. Unbekannt → `undefined`, nie werfen. */
export function fachFlaecheMitId(id: string): FachFlaeche | undefined {
  return FACH_FLAECHEN.find((f) => f.id === id);
}

/** Beschriftung des Zurück-Wegs; unbekannte/fehlende Herkunft fällt auf die Navigation zurück (Kante 2). */
export function zurueckLabel(herkunft: FlaechenHerkunft | undefined): string {
  return (herkunft && HERKUNFT_ZURUECK[herkunft]) || HERKUNFT_ZURUECK.navi;
}

/**
 * Alle anklickbaren Fachplaner-Module aus `FACH` — Untermodule der Hubs plus die Direkt-Module
 * ohne Untermenü. Das ist die gemessene Menge, nicht eine zweite Liste.
 */
export function anklickbareModule(): string[] {
  const namen: string[] = [];
  for (const hub of FACH) {
    if (hub.sub) for (const s of hub.sub) namen.push(s[0]);
    else namen.push(hub.name);
  }
  return namen;
}

/**
 * Module, die weder einen Konfigurator noch eine L4-Fläche haben — also Klicks ins Nichts.
 * Leere Liste = kein totes Element mehr.
 */
export function fehlendeFlaechen(): string[] {
  return anklickbareModule().filter((n) => !KONFIGURATOR_NAMEN[n] && !fachFlaecheNach(n));
}

/** Flächen, deren Modulname in `FACH` gar nicht vorkommt — Karteileichen. Leere Liste = sauber. */
export function verwaisteFlaechen(): string[] {
  const bekannt = new Set(anklickbareModule());
  return FACH_FLAECHEN.filter((f) => !bekannt.has(f.label)).map((f) => f.label);
}
