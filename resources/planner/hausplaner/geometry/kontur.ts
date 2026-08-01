/**
 * Z-05 — **die Mehrpunkt-Kontur und ihre Prüfung beim Schließen.**
 *
 * ---
 *
 * **Wofür.** Heute liefert der Planer für eine Fläche die **Bounding-Box aller Wände**. Bei einem
 * Rechteck stimmt das zufällig; bei L-, T- oder U-Form ist es falsch — *und zwar still: die Fläche
 * erscheint, sie ist nur zu groß.* Diese Datei liefert den Umriss, den Z-06 dann einsetzt.
 *
 * **Reine Geometrie, kein Konva, kein Zustand.** Sie entscheidet nur, ob eine Punktfolge eine
 * brauchbare Kontur ist — und wenn nicht, warum.
 *
 * ---
 *
 * **Warum `istSichereKonvexeFlaeche` aus `dachAusschnitt.ts` NICHT wiederverwendet wird.**
 *
 * Sie prüft Verwandtes und lehnt Selbstschnitte zuverlässig ab — **aber sie lehnt L-, T- und
 * U-Formen genauso ab**, ausdrücklich und dokumentiert (*„schließt L/T/U aus"*). Das sind genau
 * die Formen, für die es Z-05 gibt. *Eine Wiederverwendung hätte den Zweck der Scheibe
 * zunichtegemacht.* **Begründete Nicht-Wiederverwendung nach Klasse R5** (`CLAUDE.md`).
 *
 * **Wiederverwendet wird dagegen `signierteFlaeche`** aus `roomDetection.ts` — die Flächenformel
 * steht damit weiterhin an genau einer Stelle.
 *
 * ---
 *
 * **Die drei Bedingungen beim Schließen** (Blatt Z-05):
 *
 * ```text
 * mindestens 3 Punkte     das verlangt schon das Zod-Schema (polygon: min(3))
 * kein Selbstschnitt      die Acht — der haeufigste Fehler, und man SIEHT ihn nicht
 * Flaeche groesser null    sonst liegen alle Punkte auf einer Linie
 * ```
 *
 * **Geprüft wird beim Schließen, nicht beim Klicken.** Wer beim dritten Punkt schon gebremst
 * würde, könnte eine L-Form nie zeichnen — sie läuft unterwegs durch Zustände, die für sich
 * genommen entartet sind.
 */
import { signierteFlaeche } from './roomDetection';

export interface KonturPunkt {
  x: number;
  y: number;
}

/** Warum eine Kontur nicht geschlossen werden konnte. */
export type KonturGrund = 'zu-wenig-punkte' | 'selbstschnitt' | 'keine-flaeche';

export interface KonturUrteil {
  ok: boolean;
  grund: KonturGrund | null;
}

/** Die Mindestzahl der Punkte. **Kommt aus dem Zod-Schema** (`polygon: z.array(punkt2).min(3)`). */
export const KONTUR_MIN_PUNKTE = 3;

/**
 * Was der Mensch in der Statusleiste liest. **Jeder Satz nennt den Grund UND den Weg heraus** —
 * *„ungültig" allein lässt jemanden dieselbe Acht ein zweites Mal zeichnen.*
 */
export const KONTUR_MELDUNG: Record<KonturGrund, string> = {
  'zu-wenig-punkte': 'Eine Fläche braucht mindestens drei Punkte — setze noch einen.',
  selbstschnitt: 'Die Kontur überschneidet sich selbst — zieh den letzten Punkt so, dass sich keine zwei Kanten kreuzen.',
  'keine-flaeche': 'Alle Punkte liegen auf einer Linie — das umschließt keine Fläche.',
};

/** Kreuzprodukt (o→a) × (o→b). Vorzeichen = Drehsinn. */
function kreuz(o: KonturPunkt, a: KonturPunkt, b: KonturPunkt): number {
  return (a.x - o.x) * (b.y - o.y) - (a.y - o.y) * (b.x - o.x);
}

/** Liegt `q` innerhalb des Rechtecks über `p`–`r`? Nur sinnvoll, wenn die drei kollinear sind. */
function imKasten(p: KonturPunkt, q: KonturPunkt, r: KonturPunkt): boolean {
  return Math.min(p.x, r.x) <= q.x && q.x <= Math.max(p.x, r.x)
    && Math.min(p.y, r.y) <= q.y && q.y <= Math.max(p.y, r.y);
}

