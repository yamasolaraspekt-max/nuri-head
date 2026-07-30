/**
 * AUF-50 Stufe 1 — **die Werkzeug-Landkarte: was deckt der Bestand, was fehlt wirklich.**
 *
 * **Wofür sie da ist:** Der Stufenplan vermutete, dass die meisten `create`-Verträge durch **einen**
 * vorhandenen Befehl mit unterschiedlichem `type` gedeckt sind. Diese Datei beantwortet das je
 * Vertrag, statt es zu vermuten. **Das Produkt dieser Stufe ist die Zahl der `fehlt`-Marken** — der
 * eigentliche Bauvorrat für Stufe 3.
 *
 * **Was sie NICHT ist:** kein Verhalten, kein Dispatcher, kein zweiter Ausführungsweg. Reine Daten
 * — dieselbe Grenze, die `werkzeugVertrag.ts` sich selbst zieht.
 *
 * **Die vier Marken:**
 *
 * - **`deckt`** — ein Befehl in `applyCommand.ts` leistet es heute schon. `begruendung` nennt
 *   **den Befehl beim Namen**; ein Test prüft, dass es ihn wirklich gibt (K-03).
 * - **`fehlt`** — es braucht einen neuen Modellbefehl. `begruendung` sagt, was er leisten müsste.
 * - **`ohne-modell`** — das Werkzeug ändert das Gebäudemodell überhaupt nicht (Ansicht, Auswahl,
 *   Messen, Arbeitsablauf). Kein Bauvorrat, sondern eine andere Art Werkzeug.
 * - **`stillgelegt`** — der Vertrag gehört nicht in den Bauplaner. **Wird gemeldet, nicht
 *   entfernt** (Auflage des Auftrags).
 *
 * ---
 *
 * **⚠ Abweichung von der Auftragszahl, gemessen statt übernommen (P-04):**
 * Der Auftrag nennt **111** Verträge, gestützt auf `grep -c 'umkehrbar:'`. Dieser Befehl zählt
 * **die Interface-Deklaration mit** (`werkzeugVertrag.ts:40`, `umkehrbar: boolean;`).
 * `grep -c "werkzeugId: '"` liefert **110**, ebenso das Auszählen der Objektliterale.
 * **Es sind 110 Verträge — der ursprüngliche Stufenplan hatte recht, die Korrektur auf 111 war
 * der Zählfehler.** Diese Landkarte führt 110 Einträge; der Test prüft gegen
 * `WERKZEUG_VERTRAEGE.length`, nicht gegen eine abgeschriebene Zahl.
 */
import { WERKZEUG_VERTRAEGE } from './werkzeugVertrag';

/** Genau vier Werte — ein fünfter bricht `tsc` (K-02). */
export type LandkartenMarke = 'deckt' | 'fehlt' | 'ohne-modell' | 'stillgelegt';

export interface LandkartenEintrag {
  /** Die deutsche UI-id, dieselbe wie in `WERKZEUG_VERTRAEGE`. */
  werkzeugId: string;
  marke: LandkartenMarke;
  /**
   * Bei `deckt`: der **Name des deckenden Befehls** aus `applyCommand.ts` (ein Test prüft ihn).
   * Bei `fehlt`: was der neue Befehl leisten müsste.
   * Bei `ohne-modell` / `stillgelegt`: warum.
   */
  begruendung: string;
}

/**
 * Die Landkarte. Reihenfolge wie in `WERKZEUG_VERTRAEGE` — so ist ein Abgleich Zeile für Zeile
 * möglich, ohne zu sortieren.
 */
