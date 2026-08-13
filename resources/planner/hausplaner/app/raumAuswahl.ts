/**
 * W-05/2 — **die Auswahl eines erkannten Raums, und warum sie sich selbst zurücksetzt.**
 *
 * **Räume werden ERKANNT, nicht gezeichnet.** Sie sind ein abgeleitetes Ergebnis aus den Wänden
 * (`ableitungen.ts:61 raeumeAus`) und tragen deshalb **keine Kennung**: der Typ `ErkannterRaum`
 * (`geometry/roomDetection.ts:35-40`) führt `polygon`, `kanten`, `flaecheMm2` und `volumenMm3` —
 * mehr nicht. Ihre heutige Identität ist der **Index in der Liste** (`Buehne.tsx:147`,
 * `key={\`raum${i}\`}`).
 *
 * **Daraus folgt die ganze Auflage dieses Auftrags (W-05-2-1):** eine Auswahl, die einen Wandzug
 * überlebt, zeigt danach auf einen **anderen** Raum als den gewählten — und der Nutzer kann es
 * nicht merken, weil die Hervorhebung gleich aussieht. *Das ist eine Falschauskunft, dieselbe
 * Klasse wie die Panel-Zusage in A-24 — nur an der Auswahl statt am Hinweis.*
 *
 * **Warum eine SIGNATUR und kein `useEffect`.** Ein Effekt räumt die Auswahl *nach* dem Rendern
 * auf; für einen Bilddurchlauf stünde die Hervorhebung dann auf dem falschen Raum. Die Signatur
 * wird bei jedem Rendern **mitgeprüft** — eine veraltete Auswahl gilt schon in demselben Durchlauf
 * als keine. *Und sie schreibt nichts: A-24 hat gezeigt, was ein Effekt anrichtet, der ins Modell
 * greift.*
 *
 * **Die Auswahl ist FLÜCHTIG** — sie lebt in der Sitzung, wird nicht gespeichert und gehört nicht
 * ins Szenendokument. **Ein NAME wäre etwas anderes:** er ist dauerhaft, bräuchte eine Identität,
 * die es nicht gibt, und damit eine Entscheidung Yamas. *Er ist ausdrücklich nicht Gegenstand
 * dieses Auftrags.*
 */

/** Was zum Auswählen genügt: die Fläche und ihre Ecken. Bewusst schmaler als `ErkannterRaum`. */
export interface RaumFuerAuswahl {
  readonly flaecheMm2: number;
  readonly polygon: ReadonlyArray<{ readonly x: number; readonly y: number }>;
}

/** Die gemerkte Auswahl: der Index UND die Signatur der Liste, aus der er stammt. */
export interface RaumAuswahl {
  readonly index: number;
  readonly signatur: string;
}

/**
 * Der Fingerabdruck der Raumliste.
 *
 * **Anzahl, Fläche, Eckenzahl UND ORT je Raum.**
 *
 * **BERICHTIGT (W-05/2, Runde 2), und die alte Begründung war sachlich falsch.** *Hier stand:
 * „zwei Räume, die in Anzahl, Fläche und Eckenzahl übereinstimmen, sind für die Frage ‚zeigt der
 * Index noch auf denselben Raum?' nicht unterscheidbar."* **Sie sind es — durch ihren ORT, und
 * genau den sieht der Nutzer.**
 *
 * Der Evaluator hat es am gebauten Code gefahren, ich habe es nachgefahren:
 *
 * ```text
 * zwei Raeume, je 10 000 000 mm², 4 Ecken, verschiedene Orte, Reihenfolge getauscht
 *   Signatur vorher  2|10000000:4,10000000:4   fuer BEIDE Listen — identisch
 *   gewaehlt         Raum bei x = 0
 *   hervorgehoben    Raum bei x = 900
 * ```
 *
 * **Das ist wörtlich die Falschauskunft, die diese Datei verhindern soll** — *meine Signatur war
 * blind für genau den Fall, den ihre eigene Begründung ausgeschlossen hat.*
 *
 * **Der Ort ist der erste Polygonpunkt, auf ganze mm gerundet.** *Er genügt: zwei Räume, die in
 * Anzahl, Fläche, Eckenzahl **und** Startpunkt übereinstimmen, sind an derselben Stelle — dann ist
 * es derselbe Raum, und ein Tausch wäre keiner.*
 *
 * **Gerundet wird bei Fläche und Ort aus demselben Grund:** *zwei Ableitungen desselben Grundrisses
 * dürfen sich nicht wegen eines Gleitkomma-Restes unterscheiden — sonst setzte die Auswahl sich bei
 * jedem Rendern zurück, und die Auflage wäre in ihr Gegenteil verkehrt.*
 */
export function raumSignatur(raeume: ReadonlyArray<RaumFuerAuswahl>): string {
  return `${raeume.length}|` + raeume
    .map((r) => {
      const p = r.polygon[0];
      const ort = p ? `${Math.round(p.x)}/${Math.round(p.y)}` : '-';
      return `${Math.round(r.flaecheMm2)}:${r.polygon.length}@${ort}`;
    })
    .join(',');
}

/**
 * Der gültige Auswahlindex — oder `null`.
 *
 * **`null` in drei Fällen, und jeder ist gewollt:** keine Auswahl gemerkt; die Liste hat sich
 * geändert (Signatur weicht ab); der Index liegt außerhalb. *Der dritte ist ein Gürtel neben dem
 * Hosenträger — bei abweichender Signatur greift schon der zweite —, aber er kostet nichts und
 * hält, falls die Signatur je gröber wird.*
 */
export function gueltigeAuswahl(
  auswahl: RaumAuswahl | null,
  raeume: ReadonlyArray<RaumFuerAuswahl>,
): number | null {
  if (!auswahl) return null;
  if (auswahl.signatur !== raumSignatur(raeume)) return null;
  if (auswahl.index < 0 || auswahl.index >= raeume.length) return null;
  return auswahl.index;
}

/** Eine Auswahl setzen: der Index zusammen mit der Signatur der Liste, aus der er stammt. */
export function waehleRaum(index: number, raeume: ReadonlyArray<RaumFuerAuswahl>): RaumAuswahl {
  return { index, signatur: raumSignatur(raeume) };
}
