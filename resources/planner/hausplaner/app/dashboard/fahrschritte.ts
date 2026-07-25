/**
 * AUF-39 / L5 — die elf Wizard-Schritte **aus dem Modell abgeleitet**.
 *
 * **Was vorher dastand, war eine Notlüge, und der Code sagte es selbst:** `studioDaten.ts` trug den
 * Vermerk *„Präsentativ — echte Zustands-Ableitung folgt aus dem Modell"* und behauptete für ein
 * frisch angelegtes Projekt „Bauherr & Adresse ✓", „Maßstab erkannt · 1:50 ✓". Ein leeres Projekt
 * zeigte grüne Haken. Dieselbe Sorte falsches Versprechen, die AUF-25 aus den Fachplaner-Flächen
 * entfernt hat — nur an prominenterer Stelle.
 *
 * ## Die Regel, an der dieser Posten steht oder fällt
 *
 * **Was das Modell nicht weiß, wird nicht behauptet.** Für Bauherrendaten, Import-Maßstab,
 * Freigaben und Prüfläufe gibt es heute **keine** Grundlage im `SceneDocument`. Diese Schritte
 * bleiben `open`, tragen **keinen** erfundenen Prüfpunkt und sagen im Hinweis, **was fehlt**.
 * Ein Schritt ohne Datengrundlage ist ein leerer Schritt, kein grüner.
 *
 * ## Rein, und das ist keine Formalie
 *
 * Kein Store-Zugriff, kein `Date`, kein Zufall. Dieselbe Szene ⇒ dasselbe Ergebnis, immer. Nur so
 * ist der Fahrplan testbar (die Testumgebung hat kein DOM) und nur so kann er nicht „heute grün,
 * morgen gelb" sein, ohne dass sich am Gebäude etwas geändert hat.
 *
 * **`status` folgt aus den Prüfpunkten**, nicht umgekehrt: alle erfüllt → `ok`, keiner → `open`,
 * gemischt → `prog`, ein verletzter Zwang → `warn`.
 */
import type { SceneDocument, SceneNode } from '../../domain/scene.types';
import type { Fahrschritt, Pruefpunkt, SchrittStatus } from '../studioDaten';

/** Zählt Knoten eines Typs — die einzige Stelle, an der über `nodes` gefiltert wird. */
function zaehle(nodes: readonly SceneNode[], pruef: (n: SceneNode) => boolean): number {
  return nodes.filter(pruef).length;
}

/** Ein erfüllter Prüfpunkt mit der Zahl, die ihn belegt. */
const erfuellt = (text: string): Pruefpunkt => ({ status: 'ok', text });
/** Ein offener Prüfpunkt: benennt, was fehlt — nie „noch nicht". */
const offen = (text: string): Pruefpunkt => ({ status: 'open', text });

/**
 * `status` aus den Prüfpunkten. **Leere Liste ⇒ `open`** — ein Schritt ohne prüfbare Aussage ist
 * nicht fertig, er ist unbekannt.
 */
export function statusAus(checks: readonly Pruefpunkt[]): SchrittStatus {
  if (checks.length === 0) return 'open';
  if (checks.some((c) => c.status === 'warn')) return 'warn';
  if (checks.every((c) => c.status === 'ok')) return 'ok';
  if (checks.every((c) => c.status === 'open')) return 'open';
  return 'prog';
}

/**
 * **Die Schritte ohne Modellgrundlage.** Sie stehen hier zusammen und nicht verstreut, damit die
 * Lücke zählbar ist: `SCHRITTE_OHNE_GRUNDLAGE.length` ist die Länge der Rückgabe-Liste an den
 * Planner. Jeder Eintrag sagt, **was es bräuchte** — das ist der Anfang des nächsten Postens.
 */
export const SCHRITTE_OHNE_GRUNDLAGE: Readonly<Record<string, string>> = {
  'Projektgrundlagen': 'Bauherr, Adresse und Grundstück stehen im CRM, nicht im Gebäudemodell. '
    + 'Solange der Planer sie nicht liest, kann dieser Schritt nichts bestätigen.',
  'Import oder Grundriss': 'Ob eine Vorlage importiert und ihr Maßstab bestätigt wurde, führt das '
    + 'Dokument nicht. Sichtbar ist nur, ob Wände vorhanden sind.',
  'Räume und Einrichtung': 'Raumnutzung und Möblierung sind im Schema nicht als Eigenschaft geführt; '
    + 'Räume entstehen abgeleitet aus der Raumerkennung.',
  'Küche und Bad': 'Küchen- und Bad-Ausstattung hat keine eigene Objektart; nur Sanitärobjekte sind '
    + 'zählbar.',
  'Prüfung und Koordination': 'Es gibt keinen gespeicherten Prüflauf und keine Freigabe im Dokument.',
  'Dokumentation und Rendering': 'Erzeugte Pläne, Listen und Renderings werden nicht im Dokument '
    + 'vermerkt.',
};

