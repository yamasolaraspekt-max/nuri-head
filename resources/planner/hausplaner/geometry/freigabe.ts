/**
 * Z-06-N1 (B10) — **Die Sperre, als Funktion statt als Kommentar.**
 *
 * B10 sagt nicht nur, dass der Status überleben muss, sondern auch, wozu er da ist: *solange
 * Geometrie nicht bestätigt ist, darf sie für Dach, Mengen, Statik oder jeden anderen
 * Folgeprozess nicht als verlässliche Grundlage dienen.*
 *
 * **Ein Hinweis im Kommentar erfüllt das nicht.** Wer die Decke morgen für die Dachauflage
 * benutzt, liest keinen Kommentar — er ruft eine Funktion auf. Deshalb steht die Regel hier als
 * Aufruf, den man nicht versehentlich übergeht.
 *
 * **Die Ablehnung nennt IMMER den Grund.** Dieselbe Auflage wie bei der Erlaubnisliste in W-01
 * und bei der `UNVERAENDERT`-Meldung des Commit-Tors: eine Ablehnung ohne Grund schickt den
 * nächsten Leser suchen — und wer sucht, probiert irgendwann aus.
 */
import type { z } from 'zod/v4';
import type { freigabeSchema, geometrieHerkunftSchema } from '../domain/validation';

export type Freigabe = z.infer<typeof freigabeSchema>;
export type GeometrieHerkunft = z.infer<typeof geometrieHerkunftSchema>;

/** Alles, was eine Freigabe trägt — Decke, Dach, und was später dazukommt. */
export type MitFreigabe = {
  freigabe: Freigabe;
  geometrieHerkunft: GeometrieHerkunft;
  id?: string;
  type?: string;
};

/**
 * Genau EIN Wert gibt frei. Die Aufzählung steht bewusst nicht als `!== 'abgelehnt'` da:
 * käme morgen ein fünfter Wert dazu, wäre er mit der Negativ-Form stillschweigend freigegeben.
 */
export function istFreigegeben(node: Pick<MitFreigabe, 'freigabe'>): boolean {
  return node.freigabe === 'bestaetigt';
}

/** Klartext je Wert — was der Leser wissen muss, um weiterzukommen, nicht was schiefging. */
const GRUND: Record<Freigabe, string> = {
  bestaetigt: '',
  vorschlag: 'Die Geometrie ist ein Vorschlag und wurde noch nicht bestätigt.',
  zu_pruefen: 'Die Geometrie stammt aus dem Bestand und wurde noch nie geprüft.',
  abgelehnt: 'Die Geometrie wurde geprüft und abgelehnt.',
};

/**
 * Der Fehler trägt den Knoten mit, damit der Aufrufer nicht suchen muss, WELCHE Decke gemeint ist.
 *
 * *Die Felder stehen ausgeschrieben statt als Parameter-Properties: der Testläufer der Insel
 * fährt `--experimental-strip-types` und lehnt `constructor(readonly x: T)` ab
 * (`ERR_UNSUPPORTED_TYPESCRIPT_SYNTAX`). Beim ersten Anlauf hat er die ganze Datei verweigert —
 * die Kürzung hätte nur der Quelle genützt, nicht dem Lauf.*
 */
export class FreigabeFehlt extends Error {
  readonly node: Pick<MitFreigabe, 'freigabe' | 'geometrieHerkunft' | 'id' | 'type'>;
  readonly grund: string;

  constructor(node: Pick<MitFreigabe, 'freigabe' | 'geometrieHerkunft' | 'id' | 'type'>, grund: string) {
    super(grund);
    this.name = 'FreigabeFehlt';
    this.node = node;
    this.grund = grund;
  }
}

/**
 * Die Schranke vor jedem Folgeprozess: gibt den Knoten zurück oder wirft — mit Grund.
 *
 * *Rückgabewert statt `void`, damit der Aufruf sich nicht weglassen lässt, ohne dass es auffällt:*
 * `const decke = verlangeFreigabe(kandidat)` liest sich als Teil der Rechnung, ein `pruefe(x)`
 * daneben liest sich als Zierrat.
 */