export const WERKZEUG_LANDKARTE: readonly LandkartenEintrag[] = [
  // --- selection (4) — Auswahl ist UI-Zustand, kein Modellzustand ---------------------------------
  { werkzeugId: 'auswahl', marke: 'ohne-modell', begruendung: 'Auswahl lebt im Store (`selectedNodeIds`), nicht im Szenendokument.' },
  { werkzeugId: 'direktauswahl', marke: 'ohne-modell', begruendung: 'Wie `auswahl` — Unterelement-Auswahl ist UI-Zustand.' },
  { werkzeugId: 'lassoauswahl', marke: 'ohne-modell', begruendung: 'Wie `auswahl` — nur ein anderer Aufgriff derselben Auswahl.' },
  { werkzeugId: 'rechteckauswahl', marke: 'ohne-modell', begruendung: 'Wie `auswahl` — nur ein anderer Aufgriff derselben Auswahl.' },

  // --- modify (20) --------------------------------------------------------------------------------
  { werkzeugId: 'ausblenden', marke: 'deckt', begruendung: 'SET_NODES_SICHTBAR' },
  { werkzeugId: 'ausrichten', marke: 'fehlt', begruendung: 'Braucht einen Befehl, der mehrere Knoten an einer gemeinsamen Kante/Achse ausrichtet. `MOVE_NODE` bewegt EINEN Knoten um einen Vektor — die Zielposition je Knoten ist hier erst zu rechnen.' },
  { werkzeugId: 'drehen', marke: 'fehlt', begruendung: 'Braucht Drehung um einen Bezugspunkt. `UPDATE_NODE` kann `transform.rotation` eines ObjectNode setzen, aber Wände/Öffnungen/Zonen haben keine Rotation — ihre Punkte müssten mitgedreht werden.' },
  { werkzeugId: 'duplizieren', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'einblenden', marke: 'deckt', begruendung: 'SET_NODES_SICHTBAR' },
  { werkzeugId: 'entsperren', marke: 'deckt', begruendung: 'SET_NODES_GESPERRT' },
  { werkzeugId: 'gruppieren', marke: 'fehlt', begruendung: 'Das Szenenmodell kennt keine Gruppe — es gibt kein Feld für Zugehörigkeit und keinen Gruppenknoten. Braucht ein Schemafeld UND einen Befehl.' },
  { werkzeugId: 'horizontal-spiegeln', marke: 'deckt', begruendung: 'UPDATE_NODE' },
  { werkzeugId: 'kopieren', marke: 'ohne-modell', begruendung: 'Kopieren allein ändert nichts — erst das Einfügen legt an, und das ist `duplizieren` (ADD_NODE). Die Zwischenablage ist UI-Zustand.' },
  { werkzeugId: 'loeschen', marke: 'deckt', begruendung: 'REMOVE_NODE' },
  { werkzeugId: 'skalieren', marke: 'fehlt', begruendung: 'Braucht Skalierung um einen Bezugspunkt. `transform.scale` gibt es nur am ObjectNode (und ist per `placement.allowScaling` gegated); Wände/Zonen skalieren heißt, ihre Punkte zu rechnen.' },
  { werkzeugId: 'sperren', marke: 'deckt', begruendung: 'SET_NODES_GESPERRT' },
  { werkzeugId: 'verschieben', marke: 'deckt', begruendung: 'MOVE_NODE' },
  { werkzeugId: 'verteilen', marke: 'fehlt', begruendung: 'Braucht gleichmäßige Verteilung mehrerer Knoten entlang einer Achse — dieselbe Lücke wie `ausrichten`: die Zielposition je Knoten ist zu rechnen, nicht zu übergeben.' },
  { werkzeugId: 'vertikal-spiegeln', marke: 'deckt', begruendung: 'UPDATE_NODE' },
  { werkzeugId: 'teilen', marke: 'fehlt', begruendung: 'Eine Wand an einem Punkt in zwei zu teilen heißt: einen Knoten ändern UND einen anlegen, in EINEM umkehrbaren Schritt. Zwei getrennte Befehle wären zwei Undo-Schritte.' },
  { werkzeugId: 'trimmen', marke: 'fehlt', begruendung: 'Braucht Schnittpunktrechnung zweier Wände und ein Kürzen auf den Schnittpunkt — `UPDATE_NODE` könnte das Ergebnis setzen, aber der Befehl, der es rechnet, fehlt.' },
  { werkzeugId: 'verbinden', marke: 'fehlt', begruendung: 'Zwei Wände zu einer zu verschmelzen heißt: einen ändern, einen entfernen, in EINEM Schritt. Dieselbe Klasse wie `teilen`.' },
  { werkzeugId: 'verlaengern', marke: 'fehlt', begruendung: 'Wie `trimmen`, andere Richtung — Schnittpunkt rechnen und den Endpunkt dorthin ziehen.' },
  { werkzeugId: 'versatz', marke: 'fehlt', begruendung: 'Parallelversatz erzeugt eine NEUE Wand im Abstand d — die Geometrie dafür liegt bereits in `editierGeometrie.versetzteWand`, der Befehl, der sie anlegt, fehlt.' },

  // --- create: 2D-Grundformen (6) — der Bauplaner kennt keine freien Zeichenprimitive -------------
  { werkzeugId: 'bogen', marke: 'stillgelegt', begruendung: 'Freie 2D-Zeichenprimitive gibt es im Gebäudemodell nicht — jeder Knoten ist ein Bauteil. Ein Bogen ohne Bauteilbezug hätte keine Fachbedeutung. MELDUNG an den Planner, nicht entfernt.' },
  { werkzeugId: 'kreis', marke: 'stillgelegt', begruendung: 'Wie `bogen` — freies Zeichenprimitiv ohne Bauteilbezug.' },
  { werkzeugId: 'linie', marke: 'stillgelegt', begruendung: 'Wie `bogen`. Wer eine Wand zieht, benutzt `wand`; eine freie Linie hat kein Gegenstück im Modell.' },
  { werkzeugId: 'polygon', marke: 'stillgelegt', begruendung: 'Wie `bogen`. Eine geschlossene Fläche im Modell ist eine `zone`, kein Polygon.' },
  { werkzeugId: 'polylinie', marke: 'stillgelegt', begruendung: 'Wie `bogen`. Ein Linienzug im Modell ist eine `route` (Leitung), keine freie Polylinie.' },
  { werkzeugId: 'rechteck', marke: 'stillgelegt', begruendung: 'Wie `bogen`. Ein Rechteck im Grundriss entsteht aus vier Wänden.' },

  // --- view (7) — Ansicht berührt das Modell nie ---------------------------------------------------
  { werkzeugId: 'alles-anzeigen', marke: 'ohne-modell', begruendung: 'Ansicht. `einpassen.ts` rechnet, der Store hält Zoom/Pan — kein Szenenknoten.' },
  { werkzeugId: 'fang', marke: 'deckt', begruendung: 'UPDATE_SETTINGS' },
  { werkzeugId: 'umkreisen', marke: 'ohne-modell', begruendung: 'Kamerabewegung in der 3D-Ansicht.' },
  { werkzeugId: 'hand', marke: 'ohne-modell', begruendung: 'Ansichtsverschub (`pan.ts`), UI-Zustand.' },
  { werkzeugId: 'raster', marke: 'ohne-modell', begruendung: 'Rasteranzeige ist ein lokaler Schalter in `HausplanerApp` (`rasterAn`) — anders als `fang`, das in `settings` steht.' },
  { werkzeugId: 'vergroessern', marke: 'ohne-modell', begruendung: 'Zoom, UI-Zustand.' },
  { werkzeugId: 'verkleinern', marke: 'ohne-modell', begruendung: 'Zoom, UI-Zustand.' },

  // --- measurement (5) ----------------------------------------------------------------------------
  { werkzeugId: 'bemassen', marke: 'fehlt', begruendung: 'Eine PERSISTENTE Maßlinie ist ein Knoten und müsste im Schema existieren — heute gibt es sie nicht (`bemassung.ts` rechnet nur die automatischen Ketten). Braucht Schemafeld UND Befehl.' },
  { werkzeugId: 'distanz-messen', marke: 'ohne-modell', begruendung: 'Temporäre Messung, wird nicht gespeichert.' },
  { werkzeugId: 'flaeche-messen', marke: 'ohne-modell', begruendung: 'Temporäre Messung — anders als die Raumfläche, die aus der Raumerkennung kommt.' },
  { werkzeugId: 'volumen-messen', marke: 'ohne-modell', begruendung: 'Temporäre Messung.' },
  { werkzeugId: 'winkel-messen', marke: 'ohne-modell', begruendung: 'Temporäre Messung.' },

  // --- import (8) — AUF-88-P1 hat den PDF-Weg gebaut, aber NICHT ins Szenendokument ----------------
  { werkzeugId: 'beschneiden', marke: 'ohne-modell', begruendung: 'Die Referenzunterlage ist bewusst KEIN Knoten im Gebäudemodell (AUF-88-P1/K-03) — Beschneiden wäre eine Eigenschaft des Uploads, kein Modellbefehl.' },
  { werkzeugId: 'bild-importieren', marke: 'ohne-modell', begruendung: 'Gebaut in AUF-88-P1: der Upload liegt in `plan_uploads`, nicht im Szenendokument.' },
  { werkzeugId: 'datei-importieren', marke: 'ohne-modell', begruendung: 'Wie `bild-importieren` — gebaut in AUF-88-P1, ohne Modellzugriff.' },
  { werkzeugId: 'erkennung-bestaetigen', marke: 'fehlt', begruendung: 'Erkannte Wände als echte Knoten zu übernehmen ist ein Mehrfach-Anlegen in EINEM umkehrbaren Schritt — `ADD_NODE` legt einen einzelnen an. Dies ist der Übergang, den AUF-88-P1 ausdrücklich ausgeschlossen hat (`ausschluesse: der Inhalt des PDF`).' },
  { werkzeugId: 'grundriss-erkennen', marke: 'ohne-modell', begruendung: 'Die Erkennung selbst liefert Kandidaten (`kandidat_geometrie` am Upload) — sie ändert das Modell nicht. Erst `erkennung-bestaetigen` täte das.' },
  { werkzeugId: 'ki-assistent', marke: 'ohne-modell', begruendung: 'Vorschläge, keine Änderung. Und: generative KI ist per CLAUDE.md ausdrücklich außer Scope, bis sie separat geplant wird.' },
  { werkzeugId: 'kalibrieren', marke: 'ohne-modell', begruendung: 'Gebaut in AUF-88-P1: der Maßstab steht am Upload (`massstab_mm_pro_einheit`), nicht im Szenendokument.' },
  { werkzeugId: 'nordrichtung-setzen', marke: 'fehlt', begruendung: 'Die Nordrichtung ist eine Eigenschaft des Gebäudes (PV, Verschattung) und gehörte in `settings` — dort gibt es sie heute nicht. `UPDATE_SETTINGS` existiert, das FELD fehlt.' },

  // --- create: Bauteile (34) — hier greift der ADD_NODE/ADD_ROOF/ADD_CEILING-Vorrat ----------------
  { werkzeugId: 'boden', marke: 'deckt', begruendung: 'ADD_CEILING' },
  { werkzeugId: 'dach', marke: 'deckt', begruendung: 'ADD_ROOF' },
  { werkzeugId: 'dachfenster', marke: 'deckt', begruendung: 'ADD_ROOF_AUFBAU' },
  { werkzeugId: 'decke', marke: 'deckt', begruendung: 'ADD_CEILING' },
  { werkzeugId: 'aufriss', marke: 'ohne-modell', begruendung: 'Eine Ansicht auf das Modell, kein Bauteil — dieselbe Klasse wie `schnitt`.' },
  { werkzeugId: 'fenster', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'gaube', marke: 'deckt', begruendung: 'ADD_ROOF_AUFBAU' },
  { werkzeugId: 'raum', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'schnitt', marke: 'ohne-modell', begruendung: 'Eine Schnittansicht ist eine Darstellung, kein Knoten.' },
  { werkzeugId: 'stuetze', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'treppe', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'tuer', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'unterzug', marke: 'fehlt', begruendung: 'Ein Unterzug ist ein tragender Balken — `ObjectNode.objectType` kennt ihn nicht, und ein Träger als `furniture` zu führen wäre eine Unwahrheit im Modell. Braucht einen Typ, dann deckt ADD_NODE.' },
  { werkzeugId: 'wand', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'oeffnung', marke: 'deckt', begruendung: 'ADD_NODE' },

  // --- assign-or-calculate (9) — Zuweisen von Eigenschaften an vorhandene Knoten ------------------
  { werkzeugId: 'fassadensystem', marke: 'deckt', begruendung: 'UPDATE_NODE' },
  { werkzeugId: 'klinker', marke: 'deckt', begruendung: 'UPDATE_NODE' },
  { werkzeugId: 'material-aufnehmen', marke: 'ohne-modell', begruendung: 'Pipette: liest eine Eigenschaft, ändert nichts.' },
  { werkzeugId: 'material-zuweisen', marke: 'deckt', begruendung: 'UPDATE_NODE' },
  { werkzeugId: 'textur', marke: 'deckt', begruendung: 'UPDATE_NODE' },
  { werkzeugId: 'daemmung', marke: 'deckt', begruendung: 'UPDATE_NODE' },
  { werkzeugId: 'lueftung', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'thermische-huelle', marke: 'ohne-modell', begruendung: 'Eine Auswertung über vorhandene Bauteile (welche Fläche grenzt an außen) — sie liest, sie schreibt nicht.' },
  { werkzeugId: 'u-wert', marke: 'ohne-modell', begruendung: 'Rechnung über die Wandschichten (`uWert.ts`, bereits gebaut) — Ergebnis, kein Modellzustand.' },

  // --- Heizung / Sanitär / Küche (12) --------------------------------------------------------------
  { werkzeugId: 'fussbodenheizung', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'heizkoerper', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'hydraulischer-abgleich', marke: 'ohne-modell', begruendung: 'Eine Rechnung über die Anlage (`HydraulicService` im CRM) — Ergebnis, kein Modellzustand.' },
  { werkzeugId: 'pumpe', marke: 'fehlt', begruendung: '`ObjectNode.objectType` kennt keine Pumpe. Braucht einen Typ, dann deckt ADD_NODE.' },
  { werkzeugId: 'waermepumpe', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'rohrleitung', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'badewanne', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'dusche', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'wc', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'geraet', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'kuechenplanung', marke: 'ohne-modell', begruendung: 'Ein Arbeitsbereich, kein Bauteil — die Möbel darin sind `schrank`/`geraet`.' },
  { werkzeugId: 'schrank', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'sanitaeranschluss', marke: 'deckt', begruendung: 'ADD_NODE' },

  // --- Elektro / PV (8) -----------------------------------------------------------------------------
  { werkzeugId: 'elektroplanung', marke: 'ohne-modell', begruendung: 'Ein Arbeitsbereich, kein Bauteil — dieselbe Klasse wie `kuechenplanung`.' },
  { werkzeugId: 'leuchte', marke: 'fehlt', begruendung: '`ObjectNode.objectType` kennt keine Leuchte. Braucht einen Typ, dann deckt ADD_NODE.' },
  { werkzeugId: 'schalter', marke: 'fehlt', begruendung: '`ObjectNode.objectType` kennt keinen Schalter. Braucht einen Typ, dann deckt ADD_NODE.' },
  { werkzeugId: 'steckdose', marke: 'fehlt', begruendung: '`ObjectNode.objectType` kennt keine Steckdose. Braucht einen Typ, dann deckt ADD_NODE.' },
  { werkzeugId: 'verteiler', marke: 'fehlt', begruendung: '`ObjectNode.objectType` kennt keinen Verteiler. Braucht einen Typ, dann deckt ADD_NODE.' },
  { werkzeugId: 'batteriespeicher', marke: 'deckt', begruendung: 'ADD_NODE' },
  { werkzeugId: 'pv-modul', marke: 'fehlt', begruendung: '`ObjectNode.objectType` kennt `inverter` und `battery`, aber kein PV-Modul. Braucht einen Typ, dann deckt ADD_NODE.' },
  { werkzeugId: 'wallbox', marke: 'deckt', begruendung: 'ADD_NODE' },

  // --- workflow (15) — Arbeitsablauf, nicht Gebäude -------------------------------------------------
  { werkzeugId: 'freigeben', marke: 'ohne-modell', begruendung: 'Freigabe ist ein Vorgang am Dokument/Projekt, kein Knoten. Das CRM führt Freigaben bereits.' },
  { werkzeugId: 'prozessuebersicht', marke: 'ohne-modell', begruendung: 'Anzeige eines Ablaufs, kein Modellzustand.' },
  { werkzeugId: 'assistent', marke: 'ohne-modell', begruendung: 'Führt durch vorhandene Werkzeuge — was er auslöst, sind deren Befehle.' },
  { werkzeugId: 'uebergabepaket', marke: 'ohne-modell', begruendung: 'Ein Export-Bündel, kein Modellzustand.' },
  { werkzeugId: 'fehler', marke: 'ohne-modell', begruendung: 'Anzeige der Befunde (`befunde.ts`), kein Knoten.' },
  { werkzeugId: 'pruefen', marke: 'ohne-modell', begruendung: 'Prüflauf über das Modell — liest, schreibt nicht.' },
  { werkzeugId: 'warnungen', marke: 'ohne-modell', begruendung: 'Anzeige, kein Modellzustand.' },
  { werkzeugId: 'historie', marke: 'ohne-modell', begruendung: 'Der Verlauf liegt im Store (Undo-Stapel) und in den Snapshots — nicht im Szenendokument.' },
  { werkzeugId: 'kommentar', marke: 'fehlt', begruendung: 'Ein Kommentar AM BAUTEIL wäre ein Knoten oder ein Feld — beides fehlt. **Vor dem Bauen prüfen, ob die vorhandene CRM-Kommentarfunktion andockt** (Existing-Code-First, CLAUDE.md).' },
  { werkzeugId: 'revision', marke: 'ohne-modell', begruendung: 'Revisionen führt `hausplaner_documents.revision` + die Snapshots — bereits gebaut, kein neuer Befehl.' },
  { werkzeugId: 'befehlspalette', marke: 'ohne-modell', begruendung: 'Zugang zu anderen Werkzeugen (AUF-83-T3/K-05b), kein eigener Modellzugriff.' },
  { werkzeugId: 'einstellungen', marke: 'deckt', begruendung: 'UPDATE_SETTINGS' },
  { werkzeugId: 'exportieren', marke: 'ohne-modell', begruendung: 'Liest das Modell, ändert es nicht.' },
  { werkzeugId: 'pdf', marke: 'ohne-modell', begruendung: 'Wie `exportieren`.' },
  { werkzeugId: 'suche', marke: 'ohne-modell', begruendung: 'Findet Knoten, ändert keine (`trefferSuche.ts`).' },
];

/** Wie viele Einträge je Marke — das Produkt dieser Stufe. */
export function markenZaehlung(): Record<LandkartenMarke, number> {
  const z: Record<LandkartenMarke, number> = { deckt: 0, fehlt: 0, 'ohne-modell': 0, stillgelegt: 0 };
  for (const e of WERKZEUG_LANDKARTE) z[e.marke] += 1;

  return z;
}

/** Die Verträge ohne Landkarteneintrag — muss leer sein (K-01). */
export function vertraegeOhneEintrag(): string[] {
  const bekannt = new Set(WERKZEUG_LANDKARTE.map((e) => e.werkzeugId));

  return WERKZEUG_VERTRAEGE.filter((v) => !bekannt.has(v.werkzeugId)).map((v) => v.werkzeugId);
}

/** Landkarteneinträge ohne Vertrag — muss ebenfalls leer sein (die Gegenrichtung von K-01). */
export function eintraegeOhneVertrag(): string[] {
  const bekannt = new Set(WERKZEUG_VERTRAEGE.map((v) => v.werkzeugId));

  return WERKZEUG_LANDKARTE.filter((e) => !bekannt.has(e.werkzeugId)).map((e) => e.werkzeugId);
}
