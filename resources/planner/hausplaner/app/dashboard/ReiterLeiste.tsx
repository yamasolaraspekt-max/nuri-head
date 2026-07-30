/**
 * AUF-27 — die EINE Reiterleiste des Hausplaners.
 *
 * **Warum extrahiert und nicht kopiert:** Der Auftrag verlangt für die linke Schiene Reiter und
 * verbietet im selben Satz einen zweiten Tab-Mechanismus. Das Muster gab es seit Dashboard v2.2
 * bereits — als JSX **im Rumpf** von `HausplanerApp` (Eigenschaften-Panel), zusammen mit den
 * Nacharbeiten aus AUF-19 (`role="tabpanel"`, `aria-controls` auf eine existierende `id`,
 * Pfeiltasten mit Fokusnachführung, kein `focus()` beim Mausklick). Es zu kopieren hiesse: dieselbe
 * A11y-Verdrahtung zweimal pflegen und beim nächsten Befund eine der beiden Stellen vergessen.
 * Deshalb steht sie hier **einmal** und wird zweimal benutzt.
 *
 * **Modulebene, nicht im Rumpf** (Befund B1): Reiter sind fokussierbare Steuerelemente, und
 * `HausplanerApp` rendert in Mausbewegungs-Frequenz (`onMouseMove`). Eine im Rumpf definierte
 * Komponente würde bei jeder Bewegung neu montiert — Fokus und Tastaturbedienung gingen verloren.
 *
 * **Was hier NICHT entschieden wird:** welcher Inhalt zu einem Reiter gehört und wo er steht. Diese
 * Komponente rendert die Leiste; den Inhaltsbereich (`role="tabpanel"`) stellt die aufrufende Seite,
 * weil nur sie weiss, was darin steht. `panelId` ist die Klammer zwischen beidem.
 */
import React, { useRef } from 'react';
import { T } from '../studioDaten';
import { GESPERRT_BESCHRIFTUNG, GESPERRT_DECKKRAFT } from './gesperrtStil';

export interface ReiterEintrag {
  id: string;
  label: string;
  /** Ein Satz für den Tooltip: was dieser Reiter zeigt. Ohne ihn ist ein Reiter eine Behauptung. */
  hinweis?: string;
  /**
   * AUF-83-T3 / K-05 — **der Grund, warum dieser Reiter noch nichts trägt.**
   *
   * **Optional, und das ist die ganze Auflage:** `ReiterLeiste` hat drei Nutzer (Panel-Reiter,
   * Schienen-Reiter, Arbeitsbereiche — AUF-27). Wer das Feld nicht setzt, bekommt **exakt** das
   * heutige Verhalten; eine Zusage in `reiterLeisteGeteilt.test.ts` belegt das, statt es zu
   * behaupten.
   *
   * **Ein Satz, kein `true`.** Ein Merker sagt *dass*, der Satz sagt *warum* — und K-05 verlangt
   * ausdrücklich, dass der Reiter „das auch sagt". Ein ausgegrauter Reiter ohne Begründung ist
   * eine Sackgasse mit Beschriftung.
   *
   * **Es sperrt nicht.** Der Reiter bleibt anwählbar und tastaturbedienbar — ausgegraut ist eine
   * Aussage über den Inhalt, keine Entziehung der Bedienung. Ein `disabled` hier risse ein Loch
   * in die Pfeiltasten-Navigation der beiden anderen Nutzer.
   */
  nochNicht?: string;
}

interface Props {
  reiter: readonly ReiterEintrag[];
  aktiv: string;
  setAktiv: (id: string) => void;
  /** Beschriftung der Leiste für Screenreader — ohne sie sind zwei Tablisten nicht unterscheidbar. */
  ariaLabel: string;
  /** id des EINEN Inhaltsbereichs, auf den alle Reiter zeigen. Muss im Baum existieren. */
  panelId: string;
  /** id-Bildner der Reiter-Knöpfe. Präfix je Leiste ⇒ keine ID-Kollision im Dokument (Kante 5). */
  reiterId: (id: string) => string;
}

export function ReiterLeiste({ reiter, aktiv, setAktiv, ariaLabel, panelId, reiterId }: Props): React.ReactElement {
  /** Nur Lese-/Fokus-Zugriff — kein Zustand, kein zweiter Auswahl-Weg neben `aktiv`. */
  const reiterRefs = useRef<Record<string, HTMLButtonElement | null>>({});

  return (
    // AUF-26 / Kante 3: `flexWrap` + `overflowWrap` — in 220 px brechen drei Beschriftungen um,
    // statt gekappt zu werden. Ein Reiter, der „Fachpla…" heisst, ist kein Reiter.
    <div
      role="tablist" aria-label={ariaLabel}
      className="hp-rl-leiste"
    >
      {reiter.map((tab, i) => {
        const aktivT = tab.id === aktiv;
        return (
          <button
            key={tab.id} type="button" role="tab" aria-selected={aktivT} tabIndex={aktivT ? 0 : -1}
            id={reiterId(tab.id)} aria-controls={panelId}
            ref={(el) => { reiterRefs.current[tab.id] = el; }}
            title={tab.nochNicht ? `${tab.label} — ${tab.nochNicht}` : tab.hinweis}
            onClick={() => setAktiv(tab.id)}
            onKeyDown={(e) => {
              if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') return;
              e.preventDefault();
              const d = e.key === 'ArrowRight' ? 1 : -1;
              const ziel = reiter[(i + d + reiter.length) % reiter.length].id;
              setAktiv(ziel);
              // B4 + Kante 6: der DOM-Fokus wandert MIT der Auswahl — aber ausschliesslich
              // hier im Tasten-Zweig. `onClick` ruft das nicht auf, also springt der
              // Fokusring nicht bei jedem Mausklick auf.
              reiterRefs.current[ziel]?.focus();
            }}
            style={{
              // Aktiver Reiter an Schriftschnitt UND Unterstrich erkennbar, nicht nur farblich
              // (WCAG 1.4.1). Farben ausschliesslich aus `T` — kein roher Farbwert.
              padding: '5px 8px', fontSize: 11.5, cursor: 'pointer', background: 'transparent',
              border: 'none', borderBottom: `2px solid ${aktivT ? T.brandInk : 'transparent'}`,
              fontWeight: aktivT ? 800 : 600, color: aktivT ? T.ink : T.muted,
              overflowWrap: 'anywhere', whiteSpace: 'normal',
              // AUF-83-T3 / K-05: die Deckkraft kommt aus `gesperrtStil.ts` (AUF-71), nicht aus
              // einer sechsten Meinung darüber, wie „noch nicht" aussieht. Genau dieser Wert war
              // schon einmal an fünf Stellen verschieden — 0,6 · 0,4 · 0,45.
              opacity: tab.nochNicht ? GESPERRT_DECKKRAFT : undefined,
            }}
          >
            {tab.label}
            {/* **„sagt das auch"** — der sichtbare Teil des Kriteriums. Ohne ihn wäre der Reiter
                nur blass, und Blässe allein ist keine Auskunft (WCAG 1.4.1: nicht nur Farbe). */}
            {tab.nochNicht && <span style={{ marginLeft: 5, fontWeight: 600, color: GESPERRT_BESCHRIFTUNG }}>· noch nicht</span>}
          </button>
        );
      })}
    </div>
  );
}