/**
 * Die elf Schritte aus dem Dokument. **Titel und Reihenfolge sind unverändert** — `GuidedView`
 * wird nicht umgebaut, es bekommt nur eine andere Quelle.
 */
export function ableitenSchritte(scene: SceneDocument | null): Fahrschritt[] {
  const nodes: readonly SceneNode[] = scene?.nodes ?? [];
  const geschosse = scene?.levels.length ?? 0;
  /**
   * **Bebaute** Geschosse — solche, in denen wirklich etwas steht. Der Unterschied ist nicht
   * Haarspalterei: ein frisch angelegtes Projekt **hat** bereits ein Geschoss, weil die Anwendung
   * es anlegt, nicht der Nutzer. „1 Geschoss angelegt ✓" wäre also genau die Sorte Behauptung, die
   * dieser Posten beseitigt — grün, ohne dass jemand etwas getan hat. Gezählt wird deshalb, was
   * das Geschoss trägt.
   */
  const bebauteGeschosse = (scene?.levels ?? []).filter((l) => (
    nodes.some((n) => n.levelId === l.id)
    || (scene?.roofs ?? []).some((r) => r.levelId === l.id)
    || (scene?.ceilings ?? []).some((c) => c.levelId === l.id)
  )).length;
  const waende = zaehle(nodes, (n) => n.type === 'wall');
  const fenster = zaehle(nodes, (n) => n.type === 'window');
  const tueren = zaehle(nodes, (n) => n.type === 'door');
  const treppen = zaehle(nodes, (n) => n.type === 'object' && n.objectType === 'stair');
  const daecher = scene?.roofs?.length ?? 0;
  const decken = scene?.ceilings?.length ?? 0;
  const raeume = zaehle(nodes, (n) => n.type === 'zone' && n.zoneType === 'room');
  const moebel = zaehle(nodes, (n) => n.type === 'object' && n.objectType === 'furniture');
  const sanitaer = zaehle(nodes, (n) => n.type === 'object' && n.objectType === 'sanitary');
  const elektroObjekte = zaehle(nodes, (n) => n.type === 'object'
    && (n.objectType === 'wallbox' || n.objectType === 'battery' || n.objectType === 'inverter'));
  const elektroLeitungen = zaehle(nodes, (n) => n.type === 'route'
    && (n.routeType === 'electrical_line' || n.routeType === 'pv_dc_line'));
  const waerme = zaehle(nodes, (n) => n.type === 'object'
    && (n.objectType === 'radiator' || n.objectType === 'heat_pump_indoor'
      || n.objectType === 'heat_pump_outdoor' || n.objectType === 'buffer_tank'
      || n.objectType === 'hot_water_tank'));
  const tgaLeitungen = zaehle(nodes, (n) => n.type === 'route'
    && (n.routeType === 'heating_pipe' || n.routeType === 'water_pipe' || n.routeType === 'drainage'));
  const fbh = zaehle(nodes, (n) => n.type === 'zone' && n.zoneType === 'underfloor_heating');
  const pvFlaechen = zaehle(nodes, (n) => n.type === 'zone' && n.zoneType === 'pv_area');

  /** Baut einen Schritt und leitet den Status aus seinen Prüfpunkten ab. */
  const schritt = (
    titel: string, hinweis: string, checks: Pruefpunkt[],
    aufgaben: Fahrschritt['aufgaben'] = [], empfehlung: Fahrschritt['empfehlung'] = null,
  ): Fahrschritt => ({ titel, status: statusAus(checks), hinweis, checks, aufgaben, empfehlung });

  /** Ein Schritt ohne Modellgrundlage: keine Prüfpunkte, ehrlicher Hinweis, `open`. */
  const ohneGrundlage = (titel: string, zusatz = ''): Fahrschritt => schritt(
    titel,
    `${SCHRITTE_OHNE_GRUNDLAGE[titel]}${zusatz ? ` ${zusatz}` : ''}`,
    [],
  );

  return [
    ohneGrundlage('Projektgrundlagen'),

    // Ohne Import-Kenntnis bleibt der eine messbare Teil: gibt es überhaupt einen Grundriss?
    waende > 0
      ? schritt('Import oder Grundriss',
        `${SCHRITTE_OHNE_GRUNDLAGE['Import oder Grundriss']} Gezeichnet sind ${waende} Wände.`,
        [erfuellt(`${waende} Wände gezeichnet`)])
      : ohneGrundlage('Import oder Grundriss', 'Es sind keine Wände vorhanden.'),

    schritt('Geschosse und Gebäude',
      bebauteGeschosse > 0
        ? `${bebauteGeschosse} von ${geschosse} Geschossen bebaut${decken > 0 ? `, ${decken} mit Decke` : ''}.`
        : `${geschosse} Geschoss${geschosse === 1 ? '' : 'e'} vorhanden, aber noch nichts darin gebaut.`,
      [
        bebauteGeschosse > 0
          ? erfuellt(`${bebauteGeschosse} von ${geschosse} Geschossen bebaut`)
          : offen(`Noch nichts in ${geschosse === 1 ? 'dem Geschoss' : 'den Geschossen'} gebaut`),
        decken > 0 ? erfuellt(`${decken} Geschossdecke${decken === 1 ? '' : 'n'}`) : offen('Keine Geschossdecke'),
      ]),

    schritt('Fenster, Türen und Treppen',
      fenster + tueren + treppen > 0
        ? `${fenster} Fenster, ${tueren} Türen, ${treppen} Treppen im Modell.`
        : 'Noch keine Öffnung und keine Treppe gesetzt.',
      [
        fenster > 0 ? erfuellt(`${fenster} Fenster gesetzt`) : offen('Kein Fenster gesetzt'),
        tueren > 0 ? erfuellt(`${tueren} Türen gesetzt`) : offen('Keine Tür gesetzt'),
        treppen > 0 ? erfuellt(`${treppen} Treppe${treppen === 1 ? '' : 'n'} gesetzt`)
          : (geschosse > 1 ? { status: 'warn' as const, text: `${geschosse} Geschosse, aber keine Treppe` }
            : offen('Keine Treppe gesetzt')),
      ]),

    schritt('Dach und Fassade',
      daecher > 0 ? `${daecher} Dachfläche${daecher === 1 ? '' : 'n'} aufgesetzt.` : 'Noch kein Dach aufgesetzt.',
      [daecher > 0 ? erfuellt(`${daecher} Dach${daecher === 1 ? '' : 'flächen'} aufgesetzt`) : offen('Kein Dach aufgesetzt')]),

    // Räume entstehen abgeleitet; Nutzung und Möblierung führt das Schema nicht.
    raeume + moebel > 0
      ? schritt('Räume und Einrichtung',
        `${SCHRITTE_OHNE_GRUNDLAGE['Räume und Einrichtung']} Erkannt sind ${raeume} Räume${moebel > 0 ? `, platziert ${moebel} Möbel` : ''}.`,
        [raeume > 0 ? erfuellt(`${raeume} Räume erkannt`) : offen('Kein Raum erkannt')])
      : ohneGrundlage('Räume und Einrichtung', 'Es ist kein Raum erkannt.'),

    sanitaer > 0
      ? schritt('Küche und Bad',
        `${SCHRITTE_OHNE_GRUNDLAGE['Küche und Bad']} Platziert sind ${sanitaer} Sanitärobjekte.`,
        [erfuellt(`${sanitaer} Sanitärobjekte platziert`)])
      : ohneGrundlage('Küche und Bad', 'Es ist kein Sanitärobjekt platziert.'),

    schritt('Elektro',
      elektroObjekte + elektroLeitungen > 0
        ? `${elektroObjekte} Geräte, ${elektroLeitungen} Leitungen.`
        : 'Noch keine Elektro-Komponente im Modell.',
      [
        elektroObjekte > 0 ? erfuellt(`${elektroObjekte} Geräte platziert`) : offen('Kein Gerät platziert'),
        elektroLeitungen > 0 ? erfuellt(`${elektroLeitungen} Leitungen geführt`) : offen('Keine Leitung geführt'),
        pvFlaechen > 0 ? erfuellt(`${pvFlaechen} PV-Fläche${pvFlaechen === 1 ? '' : 'n'}`) : offen('Keine PV-Fläche'),
      ]),

    schritt('TGA',
      waerme + tgaLeitungen + fbh > 0
        ? `${waerme} Wärmeerzeuger/Heizflächen, ${tgaLeitungen} Leitungen, ${fbh} FBH-Flächen.`
        : 'Noch keine Anlagentechnik im Modell.',
      [
        waerme > 0 ? erfuellt(`${waerme} Wärmeobjekte platziert`) : offen('Kein Wärmeobjekt platziert'),
        tgaLeitungen > 0 ? erfuellt(`${tgaLeitungen} Leitungen geführt`) : offen('Keine Leitung geführt'),
        fbh > 0 ? erfuellt(`${fbh} FBH-Fläche${fbh === 1 ? '' : 'n'}`) : offen('Keine Flächenheizung'),
      ]),

    ohneGrundlage('Prüfung und Koordination'),
    ohneGrundlage('Dokumentation und Rendering'),
  ];
}

/** Die Titel in fester Reihenfolge — für den Vergleich gegen die stillgelegten Demo-Daten. */
export function schrittTitel(): string[] {
  return ableitenSchritte(null).map((s) => s.titel);
}