/**
 * **Welchen Status bekommt eine FRISCH ANGELEGTE Decke?** Die Entscheidung steht hier und nicht
 * im Klick-Handler.
 *
 * *Gemessen, warum:* die Mutationsprobe zu diesem Blatt hatte drei blinde Flecken, und alle drei
 * saßen in `HausplanerApp.tsx` — „Herkunft fest auf `manuell`", „Freigabe fest auf `bestaetigt`",
 * „Dach setzt die Felder nicht". **Keine Zusage konnte sie fangen, weil eine Entscheidung in
 * einem Klick-Handler nur über einen Klick prüfbar ist.** Als Funktion ist sie es direkt (B3).
 *
 * Aus einer gezeichneten Kontur ist die Decke `manuell` und damit `bestaetigt` — jemand hat sie
 * gesetzt. Aus dem Gebäude-Umriss ist sie `abgeleitet` und bleibt `vorschlag`, bis jemand hinsieht.
 */
export function herkunftFuerNeueDecke(ausKontur: boolean): Pick<MitFreigabe, 'geometrieHerkunft' | 'freigabe'> {
  return ausKontur
    ? { geometrieHerkunft: 'manuell', freigabe: 'bestaetigt' }
    : { geometrieHerkunft: 'abgeleitet', freigabe: 'vorschlag' };
}

/**
 * **Welchen Status bekommt ein FRISCH ANGELEGTES Dach?** — Z-07.
 *
 * Bis heute stand hier eine Konstante mit der Begründung *„es gibt keinen Weg, das Dach aus einer
 * gezeichneten Kontur zu erzeugen"*. **Z-07 baut genau diesen Weg**, und damit wird aus der
 * Konstante eine Funktion. *Die Konstante fällt weg, sie bleibt nicht daneben stehen:* zwei
 * Antworten auf „woher kommt das Dach" sind eine zweite Wahrheit, und der nächste Aufrufer nimmt
 * die falsche.
 *
 * Zeichengleich mit `herkunftFuerNeueDecke` — dieselbe Frage am anderen Bauteil, deshalb dieselbe
 * Form. *Zwei Bauteile mit derselben Regel dürfen nicht zwei Regeln haben.*
 */
export function herkunftFuerNeuesDach(ausKontur: boolean): Pick<MitFreigabe, 'geometrieHerkunft' | 'freigabe'> {
  return ausKontur
    ? { geometrieHerkunft: 'manuell', freigabe: 'bestaetigt' }
    : { geometrieHerkunft: 'abgeleitet', freigabe: 'vorschlag' };
}

/**
 * Setzt die Freigabe und stempelt — **nur wenn sich der Wert wirklich ändert.**
 *
 * Die zweite Hälfte ist die wichtige: wer bei jedem Speichern stempelt, macht aus dem Zeitstempel
 * eine Speicher-Uhr. *Dann sagt „bestätigt am 3.8. 19:40" nichts mehr darüber, wann jemand
 * hingesehen hat — und die Zeile, die Vertrauen stiften soll, ist die unzuverlässigste im
 * Dokument.*
 *
 * Rein: gibt einen neuen Knoten zurück, verändert den übergebenen nicht (Muster `migriereSzene`).
 */
export function setzeFreigabe<T extends MitFreigabe>(node: T, neu: Freigabe, von: string, jetzt: string): T {
  if (node.freigabe === neu) {
    return node;
  }
  return { ...node, freigabe: neu, freigabeAm: jetzt, freigabeVon: von };
}

export function verlangeFreigabe<T extends MitFreigabe>(node: T, wofuer = 'diesen Schritt'): T {
  if (istFreigegeben(node)) {
    return node;
  }
  const was = node.type ? `${node.type}${node.id ? ` ${node.id}` : ''}` : 'Die Geometrie';
  const grund =
    `${was}: nicht freigegeben für ${wofuer}. ${GRUND[node.freigabe]} ` +
    `(Herkunft: ${node.geometrieHerkunft}, Freigabe: ${node.freigabe})`;
  throw new FreigabeFehlt(node, grund.trim());
}
