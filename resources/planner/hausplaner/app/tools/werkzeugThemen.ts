/**
 * AUF-34 / Nachtrag 2 — die **15 Themen** des Funktionsvertrag-Pakets als Daten.
 *
 * **Yamas Entscheidung, 25.07.: „15".** Die Werkzeugleiste gruppiert seit diesem Posten nach den
 * 15 Themen, nicht mehr nach den 22 Kategorien. Der Grund ist gemessen, nicht Geschmack: die
 * Kategorien `TGA` und `Sanitär` trugen **je ein** Werkzeug — 22 gleichrangige Menüs, teils für ein
 * einziges Werkzeug. Die Themen fassen genau das zusammen (`Heizung, Hydraulik & TGA` = 6,
 * `Sanitär, Bad & Küche` = 7) und sind eine **vollständige Zerlegung**: ihre Summe ist exakt 110,
 * jedes Werkzeug steht in genau einem Thema.
 *
 * **Die Kategorie bleibt** als Datenfeld an jedem Werkzeug (`kategorie` im Paket, `groupId`/`group`
 * in `ToolDefinition`) — als Trail, **nicht** als zweite Gruppierung. Wer nach Kategorie gruppiert,
 * baut eine zweite Wahrheit.
 *
 * **Erzeugt aus** `~/Downloads/hausplaner_svg_tool_functions.zip` → `src/tool-themes.json`
 * (englische Paket-ids, deutsche Labels) und der führenden Namenstabelle
 * `docs/planner/eindeutschung-110-paket-ids.md` (englische Paket-id → deutsche UI-id, mit AUF-31
 * umgesetzt). Die `themeId` bleibt technisch (`07-architektur`), die Labels sind unverändert deutsch
 * aus dem Paket übernommen.
 *
 * **Zwei Zellen der Namenstabelle waren defekt** und sind hier korrigiert eingeflossen: der
 * führende Umlaut war verschluckt (`ffnung` statt `oeffnung`, `bergabepaket` statt
 * `uebergabepaket`). Maßstab ist der Code — `werkzeugPaket.ts` führt beide korrekt. Die Tabelle
 * selbst ist mit diesem Posten nachgezogen.
 */

export interface WerkzeugThema {
  /** Technische id aus dem Paket, bewusst englisch-numerisch: `07-architektur`. */
  id: string;
  /** Anzeigename — unverändert deutsch aus `tool-themes.json`. */
  label: string;
  /** Die deutschen UI-ids der Werkzeuge dieses Themas. */
  werkzeuge: readonly string[];
}

/**
 * Anzeigereihenfolge = Paket-Reihenfolge (01…15). Sie folgt dem Arbeitsablauf: auswählen,
 * bearbeiten, zeichnen, ansehen, messen, importieren, bauen, ausstatten, rechnen, übergeben.
 * Fest verdrahtet — eine Leiste, deren Reihenfolge sich mit den Daten ändert, zwingt den Nutzer
 * jedes Mal zum Suchen.
 */