/**
 * Schneiden sich die Strecken `a1a2` und `b1b2`?
 *
 * **Der kollineare Fall ist ausdrücklich mit drin.** Zwei Kanten, die aufeinander LIEGEN, kreuzen
 * sich nicht im Sinne der Vorzeichen — sie sind trotzdem ein Selbstschnitt. *Ohne diesen Zweig
 * käme eine Kontur durch, die auf sich selbst zurückläuft.*
 */
function streckenSchneiden(a1: KonturPunkt, a2: KonturPunkt, b1: KonturPunkt, b2: KonturPunkt): boolean {
  const d1 = kreuz(b1, b2, a1);
  const d2 = kreuz(b1, b2, a2);
  const d3 = kreuz(a1, a2, b1);
  const d4 = kreuz(a1, a2, b2);

  if (((d1 > 0 && d2 < 0) || (d1 < 0 && d2 > 0)) && ((d3 > 0 && d4 < 0) || (d3 < 0 && d4 > 0))) {
    return true;
  }
  if (d1 === 0 && imKasten(b1, a1, b2)) return true;
  if (d2 === 0 && imKasten(b1, a2, b2)) return true;
  if (d3 === 0 && imKasten(a1, b1, a2)) return true;
  if (d4 === 0 && imKasten(a1, b2, a2)) return true;

  return false;
}

/**
 * **Überschneidet sich die geschlossene Kontur selbst?**
 *
 * Geprüft wird jedes Kantenpaar, das **nicht benachbart** ist. Benachbarte Kanten teilen sich
 * einen Eckpunkt — das ist kein Schnitt, sondern die Ecke. *Die letzte Kante ist Nachbar der
 * ersten: die Kontur ist geschlossen, auch wenn man das beim Zählen leicht vergisst.*
 */
export function schneidetSichSelbst(punkte: ReadonlyArray<KonturPunkt>): boolean {
  const n = punkte.length;
  if (n < 4) {
    return false; // Ein Dreieck kann sich nicht selbst schneiden.
  }
  for (let i = 0; i < n; i++) {
    const a1 = punkte[i]!;
    const a2 = punkte[(i + 1) % n]!;
    for (let j = i + 1; j < n; j++) {
      const benachbart = j === i + 1 || (i === 0 && j === n - 1);
      if (benachbart) {
        continue;
      }
      if (streckenSchneiden(a1, a2, punkte[j]!, punkte[(j + 1) % n]!)) {
        return true;
      }
    }
  }

  return false;
}

/**
 * **Das Urteil beim Schließen.** Die Reihenfolge ist nicht beliebig: zu wenige Punkte zuerst,
 * weil man über zwei Punkte weder Schnitt noch Fläche sinnvoll aussagen kann.
 */
export function pruefeKontur(punkte: ReadonlyArray<KonturPunkt>): KonturUrteil {
  if (punkte.length < KONTUR_MIN_PUNKTE) {
    return { ok: false, grund: 'zu-wenig-punkte' };
  }
  if (schneidetSichSelbst(punkte)) {
    return { ok: false, grund: 'selbstschnitt' };
  }
  // Wiederverwendet: die Flächenformel steht in `roomDetection`, nicht ein zweites Mal hier.
  if (Math.abs(signierteFlaeche(punkte as KonturPunkt[])) < 1e-6) {
    return { ok: false, grund: 'keine-flaeche' };
  }

  return { ok: true, grund: null };
}

/**
 * **Was in der Statusleiste steht, während man zeichnet.**
 *
 * Rein, damit es prüfbar ist. *Der Text nennt immer beide Ausgänge — schließen und verwerfen —
 * weil ein angefangener Zug sonst wie eine Sackgasse aussieht.*
 */
export function konturStatusText(
  anzahlPunkte: number,
  fehler: KonturGrund | null,
  geschlossenePunkte: number | null = null,
): string {
  if (fehler) {
    return KONTUR_MELDUNG[fehler];
  }
  if (anzahlPunkte === 0) {
    // **Der Erfolg wird BENANNT.** Ohne diesen Satz sieht ein geschlossener Zug genauso aus wie
    // ein verworfener: die Vorschau ist in beiden Faellen weg.
    if (geschlossenePunkte !== null) {
      return `Kontur geschlossen — ${geschlossenePunkte} Punkte. Klick setzt den ersten Punkt der naechsten.`;
    }

    return 'Klick setzt den ersten Punkt der Kontur';
  }

  return `${anzahlPunkte} ${anzahlPunkte === 1 ? 'Punkt' : 'Punkte'} · Klick auf den ersten schließt · Enter schließt · Esc verwirft`;
}
