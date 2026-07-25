/**
 * AUF-60 — **die Rechte des angemeldeten Nutzers, gelesen statt gesetzt.**
 *
 * **Der gemessene Mangel (aus AUF-53 §4):** Die Insel kannte genau ein Recht, und es stammte nicht
 * aus dem Nutzer — `permissions: [RECHT_BEARBEITEN]` stand als Wert im Quelltext. Damit war die
 * Zuordnung aus AUF-53 (`import` ⇒ `Hausplaner,add`) richtig **und wirkungslos zugleich**: ein
 * Werkzeug sperrte oder öffnete nach einem Wert, den die Oberfläche sich selbst gab.
 *
 * **Was das ist und was nicht:** keine Sicherheitslücke — jede Route hängt weiterhin an
 * `CheckUserPermission`, wer kein Recht hat, bekommt vom Server 403, egal was die Oberfläche zeigt.
 * Es ist eine **Anzeige-Lüge in beide Richtungen**: bedienbar aussehen, was der Server verweigert —
 * und gesperrt aussehen, was erlaubt wäre.
 *
 * **Dieses Modul entscheidet nichts.** Es liest eine Zeichenkette und gibt eine Liste zurück. Die
 * Wahrheit über Berechtigungen bleibt auf dem Server; eine eigene Prüfung in der Insel wäre eine
 * zweite Wahrheit über Rechte — die gefährlichste Sorte zweiter Wahrheit.
 */

/**
 * Der Name des Datenattributs am Mount-Knoten (`data-rechte`) — dieselbe Naht, die schon
 * `data-speichern-url` und `data-snapshots-url` trägt. Kein neuer Mechanismus.
 */
export const RECHTE_ATTRIBUT = 'rechte';

/**
 * Die Rechte aus dem Attribut.
 *
 * **Trennzeichen ist der Leerraum, nicht das Komma** — ein Recht selbst enthält ein Komma
 * (`Hausplaner,update`), am Komma zu trennen zerlegte genau die Marken, die gelesen werden sollen.
 *
 * **Fehlt das Attribut oder ist es leer, gilt das Minimum: die leere Liste.** Nicht das Maximum.
 * Ein fehlender Wert darf nie „darf alles" bedeuten — sonst öffnet ausgerechnet der Fehlerfall
 * (alte Blade, Testfläche, Tippfehler im Attributnamen) alle Werkzeuge.
 *
 * **Es wird durchgereicht, nicht abgeleitet:** kein Eintrag kommt hinzu (etwa „wer schreiben darf,
 * darf auch lesen"), keiner fällt weg. Welche Rechte es gibt, weiß das Blade aus `hasPermission`;
 * diese Funktion kennt keine einzige Rechte-Marke namentlich.
 */
export function leseRechte(roh: string | null | undefined): string[] {
  if (!roh) {
    return [];
  }
  return roh.split(/\s+/).filter((t) => t.length > 0);
}
