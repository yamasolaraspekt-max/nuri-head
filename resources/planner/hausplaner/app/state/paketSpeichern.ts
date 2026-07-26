/**
 * AUF-81 — **das Konfigurator-Paket serverseitig ablegen.**
 *
 * Bis AUF-74 sagte die Fläche „gespeichert" und erzeugte eine Datei im Download-Ordner; AUF-74 hat
 * den Satz wahr gemacht. **Jetzt wird wirklich gespeichert** — und der Satz muss erneut die
 * Wahrheit sagen, diesmal in die andere Richtung.
 *
 * **Zusätzlich, nicht statt.** Der Download bleibt: er ist der Weg für alle ohne Speicherrecht, und
 * er funktioniert. Einen funktionierenden Weg zu schließen, bevor der neue bewiesen ist, wäre
 * derselbe Fehler wie ein Versprechen ohne Deckung — nur andersherum.
 *
 * **Der Besitzer wird hier NICHT mitgeschickt.** Er kommt auf dem Server aus der Sitzung. Eine
 * Kennung, die der Aufrufer mitgibt, wäre das Eigentumsgatter, das man selbst aufsperrt.
 *
 * **Kein Wurf nach außen:** schlägt das Speichern fehl, liefert diese Funktion `false`, und die
 * Fläche sagt genau das. Ein Fehler, der als Erfolg gemeldet wird, war der Befund aus AUF-74.
 */

/** Das Attribut am Mount-Knoten (`data-pakete-url`) — dieselbe Naht wie `data-speichern-url`. */
export const PAKETE_URL_ATTRIBUT = 'paketeUrl';

let zielUrl: string | null = null;
let csrf: string | null = null;

/** Einmal beim Mount gesetzt (aus `main.tsx`). Ohne Ziel wird nicht gespeichert — und nichts behauptet. */
export function setzePaketZiel(url: string | null, token: string | null): void {
  zielUrl = url && url.length > 0 ? url : null;
  csrf = token;
}

/** Ist das Speichern überhaupt möglich? Für Anzeigen, die es sonst versprechen würden. */
export function kannPaketSpeichern(): boolean {
  return zielUrl !== null;
}

/**
 * Speichert ein Paket. Gibt zurück, **ob es wirklich angekommen ist** — nicht, ob es abgeschickt
 * wurde.
 */
export async function speicherePaket(art: string, titel: string, paket: unknown): Promise<boolean> {
  if (zielUrl === null) {
    return false;
  }
  try {
    const antwort = await fetch(zielUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf ?? '',
      },
      body: JSON.stringify({
        art,
        titel,
        schema_version: (paket as { schemaVersion?: number }).schemaVersion ?? 1,
        paket,
      }),
    });
    return antwort.ok;
  } catch {
    // Netz weg, Server weg, Recht fehlt: alles derselbe ehrliche Ausgang — nicht gespeichert.
    return false;
  }
}
