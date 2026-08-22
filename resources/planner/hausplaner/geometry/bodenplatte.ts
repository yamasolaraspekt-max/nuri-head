/**
 * **Z1-E4-1 — die Fragen rund um die Bodenplatte, die keine Darstellung sind.**
 *
 * Zwei davon entscheiden über eine Anzeige (`hinweisBodenplatte`) und über eine Vorbelegung
 * (`istErdberuehrtVorbelegung`). **Beide wohnen hier und nicht im Klick-Handler**, weil sie sonst
 * nur über den Browser prüfbar wären — genau die Lehre aus `geometry/freigabe.ts`, wo dieselbe
 * Entscheidung als Ausdruck in der Komponente stand und *drei Mutationen durchkamen.*
 */
import type { Level } from '../domain/scene.types';

/** Nur `elevation` wird gebraucht — die Aufrufer sollen keinen vollen `Level` beschaffen müssen. */
type MitHoehe = Pick<Level, 'id' | 'elevation'>;

/**
 * **Ist das die unterste Etage des Gebäudes?**
 *
 * Verglichen wird über `elevation`, nicht über `sortOrder`: die Höhe ist die bauliche Wahrheit,
 * die Sortierung ist eine Anzeigereihenfolge. *Wer nach `sortOrder` fragt, bekommt beim ersten
 * umsortierten Geschoss eine falsche Antwort.*
 */
export function istUntersteEtage(levels: readonly MitHoehe[], levelId: string): boolean {
  const eigene = levels.find((l) => l.id === levelId);
  if (!eigene) return false;
  return !levels.some((l) => l.id !== levelId && l.elevation < eigene.elevation);
}

/**
 * **Kriterium (c): liegt ein Geschoss darunter, wird HINGEWIESEN — nicht abgelehnt.**
 *
 * *Der Keller mit eigener Sohle ist ein gültiger Fall.* Eine Ablehnung würde ihn verbauen, und
 * genau deshalb steht im Blatt die Absage-Regel: **„Eine Ablehnung erfüllt (c) nicht."**
 *
 * Rückgabe `null` heißt: nichts zu sagen. Der Hinweistext nennt das Geschoss beim Namen, damit
 * der Leser weiß, worauf er schaut, statt eine allgemeine Warnung wegzuklicken (A-03).
 */
export function hinweisBodenplatte(
  levels: readonly Pick<Level, 'id' | 'elevation' | 'name'>[],
  levelId: string,
): string | null {
  const eigene = levels.find((l) => l.id === levelId);
  if (!eigene) return null;
  const darunter = levels
    .filter((l) => l.id !== levelId && l.elevation < eigene.elevation)
    .sort((a, b) => b.elevation - a.elevation);
  if (darunter.length === 0) return null;
  const namen = darunter.map((l) => l.name).join(', ');
  return `Bodenplatte auf ${eigene.name} angelegt — darunter liegt noch ${namen}. `
    + 'Das ist zulässig (eigene Sohle), aber selten gewollt.';
}

/**
 * **Vorbelegung für `erdberuehrt` — ein Vorschlag, keine Festlegung.**
 *
 * Die unterste Etage liegt im Regelfall auf dem Erdreich; darüber nicht. **Der Wert ist im Panel
 * änderbar**, und das ist der Punkt: eine aufgestelzte Bodenplatte oder eine Platte über einer
 * Tiefgarage sind Fälle, die niemand aus der Geometrie ableiten kann. *Eine Ableitung, die man
 * nicht korrigieren kann, wäre eine stille Fachentscheidung — die verbietet CLAUDE.md.*
 */
export function istErdberuehrtVorbelegung(levels: readonly MitHoehe[], levelId: string): boolean {
  return istUntersteEtage(levels, levelId);
}
