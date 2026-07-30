/**
 * AUF-83-T3 / K-01 — **der Objektkopf, gelesen statt zusammengebaut.**
 *
 * T2 hat die Leiste über der Insel abgeräumt und **einen** Teil ausdrücklich stehen lassen:
 * *„Objektname mit Adresse und der Uebernehmen-Knopf samt Staleness-Pille … er wandert mit T3 in
 * die Kopfleiste, dorthin, wo ohnehin Projekt · Geschoss steht."* Das ist dieser Umzug.
 *
 * **Dieselbe Naht wie `data-rechte`, `data-projekte`, `data-speichern-url`** — das Blade setzt,
 * `main.tsx` liest, der UI-Zustand hält. Der Blade-Kommentar von T2 nennt sie selbst:
 * *„Dieselbe Naht wie `data-speichern-url`, kein neuer Mechanismus."*
 *
 * **Was hier NICHT passiert: rechnen.** Der Übernahme-Status kommt fertig aus
 * `ErmittleUebernahmeStatus` (Hash der aktiven Profil-Version gegen Hash der Szene). Ihn hier aus
 * der Szene abzuleiten wäre eine **zweite Statusquelle** — genau das, was das Blade sich
 * ausdrücklich verboten hat: *„keine zweite Statusquelle im Blade."* Die Insel erbt dieses Verbot.
 *
 * **Alles Unerwartete ergibt `null` — nie einen halb gefüllten Kopf.** Dieselbe Richtung wie bei
 * den Rechten (AUF-60) und den Projekten (AUF-78): ein kaputter Wert darf nie mehr behaupten als
 * ein fehlender. Ein Kopf mit Namen, aber ohne Übernahme-Ziel, zeigte einen Knopf ohne Wirkung.
 *
 * **Das Studio hat keinen Objektkopf, und das ist richtig so.** Dort gibt es kein Objekt (Scratch,
 * kein `data-speichern-url`). Fehlt das Attribut, bleibt die Kopfleiste bei Projekt und Geschoss.
 */

/** Das Attribut am Mount-Knoten (`data-objektkopf`) — dieselbe Naht wie `data-projekte`. */
export const OBJEKTKOPF_ATTRIBUT = 'objektkopf';

/**
 * Die drei Zustände der Übernahme, wörtlich die des Blades.
 *
 * **Kein `boolean`.** „Nie übernommen" und „veraltet" sind zwei verschiedene Aussagen: das eine
 * heißt *hier ist noch nichts passiert*, das andere *es ist etwas passiert und gilt nicht mehr*.
 * Ein Ja/Nein hätte sie zusammengeworfen, und die Pille hätte lügen müssen.
 */
export type UebernahmeStatus = 'aktuell' | 'veraltet' | 'nie';

export interface Objektkopf {
  /** Objektname oder `Objekt #<id>` — der Server entscheidet, die Insel zeigt. */
  name: string;
  /** Die volle Adresse. Darf leer sein: nicht jedes Objekt hat eine. */
  adresse: string;
  /** Das Ziel des Übernehmen-Formulars, fertig vom Server (`route(...)`). */
  uebernehmenUrl: string;
  /** Der Stand der Übernahme — aus `ErmittleUebernahmeStatus`, hier nur gelesen. */
  status: UebernahmeStatus;
  /**
   * Die Revision der übernommenen Szene. Nur bei `aktuell` gesetzt und nur dann gezeigt —
   * eine Revisionsnummer neben „noch nie übernommen" wäre ein Widerspruch in einer Zeile.
   */
  revision?: number;
  /**
   * Ist die Szene leer, kann nichts übernommen werden.
   *
   * **Der Knopf wird dann gesperrt, nicht versteckt** — genauso wie heute im Blade (`@disabled`).
   * Ein verschwundener Knopf sagt nicht, dass er wiederkommt, wenn man zeichnet.
   */
  szeneLeer: boolean;
}

const STATUS: readonly string[] = ['aktuell', 'veraltet', 'nie'];

function istKopf(x: unknown): x is Objektkopf {
  if (typeof x !== 'object' || x === null) return false;
  const o = x as Record<string, unknown>;
  /**
   * Die Revision darf fehlen — steht sie da, muss sie eine Zahl sein. Eine Zeichenkette landete
   * sonst ungeprüft in der Pille.
   *
   * **`null` IST „fehlt", und das war der Fehler (Befund `T3-K01-B1`, P1).** Die erste Fassung
   * prüfte nur `!== undefined`. Das Blade sendet aber `'revision' => $uebernahme['szene_revision']
   * ?? null` — und `JSON.parse` macht daraus **`null`**, nicht `undefined`. `typeof null` ist
   * `"object"`, die Prüfung schlug an, **und `leseObjektkopf` verwarf den GANZEN Kopf**: auf
   * 11 von 12 Objekten (alle mit Status `nie`) fehlten Name, Knopf und Pille vollständig.
   *
   * *Mein Kommentar sagte „darf fehlen" und mein Code sagte etwas anderes. Gefunden hat es der
   * Evaluator in der Sichtprobe — kein Gate konnte es sehen, weil kein Test je die echte Nutzlast
   * des Blades gelesen hat. Genau dagegen steht jetzt `objektkopf.test.ts` / „die Feldliste".*
   */
  if (o.revision !== undefined && o.revision !== null && typeof o.revision !== 'number') return false;
  return typeof o.name === 'string' && o.name.length > 0
    && typeof o.adresse === 'string'
    && typeof o.uebernehmenUrl === 'string' && o.uebernehmenUrl.length > 0
    && typeof o.status === 'string' && STATUS.includes(o.status)
    && typeof o.szeneLeer === 'boolean';
}

/**
 * Der Objektkopf aus dem Attribut.
 *
 * **Fehlt es, ist es leer oder unlesbar, gilt `null`.** Kein Wurf: eine Insel, die wegen eines
 * kaputten Attributs gar nicht erscheint, wäre schlimmer als eine ohne Objektnamen.
 */
export function leseObjektkopf(roh: string | null | undefined): Objektkopf | null {
  if (!roh) {
    return null;
  }
  let geparst: unknown;
  try {
    geparst = JSON.parse(roh);
  } catch {
    return null;
  }
  if (!istKopf(geparst)) {
    return null;
  }
  // **`null` wird zu „nicht gesetzt".** Sonst trüge das Feld einen Wert, den sein Typ nicht kennt,
  // und `kopf.revision ?? '?'` liefe für `null` anders als für `undefined`.
  return geparst.revision === null ? { ...geparst, revision: undefined } : geparst;
}

/**
 * Der Text der Staleness-Pille — **eine Quelle für drei Sätze.**
 *
 * Er stand bisher dreimal im Blade, einmal je Zweig. Hier steht er einmal, und die Zusagen prüfen
 * ihn ohne Browser.
 */
export function pillenText(kopf: Objektkopf): string {
  if (kopf.status === 'aktuell') {
    return `Übernommen — aktuell (Szene Rev. ${kopf.revision ?? '?'})`;
  }
  if (kopf.status === 'veraltet') {
    return 'Übernommen — VERALTET (Szene geändert seit Übernahme)';
  }
  return 'Noch nie übernommen';
}

/** Name und Adresse als eine Zeile — genau die Verbindung, die das Blade heute setzt (` · `). */
export function kopfzeile(kopf: Objektkopf): string {
  return kopf.adresse ? `${kopf.name} · ${kopf.adresse}` : kopf.name;
}
