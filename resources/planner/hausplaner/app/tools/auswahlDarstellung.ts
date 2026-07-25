/**
 * AUF-35a — der **Darstellungszustand** eines Objekts als reine Funktion.
 *
 * **Das Muster ist nicht neu, und genau das ist der Punkt:** I3 hat die sechs Werkzeug-Zustände so
 * gebaut (`werkzeugZustand.ts`) — Zustand als Daten, nicht als Markup, damit „wie sieht das aus?"
 * prüfbar ist statt nur ansehbar. Hier dasselbe für die Auswahl: **wiederverwendet, nicht neu
 * erfunden.**
 *
 * **Fünf Eingangszustände**, wie in Yamas Referenz: `ausgewaehlt` · `primaer` · `ueberfahren` ·
 * `gesperrt` · `ungueltig`. Sie schließen einander nicht aus — ein Objekt kann primär **und**
 * gesperrt sein; genau dann muss der Nutzer beides sehen.
 *
 * **Keine rohen Farbwerte.** Diese Datei nennt Token-Namen (`T.*`-Schlüssel), keine Hex-Werte;
 * Hex lebt ausschließlich in `studioDaten.ts` (Token-Scope, `docs/architektur/react-hausplaner-
 * token-scope.md`). So bleibt die Funktion ohne Renderer testbar und ohne Farbdiskussion.
 */

export interface DarstellungEingabe {
  ausgewaehlt?: boolean;
  /** Das Objekt, dessen Eigenschaften das Panel zeigt — nur eines der ausgewählten. */
  primaer?: boolean;
  ueberfahren?: boolean;
  gesperrt?: boolean;
  /** Fachlich unzulässiger Zustand (z. B. Wand ohne Anschluss) — der Renderer zeigt ihn rot. */
  ungueltig?: boolean;
}

export interface Darstellung {
  /** Strichstärke in px. Ausgewählt dicker, primär am dicksten. */
  strichstaerke: number;
  /** Deckkraft 0…1. Gesperrtes tritt zurück, verschwindet aber nicht. */
  deckkraft: number;
  /** Zieht der Renderer Griffe (Anfasser) an dieses Objekt? Nur am Primärobjekt. */
  griffe: boolean;
  /** Schloss-Abzeichen sichtbar? Nur bei gesperrt — ein Zustand, der nie nur über Farbe geht. */
  schloss: boolean;
  /** Token-Name der Kontur — **kein** Farbwert. Der Renderer schlägt ihn in `T` nach. */
  konturToken: 'brandInk' | 'errInk' | 'muted' | 'ink';
}

/**
 * Die Ableitung. **Reihenfolge der Kontur ist bewusst:**
 * 1. `ungueltig` schlägt alles — ein Fehler muss sichtbar bleiben, auch wenn das Objekt ausgewählt ist.
 * 2. `ausgewaehlt`/`primaer` vor `ueberfahren` — was gewählt ist, wiegt schwerer als was unter dem
 *    Zeiger liegt; sonst flackerte die Auswahl bei jeder Mausbewegung.
 * 3. `gesperrt` färbt gedämpft, wenn sonst nichts zutrifft.
 *
 * **Griffe nur am Primärobjekt:** Anfasser an fünf gleichzeitig ausgewählten Wänden wären fünfmal
 * dieselbe Geste mit unklarem Ziel. Das Panel zeigt ebenfalls das Primärobjekt — eine Wahrheit.
 */
export function aufloeseDarstellung(e: DarstellungEingabe): Darstellung {
  const konturToken: Darstellung['konturToken'] = e.ungueltig
    ? 'errInk'
    : (e.primaer || e.ausgewaehlt)
      ? 'brandInk'
      : e.ueberfahren
        ? 'ink'
        : e.gesperrt
          ? 'muted'
          : 'ink';

  return {
    strichstaerke: e.primaer ? 3 : e.ausgewaehlt ? 2 : e.ueberfahren ? 1.5 : 1,
    // Gesperrtes tritt zurück, bleibt aber lesbar; ausgewählt hebt die Dämpfung wieder auf,
    // denn wer ein gesperrtes Objekt anwählt, will genau es ansehen (Kante 1).
    deckkraft: e.gesperrt && !e.ausgewaehlt && !e.primaer ? 0.55 : 1,
    griffe: Boolean(e.primaer) && !e.gesperrt,
    schloss: Boolean(e.gesperrt),
    konturToken,
  };
}