export const WERKZEUG_THEMEN: readonly WerkzeugThema[] = [
  {
    id: '01-grundbedienung',
    label: 'Grundbedienung & Auswahl',
    werkzeuge: [
      'auswahl',
      'direktauswahl',
      'lassoauswahl',
      'rechteckauswahl',
    ],
  },
  {
    id: '02-transformieren',
    label: 'Bearbeiten & Transformieren',
    werkzeuge: [
      'ausblenden',
      'ausrichten',
      'drehen',
      'duplizieren',
      'einblenden',
      'entsperren',
      'gruppieren',
      'horizontal-spiegeln',
      'kopieren',
      'loeschen',
      'skalieren',
      'sperren',
      'verschieben',
      'verteilen',
      'vertikal-spiegeln',
    ],
  },
  {
    id: '03-zeichnen-cad',
    label: 'Zeichnen & CAD',
    werkzeuge: [
      'teilen',
      'trimmen',
      'verbinden',
      'verlaengern',
      'versatz',
      'bogen',
      'kreis',
      'linie',
      'polygon',
      'polylinie',
      'rechteck',
    ],
  },
  {
    id: '04-ansicht-navigation',
    label: 'Ansicht & Navigation',
    werkzeuge: [
      'alles-anzeigen',
      'fang',
      'umkreisen',
      'hand',
      'raster',
      'vergroessern',
      'verkleinern',
    ],
  },
  {
    id: '05-messen-dokumentieren',
    label: 'Messen & Bemaßen',
    werkzeuge: [
      'bemassen',
      'distanz-messen',
      'flaeche-messen',
      'volumen-messen',
      'winkel-messen',
    ],
  },
  {
    id: '06-import-erkennung',
    label: 'Import, Nachzeichnen & Erkennung',
    werkzeuge: [
      'beschneiden',
      'bild-importieren',
      'datei-importieren',
      'erkennung-bestaetigen',
      'grundriss-erkennen',
      'ki-assistent',
      'kalibrieren',
      'nordrichtung-setzen',
    ],
  },
  {
    id: '07-architektur',
    label: 'Architektur & Gebäude',
    werkzeuge: [
      'boden',
      // Z-05-N1: die Kontur ist der Umriss eines Bauteils und gehoert damit zur Architektur —
      // nicht zu den Zeichen-Primitiven, die stillgelegt sind.
      'kontur',
      'dach',
      'dachfenster',
      'decke',
      'aufriss',
      'fenster',
      'gaube',
      'raum',
      'schnitt',
      'stuetze',
      'treppe',
      'tuer',
      'unterzug',
      'wand',
      'oeffnung',
    ],
  },
  {
    id: '08-material-fassade',
    label: 'Material, Textur & Fassade',
    werkzeuge: [
      'fassadensystem',
      'klinker',
      'material-aufnehmen',
      'material-zuweisen',
      'textur',
    ],
  },
  {
    id: '09-bauphysik',
    label: 'Bauphysik, U-Werte & Lüftung',
    werkzeuge: [
      'daemmung',
      'lueftung',
      'thermische-huelle',
      'u-wert',
    ],
  },
  {
    id: '10-heizung-tga',
    label: 'Heizung, Hydraulik & TGA',
    werkzeuge: [
      'fussbodenheizung',
      'heizkoerper',
      'hydraulischer-abgleich',
      'pumpe',
      'waermepumpe',
      'rohrleitung',
    ],
  },
  {
    id: '11-bad-kueche',
    label: 'Sanitär, Bad & Küche',
    werkzeuge: [
      'badewanne',
      'dusche',
      'wc',
      'geraet',
      'kuechenplanung',
      'schrank',
      'sanitaeranschluss',
    ],
  },
  {
    id: '12-elektro-pv',
    label: 'Elektro, PV & Energie',
    werkzeuge: [
      'elektroplanung',
      'leuchte',
      'schalter',
      'steckdose',
      'verteiler',
      'batteriespeicher',
      'pv-modul',
      'wallbox',
    ],
  },
  {
    id: '13-workflow-pruefung',
    label: 'Wizard, Workflow & Übergabe',
    werkzeuge: [
      'freigeben',
      'prozessuebersicht',
      'assistent',
      'uebergabepaket',
    ],
  },
  {
    id: '14-pruefung-zusammenarbeit',
    label: 'Prüfung, Zusammenarbeit & Revision',
    werkzeuge: [
      'fehler',
      'pruefen',
      'warnungen',
      'historie',
      'kommentar',
      'revision',
    ],
  },
  {
    id: '15-system-export',
    label: 'System, Suche & Export',
    werkzeuge: [
      'befehlspalette',
      'einstellungen',
      'exportieren',
      'pdf',
      'suche',
    ],
  },];

/** Ein Thema nach id (oder undefined). */
export function thema(id: string): WerkzeugThema | undefined {
  return WERKZEUG_THEMEN.find((t) => t.id === id);
}

/** Das Thema, in dem ein Werkzeug steht (oder undefined) — für Tests und Zuordnungen. */
export function themaVonWerkzeug(werkzeugId: string): WerkzeugThema | undefined {
  return WERKZEUG_THEMEN.find((t) => t.werkzeuge.includes(werkzeugId));
}

/**
 * Kurzform des Labels für die Leiste: der erste Begriff vor `&` oder `,`.
 *
 * **Warum überhaupt:** die vollen Labels sind bis zu 34 Zeichen lang; elf davon nebeneinander
 * sprengen jede Fensterbreite, und genau das — eine mehrzeilige Leiste, die die Seite in den
 * waagerechten Überlauf treibt — ist der Mangel, den dieser Posten behebt. **Es ist eine
 * Ableitung, keine zweite Tabelle:** das volle Label bleibt die einzige Quelle und steht
 * unverkürzt im `title` jedes Knopfes.
 */
export function kurzLabel(t: WerkzeugThema): string {
  return t.label.split(/\s*[&,]\s*/)[0].trim();
}
