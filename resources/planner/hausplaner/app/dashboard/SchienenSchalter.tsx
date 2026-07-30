/**
 * AUF-83-T5 / K-01 — der Umschalt-Knopf einer Schiene, als eigene Datei.
 *
 * **Nicht in `pfade` des Auftrags — eine bewusste, offen gemeldete Abweichung.** Der Auftrag listet
 * keine eigene Datei für den Knopf. Extrahiert trotzdem, aus demselben Grund wie `ReiterLeiste`,
 * `GeschossFlaeche` und `WerkzeugGruppenMenue` (Befund B1): eine fokussierbare Fläche im Rumpf von
 * `HausplanerApp` würde bei jedem Render neu gemountet und verlöre den Fokus. **Und der Grund, der
 * hier zusätzlich zählt:** K-01 verlangt einen DOM-Beleg (`npm run test:hausplaner:dom
 * -- --filter=schienen`) — ohne eigene Datei gäbe es nichts, das sich isoliert mounten ließe.
 *
 * **Rein präsentational.** Der Knopf kennt weder den Klappzustand-Speicher noch den
 * Escape-Stapel — beides bleibt bei `HausplanerApp`, die den Zustand hält. Dieselbe Trennung wie
 * bei `ReiterLeiste`: die Fläche zeigt, der Aufrufer entscheidet.
 */
import React, { useState } from 'react';

interface Props {
  seite: 'links' | 'rechts';
  /** Ist die Schiene gerade offen? Bestimmt `aria-expanded` UND die Pfeilrichtung. */
  offen: boolean;
  onClick: () => void;
  /** Was diese Schiene zeigt — für den erreichbaren Namen, z. B. „Planer-Bereiche". */
  label: string;
}

export function SchienenSchalter({ seite, offen, onClick, label }: Props): React.ReactElement {
  // Der Pfeil zeigt IMMER Richtung „was passiert beim Klick" — nicht den aktuellen Zustand: offen
  // links zeigt „‹" (schließt nach links), zu zeigt „›" (öffnet nach rechts) — und umgekehrt rechts.
  const pfeilLinks = offen ? '‹' : '›';
  const pfeilRechts = offen ? '›' : '‹';
  // Der Hover-Zustand lebt in React, nicht in CSS — diese Stilschicht ist bewusst frei von
  // `:hover` (`stilschicht.test.ts:399`), weil die Insel Zeiger-Zustände sonst an zwei Stellen
  // hielte. Dasselbe Muster wie `StartView.tsx` (`boxShadow: hover ? … : …`).
  const [hover, setHover] = useState(false);
  return (
    <button
      type="button"
      aria-expanded={offen}
      title={`${label} ${offen ? 'einklappen' : 'ausklappen'}`}
      onClick={onClick}
      onMouseEnter={() => setHover(true)}
      onMouseLeave={() => setHover(false)}
      className={`hp-schiene-schalter hp-schiene-schalter--${seite}`}
      style={{ background: hover ? 'var(--hp-hair2)' : 'transparent', color: hover ? 'var(--hp-ink)' : 'var(--hp-muted)' }}
    >
      <span aria-hidden="true">{seite === 'links' ? pfeilLinks : pfeilRechts}</span>
    </button>
  );
}
